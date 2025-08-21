<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class LogbookReview extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'date',
        'time_in',
        'time_out',
        'hours',
        'task_summary',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i',
        'hours' => 'integer',
    ];

    /**
     * The possible status values.
     */
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';
    const STATUS_UNDER_REVIEW = 'Under Review';

    /**
     * Get all possible status values.
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_UNDER_REVIEW,
        ];
    }

    /**
     * Check if the logbook entry is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the logbook entry is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the logbook entry is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Calculate the duration between time_in and time_out.
     */
    public function getCalculatedHoursAttribute(): float
    {
        if (!$this->time_in || !$this->time_out) {
            return 0;
        }

        $timeIn = Carbon::parse($this->time_in);
        $timeOut = Carbon::parse($this->time_out);

        // If time_out is before time_in, assume it's the next day
        if ($timeOut->lessThan($timeIn)) {
            $timeOut->addDay();
        }

        return $timeIn->diffInHours($timeOut, true);
    }

    /**
     * Get formatted date for display.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('M d, Y');
    }

    /**
     * Get formatted time range for display.
     */
    public function getTimeRangeAttribute(): string
    {
        return Carbon::parse($this->time_in)->format('H:i') . ' - ' . Carbon::parse($this->time_out)->format('H:i');
    }
}
