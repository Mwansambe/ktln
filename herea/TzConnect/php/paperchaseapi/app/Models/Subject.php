<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'icon',
        'color',
        'bg_color',
        'border_color',
        'description',
        'paper_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'paper_count' => 'integer',
    ];

    /**
     * Get the exams for this subject.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Get active exams (non-soft-deleted).
     */
    public function activeExams(): HasMany
    {
        return $this->hasMany(Exam::class)->whereNull('deleted_at');
    }

    /**
     * Recalculate the paper count.
     */
    public function recalculatePaperCount(): void
    {
        $this->paper_count = $this->exams()->count();
        $this->save();
    }
}

