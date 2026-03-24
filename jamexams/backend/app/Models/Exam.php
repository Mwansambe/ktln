<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Exam Model
 * Represents an exam paper with optional marking scheme.
 */
class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'title', 'description', 'subject_id', 'exam_type',
        'class_level', 'year', 'exam_file_path', 'exam_file_size',
        'marking_scheme_path', 'marking_scheme_size', 'is_featured',
        'is_published', 'download_count', 'uploaded_by',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'download_count' => 'integer',
        'exam_file_size' => 'integer',
        'marking_scheme_size' => 'integer',
    ];

    // Exam type constants
    const TYPE_PAST_PAPER = 'PAST_PAPER';
    const TYPE_MOCK = 'MOCK';
    const TYPE_MIDTERM = 'MIDTERM';
    const TYPE_FINAL = 'FINAL';
    const TYPE_REVISION = 'REVISION';

    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    public function uploader() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getExamFileUrlAttribute(): ?string {
        if (!$this->exam_file_path) return null;
        return Storage::url($this->exam_file_path);
    }

    public function getMarkingSchemeUrlAttribute(): ?string {
        if (!$this->marking_scheme_path) return null;
        return Storage::url($this->marking_scheme_path);
    }

    public function hasMarkingScheme(): bool {
        return !is_null($this->marking_scheme_path);
    }

    public function incrementDownload(): void {
        $this->increment('download_count');
    }

    public function scopePublished($query) {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query) {
        return $query->where('is_featured', true);
    }

    public function scopeBySubject($query, int $subjectId) {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByClass($query, string $classLevel) {
        return $query->where('class_level', $classLevel);
    }

    public function scopeByYear($query, int $year) {
        return $query->where('year', $year);
    }
}
