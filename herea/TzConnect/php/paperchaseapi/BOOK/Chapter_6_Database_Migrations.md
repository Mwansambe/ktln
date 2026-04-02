# Chapter 6: Database Migrations and Schema Design

## Introduction

In this chapter, we'll explore database migrations in Laravel and understand how Paperchase's database schema is designed. Migrations are like version control for your database - they allow you to modify your database schema over time in a structured and organized manner.

By the end of this chapter, you will:
- Understand what migrations are and why they're important
- Know how to create and run migrations
- Understand each table in Paperchase's schema
- Learn about database relationships and foreign keys
- Be able to create your own migrations

## What are Migrations?

Migrations are PHP classes that define database table structures. They allow you to:
- Create, modify, and delete database tables
- Define columns, indexes, and relationships
- Track schema changes over time
- Work in teams by sharing schema definitions
- Roll back changes if needed

### Migration File Structure

Each migration file follows a naming convention: `YYYY_MM_DD_HHMMSS_create_table_name_table.php`

```
database/migrations/
├── 2024_01_01_000000_create_users_table.php      # Users table
├── 2024_01_01_000001_create_subjects_table.php   # Subjects table
├── 2024_01_01_000002_create_exams_table.php      # Exams table
└── 2024_01_01_000003_create_bookmarks_table.php  # Bookmarks & Downloads
```

## Understanding the Migration Class

Every migration extends Laravel's `Migration` class and has two main methods:

```php
// database/migrations/xxxx_create_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This method creates/modifies tables.
     */
    public function up(): void
    {
        // Create table logic goes here
    }

    /**
     * Reverse the migrations.
     * 
     * This method rolls back changes.
     */
    public function down(): void
    {
        // Rollback logic goes here
    }
};
```

## Creating the Users Table

The users table is the foundation of our authentication system. Let's examine Paperchase's users migration:

```php
// database/migrations/2024_01_01_000000_create_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary key
            $table->uuid('id')->primary();
            
            // Basic fields
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Role-based access
            $table->enum('role', ['VIEWER', 'EDITOR', 'ADMIN'])->default('VIEWER');
            
            // Profile fields
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login')->nullable();
            
            // Remember me token for "Remember Me" functionality
            $table->rememberToken();
            
            // Timestamps (created_at, updated_at)
            $table->timestamps();
        });

        // Password reset tokens table
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Sessions table for web authentication
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->char('user_id', 36)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
```

### Column Types Explained

Laravel's Blueprint provides many column types:

| Method | Description | Example |
|--------|-------------|---------|
| `id()` | Auto-incrementing big integer | Primary key |
| `uuid('id')` | UUID primary key | `id` column |
| `string('name')` | VARCHAR(255) | String fields |
| `text('bio')` | TEXT | Long text |
| `integer('age')` | INTEGER | Numbers |
| `boolean('active')` | BOOLEAN | True/false |
| `enum('role', [])` | ENUM | Predefined values |
| `timestamp('created_at')` | TIMESTAMP | Date/time |
| `foreignId('user_id')` | Foreign key | Relationships |
| `timestamps()` | created_at & updated_at | Automatic timestamps |

## Creating the Subjects Table

The subjects table stores exam categories:

```php
// database/migrations/2024_01_01_000001_create_subjects_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();                    // Auto-incrementing ID
            
            $table->string('name')->unique();  // Subject name (e.g., Mathematics)
            $table->string('icon')->default('Folder');  // Icon name
            $table->string('color')->default('#3B82F6');  // Primary color
            $table->string('bg_color')->default('#EFF6FF');  // Background color
            $table->string('border_color')->default('#BFDBFE');  // Border color
            $table->text('description')->nullable();  // Optional description
            $table->integer('paper_count')->default(0);  // Counter for exams
            $table->timestamps();  // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
```

## Creating the Exams Table

The exams table is the core of Paperchase, storing all exam paper information:

```php
// database/migrations/2024_01_01_000002_create_exams_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            // Primary key - UUID for distributed systems
            $table->uuid('id')->primary();
            
            // Exam identification
            $table->string('code')->unique();  // e.g., MATH2024P1
            $table->string('title');           // e.g., Mathematics Paper 1 2024
            
            // Foreign key to subjects
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->onDelete('cascade');  // Delete exams if subject deleted
            
            // Exam metadata
            $table->string('year');  // e.g., 2024
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
            
            // PDF file storage
            $table->string('pdf_path')->nullable();
            $table->string('pdf_name')->nullable();
            $table->bigInteger('file_size')->nullable();
            
            // Marking scheme (answer sheet)
            $table->string('marking_scheme_path')->nullable();
            $table->string('marking_scheme_name')->nullable();
            $table->bigInteger('marking_scheme_size')->nullable();
            $table->boolean('has_marking_scheme')->default(false);
            
            // Visual customization
            $table->string('preview_image')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->string('bg_color')->nullable();
            $table->string('border_color')->nullable();
            
            // Status flags
            $table->boolean('is_featured')->default(false);  // Featured on homepage
            $table->boolean('is_new')->default(true);       // New exam badge
            
            // Statistics counters
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('bookmark_count')->default(0);
            
            // Audit fields - who created/updated
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            $table->foreign('updated_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            
            // Timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
```

### Why UUIDs?

You might notice that the exams table uses UUIDs instead of auto-incrementing IDs:

```php
$table->uuid('id')->primary();
```

**Benefits of UUIDs:**
- Can be generated on the client side
- No sequential guessing (security)
- Can merge databases without ID conflicts
- Works well in distributed systems

**Drawbacks:**
- Larger storage size
- Not as readable as 1, 2, 3
- Slightly slower queries

## Creating Bookmarks and Downloads Tables

These tables track user interactions with exams:

```php
// database/migrations/2024_01_01_000003_create_bookmarks_downloads_tables.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bookmarks - Users saving favorite exams
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Foreign keys
            $table->uuid('user_id');
            $table->uuid('exam_id');
            
            // Relationships with cascade delete
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');  // Delete bookmarks if user deleted
            
            $table->foreign('exam_id')
                  ->references('id')
                  ->on('exams')
                  ->onDelete('cascade');  // Delete bookmarks if exam deleted
            
            // Timestamp
            $table->timestamp('created_at')->useCurrent();
            
            // Unique constraint - prevent duplicate bookmarks
            $table->unique(['user_id', 'exam_id']);
        });

        // Downloads - Tracking exam downloads
        Schema::create('downloads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Foreign keys
            $table->uuid('user_id');
            $table->uuid('exam_id');
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->foreign('exam_id')
                  ->references('id')
                  ->on('exams')
                  ->onDelete('cascade');
            
            $table->timestamp('created_at')->useCurrent();
            // Note: Downloads can be repeated, so no unique constraint
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
        Schema::dropIfExists('bookmarks');
    }
};
```

### Understanding Foreign Keys and Relationships

```
┌─────────────────────────────────────────────────────────────────┐
│                    FOREIGN KEY RELATIONSHIPS                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  users ────────────── exams                                      │
│  (id)     created_by ───► (id)                                  │
│                                                                  │
│  ON DELETE: set null  (if user deleted, exam keeps but          │
│                         created_by becomes null)                │
│                                                                  │
│  subjects ─────────── exams                                      │
│  (id)    subject_id ──► (id)                                   │
│                                                                  │
│  ON DELETE: cascade   (if subject deleted, all its exams        │
│                         are also deleted)                       │
│                                                                  │
│  users ─────────────── bookmarks                                │
│  (id)      user_id ─────► (id)                                  │
│                                                                  │
│  ON DELETE: cascade   (if user deleted, their bookmarks         │
│                         are deleted)                            │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

## Running Migrations

Now let's see how to work with migrations in practice:

### Running All Migrations

```bash
# Run all pending migrations
php artisan migrate

# Run migrations for a specific database
php artisan migrate --database=pgsql
```

### Migration Status

```bash
# Check migration status
php artisan migrate:status

# Output:
# +------+------------------------------------------------+-------+
# | Ran? | Migration                                      | Batch |
# +------+------------------------------------------------+-------+
# | Yes  | 2024_01_01_000000_create_users_table         | 1     |
# | Yes  | 2024_01_01_000001_create_subjects_table      | 1     |
# | Yes  | 2024_01_01_000002_create_exams_table         | 1     |
# | Yes  | 2024_01_01_000003_create_bookmarks_table     | 1     |
# +------+------------------------------------------------+-------+
```

### Rolling Back Migrations

```bash
# Rollback last migration
php artisan migrate:rollback

# Rollback with step
php artisan migrate:rollback --step=2

# Rollback all migrations
php artisan migrate:reset
```

### Fresh Installation

```bash
# Drop all tables and re-run all migrations
php artisan migrate:fresh

# With seeding
php artisan migrate:fresh --seed

# With specific seeder
php artisan migrate:fresh --seed --seeder=SubjectSeeder
```

## Creating New Migrations

Let's create a new migration step by step:

### Step 1: Generate the Migration

```bash
php artisan make:migration create_ratings_table
```

This creates a file like `database/migrations/2024_02_01_000000_create_ratings_table.php`

### Step 2: Define the Schema

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('user_id');
            $table->uuid('exam_id');
            $table->tinyInteger('rating');  // 1-5 stars
            $table->text('comment')->nullable();
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('exam_id')
                  ->references('id')
                  ->on('exams')
                  ->onDelete('cascade');
            
            $table->timestamps();
            
            // Prevent duplicate ratings
            $table->unique(['user_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
```

### Step 3: Run the Migration

```bash
php artisan migrate
```

## Modifying Existing Tables

Sometimes you need to modify an existing table. Laravel provides two approaches:

### Approach 1: Create a New Migration

```bash
php artisan make:migration add_status_to_exams_table
```

```php
public function up(): void
{
    Schema::table('exams', function (Blueprint $table) {
        $table->string('status')->default('published')->after('is_new');
        $table->index(['status', 'created_at']);
    });
}

public function down(): void
{
    Schema::table('exams', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
```

### Approach 2: Modify Schema Class

For more complex modifications, you can use the `Schema` facade:

```php
// Check if column exists before adding
if (!Schema::hasColumn('exams', 'status')) {
    Schema::table('exams', function (Blueprint $table) {
        $table->string('status')->default('published');
    });
}
```

## Database Indexes

Indexes improve query performance. Let's add some:

```php
Schema::create('exams', function (Blueprint $table) {
    // ... columns ...
    
    // Indexes for common queries
    $table->index('subject_id');
    $table->index('year');
    $table->index('type');
    $table->index(['subject_id', 'year']);  // Composite index
    $table->index('created_at');
});
```

### Index Types in Laravel

| Method | Creates |
|--------|---------|
| `$table->index('column')` | Regular index |
| `$table->unique('email')` | Unique index |
| `$table->primary('id')` | Primary key |
| `$table->spatialIndex('location')` | Spatial index |

## Seeding the Database

After creating tables, you need to populate them with sample data. Let's look at Paperchase's seeders:

### Creating a Seeder

```bash
php artisan make:seeder SubjectSeeder
```

### Example Seeder

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            [
                'name' => 'Mathematics',
                'icon' => 'Calculator',
                'color' => '#3B82F6',
                'bg_color' => '#EFF6FF',
                'border_color' => '#BFDBFE',
                'description' => 'Mathematics exam papers and revision materials',
                'paper_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Physics',
                'icon' => 'Atom',
                'color' => '#10B981',
                'bg_color' => '#ECFDF5',
                'border_color' => '#A7F3D0',
                'description' => 'Physics exam papers and practical notes',
                'paper_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chemistry',
                icon' => 'Flask',
                'color' => '#8B5CF6',
                'bg_color' => '#F5F3FF',
                'border_color' => '#C4B5FD',
                'description' => 'Chemistry exam papers and experiment guides',
                'paper_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more subjects...
        ];

        DB::table('subjects')->insert($subjects);
    }
}
```

### Running Seeders

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=SubjectSeeder

# Seed with migration
php artisan migrate:fresh --seed
```

## Summary

In this chapter, you have learned:
- ✅ What database migrations are and why they matter
- ✅ The structure of a Laravel migration file
- ✅ How to create each table in Paperchase's schema
- ✅ Understanding foreign keys and relationships
- ✅ Why UUIDs are used for certain tables
- ✅ Running, rolling back, and managing migrations
- ✅ Creating new migrations from scratch
- ✅ Adding indexes for performance
- ✅ Seeding the database with sample data

### What's Next?

In Chapter 7, we'll dive into creating Eloquent Models that interact with our database tables. We'll define relationships, query scopes, and accessor methods.

---

## Practice Exercises

1. **Create a New Table**: Create a migration for a `comments` table that links users to exams with a comment text field.

2. **Add a Column**: Add a migration that adds a `published_at` timestamp to the exams table.

3. **Create a Seeder**: Create a seeder that populates the subjects table with 5 subjects relevant to your region.

4. **Add an Index**: Create a migration that adds an index on the `year` column of the exams table.

5. **Explore Relationships**: Draw an ER diagram showing all the relationships between tables in Paperchase.

6. **Rollback Practice**: Run a migration, then roll it back to understand how it works.

---

*Continue to Chapter 7: Building Models with Eloquent*

