<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AssignedTaskController extends Controller
{
    /**
     * Display the assigned tasks for the authenticated student
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Debug: Check if user exists
            if (!$user) {
                return redirect()->route('login')->with('error', 'User not authenticated');
            }
            
            // Get tasks assigned to the current user
            $tasks = Task::where('assigned_to', $user->id)
                        ->orderBy('due_date', 'asc')
                        ->orderBy('created_at', 'desc')
                        ->get();

            // Get student profile for sidebar
            $student = $user->student;

            return view('student-interns.assigned-task.index', compact('tasks', 'student'));
            
        } catch (\Exception $e) {
            Log::error('Error in AssignedTaskController@index: ' . $e->getMessage());
            
            // Return to view with error message instead of redirecting
            $user = Auth::user();
            $tasks = collect(); // Empty collection
            $student = $user ? $user->student : null;
            
            return view('student-interns.assigned-task.index', compact('tasks', 'student'))
                   ->with('error', 'Unable to load assigned tasks: ' . $e->getMessage());
        }
    }

    /**
     * Start a task (change status from pending to in_progress)
     */
    public function startTask(Task $task)
    {
        // Ensure the task belongs to the authenticated user
        if ($task->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Only allow starting if task is pending
        if ($task->status !== 'pending') {
            return redirect()->route('student.assigned-tasks')
                           ->with('error', 'Task cannot be started. Current status: ' . $task->status);
        }

        $task->update(['status' => 'in_progress']);

        return redirect()->route('student.assigned-tasks')
                       ->with('success', 'Task started successfully!');
    }

    /**
     * Complete a task (change status from in_progress to completed)
     */
    public function completeTask(Task $task)
    {
        // Ensure the task belongs to the authenticated user
        if ($task->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Only allow completing if task is in progress
        if ($task->status !== 'in_progress') {
            return redirect()->route('student.assigned-tasks')
                           ->with('error', 'Task cannot be completed. Current status: ' . $task->status);
        }

        $task->update(['status' => 'completed']);

        return redirect()->route('student.assigned-tasks')
                       ->with('success', 'Task completed successfully!');
    }
}
