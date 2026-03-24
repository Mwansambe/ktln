<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * ExamController (API)
 * Handles exam browsing and downloading for the mobile app.
 */
class ExamController extends Controller
{
    /**
     * List all published exams with optional filters.
     * GET /api/exams
     */
    public function index(Request $request): JsonResponse
    {
        $query = Exam::with('subject:id,name,code,color,icon')
                     ->published();

        // Filter by subject
        if ($request->has('subject_id')) {
            $query->bySubject($request->subject_id);
        }

        // Filter by class level
        if ($request->has('class_level')) {
            $query->byClass($request->class_level);
        }

        // Filter by exam type
        if ($request->has('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        // Filter by year
        if ($request->has('year')) {
            $query->byYear($request->year);
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'ilike', '%' . $request->search . '%');
        }

        // Featured only
        if ($request->boolean('featured')) {
            $query->featured();
        }

        $exams = $query->orderByDesc('created_at')
                       ->paginate($request->get('per_page', 20));

        return $this->successResponse('Exams retrieved.', [
            'exams' => $exams->map(fn($exam) => $this->formatExam($exam)),
            'pagination' => [
                'current_page' => $exams->currentPage(),
                'last_page'    => $exams->lastPage(),
                'per_page'     => $exams->perPage(),
                'total'        => $exams->total(),
            ],
        ]);
    }

    /**
     * Get single exam details.
     * GET /api/exams/{id}
     */
    public function show(int $id): JsonResponse
    {
        $exam = Exam::with('subject:id,name,code,color,icon')->published()->findOrFail($id);

        return $this->successResponse('Exam retrieved.', ['exam' => $this->formatExam($exam)]);
    }

    /**
     * Download exam PDF.
     * GET /api/exams/{id}/download
     */
    public function download(int $id)
    {
        $exam = Exam::published()->findOrFail($id);

        if (!Storage::exists($exam->exam_file_path)) {
            return $this->errorResponse('Exam file not found.', 404);
        }

        // Increment download counter
        $exam->incrementDownload();

        return Storage::download(
            $exam->exam_file_path,
            $exam->title . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Download marking scheme PDF.
     * GET /api/exams/{id}/marking-scheme
     */
    public function downloadMarkingScheme(int $id)
    {
        $exam = Exam::published()->findOrFail($id);

        if (!$exam->marking_scheme_path || !Storage::exists($exam->marking_scheme_path)) {
            return $this->errorResponse('Marking scheme not found.', 404);
        }

        return Storage::download(
            $exam->marking_scheme_path,
            $exam->title . ' - Marking Scheme.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    // ==================== HELPERS ====================

    private function formatExam(Exam $exam): array
    {
        return [
            'id'                    => $exam->id,
            'code'                  => $exam->code,
            'title'                 => $exam->title,
            'description'           => $exam->description,
            'exam_type'             => $exam->exam_type,
            'class_level'           => $exam->class_level,
            'year'                  => $exam->year,
            'has_marking_scheme'    => $exam->hasMarkingScheme(),
            'exam_file_size'        => $exam->exam_file_size,
            'marking_scheme_size'   => $exam->marking_scheme_size,
            'is_featured'           => $exam->is_featured,
            'download_count'        => $exam->download_count,
            'subject'               => $exam->subject ? [
                'id'    => $exam->subject->id,
                'name'  => $exam->subject->name,
                'code'  => $exam->subject->code,
                'color' => $exam->subject->color,
                'icon'  => $exam->subject->icon,
            ] : null,
            'created_at'            => $exam->created_at->toISOString(),
        ];
    }

    protected function successResponse(string $message, array $data = []): JsonResponse
    {
        $response = ['status' => 'success', 'message' => $message];
        if (!empty($data)) $response['data'] = $data;
        return response()->json($response);
    }

    protected function errorResponse(string $message, int $code = 400): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $message], $code);
    }
}
