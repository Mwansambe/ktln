# Chapter 12: Building the Subject and Category Module

## Introduction
Subjects organize exams and improve search clarity. This chapter shows how to build category management with stable relationships, counts, and user-friendly pages.

## Learning Objectives
By the end of this chapter, you can:
- Implement subject CRUD
- Maintain exam counts per subject
- Prevent invalid deletes
- Build clear category navigation

## 1. Subject Data Model
Common fields:
- `name`
- `code`
- `description`
- `color`
- `is_active`
- `exam_count` (optional denormalized field)

## 2. Subject Routes and Controller
Paperchase uses `categories` URLs in web routes mapped to `Web\SubjectController`.

```php
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [WebSubjectController::class, 'index'])->name('index');
    Route::post('/', [WebSubjectController::class, 'store'])->name('store');
    Route::put('/{subject}', [WebSubjectController::class, 'update'])->name('update');
    Route::delete('/{subject}', [WebSubjectController::class, 'destroy'])->name('destroy');
});
```

## 3. Prevent Duplicate Subject Names

```php
'name' => ['required', 'string', 'max:150', 'unique:subjects,name,' . $subject->id],
'code' => ['required', 'string', 'max:20', 'unique:subjects,code,' . $subject->id],
```

## 4. Safe Deletion Strategy
Do not delete subject if exams exist.

```php
if ($subject->exams()->exists()) {
    return back()->withErrors(['subject' => 'Cannot delete subject with existing exams.']);
}

$subject->delete();
```

Alternative: soft delete subjects and hide inactive ones from users.

## 5. Keep Subject Exam Counts Accurate
Option A: compute dynamically with `withCount('exams')`
Option B: persist count and recalculate after exam create/delete

```php
$subjects = Subject::withCount('exams')->orderBy('name')->get();
```

## 6. Subject Pages UX Tips
- Show number of exams clearly
- Show color badge for easy identification
- Include quick links: “View exams in this subject”
- Add empty-state message if subject has no exams

## 7. API Endpoints to Include
- `GET /api/subjects`
- `GET /api/subjects/popular`
- `GET /api/subjects/{id}`
- `POST /api/subjects` (admin/editor)
- `PUT /api/subjects/{id}` (admin/editor)
- `DELETE /api/subjects/{id}` (admin only)

## Hands-On Exercise
1. Add a “popular subjects” widget sorted by exam count.
2. Add subject filter dropdown to exam list.
3. Add endpoint that returns subjects with zero exams.
4. Add form validation message for duplicate codes.

## Challenge Extension
Add subject aliases (for example, “Math”, “Mathematics”) and make search resolve aliases to one canonical subject.

## Summary
You now have a stable category layer that keeps exams discoverable and data consistent. Next, you will build user management for admin operations and account governance.
