<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ActivationRecord Model
 * Tracks each time a user is activated, with duration info.
 */
class ActivationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'activated_by', 'activated_at', 'expires_at',
        'duration_days', 'notes', 'is_current',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_current' => 'boolean',
        'duration_days' => 'integer',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function activatedBy() {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function isExpired(): bool {
        return now()->isAfter($this->expires_at);
    }
}
