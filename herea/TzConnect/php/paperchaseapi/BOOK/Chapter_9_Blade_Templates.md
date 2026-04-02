# Chapter 9: Building Views with Blade Templates

## Introduction
Blade is Laravel’s templating engine. It helps you build clean server-rendered pages with reusable layouts, components, and directives.

## Learning Objectives
By the end of this chapter, you can:
- Create a shared layout
- Build reusable Blade components
- Render form errors and flash messages
- Connect forms to routes safely with CSRF protection

## 1. Blade Structure in Paperchase
Key files:
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/auth.blade.php`
- `resources/views/components/navbar.blade.php`
- `resources/views/components/footer.blade.php`
- `resources/views/exams/*.blade.php`
- `resources/views/categories/*.blade.php`

## 2. Create a Reusable Layout

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Paperchase')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <x-navbar />

    <main class="max-w-7xl mx-auto p-4">
        @if (session('success'))
            <div class="mb-4 rounded bg-green-100 p-3 text-green-800">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>

    <x-footer />
</body>
</html>
```

## 3. Extend Layout in Pages

```blade
@extends('layouts.app')

@section('title', 'All Exams')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Exams</h1>

    @foreach ($exams as $exam)
        <div class="mb-3 rounded border p-3">
            <a href="{{ route('exams.show', $exam) }}" class="font-semibold">{{ $exam->title }}</a>
            <p class="text-sm text-gray-600">{{ $exam->subject->name }} - {{ $exam->year }}</p>
        </div>
    @endforeach

    {{ $exams->links() }}
@endsection
```

## 4. Forms and Validation Errors

```blade
<form method="POST" action="{{ route('exams.store') }}" enctype="multipart/form-data">
    @csrf

    <label class="block mb-1">Title</label>
    <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded p-2">
    @error('title')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror

    <button class="mt-3 rounded bg-blue-600 px-4 py-2 text-white">Save</button>
</form>
```

## 5. Useful Blade Directives
- `@auth` / `@guest`
- `@can`
- `@csrf`
- `@method('PUT')`
- `@error('field')`
- `@foreach`, `@forelse`

Example:

```blade
@can('manage-exams')
    <a href="{{ route('exams.create') }}" class="btn">Upload Exam</a>
@endcan
```

## 6. Shared Partials and Components
When repeating markup (cards, filters, alerts), extract to components.

```blade
{{-- resources/views/components/exam-card.blade.php --}}
<div class="rounded border p-4">
    <h3 class="font-semibold">{{ $exam->title }}</h3>
    <p class="text-sm">{{ $exam->subject->name }} - {{ $exam->year }}</p>
</div>
```

Use it:

```blade
<x-exam-card :exam="$exam" />
```

## 7. Frontend Best Practices
- Keep business logic out of Blade templates
- Escape output with `{{ }}` by default
- Keep forms simple and server-validated
- Use pagination for large lists

## Hands-On Exercise
1. Create a reusable alert component for success and error messages.
2. Convert one repeated exam block into a component.
3. Add search/filter form to `exams/index.blade.php`.
4. Preserve filter values using `old()` and query params.

## Challenge Extension
Add a dark mode toggle stored in session. Show how layout styling changes based on a selected theme.

## Summary
You can now build clean, reusable Blade interfaces. In the next chapter, you will implement authentication and authorization so only valid users can access protected features.
