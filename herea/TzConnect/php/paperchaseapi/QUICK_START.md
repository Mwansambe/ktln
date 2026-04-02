# Quick Start Guide - Common Modifications

This is a quick reference guide for the most frequent modifications you'll make.

---

## 🚀 Quick Start (First Time)

```bash
# 1. Navigate to project
cd /home/mwansambe/Documents/github-projects/paperchaseadmin/php/paperchaseapi

# 2. Check if server is running
php artisan serve

# 3. Access in browser
# Go to: http://127.0.0.1:8001
# Login: admin@paperchase.local / admin123
```

---

## 📝 Most Common Tasks

### 1️⃣ Add a New Table to Database

```bash
# Step 1: Create migration
php artisan make:migration create_comments_table

# Step 2: Edit database/migrations/XXXX_create_comments_table.php

<?php
return new class extends Migration {
    public function up(): void {
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_id', 36);
            $table->string('exam_id', 36);
            $table->text('content');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('comments');
    }
};

# Step 3: Run migration
php artisan migrate

# Step 4: Clear caches
php artisan optimize:clear
```

---

### 2️⃣ Add a New Column to Existing Table

```bash
# Step 1: Create migration
php artisan make:migration add_verified_to_exams --table=exams

# Step 2: Edit migration
<?php
public function up(): void {
    Schema::table('exams', function (Blueprint $table) {
        $table->boolean('is_verified')->default(false);
    });
}

public function down(): void {
    Schema::table('exams', function (Blueprint $table) {
        $table->dropColumn('is_verified');
    });
}

# Step 3: Run migration
php artisan migrate

# Step 4: Update model (app/Models/Exam.php)
protected $fillable = [..., 'is_verified'];
protected $casts = [..., 'is_verified' => 'boolean'];

# Step 5: Clear caches
php artisan optimize:clear
```

---

### 3️⃣ Create a New Page (Route → Controller → View)

**Step 1: Create route** (routes/web.php)

```php
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
```

**Step 2: Create controller**

```bash
php artisan make:controller Web/ReviewController
```

**Step 3: Add method to controller** (app/Http/Controllers/Web/ReviewController.php)

```php
<?php
namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

class ReviewController
{
    public function index()
    {
        $reviews = Review::paginate(15);
        return view('reviews.index', ['reviews' => $reviews]);
    }
}
```

**Step 4: Create view** (resources/views/reviews/index.blade.php)

```blade
@extends('layouts.app')

@section('title', 'Reviews')

@section('content')
<div class="container">
    <h1>Reviews</h1>
    
    <div class="reviews-list">
        @forelse($reviews as $review)
            <div class="review-item">
                <h3>{{ $review->title }}</h3>
                <p>{{ $review->content }}</p>
                <small>By {{ $review->user->name }}</small>
            </div>
        @empty
            <p>No reviews found</p>
        @endforelse
    </div>

    {{ $reviews->links() }}
</div>
@endsection
```

**Step 5: Clear caches and test**

```bash
php artisan optimize:clear
# Visit http://127.0.0.1:8001/reviews
```

---

### 4️⃣ Create a Form to Save Data

**Step 1: Create route**

```php
// routes/web.php
Route::get('/reviews/create', [ReviewController::class, 'create']);
Route::post('/reviews', [ReviewController::class, 'store']);
```

**Step 2: Add controller methods**

```php
// app/Http/Controllers/Web/ReviewController.php

public function create()
{
    return view('reviews.create');
}

public function store(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string|min:10',
        'rating' => 'required|integer|min:1|max:5',
    ]);

    // Save to database
    Review::create([
        'user_id' => auth()->id(),
        ...$validated
    ]);

    return redirect('/reviews')->with('success', 'Review created!');
}
```

**Step 3: Create form view** (resources/views/reviews/create.blade.php)

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Review</h1>

    <form action="{{ route('reviews.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" 
                   value="{{ old('title') }}" 
                   class="form-control @error('title') is-invalid @enderror"
                   required>
            @error('title')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea name="content" id="content" rows="5"
                      class="form-control @error('content') is-invalid @enderror"
                      required>{{ old('content') }}</textarea>
            @error('content')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="rating">Rating</label>
            <select name="rating" id="rating" 
                    class="form-control @error('rating') is-invalid @enderror"
                    required>
                <option value="">-- Select --</option>
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" @selected(old('rating') == $i)>
                        {{ $i }} Stars
                    </option>
                @endfor
            </select>
            @error('rating')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Create Review</button>
    </form>
</div>
@endsection
```

**Step 4: Clear caches**

```bash
php artisan optimize:clear
```

---

### 5️⃣ Create an API Endpoint

**Step 1: Create route** (routes/api.php)

```php
Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/{review}', [ReviewController::class, 'show']);
Route::post('/reviews', [ReviewController::class, 'store']);
```

**Step 2: Create API controller**

```bash
php artisan make:controller ReviewController --api
```

**Step 3: Add methods** (app/Http/Controllers/ReviewController.php)

```php
<?php
namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController
{
    public function index()
    {
        $reviews = Review::paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    public function show(Review $review)
    {
        return response()->json([
            'success' => true,
            'data' => $review,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review = Review::create($validated);

        return response()->json([
            'success' => true,
            'data' => $review,
            'message' => 'Review created successfully'
        ], 201);
    }
}
```

**Step 4: Test with curl**

```bash
# GET all reviews
curl http://127.0.0.1:8001/api/reviews

# GET single review
curl http://127.0.0.1:8001/api/reviews/review-id

# POST (create review)
curl -X POST http://127.0.0.1:8001/api/reviews \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Great Exam",
    "content": "This exam helped me prepare",
    "rating": 5
  }'
```

---

### 6️⃣ Add New Model with Everything

```bash
# This creates model + migration + controller + resource
php artisan make:model Comment -mcr
```

Then edit:
- **Migration**: `database/migrations/XXXX_create_comments_table.php`
- **Controller**: `app/Http/Controllers/CommentController.php`
- **Routes**: Add routes in `routes/web.php` or `routes/api.php`
- **Views**: Create in `resources/views/comments/`

---

### 7️⃣ Fix "Page Expired" (419) Error

```blade
<!-- Always add @csrf in forms -->
<form method="POST" action="/some-route">
    @csrf  <!-- This line is required -->
    <!-- form fields -->
</form>
```

---

### 8️⃣ Fix Validation Errors Not Showing

```blade
<!-- Display all errors -->
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Display field-specific error -->
<input type="email" name="email" value="{{ old('email') }}">
@error('email')
    <span class="error">{{ $message }}</span>
@enderror
```

---

### 9️⃣ Protect a Route (Only Authenticated Users)

```php
// Single route
Route::middleware('auth')->get('/profile', [UserController::class, 'profile']);

// Multiple routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile']);
    Route::get('/settings', [UserController::class, 'settings']);
    Route::post('/profile', [UserController::class, 'updateProfile']);
});
```

---

### 🔟 Protect a Route (Only Admins)

```php
// Create custom middleware (optional)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('subjects', SubjectController::class);
});

// Or check in controller
public function store(Request $request)
{
    if (!auth()->user()->isAdmin()) {
        abort(403, 'Unauthorized');
    }
    
    // Process request
}

// Or in Blade
@if(auth()->user()->isAdmin())
    <!-- Admin-only content -->
@endif
```

---

## 🔧 Troubleshooting

### Problem: Changes not showing

```bash
php artisan optimize:clear
```

### Problem: Database is empty

```bash
php artisan migrate:fresh --seed
```

### Problem: "Class not found"

```bash
composer dump-autoload
php artisan optimize:clear
```

### Problem: "Undefined variable"

Check:
1. Is the variable passed from controller to view?
2. Is the route pointing to the correct controller?
3. Check spelling

### Problem: Form not submitting

```blade
<!-- Make sure of these in your form: -->
<form method="POST" action="{{ route('route-name') }}">
    @csrf              <!-- This is required for POST -->
    <!-- fields -->
    <button type="submit">Submit</button>
</form>
```

---

## 📚 File Locations Quick Reference

| What to do | Location |
|-----------|----------|
| Add a web route | `routes/web.php` |
| Add an API route | `routes/api.php` |
| Create/edit controller | `app/Http/Controllers/` |
| Create/edit model | `app/Models/` |
| Create database migration | `database/migrations/` |
| Create Blade view | `resources/views/` |
| Add CSS styles | `resources/css/` |
| Add validation logic | `app/Http/Requests/` |
| Seed test data | `database/seeders/` |
| Configure app settings | `config/` |
| Upload files | `storage/app/public/` |
| Environment variables | `.env` |

---

## ⚡ Essential Commands

```bash
# Start server
php artisan serve

# Run migrations
php artisan migrate

# Clear everything
php artisan optimize:clear

# Check routes
php artisan route:list

# Interactive shell
php artisan tinker

# View logs
tail -100 storage/logs/laravel.log

# Database
PGPASSWORD=admin@123 psql -h 127.0.0.1 -U postgres -d paperchase
```

---

## ✅ Before Going to Production

- [ ] Clear caches: `php artisan optimize:clear`
- [ ] Run migrations: `php artisan migrate`
- [ ] Test login
- [ ] Test create/edit/delete operations
- [ ] Check logs: `tail storage/logs/laravel.log`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Update `.env` database credentials

---

## 📞 Getting Help

1. Check the full **MODIFICATION_GUIDE.md** in the same directory
2. View logs: `tail -100 storage/logs/laravel.log`
3. Check Laravel docs: https://laravel.com/docs

---

**💡 Pro Tips:**

1. Always end routes with semicolon: `Route::get(...);`
2. Always add `@csrf` in POST forms
3. Always validate user input
4. Clear caches after every code change
5. Test locally before deploying
6. Keep backups of `.env` file
7. Use named routes: `route('route-name')` instead of `/path`

---

Last Updated: February 24, 2026
