# PaperChase API - Complete Documentation

> **🔗 Related Documentation:**  
> - [README.md](./README.md) - Quick start and setup guide  
> - [DOCUMENTATION_INDEX.md](../DOCUMENTATION_INDEX.md) - Full system documentation index  
> - [PROJECT_ANALYSIS.md](../PROJECT_ANALYSIS.md) - System architecture overview  
> - [DATABASE_SETUP.md](../DATABASE_SETUP.md) - Database configuration and troubleshooting  
> - [BUSINESS_LOGIC.md](../BUSINESS_LOGIC.md) - Development workflows and patterns

---

## Table of Contents
1. [Introduction](#introduction)
2. [Project Overview](#project-overview)
3. [Technology Stack](#technology-stack)
4. [Architecture](#architecture)
5. [Database Schema](#database-schema)
6. [API Endpoints](#api-endpoints)
7. [Authentication](#authentication)
8. [Models & Relationships](#models--relationships)
9. [Controllers](#controllers)
10. [Middleware & Guards](#middleware--guards)
11. [Configuration](#configuration)
12. [Getting Started](#getting-started)
13. [Common Tasks](#common-tasks)

---

## 1. Introduction

Welcome to the PaperChase API documentation. This document provides comprehensive technical information about the backend API built with **Laravel 12** and **PostgreSQL**.

**PaperChase** is a comprehensive exam management system that enables:
- Browsing and searching exam papers by subject, year, and type
- Downloading exam papers and marking schemes
- Bookmarking frequently used exams
- Tracking download and bookmark statistics
- Managing exam categories and papers (for admins/editors)
- User role-based access control (USER, EDITOR, ADMIN)

### Part of Monolithic System

This API is part of the larger PaperChase Admin system. For overall system architecture and workflows, see:
- [PROJECT_ANALYSIS.md](../PROJECT_ANALYSIS.md) - Complete system overview
- [DOCUMENTATION_INDEX.md](../DOCUMENTATION_INDEX.md) - Documentation navigation

---

## 2. Project Overview

**Project Name:** PaperChase API  
**Project Type:** RESTful API Backend  
**Framework:** Laravel 12 (PHP 8.2+)  
**Database:** PostgreSQL  
**Authentication:** JWT (JSON Web Tokens) with Laravel Sanctum  

### Purpose
The PaperChase API provides a comprehensive backend for managing:
- User authentication and authorization
- Subject/category management
- Exam paper management (upload, update, delete)
- Bookmarking system
- Download tracking
- Statistics and analytics

---

## 3. Technology Stack

| Component | Technology | Version |
|-----------|------------|---------|
| Backend Framework | Laravel | 12.x |
| PHP | PHP | 8.2+ |
| Database | PostgreSQL | 15+ |
| Authentication | JWT (php-open-source-saver/jwt-auth) | 2.1 |
| API Tokens | Laravel Sanctum | 4.3 |
| Image Handling | Intervention Image | 2.7 |
| UUID | Ramsey UUID | 4.7 |
| Queue Driver | Predis (Redis) | 2.2 |

---

## 4. Architecture

### Backend Structure

```
php/paperchaseapi/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # RESTful API Controllers
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── SubjectController.php
│   │   │   │   ├── ExamController.php
│   │   │   │   ├── BookmarkController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── StatisticsController.php
│   │   │   └── ...
│   │   ├── Middleware/         # Request/Response Middleware
│   │   ├── Requests/           # Form Validation Requests
│   │   └── Resources/          # API Response Resources
│   ├── Models/                 # Eloquent Models (User, Exam, Subject, etc)
│   ├── Services/               # Business Logic Services
│   ├── Traits/                 # Reusable Code Traits
│   └── Providers/              # Service Providers
├── config/
│   ├── app.php                 # App Configuration
│   ├── auth.php                # Authentication Driver Configuration
│   ├── database.php            # Database Connection Configuration
│   ├── jwt.php                 # JWT Configuration
│   └── ...
├── database/
│   ├── migrations/             # Database Schema Migrations
│   ├── factories/              # Model Factories (Testing)
│   └── seeders/                # Database Seeders
├── routes/
│   ├── api.php                 # RESTful API Routes (v1)
│   └── web.php                 # Web Routes
├── tests/
│   ├── Feature/                # Feature Tests
│   ├── Unit/                   # Unit Tests
│   └── TestCase.php            # Base Test Class
├── storage/
│   ├── app/                    # Uploaded Files
│   ├── framework/              # Framework Cache & Sessions
│   └── logs/                   # Application Logs
├── bootstrap/
│   └── app.php                 # Bootstrap Configuration
├── public/
│   └── index.php               # Application Entry Point
└── composer.json               # PHP Dependencies
```

### Request Flow

```
HTTP Request
    ↓
routes/api.php (Route matching)
    ↓
Middleware Stack (CORS, JWT Auth, etc)
    ↓
Controller (Request handling)
    ↓
Service Layer (Business logic)
    ↓
Eloquent Models (Database queries)
    ↓
Response Resources (Response formatting)
    ↓
HTTP Response (JSON)
```

---

## 5. Database Schema

### 4.1 Users Table
```sql
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('USER', 'EDITOR', 'ADMIN') DEFAULT 'USER',
    avatar VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified_at TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4.2 Subjects Table
```sql
CREATE TABLE subjects (
    id UUID PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    icon VARCHAR(255) NULL,
    color VARCHAR(7) NULL,
    bg_color VARCHAR(7) NULL,
    border_color VARCHAR(7) NULL,
    exam_count INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4.3 Exams Table
```sql
CREATE TABLE exams (
    id UUID PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    subject_id UUID REFERENCES subjects(id) ON DELETE CASCADE,
    year VARCHAR(10) NOT NULL,
    type ENUM('PRACTICE_PAPER', 'MOCK_PAPER', 'PAST_PAPER', 'NECTA_PAPER', 'REVISION_PAPER', 'JOINT_PAPER', 'PRE_NECTA') NOT NULL,
    description TEXT NULL,
    pdf_path VARCHAR(255) NULL,
    pdf_name VARCHAR(255) NULL,
    file_size BIGINT NULL,
    marking_scheme_path VARCHAR(255) NULL,
    marking_scheme_name VARCHAR(255) NULL,
    marking_scheme_size BIGINT NULL,
    has_marking_scheme BOOLEAN DEFAULT FALSE,
    preview_image VARCHAR(255) NULL,
    icon VARCHAR(255) NULL,
    color VARCHAR(7) NULL,
    bg_color VARCHAR(7) NULL,
    border_color VARCHAR(7) NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    is_new BOOLEAN DEFAULT TRUE,
    download_count INTEGER DEFAULT 0,
    view_count INTEGER DEFAULT 0,
    bookmark_count INTEGER DEFAULT 0,
    created_by BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    updated_by BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 4.4 Bookmarks Table
```sql
CREATE TABLE bookmarks (
    id UUID PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    exam_id UUID NOT NULL REFERENCES exams(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, exam_id)
);
```

### 4.5 Downloads Table
```sql
CREATE TABLE downloads (
    id UUID PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    exam_id UUID NOT NULL REFERENCES exams(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 6. API Endpoints

### 6.1 Authentication Routes (Public)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register a new user |
| POST | `/api/auth/login` | Login and get JWT token |

### 6.2 Authentication Routes (Protected)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/refresh` | Refresh JWT token |
| GET | `/api/auth/me` | Get current user info |
| PUT | `/api/auth/change-password` | Change password |
| POST | `/api/auth/logout` | Logout (invalidate token) |
| GET | `/api/auth/verify-email` | Verify email address |
| POST | `/api/auth/resend-verification` | Resend verification email |

### 6.3 User Management Routes
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/users` | List all users (paginated) |
| GET | `/api/users/{id}` | Get user by ID |
| PUT | `/api/users/{id}` | Update user |
| PUT | `/api/users/{id}/role` | Update user role |
| PUT | `/api/users/{id}/toggle-active` | Toggle user active status |
| DELETE | `/api/users/{id}` | Delete user |
| GET | `/api/users/statistics` | Get user statistics |

### 6.4 Subject Routes
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/subjects` | List all subjects |
| GET | `/api/subjects/paginated` | List subjects (paginated) |
| GET | `/api/subjects/popular` | Get popular subjects |
| GET | `/api/subjects/top` | Get top subjects |
| GET | `/api/subjects/with-exams` | Get subjects with exam counts |
| GET | `/api/subjects/empty` | Get subjects with no exams |
| GET | `/api/subjects/search` | Search subjects |
| GET | `/api/subjects/check-name` | Check name availability |
| GET | `/api/subjects/statistics` | Get subject statistics |
| GET | `/api/subjects/{id}` | Get subject by ID |
| GET | `/api/subjects/name/{name}` | Get subject by name |
| POST | `/api/subjects` | Create subject |
| PUT | `/api/subjects/{id}` | Update subject |
| DELETE | `/api/subjects/{id}` | Delete subject |
| POST | `/api/subjects/{id}/recalculate-count` | Recalculate exam count |
| POST | `/api/subjects/recalculate-all-counts` | Recalculate all counts |

### 6.5 Exam Routes
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/exams` | List all exams |
| POST | `/api/exams/search` | Search exams |
| GET | `/api/exams/subject/{subjectId}` | Get exams by subject |
| GET | `/api/exams/year/{year}` | Get exams by year |
| GET | `/api/exams/featured` | Get featured exams |
| GET | `/api/exams/new` | Get new exams |
| GET | `/api/exams/most-downloaded` | Get most downloaded |
| GET | `/api/exams/recent` | Get recent exams |
| GET | `/api/exams/years/distinct` | Get distinct years |
| GET | `/api/exams/subjects/distinct` | Get distinct subjects |
| GET | `/api/exams/statistics` | Get exam statistics |
| GET | `/api/exams/{id}` | Get exam by ID |
| GET | `/api/exams/code/{code}` | Get exam by code |
| GET | `/api/exams/{id}/similar` | Get similar exams |
| POST | `/api/exams` | Create exam |
| PUT | `/api/exams/{id}` | Update exam |
| DELETE | `/api/exams/{id}` | Delete exam |
| POST | `/api/exams/{id}/marking-scheme` | Upload marking scheme |
| POST | `/api/exams/{id}/download` | Record download |

### 6.6 Statistics Routes
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/statistics/dashboard` | Get dashboard statistics |
| GET | `/api/statistics/overview` | Get overview statistics |

---

## 7. Authentication

### 7.1 JWT Authentication
The API uses JWT (JSON Web Tokens) for authentication. All protected routes require a valid JWT token in the Authorization header.

**Header Format:**
```
Authorization: Bearer <your-jwt-token>
```

### 7.2 Token Configuration
JWT tokens are configured in the `.env` file:
```
JWT_SECRET=<generated-secret>
JWT_TTL=60              # Token time to live in minutes
JWT_REFRESH_TTL=20160   # Refresh token time to live in minutes
```

### 7.3 User Roles
| Role | Description | Permissions |
|------|-------------|-------------|
| USER | Regular user | Browse, download, bookmark exams |
| EDITOR | Content editor | All user permissions + create/edit exams |
| ADMIN | System administrator | Full access to all features |

### 7.4 Authentication Flow
1. **Register:** POST to `/api/auth/register` with name, email, password
2. **Login:** POST to `/api/auth/login` with email, password
3. **Receive Token:** Get JWT token in response
4. **Use Token:** Include token in Authorization header for protected routes
5. **Refresh:** POST to `/api/auth/refresh` before token expires
6. **Logout:** POST to `/api/auth/logout` to invalidate token

---

## 8. Models & Relationships

### 8.1 User Model
```php
class User extends Authenticatable implements JWTSubject
{
    // Relationships
    public function exams()           // Exams created by user
    public function bookmarks()       // User's bookmarks
    public function downloads()       // User's downloads
    
    // Methods
    public function isAdmin(): bool
    public function isEditor(): bool
    public function canManageContent(): bool
    public function canManageUsers(): bool
}
```

### 8.2 Subject Model
```php
class Subject extends Model
{
    // Relationships
    public function exams()           // Exams in this subject
    
    // Attributes
    - id (UUID)
    - name
    - slug
    - description
    - icon
    - color, bg_color, border_color
    - exam_count
    - is_active
}
```

### 8.3 Exam Model
```php
class Exam extends Model
{
    // Relationships
    public function subject()         // Parent subject
    public function creator()        // User who created
    public function updater()        // User who last updated
    public function bookmarkedBy()   // Users who bookmarked
    public function downloads()      // Download records
    
    // Methods
    public function hasMarkingScheme(): bool
    public function getFileSizeFormattedAttribute(): ?string
    public function incrementDownloadCount(): void
    public function incrementViewCount(): void
}
```

### 8.4 Bookmark Model
```php
class Bookmark extends Model
{
    // Relationships
    public function user()           // Owner of bookmark
    public function exam()           // Bookmarked exam
}
```

### 8.5 Download Model
```php
class Download extends Model
{
    // Relationships
    public function user()           // User who downloaded
    public function exam()           // Downloaded exam
}
```

### 8.6 Entity Relationship Diagram
```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│    Users    │       │  Subjects   │       │    Exams    │
├─────────────┤       ├─────────────┤       ├─────────────┤
│ id (PK)     │       │ id (PK)     │       │ id (PK)     │
│ name        │       │ name        │◄──────│ subject_id  │
│ email       │       │ slug        │       │ (FK)        │
│ password    │       │ exam_count  │       │ title       │
│ role        │       └─────────────┘       │ code        │
│ avatar      │                              │ year        │
│ is_active   │       ┌─────────────┐       │ type        │
└─────────────┘       │  Bookmarks  │       │ created_by  │
       │              ├─────────────┤       │ (FK)        │
       │              │ id (PK)     │       └─────────────┘
       │              │ user_id (FK)│              │
       │              │ exam_id (FK)│              │
       │              └─────────────┘       ┌─────────────┐
       │                                    │  Downloads  │
       └───────────────────────────────────│├─────────────┤
              ┌─────────────┐               │ id (PK)     │
              │             │               │ user_id (FK)│
              │             │               │ exam_id (FK)│
              │             │               └─────────────┘
              │             │
              └─────────────┘
```

---

## 9. Controllers

### 8.1 AuthController
Handles all authentication-related operations:
- `register()` - Register new user
- `login()` - User login
- `logout()` - User logout
- `refresh()` - Refresh JWT token
- `me()` - Get current user
- `changePassword()` - Change password
- `verifyEmail()` - Verify email
- `resendVerification()` - Resend verification email

### 8.2 UserController
Manages user operations:
- `index()` - List all users (paginated)
- `show()` - Get user by ID
- `update()` - Update user
- `updateRole()` - Update user role
- `toggleActive()` - Toggle user active status
- `destroy()` - Delete user
- `statistics()` - Get user statistics

### 8.3 SubjectController
Manages subjects/categories:
- `index()` - List all subjects
- `paginated()` - Paginated list
- `popular()` - Popular subjects
- `top()` - Top subjects
- `withExams()` - Subjects with exam counts
- `empty()` - Subjects with no exams
- `search()` - Search subjects
- `checkName()` - Check name availability
- `statistics()` - Get statistics
- `show()` - Get subject by ID
- `showByName()` - Get by name
- `store()` - Create subject
- `update()` - Update subject
- `destroy()` - Delete subject
- `recalculateCount()` - Recalculate exam count
- `recalculateAllCounts()` - Recalculate all counts

### 8.4 ExamController
Manages exam papers:
- `index()` - List all exams
- `search()` - Search exams
- `bySubject()` - Get by subject
- `byYear()` - Get by year
- `featured()` - Featured exams
- `newExams()` - New exams
- `mostDownloaded()` - Most downloaded
- `recent()` - Recent exams
- `distinctYears()` - Get distinct years
- `distinctSubjects()` - Get distinct subjects
- `statistics()` - Get statistics
- `show()` - Get by ID
- `showByCode()` - Get by code
- `similar()` - Similar exams
- `store()` - Create exam
- `update()` - Update exam
- `destroy()` - Delete exam
- `uploadMarkingScheme()` - Upload marking scheme
- `recordDownload()` - Record download

### 8.5 StatisticsController
Provides analytics:
- `dashboard()` - Dashboard statistics
- `overview()` - Overview statistics

---

## 10. Middleware & Guards

### 9.1 API Guard
```php
// config/auth.php
'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

### 9.2 Middleware
- `auth:api` - Protects routes requiring authentication
- `throttle` - Rate limiting (60 requests per minute)

---

## 11. Configuration

### 10.1 Environment Variables
```env
# Application
APP_NAME=PaperChaseAPI
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=paperchase
DB_USERNAME=postgres
DB_PASSWORD=admin@123

# JWT
JWT_SECRET=...
JWT_TTL=60
JWT_REFRESH_TTL=20160

# Redis (optional)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

### 10.2 CORS Configuration
CORS is configured in `config/cors.php` for frontend access.

---

## 12. Getting Started

### 11.1 Prerequisites
- PHP 8.2+
- Composer
- PostgreSQL 15+
- Node.js (for frontend assets)

### 11.2 Installation Steps
```bash
# 1. Navigate to project directory
cd php/paperchaseapi

# 2. Install dependencies
composer install

# 3. Create environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Generate JWT secret
php artisan jwt:secret

# 6. Run migrations
php artisan migrate

# 7. Start the development server
php artisan serve
```

### 11.3 Running the Application
```bash
# Development server
php artisan serve

# Queue worker (for background jobs)
php artisan queue:work

# Schedule runner
php artisan schedule:work
```

---

## 13. Common Tasks

### 12.1 Creating a New User
```bash
php artisan tinker
>>> App\Models\User::create([
>>>     'name' => 'John Doe',
>>>     'email' => 'john@example.com',
>>>     'password' => bcrypt('password'),
>>>     'role' => 'ADMIN'
>>> ]);
```

### 12.2 Creating a Subject
```php
$subject = \App\Models\Subject::create([
    'name' => 'Mathematics',
    'slug' => 'mathematics',
    'description' => 'Math exam papers',
    'color' => '#FF5733',
]);
```

### 12.3 Creating an Exam
```php
$exam = \App\Models\Exam::create([
    'code' => 'MATH2024P1',
    'title' => 'Mathematics Paper 1 2024',
    'subject_id' => $subject->id,
    'year' => '2024',
    'type' => 'PAST_PAPER',
    'created_by' => auth()->id(),
]);
```

### 12.4 Recording a Download
```php
$download = \App\Models\Download::create([
    'user_id' => auth()->id(),
    'exam_id' => $exam->id,
]);

$exam->incrementDownloadCount();
```

### 12.5 Creating a Bookmark
```php
$bookmark = \App\Models\Bookmark::create([
    'user_id' => auth()->id(),
    'exam_id' => $exam->id,
]);

$exam->incrementBookmarkCount();
```

### 12.6 Testing the API
```bash
# Login and get token
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'

# Use token to access protected route
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer <your-token>"
```

### 12.7 Database Commands
```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset all migrations
php artisan migrate:reset

# Fresh install (drop all and recreate)
php artisan migrate:fresh

# Seed database
php artisan db:seed
```

---

## Appendix A: API Response Format

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {
        // Response data
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field": ["Error message"]
    }
}
```

### Paginated Response
```json
{
    "success": true,
    "data": [...],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

---

## Appendix B: Exam Types
| Type | Description |
|------|-------------|
| PRACTICE_PAPER | Practice questions |
| MOCK_PAPER | Mock exam papers |
| PAST_PAPER | Past exam papers |
| NECTA_PAPER | NECTA official papers |
| REVISION_PAPER | Revision papers |
| JOINT_PAPER | Joint examination papers |
| PRE_NECTA | Pre-NECTA papers |

---

## Appendix C: User Roles
| Role | Description |
|------|-------------|
| USER | Default user role |
| EDITOR | Can manage content |
| ADMIN | Full administrative access |

---

## Appendix D: Troubleshooting

### Common Issues

**1. JWT Token Issues**
- Ensure JWT_SECRET is set in .env
- Regenerate secret: `php artisan jwt:secret --force`

**2. Database Connection**
- Verify PostgreSQL is running
- Check credentials in .env
- Run: `php artisan config:clear`

**3. Migration Issues**
- Check database exists
- Run: `php artisan migrate:fresh`

**4. Permission Issues**
- Ensure storage directory is writable
- Run: `chmod -R 775 storage bootstrap/cache`

---

## Appendix E: File Structure
```
paperchaseapi/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── ExamController.php
│   │   │   ├── StatisticsController.php
│   │   │   ├── SubjectController.php
│   │   │   └── UserController.php
│   │   └── Traits/
│   │       └── ApiResponseTrait.php
│   ├── Models/
│   │   ├── Bookmark.php
│   │   ├── Download.php
│   │   ├── Exam.php
│   │   ├── Subject.php
│   │   └── User.php
│   └── Providers/
│       └── AppServiceProvider.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   └── jwt.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2024_01_01_000001_create_subjects_table.php
│   │   ├── 2024_01_01_000002_create_exams_table.php
│   │   └── 2024_01_01_000003_create_bookmarks_downloads_tables.php
│   └── seeders/
├── routes/
│   ├── api.php
│   ├── console.php
│   └── web.php
├── .env
├── artisan
└── composer.json
```

---

*Document Version: 1.0*  
*Last Updated: 2024*  
*For PaperChase API - Laravel 12 + PostgreSQL*

