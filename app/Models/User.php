<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     * Updated to match Specification & Migrations.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // New Columns added for Spec Compliance:
        'cnic',                 // Family Logic
        'phone',                // SMS Alerts
        'role',                 // Admin, Teacher, Student, Parent
        'status',               // Active/Inactive
        'profile_photo_path',   // Images
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 10/11 syntax
    ];

    // Helper to check role easily (Optional but helpful)
    public function hasRole($role) {
        return $this->role === $role;
    }
}