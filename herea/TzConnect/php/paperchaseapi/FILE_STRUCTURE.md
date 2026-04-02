# Project File Structure & Organization

This guide explains where everything is located in the project.

---

## Directory Tree

```
php/paperchaseapi/
│
├── 📁 app/                          # Application code
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/          # Controllers for handling requests
│   │   │   ├── 📁 Auth/             # Authentication controllers (Login, Register)
│   │   │   │   └── AuthenticatedSessionController.php
│   │   │   ├── 📁 Web/              # Web page controllers
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ExamController.php
│   │   │   │   ├── SubjectController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── SettingsController.php
│   │   │   └── *.php                # API controllers
│   │   │
│   │   ├── 📁 Requests/             # Form validation classes
│   │   │   └── 📁 Auth/
│   │   │       ├── LoginRequest.php
│   │   │       └── RegisterRequest.php
│   │   │
│   │   └── 📁 Middleware/           # Middleware (request filtering)
│   │       └── Authenticate.php
│   │
│   ├── 📁 Models/                   # Database models
│   │   ├── User.php
│   │   ├── Exam.php
│   │   ├── Subject.php
│   │   ├── Bookmark.php
│   │   └── Download.php
│   │
│   ├── 📁 Policies/                 # Authorization policies
│   │   └── ExamPolicy.php
│   │
│   ├── 📁 Mail/                     # Email classes
│   │   └── ExamUploaded.php
│   │
│   ├── 📁 Events/                   # Event classes
│   │   └── ExamCreated.php
│   │
│   ├── 📁 Listeners/                # Event listeners
│   │   └── LogExamCreated.php
│   │
│   ├── 📁 Jobs/                     # Queued jobs
│   │   └── ProcessExamUpload.php
│   │
│   ├── 📁 Providers/                # Service providers
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php   # Policy/Authorization
│   │   ├── EventServiceProvider.php
│   │   ├── RouteServiceProvider.php  # Route configuration
│   │   └── BroadcastServiceProvider.php
│   │
│   └── 📁 Exceptions/               # Custom exceptions
│       └── Handler.php
│
├── 📁 bootstrap/                    # Bootstrap files (auto-loaded)
│   ├── app.php                      # App initialization
│   └── cache/                       # Cache files (temporary)
│
├── 📁 config/                       # Configuration files
│   ├── app.php                      # App configuration
│   ├── auth.php                     # Authentication config
│   ├── database.php                 # Database config
│   ├── cache.php                    # Cache config
│   ├── logging.php                  # Logging config
│   ├── mail.php                     # Email config
│   ├── queue.php                    # Queue config
│   ├── session.php                  # Session config
│   ├── services.php                 # Third-party services
│   ├── filesystems.php              # File storage config
│   └── jwt.php                      # JWT token config
│
├── 📁 database/
│   ├── 📁 migrations/               # Database schema migrations
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 2024_01_01_000001_create_subjects_table.php
│   │   ├── 2024_01_01_000002_create_exams_table.php
│   │   ├── 2024_01_01_000003_create_bookmarks_table.php
│   │   └── 2024_01_01_000004_create_downloads_table.php
│   │
│   ├── 📁 seeders/                  # Database seeders (test data)
│   │   ├── DatabaseSeeder.php       # Main seeder
│   │   ├── UserSeeder.php
│   │   ├── SubjectSeeder.php
│   │   └── ExamSeeder.php
│   │
│   └── 📁 factories/                # Data factories (for testing)
│       └── UserFactory.php
│
├── 📁 public/                       # Public web root
│   ├── index.php                    # Entry point
│   ├── robots.txt                   # SEO robots rules
│   ├── favicon.ico
│   └── 📁 storage/                  # Public file storage (pdfs, images)
│
├── 📁 resources/
│   ├── 📁 views/                    # Blade templates (HTML)
│   │   ├── 📁 layouts/              # Layout templates
│   │   │   ├── app.blade.php        # Main dashboard layout
│   │   │   ├── auth.blade.php       # Login/register layout
│   │   │   └── guest.blade.php
│   │   │
│   │   ├── 📁 auth/                 # Authentication pages
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   ├── forgot-password.blade.php
│   │   │   └── reset-password.blade.php
│   │   │
│   │   ├── 📁 components/           # Reusable components
│   │   │   ├── navbar.blade.php
│   │   │   ├── sidebar.blade.php
│   │   │   ├── footer.blade.php
│   │   │   ├── alert.blade.php
│   │   │   ├── pagination.blade.php
│   │   │   └── card.blade.php
│   │   │
│   │   ├── 📁 dashboard/            # Dashboard pages
│   │   │   ├── index.blade.php
│   │   │   └── stats.blade.php
│   │   │
│   │   ├── 📁 exams/                # Exam management pages
│   │   │   ├── index.blade.php      # List exams
│   │   │   ├── create.blade.php     # Create exam form
│   │   │   ├── edit.blade.php       # Edit exam form
│   │   │   ├── show.blade.php       # View exam detail
│   │   │   ├── form.blade.php       # Shared form partial
│   │   │   └── search.blade.php
│   │   │
│   │   ├── 📁 subjects/             # Subject/Category pages
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── show.blade.php
│   │   │
│   │   ├── 📁 users/                # User management pages
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   │   │
│   │   └── 📁 emails/               # Email templates
│   │       └── exam-uploaded.blade.php
│   │
│   ├── 📁 css/                      # Stylesheets
│   │   ├── app.css
│   │   └── tailwind.css
│   │
│   └── 📁 js/                       # JavaScript files
│       ├── app.js
│       └── bootstrap.js
│
├── 📁 routes/
│   ├── web.php                      # Web routes (HTML pages)
│   ├── api.php                      # API routes (JSON endpoints)
│   ├── console.php                  # Artisan commands
│   └── channels.php                 # WebSocket channels
│
├── 📁 storage/
│   ├── 📁 app/
│   │   ├── 📁 public/               # Public file storage
│   │   │   └── exams/               # Uploaded PDF files
│   │   └── 📁 private/              # Private files
│   │
│   ├── 📁 framework/                # Framework generated files
│   │   ├── 📁 cache/                # Application cache
│   │   ├── 📁 views/                # Compiled views
│   │   └── 📁 sessions/             # Session files
│   │
│   └── 📁 logs/                     # Application logs
│       └── laravel.log              # Main error log
│
├── 📁 tests/                        # Test files
│   ├── Feature/                     # Feature tests
│   │   └── ExamTest.php
│   ├── Unit/                        # Unit tests
│   │   └── UserTest.php
│   └── TestCase.php                 # Base test class
│
├── 📁 vendor/                       # Third-party packages (auto-generated)
│   ├── composer/
│   ├── laravel/
│   ├── symfony/
│   └── ... (many more)
│
├── 📄 .env                          # Environment configuration
├── 📄 .env.example                  # Example env file
├── 📄 .gitignore                    # Git ignore rules
├── 📄 artisan                       # Artisan CLI tool
├── 📄 composer.json                 # PHP dependencies
├── 📄 composer.lock                 # Locked versions
├── 📄 package.json                  # Node dependencies
├── 📄 package-lock.json             # Locked Node versions
├── 📄 phpunit.xml                   # PHPUnit config
├── 📄 vite.config.js                # Vite bundler config
├── 📄 README.md                     # Project readme
├── 📄 DOCUMENTATION.md              # Project documentation
└── 📄 TODO.md                       # Things to do
```

---

## What Goes Where?

### 📝 Adding a New Feature

| Component | Location | Example |
|-----------|----------|---------|
| **Database** | `database/migrations/` | `2024_02_24_create_ratings_table.php` |
| **Model** | `app/Models/` | `Rating.php` |
| **Controller** | `app/Http/Controllers/Web/` | `RatingController.php` |
| **Web Routes** | `routes/web.php` | `Route::resource('ratings', RatingController::class);` |
| **HTML Template** | `resources/views/ratings/` | `index.blade.php`, `create.blade.php` |
| **Form Validation** | `app/Http/Requests/` | `StoreRatingRequest.php` |
| **API Controller** | `app/Http/Controllers/` | `RatingController.php` |
| **API Routes** | `routes/api.php` | `Route::apiResource('ratings', RatingController::class);` |
| **Tests** | `tests/Feature/` | `RatingTest.php` |
| **Policy** | `app/Policies/` | `RatingPolicy.php` |

---

## Key Files to Understand

### 🌐 Routing

**File:** `routes/web.php`
- Defines all web page routes
- Maps URLs to controllers
- Example: `GET /exams` → `ExamController@index`

**File:** `routes/api.php`
- Defines all API endpoints
- Maps to JSON responses
- Example: `GET /api/exams` → Returns JSON

### 🎮 Controllers

**Location:** `app/Http/Controllers/`

- **Web Controllers:** `Web/` subdirectory
  - Return HTML views
  - Handle form submissions
  - Example: `DashboardController`, `ExamController`

- **API Controllers:** Root directory
  - Return JSON responses
  - Handle API requests
  - Example: `ExamController`, `UserController`

### 🗄️ Models

**Location:** `app/Models/`

- Represent database tables
- Define relationships
- Contain query logic
- Examples: `User.php`, `Exam.php`, `Subject.php`

### 🎨 Views (Templates)

**Location:** `resources/views/`

- HTML templates using Blade syntax
- Show data to users
- Handle user input (forms)

**Structure:**
```
resources/views/
├── layouts/           # Main page layouts
├── auth/              # Login/register pages
├── components/        # Reusable components (navbar, alerts)
├── dashboard/         # Dashboard pages
├── exams/             # Exam page templates
├── subjects/          # Subject page templates
└── users/             # User management pages
```

### 🔐 Authentication & Authorization

**Location:** `app/Policies/`
- Define who can do what
- Example: Only admins can delete users

**Location:** `app/Http/Requests/`
- Validate form input
- Example: `LoginRequest.php` validates login form

### 📦 Configuration

**Location:** `config/`
- `app.php` - App settings
- `auth.php` - Authentication settings
- `database.php` - Database settings
- `mail.php` - Email settings

### 🗄️ Database

**Location:** `database/migrations/`
- Define database schema
- Create tables, add columns
- Run with: `php artisan migrate`

**Location:** `database/seeders/`
- Populate database with test data
- Run with: `php artisan db:seed`

### 📄 Environment Configuration

**File:** `.env`
```
APP_NAME=PaperChaseAPI
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8001

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=paperchase
DB_USERNAME=postgres
DB_PASSWORD=admin@123

MAIL_DRIVER=smtp
MAIL_HOST=...
```

---

## Common File Navigation

### ❓ Where is...?

**Login page?**
- Route: `routes/web.php` → `GET /login`
- Controller: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- View: `resources/views/auth/login.blade.php`

**Dashboard page?**
- Route: `routes/web.php` → `GET /dashboard`
- Controller: `app/Http/Controllers/Web/DashboardController.php`
- View: `resources/views/dashboard/index.blade.php`

**Exams list?**
- Route: `routes/web.php` → `GET /exams`
- Controller: `app/Http/Controllers/Web/ExamController.php`
- View: `resources/views/exams/index.blade.php`
- Model: `app/Models/Exam.php`

**API exams endpoint?**
- Route: `routes/api.php` → `GET /api/exams`
- Controller: `app/Http/Controllers/ExamController.php`
- Model: `app/Models/Exam.php`

**User model?**
- File: `app/Models/User.php`
- Migrations: `database/migrations/` (look for user-related)

**Subject creation?**
- Route: `routes/web.php` → `POST /subjects`
- Controller: `app/Http/Controllers/Web/SubjectController.php@store`
- Model: `app/Models/Subject.php`
- View: `resources/views/subjects/create.blade.php`

---

## File Organization Best Practices

### ✅ DO

```
controllers/
├── Web/                 # Separate web controllers
│   └── ExamController.php
└── ExamController.php   # Separate API controllers

models/
└── Exam.php             # One model per table

views/
├── exams/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
└── subjects/
    └── index.blade.php

requests/
├── StoreExamRequest.php
├── UpdateExamRequest.php
└── StoreSubjectRequest.php
```

### ❌ DON'T

```
controllers/
└── AllController.php         # Don't put everything in one file

views/
├── exam_list.blade.php       # No! Use subdirectories
├── exam_create.blade.php
└── exam_edit.blade.php

requests/
└── Request.php               # Don't use generic names
```

---

## File Access in Code

### In Controllers

```php
// Access models
use App\Models\Exam;
$exam = Exam::find($id);

// Access views
return view('exams.show', ['exam' => $exam]);

// Access config
$appName = config('app.name');
```

### In Views

```blade
<!-- Access routes -->
<a href="{{ route('exams.show', $exam->id) }}">View</a>

<!-- Access auth user -->
{{ Auth::user()->name }}

<!-- Access session -->
{{ session('success') }}

<!-- Include components -->
@include('components.navbar')
```

### In Models

```php
// Access config
$maxSize = config('filesystems.max_upload_size');

// Define relationships
public function subject() {
    return $this->belongsTo(Subject::class);
}
```

---

## Finding Files

### Using Terminal

```bash
# Find files by name
find . -name "ExamController.php"

# Find files by pattern
find . -path "*/Controllers/Web/*" -name "*.php"

# Find files with specific content
grep -r "class ExamController" --include="*.php"

# Search in specific directory
grep -r "bookmarks" app/Models/
```

### Using VS Code

- **Ctrl+P** - Quick file search
- **Ctrl+Shift+F** - Search across files
- **Ctrl+Shift+O** - Show outline (symbols in file)
- **F12** - Go to definition

---

## Import Statements (Namespaces)

### Always add at top of file

```php
<?php

namespace App\Http\Controllers\Web;

// Models
use App\Models\Exam;
use App\Models\Subject;

// Requests
use App\Http\Requests\StoreExamRequest;

// Illuminate (Laravel framework)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController
{
    //...
}
```

### Common Imports

```php
// Models
use App\Models\User;
use App\Models\Exam;

// Controllers
use App\Http\Controllers\Controller;

// Requests
use App\Http\Requests\LoginRequest;

// Facades
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

// HTTP
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

// Views
use Illuminate\View\View;
```

---

## Configuration Files Explained

### `config/app.php`
```php
'name' => 'PaperChaseAPI',        // App name
'env' => env('APP_ENV', 'local'),// Environment
'debug' => env('APP_DEBUG', false), // Debug mode
'url' => env('APP_URL'),          // App URL
'timezone' => 'UTC',              // Timezone
```

### `config/auth.php`
```php
'guards' => [
    'web' => [...],      // Session auth (web)
    'api' => [...],      // Token auth (API)
];
```

### `config/database.php`
```php
'default' => env('DB_CONNECTION', 'sqlite'),
'connections' => [
    'pgsql' => [
        'host' => env('DB_HOST'),
        'database' => env('DB_DATABASE'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
    ]
]
```

---

## Quick File Location Reference

```
New route?              → routes/web.php or routes/api.php
New page?               → resources/views/pagename/
New controller?         → app/Http/Controllers/Web/
New form validation?    → app/Http/Requests/
New database table?     → database/migrations/
New permission check?   → app/Policies/
New email?              → app/Mail/
New command?            → app/Console/Commands/
Fix bug/error?          → Check storage/logs/laravel.log
Can't find file?        → Use Ctrl+P in VSCode
Need example code?      → Check similar existing file
```

---

## Tips

1. **Models** should handle database logic
2. **Controllers** should handle request logic
3. **Views** should only display data
4. **Requests** should validate input
5. **Policies** should check permissions
6. **Migrations** should define schema
7. **Tests** should verify functionality

---

Last Updated: February 24, 2026
