<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Try to find student record by user's email or create a default one for demo
        $student = Student::where('name', $user->name)->first();
        
        // If no student record exists, create a default one for demo purposes
        if (!$student) {
            $student = new Student([
                'student_id' => 'DEMO-001',
                'name' => $user->name,
                'program' => 'Computer Science',
                'year' => '4th Year',
                'coordinator_assigned' => 'Dr. John Smith',
                'internship_duration' => 12,
                'required_hours' => 480,
                'hours_completed' => 360,
                'profile_picture' => null,
            ]);
        }
        
        // Calculate evaluation status based on progress
        $evaluationStatus = $student->hours_completed > 0 ? 'Started' : 'Not Started';
        
        return view('student-interns.dashboard.index', compact('student', 'evaluationStatus'));
    }
}
