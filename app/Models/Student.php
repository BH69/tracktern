<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'program',
        'year',
        'year_level',
        'course',
        'department',
        'coordinator_assigned',
        'contact_number',
        'assigned_company',
        'company_supervisor',
        'internship_duration',
        'required_hours',
        'hours_completed',
        'profile_picture',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'internship_duration' => 'integer',
        'required_hours' => 'integer',
        'hours_completed' => 'integer',
    ];

    /**
     * Get the progress percentage of completed hours.
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->required_hours === 0) {
            return 0;
        }
        
        return min(100, ($this->hours_completed / $this->required_hours) * 100);
    }

    /**
     * Get the remaining hours needed to complete the internship.
     */
    public function getRemainingHoursAttribute(): int
    {
        return max(0, $this->required_hours - $this->hours_completed);
    }

    /**
     * Check if the student has completed their required hours.
     */
    public function hasCompletedHours(): bool
    {
        return $this->hours_completed >= $this->required_hours;
    }
}
