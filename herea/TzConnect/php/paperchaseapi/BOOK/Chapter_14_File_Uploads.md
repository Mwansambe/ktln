# Chapter 14: File Upload and Document Management

## Introduction
Paperchase depends on reliable document management for exam papers and marking schemes. This chapter covers secure uploads, storage choices, and lifecycle management.

## Learning Objectives
By the end of this chapter, you can:
- Handle PDF uploads safely
- Store and serve files through Laravel storage
- Replace and delete files without orphaned records
- Enforce upload constraints

## 1. Storage Configuration
Use Laravel filesystem (`config/filesystems.php`):
- `local` for development
- `public` for public URL access
- `s3` for scalable production storage

Create symbolic link:

```bash
php artisan storage:link
```

## 2. Upload Validation

```php
$data = $request->validate([
    'exam_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
    'marking_scheme_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
]);
```

Why:
- Restrict file type to PDF
- Limit size (10MB here)
- Avoid malicious upload vectors

## 3. Save Uploaded Files

```php
$path = $request->file('exam_file')->store('exams', 'public');

$exam = Exam::create([
    'title' => $request->title,
    'file_path' => $path,
]);
```

Store only relative paths in DB.

## 4. Download Files Securely

```php
return Storage::disk('public')->download($exam->file_path, $exam->title . '.pdf');
```

Add authorization checks before download.

## 5. Replace File on Update

```php
if ($request->hasFile('exam_file')) {
    if ($exam->file_path && Storage::disk('public')->exists($exam->file_path)) {
        Storage::disk('public')->delete($exam->file_path);
    }

    $exam->file_path = $request->file('exam_file')->store('exams', 'public');
}

$exam->save();
```

## 6. Delete Files When Record Is Deleted
Avoid orphaned files:

```php
if ($exam->file_path) {
    Storage::disk('public')->delete($exam->file_path);
}
$exam->delete();
```

## 7. Performance and Cost Tips
- Compress PDFs before upload
- Use queue jobs for heavy processing
- Move production files to S3-compatible storage
- Add cache headers for frequent downloads

## 8. Security Checklist
- Validate MIME and extension
- Never trust client-provided file names
- Use private storage for sensitive documents
- Log upload and delete actions

## Hands-On Exercise
1. Add upload progress indicator in Blade form.
2. Add preview metadata (size, upload date) in exam detail page.
3. Add API endpoint to upload marking scheme.
4. Add cleanup command for orphaned files.

## Challenge Extension
Implement virus scanning on upload using a queue worker and quarantine failed files.

## Summary
You now have production-grade file handling. Next, you will improve discoverability with advanced search, filtering, sorting, and pagination.
