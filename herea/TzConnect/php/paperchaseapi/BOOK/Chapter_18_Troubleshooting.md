# Chapter 18: Troubleshooting and Debugging Laravel Applications

## Introduction
Every developer faces bugs. The difference is how quickly and systematically they are solved. This chapter gives you a debugging workflow for Paperchase.

## Learning Objectives
By the end of this chapter, you can:
- Diagnose common Laravel errors
- Use logs, exceptions, and tests effectively
- Debug database, auth, and file upload issues
- Build a repeatable troubleshooting process

## 1. A Practical Debugging Workflow
Use this sequence:
1. Reproduce the issue consistently
2. Read exact error message and stack trace
3. Check logs (`storage/logs/laravel.log`)
4. Isolate the failing layer (route, controller, model, DB, view)
5. Write or run a test for the failing behavior
6. Fix, retest, and prevent regression

## 2. Common Issues in Paperchase

### 2.1 Route Not Found
Symptoms:
- 404 on known page or API endpoint

Checks:
- Route exists in `routes/web.php` or `routes/api.php`
- Method is correct (`GET`, `POST`, etc.)
- Clear route cache:

```bash
php artisan route:clear
php artisan route:list
```

### 2.2 Database Connection Errors
Symptoms:
- `SQLSTATE[08006]` or authentication failures

Checks:
- `.env` DB settings
- PostgreSQL running
- user credentials
- config cache stale

Fix:

```bash
php artisan config:clear
php artisan cache:clear
```

### 2.3 Mass Assignment Exception
Symptoms:
- `Add [field] to fillable property`

Fix:
- Add fields to model `$fillable`
- or use explicit property assignment

### 2.4 File Upload Fails
Symptoms:
- validation rejects file
- download returns 404

Checks:
- form has `enctype="multipart/form-data"`
- validation rules accept PDF + size
- `storage:link` exists
- file path stored correctly

### 2.5 Unauthorized (401/403)
Symptoms:
- valid user cannot access route

Checks:
- `auth`/`auth:api` middleware
- token/session validity
- role/permission checks
- guard mismatch in tests

## 3. Logging Strategy
Use structured logs with context:

```php
Log::error('Exam upload failed', [
    'user_id' => auth()->id(),
    'exam_id' => $exam->id ?? null,
    'error' => $e->getMessage(),
]);
```

Do not log secrets (passwords, raw tokens).

## 4. Debugging Tools
- `php artisan tinker` for quick query checks
- PHPUnit feature tests for regressions
- Laravel Telescope (dev only)
- Browser network tab for API payload/response checks

## 5. Production Incident Checklist
When issue appears in production:
1. Confirm user impact and severity
2. Capture exact timestamp and endpoint
3. Inspect logs around failure window
4. Apply minimal safe fix
5. Deploy hotfix with rollback plan
6. Write postmortem and add test coverage

## 6. Preventing Recurring Bugs
- Validate all external input
- Add feature tests for critical flows
- Use typed casts in models
- Keep controllers thin and predictable
- Review logs weekly

## Final Hands-On Project
Complete this end-to-end practice:
1. Break a validation rule intentionally and observe error behavior.
2. Fix it and write a feature test.
3. Simulate bad DB credentials and recover.
4. Simulate missing storage symlink and restore file downloads.

## Book Completion Outcomes
You can now:
- Build Paperchase from zero to production
- Extend modules with new features confidently
- Secure users, files, and API endpoints
- Deploy and maintain Laravel applications professionally
- Troubleshoot issues using a disciplined method

## Next Steps
1. Add automated CI tests for all major routes.
2. Implement one advanced extension (recommendation engine, notifications, or 2FA).
3. Publish your first production release with monitoring enabled.

You have completed **SHOW AFRICA**. Continue iterating on Paperchase and treat each new feature as a chance to improve architecture, testing, and user value.
