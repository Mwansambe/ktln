# Chapter 16: RESTful API Development and Integrations

## Introduction
Paperchase already has API routes. This chapter organizes them into a maintainable RESTful design with proper versioning, auth, and response contracts.

## Learning Objectives
By the end of this chapter, you can:
- Design predictable REST endpoints
- Secure API access with token auth
- Return consistent JSON contracts
- Add rate limiting and API documentation

## 1. Core API Groups in Paperchase
Current groups in `routes/api.php`:
- `auth/*`
- `users/*`
- `subjects/*`
- `exams/*`
- `statistics/*`

## 2. Version Your API
Use prefixing:

```php
Route::prefix('v1')->group(function () {
    // existing routes
});
```

Benefits:
- Safe future changes
- Backward compatibility for mobile clients

## 3. Response Contract
Example success response:

```json
{
  "success": true,
  "message": "Exams retrieved successfully",
  "data": [ ... ]
}
```

Validation error response:

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "title": ["The title field is required."]
  }
}
```

## 4. Auth Endpoints
Required auth endpoints:
- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `POST /api/v1/auth/refresh`
- `GET /api/v1/auth/me`
- `POST /api/v1/auth/logout`

Use middleware: `auth:api` for protected endpoints.

## 5. Resource Classes
Use API Resources for clean output:

```bash
php artisan make:resource ExamResource
```

```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'year' => $this->year,
        'type' => $this->type,
        'subject' => [
            'id' => $this->subject?->id,
            'name' => $this->subject?->name,
        ],
    ];
}
```

## 6. Rate Limiting
Protect login and heavy endpoints:

```php
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
});
```

## 7. API Testing
Use feature tests for API contracts:
- authentication required behavior
- validation failure behavior
- response structure
- pagination metadata

## 8. Advanced Features Not in Core Paperchase
Recommended extensions:
- Saved searches endpoint
- Notification preferences API
- Webhook event delivery for new exam uploads
- API keys for partner institutions

## Hands-On Exercise
1. Wrap one existing endpoint with API Resource.
2. Add `/api/v1` route prefix.
3. Add rate limit middleware to auth routes.
4. Write tests for `401`, `422`, and `200` responses.

## Challenge Extension
Generate OpenAPI documentation (Swagger) and publish an internal API portal for frontend and mobile teams.

## Summary
You now have a stable, integrator-friendly API layer. Next, you will package the app for production deployment and long-term maintenance.
