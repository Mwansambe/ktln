# Chapter 13: User Management and Roles

## Introduction
A production system must manage users securely and predictably. In this chapter, you will build user administration features for Paperchase.

## Learning Objectives
By the end of this chapter, you can:
- List, create, edit, and deactivate users
- Apply role-based restrictions
- Protect sensitive operations with authorization checks
- Track user activity indicators

## 1. User Roles
Paperchase role model:
- `USER`: browse/download/bookmark
- `EDITOR`: manage exam and subject content
- `ADMIN`: full access including users and system controls

## 2. User Management Routes

```php
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [WebUserController::class, 'index'])->name('index');
    Route::post('/', [WebUserController::class, 'store'])->name('store');
    Route::put('/{user}', [WebUserController::class, 'update'])->name('update');
    Route::delete('/{user}', [WebUserController::class, 'destroy'])->name('destroy');
});
```

Wrap in admin-only middleware/policy.

## 3. Create User Action

```php
$data = $request->validate([
    'name' => ['required', 'string', 'max:120'],
    'email' => ['required', 'email', 'unique:users,email'],
    'password' => ['required', 'min:8', 'confirmed'],
    'role' => ['required', 'in:USER,EDITOR,ADMIN'],
]);

$user = User::create([
    ...$data,
    'password' => bcrypt($data['password']),
]);
```

## 4. Toggle User Active State
Soft disable accounts instead of hard delete.

```php
$user->update(['is_active' => ! $user->is_active]);
```

In login flow, block inactive users.

## 5. Protect Critical Operations
- Prevent non-admin users from editing roles
- Prevent admin from deleting their own active account accidentally
- Log role change events

Example guard:

```php
if (auth()->id() === $user->id) {
    return back()->withErrors(['user' => 'You cannot delete your own account.']);
}
```

## 6. User Statistics for Dashboard
Useful metrics:
- Total users
- Active users
- New users this month
- Top downloaders

```php
$stats = [
    'total' => User::count(),
    'active' => User::where('is_active', true)->count(),
    'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
];
```

## 7. Audit and Logs
For accountability, log:
- user created
- role changed
- account deactivated

Use Laravel logs now; later move to dedicated audit table.

## Hands-On Exercise
1. Add user search by name/email on admin page.
2. Add a role filter dropdown.
3. Show last login time in user list.
4. Add API endpoint for user statistics.

## Challenge Extension
Implement invitation-based onboarding:
- Admin invites via email
- User sets password via secure link
- Invitation expires after 24 hours

## Summary
You now have reliable user governance in Paperchase. Next, you will implement robust file upload and storage workflows for exam documents.
