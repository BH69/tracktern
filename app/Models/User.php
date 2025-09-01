<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Role constants.
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_STUDENT = 'student';

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    /**
     * Get all available roles.
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_STUDENT,
        ];
    }

    /**
     * Scope to get only admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    /**
     * Scope to get only student users.
     */
    public function scopeStudents($query)
    {
        return $query->where('role', self::ROLE_STUDENT);
    }

    /**
     * Get the logbook entries for the user
     */
    public function logbookEntries()
    {
        return $this->hasMany(LogbookEntry::class);
    }

    /**
     * Get today's logbook entry for the user
     */
    public function todayLogbookEntry()
    {
        return $this->hasOne(LogbookEntry::class)->whereDate('log_date', today());
    }

    /**
     * Get the student profile for this user
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'name', 'name');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
