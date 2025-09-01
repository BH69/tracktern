<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LogbookEntry extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'log_date',
        'time_in',
        'time_out',
        'hours_logged',
        'status',
        'notes'
    ];

    protected $casts = [
        'log_date' => 'date',
        'hours_logged' => 'decimal:2'
    ];

    /**
     * Boot the model and set up event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically recalculate hours when time_in or time_out changes
        static::saving(function ($entry) {
            if ($entry->time_in && $entry->time_out) {
                $entry->hours_logged = round($entry->calculateHours(), 2);
            }
        });
    }

    /**
     * Get the user that owns the logbook entry
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate hours logged based on time_in and time_out
     */
    public function calculateHours(): float
    {
        if (!$this->time_in || !$this->time_out) {
            return 0;
        }

        // Create Carbon instances with today's date for proper time calculation
        $timeIn = Carbon::createFromFormat('H:i:s', $this->time_in, 'Asia/Manila');
        $timeOut = Carbon::createFromFormat('H:i:s', $this->time_out, 'Asia/Manila');
        
        // Calculate hours difference as decimal (e.g., 8.5 for 8 hours 30 minutes)
        return abs($timeOut->diffInMinutes($timeIn)) / 60;
    }

    /**
     * Scope to get entries for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get entries for a specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('log_date', $date);
    }

    /**
     * Scope to get completed entries
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get approved entries
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get entries pending review
     */
    public function scopePendingReview($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get all entries for review (completed, approved, rejected)
     */
    public function scopeForReview($query)
    {
        return $query->whereIn('status', ['completed', 'approved', 'rejected']);
    }
}
