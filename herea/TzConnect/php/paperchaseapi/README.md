# PaperChase - Laravel Monolithic Application

> **Full-Stack Exam Management System with Laravel + Blade Templates + PostgreSQL**

A complete monolithic application combining Laravel backend, Blade template frontend, and PostgreSQL database for comprehensive exam paper management and distribution.

## Table of Contents

- [Overview](#overview)
- [Technology Stack](#technology-stack)
- [Quick Start](#quick-start)
- [Architecture](#architecture)
- [Features](#features)
- [Development](#development)
- [Database](#database)
- [Directory Structure](#directory-structure)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)

---

## Overview

**PaperChase** is a monolithic full-stack application that provides:

- **Web Frontend**: Blade templates with Tailwind CSS styling (embedded in Laravel)
- **RESTful API**: JSON endpoints for mobile/external clients
- **Database**: PostgreSQL with comprehensive schema
- **Authentication**: JWT-based auth + session management
- **Features**:
  - Browse and search exam papers by subject, year, type
  - Download exam papers and marking schemes
  - Bookmark favorite exams
  - Track download and bookmark statistics
  - Admin panel for content management
  - Real-time updates (WebSocket ready)
  - Role-based access control (USER, EDITOR, ADMIN)

### Monolithic vs Separated

```
Traditional Separated Architecture:
[React/Frontend:5173] ←→ [Laravel API:8000] → [PostgreSQL]

PaperChase Monolithic:
[Laravel:8000] 
├── Routes (Web + API)
├── Blade Templates (Frontend)
├── Controllers (Business Logic)
├── Models (Database Layer)
└── PostgreSQL
```

This approach provides:
- Simpler deployment
- Sessions and CSRF protection
- Server-side rendering
- Integrated testing
- Single authentication system

---

## Technology Stack

| Layer | Technology | Version | Purpose |
|-------|-----------|---------|---------|
| **Frontend (Web)** | Blade Templates | Laravel 12 | Server-side rendered HTML |
| **Styling** | Tailwind CSS | 4.0+ | Utility-first CSS framework |
| **Frontend (API)** | JSON API | REST | Mobile/external clients |
| **Backend Framework** | Laravel | 12.x | MVC framework |
| **Language** | PHP | 8.2+ | Server-side language |
| **Database** | PostgreSQL | 15+ | Relational database |
| **Authentication** | JWT + Sessions | 2.1 | Token & session auth |
| **Task Queues** | Redis/Predis | 2.2 | Background jobs |
| **Assets** | Vite | 7.0+ | Asset bundling |
| **Testing** | PHPUnit | 11.5.3 | Unit/Feature tests |

---

## Quick Start

### Prerequisites

- PHP 8.2+
- PostgreSQL 15+
- Composer
- Node.js 18+
- Git

### Installation (5 minutes)

```bash
# 1. Navigate to project
cd php/paperchaseapi

# 2. Copy environment file
cp .env.example .env

# 3. Install dependencies
composer install

# 4. Generate app key
php artisan key:generate

# 5. Configure database in .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=paperchase
# DB_USERNAME=paperchase_user
# DB_PASSWORD=your_password

# 6. Run migrations and seed
php artisan migrate:fresh --seed

# 7. Install frontend dependencies
npm install

# 8. Build frontend assets
npm run build

# 9. Start development server
composer run-script dev
```

### Verify Installation

```bash
# Open browser
http://localhost:8000

# Login credentials (after seeding)
Email: admin@paperchase.local
Password: password

# Check health endpoint
curl http://localhost:8000/api/health
```

---

## Architecture

### Directory Structure

```
php/paperchaseapi/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/              # API Controllers
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── ExamController.php
│   │   │   │   ├── SubjectController.php
│   │   │   │   └── BookmarkController.php
│   │   │   └── Web/              # Web Controllers
│   │   │       ├── HomeController.php
│   │   │       ├── ExamController.php
│   │   │       ├── DashboardController.php
│   │   │       └── ProfileController.php
│   │   ├── Middleware/           # Request middleware
│   │   ├── Requests/             # Form validation
│   │   └── Resources/            # API response formatters
│   ├── Models/                   # Eloquent Models
│   │   ├── User.php
│   │   ├── Exam.php
│   │   ├── Subject.php
│   │   ├── Bookmark.php
│   │   └── Download.php
│   ├── Services/                 # Business logic services
│   ├── Traits/                   # Reusable traits
│   └── Providers/                # Service providers
│
├── resources/
│   ├── views/                    # Blade templates
│   │   ├── layouts/              # Layout templates
│   │   │   ├── app.blade.php     # Main layout (with navbar/footer)
│   │   │   └── auth.blade.php    # Auth pages layout
│   │   ├── components/           # Reusable components
│   │   │   ├── navbar.blade.php
│   │   │   └── footer.blade.php
│   │   ├── pages/                # Public pages
│   │   │   └── home.blade.php
│   │   ├── auth/                 # Auth pages
│   │   │   ├── login.blade.php
│   │   │   └── register.blade.php
│   │   ├── exams/                # Exam pages
│   │   │   ├── index.blade.php   # Browse/search
│   │   │   ├── show.blade.php    # Detail page
│   │   │   └── create.blade.php  # Upload form
│   │   ├── subjects/             # Subject pages
│   │   │   └── index.blade.php
│   │   ├── dashboard/            # User dashboard
│   │   │   └── index.blade.php
│   │   └── admin/                # Admin panel
│   │       ├── dashboard.blade.php
│   │       ├── users.blade.php
│   │       └── exams.blade.php
│   ├── css/
│   │   └── app.css               # Tailwind CSS
│   └── js/
│       └── app.js                # Frontend JS (Vite)
│
├── routes/
│   ├── api.php                   # API routes (/api/*)
│   ├── web.php                   # Web routes (Blade pages)
│   └── console.php               # Artisan commands
│
├── database/
│   ├── migrations/               # Schema migrations
│   ├── factories/                # Model factories
│   ├── seeders/                  # Database seeders
│   └── database.sqlite           # SQLite cache (dev)
│
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── jwt.php                   # JWT configuration
│   └── ...
│
├── tests/
│   ├── Feature/                  # Integration tests
│   ├── Unit/                     # Unit tests
│   └── TestCase.php
│
├── public/
│   ├── index.php                 # Entry point
│   └── storage/                  # Public file uploads
│
├── storage/
│   ├── app/                      # File uploads
│   ├── framework/                # Cache, logs
│   └── logs/                     # Application logs
│
├── bootstrap/
│   └── app.php                   # Bootstrap configuration
│
├── .env.example                  # Environment template
├── composer.json                 # PHP dependencies
├── package.json                  # Node.js dependencies
├── phpunit.xml                   # Test config
├── vite.config.js                # Vite config
├── artisan                       # Laravel CLI
└── README.md                     # This file
```

### Request Flow

#### Web Route (Blade Rendering)

```
User Request (GET /exams)
    ↓
routes/web.php (Route matching)
    ↓
Middleware Stack (Auth, CSRF, etc)
    ↓
ExamController@index (Web Controller)
    ↓
Service Layer (Fetch data)
    ↓
Eloquent Models (Database query)
    ↓
Blade Template Render (exams/index.blade.php)
    ↓
HTML Response (with styling)
```

#### API Route (JSON Response)

```
API Request (GET /api/exams)
    ↓
routes/api.php (Route matching)
    ↓
Middleware Stack (JWT Auth, etc)
    ↓
ExamController@index (API Controller)
    ↓
Service Layer (Fetch data)
    ↓
Eloquent Models (Database query)
    ↓
API Resource (Format JSON)
    ↓
JSON Response
```

---

## Features

### User Features

- **Browse Exams**: Search by subject, year, type, keyword
- **Download**: Get exam papers and marking schemes
- **Bookmarks**: Save favorite exams for quick access
- **Dashboard**: Track downloads, statistics, learning progress
- **Profiles**: Manage account and preferences
- **Real-time**: Live updates on bookmarks and downloads

### Admin/Editor Features

- **Upload Exams**: Add exam papers and marking schemes
- **Manage Content**: Edit exam details, categories
- **User Management**: Monitor users, assign roles
- **Analytics**: View statistics and usage patterns
- **Bulk Operations**: Import/export exam data
- **Content Moderation**: Approve/reject submissions

### Technical Features

- **Authentication**: JWT tokens + session management
- **Authorization**: Role-based access control (USER, EDITOR, ADMIN)
- **Validation**: Server-side form validation
- **Error Handling**: Comprehensive error responses
- **Pagination**: Efficient data loading
- **Search**: Full-text search capabilities
- **Caching**: Redis query caching
- **Queues**: Background job processing
- **Logging**: Detailed application logging
- **Testing**: Unit and feature tests

---

## Development

### Available Commands

```bash
cd php/paperchaseapi

# Start development server (concurrent: server + assets + queue)
composer run-script dev

# Or individually:
composer run-script serve          # Start Laravel (port 8000)
npm run dev                        # Start Vite (assets)
composer run-script queue          # Start queue listener

# Code quality
composer run-script lint           # Check code style with Pint
composer run-script lint:check     # Check without fixing

# Testing
composer run-script test           # Run all tests
composer run-script test:unit      # Unit tests only
composer run-script test:coverage  # With coverage report

# Database
composer run-script migrate        # Run migrations
composer run-script migrate:fresh  # Fresh migration with seeding
composer run-script db:seed        # Run seeders

# Development tools
composer run-script tinker         # Interactive Laravel shell
composer run-script optimize       # Production optimization
composer run-script optimize:clear # Clear caches
```

### Creating New Features

```bash
# Example: Create new feature for statistics

# 1. Create migration
php artisan make:migration create_statistics_table

# 2. Create model
php artisan make:model Statistic -m

# 3. Create controller
php artisan make:controller Api/StatisticController --api

# 4. Create service
php artisan make:class Services/StatisticService

# 5. Create test
php artisan make:test Feature/StatisticTest

# 6. Create routes
# Add to routes/api.php:
# Route::apiResource('statistics', StatisticController::class);

# 7. Run migration
php artisan migrate
```

### Blade Template Development

```php
<!-- Create a new page: resources/views/pages/custom.blade.php -->
@extends('layouts.app')

@section('title', 'Custom Page')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold">Welcome</h1>
        {{-- Content here --}}
    </div>
@endsection

<!-- Add route: routes/web.php -->
Route::get('/custom', fn() => view('pages.custom'));
```

---

## Database

See [DATABASE_SETUP.md](./DATABASE_SETUP.md) for complete database documentation.

### Quick Database Setup

```bash
# Create database and user
sudo -u postgres createdb paperchase
sudo -u postgres createdb super
sudo -u postgres createuser paperchase_user

# Run migrations
php artisan migrate:fresh --seed

# Verify
pg_isready -h 127.0.0.1 -p 5432 -U paperchase_user
```

### Key Tables

| Table | Purpose |
|-------|---------|
| `users` | User accounts (role-based) |
| `subjects` | Exam categories |
| `exams` | Exam papers with metadata |
| `bookmarks` | User bookmarked exams |
| `downloads` | Download history |

---

## Configuration

### Environment Variables

```env
# App
APP_NAME="PaperChase"
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:...
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=paperchase
DB_USERNAME=paperchase_user
DB_PASSWORD=yourpassword

# JWT
JWT_SECRET=your_secret_here
JWT_TTL=60

# Mail (optional)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465

# Redis (optional)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## Deployment

### Production Build

```bash
# 1. Install dependencies
composer install --optimize-autoloader --no-dev

# 2. Build assets
npm run build

# 3. Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Run migrations
php artisan migrate --force

# 5. Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Server Configuration

See nginx configuration in [nginx.conf](../../nginx.conf)

```nginx
server {
    listen 80;
    server_name paperchase.local;
    root /path/to/public;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass php-upstream;
        include fastcgi_params;
    }
}
```

### Deployment Checklist

- [ ] Update `.env` with production values
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY`
- [ ] Configure CloudFlare/SSL
- [ ] Set up PostgreSQL backups
- [ ] Configure email settings
- [ ] Test all endpoints
- [ ] Monitor logs and errors

---

## Troubleshooting

### Common Issues

**Port 8000 already in use**

```bash
# Find and kill process
fuser -k 8000/tcp
# Or use different port
php artisan serve --port=8001
```

**Database connection failed**

```bash
# Check PostgreSQL is running
pg_isready -h 127.0.0.1 -p 5432

# Verify credentials in .env
php artisan db:show
```

**JWT token errors**

```bash
# Generate JWT secret
php artisan jwt:secret --force
```

**Blade template not rendering**

```bash
# Clear view cache
php artisan view:clear

# Check file permissions
chmod -R 755 resources/views
```

**Storage permission denied**

```bash
# Fix permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Blade Template Syntax](https://laravel.com/docs/blade)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [API Development](https://laravel.com/docs/api)
- [Testing](https://laravel.com/docs/testing)
- [Deployment](https://laravel.com/docs/deployment)

---

## Documentation

- **[DOCUMENTATION.md](./DOCUMENTATION.md)** - Full API and system documentation
- **[DATABASE_SETUP.md](./DATABASE_SETUP.md)** - Database setup and configuration
- **[PROJECT_ANALYSIS.md](../PROJECT_ANALYSIS.md)** - System architecture overview
- **[BUSINESS_LOGIC.md](../BUSINESS_LOGIC.md)** - Development workflows
- **[DOCUMENTATION_INDEX.md](../DOCUMENTATION_INDEX.md)** - Complete documentation index

---

## License

Licensed under the MIT License. See [LICENSE](LICENSE.md) for details.

---

## Support

For issues, questions, or contributions, please refer to the documentation or create an issue in the repository.
