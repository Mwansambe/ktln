# Chapter 7: Building Models with Eloquent

## Introduction
In this chapter, you will turn your database tables into working Laravel models. You will learn how Eloquent maps rows to PHP objects and how relationships make your Paperchase code simpler and safer.

## Learning Objectives
By the end of this chapter, you can:
- Create and configure Eloquent models
- Define model relationships for `User`, `Exam`, `Subject`, `Bookmark`, and `Download`
- Use casts, scopes, accessors, and mutators
- Write clean, reusable query logic

## 1. Inspect Existing Models
Paperchase already includes these models:
- `app/Models/User.php`
- `app/Models/Subject.php`
- `app/Models/Exam.php`
- `app/Models/Bookmark.php`
- `app/Models/Download.php`

Open each file and identify:
1. `$fillable`
2. `$casts`
3. Relationship methods
4. Any helper methods and scopes

## 2. Build a Subject Model from Scratch

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
```

Why this works:
- `fillable` protects against mass assignment vulnerabilities
- `casts` guarantees predictable data types
- `scopeActive()` keeps filtering logic reusable

## 3. Define Relationships for Paperchase

### User relationships
```php
public function bookmarks()
{
    return $this->hasMany(Bookmark::class);
}

public function downloads()
{
    return $this->hasMany(Download::class);
}
```

### Exam relationships
```php
public function subject()
{
    return $this->belongsTo(Subject::class);
}

public function bookmarks()
{
    return $this->hasMany(Bookmark::class);
}

public function downloads()
{
    return $this->hasMany(Download::class);
}
```

### Bookmark relationships
```php
public function user()
{
    return $this->belongsTo(User::class);
}

public function exam()
{
    return $this->belongsTo(Exam::class);
}
```

## 4. Add Query Scopes
Scopes keep controllers thin.

```php
public function scopePublished($query)
{
    return $query->where('is_published', true);
}

public function scopeForYear($query, int $year)
{
    return $query->where('year', $year);
}

public function scopeForType($query, string $type)
{
    return $query->where('type', $type);
}
```

Usage:

```php
$exams = Exam::query()
    ->published()
    ->forYear(2025)
    ->forType('MOCK')
    ->latest()
    ->get();
```

## 5. Accessors and Mutators
Use these to normalize or format data.

```php
protected function title(): Attribute
{
    return Attribute::make(
        get: fn (string $value) => ucwords($value),
        set: fn (string $value) => trim($value)
    );
}
```

Practical use in Paperchase:
- Clean exam titles during create/update
- Standardize subject codes to uppercase

## 6. UUID vs Auto-Increment IDs
Paperchase may use UUIDs for exams. Benefits:
- Harder to guess URLs
- Better for distributed systems
- Safer public-facing identifiers

When using UUID:
- In migration: `$table->uuid('id')->primary();`
- In model:

```php
protected $keyType = 'string';
public $incrementing = false;
```

## 7. Service-Like Model Methods
For beginners, keep logic in controllers first. As complexity grows, add model methods:

```php
public function recordDownloadBy(User $user): Download
{
    return $this->downloads()->create([
        'user_id' => $user->id,
        'downloaded_at' => now(),
    ]);
}
```

## 8. Common Mistakes
- Forgetting `fillable` then getting mass assignment exceptions
- Using wrong relationship type (`hasMany` instead of `belongsTo`)
- Not eager-loading relations, causing N+1 query problems

Fix N+1:

```php
$exams = Exam::with(['subject', 'downloads'])->paginate(15);
```

## Hands-On Exercise
1. Add `scopePopular()` to `Exam` using `downloads_count`.
2. Add `scopeWithSearch($query, string $term)` for title/code search.
3. Update one controller to use your new scopes.
4. Test in `php artisan tinker`.

## Challenge Extension
Implement soft deletes for exams:
- Add `deleted_at` in migration
- Use `SoftDeletes` in model
- Update list endpoints to exclude trashed records by default

## Summary
You now have the Eloquent foundation of Paperchase. In the next chapter, you will use these models inside controllers to implement full CRUD workflows and business rules.
