<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model
 * Represents system users: students, editors, admins.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'phone',
        'is_active', 'activated_at', 'expires_at', 'fcm_token', 'avatar',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function activationRecords() {
        return $this->hasMany(ActivationRecord::class);
    }

    public function latestActivation() {
        return $this->hasOne(ActivationRecord::class)->latest();
    }

    public function isExpired(): bool {
        if (!$this->expires_at) return false;
        return now()->isAfter($this->expires_at);
    }

    public function canAccess(): bool {
        return $this->is_active && !$this->isExpired();
    }

    public function daysRemaining(): int {
        if (!$this->expires_at) return 0;
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    public function scopeActive($query) {
        return $query->where('is_active', true)->where('expires_at', '>', now());
    }

    public function scopeExpired($query) {
        return $query->where('expires_at', '<', now());
    }
}
