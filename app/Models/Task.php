<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'date',
        'assigned_to',
        'company',
        'supervisor',
        'task_assigned',
        'due_date',
        'task_file_path',
        'task_file_name',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Get the user that owns the task
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
