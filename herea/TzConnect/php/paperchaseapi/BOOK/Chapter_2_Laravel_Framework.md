# Chapter 2: Introduction to Laravel Framework

## What is Laravel?

Laravel is a free, open-source PHP framework designed for web application development. Created by Taylor Otwell in 2011, Laravel has become the most popular PHP framework due to its elegant syntax, powerful features, and excellent documentation.

### Why Use Laravel?

Laravel solves many common development problems:

| Feature | Without Laravel | With Laravel |
|---------|---------------|--------------|
| Routing | Manual URL parsing | Simple route definitions |
| Database | Raw SQL queries | Eloquent ORM |
| Authentication | Build from scratch | Built-in solutions |
| Security | Manual protection | Built-in security |
| Testing | Manual testing | PHPUnit integration |
| API Development | Custom implementation | Laravel Sanctum/Telescope |

### Laravel Philosophy

Laravel is built on these principles:

1. **Expressive**: Clean, readable code that's a joy to write
2. **Productive**: Tools that make developers more productive
3. **Secure**: Built-in protection against common threats
4. **Scalable**: Grows with your application
5. **Community-Driven**: Large, supportive community

### The Laravel Ecosystem

Laravel isn't just a framework – it's a complete ecosystem:

```
Laravel Ecosystem
├── Laravel Framework    - Core framework
├── Laravel Forge       - Server management & deployment
├── Laravel Vapor       - Serverless deployment platform
├── Laravel Envoyer     - Zero-downtime deployment
├── Laravel Spark       - SaaS scaffolding
├── Laravel Nova       - Admin panel
├── Laravel Horizon    - Queue management
├── Laravel Telescope  - Debugging assistant
├── Laravel Sail       - Docker development environment
└── Laravel Sanctum    - API authentication
```

## Understanding MVC Architecture

Laravel follows the **Model-View-Controller (MVC)** design pattern. This separation of concerns makes your code organized and maintainable.

### The MVC Pattern

```
User Request
    │
    ▼
┌─────────────────────────────────────────┐
│           CONTROLLER                    │
│  - Handles user input                   │
│  - Processes logic                      │
│  - Coordinates Model and View           │
└─────────────────────────────────────────┘
    │                    │
    ▼                    ▼
┌──────────┐      ┌──────────────┐
│  MODEL   │      │     VIEW     │
│          │      │              │
│ - Data   │◄────►│ - UI/HTML    │
│ - Logic  │      │ - Templates  │
└──────────┘      └──────────────┘
    │
    ▼
┌─────────────────────────────────────────┐
│          DATABASE                        │
│  - Stores data                          │
│  - Retrieves data                       │
└─────────────────────────────────────────┘
```

### Components in Laravel

#### Model
The Model represents your data and business logic:
- Handles database interactions
- Defines data relationships
- Contains validation rules

```php
// app/Models/User.php
namespace App\Models;

class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
    
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

#### View
The View handles the presentation layer:
- HTML templates
- What users see
- Receives data from controllers

```blade
<!-- resources/views/users/show.blade.php -->
<h1>Welcome, {{ $user->name }}</h1>
```

#### Controller
The Controller mediates between Model and View:
- Receives user requests
- Processes business logic
- Returns responses

```php
// app/Http/Controllers/UserController.php
class UserController extends Controller
{
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show', compact('user'));
    }
}
```

## Laravel Directory Structure

Understanding the Laravel directory structure is crucial for efficient development:

```
laravel-project/
├── app/                    # Application code
│   ├── Console/           # Artisan commands
│   ├── Exceptions/        # Error handling
│   ├── Http/              # HTTP layer
│   │   ├── Controllers/   # Controllers
│   │   ├── Middleware/    # Request filtering
│   │   └── Requests/     # Form validation
│   ├── Models/            # Database models
│   ├── Providers/         # Service providers
│   └── ...
├── bootstrap/             # Framework bootstrap
├── config/                # Configuration files
├── database/              # Database files
│   ├── migrations/       # Schema migrations
│   ├── seeders/         # Sample data
│   └── factories/        # Test data factories
├── public/               # Public web root
├── resources/            # Frontend resources
│   ├── views/           # Blade templates
│   ├── js/              # JavaScript
│   └── css/             # Stylesheets
├── routes/               # Route definitions
├── storage/              # Storage files
├── tests/                # Test files
└── vendor/               # Composer dependencies
```

## Key Laravel Concepts

### 1. Routing

Laravel makes routing simple and expressive:

```php
// routes/web.php

// Basic route
Route::get('/about', function () {
    return 'About Page';
});

// Route with parameters
Route::get('/user/{id}', function ($id) {
    return "User ID: $id";
});

// Named routes
Route::get('/profile', function () {
    return view('profile');
})->name('profile');

// Route groups
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/users', [UserController::class, 'index']);
});
```

### 2. Middleware

Middleware provides a way to filter HTTP requests:

```php
// Check if user is authenticated
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

// Multiple middleware
Route::get('/admin', function () {
    return view('admin');
})->middleware(['auth', 'admin']);
```

### 3. Blade Templates

Laravel's Blade templating engine is powerful and intuitive:

```blade
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'My App')</title>
</head>
<body>
    <nav>
        @section('navigation')
            <a href="/">Home</a>
            <a href="/about">About</a>
        @show
    </nav>
    
    <main>
        @yield('content')
    </main>
    
    <footer>
        @include('partials.footer')
    </footer>
</body>
</html>
```

```blade
<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('title', 'Home Page')

@section('navigation')
    @parent
    <a href="/contact">Contact</a>
@endsection

@section('content')
    <h1>Welcome to My App!</h1>
    
    @if($user)
        <p>Hello, {{ $user->name }}!</p>
    @else
        <p>Please log in.</p>
    @endif
    
    @foreach($posts as $post)
        <article>
            <h2>{{ $post->title }}</h2>
            <p>{{ $post->excerpt }}</p>
        </article>
    @endforeach
    
    @forelse($items as $item)
        <li>{{ $item->name }}</li>
    @empty
        <li>No items found.</li>
    @endforelse
@endsection
```

### 4. Eloquent ORM

Eloquent provides an elegant way to interact with databases:

```php
// Retrieving data
$users = User::all();
$user = User::find(1);
$activeUsers = User::where('active', true)->get();

// Creating data
$user = new User();
$user->name = 'John Doe';
$user->email = 'john@example.com';
$user->save();

// Updating data
$user = User::find(1);
$user->name = 'Jane Doe';
$user->save();

// Deleting data
$user = User::find(1);
$user->delete();

// Relationships
$user = User::with('posts')->find(1);
$posts = $user->posts;
```

### 5. Form Requests

Laravel's form request validation is powerful:

```php
// app/Http/Requests/CreateUserRequest.php
class CreateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'age' => 'nullable|integer|min:18',
        ];
    }
    
    public function messages()
    {
        return [
            'email.required' => 'We need your email address!',
            'password.min' => 'Password must be at least 8 characters.',
        ];
    }
}

// Using in controller
public function store(CreateUserRequest $request)
{
    // Validation passes automatically
    $validated = $request->validated();
    
    User::create($validated);
    
    return redirect('/users')->with('success', 'User created!');
}
```

### 6. Artisan CLI

Artisan is Laravel's command-line interface:

```bash
# General commands
php artisan list                  # List all commands
php artisan help make:model       # Get help for a command

# Creating components
php artisan make:controller UserController
php artisan make:model User
php artisan make:migration create_users_table
php artisan make:request CreateUserRequest
php artisan make:seeder UserSeeder

# Database commands
php artisan migrate               # Run migrations
php artisan migrate:rollback       # Rollback last migration
php artisan migrate:fresh         # Drop all tables and re-migrate
php artisan db:seed               # Seed database
php artisan tinker                # Interactive REPL

# Development server
php artisan serve                 # Start development server
php artisan serve --port=8080    # Custom port

# Cache and config
php artisan cache:clear          # Clear cache
php artisan config:cache          # Cache configuration
php artisan route:cache          # Cache routes
```

### 7. Database Migrations

Migrations are like version control for your database:

```php
// database/migrations/2024_01_01_000001_create_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

### 8. Authentication

Laravel makes authentication simple:

```bash
# Install Laravel Breeze (recommended for beginners)
composer require laravel/breeze --dev
php artisan breeze:install
```

This creates:
- Login and registration pages
- Password reset functionality
- Email verification
- Session management

### 9. Localization

Laravel supports multiple languages:

```php
// resources/lang/en/messages.php
return [
    'welcome' => 'Welcome to our application!',
    'login' => 'Log In',
];

// resources/lang/fr/messages.php
return [
    'welcome' => 'Bienvenue dans notre application!',
    'login' => 'Se connecter',
];

// Using in code
echo __('messages.welcome');
echo trans('messages.login');

// In Blade templates
{{ __('messages.welcome') }}
```

### 10. Events and Listeners

Events provide a simple observer pattern:

```php
// Define event
// app/Events/UserRegistered.php
class UserRegistered
{
    public $user;
    
    public function __construct($user)
    {
        $this->user = $user;
    }
}

// Define listener
// app/Listeners/SendWelcomeEmail.php
class SendWelcomeEmail
{
    public function handle(UserRegistered $event)
    {
        Mail::to($event->user)->send(new WelcomeMail($event->user));
    }
}

// Register in EventServiceProvider
protected $listen = [
    UserRegistered::class => [
        SendWelcomeEmail::class,
    ],
];
```

## Laravel Versions

Here's a quick overview of Laravel versions:

| Version | Released | PHP Version | Status |
|---------|----------|-------------|--------|
| Laravel 5 | 2015 | ≥5.4 | End of life |
| Laravel 6 | 2019 | ≥7.0 | End of life |
| Laravel 7 | 2020 | ≥7.0 | End of life |
| Laravel 8 | 2020 | ≥7.2 | Security fixes only |
| Laravel 9 | 2022 | ≥8.0 | Security fixes only |
| Laravel 10 | 2023 | ≥8.1 | Active support |
| Laravel 11 | 2024 | ≥8.2 | Latest stable |
| Laravel 12 | 2025 | ≥8.2 | Latest stable |

> **Note**: This book uses Laravel 12 (the latest stable version), which requires PHP 8.2 or higher.

## Setting Expectations

As we build Paperchase together, you'll learn to:

1. **Create a complete Laravel project** from scratch
2. **Design and implement** a database schema
3. **Build authentication** from login to logout
4. **Develop CRUD operations** for exams, subjects, users
5. **Implement search and filtering** functionality
6. **Create RESTful APIs** for mobile access
7. **Handle file uploads** (PDF exams)
8. **Deploy to production**

## Summary

In this chapter, you've learned:

- ✅ What Laravel is and why it's the leading PHP framework
- ✅ The MVC architecture and how Laravel implements it
- ✅ Key Laravel concepts: routing, middleware, Blade templates, Eloquent
- ✅ How to use Artisan CLI
- ✅ Database migrations and seeding
- ✅ Built-in authentication features

### What's Next?

In Chapter 3, we'll set up your development environment and install Laravel, preparing you to build the Paperchase application.

---

## Practice Exercises

1. **Explore Laravel**: Install Laravel locally and run the development server. Navigate to the default welcome page.

2. **Create Routes**: Practice defining different types of routes (GET, POST, PUT, DELETE) in routes/web.php.

3. **Build a Blade Template**: Create a master layout with navigation and multiple pages that extend it.

4. **Database Migration**: Create a migration for a "products" table with various column types.

5. **Eloquent Queries**: Practice CRUD operations using tinker:
   ```bash
   php artisan tinker
   >>> App\Models\User::all()
   >>> User::create(['name' => 'Test', 'email' => 'test@test.com', 'password' => 'password'])
   ```

---

*Continue to Chapter 3: Setting Up Your Development Environment*
