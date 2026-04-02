# Chapter 15: Advanced Search, Filtering, and Pagination

## Introduction
As Paperchase grows, users must quickly find the right exam. This chapter focuses on efficient search and filtering strategies with good performance.

## Learning Objectives
By the end of this chapter, you can:
- Build multi-criteria filters
- Add sorting and pagination
- Avoid expensive query patterns
- Improve UX with preserved filter state

## 1. Search Requirements for Paperchase
Users should filter by:
- keyword (`title`, `code`)
- subject
- year
- type
- popularity (downloads)

## 2. Dynamic Query Builder Pattern

```php
$query = Exam::query()->with('subject');

$query->when($request->search, function ($q, $term) {
    $q->where(function ($inner) use ($term) {
        $inner->where('title', 'like', "%{$term}%")
              ->orWhere('code', 'like', "%{$term}%");
    });
});

$query->when($request->subject_id, fn ($q, $id) => $q->where('subject_id', $id));
$query->when($request->year, fn ($q, $year) => $q->where('year', $year));
$query->when($request->type, fn ($q, $type) => $q->where('type', $type));

$exams = $query->latest()->paginate(15)->withQueryString();
```

## 3. Sorting Options
Support controlled sorts only:
- newest
- oldest
- most_downloaded

```php
$sort = $request->get('sort', 'newest');

match ($sort) {
    'oldest' => $query->oldest(),
    'most_downloaded' => $query->orderByDesc('download_count'),
    default => $query->latest(),
};
```

## 4. Pagination UX
Always preserve filter params:

```php
$exams = $query->paginate(15)->withQueryString();
```

In Blade:

```blade
{{ $exams->links() }}
```

## 5. Database Performance
Add indexes for common filters:
- `subject_id`
- `year`
- `type`
- `created_at`

Example migration snippet:

```php
$table->index(['subject_id', 'year']);
$table->index('type');
```

## 6. API Search Endpoint
`POST /api/exams/search` can accept complex payloads and return paginated results.

Response should include:
- filtered items
- meta (page, total, per_page)
- active filters summary

## 7. Common Mistakes
- Building raw SQL strings manually
- Not indexing filter columns
- Forgetting eager loading
- Returning huge unpaginated lists

## Hands-On Exercise
1. Add “year range” filter (`from_year`, `to_year`).
2. Add endpoint for distinct years (`/api/exams/years/distinct`).
3. Add “clear filters” button in Blade UI.
4. Benchmark query before and after adding indexes.

## Challenge Extension
Integrate full-text search (PostgreSQL `tsvector`) for better relevance ranking across title, subject, and code.

## Summary
You now provide fast and flexible exam discovery. Next, you will expose a clean RESTful API for external applications and integrations.
