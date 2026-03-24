<?php

namespace App\Services;

use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * ExamService
 * Business logic for exam management: create, update, delete, stats.
 */
class ExamService
{
    /**
     * Create a new exam with file uploads.
     */
    public function createExam(Request $request): Exam
    {
        // Upload exam PDF
        $examFile = $request->file('exam_file');
        $examPath = $examFile->store('exams/papers', 'public');
        $examSize = $examFile->getSize();

        // Upload marking scheme if provided
        $markingPath = null;
        $markingSize = 0;
        if ($request->hasFile('marking_scheme')) {
            $markingFile = $request->file('marking_scheme');
            $markingPath = $markingFile->store('exams/marking-schemes', 'public');
            $markingSize = $markingFile->getSize();
        }

        return Exam::create([
            'code'                 => $request->code,
            'title'                => $request->title,
            'description'          => $request->description,
            'subject_id'           => $request->subject_id,
            'exam_type'            => $request->exam_type,
            'class_level'          => $request->class_level,
            'year'                 => $request->year,
            'exam_file_path'       => $examPath,
            'exam_file_size'       => $examSize,
            'marking_scheme_path'  => $markingPath,
            'marking_scheme_size'  => $markingSize,
            'is_featured'          => $request->boolean('is_featured'),
            'is_published'         => $request->boolean('is_published', true),
            'uploaded_by'          => auth()->id(),
        ]);
    }

    /**
     * Update exam details, optionally replacing files.
     */
    public function updateExam(Exam $exam, Request $request): Exam
    {
        $data = $request->only(['code', 'title', 'description', 'subject_id', 'exam_type', 'class_level', 'year', 'is_featured', 'is_published']);

        // Replace exam file if new one uploaded
        if ($request->hasFile('exam_file')) {
            Storage::disk('public')->delete($exam->exam_file_path);
            $file = $request->file('exam_file');
            $data['exam_file_path'] = $file->store('exams/papers', 'public');
            $data['exam_file_size'] = $file->getSize();
        }

        // Replace marking scheme if uploaded
        if ($request->hasFile('marking_scheme')) {
            if ($exam->marking_scheme_path) {
                Storage::disk('public')->delete($exam->marking_scheme_path);
            }
            $file = $request->file('marking_scheme');
            $data['marking_scheme_path'] = $file->store('exams/marking-schemes', 'public');
            $data['marking_scheme_size'] = $file->getSize();
        }

        $exam->update($data);
        return $exam->fresh();
    }

    /**
     * Delete exam and its files.
     */
    public function deleteExam(Exam $exam): void
    {
        if ($exam->exam_file_path) {
            Storage::disk('public')->delete($exam->exam_file_path);
        }
        if ($exam->marking_scheme_path) {
            Storage::disk('public')->delete($exam->marking_scheme_path);
        }
        $exam->delete();
    }

    /**
     * Get dashboard statistics.
     */
    public function getStats(): array
    {
        return [
            'total'           => Exam::count(),
            'with_marking'    => Exam::whereNotNull('marking_scheme_path')->count(),
            'featured'        => Exam::featured()->count(),
            'this_month'      => Exam::where('created_at', '>=', now()->startOfMonth())->count(),
            'total_downloads' => Exam::sum('download_count'),
        ];
    }
}
