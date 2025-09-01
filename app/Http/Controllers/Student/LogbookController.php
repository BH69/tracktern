<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LogbookEntry;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LogbookController extends Controller
{
    /**
     * Display the logbook page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get today's entry if it exists
        $todayEntry = LogbookEntry::forUser($user->id)
            ->forDate(today())
            ->first();
        
        // Check if today's entry is finalized (completed, approved, or rejected)
        $isEntryFinalized = $todayEntry && in_array($todayEntry->status, ['completed', 'approved', 'rejected']);
        
        // Keep reference to original today entry for status checking
        $originalTodayEntry = $todayEntry;
        
        // Get all entries with status completed, approved, or rejected
        // If today's entry is completed/approved/rejected, it should appear in previous entries section
        $previousEntries = LogbookEntry::forUser($user->id)
            ->whereIn('status', ['completed', 'approved', 'rejected'])
            ->orderBy('log_date', 'desc')
            ->limit(30)
            ->get();
        
        // Format entries for JavaScript
        $formattedEntries = $previousEntries->map(function($entry) {
            // Ensure we parse time correctly from the database
            $timeIn = Carbon::createFromFormat('H:i:s', $entry->time_in, 'Asia/Manila');
            $timeOut = Carbon::createFromFormat('H:i:s', $entry->time_out, 'Asia/Manila');
            
            $status = 'Completed';
            if ($entry->status === 'approved') {
                $status = 'Approved';
            } elseif ($entry->status === 'rejected') {
                $status = 'Rejected';
            }
            
            return [
                'date' => $entry->log_date->format('M j, Y'),
                'timeIn' => $timeIn->format('g:i A'),
                'timeOut' => $timeOut->format('g:i A'),
                'hoursLogged' => number_format($entry->hours_logged, 2),
                'status' => $status,
                'user_name' => $entry->user_name
            ];
        });
        
        // Calculate total logged hours (only approved entries)
        $totalLoggedHours = LogbookEntry::forUser($user->id)
            ->approved()
            ->sum('hours_logged');
        
        // Get required hours from student profile (same approach as dashboard)
        $student = Student::where('name', $user->name)->first();
        $requiredHours = ($student && $student->required_hours) ? $student->required_hours : 486; // Default to 486 if no student profile or no required_hours set
        
        return view('student-interns.logbook.index', compact(
            'todayEntry', 
            'previousEntries', 
            'formattedEntries',
            'totalLoggedHours', 
            'requiredHours',
            'isEntryFinalized',
            'originalTodayEntry',
            'student'
        ));
    }
    
    /**
     * Record time-in
     */
    public function timeIn(Request $request)
    {
        $user = Auth::user();
        $today = today();
        
        // Check if there's already a completed entry for today
        $existingEntry = LogbookEntry::forUser($user->id)
            ->forDate($today)
            ->first();
        
        if ($existingEntry && $existingEntry->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'You have already completed your time entry for today. You cannot time in again.'
            ], 400);
        }
        
        if ($existingEntry && $existingEntry->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your time entry for today has been approved. You cannot time in again.'
            ], 400);
        }
        
        if ($existingEntry && $existingEntry->status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Your time entry for today has been rejected. Please contact your coordinator.'
            ], 400);
        }
        
        // Find or create today's entry (only if not completed/approved/rejected)
        $entry = LogbookEntry::updateOrCreate(
            [
                'user_id' => $user->id,
                'log_date' => $today
            ],
            [
                'user_name' => $user->name,
                'time_in' => now()->format('H:i:s'),
                'status' => 'logged_in'
            ]
        );
        
        return response()->json([
            'success' => true,
            'time_in' => $entry->time_in,
            'status' => $entry->status
        ]);
    }
    
    /**
     * Record time-out
     */
    public function timeOut(Request $request)
    {
        $user = Auth::user();
        $today = today();
        
        $entry = LogbookEntry::forUser($user->id)
            ->forDate($today)
            ->first();
        
        if (!$entry || !$entry->time_in) {
            return response()->json([
                'success' => false,
                'message' => 'No time-in record found for today'
            ], 400);
        }
        
        // Check if entry is already completed
        if ($entry->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'You have already completed your time entry for today. You cannot time out again.'
            ], 400);
        }
        
        if ($entry->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your time entry for today has been approved. You cannot time out again.'
            ], 400);
        }
        
        if ($entry->status === 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Your time entry for today has been rejected. Please contact your coordinator.'
            ], 400);
        }
        
        // Only allow time-out if status is 'logged_in'
        if ($entry->status !== 'logged_in') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid entry status. Please contact your coordinator.'
            ], 400);
        }
        
        $timeOut = now()->format('H:i:s');
        
        // Create Carbon instances with today's date for proper time calculation
        $timeIn = Carbon::createFromFormat('H:i:s', $entry->time_in, 'Asia/Manila');
        $timeOutCarbon = Carbon::createFromFormat('H:i:s', $timeOut, 'Asia/Manila');
        
        // Calculate hours difference as decimal (e.g., 8.5 for 8 hours 30 minutes)
        $hoursLogged = abs($timeOutCarbon->diffInMinutes($timeIn)) / 60;
        
        $entry->update([
            'time_out' => $timeOut,
            'hours_logged' => round($hoursLogged, 2),
            'status' => 'completed'
        ]);

        // Note: Student's total hours will be updated when coordinator approves the entry
        
        return response()->json([
            'success' => true,
            'time_out' => $entry->time_out,
            'hours_logged' => $entry->hours_logged,
            'status' => $entry->status,
            'message' => 'Time out recorded successfully. Entry is now pending coordinator approval.'
        ]);
    }
    
    /**
     * Get logbook entries for the current user
     */
    public function getEntries(Request $request)
    {
        $user = Auth::user();
        
        $entries = LogbookEntry::forUser($user->id)
            ->completed()
            ->orderBy('log_date', 'desc')
            ->paginate(10);
        
        $formattedEntries = $entries->map(function ($entry) {
            // Ensure we parse time correctly from the database
            $timeIn = Carbon::createFromFormat('H:i:s', $entry->time_in, 'Asia/Manila');
            $timeOut = Carbon::createFromFormat('H:i:s', $entry->time_out, 'Asia/Manila');
            
            return [
                'date' => $entry->log_date->format('M j, Y'),
                'timeIn' => $timeIn->format('g:i A'),
                'timeOut' => $timeOut->format('g:i A'),
                'hoursLogged' => number_format($entry->hours_logged, 2),
                'status' => 'Completed',
                'user_name' => $entry->user_name
            ];
        });
        
        return response()->json([
            'entries' => $formattedEntries,
            'pagination' => [
                'current_page' => $entries->currentPage(),
                'last_page' => $entries->lastPage(),
                'total' => $entries->total()
            ]
        ]);
    }
}
