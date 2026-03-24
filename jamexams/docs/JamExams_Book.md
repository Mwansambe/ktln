# 📖 JamExams: Building a Full Exam Distribution System from Scratch

**Author: Jacob Godwin Mwansambe**
**Version: 1.0**

---

# PREFACE

This book teaches you how to build JamExams — a complete exam distribution platform — from absolute zero. By the end, you will be able to rebuild this entire system independently, without any AI assistance.

We build:
- A **Laravel + PostgreSQL** web backend and admin panel
- A **Kotlin + Jetpack Compose** Android mobile app
- A **REST API** connecting both systems
- **Firebase** push notifications

Every decision is explained. Every concept is taught with real-life analogies. Every chapter ends with exercises.

---

# PART 1: FUNDAMENTALS

---

## Chapter 1: Understanding What We Are Building

### 1.1 The Big Picture

Imagine a school library. The librarian (admin) organizes books on shelves (subjects). Students come in and borrow books (download exams). The library has a membership system — only paid members can borrow.

**JamExams is that digital library.** Here's the breakdown:

| Real Library | JamExams |
|---|---|
| Librarian | Admin User |
| Books | Exam PDFs |
| Book shelves | Subject Categories |
| Membership | 30-day Activation |
| Borrowing | PDF Download |
| Library card | API Token |

### 1.2 System Architecture Overview

```
┌─────────────────────────────────────────────────────────┐
│                    JamExams System                       │
│                                                         │
│  ┌──────────────┐    REST API    ┌──────────────────┐  │
│  │   Mobile App │ ◄────────────► │  Laravel Backend │  │
│  │  (Students)  │                │   (API + Admin)  │  │
│  │  Kotlin/     │                │                  │  │
│  │  Compose     │                │   PostgreSQL DB  │  │
│  └──────────────┘                └──────────────────┘  │
│          ▲                               ▲              │
│          │  Push Notifications           │ Web Browser  │
│          └──────── Firebase ─────────────┘             │
└─────────────────────────────────────────────────────────┘
```

### 1.3 Technology Choices Explained

**Why Laravel?**
Laravel is a PHP framework that provides everything out of the box: routing, database ORM, authentication, file storage, job queues, and more. For a system like JamExams, this means faster development without reinventing the wheel.

**Why PostgreSQL?**
PostgreSQL is a powerful relational database. Compared to MySQL, it supports more advanced features like full-text search, better JSON handling, and is more strict with data types — which means fewer bugs.

**Why Kotlin + Jetpack Compose?**
Kotlin is the modern language for Android development. Jetpack Compose replaces XML layouts with declarative Kotlin code — you describe what the UI should look like, and Android figures out how to draw it.

**Why Firebase Cloud Messaging (FCM)?**
FCM is Google's free push notification service. It handles all the complexity of delivering notifications to Android devices reliably.

---

## Chapter 2: Setting Up Your Development Environment

### 2.1 Backend Setup (Laravel)

#### Step 1: Install PHP 8.1+

On Ubuntu/Debian:
```bash
sudo apt update
sudo apt install php8.1 php8.1-pgsql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip
```

On macOS with Homebrew:
```bash
brew install php@8.1
```

Verify installation:
```bash
php -v
# PHP 8.1.x
```

#### Step 2: Install Composer (PHP Package Manager)

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

**Analogy:** Composer is like the Google Play Store for PHP code — it downloads and manages packages that other developers have built.

#### Step 3: Install PostgreSQL

```bash
sudo apt install postgresql postgresql-contrib
sudo service postgresql start
```

Create the database:
```bash
sudo -u postgres psql
CREATE DATABASE jamexams;
CREATE USER jamexams_user WITH PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE jamexams TO jamexams_user;
\q
```

#### Step 4: Clone and Set Up Laravel Project

```bash
# Clone the project
git clone https://github.com/yourusername/jamexams-backend.git
cd jamexams-backend

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Edit .env file with your database credentials
nano .env
```

Configure these values in `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=jamexams
DB_USERNAME=jamexams_user
DB_PASSWORD=secure_password
```

#### Step 5: Run Migrations and Seed Data

```bash
# Run all database migrations
php artisan migrate

# Seed with initial data (admin user + subjects)
php artisan db:seed

# Create storage symlink (for file access)
php artisan storage:link
```

After seeding, you can login with:
- **Email:** admin@jamexams.com
- **Password:** Admin@123

#### Step 6: Start Development Server

```bash
php artisan serve
# Server running at http://localhost:8000
```

### 2.2 Mobile Setup (Android Studio)

#### Step 1: Install Android Studio

Download from: https://developer.android.com/studio

Minimum requirements:
- RAM: 8GB (16GB recommended)
- Storage: 8GB free
- JDK 17 included

#### Step 2: Configure the Project

Open Android Studio → Open → select the `mobile/` folder.

In `app/build.gradle.kts`, update the BASE_URL:
```kotlin
buildConfigField("String", "BASE_URL", "\"http://YOUR_SERVER_IP/api/\"")
```

#### Step 3: Add google-services.json

1. Go to Firebase Console (console.firebase.google.com)
2. Create project "JamExams"
3. Add Android app with package name `com.jamexams.app`
4. Download `google-services.json`
5. Place it in the `mobile/app/` folder

#### Step 4: Build and Run

Connect an Android device (API 24+) or create an emulator:
```
Build → Run → Select device
```

---

## Chapter 3: Database Design Deep Dive

### 3.1 Entity Relationship Diagram

```
users
├── id (UUID, PK)
├── name
├── email (unique)
├── password (hashed)
├── phone
├── fcm_token
├── is_active (boolean)
├── activated_at (timestamp)
└── expires_at (timestamp)

roles / permissions (Spatie package)
├── id
├── name
└── guard_name

subjects
├── id (PK)
├── name
├── code (unique, e.g., PHY)
├── description
├── color (hex)
└── created_by → users.id

exams
├── id (PK)
├── code (e.g., 001)
├── title
├── subject_id → subjects.id
├── exam_type (PAST_PAPER/MOCK/etc)
├── class_level
├── year
├── exam_file_path
├── exam_file_size
├── marking_scheme_path
├── is_featured
├── is_published
├── download_count
└── uploaded_by → users.id

activation_records
├── id (PK)
├── user_id → users.id
├── activated_by → users.id
├── activated_at
├── expires_at
├── duration_days
└── is_current

push_notifications
├── id (PK)
├── title
├── body
├── exam_id → exams.id
├── sent_by → users.id
├── recipient_count
└── data (JSON)
```

### 3.2 Why We Use UUIDs for User IDs

Traditional auto-increment integers (1, 2, 3...) are predictable. A hacker can guess user IDs. UUIDs (like `a3f8c2d1-...`) are impossible to guess, making your API more secure.

### 3.3 Why We Index Columns

```sql
-- Without index: PostgreSQL scans every row (slow for 100,000 users)
SELECT * FROM users WHERE email = 'student@test.com';

-- With index: PostgreSQL jumps directly to the row (fast)
CREATE INDEX idx_users_email ON users(email);
```

Think of an index like a book's index page — instead of reading every page to find "PostgreSQL", you jump to page 47 directly.

---

# PART 2: BACKEND DEVELOPMENT

---

## Chapter 4: Laravel Architecture

### 4.1 MVC + Service Layer Pattern

JamExams uses a layered architecture:

```
HTTP Request
    ↓
Route (routes/api.php, routes/web.php)
    ↓
Middleware (auth, check.activation)
    ↓
Controller (receives request, delegates)
    ↓
Service (business logic lives here)
    ↓
Model/Repository (database interaction)
    ↓
Response (JSON for API, view for web)
```

**Why Services?**
Controllers should only handle HTTP. Business logic (e.g., "when activating a user, also create an activation record and set expires_at") belongs in a Service. This makes testing easier and keeps code clean.

### 4.2 The Standard API Response Format

Every API response follows this format:

```json
// Success
{
  "status": "success",
  "message": "Request successful",
  "data": { ... }
}

// Error
{
  "status": "error",
  "message": "Error description",
  "code": "ERROR_CODE"
}
```

The `code` field allows the mobile app to show specific UI (e.g., show payment message when code is `ACCESS_EXPIRED`).

### 4.3 Authentication Flow

```
Mobile App                     Laravel Backend
    │                               │
    │  POST /api/auth/login         │
    │  { email, password, fcm_token}│
    │ ──────────────────────────► │
    │                               │ 1. Find user by email
    │                               │ 2. Verify password hash
    │                               │ 3. Check is_active = true
    │                               │ 4. Check expires_at > now()
    │                               │ 5. Create Sanctum token
    │                               │ 6. Update FCM token
    │  { token, user data }         │
    │ ◄────────────────────────── │
    │                               │
    │ (Store token in DataStore)    │
    │                               │
    │  GET /api/exams               │
    │  Authorization: Bearer {token}│
    │ ──────────────────────────► │
    │                               │ 7. Validate token
    │                               │ 8. Check activation again
    │  { exams list }               │
    │ ◄────────────────────────── │
```

---

## Chapter 5: API Endpoints Reference

### Authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | /api/auth/login | Login and get token | No |
| POST | /api/auth/logout | Logout (revoke token) | Yes |
| GET | /api/auth/me | Get current user profile | Yes |
| PUT | /api/auth/fcm-token | Update Firebase token | Yes |

### Subjects

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | /api/subjects | List all active subjects | Yes |

### Exams

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | /api/exams | List exams (with filters) | Yes |
| GET | /api/exams/{id} | Get exam details | Yes |
| GET | /api/exams/{id}/download | Download exam PDF | Yes |
| GET | /api/exams/{id}/marking-scheme | Download marking scheme | Yes |

### Query Parameters for /api/exams

| Parameter | Type | Example | Description |
|-----------|------|---------|-------------|
| subject_id | int | 1 | Filter by subject |
| class_level | string | "Form 4" | Filter by class |
| exam_type | string | "PAST_PAPER" | Filter by type |
| year | int | 2024 | Filter by year |
| search | string | "Physics" | Search in title |
| featured | boolean | true | Only featured exams |
| page | int | 1 | Page number |
| per_page | int | 20 | Items per page |

---

## Chapter 6: Access Control System

### 6.1 The 30-Day Activation System

When an admin activates a user:

```php
// UserService.php - activateUser()

$now = now();
$expiresAt = $now->copy()->addDays($days); // Default: 30 days

// 1. Create audit record
ActivationRecord::create([
    'user_id'      => $user->id,
    'activated_by' => $adminId,
    'activated_at' => $now,
    'expires_at'   => $expiresAt,
    'duration_days'=> $days,
]);

// 2. Update user status
$user->update([
    'is_active'    => true,
    'activated_at' => $now,
    'expires_at'   => $expiresAt,
]);
```

### 6.2 Auto-Deactivation via Laravel Scheduler

Add to your server's crontab (production):
```bash
crontab -e
# Add this line:
* * * * * cd /var/www/jamexams && php artisan schedule:run >> /dev/null 2>&1
```

The scheduler runs `users:deactivate-expired` every night at midnight:
```php
// Kernel.php
$schedule->command('users:deactivate-expired')->dailyAt('00:00');
```

### 6.3 The Expiry Message

When a user's 30 days expire and they try to login or access the API, the system returns:

```json
{
  "status": "error",
  "message": "Lipa 1,000 Voda 0756527718 January\nTuma Ujumbe Malipo WhatsApp",
  "code": "ACCESS_EXPIRED"
}
```

The mobile app detects `code: ACCESS_EXPIRED` and shows this message prominently in the UI.

---

# PART 3: MOBILE DEVELOPMENT

---

## Chapter 7: Kotlin and Jetpack Compose Basics

### 7.1 Why Kotlin?

Kotlin is safer than Java. Compare:

```java
// Java - crashes if name is null
String upper = name.toUpperCase(); // NullPointerException!

// Kotlin - compiler forces you to handle null
val upper = name?.toUpperCase() ?: "UNKNOWN" // Safe
```

### 7.2 Jetpack Compose Fundamentals

Compose uses a declarative approach. You describe what UI should look like, not how to update it:

```kotlin
// Traditional Android (XML + Java) - imperative
textView.setText("Hello")
textView.setTextColor(Color.RED)
textView.setVisibility(View.VISIBLE)

// Jetpack Compose - declarative
Text(
    text  = "Hello",
    color = Color.Red,
)
```

### 7.3 State and Recomposition

Compose re-draws (recomposes) only when state changes:

```kotlin
@Composable
fun Counter() {
    // 'count' is state - Compose watches it
    var count by remember { mutableStateOf(0) }

    Column {
        Text("Count: $count")
        Button(onClick = { count++ }) {
            Text("Increment")
        }
    }
}
// When count changes → only this composable redraws, not the whole screen
```

### 7.4 MVVM Architecture in Practice

```
UI Layer (Composable)          ViewModel              Repository
      │                            │                      │
      │ collectAsStateWithLifecycle │                      │
      │ ◄──────────────────────── │                      │
      │                            │                      │
      │ viewModel.login(...)       │                      │
      │ ──────────────────────── ► │                      │
      │                            │ authRepository.login │
      │                            │ ──────────────────► │
      │                            │                      │ API call
      │                            │ Result.Success(user) │
      │                            │ ◄────────────────── │
      │                            │                      │
      │ uiState = LoginUiState.    │                      │
      │ Success(...)               │                      │
      │ ◄──────────────────────── │                      │
      │ (Navigate to home screen)  │                      │
```

### 7.5 StateFlow vs LiveData

JamExams uses `StateFlow` because:
- It's Kotlin-native (no Android dependency)
- It always has a current value
- It works well with Coroutines
- Easier to test

```kotlin
// In ViewModel
private val _uiState = MutableStateFlow<LoginUiState>(LoginUiState.Idle)
val uiState: StateFlow<LoginUiState> = _uiState.asStateFlow()

// In Composable
val uiState by viewModel.uiState.collectAsStateWithLifecycle()
```

---

## Chapter 8: Network Layer

### 8.1 Retrofit Setup

Retrofit converts API calls into Kotlin function calls:

```kotlin
// Define API interface
interface ApiService {
    @GET("exams")
    suspend fun getExams(): Response<ApiResponse<ExamsData>>
}

// Call it like a regular function
val response = apiService.getExams()
if (response.isSuccessful) {
    val exams = response.body()!!.data!!.exams
}
```

### 8.2 Token Authentication

The `authInterceptor` in `ApiClient.kt` automatically adds the Bearer token to every request:

```kotlin
val authInterceptor = Interceptor { chain ->
    val token = runBlocking { tokenDataStore.token.first() }
    val request = chain.request().newBuilder()
        .addHeader("Authorization", "Bearer $token")
        .build()
    chain.proceed(request)
}
```

**Analogy:** This is like a receptionist who stamps every letter you send with your membership card number, so the server always knows who you are.

### 8.3 Error Handling Pattern

```kotlin
sealed class Result<out T> {
    data class Success<T>(val data: T) : Result<T>()
    data class Error(val message: String, val code: String? = null) : Result<Nothing>()
    object Loading : Result<Nothing>()
}

// In Repository
override suspend fun getExams(...): Result<...> {
    return try {
        val response = api.getExams(...)
        if (response.isSuccessful) {
            Result.Success(response.body()!!.data!!)
        } else {
            Result.Error(response.body()?.message ?: "Unknown error")
        }
    } catch (e: Exception) {
        Result.Error(e.message ?: "Network error")
    }
}
```

---

## Chapter 9: Firebase Push Notifications

### 9.1 How FCM Works

```
Admin uploads exam
        │
        ▼
Laravel sends HTTP request to FCM servers
  POST https://fcm.googleapis.com/fcm/send
  {
    "registration_ids": ["token1", "token2", ...],
    "notification": { "title": "New Exam!", "body": "..." },
    "data": { "exam_id": "42" }
  }
        │
        ▼
FCM delivers to all Android devices
        │
        ▼
FirebaseMessagingService.onMessageReceived() called
        │
        ▼
App shows notification
        │
        ▼ (user taps)
MainActivity opens with exam_id in Intent
        │
        ▼
Navigate to exam detail screen
```

### 9.2 Getting FCM Token

```kotlin
// In LoginViewModel
val fcmToken = try {
    FirebaseMessaging.getInstance().token.await()
} catch (e: Exception) {
    null
}

// Send with login request
authRepository.login(email, password, fcmToken)
```

The backend stores this token in `users.fcm_token` and uses it when sending notifications.

---

# PART 4: SECURITY

---

## Chapter 10: Security Best Practices

### 10.1 Password Hashing

**Never store plain text passwords.** Laravel uses bcrypt by default:

```php
// Storing password
$user->password = Hash::make('userpassword123');
// Stores: $2y$12$randomsalt...hashedvalue

// Verifying password
Hash::check('userpassword123', $user->password); // true
Hash::check('wrongpassword', $user->password);   // false
```

**Analogy:** It's like putting a letter in a vault. You can verify the original letter fits the vault shape, but you can never extract the letter back.

### 10.2 Rate Limiting

Prevents brute-force attacks:

```php
// routes/api.php
Route::middleware('throttle:5,1')->group(function () {
    // Max 5 login attempts per minute
    Route::post('/auth/login', [AuthController::class, 'login']);
});
```

### 10.3 File Upload Validation

```php
// Only accept PDF files, max 50MB
$request->validate([
    'exam_file' => 'required|file|mimes:pdf|max:51200',
]);
```

Never trust the file extension alone — always validate MIME type.

### 10.4 Role-Based Authorization

```php
// Middleware in routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
});

// In controller method
public function activate(User $user) {
    $this->authorize('manage-users'); // Throws 403 if no permission
    ...
}
```

---

# PART 5: DEPLOYMENT

---

## Chapter 11: Deploying to Production

### 11.1 Server Requirements

Minimum server specs (Contabo VPS recommended):
- Ubuntu 22.04 LTS
- 2 vCPU
- 4GB RAM
- 50GB SSD
- Monthly: ~€4.50

### 11.2 Backend Deployment Steps

```bash
# 1. Connect to server
ssh root@your_server_ip

# 2. Install required software
apt update && apt upgrade -y
apt install php8.1 php8.1-pgsql php8.1-mbstring php8.1-xml php8.1-curl nginx postgresql

# 3. Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# 4. Upload project
cd /var/www
git clone https://github.com/yourusername/jamexams-backend.git jamexams
cd jamexams

# 5. Install dependencies
composer install --no-dev --optimize-autoloader

# 6. Configure .env
cp .env.example .env
php artisan key:generate
# Edit .env with production values

# 7. Setup database and migrate
php artisan migrate --force
php artisan db:seed --force

# 8. Set permissions
chown -R www-data:www-data /var/www/jamexams
chmod -R 755 /var/www/jamexams/storage

# 9. Create storage symlink
php artisan storage:link

# 10. Configure Nginx
nano /etc/nginx/sites-available/jamexams
```

Nginx configuration:
```nginx
server {
    listen 80;
    server_name chasepaper.duckdns.org;
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

    client_max_body_size 60M;
}
```

```bash
ln -s /etc/nginx/sites-available/jamexams /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx

# 11. Setup cron for scheduler
crontab -e
# Add: * * * * * cd /var/www/jamexams && php artisan schedule:run >> /dev/null 2>&1
```

### 11.3 Generating the Android APK

In Android Studio:
1. **Build → Generate Signed Bundle/APK**
2. Choose **APK**
3. Create a new keystore (save the keystore file safely!)
4. Fill in keystore details
5. Choose **release** build variant
6. Click **Finish**

APK location: `mobile/app/release/app-release.apk`

---

# PART 6: EXERCISES AND PROJECTS

---

## Chapter 12: Practice Exercises

### Exercise 1: Add Exam Type Filter
Add a new filter chip row to `ExamListScreen.kt` that filters exams by type (PAST_PAPER, MOCK, MIDTERM).

**Hint:** Use `LazyRow` with `FilterChip` composables. Call `viewModel.applyFilter()` when a chip is selected.

### Exercise 2: Offline Caching with Room
Implement Room database to cache subjects and exams locally. Show cached data when the device is offline.

**Steps:**
1. Add Room dependency to `build.gradle`
2. Create `SubjectEntity` and `ExamEntity` data classes
3. Create `JamExamsDatabase`
4. Update `ExamRepositoryImpl` to save API results to Room
5. Read from Room when API fails

### Exercise 3: Exam Search Screen
Build a search screen with a text field that searches exams by title in real-time (with debounce — wait 500ms after typing stops before calling API).

**Hint:** Use `Flow.debounce(500)` on the search query.

### Exercise 4: Admin Notification Panel
Add a "Send Notification" form to the admin web panel where admins can type a title and message, then send to all active users.

### Exercise 5: Download History
Add a "Downloads" tab that shows all PDFs the user has downloaded, with the ability to open them again without re-downloading.

---

# APPENDIX

## A. Git Workflow

```bash
# Feature branch workflow
git checkout -b feature/exam-search
# Make changes...
git add .
git commit -m "feat: add exam search functionality"
git push origin feature/exam-search
# Create Pull Request on GitHub
```

**Commit message conventions:**
- `feat:` — new feature
- `fix:` — bug fix
- `docs:` — documentation
- `refactor:` — code restructuring
- `test:` — adding tests

## B. Common Errors and Solutions

| Error | Cause | Solution |
|-------|-------|----------|
| `SQLSTATE[HY000]` | Wrong DB credentials | Check .env file |
| `UnauthorizedException 401` | Token missing/expired | Re-login in app |
| `419 CSRF token mismatch` | Web form missing CSRF | Add @csrf to blade form |
| `403 ACCESS_EXPIRED` | 30 days passed | Admin must re-activate |
| `Network error` (mobile) | Wrong BASE_URL | Update BASE_URL in build.gradle |
| `FileNotFoundException` | Storage not linked | Run `php artisan storage:link` |

## C. API Response Codes Reference

| HTTP Code | Meaning | When Used |
|-----------|---------|-----------|
| 200 | OK | Successful request |
| 401 | Unauthorized | No/invalid token |
| 403 | Forbidden | Active but no permission / expired |
| 404 | Not Found | Resource doesn't exist |
| 422 | Validation Error | Invalid request data |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Server Error | Backend crash |

---

*JamExams — Built with ❤️ by Jacob Godwin Mwansambe*
*"The best exam prep system is one you built yourself."*
