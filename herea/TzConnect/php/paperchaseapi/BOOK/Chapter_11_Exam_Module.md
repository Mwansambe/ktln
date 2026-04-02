# Chapter 11: Building the Exam Module

## Introduction
The exam module is the heart of Paperchase. It handles exam creation, listing, filtering, details, downloads, and metadata updates.

## Learning Objectives
By the end of this chapter, you can:
- Implement full exam CRUD
- Validate exam input correctly
- Track exam downloads
- Build reusable listing and detail pages

## 1. Define Routes
Web routes example (`routes/web.php`):

```php
Route::prefix('exams')->name('exams.')->group(function () {
    Route::get('/', [WebExamController::class, 'index'])->name('index');
    Route::get('/new', [WebExamController::class, 'create'])->name('create');
    Route::post('/', [WebExamController::class, 'store'])->name('store');
    Route::get('/{exam}', [WebExamController::class, 'show'])->name('show');
    Route::get('/{exam}/edit', [WebExamController::class, 'edit'])->name('edit');
    Route::put('/{exam}', [WebExamController::class, 'update'])->name('update');
    Route::delete('/{exam}', [WebExamController::class, 'destroy'])->name('destroy');
});
```

## 2. Build the Index Action

```php
public function index(Request $request)
{
    $exams = Exam::with('subject')
        ->when($request->search, fn ($q, $search) =>
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
        )
        ->when($request->year, fn ($q, $year) => $q->where('year', $year))
        ->when($request->subject_id, fn ($q, $id) => $q->where('subject_id', $id))
        ->latest()
        ->paginate(15)
        ->withQueryString();

    return view('exams.index', compact('exams'));
}
```

## 3. Create and Update Validation

```php
$data = $request->validate([
    'subject_id' => ['required', 'exists:subjects,id'],
    'title' => ['required', 'string', 'max:255'],
    'year' => ['required', 'integer', 'between:1990,' . now()->year],
    'type' => ['required', 'in:MOCK,NATIONAL,SCHOOL'],
    'code' => ['nullable', 'string', 'max:50'],
]);
```

## 4. Exam Detail Page
Show:
- Title, subject, type, year
- Download count
- Bookmark button
- Related exams (same subject or year)

Example related query:

```php
$similar = Exam::where('subject_id', $exam->subject_id)
    ->whereKeyNot($exam->id)
    ->latest()
    ->limit(5)
    ->get();
```

## 5. Download Tracking
For each download:
- Save a row in `downloads`
- Increment exam counter if needed
- Return file response

```php
Download::create([
    'user_id' => auth()->id(),
    'exam_id' => $exam->id,
    'downloaded_at' => now(),
]);
```

## 6. Admin Controls
Restrict create, edit, delete to `EDITOR` and `ADMIN`.
Allow users with `USER` role to browse, view, download, bookmark.

## 7. Testing Checklist
- Can list exams with filters
- Can create exam with valid payload
- Invalid year returns validation error
- Delete action blocked for basic users
- Download action writes to `downloads` table

## Hands-On Exercise
1. Add a `featured` boolean field and filter on homepage.
2. Add “most downloaded this month” section.
3. Add an API endpoint `/api/exams/featured`.
4. Write one feature test for download tracking.

## Challenge Extension
Add exam versioning:
- Keep history when an exam PDF is replaced
- Show previous versions in admin detail page

## Summary
You now have a complete exam module with CRUD, filtering, and usage tracking. Next, you will build and optimize the subject/category module.
