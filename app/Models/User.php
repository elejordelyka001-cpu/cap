<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'department',
        'position',
        'phone',
        'bio',
        'avatar',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationships
    public function goals()
    {
        return $this->hasMany(PerformanceGoal::class);
    }

    public function tracking()
    {
        return $this->hasMany(PerformanceTracking::class);
    }

    public function reviews()
    {
        return $this->hasMany(PerformanceReview::class, 'user_id');
    }

    public function submittedReviews()
    {
        return $this->hasMany(PerformanceReview::class, 'reviewer_id');
    }

    public function aiFormulations()
    {
        return $this->hasMany(AIFormulation::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }
}
