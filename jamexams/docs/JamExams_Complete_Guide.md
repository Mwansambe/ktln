# 📖 JamExams: Building a Full Exam Distribution System from Scratch

**Author: Jacob Godwin Mwansambe**
**Version: 1.0 | Year: 2026**

---

> *"The learner who finishes this book can rebuild the entire JamExams system — from database to mobile app — without AI assistance."*

---

## TABLE OF CONTENTS

- Part 1: Foundations
- Part 2: Database Design
- Part 3: Laravel Backend
- Part 4: REST API
- Part 5: Android Mobile App (Kotlin + Jetpack Compose)
- Part 6: Integration & Testing
- Part 7: Deployment

---

# PART 1: FOUNDATIONS

## Chapter 1 — Understanding the System

### 1.1 What is JamExams?

JamExams is a full-stack exam distribution platform with two main parts:

1. **Web Admin Panel** (Laravel): Admins upload exams, manage users, control access.
2. **Mobile App** (Kotlin + Jetpack Compose): Students download and view exams.

### 1.2 System Architecture Overview

```
┌─────────────────────────────────────────────────────┐
│                   JAMEXAMS SYSTEM                    │
│                                                      │
│  ┌──────────────┐      ┌──────────────────────────┐ │
│  │  Mobile App  │ ───> │   Laravel REST API        │ │
│  │  (Android)   │      │   /api/...                │ │
│  └──────────────┘      └────────────┬─────────────┘ │
│                                     │               │
│  ┌──────────────┐      ┌────────────▼─────────────┐ │
│  │  Web Admin   │ ───> │   PostgreSQL Database     │ │
│  │  Panel       │      │                           │ │
│  └──────────────┘      └───────────────────────────┘ │
│                                     │               │
│  ┌──────────────────────────────────▼─────────────┐ │
│  │              Firebase (FCM)                     │ │
│  │         Push Notifications                      │ │
│  └─────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────┘
```

### 1.3 Data Flow

```
Admin uploads exam via web panel
        │
        ▼
Laravel stores PDF + metadata in database
        │
        ▼
Laravel sends FCM notification via Firebase
        │
        ▼
All active users receive push notification
        │
        ▼
Student taps notification → opens exam screen
        │
        ▼
App fetches exam details from API
        │
        ▼
Student downloads PDF to device
```

---

## Chapter 2 — Development Environment Setup

### 2.1 Backend Requirements

```bash
# Install PHP 8.1+
sudo apt install php8.1 php8.1-pgsql php8.1-mbstring php8.1-xml php8.1-curl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install PostgreSQL
sudo apt install postgresql postgresql-contrib

# Create database
sudo -u postgres psql
CREATE DATABASE jamexams;
CREATE USER jamexams_user WITH PASSWORD 'strong_password';
GRANT ALL PRIVILEGES ON DATABASE jamexams TO jamexams_user;
\q
```

### 2.2 Laravel Project Setup

```bash
# Clone project
git clone https://github.com/yourname/jamexams.git
cd jamexams/backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env
php artisan key:generate

# Edit .env with your database credentials
nano .env

# Run migrations and seed
php artisan migrate --seed

# Create storage link
php artisan storage:link

# Start development server
php artisan serve
```

### 2.3 Android Studio Setup

1. Download Android Studio from developer.android.com
2. Open the `mobile/` folder as a project
3. Sync Gradle
4. Update `BASE_URL` in `app/build.gradle.kts`
5. Add `google-services.json` from Firebase Console
6. Run on emulator or device

---

# PART 2: DATABASE DESIGN

## Chapter 3 — Schema & Relationships

### 3.1 Entity Relationship Diagram

```
users
  │
  ├── activation_records (1:N)
  ├── exams (1:N via uploaded_by)
  └── subjects (1:N via created_by)

subjects
  └── exams (1:N)

exams
  └── push_notifications (1:N)

roles ──── model_has_roles ──── users
permissions ── role_has_permissions ── roles
```

### 3.2 Users Table

| Column       | Type      | Description                          |
|--------------|-----------|--------------------------------------|
| id           | uuid      | Primary key (UUID for security)      |
| name         | varchar   | Full name                            |
| email        | varchar   | Unique email (login identifier)      |
| password     | varchar   | bcrypt hashed                        |
| phone        | varchar   | Optional contact                     |
| is_active    | boolean   | Default false — admin must activate  |
| activated_at | timestamp | When admin activated the account     |
| expires_at   | timestamp | activated_at + 30 days               |
| fcm_token    | varchar   | Firebase push token                  |

**Why UUID?** Using UUIDs instead of auto-increment integers prevents:
- Enumeration attacks (attacker guesses `/users/1`, `/users/2`)
- Data leakage about system size

### 3.3 Exams Table

| Column               | Type      | Description                     |
|----------------------|-----------|---------------------------------|
| id                   | bigint    | Primary key                     |
| code                 | varchar   | Short code e.g. "001"           |
| title                | varchar   | Full exam title                 |
| subject_id           | FK        | Links to subjects table         |
| exam_type            | enum      | PAST_PAPER, MOCK, MIDTERM, etc  |
| class_level          | varchar   | Form 1, Form 2, etc             |
| year                 | year      | Exam year                       |
| exam_file_path       | varchar   | Storage path to PDF             |
| marking_scheme_path  | varchar   | Optional marking scheme path    |
| download_count       | integer   | Incremented on each download    |

### 3.4 Activation Records Table

**Purpose**: Every time an admin activates a user, a record is created.
This provides a full audit trail — you can see exactly who activated whom and when.

```
activation_records:
  user_id      → which user was activated
  activated_by → which admin did the activation
  activated_at → when activation started
  expires_at   → when access ends (30 days later)
  duration_days → how many days granted
  notes        → admin notes (optional)
  is_current   → only one record is "current" at a time
```

---

# PART 3: LARAVEL BACKEND

## Chapter 4 — Project Structure

```
backend/
├── app/
│   ├── Console/
│   │   ├── Commands/
│   │   │   └── DeactivateExpiredUsers.php   ← runs daily
│   │   └── Kernel.php                       ← cron schedule
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                         ← mobile API
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── ExamController.php
│   │   │   │   └── SubjectController.php
│   │   │   └── Admin/                       ← web panel
│   │   │       ├── DashboardController.php
│   │   │       ├── ExamController.php
│   │   │       ├── UserController.php
│   │   │       └── SubjectController.php
│   │   └── Middleware/
│   │       └── CheckActivation.php          ← access control
│   ├── Models/
│   │   ├── User.php
│   │   ├── Exam.php
│   │   ├── Subject.php
│   │   ├── ActivationRecord.php
│   │   └── PushNotification.php
│   └── Services/
│       ├── ExamService.php                  ← file upload logic
│       ├── UserService.php                  ← activation logic
│       └── NotificationService.php          ← FCM logic
├── database/
│   ├── migrations/
│   └── seeders/
└── routes/
    ├── api.php                              ← API routes
    └── web.php                              ← web routes
```

## Chapter 5 — Authentication System

### 5.1 How Sanctum Works

Laravel Sanctum provides token-based authentication.

**Flow:**
1. Student sends `POST /api/auth/login` with email + password
2. Server validates credentials
3. Server creates a token in the `personal_access_tokens` table
4. Token is returned to the app
5. App saves token in DataStore
6. Every subsequent request includes `Authorization: Bearer {token}`

### 5.2 Access Control Logic

```
Request comes in
    │
    ▼
auth:sanctum middleware → Is token valid?
    │
    ├── NO  → 401 Unauthenticated
    │
    └── YES → check.activation middleware
                │
                ├── is_active = false → 403 ACCOUNT_NOT_ACTIVATED
                │
                ├── expires_at < now() → 403 ACCESS_EXPIRED
                │                        (with payment message)
                │
                └── ALL OK → continue to controller
```

### 5.3 The 30-Day Activation System

```php
// When admin activates a user:
$user->update([
    'is_active'    => true,
    'activated_at' => now(),
    'expires_at'   => now()->addDays(30),
]);

// Daily cron job (runs at midnight):
php artisan users:deactivate-expired

// Crontab entry:
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Chapter 6 — File Storage & Security

### 6.1 PDF Upload Strategy

```php
// In ExamService::createExam():
$examFile = $request->file('exam_file');

// Store in public disk (accessible via URL)
$path = $examFile->store('exams/papers', 'public');

// For private files (not directly accessible):
$path = $examFile->store('exams/private', 'local');
// Then serve via controller with Storage::download()
```

### 6.2 File Validation Rules

```php
'exam_file' => [
    'required',
    'file',
    'mimes:pdf',       // Only PDF files
    'max:51200',       // Max 50MB
],
```

### 6.3 API Security

```php
// Rate limiting (in RouteServiceProvider):
Route::middleware(['throttle:60,1'])->group(function () {
    // 60 requests per minute
});

// Password hashing:
Hash::make($password);  // bcrypt by default

// Input sanitization via Form Requests:
class StoreExamRequest extends FormRequest {
    public function rules(): array {
        return [
            'title' => 'required|string|max:200|strip_tags',
        ];
    }
}
```

---

# PART 4: REST API

## Chapter 7 — API Design Principles

### 7.1 Consistent Response Format

Every API response follows this structure:

**Success:**
```json
{
    "status": "success",
    "message": "Exams retrieved.",
    "data": {
        "exams": [...],
        "pagination": { "current_page": 1, "last_page": 5, "total": 83 }
    }
}
```

**Error:**
```json
{
    "status": "error",
    "message": "Your access has expired.",
    "code": "ACCESS_EXPIRED"
}
```

**Why a `code` field?** The app can react differently based on `code`:
- `ACCESS_EXPIRED` → Show payment message
- `ACCOUNT_NOT_ACTIVATED` → Show "contact admin" message
- `VALIDATION_ERROR` → Show field-level errors

### 7.2 Complete API Reference

| Method | Endpoint                        | Auth | Description              |
|--------|---------------------------------|------|--------------------------|
| POST   | /api/auth/login                 | No   | Login                    |
| POST   | /api/auth/logout                | Yes  | Logout                   |
| GET    | /api/auth/me                    | Yes  | Get profile              |
| PUT    | /api/auth/fcm-token             | Yes  | Update push token        |
| GET    | /api/subjects                   | Yes  | List subjects            |
| GET    | /api/exams                      | Yes  | List exams (filterable)  |
| GET    | /api/exams/{id}                 | Yes  | Single exam details      |
| GET    | /api/exams/{id}/download        | Yes  | Download exam PDF        |
| GET    | /api/exams/{id}/marking-scheme  | Yes  | Download marking scheme  |

### 7.3 Query Parameters for /api/exams

```
GET /api/exams?subject_id=3&class_level=Form+3&year=2025&page=1&per_page=20
```

---

# PART 5: ANDROID MOBILE APP

## Chapter 8 — Architecture

### 8.1 Clean Architecture Layers

```
presentation/      ← ViewModels, UI state
    │
domain/            ← Business logic (pure Kotlin, no Android)
    │
data/              ← API calls, local storage, repositories
```

**Why three layers?** Each layer has one responsibility:
- **Presentation**: "What does the user see?"
- **Domain**: "What are the business rules?"
- **Data**: "Where does the data come from?"

This makes testing easy — you can test domain logic without Android.

### 8.2 MVVM Pattern

```
User Action
    │
    ▼
Composable ──→ ViewModel ──→ Repository ──→ API/DB
                  │
                  ▼
              StateFlow
                  │
                  ▼
Composable recomposes based on state
```

### 8.3 State Management with StateFlow

```kotlin
// In ViewModel:
private val _uiState = MutableStateFlow<LoginUiState>(LoginUiState.Idle)
val uiState: StateFlow<LoginUiState> = _uiState.asStateFlow()

// In Composable:
val uiState by viewModel.uiState.collectAsState()

when (uiState) {
    is LoginUiState.Loading -> CircularProgressIndicator()
    is LoginUiState.Success -> // navigate
    is LoginUiState.Error   -> // show error
}
```

## Chapter 9 — Jetpack Compose Concepts

### 9.1 What is Recomposition?

Compose redraws parts of the UI when state changes. Think of it as:

> *"Every time the data changes, Compose automatically redraws only the affected parts of the screen."*

```kotlin
// This text updates automatically when `name` changes:
var name by remember { mutableStateOf("") }
Text("Hello, $name")
TextField(value = name, onValueChange = { name = it })
```

### 9.2 State Hoisting

State should be "hoisted" up to the parent that needs to share it:

```kotlin
// BAD: state inside composable (hard to test)
@Composable
fun EmailField() {
    var email by remember { mutableStateOf("") }
    TextField(value = email, onValueChange = { email = it })
}

// GOOD: state hoisted to parent (testable, shareable)
@Composable
fun EmailField(value: String, onValueChange: (String) -> Unit) {
    TextField(value = value, onValueChange = onValueChange)
}
```

### 9.3 LaunchedEffect for Side Effects

```kotlin
// Navigate only when state changes to Success:
LaunchedEffect(uiState) {
    if (uiState is LoginUiState.Success) {
        navController.navigate("home")
    }
}
```

---

## Chapter 10 — Push Notifications

### 10.1 FCM Flow

```
Admin uploads exam
    │
    ▼
Backend collects FCM tokens of all active users
    │
    ▼
POST https://fcm.googleapis.com/fcm/send
    { registration_ids: [...], notification: {...}, data: { exam_id: "42" } }
    │
    ▼
Firebase routes to each device
    │
    ▼
JamExamsFcmService.onMessageReceived() called
    │
    ▼
Notification shown in system tray
    │
    ▼
User taps notification
    │
    ▼
Intent with deep link: jamexams://exam/42
    │
    ▼
NavGraph opens ExamDetailScreen(examId = 42)
```

### 10.2 Updating FCM Token

Firebase periodically generates new tokens. The app must keep the backend updated:

```kotlin
// In JamExamsFcmService:
override fun onNewToken(token: String) {
    // 1. Save locally
    tokenDataStore.saveFcmToken(token)
    // 2. Send to backend
    api.updateFcmToken(FcmTokenRequest(token))
}
```

---

# PART 6: INTEGRATION & TESTING

## Chapter 11 — Connecting Mobile to Backend

### 11.1 Handling Network Errors Gracefully

```kotlin
// Always wrap API calls:
return try {
    val response = api.getExams(...)
    if (response.isSuccessful) {
        Result.Success(response.body()!!.data!!)
    } else {
        Result.Error(response.body()?.message ?: "Unknown error")
    }
} catch (e: java.net.UnknownHostException) {
    Result.Error("No internet connection")
} catch (e: java.net.SocketTimeoutException) {
    Result.Error("Connection timed out. Please try again.")
} catch (e: Exception) {
    Result.Error(e.message ?: "An unexpected error occurred")
}
```

### 11.2 Testing ViewModels

```kotlin
@Test
fun `login with valid credentials updates state to Success`() = runTest {
    // Arrange
    val mockRepo = mockk<AuthRepository>()
    coEvery { mockRepo.login(any(), any(), any()) } returns Result.Success(fakeUser)
    val viewModel = LoginViewModel(mockRepo)

    // Act
    viewModel.onEmailChanged("test@example.com")
    viewModel.onPasswordChanged("password123")
    viewModel.login()

    // Assert
    val state = viewModel.uiState.value
    assertTrue(state is LoginUiState.Success)
}
```

### 11.3 Testing API Endpoints

```php
// ExamControllerTest.php
public function test_authenticated_user_can_list_exams(): void
{
    $user = User::factory()->create(['is_active' => true, 'expires_at' => now()->addDays(30)]);
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->getJson('/api/exams');

    $response->assertStatus(200)
             ->assertJsonPath('status', 'success')
             ->assertJsonStructure(['data' => ['exams', 'pagination']]);
}

public function test_expired_user_is_rejected(): void
{
    $user = User::factory()->create([
        'is_active' => true,
        'expires_at' => now()->subDay(), // expired yesterday
    ]);
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                     ->getJson('/api/exams');

    $response->assertStatus(403)
             ->assertJsonPath('code', 'ACCESS_EXPIRED');
}
```

---

# PART 7: DEPLOYMENT

## Chapter 12 — Backend Deployment

### 12.1 Server Requirements

- Ubuntu 22.04+ VPS (Contabo, DigitalOcean, etc.)
- PHP 8.1+
- PostgreSQL 14+
- Nginx
- SSL certificate (Let's Encrypt)

### 12.2 Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/jamexams/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Block direct access to storage
    location /storage {
        deny all;
    }
}
```

### 12.3 Crontab for Scheduler

```bash
# Edit crontab:
crontab -e

# Add this line:
* * * * * cd /var/www/jamexams && php artisan schedule:run >> /dev/null 2>&1
```

This runs every minute and checks if any scheduled tasks need to run.
The `DeactivateExpiredUsers` command runs daily at midnight.

### 12.4 Mobile APK Release

```bash
# In Android Studio:
Build → Generate Signed Bundle/APK → APK
→ Create new keystore (save it safely!)
→ Build Type: Release
→ Done
```

**Important**: Never lose your keystore file. You need it for every update.

---

# APPENDIX

## A — Glossary

| Term | Meaning |
|------|---------|
| API | Application Programming Interface — the "door" between frontend and backend |
| JWT | JSON Web Token — an encoded string that proves who you are |
| Sanctum | Laravel's simple token authentication package |
| FCM | Firebase Cloud Messaging — Google's push notification service |
| MVVM | Model-View-ViewModel — architecture pattern for Android |
| StateFlow | Kotlin's reactive state container (like LiveData but better) |
| Compose | Android's modern UI toolkit — UI is written in Kotlin code |
| Migration | A file that creates/modifies database tables |
| Seeder | A file that fills database with initial data |
| Middleware | Code that runs before/after every request |
| Repository | Pattern that separates data fetching from business logic |

## B — Common Debugging Tips

### Backend
```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# Test API with curl
curl -X POST https://yourdomain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@jamexams.com","password":"Admin@123"}'

# Clear all caches
php artisan optimize:clear
```

### Android
```kotlin
// Log debug info
Log.d("JamExams", "API response: $response")

// Check if user is logged in
val token = runBlocking { tokenDataStore.token.first() }
Log.d("AUTH", "Token: $token")
```

## C — Chapter Exercises

### Chapter 3 Exercise
1. Draw the ER diagram on paper
2. Write the SQL for the `subjects` table manually
3. Add a `notes` column to `exams` table via a new migration

### Chapter 5 Exercise
1. Add a `register` endpoint to the API
2. Add email verification before activation

### Chapter 9 Exercise
1. Add a "Profile" screen showing user name, email, and days remaining
2. Add a dark mode toggle using the JamExams dark theme

### Chapter 10 Exercise
1. Test push notifications using FCM test message from Firebase Console
2. Add notification sound and custom icon

---

*JamExams Documentation Book — Jacob Godwin Mwansambe — 2026*
