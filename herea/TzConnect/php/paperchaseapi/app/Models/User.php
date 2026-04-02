<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The primary key type (uuid is a string)
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'is_active',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Check if user has admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    /**
     * Check if user has editor role or higher.
     */
    public function isEditor(): bool
    {
        return in_array($this->role, ['EDITOR', 'ADMIN']);
    }

    /**
     * Check if user can manage content (editor or admin).
     */
    public function canManageContent(): bool
    {
        return in_array($this->role, ['EDITOR', 'ADMIN']);
    }

    /**
     * Check if user can manage users (admin only).
     */
    public function canManageUsers(): bool
    {
        return $this->role === 'ADMIN';
    }

    /**
     * Get the exams created by this user.
     */
    public function exams()
    {
        return $this->hasMany(Exam::class, 'created_by');
    }

    /**
     * Get the bookmarks for this user.
     */
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * Get the downloads for this user.
     */
    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    /**
     * Get total downloads by this user.
     */
    public function getTotalDownloadsAttribute(): int
    {
        return $this->downloads()->count();
    }

    /**
     * Get total bookmarks by this user.
     */
    public function getTotalBookmarksAttribute(): int
    {
        return $this->bookmarks()->count();
    }

    /**
     * Get total exams created by this user.
     */
    public function getTotalExamsCreatedAttribute(): int
    {
        return $this->exams()->count();
    }
}

