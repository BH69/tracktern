<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\LogbookEntry;
use App\Models\Task;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CoordinatorController extends Controller
{
    public function dashboard()
    {
        return view('coordinators.dashboard.index');
    }

    public function logbookReview()
    {
        // Get logbook entries that need review or have been reviewed
        $logbookEntries = LogbookEntry::with('user')
            ->forReview() // Only show completed, approved, or rejected entries
            ->orderBy('log_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'user_name' => $entry->user_name,
                    'date' => $entry->log_date->format('M j, Y'),
                    'time_in' => \Carbon\Carbon::parse($entry->time_in)->format('g:i A'),
                    'time_out' => $entry->time_out ? \Carbon\Carbon::parse($entry->time_out)->format('g:i A') : 'N/A',
                    'hours_logged' => number_format($entry->hours_logged, 1),
                    'status' => $entry->status,
                    'notes' => $entry->notes,
                    'raw_status' => $entry->status,
                ];
            });

        return view('coordinators.logbook-review.index', compact('logbookEntries'));
    }

    public function approveLogbookEntry(Request $request, $id)
    {
        $entry = LogbookEntry::findOrFail($id);
        $entry->update(['status' => 'approved']);
        
        // Update student's total hours completed with approved entries
        $user = $entry->user;
        $student = $user->student;
        if ($student) {
            $totalApprovedHours = LogbookEntry::forUser($user->id)
                ->approved()
                ->sum('hours_logged');
            $student->update(['hours_completed' => $totalApprovedHours]);
        }
        
        return redirect()->route('coordinators.logbook-review')->with('success', 'Logbook entry approved successfully!');
    }

    public function rejectLogbookEntry(Request $request, $id)
    {
        $entry = LogbookEntry::findOrFail($id);
        $entry->update(['status' => 'rejected']);
        
        // Update student's total hours completed (recalculate with only approved entries)
        $user = $entry->user;
        $student = $user->student;
        if ($student) {
            $totalApprovedHours = LogbookEntry::forUser($user->id)
                ->approved()
                ->sum('hours_logged');
            $student->update(['hours_completed' => $totalApprovedHours]);
        }
        
        return redirect()->route('coordinators.logbook-review')->with('success', 'Logbook entry rejected.');
    }

    public function internProgressTracker()
    {
        // Get all students with their progress data
        $students = Student::whereNotNull('name')
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $completed = $student->hours_completed ?? 0;
                $required = $student->required_hours ?? 0;
                
                // Calculate progress percentage
                $progress = $required > 0 ? round(($completed / $required) * 100, 1) : 0;
                
                // Determine status based on progress
                if ($progress == 0) {
                    $status = 'Pending';
                    $statusColor = 'bg-gray-500';
                } elseif ($progress >= 100) {
                    $status = 'Completed';
                    $statusColor = 'bg-green-500';
                } else {
                    $status = 'Ongoing';
                    $statusColor = 'bg-blue-500';
                }
                
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'course' => $student->course ?? 'Not specified',
                    'hours_completed' => $completed,
                    'required_hours' => $required,
                    'progress' => $progress,
                    'status' => $status,
                    'status_color' => $statusColor,
                ];
            });

        return view('coordinators.intern-progress-tracker.index', compact('students'));
    }

    public function tasksManagement()
    {
        // Get all tasks with assigned user information
        $tasks = Task::with('assignedUser')->orderBy('date', 'desc')->get();
        
        return view('coordinators.tasks-management.index', compact('tasks'));
    }

    public function createTask()
    {
        // Get all students for the dropdown
        $students = User::where('role', 'student')->get();
        
        return view('coordinators.tasks-management.create', compact('students'));
    }

    public function storeTask(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'assigned_to' => 'required|exists:users,id',
            'company' => 'required|string|max:255',
            'supervisor' => 'required|string|max:255',
            'task_assigned' => 'required|string',
            'due_date' => 'required|date|after_or_equal:today',
            'task_file' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif|max:10240', // Max 10MB
        ]);

        $taskFilePath = null;
        $taskFileName = null;
        
        // Handle file upload
        if ($request->hasFile('task_file')) {
            $file = $request->file('task_file');
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . $originalName;
            
            // Store file in the storage/app/public/task-files directory
            $taskFilePath = $file->storeAs('task-files', $fileName, 'public');
            $taskFileName = $originalName;
        }

        // Create the task
        Task::create([
            'date' => $request->date,
            'assigned_to' => $request->assigned_to,
            'company' => $request->company,
            'supervisor' => $request->supervisor,
            'task_assigned' => $request->task_assigned,
            'due_date' => $request->due_date,
            'task_file_path' => $taskFilePath,
            'task_file_name' => $taskFileName,
            'status' => 'pending'
        ]);
        
        $successMessage = 'Task created successfully!';
        if ($taskFilePath) {
            $successMessage .= ' File uploaded: ' . $taskFileName;
        }
        
        return redirect()->route('coordinators.tasks-management')->with('success', $successMessage);
    }

    public function showCreateInternForm()
    {
        return view('coordinators.create-intern');
    }

    public function createIntern(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:student,admin',
        ]);

        $user = new User();
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->name = ''; // Set blank name for user to fill later
        $user->save();

        return redirect()->route('coordinators.create-intern')->with('success', 'User account created successfully!');
    }
}
