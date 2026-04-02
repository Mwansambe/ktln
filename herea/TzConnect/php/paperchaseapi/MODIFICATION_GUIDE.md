# Laravel Web Application Modification Guide

## Table of Contents
1. [Project Structure](#project-structure)
2. [Getting Started](#getting-started)
3. [Adding New Features](#adding-new-features)
4. [Database Operations](#database-operations)
5. [Creating Routes](#creating-routes)
6. [Building Controllers](#building-controllers)
7. [Creating Views](#creating-views)
8. [API Endpoints](#api-endpoints)
9. [Authentication & Authorization](#authentication--authorization)
10. [Testing Changes](#testing-changes)
11. [Deployment](#deployment)
12. [Common Tasks](#common-tasks)

---

## Project Structure

### Directory Overview

```
php/paperchaseapi/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Web and API controllers
│   │   │   ├── Auth/             # Authentication controllers
│   │   │   ├── Web/              # Web page controllers (dashboard, exams, etc.)
│   │   │   └── *.php             # API controllers
│   │   └── Requests/             # Form validation classes
│   │       └── Auth/
│   ├── Models/                   # Database models (User, Exam, Subject, etc.)
│   └── Providers/                # Service providers
├── resources/
│   ├── views/                    # Blade templates
│   │   ├── layouts/              # Main layout files
│   │   ├── auth/                 # Login/register pages
│   │   ├── components/           # Reusable components (navbar, footer)
│   │   ├── dashboard/            # Dashboard pages
│   │   ├── exams/                # Exam management pages
│   │   ├── subjects/             # Subject/category pages
│   │   └── users/                # User management pages
│   ├── css/                      # Stylesheets
│   └── js/                       # JavaScript files
├── routes/
│   ├── web.php                   # Web routes (HTML pages)
│   └── api.php                   # API routes (JSON responses)
├── database/
│   ├── migrations/               # Database schema changes
│   ├── seeders/                  # Initial data
│   └── factories/                # Test data generators
├── config/                       # Configuration files
├── public/                       # Public assets
└── storage/                      # File uploads, logs

```

### Key Files to Know

| File | Purpose |
|------|---------|
| `routes/web.php` | Define all web page routes |
| `routes/api.php` | Define all API endpoints |
| `app/Models/User.php` | User model with roles |
| `app/Models/Exam.php` | Exam model with relationships |
| `app/Models/Subject.php` | Subject/Category model |
| `resources/views/layouts/app.blade.php` | Main HTML layout |
| `.env` | Environment configuration |
| `package.json` | PHP dependencies |

---

## Getting Started

### 1. Start the Server

```bash
cd php/paperchaseapi
php artisan serve
# Server runs on http://127.0.0.1:8001
```

### 2. Clear Caches Before Testing

Always clear caches after making changes:

```bash
php artisan optimize:clear
```

Or use individual commands:

```bash
php artisan cache:clear      # Clear application cache
php artisan view:clear       # Clear compiled views
php artisan route:clear      # Clear cached routes
php artisan config:clear     # Clear config cache
```

### 3. Check Logs

View errors and debug information:

```bash
tail -100 storage/logs/laravel.log
```

### 4. Access Database

```bash
# Connect to PostgreSQL
PGPASSWORD=admin@123 psql -h 127.0.0.1 -U postgres -d paperchase

# In psql, useful commands:
\dt                    # List all tables
\d subjects           # Show schema of 'subjects' table
SELECT * FROM users;  # Query users
\q                    # Quit psql
```

---

## Adding New Features

### Example: Adding a New Exam Status Feature

Let's say you want to add an "archived" status to exams.

#### Step 1: Create a Migration

```bash
php artisan make:migration add_archived_to_exams --table=exams
```

This creates a file: `database/migrations/2026_02_24_XXXXXX_add_archived_to_exams.php`

Edit it:

```php
<?php

class AddArchivedToExams extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('is_archived');
        });
    }
}
```

Run the migration:

```bash
php artisan migrate
```

#### Step 2: Update the Model

Edit `app/Models/Exam.php`:

```php
public function up(): void
{
    // In fillable array, add 'is_archived':
    protected $fillable = [
        // ... existing fields ...
        'is_archived',  // Add this
    ];

    // In casts array, add boolean cast:
    protected $casts = [
        // ... existing casts ...
        'is_archived' => 'boolean',
    ];

    // Add scope method for filtering:
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }
}
```

#### Step 3: Update Controller

Edit `app/Http/Controllers/Web/ExamController.php`:

```php
public function index(Request $request)
{
    $exams = Exam::when($request->has('archived') && $request->archived == 'true', 
        fn($q) => $q->archived(),
        fn($q) => $q->active()
    )->get();

    return view('exams.index', ['exams' => $exams]);
}

public function archive(Exam $exam)
{
    $exam->update(['is_archived' => true]);
    return redirect()->back()->with('success', 'Exam archived successfully');
}

public function unarchive(Exam $exam)
{
    $exam->update(['is_archived' => false]);
    return redirect()->back()->with('success', 'Exam restored');
}
```

#### Step 4: Add Routes

Edit `routes/web.php`:

```php
Route::middleware('auth')->group(function () {
    Route::post('exams/{exam}/archive', [ExamController::class, 'archive'])->name('exams.archive');
    Route::post('exams/{exam}/unarchive', [ExamController::class, 'unarchive'])->name('exams.unarchive');
});
```

#### Step 5: Update View

Edit `resources/views/exams/index.blade.php`, add button:

```blade
<button onclick="document.getElementById('archive-form-{{ $exam->id }}').submit()">
    Archive
</button>

<form id="archive-form-{{ $exam->id }}" 
      action="{{ route('exams.archive', $exam) }}" 
      method="POST" 
      style="display:none">
    @csrf
    @method('POST')
</form>
```

#### Step 6: Test

```bash
# Clear caches
php artisan optimize:clear

# Visit the page and test
# http://127.0.0.1:8001/exams
```

---

## Database Operations

### Creating Models with Database

```bash
# Create model with migration
php artisan make:model Topic -m

# Create model with migration and factory
php artisan make:model Topic -mf
```

### Writing Migrations

**Create Migration:**

```bash
php artisan make:migration create_topics_table
```

**Edit the migration file:**

```php
<?php

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->uuid('exam_id');
            
            // Foreign key
            $table->foreign('exam_id')
                ->references('id')
                ->on('exams')
                ->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
```

**Common Column Types:**

```php
$table->uuid('id')->primary();           // UUID primary key
$table->string('name');                  // VARCHAR(255)
$table->string('email', 100);            // VARCHAR(100)
$table->text('description');             // TEXT
$table->integer('count')->default(0);    // INTEGER
$table->boolean('is_active')->default(true);
$table->enum('status', ['active', 'inactive']);
$table->decimal('price', 8, 2);          // 8 digits, 2 decimals
$table->timestamp('created_at');         // TIMESTAMP
$table->dateTime('published_at');        // DATETIME
$table->date('release_date');            // DATE
$table->timestamps();                    // created_at, updated_at
$table->softDeletes();                   // Soft delete column
```

**Run Migration:**

```bash
php artisan migrate              # Run all pending migrations
php artisan migrate:rollback     # Undo last batch
php artisan migrate:refresh      # Rollback and re-run all
php artisan migrate:fresh --seed # Wipe database and reseed
```

### Seeding Data

```bash
# Create seeder
php artisan make:seeder TopicSeeder
```

**Edit `database/seeders/TopicSeeder.php`:**

```php
<?php

namespace Database\Seeders;

use App\Models\Topic;
use App\Models\Exam;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $exams = Exam::all();
        
        foreach ($exams as $exam) {
            Topic::create([
                'name' => 'Topic for ' . $exam->title,
                'description' => 'Sample topic description',
                'exam_id' => $exam->id,
            ]);
        }
    }
}
```

**Run seeder:**

```bash
php artisan db:seed --class=TopicSeeder
```

---

## Creating Routes

### Web Routes (HTML Pages)

Edit `routes/web.php`:

```php
<?php

use App\Http\Controllers\Web\ExamController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
})->name('home');

// Guest routes (only non-authenticated users)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Protected routes (authenticated users only)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Resource routes (auto-generates 7 routes)
    Route::resource('exams', ExamController::class);
    
    // Custom routes
    Route::get('exams/{exam}/download', [ExamController::class, 'download'])->name('exams.download');
    Route::post('exams/{exam}/archive', [ExamController::class, 'archive'])->name('exams.archive');
    
    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
```

**Route Methods:**

```php
Route::get('path', Controller@method);           // GET request
Route::post('path', Controller@method);          // POST request
Route::put('path', Controller@method);           // PUT request
Route::delete('path', Controller@method);        // DELETE request
Route::patch('path', Controller@method);         // PATCH request

Route::resource('exams', ExamController::class); // RESTful routes

// Grouped routes
Route::prefix('admin')->group(function () {     // /admin/dashboard
    Route::get('dashboard', ...);
});

Route::middleware('auth')->group(function () {  // Protected routes
    Route::get('profile', ...);
});

// Named routes
Route::get('about', ...)->name('about');         // Access: route('about')
```

### API Routes (JSON Responses)

Edit `routes/api.php`:

```php
<?php

use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Route;

// Public APIs
Route::get('/exams', [ExamController::class, 'index']);
Route::get('/subjects', [SubjectController::class, 'index']);

// Protected APIs (requires authentication)
Route::middleware('auth:api')->group(function () {
    Route::post('/exams/{exam}/bookmark', [ExamController::class, 'bookmark']);
    Route::post('/exams/{exam}/download', [ExamController::class, 'download']);
    Route::get('/user/downloads', [UserController::class, 'downloads']);
});
```

**Test API Routes:**

```bash
# GET request
curl http://127.0.0.1:8001/api/subjects

# POST with JSON
curl -X POST http://127.0.0.1:8001/api/exams \
  -H "Content-Type: application/json" \
  -d '{"title":"New Exam"}'
```

---

## Building Controllers

### Create a Controller

```bash
# Basic controller
php artisan make:controller ExamController

# Controller with resource methods (index, create, store, etc.)
php artisan make:controller ExamController --resource

# API controller
php artisan make:controller ExamController --api
```

### Resource Controller Methods

```php
<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    // Show list of exams
    public function index()
    {
        $exams = Exam::paginate(15);
        return view('exams.index', ['exams' => $exams]);
    }

    // Show form to create exam
    public function create()
    {
        return view('exams.create');
    }

    // Store exam in database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:exams',
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'year' => 'required|integer',
            'pdf_path' => 'required|file|mimes:pdf',
        ]);

        $validatedData = $request->validated();
        
        $exam = Exam::create($validatedData);

        return redirect(route('exams.show', $exam))->with('success', 'Exam created');
    }

    // Show single exam
    public function show(Exam $exam)
    {
        return view('exams.show', ['exam' => $exam]);
    }

    // Show form to edit exam
    public function edit(Exam $exam)
    {
        return view('exams.edit', ['exam' => $exam]);
    }

    // Update exam
    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'required|integer',
        ]);

        $exam->update($validated);

        return redirect(route('exams.show', $exam))->with('success', 'Exam updated');
    }

    // Delete exam
    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect(route('exams.index'))->with('success', 'Exam deleted');
    }
}
```

### API Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExamController extends Controller
{
    public function index(): JsonResponse
    {
        $exams = Exam::with(['subject', 'creator'])
            ->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $exams,
            'message' => 'Exams retrieved successfully'
        ]);
    }

    public function show(Exam $exam): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $exam->load(['subject', 'creator']),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $exam = Exam::create($validated);

        return response()->json([
            'success' => true,
            'data' => $exam,
            'message' => 'Exam created successfully'
        ], Response::HTTP_CREATED);
    }

    public function destroy(Exam $exam): JsonResponse
    {
        $exam->delete();

        return response()->json([
            'success' => true,
            'message' => 'Exam deleted successfully'
        ]);
    }
}
```

---

## Creating Views

### Blade Template Syntax

Edit `resources/views/exams/index.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Exams</h1>
    
    <!-- Show success message -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Show validation errors -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Create button -->
    <a href="{{ route('exams.create') }}" class="btn btn-primary">Create Exam</a>

    <!-- List exams -->
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Subject</th>
                <th>Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($exams as $exam)
                <tr>
                    <td>{{ $exam->title }}</td>
                    <td>{{ $exam->subject->name }}</td>
                    <td>{{ $exam->year }}</td>
                    <td>
                        <a href="{{ route('exams.show', $exam) }}" class="btn btn-info">View</a>
                        <a href="{{ route('exams.edit', $exam) }}" class="btn btn-warning">Edit</a>
                        
                        <!-- Delete form -->
                        <form action="{{ route('exams.destroy', $exam) }}" 
                              method="POST" 
                              style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No exams found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    {{ $exams->links() }}
</div>
@endsection
```

### Common Blade Directives

```blade
<!-- Variables -->
{{ $variable }}                    <!-- Echo with escaping -->
{!! $html !!}                      <!-- Echo without escaping (HTML) -->

<!-- Control Structures -->
@if($condition)
    <!-- code -->
@elseif($other)
    <!-- code -->
@else
    <!-- code -->
@endif

<!-- Loops -->
@foreach($items as $item)
    {{ $item->name }}
@endforeach

@forelse($items as $item)
    {{ $item->name }}
@empty
    <p>No items</p>
@endforelse

<!-- Forms -->
<form action="{{ route('exams.store') }}" method="POST">
    @csrf
    @method('PUT')
    
    <input type="text" name="title" value="{{ old('title') }}">
    @error('title')
        <span class="error">{{ $message }}</span>
    @enderror
</form>

<!-- Authentication -->
@auth
    <p>Logged in as {{ Auth::user()->name }}</p>
@endauth

@guest
    <p>Not logged in</p>
@endguest

<!-- Authorization -->
@can('edit', $exam)
    <a href="{{ route('exams.edit', $exam) }}">Edit</a>
@endcan

<!-- Components -->
@include('components.navbar')
<x-alert type="success">Message</x-alert>

<!-- Layout inheritance -->
@extends('layouts.app')
@section('content') ... @endsection
```

### Create a Form View

Edit `resources/views/exams/create.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Exam</h1>

    <form action="{{ route('exams.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Code Field -->
        <div class="form-group">
            <label for="code">Exam Code</label>
            <input type="text" 
                   name="code" 
                   id="code"
                   value="{{ old('code') }}"
                   class="form-control @error('code') is-invalid @enderror"
                   required>
            @error('code')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Title Field -->
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" 
                   name="title" 
                   id="title"
                   value="{{ old('title') }}"
                   class="form-control @error('title') is-invalid @enderror"
                   required>
            @error('title')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Subject Field -->
        <div class="form-group">
            <label for="subject_id">Subject</label>
            <select name="subject_id" id="subject_id" 
                    class="form-control @error('subject_id') is-invalid @enderror"
                    required>
                <option value="">-- Select Subject --</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" 
                            @selected(old('subject_id') == $subject->id)>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
            @error('subject_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Year Field -->
        <div class="form-group">
            <label for="year">Year</label>
            <input type="number" 
                   name="year" 
                   id="year"
                   value="{{ old('year') }}"
                   class="form-control @error('year') is-invalid @enderror"
                   required>
            @error('year')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- PDF File -->
        <div class="form-group">
            <label for="pdf_path">PDF File</label>
            <input type="file" 
                   name="pdf_path" 
                   id="pdf_path"
                   accept=".pdf"
                   class="form-control @error('pdf_path') is-invalid @enderror"
                   required>
            @error('pdf_path')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Exam</button>
        <a href="{{ route('exams.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
```

---

## API Endpoints

### Creating API Endpoints

Edit `routes/api.php`:

```php
<?php

use App\Http\Controllers\ExamController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

// Public endpoints
Route::get('/exams', [ExamController::class, 'index']);
Route::get('/exams/{exam}', [ExamController::class, 'show']);
Route::get('/subjects', [SubjectController::class, 'index']);

// Protected endpoints (require authentication)
Route::middleware('auth:api')->group(function () {
    Route::post('/exams', [ExamController::class, 'store']);
    Route::put('/exams/{exam}', [ExamController::class, 'update']);
    Route::delete('/exams/{exam}', [ExamController::class, 'destroy']);
    
    Route::post('/exams/{exam}/bookmark', [ExamController::class, 'bookmark']);
    Route::post('/exams/{exam}/download', [ExamController::class, 'download']);
});
```

### API Response Format

```php
// Success response
return response()->json([
    'success' => true,
    'data' => $exam,
    'message' => 'Exam retrieved successfully'
], 200);

// Error response
return response()->json([
    'success' => false,
    'error' => 'Exam not found',
    'message' => 'The requested exam does not exist'
], 404);

// Paginated response
return response()->json([
    'success' => true,
    'data' => $exams->items(),
    'pagination' => [
        'total' => $exams->total(),
        'per_page' => $exams->perPage(),
        'current_page' => $exams->currentPage(),
        'last_page' => $exams->lastPage(),
    ]
]);
```

### Testing API Endpoints

```bash
# GET request
curl http://127.0.0.1:8001/api/exams

# GET with query parameter
curl "http://127.0.0.1:8001/api/exams?limit=10&page=1"

# POST request
curl -X POST http://127.0.0.1:8001/api/exams \
  -H "Content-Type: application/json" \
  -d '{
    "title": "New Exam",
    "subject_id": "uuid-here",
    "year": 2024
  }'

# PUT request (update)
curl -X PUT http://127.0.0.1:8001/api/exams/exam-id \
  -H "Content-Type: application/json" \
  -d '{"title": "Updated Title"}'

# DELETE request
curl -X DELETE http://127.0.0.1:8001/api/exams/exam-id
```

---

## Authentication & Authorization

### User Roles

The application has 3 user roles:

1. **ADMIN** - Full access to everything
2. **EDITOR** - Can create and edit exams
3. **VIEWER** - Can only view exams

### Check Authentication

```php
// In controllers
if (Auth::check()) {
    $user = Auth::user();
    echo $user->name;
}

// In Blade views
@auth
    <p>Logged in as {{ Auth::user()->name }}</p>
@endauth

@guest
    <p>Please log in</p>
@endguest
```

### Check User Role

```php
// In controllers
$user = Auth::user();

if ($user->isAdmin()) {
    // Admin-only code
}

if ($user->isEditor()) {
    // Editor or admin code
}

// Or check role directly
if ($user->role === 'ADMIN') {
    // Admin-only code
}
```

### In Blade Templates

```blade
@if(Auth::user()->isAdmin())
    <a href="{{ route('users.index') }}">Manage Users</a>
@endif

@if(Auth::user()->isEditor())
    <a href="{{ route('exams.create') }}">Create Exam</a>
@endif
```

### Protect Routes

```php
// Only authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', ...);
});

// Only guests (not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', ...);
});

// Only specific role
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
});
```

### Create Policies (Authorization)

```bash
php artisan make:policy ExamPolicy --model=Exam
```

Edit `app/Policies/ExamPolicy.php`:

```php
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Exam;
use Illuminate\Auth\Access\Response;

class ExamPolicy
{
    public function view(User $user, Exam $exam): bool
    {
        return true; // Anyone can view
    }

    public function create(User $user): bool
    {
        return $user->isEditor(); // Only editors can create
    }

    public function update(User $user, Exam $exam): bool
    {
        // Only the creator or admin can edit
        return $user->id === $exam->created_by || $user->isAdmin();
    }

    public function delete(User $user, Exam $exam): bool
    {
        // Only admin can delete
        return $user->isAdmin();
    }
}
```

Use in controller:

```php
public function edit(Exam $exam)
{
    $this->authorize('update', $exam);
    return view('exams.edit', ['exam' => $exam]);
}
```

---

## Testing Changes

### Before Pushing to Production

#### 1. Clear Caches

```bash
php artisan optimize:clear
```

#### 2. Test Database Changes

```bash
# Check if migrations are pending
php artisan migrate:status

# Run migrations
php artisan migrate

# Rollback if needed
php artisan migrate:rollback
```

#### 3. Test Routes

```bash
# List all routes
php artisan route:list

# Test specific route
curl http://127.0.0.1:8001/dashboard
```

#### 4. Check Logs

```bash
tail -50 storage/logs/laravel.log
```

#### 5. Test in Browser

```
http://127.0.0.1:8001/login
→ Login with: admin@paperchase.local / admin123
→ Navigate to dashboard and pages
→ Check for errors in console
```

### Write Tests (Optional)

```bash
# Create test
php artisan make:test ExamTest
```

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Exam;
use Tests\TestCase;

class ExamTest extends TestCase
{
    public function test_user_can_view_exam_list()
    {
        $user = User::first();
        $response = $this->actingAs($user)->get('/exams');
        $response->assertStatus(200);
    }

    public function test_editor_can_create_exam()
    {
        $editor = User::where('role', 'EDITOR')->first();
        $response = $this->actingAs($editor)->post('/exams', [
            'title' => 'Test Exam',
            'subject_id' => '...',
            'year' => 2024,
        ]);
        $response->assertRedirect();
    }
}
```

Run tests:

```bash
php artisan test
```

---

## Deployment

### Production Checklist

Before deploying to production:

- [ ] Clear caches: `php artisan optimize:clear`
- [ ] Run migrations: `php artisan migrate`
- [ ] Update environment: Edit `.env` for production values
- [ ] Set debug mode: `APP_DEBUG=false` in `.env`
- [ ] Generate app key: `php artisan key:generate`
- [ ] Cache config: `php artisan config:cache`
- [ ] Test login credentials
- [ ] Test critical features
- [ ] Check logs: `tail -100 storage/logs/laravel.log`

### Deploy to Server

1. **Push code to GitHub**

```bash
cd /home/mwansambe/Documents/github-projects/paperchaseadmin
git add -A
git commit -m "Your changes here"
git push origin main
```

2. **SSH into server**

```bash
ssh january@161.97.79.248
cd /opt/paperchase
git pull origin main
```

3. **Run deployment script**

```bash
./deploy.sh 161.97.79.248 22 chasepaper.duckdns.org
```

4. **Verify**

```bash
# Check if running
curl https://chasepaper.duckdns.org/login

# Check logs
tail -50 storage/logs/laravel.log
```

---

## Common Tasks

### Task 1: Add a New Database Field

**Step 1: Create migration**
```bash
php artisan make:migration add_color_to_subjects --table=subjects
```

**Step 2: Edit migration**
```php
public function up(): void {
    Schema::table('subjects', function (Blueprint $table) {
        $table->string('color')->default('#000000');
    });
}
```

**Step 3: Update Model**
```php
// In Subject.php
protected $fillable = [..., 'color'];
protected $casts = [..., 'color' => 'string'];
```

**Step 4: Run migration**
```bash
php artisan migrate
php artisan optimize:clear
```

---

### Task 2: Add Authentication to a Route

```php
// Before
Route::get('/profile', [UserController::class, 'profile']);

// After
Route::middleware('auth')->get('/profile', [UserController::class, 'profile']);

// Or group multiple routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile']);
    Route::get('/settings', [UserController::class, 'settings']);
});
```

---

### Task 3: Add Validation

**In Controller:**

```php
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email,' . $user->id,
    'age' => 'numeric|min:18|max:120',
    'password' => 'required|min:8|confirmed',
    'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
]);
```

**Common Rules:**

```php
required              // Field is required
string                // Must be string
numeric               // Must be number
email                 // Valid email format
unique:table,column   // Unique in database
min:value             // Minimum value
max:value             // Maximum value
confirmed             // Must match field_confirmation
image                 // Must be image
mimes:jpeg,png        // Specific file types
url                   // Valid URL
date                  // Valid date
regex:/pattern/       // Regex pattern
in:value1,value2      // Must be one of values
```

---

### Task 4: Send Emails

**Create Mailable:**

```bash
php artisan make:mail ExamUploaded
```

**Edit `app/Mail/ExamUploaded.php`:**

```php
public function __construct(public Exam $exam) {}

public function envelope(): Envelope
{
    return new Envelope(
        subject: 'New Exam: ' . $this->exam->title,
    );
}

public function content(): Content
{
    return new Content(
        view: 'emails.exam-uploaded',
        with: ['exam' => $this->exam],
    );
}
```

**Send email in Controller:**

```php
Mail::to($user->email)->send(new ExamUploaded($exam));
```

---

### Task 5: Add File Upload

**In Form:**

```blade
<input type="file" name="pdf" accept=".pdf" required>
```

**In Controller:**

```php
public function store(Request $request)
{
    $request->validate([
        'pdf' => 'required|file|mimes:pdf|max:10240', // 10MB
    ]);

    $file = $request->file('pdf');
    $path = $file->store('exams', 'public'); // Stores in storage/app/public/exams/

    Exam::create([
        'title' => $request->title,
        'pdf_path' => $path,
    ]);
}
```

**In View (retrieve file):**

```blade
<a href="{{ asset('storage/' . $exam->pdf_path) }}" download>
    Download PDF
</a>
```

---

### Task 6: Create API Resource

```bash
php artisan make:resource ExamResource
```

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'code' => $this->code,
        'subject' => $this->subject->name,
        'year' => $this->year,
        'downloads' => $this->download_count,
        'url' => route('exams.show', $this->id),
    ];
}
```

Use in controller:

```php
public function index()
{
    return ExamResource::collection(Exam::paginate());
}
```

---

## Quick Reference

### Important Commands

```bash
# Server
php artisan serve                    # Start dev server

# Cache
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan optimize:clear           # Clear all

# Database
php artisan migrate                  # Run migrations
php artisan migrate:rollback
php artisan db:seed
php artisan tinker                   # Interactive shell

# Code Generation
php artisan make:model Name -m       # Model + migration
php artisan make:controller Name     # Controller
php artisan make:migration name      # Migration
php artisan make:seeder Name         # Seeder
php artisan make:request Name        # Form request

# Debugging
tail -100 storage/logs/laravel.log
php artisan route:list
php artisan config:show
```

### Important Files

| File | Purpose |
|------|---------|
| `.env` | Environment variables |
| `routes/web.php` | Web routes |
| `routes/api.php` | API routes |
| `app/Models/` | Database models |
| `app/Http/Controllers/` | Controllers |
| `resources/views/` | Blade templates |
| `database/migrations/` | Database schemas |
| `config/app.php` | App configuration |

---

## Support & Resources

### Documentation Links

- [Laravel Documentation](https://laravel.com/docs)
- [Blade Template Documentation](https://laravel.com/docs/blade)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [API Documentation](https://laravel.com/docs/routing#api-routes)

### Key Contacts / Project Info

- **Database**: PostgreSQL at 127.0.0.1:5432
- **Server**: http://127.0.0.1:8001
- **Application**: Laravel 12.x
- **PHP Version**: 8.4.18

---

## Tips & Best Practices

1. **Always clear caches** after code changes
2. **Use migrations** for database changes (never modify database directly)
3. **Validate input** on both frontend and backend
4. **Use eloquent relationships** instead of raw queries
5. **Create separate controllers** for Web and API
6. **Comment your code** for other developers
7. **Test before deploying** to production
8. **Keep `.env` out of version control**
9. **Use named routes** instead of hardcoded URLs
10. **Log errors** using Laravel's logging

---

**Last Updated:** February 24, 2026  
**Version:** 1.0  
**For Questions:** Check the logs or refer to Laravel documentation
