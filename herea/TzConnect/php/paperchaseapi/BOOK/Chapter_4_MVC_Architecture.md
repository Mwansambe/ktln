# Chapter 4: Understanding MVC Architecture in Detail

## Introduction

In Chapter 2, we introduced the Model-View-Controller (MVC) architecture that Laravel follows. Now, we'll take a deeper dive into each component and understand how they work together in the Paperchase application.

By the end of this chapter, you will:
- Understand the complete request-response lifecycle in Laravel
- Know how Models interact with the database
- Understand how Controllers process business logic
- Learn how Views render the user interface
- Understand Service Containers and Dependency Injection

## The Request-Response Lifecycle

Before we dive into MVC, let's understand how Laravel handles a web request:

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         LARAVEL REQUEST LIFECYCLE                       │
└─────────────────────────────────────────────────────────────────────────┘

1. REQUEST
   ┌─────────────────────┐
   │  User's Browser     │
   │  GET /exams         │
   └──────────┬──────────┘
              │
              ▼
2. ENTRY POINT
   ┌─────────────────────┐
   │  public/index.php   │
   │  Loads autoloader   │
   │  Creates app        │
   └──────────┬──────────┘
              │
              ▼
3. KERNEL
   ┌─────────────────────┐
   │  HTTP Kernel        │
   │  Middleware stack   │
   │  (auth, CORS, etc)  │
   └──────────┬──────────┘
              │
              ▼
4. ROUTING
   ┌─────────────────────┐
   │  routes/web.php     │
   │  Match URL to       │
   │  Controller         │
   └──────────┬──────────┘
              │
              ▼
5. CONTROLLER
   ┌─────────────────────┐
   │  ExamController     │
   │  Process request    │
   │  Business logic     │
   └──────────┬──────────┘
              │
       ┌──────┴──────┐
       ▼             ▼
6. MODEL         7. DATABASE
   ┌──────────┐    ┌──────────────┐
   │ Eloquent │───►│ PostgreSQL   │
   │ Query    │    │ Fetch data   │
   └──────────┘    └──────────────┘
       │             │
       │             │
       ◄─────────────┘
              │
              ▼
8. VIEW
   ┌─────────────────────┐
   │  exams/index.blade  │
   │  Render HTML        │
   └──────────┬──────────┘
              │
              ▼
9. RESPONSE
   ┌─────────────────────┐
   │  HTTP Response      │
   │  HTML + Headers     │
   └─────────────────────┘
```

## Deep Dive: The Model

The Model represents your data and business logic. In Laravel, Models interact with the database through Eloquent ORM.

### Examining the User Model

Let's look at the User model from Paperchase:

```php
// app/Models/User.php
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
}
```

### Key Model Properties

| Property | Description |
|----------|-------------|
| `$table` | Custom table name (defaults to plural of model name) |
| `$fillable` | Fields that can be mass-assigned |
| `$hidden` | Fields hidden from JSON serialization |
| `$casts` | Automatically convert field types |
| `$keyType` | Type of primary key ('int' or 'string' for UUIDs) |
| `$incrementing` | Whether IDs auto-increment |

### Model Relationships

In Paperchase, the User model has several relationships:

```php
// app/Models/User.php (continued)

// One-to-Many: User has many exams
public function exams()
{
    return $this->hasMany(Exam::class, 'created_by');
}

// One-to-Many: User has many bookmarks
public function bookmarks()
{
    return $this->hasMany(Bookmark::class);
}

// One-to-Many: User has many downloads
public function downloads()
{
    return $this->hasMany(Download::class);
}
```

### Examining the Exam Model

Let's look at another model - the Exam model:

```php
// app/Models/Exam.php
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

    // Relationships
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks')
            ->withTimestamps();
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }
}
```

### Relationship Types in Laravel

Laravel Eloquent provides several relationship types:

```
RELATIONSHIP TYPES IN LARAVEL
────────────────────────────────────────────────────────────

ONE-TO-ONE
┌─────────┐         ┌─────────┐
│  User   │────────►│  UserDetail │
└─────────┘         └─────────┘
hasOne()            belongsTo()

ONE-TO-MANY
┌─────────┐         ┌─────────┐
│ Subject  │────────►│   Exam   │
└─────────┘         └─────────┘
hasMany()           belongsTo()

MANY-TO-MANY
┌─────────┐         ┌──────────┐         ┌─────────┐
│  User   │────────►│ Bookmarks│◄────────│  Exam   │
└─────────┘         └──────────┘         └─────────┘
belongsToMany()     (pivot table)

HAS-MANY-THROUGH
┌─────────┐         ┌─────────┐         ┌─────────┐
│ Country │────────►│  User   │────────►│  Post   │
└─────────┘         └─────────┘         └─────────┘
hasManyThrough()
```

### Eloquent Query Methods

Here are common Eloquent methods for interacting with data:

```php
// Retrieving Data
$users = User::all();                    // Get all users
$user = User::find(1);                   // Find by ID
$user = User::findOrFail(1);              // Find or throw 404
$users = User::where('role', 'ADMIN')->get();
$users = User::orderBy('name')->paginate(10);

// Creating Data
$user = new User();
$user->name = 'John Doe';
$user->email = 'john@example.com';
$user->save();

User::create([
    'name' => 'Jane Doe',
    'email' => 'jane@example.com',
    'password' => bcrypt('password'),
]);

// Updating Data
$user = User::find(1);
$user->name = 'John Smith';
$user->save();

User::where('role', 'USER')->update(['is_active' => false]);

// Deleting Data
$user = User::find(1);
$user->delete();

User::destroy([1, 2, 3]);
User::where('is_active', false)->delete();
```

## Deep Dive: The Controller

Controllers handle the logic between Models and Views. They process user input and return responses.

### Examining the Exam Controller

Let's look at the Web ExamController from Paperchase:

```php
// app/Http/Controllers/Web/ExamController.php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    /**
     * Display a listing of exams.
     */
    public function index(Request $request)
    {
        $query = Exam::with(['subject', 'creator']);

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        // Filter by subject
        if ($request->has('subject') && $request->subject) {
            $query->where('subject_id', $request->subject);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(15);
        $subjects = Subject::orderBy('name')->get();
        $years = Exam::distinct()->pluck('year')->sort()->reverse();

        return view('exams.index', compact('exams', 'subjects', 'years'));
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create()
    {
        $this->authorize('create', Exam::class);
        
        $subjects = Subject::orderBy('name')->get();
        $years = range(date('Y'), date('Y') - 10);
        $types = ['PRACTICE_PAPER', 'MOCK_PAPER', 'PAST_PAPER', 'NECTA_PAPER', 'REVISION_PAPER', 'JOINT_PAPER', 'PRE_NECTA'];

        return view('exams.create', compact('subjects', 'years', 'types'));
    }

    /**
     * Store a newly created exam.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Exam::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|unique:exams,code|max:50',
            'subject_id' => 'required|exists:subjects,id',
            'year' => 'required|string|max:10',
            'type' => 'required|string|in:PRACTICE_PAPER,MOCK_PAPER,PAST_PAPER,NECTA_PAPER,REVISION_PAPER,JOINT_PAPER,PRE_NECTA',
            'description' => 'nullable|string',
            'pdf' => 'nullable|file|mimes:pdf|max:51200',
            'marking_scheme' => 'nullable|file|mimes:pdf|max:51200',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
        ]);

        $exam = new Exam($validated);
        $exam->created_by = auth()->id();
        $exam->updated_by = auth()->id();

        // Handle PDF upload
        if ($request->hasFile('pdf')) {
            $path = $request->file('pdf')->store('exams', 'public');
            $exam->pdf_path = $path;
            $exam->pdf_name = $request->file('pdf')->getClientOriginalName();
            $exam->file_size = $request->file('pdf')->getSize();
        }

        // Handle marking scheme upload
        if ($request->hasFile('marking_scheme')) {
            $path = $request->file('marking_scheme')->store('marking-schemes', 'public');
            $exam->marking_scheme_path = $path;
            $exam->marking_scheme_name = $request->file('marking_scheme')->getClientOriginalName();
            $exam->marking_scheme_size = $request->file('marking_scheme')->getSize();
            $exam->has_marking_scheme = true;
        }

        $exam->save();

        // Update subject exam count
        $exam->subject->increment('exam_count');

        return redirect()->route('exams.index')->with('success', 'Exam created successfully.');
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam)
    {
        return view('exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified exam.
     */
    public function edit(Exam $exam)
    {
        $this->authorize('update', $exam);

        $subjects = Subject::orderBy('name')->get();
        $years = range(date('Y'), date('Y') - 10);
        $types = ['PRACTICE_PAPER', 'MOCK_PAPER', 'PAST_PAPER', 'NECTA_PAPER', 'REVISION_PAPER', 'JOINT_PAPER', 'PRE_NECTA'];

        return view('exams.edit', compact('exam', 'subjects', 'years', 'types'));
    }

    /**
     * Update the specified exam.
     */
    public function update(Request $request, Exam $exam)
    {
        $this->authorize('update', $exam);

        // Validation and update logic...
    }

    /**
     * Remove the specified exam.
     */
    public function destroy(Exam $exam)
    {
        $this->authorize('delete', $exam);

        // Delete files
        if ($exam->pdf_path) {
            Storage::disk('public')->delete($exam->pdf_path);
        }
        if ($exam->marking_scheme_path) {
            Storage::disk('public')->delete($exam->marking_scheme_path);
        }

        // Update subject count
        $exam->subject->decrement('exam_count');

        $exam->delete();

        return redirect()->route('exams.index')->with('success', 'Exam deleted successfully.');
    }
}
```

### Controller Structure

Every controller typically follows this pattern:

```php
class ExampleController extends Controller
{
    // 1. Constructor - for dependency injection and middleware
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 2. index() - List all resources
    public function index() { }

    // 3. create() - Show create form
    public function create() { }

    // 4. store() - Save new resource
    public function store(Request $request) { }

    // 5. show() - Display single resource
    public function show($id) { }

    // 6. edit() - Show edit form
    public function edit($id) { }

    // 7. update() - Update resource
    public function update(Request $request, $id) { }

    // 8. destroy() - Delete resource
    public function destroy($id) { }
}
```

### Route Model Binding

Notice in the controller above how we type-hint the Exam model in the `show()`, `edit()`, `update()`, and `destroy()` methods. This is called Route Model Binding:

```php
// routes/web.php
Route::get('/exams/{exam}', [WebExamController::class, 'show'])->name('exams.show');

// In the controller, Laravel automatically finds the exam
public function show(Exam $exam)
{
    // $exam is already fetched from the database!
    return view('exams.show', compact('exam'));
}
```

## Deep Dive: The View

Views are the presentation layer - what the user sees. Laravel uses Blade templating engine.

### Layout Structure

Paperchase uses a master layout:

```php
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PaperChase')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    @yield('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside>
            @include('layouts.partials.sidebar')
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <header>
                @include('layouts.partials.header')
            </header>

            <main>
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
```

### Extending the Layout

```php
<!-- resources/views/exams/index.blade.php -->
@extends('layouts.app')

@section('title', 'All Exams')

@section('styles')
<style>
    .custom-style { color: blue; }
</style>
@endsection

@section('content')
<div class="container">
    <h1>Exams</h1>
    
    @if($exams->count() > 0)
        @foreach($exams as $exam)
            <div class="exam-card">
                <h2>{{ $exam->title }}</h2>
                <p>{{ $exam->subject->name }}</p>
            </div>
        @endforeach
        
        {{ $exams->links() }}
    @else
        <p>No exams found.</p>
    @endif
</div>
@endsection
```

### Blade Directives

Laravel Blade provides convenient directives:

```blade
{{-- Comments (won't appear in rendered HTML) --}}

{{-- Output variable (escaped) --}}
{{ $name }}
{{ $user->name }}

{!! $html !!}  {{-- Output raw HTML --}}

{{-- If statements --}}
@if($condition)
    <p>True</p>
@elseif($other)
    <p>Other</p>
@else
    <p>False</p>
@endif

@unless($condition)  {{-- Equivalent to @if(!$condition) --}}
    <p>Not true</p>
@endunless

@isset($variable)
    <p>Variable is set</p>
@endisset

@empty($variable)
    <p>Variable is empty</p>
@endempty

{{-- Loops --}}
@for($i = 0; $i < 10; $i++)
    <p>{{ $i }}</p>
@endfor

@foreach($items as $item)
    <p>{{ $item->name }}</p>
@endforeach

@forelse($items as $item)
    <p>{{ $item->name }}</p>
@empty
    <p>No items</p>
@endforelse

@while(true)
    <p>Infinite loop</p>
@endwhile

{{-- Include other views --}}
@include('partials.header')
@includeWhen($condition, 'partials.header')
@includeIf('partials.maybe')

{{-- Component --}}
@component('components.alert')
    @slot('title')
        Alert Title
    @endslot
    Alert content here
@endcomponent

{{-- CSRF Token (for forms) --}}
@csrf

{{-- Method spoofing (for PUT/DELETE) --}}
@method('PUT')
@method('DELETE')

{{-- Authentication --}}
@auth
    <p>Logged in as {{ Auth::user()->name }}</p>
@endauth

@guest
    <p>Please log in</p>
@endguest

{{-- Environment --}}
@production
    <p>Production code</p>
@endproduction
```

## Service Container and Dependency Injection

One of Laravel's most powerful features is the Service Container, which manages class dependencies.

### What is Dependency Injection?

Instead of creating dependencies inside a class, you "inject" them from outside:

```php
// Without Dependency Injection (Bad)
class ExamController extends Controller
{
    public function store(Request $request)
    {
        // Creating dependency inside - hard to test
        $validator = new ExamValidator();
        // ...
    }
}

// With Dependency Injection (Good)
class ExamController extends Controller
{
    protected $validator;
    
    // Dependency injected via constructor
    public function __construct(ExamValidator $validator)
    {
        $this->validator = $validator;
    }
}
```

### Automatic Resolution

Laravel can automatically resolve dependencies:

```php
class ExamController extends Controller
{
    // Laravel automatically injects Request
    public function store(Request $request)
    {
        // Laravel automatically injects Exam model
    }
    
    // Laravel automatically resolves dependencies
    public function show(Exam $exam)
    {
        // $exam is already an Exam instance!
    }
}
```

## Middleware

Middleware provides a way to filter HTTP requests entering your application.

### How Middleware Works

```
REQUEST → Middleware Stack → Controller → Response
         ↓
    ┌────────────┐
    │  Verify    │  ✓ Pass → Next middleware
    │  CSRF     │  ✗ Fail → 403 Error
    └────────────┘
         ↓
    ┌────────────┐
    │  Check     │  ✓ Pass → Next middleware
    │  Auth     │  ✗ Fail → Redirect to login
    └────────────┘
         ↓
    Controller
```

### Using Middleware in Controllers

```php
class ExamController extends Controller
{
    public function __construct()
    {
        // Apply to all methods
        $this->middleware('auth');
        
        // Apply to specific methods
        $this->middleware('verified')->only(['store', 'update', 'destroy']);
        
        // Exclude specific methods
        $this->middleware('auth')->except(['index', 'show']);
    }
}
```

## Summary

In this chapter, you have learned:
- ✅ The complete Laravel request-response lifecycle
- ✅ How Models interact with the database using Eloquent
- ✅ The different types of Eloquent relationships
- ✅ How Controllers process requests and return responses
- ✅ Route Model Binding for automatic model retrieval
- ✅ Blade templating with directives
- ✅ Service Container and Dependency Injection
- ✅ Middleware and how it filters requests

### What's Next?

In Chapter 5, we'll explore the Paperchase project in detail - understanding its features, database design, and how all the pieces fit together.

---

## Practice Exercises

1. **Create a Model**: Create a new model called `Rating` that links users to exams with a score.

2. **Add a Relationship**: Add a `hasMany` relationship to the Subject model for exams.

3. **Create a Controller Method**: Add a `featured()` method to the ExamController that returns only featured exams.

4. **Create a Blade View**: Create a new view that displays a list of subjects with their exam counts.

5. **Explore Route Model Binding**: Modify the routes to use implicit route model binding and verify it works.

6. **Add Middleware**: Create a custom middleware that logs all requests to the application.

---

*Continue to Chapter 5: Paperchase Project Overview*

