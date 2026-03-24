# JamExams - Complete Exam Distribution Platform

A full-stack exam distribution system built with **Laravel + PostgreSQL** (backend) and **Kotlin + Jetpack Compose** (Android mobile app).

## 📁 Project Structure

```
jamexams/
├── backend/          # Laravel API + Admin Web Panel
├── mobile/           # Android App (Kotlin + Compose)
└── docs/             # Documentation Book
    └── JamExams_Book.md
```

## 🚀 Quick Start

### Backend
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
# Configure database in .env
php artisan migrate
php artisan db:seed
php artisan serve
```

**Admin Login:**
- URL: http://localhost:8000/login
- Email: admin@jamexams.com
- Password: Admin@123

### Mobile App
```bash
# Open mobile/ in Android Studio
# Add google-services.json from Firebase Console
# Update BASE_URL in app/build.gradle.kts
# Build & Run
```

## 🌐 Live Demo
- Admin Panel: https://chasepaper.duckdns.org/app/dashboard
- API Base: https://chasepaper.duckdns.org/api

## 📘 Documentation
See `docs/JamExams_Book.md` for the complete step-by-step teaching guide.

## 🔑 Key Features
- ✅ JWT/Sanctum authentication
- ✅ 30-day access control with auto-expiry
- ✅ PDF exam upload and download
- ✅ Firebase push notifications
- ✅ Role-based access (Admin, Editor, Viewer)
- ✅ MVVM + Clean Architecture (mobile)
- ✅ Laravel Scheduler for auto-deactivation
- ✅ PostgreSQL with proper indexing

## 👨‍💻 Author
Jacob Godwin Mwansambe
