# Chapter 8: Creating Controllers and Business Logic

## Introduction
Controllers receive requests, validate input, call models, and return responses. In Paperchase, you have both web controllers (Blade views) and API controllers (JSON). This chapter teaches you how to structure both clearly.

## Learning Objectives
By the end of this chapter, you can:
- Create web and API controllers
- Build CRUD actions with validation
- Return consistent responses
- Keep controllers maintainable

## 1. Controller Types in Paperchase

Web controllers (HTML views):
- `app/Http/Controllers/Web/ExamController.php`
- `app/Http/Controllers/Web/SubjectController.php`
- `app/Http/Controllers/Web/UserController.php`

API controllers (JSON):
- `app/Http/Controllers/ExamController.php`
- `app/Http/Controllers/SubjectController.php`
- `app/Http/Controllers/UserController.php`
- `app/Http/Controllers/AuthController.php`

Rule of thumb:
- Web controller returns `view(...)` or redirects
- API controller returns JSON + HTTP status codes

## 2. Build a RESTful Exam API Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $exams = Exam::with('subject')
            ->when($request->subject_id, fn ($q, $id) => $q->where('subject_id', $id))
            ->when($request->year, fn ($q, $year) => $q->where('year', $year))
            ->latest()
            ->paginate(15);

        return response()->json($exams);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1990'],
            'type' => ['required', 'in:MOCK,NATIONAL,SCHOOL'],
        ]);

        $exam = Exam::create($data);

        return response()->json($exam, 201);
    }
}
```

## 3. Web Controller Example

```php
public function store(Request $request)
{
    $data = $request->validate([
        'subject_id' => ['required', 'exists:subjects,id'],
        'title' => ['required', 'string', 'max:255'],
        'year' => ['required', 'integer'],
    ]);

    Exam::create($data);

    return redirect()
        ->route('exams.index')
        ->with('success', 'Exam created successfully.');
}
```

Difference:
- API returns JSON
- Web returns redirect + flash message

## 4. Use Form Request Classes
Move validation out of controllers.

Create request:
```bash
php artisan make:request StoreExamRequest
```

Use it:

```php
public function store(StoreExamRequest $request)
{
    $exam = Exam::create($request->validated());
    return response()->json($exam, 201);
}
```

Benefits:
- Cleaner controllers
- Reusable validation
- Better testability

## 5. Authorization in Controllers

```php
public function destroy(Exam $exam)
{
    $this->authorize('delete', $exam);

    $exam->delete();

    return response()->json(['message' => 'Exam deleted']);
}
```

For beginners, start with role checks; later move to Policies/Gates.

## 6. Keep Controllers Thin
Anti-pattern:
- Big query logic, upload logic, and statistics logic in one method

Better:
- Validation in Form Request
- Query scopes in models
- Reusable logic in service classes

## 7. Consistent API Responses
Paperchase has `app/Http/Traits/ApiResponseTrait.php`. Standardize all responses:

```php
return $this->successResponse($data, 'Exams retrieved successfully');
```

Keep structure predictable:
- `success`
- `message`
- `data`
- `errors` (only when validation fails)

## 8. Controller Testing Basics
Feature test example:

```php
public function test_authenticated_user_can_create_exam(): void
{
    $user = User::factory()->create();
    $subject = Subject::factory()->create();

    $response = $this->actingAs($user, 'api')->postJson('/api/exams', [
        'subject_id' => $subject->id,
        'title' => 'Physics Paper 1',
        'year' => 2025,
        'type' => 'MOCK',
    ]);

    $response->assertStatus(201);
}
```

## Hands-On Exercise
1. Add `update` and `destroy` methods to your exam API controller.
2. Add route model binding where possible.
3. Add one validation rule that prevents future years.
4. Write one feature test for invalid payload.

## Challenge Extension
Create an `ExamService` class and move search/filter logic from controller to service. Compare readability before and after.

## Summary
You can now design clear, testable controllers for both Blade pages and API clients. Next, you will build reusable Blade layouts and components for the web frontend.
