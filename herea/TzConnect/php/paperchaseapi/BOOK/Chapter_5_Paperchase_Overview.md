# Chapter 5: Paperchase Project Overview

## Introduction

Now that we understand Laravel's architecture, let's explore the Paperchase project itself. In this chapter, we'll understand what Paperchase does, who uses it, and how the application is structured to meet their needs.

By the end of this chapter, you will:
- Understand the purpose and scope of Paperchase
- Know the core features and functionality
- Understand user roles and permissions
- Be familiar with the database schema
- Know how to navigate the codebase

## What is Paperchase?

Paperchase is a comprehensive **Exam Management System** built with Laravel and PostgreSQL. It's designed to help students, teachers, and educational institutions manage, distribute, and access exam papers efficiently.

### Problem Statement

In many educational institutions, especially in Africa, accessing past exam papers and revision materials can be challenging:
- Papers are scattered across different locations
- No centralized repository exists
- Difficulty in finding specific papers by subject or year
- Limited access to marking schemes

### Paperchase Solution

Paperchase solves these problems by providing:
- A centralized digital repository for all exam papers
- Easy search and filtering by subject, year, and type
- Secure download system with tracking
- Bookmarking for favorite papers
- Role-based access for different user types

## Core Features

### For Students/Users

```
┌─────────────────────────────────────────────────────────────────┐
│                      USER FEATURES                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  📚 Browse Exams     ───  Search by subject, year, type        │
│                                                                 │
│  ⬇️  Download       ───  Get PDF papers & marking schemes     │
│                                                                 │
│  🔖 Bookmark        ───  Save favorites for quick access      │
│                                                                 │
│  📊 Dashboard       ───  Track downloads & statistics         │
│                                                                 │
│  👤 Profile          ───  Manage account & preferences        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### For Editors/Admins

```
┌─────────────────────────────────────────────────────────────────┐
│                    ADMIN/EDITOR FEATURES                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ➕ Upload Exams    ───  Add papers with PDFs & schemes        │
│                                                                 │
│  ✏️  Manage Content ───  Edit, update, delete exam papers     │
│                                                                 │
│  👥 User Management ───  View users, assign roles            │
│                                                                 │
│  📈 Analytics       ───  View statistics & usage patterns     │
│                                                                 │
│  🗂️  Categories     ───  Manage subjects & classifications    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## User Roles and Permissions

Paperchase implements a role-based access control (RBAC) system with three main roles:

### Role Hierarchy

```
                    ┌─────────────┐
                    │    ADMIN   │
                    │  (Full     │
                    │   Access)  │
                    └─────┬───────┘
                          │
          ┌───────────────┼───────────────┐
          ▼               ▼               ▼
    ┌───────────┐   ┌───────────┐   ┌───────────┐
    │  EDITOR  │   │   USER   │   │  VIEWER   │
    │(Content  │   │(Download  │   │(Limited   │
    │ Manager) │   │  Access)  │   │  Access)  │
    └───────────┘   └───────────┘   └───────────┘
```

### Role Permissions Matrix

| Feature | USER | EDITOR | ADMIN |
|---------|:----:|:------:|:-----:|
| Browse exams | ✓ | ✓ | ✓ |
| Search exams | ✓ | ✓ | ✓ |
| Download papers | ✓ | ✓ | ✓ |
| Bookmark exams | ✓ | ✓ | ✓ |
| View dashboard | ✓ | ✓ | ✓ |
| Create exams | ✗ | ✓ | ✓ |
| Edit exams | ✗ | ✓ | ✓ |
| Delete exams | ✗ | ✓ | ✓ |
| Manage subjects | ✗ | ✓ | ✓ |
| View users | ✗ | ✓ | ✓ |
| Edit users | ✗ | ✗ | ✓ |
| Delete users | ✗ | ✗ | ✓ |
| System settings | ✗ | ✗ | ✓ |

### Examining Role Implementation

Let's look at how roles are implemented in the User model:

```php
// app/Models/User.php

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
```

## Database Schema Overview

Paper fivechase uses main database tables:

### Entity Relationship Diagram

```
┌──────────────────────────────────────────────────────────────────────────┐
│                        PAPERCHASE DATABASE SCHEMA                        │
└──────────────────────────────────────────────────────────────────────────┘

    ┌─────────────────┐
    │     USERS       │
    ├─────────────────┤
    │ id (PK)         │
    │ name            │
    │ email (unique)  │
    │ password        │
    │ role            │◄──────────────┐
    │ avatar          │               │
    │ is_active       │               │
    │ last_login      │               │
    │ timestamps      │               │
    └────────┬────────┘               │
             │                        │
             │ created_by             │
             │ updated_by             │
             │                        │
             ▼                        │
    ┌─────────────────┐       ┌────────┴────────┐
    │     EXAMS       │       │    SUBJECTS     │
    ├─────────────────┤       ├─────────────────┤
    │ id (PK) [UUID] │       │ id (PK) [UUID]  │
    │ code (unique)  │       │ name (unique)   │
    │ title          │       │ slug (unique)   │
    │ subject_id (FK)│───────│ description     │
    │ year           │       │ icon            │
    │ type           │       │ color           │
    │ description    │       │ exam_count      │
    │ pdf_path       │       │ is_active       │
    │ file_size      │       │ timestamps      │
    │ has_marking_scheme│   └─────────────────┘
    │ is_featured    │
    │ is_new        │
    │ download_count│
    │ view_count    │
    │ timestamps    │
    └────────┬──────┘
             │
             │
    ┌────────┴────────┐      ┌─────────────────┐
    │    BOOKMARKS    │      │    DOWNLOADS    │
    ├─────────────────┤      ├─────────────────┤
    │ id (PK) [UUID] │      │ id (PK) [UUID]  │
    │ user_id (FK)   │      │ user_id (FK)    │
    │ exam_id (FK)   │      │ exam_id (FK)    │
    │ timestamps     │      │ timestamps      │
    └─────────────────┘      └─────────────────┘
```

### Table Details

#### Users Table
```php
// database/migrations/xxxx_xx_xx_create_users_table.php
Schema::create('users', function (Blueprint $table) {
    $table->id();                    // Auto-incrementing ID
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->enum('role', ['USER', 'EDITOR', 'ADMIN'])->default('USER');
    $table->string('avatar')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamp('email_verified_at')->nullable();
    $table->timestamp('last_login')->nullable();
    $table->timestamps();
});
```

#### Subjects Table
```php
// database/migrations/xxxx_xx_xx_create_subjects_table.php
Schema::create('subjects', function (Blueprint $table) {
    $table->uuid('id')->primary();  // UUID primary key
    $table->string('name')->unique();
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('icon')->nullable();
    $table->string('color')->nullable();
    $table->string('bg_color')->nullable();
    $table->string('border_color')->nullable();
    $table->integer('exam_count')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

#### Exams Table
```php
// database/migrations/xxxx_xx_xx_create_exams_table.php
Schema::create('exams', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('code')->unique();
    $table->string('title');
    $table->foreignUuid('subject_id')->constrained()->onDelete('cascade');
    $table->string('year');
    $table->enum('type', [
        'PRACTICE_PAPER',
        'MOCK_PAPER', 
        'PAST_PAPER',
        'NECTA_PAPER',
        'REVISION_PAPER',
        'JOINT_PAPER',
        'PRE_NECTA'
    ]);
    $table->text('description')->nullable();
    $table->string('pdf_path')->nullable();
    $table->string('pdf_name')->nullable();
    $table->bigInteger('file_size')->nullable();
    $table->string('marking_scheme_path')->nullable();
    $table->string('marking_scheme_name')->nullable();
    $table->boolean('has_marking_scheme')->default(false);
    $table->boolean('is_featured')->default(false);
    $table->boolean('is_new')->default(true);
    $table->integer('download_count')->default(0);
    $table->integer('view_count')->default(0);
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
});
```

#### Bookmarks Table
```php
// database/migrations/xxxx_xx_xx_create_bookmarks_table.php
Schema::create('bookmarks', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignUuid('exam_id')->constrained()->onDelete('cascade');
    $table->timestamps();
    
    // Prevent duplicate bookmarks
    $table->unique(['user_id', 'exam_id']);
});
```

#### Downloads Table
```php
// database/migrations/xxxx_xx_xx_create_downloads_table.php
Schema::create('downloads', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignUuid('exam_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

## Exam Types

Paperchase supports various types of exam papers:

| Type | Description | Example |
|------|-------------|---------|
| `PAST_PAPER` | Previous years' exam papers | 2023 Mathematics Paper 1 |
| `MOCK_PAPER` | Practice mock exams | Mock National Exam 2024 |
| `PRACTICE_PAPER` | Practice questions | Weekly practice set |
| `NECTA_PAPER` | Official NECTA papers | CSEE 2022 |
| `REVISION_PAPER` | Revision materials | Form 4 Revision Set |
| `JOINT_PAPER` | Joint examination papers | Regional Joint Exam 2023 |
| `PRE_NECTA` | Pre-NECTA preparatory | Pre-NECTA Mock 2024 |

## Application Flow

### User Registration Flow

```
1. User visits /register
        ↓
2. User fills registration form
   (name, email, password)
        ↓
3. Form submitted via POST
        ↓
4. Validation (email unique, password strong)
        ↓
5. User created in database
   (role: USER by default)
        ↓
6. Auto-login and redirect to dashboard
```

### Exam Download Flow

```
1. User browses/searches exams
        ↓
2. User clicks on exam
        ↓
3. Exam detail page shows
   (increments view_count)
        ↓
4. User clicks "Download"
        ↓
5. System checks authentication
        ↓
6. Download recorded in downloads table
   (increments download_count)
        ↓
7. PDF file served to user
```

### Bookmark Flow

```
1. User clicks bookmark icon on exam
        ↓
2. System checks if already bookmarked
        ↓
3. If not, create bookmark record
   (increment bookmark_count)
        ↓
4. If yes, remove bookmark
   (decrement bookmark_count)
        ↓
5. Update UI to reflect change
```

## Navigating the Codebase

Let's understand where different components are located:

### Directory Structure for Paperchase

```
php/paperchaseapi/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Web/                    # Web page controllers
│   │   │   │   ├── ExamController.php  # Exam pages
│   │   │   │   ├── SubjectController.php
│   │   │   │   └── DashboardController.php
│   │   │   └── Auth/
│   │   │       └── AuthenticatedSessionController.php
│   │   ├── Requests/                   # Form validation
│   │   └── Middleware/                 # Request filtering
│   ├── Models/
│   │   ├── User.php                    # User model
│   │   ├── Exam.php                    # Exam model
│   │   ├── Subject.php                 # Subject model
│   │   ├── Bookmark.php                # Bookmark model
│   │   └── Download.php                # Download model
│   └── Providers/
├── database/
│   ├── migrations/                      # Database schema
│   └── seeders/                         # Sample data
├── resources/
│   └── views/
│       ├── layouts/                     # Master layouts
│       ├── exams/                       # Exam pages
│       ├── subjects/                    # Subject pages
│       ├── auth/                        # Login/Register
│       └── dashboard/                   # Dashboard pages
├── routes/
│   ├── web.php                         # Web routes
│   └── api.php                         # API routes
└── config/
    └── (configuration files)
```

### Key Files Location

| Feature | Route | Controller | View | Model |
|---------|-------|------------|------|-------|
| Login | GET /login | AuthController | auth/login | User |
| Register | GET /register | AuthController | auth/register | User |
| Dashboard | GET /dashboard | DashboardController | dashboard/index | - |
| Exams List | GET /exams | ExamController | exams/index | Exam |
| Exam Detail | GET /exams/{exam} | ExamController | exams/show | Exam |
| Create Exam | GET /exams/create | ExamController | exams/create | Exam |
| Subjects | GET /subjects | SubjectController | subjects/index | Subject |

## Understanding Configuration

### Environment Variables (.env)

Key configuration in `.env`:

```env
# Application
APP_NAME=PaperChase
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=paperchase
DB_USERNAME=paperchase_user
DB_PASSWORD=your_password

# Authentication
JWT_SECRET=your_jwt_secret
JWT_TTL=60
```

### Authentication Configuration

```php
// config/auth.php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

## Summary

In this chapter, you have learned:
- ✅ What Paperchase is and its purpose
- ✅ Core features for users and administrators
- ✅ Role-based access control system
- ✅ Database schema and table relationships
- ✅ Exam types supported
- ✅ Application flow for key features
- ✅ How to navigate the codebase

### What's Next?

In Chapter 6, we'll dive into creating the database migrations and setting up the database schema for Paperchase. We'll create each table step by step.

---

## Practice Exercises

1. **Explore the Database**: Connect to the PostgreSQL database and examine each table structure.

2. **List All Routes**: Run `php artisan route:list` to see all available routes in Paperchase.

3. **Check User Roles**: Look at the users table to see what roles exist and count users in each role.

4. **Examine Exam Types**: Query the exams table to see examples of each exam type.

5. **Trace a Flow**: Follow the code flow from a route to controller to view for the exams list page.

---

*Continue to Chapter 6: Database Migrations and Schema Design*

