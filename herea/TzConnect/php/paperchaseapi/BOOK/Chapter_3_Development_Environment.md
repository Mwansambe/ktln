# Chapter 3: Setting Up Your Development Environment

## Introduction

Before we can start building the Paperchase application, we need to set up a proper development environment. This chapter will guide you through installing all the necessary tools and configuring them to work together seamlessly.

By the end of this chapter, you will have:
- A fully functional PHP development environment
- Composer installed and configured
- Node.js and npm for frontend assets
- PostgreSQL database set up
- Laravel installed and running

## System Requirements

Before installing, make sure your computer meets these minimum requirements:

| Requirement | Minimum Version | Recommended Version |
|-------------|-----------------|---------------------|
| PHP | 8.2 | 8.3 or higher |
| Composer | 2.0 | Latest |
| Node.js | 18.0 | 20.0 or higher |
| PostgreSQL | 15 | 16 or higher |
| RAM | 4 GB | 8 GB or more |
| Disk Space | 10 GB | 20 GB |

## Installing PHP

PHP is the foundation of our Laravel application. Let's install it based on your operating system.

### Installing PHP on Windows

The easiest way to install PHP on Windows is using one of these methods:

#### Method 1: Using XAMPP (Recommended for Beginners)

1. Download XAMPP from [apachefriends.org](https://www.apachefriends.org/)
2. Run the installer
3. During installation, select:
   - Apache
   - MySQL (we'll use PostgreSQL separately)
   - PHP
4. Complete the installation
5. Start Apache from XAMPP Control Panel

#### Method 2: Using WSL2 (Recommended for Development)

If you're on Windows 10/11, WSL2 provides an excellent development environment:

```powershell
# Open PowerShell as Administrator and run:
wsl --install

# Restart your computer

# After restart, open Ubuntu terminal and install PHP:
sudo apt update
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.3 php8.3-cli php8.3-common php8.3-pgsql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-sqlite
```

### Installing PHP on macOS

macOS comes with PHP pre-installed, but it's often an older version. For development, we recommend using Homebrew:

```bash
# Install Homebrew if not installed
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP 8.3
brew install php@8.3

# Add to PATH
echo 'export PATH="/usr/local/opt/php@8.3/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc

# Verify installation
php -v
```

### Installing PHP on Linux (Ubuntu/Debian)

```bash
# Update package list
sudo apt update

# Install PHP and required extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-common php8.3-pgsql php8.3-xml \
    php8.3-mbstring php8.3-curl php8.3-zip php8.3-sqlite php8.3-intl \
    php8.3-bcmath php8.3-redis

# Verify installation
php -v
```

### Verifying PHP Installation

```bash
# Check PHP version
php -v

# You should see output similar to:
# PHP 8.3.0 (cli) (built: Nov 30 2023 12:00:00)
# Copyright (c) The PHP Group
# Zend Engine v4.3.0, Copyright (c) Zend Technologies
#     with Zend OPcache v8.3.0, Copyright (c), by Zend Technologies
```

## Installing Composer

Composer is PHP's dependency manager. It's essential for installing and managing Laravel and its packages.

### Installing Composer on Windows

1. Download the installer from [getcomposer.org](https://getcomposer.org/Composer-Setup.exe)
2. Run the installer
3. When asked about PHP, browse to your PHP installation (e.g., `C:\xampp\php\php.exe`)
4. Complete the installation

### Installing Composer on macOS and Linux

```bash
# Download the installer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Verify the installer
php -r "if (hash_file('SHA384', 'composer-setup.php') === 'INSERT_HASH_HERE') { echo 'Installer verified'; } else { unlink('composer-setup.php'); }"

# Install Composer
php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Remove installer
php -r "unlink('composer-setup.php');"

# Verify installation
composer --version
```

> **Note**: Replace 'INSERT_HASH_HERE' with the actual hash from [getcomposer.org](https://getcomposer.org/download/)

### Alternative: Install Composer Locally

If you don't have sudo access:

```bash
# Download composer
curl -sS https://getcomposer.org/installer | php

# Use composer
php composer.phar install
```

## Installing Node.js and npm

Node.js and npm are needed for compiling frontend assets (CSS and JavaScript) using Vite, which comes with Laravel.

### Installing Node.js on Windows

1. Download the LTS installer from [nodejs.org](https://nodejs.org/)
2. Run the installer
3. Complete the installation
4. Restart your terminal

### Installing Node.js on macOS

```bash
# Using Homebrew
brew install node

# Verify installation
node -v    # Should show v20.x.x
npm -v     # Should show 10.x.x
```

### Installing Node.js on Linux

```bash
# Using NodeSource repository
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Verify installation
node -v
npm -v
```

## Installing PostgreSQL

PostgreSQL is our database of choice for Paperchase. Let's install and configure it.

### Installing PostgreSQL on Windows

1. Download the installer from [postgresql.org](https://www.postgresql.org/download/windows/)
2. Run the installer
3. Set a password for the postgres user
4. Keep default port (5432)
5. Complete the installation

### Installing PostgreSQL on macOS

```bash
# Using Homebrew
brew install postgresql@15

# Start PostgreSQL
brew services start postgresql@15

# Verify it's running
pg_isready
```

### Installing PostgreSQL on Linux

```bash
# Install PostgreSQL
sudo apt update
sudo apt install postgresql postgresql-contrib

# Start PostgreSQL
sudo systemctl start postgresql
sudo systemctl enable postgresql

# Check status
sudo systemctl status postgresql
```

### Configuring PostgreSQL

After installation, we need to set up a database and user for Paperchase:

```bash
# Switch to postgres user
sudo -u postgres psql

# Create database
CREATE DATABASE paperchase;

# Create user
CREATE USER paperchase_user WITH PASSWORD 'your_secure_password';

# Grant privileges
GRANT ALL PRIVILEGES ON DATABASE paperchase TO paperchase_user;

# Exit psql
\q
```

### Allowing Password Authentication

For local development, we need to configure PostgreSQL to accept password authentication:

```bash
# Find and edit pg_hba.conf
sudo nano /etc/postgresql/15/main/pg_hba.conf

# Change authentication method to md5 or scram-sha-256:
# local   all             all                                     peer
# host    all             all             127.0.0.1/32            scram-sha-256

# Restart PostgreSQL
sudo systemctl restart postgresql
```

## Installing Laravel

Now let's install Laravel using Composer. There are two main approaches:

### Method 1: Create New Laravel Project

```bash
# Install Laravel globally first (optional but recommended)
composer global require laravel/installer

# Add Composer's global bin to your PATH
# Add this to your ~/.bashrc or ~/.zshrc
export PATH="$HOME/.composer/vendor/bin:$PATH"

# Create new Laravel project
laravel new paperchase

# Or using Composer directly
composer create-project laravel/laravel paperchase
```

### Method 2: Using Our Existing Project

Since we've already cloned the Paperchase project, let's set it up:

```bash
# Navigate to project directory
cd php/paperchaseapi

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Understanding the .env File

The `.env` file contains all your application configuration. Let's configure it for our setup:

```env
APP_NAME=PaperChase
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:generated_key_here
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=paperchase
DB_USERNAME=paperchase_user
DB_PASSWORD=your_secure_password

# JWT Configuration
JWT_SECRET=your_jwt_secret_here
JWT_TTL=60
```

### Generate JWT Secret

```bash
# Generate JWT secret for authentication
php artisan jwt:secret --force
```

## Setting Up the Database

Now let's create the database tables using migrations:

```bash
# Run migrations
php artisan migrate

# If you want to start fresh
php artisan migrate:fresh

# With seed data
php artisan migrate:fresh --seed
```

## Installing Frontend Dependencies

Laravel uses Vite for bundling frontend assets:

```bash
# Install npm dependencies
npm install

# Build for development
npm run dev

# Or build for production
npm run build
```

## Running the Development Server

Now let's start our development server:

```bash
# Start the Laravel development server
php artisan serve

# Or specify a port
php artisan serve --port=8080
```

You should see output like:

```
INFO  Server running on [http://127.0.0.1:8000].

Press Ctrl+C to stop the server
```

## Installing Useful Tools

### PHPStorm/WebStorm

JetBrains IDEs are excellent for Laravel development. Students can get a free license from [jetbrains.com](https://www.jetbrains.com/community/education/#students)

### VS Code Extensions

If you're using Visual Studio Code, install these extensions:

1. **PHP Intelephense** - PHP code completion and analysis
2. **Laravel Blade formatter** - Format Blade templates
3. **Laravel goto view** - Navigate to Blade views
4. **Laravel Artisan** - Run Artisan commands from VS Code

```bash
# Install via VS Code marketplace or:
code --install-extension bmewburn.vscode-intelephense-client
code --install-extension codingyu.laravel-blade
code --install-extension amirmeyad.laravel-env
```

### Laravel Debugbar

For debugging and profiling:

```bash
composer require barryvdh/laravel-debugbar --dev
```

## Troubleshooting Common Issues

### Port Already in Use

If port 8000 is already in use:

```bash
# Find what's using the port
lsof -i :8000

# Kill the process
kill -9 <PID>

# Or use a different port
php artisan serve --port=8001
```

### Database Connection Issues

```bash
# Check if PostgreSQL is running
pg_isready -h 127.0.0.1 -p 5432

# Test connection
psql -h 127.0.0.1 -U paperchase_user -d paperchase

# Clear Laravel config cache
php artisan config:clear
php artisan cache:clear
```

### Permission Issues

```bash
# Fix storage permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# On macOS with Homebrew PHP
sudo chown -R $(whoami) staff storage bootstrap/cache
```

### PHP Extensions Missing

```bash
# Install missing PHP extensions (Ubuntu)
sudo apt install php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-curl

# Restart Apache or PHP-FPM
sudo systemctl restart apache2
# or
sudo systemctl restart php8.3-fpm
```

## Your First Laravel Application

Let's verify everything is working by creating a simple route:

```bash
# Open routes/web.php and add:
Route::get('/hello', function () {
    return 'Hello, World! Welcome to Paperchase!';
});
```

Visit `http://localhost:8000/hello` in your browser. You should see "Hello, World! Welcome to Paperchase!"

## Summary

In this chapter, you have:
- ✅ Installed PHP 8.3+
- ✅ Installed Composer for dependency management
- ✅ Installed Node.js and npm for frontend assets
- ✅ Set up PostgreSQL database
- ✅ Installed Laravel
- ✅ Configured the .env file
- ✅ Set up the database with migrations
- ✅ Started the development server

### What's Next?

In Chapter 4, we'll dive deeper into Laravel's MVC architecture and understand how the framework handles requests, processes data, and returns responses.

---

## Practice Exercises

1. **Verify Your Setup**: Run `php artisan serve` and access the application in your browser. Take a screenshot of the welcome page.

2. **Explore Artisan**: Run `php artisan list` to see all available Artisan commands. Try a few commands like `php artisan --version` and `php artisan about`.

3. **Database Connection**: Try connecting to the database using a GUI tool like DBeaver or pgAdmin. Explore the database structure.

4. **Modify Configuration**: Change the application name in `.env` and see how it affects the application.

5. **Create a Test Route**: Create a route that displays today's date and time.

---

*Continue to Chapter 4: Understanding MVC Architecture in Detail*

