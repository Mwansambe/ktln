# Laravel + PostgreSQL - Database Setup Guide

> **Part of PaperChase Admin Monolithic Application**  
> Backend: Laravel 12 with Blade Templates  
> Database: PostgreSQL 15+  

---

## Table of Contents

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Database Creation](#database-creation)
4. [Environment Configuration](#environment-configuration)
5. [Migrations & Seeding](#migrations--seeding)
6. [Database Connection Verification](#database-connection-verification)
7. [Backup & Restore](#backup--restore)
8. [Troubleshooting](#troubleshooting)
9. [Performance Optimization](#performance-optimization)

---

## Overview

The PaperChase Laravel application uses **PostgreSQL** as its relational database. This guide covers:

- PostgreSQL setup and configuration
- Database creation and user management
- Schema migrations (Users, Subjects, Exams, Bookmarks, Downloads)
- Data seeding for development
- Connection verification
- Backup and restoration procedures

### Database Schema Overview

```
Users Table
├── Stores user accounts (roles: USER, EDITOR, ADMIN)
├── Relationships: exams (created_by), bookmarks, downloads
└── Features: JWT auth, password hashing, timestamps

Subjects Table
├── Exam categories (Math, Biology, Chemistry, etc.)
├── Relationships: exams (one-to-many)
└── Features: UUID primary key, slug generation, color coding

Exams Table
├── Individual exam papers with metadata
├── Relationships: subject, user (created_by), bookmarks, downloads
└── Features: UUID primary key, file tracking, statistics

Bookmarks Table
├── User favorite exams
├── Relationships: user, exam
└── Features: unique user-exam combination

Downloads Table
├── Track exam downloads
├── Relationships: user, exam
└── Features: timestamp recording, analytics
```

---

## Prerequisites

### System Requirements

- **PostgreSQL**: 15 or higher
- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Linux/macOS/WSL2**: For development

### Installation

#### Linux (Ubuntu/Debian)

```bash
# Update package manager
sudo apt update

# Install PostgreSQL
sudo apt install postgresql postgresql-contrib

# Verify installation
psql --version
```

#### macOS (Homebrew)

```bash
# Install PostgreSQL
brew install postgresql@15

# Start PostgreSQL service
brew services start postgresql@15

# Verify installation
psql --version
```

#### Windows

- Download from [postgresql.org](https://www.postgresql.org/download/windows/)
- Or use WSL2 with Ubuntu/Debian installation

---

## Database Creation

### Step 1: Start PostgreSQL

```bash
# Linux (if using service)
sudo systemctl start postgresql
sudo systemctl enable postgresql

# macOS
brew services start postgresql@15

# Verify it's running
pg_isready -h 127.0.0.1 -p 5432
```

### Step 2: Connect as PostgreSQL Superuser

```bash
# Connect to PostgreSQL
sudo -u postgres psql

# Or with password
psql -h 127.0.0.1 -U postgres
```

### Step 3: Create Database and User

```sql
-- Create application user
CREATE USER paperchase_user WITH PASSWORD 'secure_password_here';

-- Create database owned by user
CREATE DATABASE paperchase OWNER paperchase_user;

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE paperchase TO paperchase_user;

-- Connect to the database
\c paperchase

-- Grant schema privileges
GRANT ALL PRIVILEGES ON SCHEMA public TO paperchase_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON TABLES TO paperchase_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON SEQUENCES TO paperchase_user;

-- Exit
\q
```

### Step 4: Verify Connection

```bash
# Test connection with new user
psql -h 127.0.0.1 -U paperchase_user -d paperchase

# Inside psql
\dt                 # List tables
\l                  # List databases
\q                  # Exit
```

---

## Environment Configuration

### Step 1: Configure .env

Update `.env` file in `php/paperchaseapi/`:

```env
# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=paperchase
DB_USERNAME=paperchase_user
DB_PASSWORD=secure_password_here

# Alternative (using Postgres socket)
# DB_CONNECTION=pgsql
# DB_HOST=/var/run/postgresql
# DB_DATABASE=paperchase
# DB_USERNAME=paperchase_user

# Application Settings
APP_DEBUG=true          # Set to false in production
APP_ENV=local           # local, staging, production

# JWT Configuration (if using JWT auth)
JWT_SECRET=your_jwt_secret_key_here
JWT_TTL=60              # Token TTL in minutes
JWT_REFRESH_TTL=20160   # Refresh token TTL
```

### Step 2: Verify .env

```bash
cd php/paperchaseapi

# Copy example if not exists
cp .env.example .env

# Update with PostgreSQL credentials
nano .env
```

---

## Migrations & Seeding

### Step 1: Run Migrations

```bash
cd php/paperchaseapi

# Generate app key (if not done)
php artisan key:generate

# Run migrations
php artisan migrate

# Fresh migration (drops all tables, starts fresh)
php artisan migrate:fresh

# With seeding
php artisan migrate:fresh --seed

# Verify migrations
php artisan migrate:status
```

### Step 2: Seed Sample Data (Optional)

```bash
# Run seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=SubjectSeeder

# Create sample data
php artisan db:seed --class=ExamSeeder
```

### Step 3: Create Admin User (Required)

```bash
# Using tinker CLI
php artisan tinker

# Inside tinker
>>> $user = new App\Models\User();
>>> $user->name = 'Admin User';
>>> $user->email = 'admin@paperchase.local';
>>> $user->password = bcrypt('password');
>>> $user->role = 'ADMIN';
>>> $user->save();
>>> exit
```

Or create a custom command:

```bash
php artisan make:command CreateAdminUser
```

---

## Database Connection Verification

### Test Connection via Laravel

```bash
cd php/paperchaseapi

# Check database connection
php artisan db:show

# Run a test query
php artisan tinker
>>> DB::table('users')->count()
>>> User::all()
>>> exit
```

### Test Connection via psql

```bash
# Connect directly
psql -h 127.0.0.1 -U paperchase_user -d paperchase

# Inside psql
\dt                     # List tables
SELECT * FROM users;    # Test query
\q                      # Exit
```

### Test Laravel Connection

```bash
# Artisan command
php artisan test:database

# Or check in your application
```

---

## Backup & Restore

### Backup Database

```bash
# Backup to SQL file
pg_dump -h 127.0.0.1 -U paperchase_user paperchase > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup with compression
pg_dump -h 127.0.0.1 -U paperchase_user -Fc paperchase > paperchase_$(date +%Y%m%d).dump

# Backup with verbose output
pg_dump -h 127.0.0.1 -U paperchase_user -v paperchase > backup.sql
```

### Restore Database

```bash
# From SQL backup
psql -h 127.0.0.1 -U paperchase_user paperchase < backup.sql

# From compressed backup
pg_restore -h 127.0.0.1 -U paperchase_user -d paperchase backup.dump

# Restore to fresh database
dropdb -h 127.0.0.1 -U postgres -i paperchase
createdb -h 127.0.0.1 -U postgres -O paperchase_user paperchase
pg_restore -h 127.0.0.1 -U paperchase_user -d paperchase backup.dump
```

### Automated Backups

Create a backup script (`backup.sh`):

```bash
#!/bin/bash
BACKUP_DIR="/backups/postgresql"
DB_NAME="paperchase"
DB_USER="paperchase_user"
DB_HOST="127.0.0.1"

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/paperchase_$DATE.dump"

# Create backup directory if not exists
mkdir -p $BACKUP_DIR

# Create backup
pg_dump -h $DB_HOST -U $DB_USER -Fc $DB_NAME > $BACKUP_FILE

# Compress backup
gzip $BACKUP_FILE

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.dump.gz" -mtime +7 -delete

echo "Backup completed: $BACKUP_FILE.gz"
```

Enable automatic daily backups:

```bash
# Add to crontab
crontab -e

# Add this line (3 AM daily)
0 3 * * * /path/to/backup.sh
```

---

## Troubleshooting

### Common Issues & Solutions

#### 1. Connection Refused

**Error**: `could not connect to server: Connection refused`

**Solutions**:

```bash
# Check if PostgreSQL is running
pg_isready -h 127.0.0.1 -p 5432

# Start PostgreSQL
sudo systemctl start postgresql           # Linux
brew services start postgresql@15        # macOS

# Check port (should be 5432)
netstat -tulpn | grep postgres          # Linux
lsof -i :5432                           # macOS
```

#### 2. Authentication Failed

**Error**: `FATAL: Ident authentication failed`

**Solution**: Update `pg_hba.conf`:

```bash
# Find pg_hba.conf location
sudo -u postgres psql -c "SHOW hba_file"

# Edit file
sudo nano /etc/postgresql/15/main/pg_hba.conf

# Change "ident" to "md5" or "scram-sha-256"
# local   all             all                                     md5
# host    all             all             127.0.0.1/32            md5

# Restart PostgreSQL
sudo systemctl restart postgresql
```

#### 3. Database Does Not Exist

**Error**: `database "paperchase" does not exist`

**Solution**:

```bash
# Create database
sudo -u postgres createdb paperchase -O paperchase_user

# Verify
sudo -u postgres psql -l | grep paperchase
```

#### 4. Permission Denied

**Error**: `permission denied for schema public`

**Solution**:

```bash
# Grant permissions
sudo -u postgres psql -d paperchase
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON TABLES TO paperchase_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON SEQUENCES TO paperchase_user;
GRANT ALL PRIVILEGES ON SCHEMA public TO paperchase_user;
\q
```

#### 5. Migration Errors

**Error**: `SQLSTATE[HY000]` or migration fails

**Solution**:

```bash
# Reset migrations
php artisan migrate:reset

# Fresh start
php artisan migrate:fresh

# Check syntax
php artisan migrate --step
```

#### 6. Port Already in Use

**Error**: `TCP port 5432 is already in use`

**Solution**:

```bash
# Find process using port
lsof -i :5432                          # macOS
netstat -tulpn | grep 5432             # Linux

# Kill process
kill -9 <PID>

# Or use different port in .env
DB_PORT=5433
```

---

## Performance Optimization

### Create Indexes

```bash
php artisan tinker

# Create indexes for frequently queried columns
>>> DB::statement('CREATE INDEX idx_exams_subject_id ON exams(subject_id)');
>>> DB::statement('CREATE INDEX idx_exams_year ON exams(year)');
>>> DB::statement('CREATE INDEX idx_bookmarks_user_id ON bookmarks(user_id)');
>>> DB::statement('CREATE INDEX idx_downloads_user_id ON downloads(user_id)');
>>> exit
```

### Query Optimization

```php
// Use eager loading to prevent N+1 queries
$exams = Exam::with('subject', 'createdBy')->paginate();

// Use select to limit columns
$exams = Exam::select('id', 'title', 'subject_id', 'year')->get();

// Use whereIn for multiple conditions
$exams = Exam::whereIn('subject_id', $subjectIds)->get();
```

### Connection Pooling

For production, enable connection pooling:

```env
# .env
DATABASE_POOL_MIN=5
DATABASE_POOL_MAX=20
```

### Monitor Database

```bash
# Check active connections
sudo -u postgres psql -d paperchase
SELECT datname, count(*) FROM pg_stat_activity GROUP BY datname;

# View slow queries
sudo -u postgres psql -d paperchase
SELECT query, mean_exec_time FROM pg_stat_statements ORDER BY mean_exec_time DESC;
```

---

## Resources

- [PostgreSQL Official Docs](https://www.postgresql.org/docs/)
- [Laravel Database Connections](https://laravel.com/docs/database)
- [Laravel Migrations](https://laravel.com/docs/migrations)
- [PostgreSQL Performance Tips](https://wiki.postgresql.org/wiki/Performance_Optimization)

---

## Related Documentation

- [README.md](./README.md) - Quick start guide
- [DOCUMENTATION.md](./DOCUMENTATION.md) - API documentation
- [BUSINESS_LOGIC.md](../BUSINESS_LOGIC.md) - Development workflows
- [PROJECT_ANALYSIS.md](../PROJECT_ANALYSIS.md) - System architecture
