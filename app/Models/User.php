<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'role',
        'otp',
        'otp_expires_at',
        'is_verified',
        'is_active',
        'trial_starts_at',
        'trial_ends_at',
    ];

    protected $hidden = [
        'password',
        'otp',
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
            'otp_expires_at' => 'datetime',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'trial_starts_at' => 'datetime',
            'trial_ends_at' => 'datetime',
        ];
    }

    // -------------------------------------------------
    // Relationships
    // -------------------------------------------------

    /**
     * Student profile associated with this user.
     */
    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * Teacher profile associated with this user.
     */
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    /**
     * All subscription purchases by this user.
     */
    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Test attempts by this user.
     */
    public function testAttempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    /**
     * Chapter progress records for this user.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    /**
     * Rewards earned by this user.
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(Reward::class);
    }

    /**
     * In-app notifications for this user.
     */
    public function appNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Subjects assigned to this teacher.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects', 'teacher_id', 'subject_id')
            ->withPivot('class_level')
            ->withTimestamps();
    }

    // -------------------------------------------------
    // Helper Methods
    // -------------------------------------------------

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Check if the user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if the user has an active subscription for the current validity period.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->userSubscriptions()
            ->where('payment_status', 'completed')
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where('ends_at', '>=', now())
            ->exists();
    }

    /**
     * Get the active subscription record for the user.
     */
    public function activeSubscription(): ?UserSubscription
    {
        return $this->userSubscriptions()
            ->with('subscription')
            ->where('payment_status', 'completed')
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where('ends_at', '>=', now())
            ->orderByDesc('ends_at')
            ->first();
    }

    /**
     * Check if the user is currently within their active free trial window.
     */
    public function isInTrial(): bool
    {
        return $this->trial_starts_at !== null
            && $this->trial_ends_at !== null
            && now()->lessThanOrEqualTo($this->trial_ends_at);
    }

    /**
     * Check if the user has full access to content (Admin, Teacher, Active Subscription, or Trial).
     */
    public function hasContentAccess(): bool
    {
        if ($this->isAdmin() || $this->isTeacher()) {
            return true;
        }

        if ($this->hasActiveSubscription()) {
            return true;
        }

        return $this->isInTrial();
    }
}
