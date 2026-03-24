# JamExams Backend

Laravel 10 + PostgreSQL REST API and Admin Panel.

## Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
# Edit .env with DB credentials
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
```

## Run Tests
```bash
php artisan test
```

## Run Scheduler (Development)
```bash
php artisan schedule:work
```

## API Base URL
`http://localhost:8000/api`

## Admin Panel
`http://localhost:8000/app/dashboard`
