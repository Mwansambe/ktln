<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Subject Model
 * Represents exam subject categories (Physics, Math, etc.)
 */
class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'icon', 'color', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function exams() {
        return $this->hasMany(Exam::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    public function getExamCountAttribute(): int {
        return $this->exams()->count();
    }
}
