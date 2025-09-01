<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\User;
use App\Models\LogbookEntry;

class TestLogbookIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:logbook-integration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the logbook integration with student profile required hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Logbook Integration...');
        
        // Show all students
        $students = Student::all();
        $this->info('Students in database:');
        foreach ($students as $student) {
            $this->line("- {$student->name}: Required Hours = {$student->required_hours}, Completed Hours = {$student->hours_completed}");
        }
        
        // Show all users
        $users = User::all();
        $this->info("\nUsers in database:");
        foreach ($users as $user) {
            $student = $user->student;
            $studentInfo = $student ? "Student Profile: {$student->required_hours} required hours" : "No student profile";
            $this->line("- {$user->name} ({$user->role}): {$studentInfo}");
        }
        
        // Show logbook entries
        $entries = LogbookEntry::all();
        $this->info("\nLogbook entries: " . $entries->count());
        
        // Test creating a logbook entry to verify integration
        $studentUser = User::where('role', 'student')->first();
        if ($studentUser) {
            $this->info("\nTesting logbook entry creation for: {$studentUser->name}");
            
            // Create a test entry
            $entry = LogbookEntry::create([
                'user_id' => $studentUser->id,
                'user_name' => $studentUser->name,
                'log_date' => today(),
                'time_in' => '09:00:00',
                'time_out' => '17:00:00',
                'hours_logged' => 8.0,
                'status' => 'completed'
            ]);
            
            // Update student's completed hours (simulating the controller logic)
            $student = $studentUser->student;
            if ($student) {
                $totalHours = LogbookEntry::where('user_id', $studentUser->id)
                    ->where('status', 'completed')
                    ->sum('hours_logged');
                $student->update(['hours_completed' => $totalHours]);
                
                $this->info("✅ Created logbook entry: 8 hours");
                $this->info("✅ Updated student completed hours: {$student->fresh()->hours_completed}");
                $this->info("✅ Progress: {$student->fresh()->hours_completed}/{$student->required_hours} hours");
            }
        }
        
        return 0;
    }
}
