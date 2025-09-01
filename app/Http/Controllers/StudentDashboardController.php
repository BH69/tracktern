<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\LogbookEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Try to find student record by user's name
        $student = Student::where('name', $user->name)->first();
        
        // Get actual logged hours from approved logbook entries
        $actualLoggedHours = LogbookEntry::forUser($user->id)
            ->approved()
            ->sum('hours_logged');
        
        // If no student record exists, create a default one for demo purposes
        if (!$student) {
            $student = new Student([
                'student_id' => 'DEMO-001',
                'name' => $user->name,
                'program' => 'Computer Science',
                'year' => '4th Year',
                'coordinator_assigned' => 'Dr. John Smith',
                'internship_duration' => 12,
                'required_hours' => 486,
                'hours_completed' => $actualLoggedHours,
                'profile_picture' => null,
            ]);
        } else {
            // Update student's hours_completed with actual logged hours
            $student->hours_completed = $actualLoggedHours;
            $student->save();
        }
        
        // Calculate evaluation status based on progress
        $evaluationStatus = $student->hours_completed > 0 ? 'Started' : 'Not Started';
        
        return view('student-interns.dashboard.index', compact('student', 'evaluationStatus'));
    }

    public function profile()
    {
        $user = Auth::user();
        
        // Try to find student record by user's email
        $student = Student::where('name', $user->name)->first();
        
        // If no student record exists, pass null to let the view handle empty states
        if (!$student) {
            $student = null;
        }
        
        return view('student-interns.profile.index', compact('student'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        
        // Find existing student record
        $student = Student::where('name', $user->name)->first();
        
        // If no student record exists, create a new empty instance
        if (!$student) {
            $student = new Student([
                'name' => $user->name,
                'student_id' => null,
                'program' => null,
                'course' => null,
                'year' => null,
                'year_level' => null,
                'coordinator_assigned' => null,
                'internship_duration' => null,
                'required_hours' => null,
                'hours_completed' => 0,
                'profile_picture' => null,
                'department' => null,
                'contact_number' => null,
                'assigned_company' => null,
                'company_supervisor' => null,
            ]);
        }
        
        return view('student-interns.profile.edit', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'student_id' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'year_level' => 'nullable|string|in:1st Year,2nd Year,3rd Year,4th Year,5th Year',
            'assigned_company' => 'nullable|string|max:255',
            'company_supervisor' => 'nullable|string|max:255',
            'required_hours' => 'nullable|integer|in:240,486',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Find or create student record
        $student = Student::where('name', $user->name)->first();
        
        if (!$student) {
            $student = new Student();
            $student->name = $user->name;
            $student->coordinator_assigned = null;
            $student->internship_duration = null;
            $student->hours_completed = 0;
            $student->year = null;
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if it exists
            if ($student->profile_picture) {
                Storage::disk('public')->delete($student->profile_picture);
            }
            
            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $student->profile_picture = $path;
        }

        // Combine name fields
        $fullName = trim($request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name);
        $fullName = preg_replace('/\s+/', ' ', $fullName); // Remove extra spaces

        // Update student information - only save non-empty values
        $student->name = $fullName;
        $student->student_id = $request->student_id ?: null;
        $student->contact_number = $request->contact_number ?: null;
        $student->course = $request->course ?: null;
        $student->program = $request->course ?: null; // Use course as program
        $student->department = $request->department ?: null;
        $student->year_level = $request->year_level ?: null;
        $student->assigned_company = $request->assigned_company ?: null;
        $student->company_supervisor = $request->company_supervisor ?: null;
        $student->required_hours = $request->required_hours ?: null;

        $student->save();

        // Also update the user's name
        $user->name = $fullName;
        $user->save();

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }
}
