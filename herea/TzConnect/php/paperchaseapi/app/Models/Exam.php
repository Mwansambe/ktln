<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'title',
        'subject_id',
        'year',
        'type',
        'description',
        'pdf_path',
        'pdf_name',
        'file_size',
        'marking_scheme_path',
        'marking_scheme_name',
        'marking_scheme_size',
        'has_marking_scheme',
        'preview_image',
        'icon',
        'color',
        'bg_color',
        'border_color',
        'is_featured',
        'is_new',
        'download_count',
        'view_count',
        'bookmark_count',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'has_marking_scheme' => 'boolean',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'download_count' => 'integer',
        'view_count' => 'integer',
        'bookmark_count' => 'integer',
        'file_size' => 'integer',
        'marking_scheme_size' => 'integer',
    ];

    /**
     * Get the subject that owns the exam.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the user who created the exam.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the exam.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the users who bookmarked this exam.
     */
    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks')
            ->withTimestamps();
    }

    /**
     * Get the downloads for this exam.
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    /**
     * Check if exam has marking scheme.
     */
    public function hasMarkingScheme(): bool
    {
        return $this->has_marking_scheme && $this->marking_scheme_path !== null;
    }

    /**
     * Get formatted file size.
     */
    public function getFileSizeFormattedAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Get formatted marking scheme size.
     */
    public function getMarkingSchemeSizeFormattedAttribute(): ?string
    {
        if (!$this->marking_scheme_size) {
            return null;
        }
        
        $bytes = $this->marking_scheme_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Get PDF URL.
     */
    public function getPdfUrlAttribute(): ?string
    {
        if (!$this->pdf_path) {
            return null;
        }
        
        return asset('storage/' . $this->pdf_path);
    }

    /**
     * Get marking scheme URL.
     */
    public function getMarkingSchemeUrlAttribute(): ?string
    {
        if (!$this->marking_scheme_path) {
            return null;
        }
        
        return asset('storage/' . $this->marking_scheme_path);
    }

    /**
     * Increment download count.
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}

