<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Services\ExamService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Admin ExamController
 * Manages exam upload, editing, deletion for admin/editor roles.
 */
class ExamController extends Controller
{
    public function __construct(
        private ExamService $examService,
        private NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $query = Exam::with('subject', 'uploader')->latest();

        if ($request->has('subject_id')) {
            $query->bySubject($request->subject_id);
        }
        if ($request->has('search')) {
            $query->where('title', 'ilike', '%' . $request->search . '%')
                  ->orWhere('code', 'ilike', '%' . $request->search . '%');
        }
        if ($request->has('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        $exams = $query->paginate(20);
        $subjects = Subject::active()->get();
        $stats = $this->examService->getStats();

        return view('admin.exams.index', compact('exams', 'subjects', 'stats'));
    }

    public function create()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        return view('admin.exams.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'             => 'required|string|max:10',
            'title'            => 'required|string|max:200',
            'subject_id'       => 'required|exists:subjects,id',
            'exam_type'        => 'required|in:PAST_PAPER,MOCK,MIDTERM,FINAL,REVISION',
            'class_level'      => 'nullable|string|max:20',
            'year'             => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'description'      => 'nullable|string|max:500',
            'exam_file'        => 'required|file|mimes:pdf|max:51200', // 50MB
            'marking_scheme'   => 'nullable|file|mimes:pdf|max:51200',
            'is_featured'      => 'boolean',
            'is_published'     => 'boolean',
        ]);

        $exam = $this->examService->createExam($request);

        // Send FCM notification to active users
        if ($exam->is_published) {
            $this->notificationService->sendExamUploadedNotification($exam);
        }

        return redirect()->route('admin.exams.index')
                         ->with('success', 'Exam created successfully!');
    }

    public function show(Exam $exam)
    {
        $exam->load('subject', 'uploader');
        return view('admin.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $subjects = Subject::active()->orderBy('name')->get();
        return view('admin.exams.edit', compact('exam', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'code'        => 'required|string|max:10',
            'title'       => 'required|string|max:200',
            'subject_id'  => 'required|exists:subjects,id',
            'exam_type'   => 'required|in:PAST_PAPER,MOCK,MIDTERM,FINAL,REVISION',
            'class_level' => 'nullable|string|max:20',
            'year'        => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
            'description' => 'nullable|string|max:500',
            'exam_file'   => 'nullable|file|mimes:pdf|max:51200',
            'marking_scheme' => 'nullable|file|mimes:pdf|max:51200',
        ]);

        $this->examService->updateExam($exam, $request);

        return redirect()->route('admin.exams.index')
                         ->with('success', 'Exam updated successfully!');
    }

    public function destroy(Exam $exam)
    {
        $this->examService->deleteExam($exam);
        return redirect()->route('admin.exams.index')
                         ->with('success', 'Exam deleted.');
    }

    public function download(Exam $exam)
    {
        if (!Storage::exists($exam->exam_file_path)) {
            abort(404, 'File not found');
        }
        return Storage::download($exam->exam_file_path, $exam->title . '.pdf');
    }
}
