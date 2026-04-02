# Chapter 10: Authentication and Authorization

## Introduction
Security is essential for Paperchase. In this chapter, you will implement user registration/login, role-based access control, and secure route protection.

## Learning Objectives
By the end of this chapter, you can:
- Configure web and API authentication
- Protect routes and controller actions
- Implement role-based authorization
- Add password update/reset flows

## 1. Authentication in Paperchase
Paperchase uses two auth styles:
- Web (session-based) for Blade pages
- API (`auth:api`) for JSON clients

Relevant files:
- `routes/web.php`
- `routes/api.php`
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Controllers/AuthController.php`

## 2. Web Login Flow

```php
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
```

Best practices:
- Regenerate session after login
- Invalidate session on logout
- Use CSRF protection for forms

## 3. API Authentication Flow

```php
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});
```

On successful login, return token + user details.

## 4. Role-Based Authorization
Paperchase roles: `USER`, `EDITOR`, `ADMIN`.

Simple controller-level check:

```php
if ($request->user()->role !== 'ADMIN') {
    abort(403, 'Unauthorized action.');
}
```

Better long-term approach:
- Use Policies for model actions
- Use Gates for feature-level permissions

## 5. Protect Routes by Role

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', ...);

    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', WebUserController::class);
    });
});
```

## 6. Password Management
Minimum rules:
- min length (8+)
- mixed case
- numbers
- symbols (optional but recommended)

Update password example:

```php
$request->validate([
    'current_password' => ['required', 'current_password'],
    'password' => ['required', 'confirmed', 'min:8'],
]);

$request->user()->update([
    'password' => bcrypt($request->password),
]);
```

## 7. Email Verification (Optional but Recommended)
Use Laravel’s verification middleware to stop unverified users from accessing critical features.

## 8. Common Security Mistakes
- Storing plain text passwords
- Trusting role sent from frontend
- Missing middleware on protected routes
- Not rate-limiting login endpoints

Add rate limiting to login route to reduce brute-force risk.

## Hands-On Exercise
1. Protect user management routes so only admins can access.
2. Add `EDITOR` permissions for exam create/update but not user deletion.
3. Add API endpoint `/api/auth/me` and test with token.
4. Add password update form in settings page.

## Challenge Extension
Implement two-factor authentication (2FA) for admin accounts and enforce it for sensitive actions (delete user, change role, delete exam).

## Summary
You now control who can sign in and what each user can do. Next, you will build the full exam module using this security foundation.
