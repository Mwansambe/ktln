# Database Models & Relationships Guide

## Overview

This guide explains the database structure and how the models relate to each other.

---

## Entity Relationship Diagram

```
┌────────────────────┐
│     USERS          │
│  ─────────────────  │
│  id (UUID)        │
│  name             │
│  email            │
│  password         │
│  role             │
│  avatar           │
│  is_active        │
│  last_login       │
│  created_at       │
│  updated_at       │
└────────┬───────────┘
         │
         ├─────────────────────────┐
         │                         │
         │ 1 user : M exams       │ 1 user : M bookmarks
         │                         │
         ▼                         ▼
┌──────────────────────┐   ┌─────────────────┐
│  EXAMS              │   │  BOOKMARKS      │
│  ──────────────────  │   │  ─────────────  │
│  id (UUID)          │   │  id (UUID)      │
│  code               │   │  user_id        │
│  title              │   │  exam_id        │
│  subject_id ──┐     │   │  created_at     │
│  year         │     │   ├─────────────────┤
│  type         │     │   │ UNIQUE:         │
│  description  │     │   │ user_id,exam_id │
│  pdf_path     │     │   └─────────────────┘
│  pdf_name     │
│  file_size    │     ┌─────────────────┐
│  marking_...  │     │  DOWNLOADS      │
│  is_featured  │     │  ─────────────  │
│  is_new       │     │  id (UUID)      │
│  download_count     │  user_id        │
│  view_count   │     │  exam_id        │
│  bookmark_count     │  created_at     │
│  created_by   │     │  ─────────────  │
│  updated_by   │     │  Records every  │
│  created_at   │     │  download       │
│  updated_at   │     └─────────────────┘
└───┬──────────────────┘
    │
    │ 1 exam : 1 subject
    │
    ▼
┌──────────────────────┐
│  SUBJECTS           │
│  ──────────────────  │
│  id (UUID)          │
│  name               │
│  icon               │
│  color              │
│  bg_color           │
│  border_color       │
│  description        │
│  created_at         │
│  updated_at         │
└──────────────────────┘
```

---

## Models & Their Relationships

### 1. User Model

**Location:** `app/Models/User.php`

**Fields:**
- `id` - UUID primary key
- `name` - User's full name
- `email` - Email address (unique)
- `password` - Hashed password
- `role` - ADMIN, EDITOR, or VIEWER
- `avatar` - Profile picture URL
- `is_active` - Account status
- `last_login` - Last login timestamp
- `email_verified_at` - Email verification status
- `remember_token` - "Remember me" token
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

**Relations:**

```php
// One user has many exams (created_by)
public function exams()
{
    return $this->hasMany(Exam::class, 'created_by');
}

// One user has many bookmarks
public function bookmarks()
{
    return $this->hasMany(Bookmark::class);
}

// One user has many downloads
public function downloads()
{
    return $this->hasMany(Download::class);
}
```

**Role Methods:**

```php
$user->isAdmin()     // Check if admin (ADMIN role)
$user->isEditor()    // Check if editor or admin
$user->isViewer()    // Check if viewer (default)

// Use in code:
if (auth()->user()->isAdmin()) {
    // Admin-only code
}
```

**Using Relations:**

```php
// Get all exams created by a user
$exams = $user->exams;

// Count bookmarks
$bookmarkCount = $user->bookmarks->count();
// Or with query: $user->bookmarks()->count();

// Get exams user downloaded
$downloadedExamIds = $user->downloads->pluck('exam_id');
$downloadedExams = Exam::whereIn('id', $downloadedExamIds)->get();
```

---

### 2. Subject Model

**Location:** `app/Models/Subject.php`

**Fields:**
- `id` - UUID primary key
- `name` - Subject name (e.g., "Mathematics")
- `icon` - Icon URL or class
- `color` - Text color (hex code)
- `bg_color` - Background color (hex code)
- `border_color` - Border color (hex code)
- `description` - Subject description
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

**Relations:**

```php
// One subject has many exams
public function exams()
{
    return $this->hasMany(Exam::class);
}
```

**Using Relations:**

```php
// Get all exams for a subject
$exams = $subject->exams;

// Get subject with count of exams
$subject->load('exams');
$examCount = $subject->exams->count();

// Query with exams count
$subjects = Subject::withCount('exams')
    ->orderBy('exams_count', 'desc')
    ->get();
```

---

### 3. Exam Model

**Location:** `app/Models/Exam.php`

**Fields:**
- `id` - UUID primary key
- `code` - Exam code (unique identifier, e.g., "EXAM-2024-001")
- `title` - Exam title
- `subject_id` - Foreign key to subjects
- `year` - Exam year (e.g., 2023, 2024)
- `type` - Exam type (PRACTICE_PAPER, MOCK_PAPER, PAST_PAPER, etc.)
- `description` - Detailed description
- `pdf_path` - Path to PDF file
- `pdf_name` - Original PDF filename
- `file_size` - File size in bytes
- `marking_scheme_path` - Path to marking scheme PDF
- `marking_scheme_name` - Marking scheme filename
- `is_featured` - Whether featured on homepage
- `is_new` - Whether marked as new
- `download_count` - Total downloads
- `view_count` - Total views
- `bookmark_count` - Total bookmarks
- `created_by` - User ID who uploaded
- `updated_by` - User ID who last updated
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

**Relations:**

```php
// Exam belongs to a subject
public function subject()
{
    return $this->belongsTo(Subject::class);
}

// Exam has many bookmarks
public function bookmarks()
{
    return $this->hasMany(Bookmark::class);
}

// Exam has many downloads
public function downloads()
{
    return $this->hasMany(Download::class);
}

// Creator of exam
public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

// Last editor
public function editor()
{
    return $this->belongsTo(User::class, 'updated_by');
}
```

**Using Relations:**

```php
// Get subject of an exam
$subject = $exam->subject;
$subjectName = $exam->subject->name;

// Get creator of exam
$creatorName = $exam->creator->name;

// Get all users who bookmarked
$users = $exam->bookmarks;

// Check if user bookmarked
$isBookmarked = $exam->bookmarks()->where('user_id', auth()->id())->exists();

// Load all relations
$exam->load('subject', 'creator', 'bookmarks', 'downloads');
```

**Query Examples:**

```php
// Get exam with subject and creator
$exam = Exam::with(['subject', 'creator'])->find($id);

// Get featured exams
$featured = Exam::where('is_featured', true)->get();

// Get exams from last year
$lastYear = Exam::where('year', now()->year - 1)->get();

// Get most downloaded exams
$popular = Exam::orderBy('download_count', 'desc')
    ->take(10)
    ->get();

// Get exams by type
$pastPapers = Exam::where('type', 'PAST_PAPER')->get();

// Search exams
$results = Exam::where('title', 'like', '%search%')
    ->orWhere('code', 'like', '%search%')
    ->get();
```

---

### 4. Bookmark Model

**Location:** `app/Models/Bookmark.php`

**Fields:**
- `id` - UUID primary key
- `user_id` - Foreign key to users
- `exam_id` - Foreign key to exams
- `created_at` - When bookmarked
- **UNIQUE CONSTRAINT:** `(user_id, exam_id)` - A user can only bookmark an exam once

**Relations:**

```php
// Bookmark belongs to a user
public function user()
{
    return $this->belongsTo(User::class);
}

// Bookmark belongs to an exam
public function exam()
{
    return $this->belongsTo(Exam::class);
}
```

**Using Relations:**

```php
// Get all bookmarked exams for user
$bookmarks = Bookmark::where('user_id', auth()->id())
    ->with('exam')
    ->get();

$exams = $bookmarks->pluck('exam');

// Check if user bookmarked an exam
$isBookmarked = Bookmark::where('user_id', auth()->id())
    ->where('exam_id', $exam->id)
    ->exists();

// Create bookmark (add to model for convenience)
Bookmark::firstOrCreate([
    'user_id' => auth()->id(),
    'exam_id' => $exam->id,
]);

// Remove bookmark
Bookmark::where('user_id', auth()->id())
    ->where('exam_id', $exam->id)
    ->delete();
```

**In Blade View:**

```blade
<!-- Check if bookmarked -->
@php
    $isBookmarked = auth()->check() && 
                    auth()->user()->bookmarks()
                        ->where('exam_id', $exam->id)
                        ->exists();
@endphp

@if($isBookmarked)
    <button class="bookmark-btn active">★ Bookmarked</button>
@else
    <button class="bookmark-btn">☆ Bookmark</button>
@endif
```

---

### 5. Download Model

**Location:** `app/Models/Download.php`

**Fields:**
- `id` - UUID primary key
- `user_id` - Foreign key to users
- `exam_id` - Foreign key to exams
- `created_at` - When downloaded
- **NOTE:** This records every download, so same user can download same exam multiple times

**Relations:**

```php
// Download belongs to a user
public function user()
{
    return $this->belongsTo(User::class);
}

// Download belongs to an exam
public function exam()
{
    return $this->belongsTo(Exam::class);
}
```

**Using Relations:**

```php
// Get all user downloads
$downloads = Download::where('user_id', auth()->id())
    ->with('exam')
    ->get();

// Count how many times user downloaded an exam
$downloadCount = Download::where('user_id', auth()->id())
    ->where('exam_id', $exam->id)
    ->count();

// Get top downloaded exams
$topExams = Download::select('exam_id', DB::raw('count(*) as total'))
    ->groupBy('exam_id')
    ->orderBy('total', 'desc')
    ->take(10)
    ->with('exam')
    ->get();

// Record a download
Download::create([
    'user_id' => auth()->id(),
    'exam_id' => $exam->id,
]);

// Get downloads from last week
$recentDownloads = Download::where('created_at', '>', now()->subWeek())->get();
```

---

## Common Query Patterns

### Get Exams with All Relations

```php
$exam = Exam::with([
    'subject',           // Subject name
    'creator',           // Who uploaded
    'bookmarks',         // Who bookmarked
    'downloads'          // Who downloaded
])->find($id);

// Access them
$exam->subject->name;
$exam->creator->name;
$exam->bookmarks->count();
$exam->downloads->count();
```

### Get User with Their Data

```php
$user = User::with([
    'exams',             // Exams they uploaded
    'bookmarks.exam',    // Exams they bookmarked with exam details
    'downloads.exam'     // Exams they downloaded
])->find($id);
```

### Get Statistics

```php
// Most bookmarked exams
$topBookmarked = Exam::orderBy('bookmark_count', 'desc')
    ->take(10)
    ->get();

// Most downloaded exams
$topDownloaded = Exam::orderBy('download_count', 'desc')
    ->take(10)
    ->get();

// Total exams per subject
$subjectStats = Subject::withCount('exams')
    ->orderBy('exams_count', 'desc')
    ->get();

// User bookmarks count
$bookmarkCount = auth()->user()->bookmarks()->count();

// Exams uploaded by user
$userExams = auth()->user()->exams()->count();
```

### Filter & Search

```php
// By subject
$exams = Exam::where('subject_id', $subjectId)->get();

// By year
$exams = Exam::where('year', 2024)->get();

// By type
$exams = Exam::where('type', 'PAST_PAPER')->get();

// By subject AND year
$exams = Exam::where('subject_id', $subjectId)
    ->where('year', 2024)
    ->get();

// Search in title or code
$exams = Exam::where('title', 'like', '%physics%')
    ->orWhere('code', 'like', '%physics%')
    ->get();

// Featured only
$exams = Exam::where('is_featured', true)->get();

// New exams
$exams = Exam::where('is_new', true)->get();

// Combine multiple filters
$exams = Exam::where('subject_id', $subjectId)
    ->where('year', 2024)
    ->where('type', $type)
    ->orderBy('created_at', 'desc')
    ->paginate(15);
```

---

## Creating Data in Relations

### Create a Bookmark

```php
// Method 1: Using create
$bookmark = Bookmark::create([
    'user_id' => auth()->id(),
    'exam_id' => $exam->id,
]);

// Method 2: Using firstOrCreate (no duplicate)
$bookmark = Bookmark::firstOrCreate(
    [
        'user_id' => auth()->id(),
        'exam_id' => $exam->id,
    ]
);

// Method 3: Using relation
$user->bookmarks()->create([
    'exam_id' => $exam->id,
]);
```

### Create an Exam

```php
// Method 1: Direct create
$exam = Exam::create([
    'code' => 'EXAM-2024-001',
    'title' => 'Mathematics Final',
    'subject_id' => $subject->id,
    'year' => 2024,
    'type' => 'PAST_PAPER',
    'created_by' => auth()->id(),
]);

// Method 2: Using relation
$exam = $subject->exams()->create([
    'code' => 'EXAM-2024-001',
    'title' => 'Mathematics Final',
    'year' => 2024,
    'created_by' => auth()->id(),
]);

// Method 3: Using user relation
$exam = auth()->user()->exams()->create([
    'code' => 'EXAM-2024-001',
    'title' => 'Mathematics Final',
    'subject_id' => $subject->id,
    'year' => 2024,
]);
```

### Update Related Data

```php
// Update exam details
$exam->update([
    'title' => 'Updated Title',
    'year' => 2024,
    'updated_by' => auth()->id(),
]);

// Update bookmark
$bookmark->update([
    // Note: usually bookmarks just have user_id and exam_id
    // but you can add more fields if needed
]);
```

### Delete Related Data

```php
// Delete an exam (will cascade delete bookmarks and downloads)
$exam->delete();

// Delete a bookmark
$bookmark->delete();

// Delete all bookmarks for an exam
$exam->bookmarks()->delete();

// Delete all bookmarks for a user
$user->bookmarks()->delete();
```

---

## Best Practices

### 1. Always Include Relations in Complex Queries

❌ **Bad:**
```php
$exams = Exam::all();
foreach ($exams as $exam) {
    echo $exam->subject->name;  // N+1 query problem!
}
```

✅ **Good:**
```php
$exams = Exam::with('subject')->get();
foreach ($exams as $exam) {
    echo $exam->subject->name;  // Great! Only 2 queries
}
```

### 2. Use Exists Instead of Count for Checking

❌ **Bad:**
```php
if ($exam->bookmarks()->count() > 0) {
    // Bookmarked
}
```

✅ **Good:**
```php
if ($exam->bookmarks()->exists()) {
    // Bookmarked
}
```

### 3. Paginate Large Result Sets

❌ **Bad:**
```php
$exams = Exam::all();  // Loads ALL exams into memory
```

✅ **Good:**
```php
$exams = Exam::paginate(15);  // Loads only 15 per page
```

### 4. Use Fillable Arrays in Models

```php
// In Model
protected $fillable = ['title', 'code', 'subject_id'];

// Then safe to use
Exam::create($request->validated());
```

### 5. Cast Attributes Properly

```php
// In Model
protected $casts = [
    'is_featured' => 'boolean',
    'created_at' => 'datetime',
    'file_size' => 'integer',
];

// Then use naturally
if ($exam->is_featured) { }
```

---

## Database Schema SQL Reference

### Current Schema

```sql
-- Users table
CREATE TABLE users (
    id UUID PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(50) DEFAULT 'VIEWER',
    avatar VARCHAR(255),
    is_active BOOLEAN DEFAULT true,
    last_login TIMESTAMP NULL,
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Subjects table
CREATE TABLE subjects (
    id UUID PRIMARY KEY,
    name VARCHAR(255),
    icon VARCHAR(255),
    color VARCHAR(255),
    bg_color VARCHAR(255),
    border_color VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Exams table
CREATE TABLE exams (
    id UUID PRIMARY KEY,
    code VARCHAR(255) UNIQUE,
    title VARCHAR(255),
    subject_id UUID FOREIGN KEY,
    year VARCHAR(4),
    type VARCHAR(50),
    description TEXT,
    pdf_path VARCHAR(255),
    pdf_name VARCHAR(255),
    file_size BIGINT,
    marking_scheme_path VARCHAR(255),
    marking_scheme_name VARCHAR(255),
    is_featured BOOLEAN DEFAULT false,
    is_new BOOLEAN DEFAULT true,
    download_count INT DEFAULT 0,
    view_count INT DEFAULT 0,
    bookmark_count INT DEFAULT 0,
    created_by UUID FOREIGN KEY,
    updated_by UUID FOREIGN KEY,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Bookmarks table
CREATE TABLE bookmarks (
    id UUID PRIMARY KEY,
    user_id UUID FOREIGN KEY,
    exam_id UUID FOREIGN KEY,
    created_at TIMESTAMP,
    UNIQUE(user_id, exam_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);

-- Downloads table
CREATE TABLE downloads (
    id UUID PRIMARY KEY,
    user_id UUID FOREIGN KEY,
    exam_id UUID FOREIGN KEY,
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);
```

---

## Summary

| Model | Purpose | Main Relations |
|-------|---------|-----------------|
| **User** | User accounts & authentication | Has many exams, bookmarks, downloads |
| **Subject** | Exam categories/subjects | Has many exams |
| **Exam** | Exam papers/documents | Belongs to subject & user; has bookmarks/downloads |
| **Bookmark** | Track bookmarked exams | Belongs to user & exam |
| **Download** | Track downloads | Belongs to user & exam |

---

Last Updated: February 24, 2026
