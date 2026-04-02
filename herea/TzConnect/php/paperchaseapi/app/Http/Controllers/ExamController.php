<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ExamController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all exams (paginated).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $page = $request->query('page', 0);
            $size = $request->query('size', 20);
            $sortBy = $request->query('sortBy', 'createdAt');
            $sortDirection = $request->query('sortDirection', 'desc');

            $query = Exam::with('subject');

            $sortColumn = match ($sortBy) {
                'title' => 'title',
                'code' => 'code',
                'year' => 'year',
                'downloadCount' => 'download_count',
                'viewCount' => 'view_count',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at',
                default => 'created_at',
            };

            $query->orderBy($sortColumn, $sortDirection === 'asc' ? 'asc' : 'desc');

            $exams = $query->paginate($size, ['*'], 'page', $page + 1);

            return $this->successResponse([
                'content' => collect($exams->items())->map(fn($e) => $this->formatExam($e)),
                'pageNumber' => $exams->currentPage() - 1,
                'pageSize' => $exams->perPage(),
                'totalElements' => $exams->total(),
                'totalPages' => $exams->lastPage(),
                'first' => $exams->onFirstPage(),
                'last' => $exams->onLastPage(),
                'empty' => $exams->isEmpty(),
            ], 'Exams retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve exams: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Search exams.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'keyword' => 'sometimes|string',
                'subjectId' => 'sometimes|uuid',
                'year' => 'sometimes|string',
                'type' => 'sometimes|string',
                'hasMarkingScheme' => 'sometimes|boolean',
                'isFeatured' => 'sometimes|boolean',
                'isNew' => 'sometimes|boolean',
                'page' => 'sometimes|integer',
                'size' => 'sometimes|integer',
                'sortBy' => 'sometimes|string',
                'sortDirection' => 'sometimes|in:ASC,DESC',
            ]);

            $page = $validated['page'] ?? 0;
            $size = $validated['size'] ?? 20;
            $sortBy = $validated['sortBy'] ?? 'createdAt';
            $sortDirection = $validated['sortDirection'] ?? 'DESC';

            $query = Exam::with('subject');

            if (!empty($validated['keyword'])) {
                $keyword = $validated['keyword'];
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'ilike', "%{$keyword}%")
                        ->orWhere('code', 'ilike', "%{$keyword}%")
                        ->orWhere('description', 'ilike', "%{$keyword}%");
                });
            }

            if (!empty($validated['subjectId'])) {
                $query->where('subject_id', $validated['subjectId']);
            }

            if (!empty($validated['year'])) {
                $query->where('year', $validated['year']);
            }

            if (!empty($validated['type'])) {
                $query->where('type', $validated['type']);
            }

            if (isset($validated['hasMarkingScheme'])) {
                $query->where('has_marking_scheme', $validated['hasMarkingScheme']);
            }

            if (isset($validated['isFeatured'])) {
                $query->where('is_featured', $validated['isFeatured']);
            }

            if (isset($validated['isNew'])) {
                $query->where('is_new', $validated['isNew']);
            }

            $sortColumn = match ($sortBy) {
                'title' => 'title',
                'code' => 'code',
                'year' => 'year',
                'downloadCount' => 'download_count',
                'viewCount' => 'view_count',
                'createdAt' => 'created_at',
                default => 'created_at',
            };

            $query->orderBy($sortColumn, $sortDirection === 'ASC' ? 'asc' : 'desc');

            $exams = $query->paginate($size, ['*'], 'page', $page + 1);

            return $this->successResponse([
                'content' => collect($exams->items())->map(fn($e) => $this->formatExam($e)),
                'pageNumber' => $exams->currentPage() - 1,
                'pageSize' => $exams->perPage(),
                'totalElements' => $exams->total(),
                'totalPages' => $exams->lastPage(),
                'first' => $exams->onFirstPage(),
                'last' => $exams->onLastPage(),
                'empty' => $exams->isEmpty(),
            ], 'Search results retrieved successfully');
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Search failed: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get exams by subject.
     */
    public function bySubject(string $subjectId, Request $request): JsonResponse
    {
        try {
            $page = $request->query('page', 0);
            $size = $request->query('size', 20);

            $exams = Exam::with('subject')
                ->where('subject_id', $subjectId)
                ->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $page + 1);

            return $this->successResponse([
                'content' => collect($exams->items())->map(fn($e) => $this->formatExam($e)),
                'pageNumber' => $exams->currentPage() - 1,
                'pageSize' => $exams->perPage(),
                'totalElements' => $exams->total(),
                'totalPages' => $exams->lastPage(),
                'first' => $exams->onFirstPage(),
                'last' => $exams->onLastPage(),
                'empty' => $exams->isEmpty(),
            ], 'Exams retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve exams: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get exams by year.
     */
    public function byYear(string $year, Request $request): JsonResponse
    {
        try {
            $page = $request->query('page', 0);
            $size = $request->query('size', 20);

            $exams = Exam::with('subject')
                ->where('year', $year)
                ->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $page + 1);

            return $this->successResponse([
                'content' => collect($exams->items())->map(fn($e) => $this->formatExam($e)),
                'pageNumber' => $exams->currentPage() - 1,
                'pageSize' => $exams->perPage(),
                'totalElements' => $exams->total(),
                'totalPages' => $exams->lastPage(),
                'first' => $exams->onFirstPage(),
                'last' => $exams->onLastPage(),
                'empty' => $exams->isEmpty(),
            ], 'Exams retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve exams: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get featured exams.
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $page = $request->query('page', 0);
            $size = $request->query('size', 20);

            $exams = Exam::with('subject')
                ->where('is_featured', true)
                ->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $page + 1);

            return $this->successResponse([
                'content' => collect($exams->items())->map(fn($e) => $this->formatExam($e)),
                'pageNumber' => $exams->currentPage() - 1,
                'pageSize' => $exams->perPage(),
                'totalElements' => $exams->total(),
                'totalPages' => $exams->lastPage(),
                'first' => $exams->onFirstPage(),
                'last' => $exams->onLastPage(),
                'empty' => $exams->isEmpty(),
            ], 'Featured exams retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve featured exams: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get new exams.
     */
    public function newExams(Request $request): JsonResponse
    {
        try {
            $page = $request->query('page', 0);
            $size = $request->query('size', 20);

            $exams = Exam::with('subject')
                ->where('is_new', true)
                ->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $page + 1);

            return $this->successResponse([
                'content' => collect($exams->items())->map(fn($e) => $this->formatExam($e)),
                'pageNumber' => $exams->currentPage() - 1,
                'pageSize' => $exams->perPage(),
                'totalElements' => $exams->total(),
                'totalPages' => $exams->lastPage(),
                'first' => $exams->onFirstPage(),
                'last' => $exams->onLastPage(),
                'empty' => $exams->isEmpty(),
            ], 'New exams retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve new exams: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get most downloaded exams.
     */
    public function mostDownloaded(Request $request): JsonResponse
    {
        try {
            $limit = $request->query('limit', 10);

            $exams = Exam::with('subject')
                ->orderBy('download_count', 'desc')
                ->limit($limit)
                ->get();

            return $this->successResponse(
                $exams->map(fn($e) => $this->formatExam($e)),
                'Most downloaded exams retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve most downloaded exams: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get recently added exams.
     */
    public function recent(Request $request): JsonResponse
    {
        try {
            $limit = $request->query('limit', 10);

            $exams = Exam::with('subject')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return $this->successResponse(
                $exams->map(fn($e) => $this->formatExam($e)),
                'Recent exams retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve recent exams: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get exam by ID.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $exam = Exam::with('subject')->findOrFail($id);
            $exam->incrementViewCount();
            
            return $this->successResponse(
                $this->formatExam($exam),
                'Exam retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Exam not found',
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Get exam by code.
     */
    public function showByCode(string $code): JsonResponse
    {
        try {
            $exam = Exam::with('subject')->where('code', $code)->firstOrFail();
            $exam->incrementViewCount();
            
            return $this->successResponse(
                $this->formatExam($exam),
                'Exam retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Exam not found',
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Get similar exams.
     */
    public function similar(string $id, Request $request): JsonResponse
    {
        try {
            $limit = $request->query('limit', 5);
            $exam = Exam::findOrFail($id);

            $exams = Exam::with('subject')
                ->where('subject_id', $exam->subject_id)
                ->where('id', '!=', $id)
                ->orderBy('download_count', 'desc')
                ->limit($limit)
                ->get();

            return $this->successResponse(
                $exams->map(fn($e) => $this->formatExam($e)),
                'Similar exams retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve similar exams: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get distinct years.
     */
    public function distinctYears(): JsonResponse
    {
        try {
            $years = Exam::distinct()
                ->pluck('year')
                ->sort()
                ->values();

            return $this->successResponse(
                $years,
                'Years retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve years: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get distinct subjects.
     */
    public function distinctSubjects(): JsonResponse
    {
        try {
            $subjects = Exam::with('subject')
                ->distinct()
                ->pluck('subject_id')
                ->toArray();

            return $this->successResponse(
                $subjects,
                'Subjects retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve subjects: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Create new exam.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|unique:exams',
                'title' => 'required|string|max:255',
                'subjectId' => 'required|uuid|exists:subjects,id',
                'year' => 'required|string',
                'type' => 'required|in:PRACTICE_PAPER,MOCK_PAPER,PAST_PAPER,NECTA_PAPER,REVISION_PAPER,JOINT_PAPER,PRE_NECTA',
                'description' => 'sometimes|string|nullable',
                'isFeatured' => 'sometimes|boolean',
                'isNew' => 'sometimes|boolean',
                'examFile' => 'required|file|mimes:pdf|max:51200',
                'markingSchemeFile' => 'sometimes|file|mimes:pdf|max:51200',
            ]);

            $user = $request->user();

            $examData = [
                'code' => $validated['code'],
                'title' => $validated['title'],
                'subject_id' => $validated['subjectId'],
                'year' => $validated['year'],
                'type' => $validated['type'],
                'description' => $validated['description'] ?? null,
                'is_featured' => $validated['isFeatured'] ?? false,
                'is_new' => $validated['isNew'] ?? true,
                'created_by' => $user?->id,
            ];

            // Handle exam file upload
            if ($request->hasFile('examFile')) {
                $file = $validated['examFile'];
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('exams', $filename, 'public');
                
                $examData['pdf_path'] = $path;
                $examData['pdf_name'] = $file->getClientOriginalName();
                $examData['file_size'] = $file->getSize();
            }

            // Handle marking scheme file upload
            if ($request->hasFile('markingSchemeFile')) {
                $file = $validated['markingSchemeFile'];
                $filename = time() . '_ms_' . $file->getClientOriginalName();
                $path = $file->storeAs('marking-schemes', $filename, 'public');
                
                $examData['marking_scheme_path'] = $path;
                $examData['marking_scheme_name'] = $file->getClientOriginalName();
                $examData['marking_scheme_size'] = $file->getSize();
                $examData['has_marking_scheme'] = true;
            }

            $exam = Exam::create($examData);

            // Update subject paper count
            Subject::find($validated['subjectId'])?->recalculatePaperCount();

            $exam->load('subject');

            return $this->createdResponse(
                $this->formatExam($exam),
                'Exam created successfully'
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to create exam: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update exam.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $exam = Exam::findOrFail($id);

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'subjectId' => 'sometimes|uuid|exists:subjects,id',
                'year' => 'sometimes|string',
                'type' => 'sometimes|in:PRACTICE_PAPER,MOCK_PAPER,PAST_PAPER,NECTA_PAPER,REVISION_PAPER,JOINT_PAPER,PRE_NECTA',
                'description' => 'sometimes|string|nullable',
                'isFeatured' => 'sometimes|boolean',
                'isNew' => 'sometimes|boolean',
            ]);

            $user = $request->user();

            $updateData = [
                'title' => $validated['title'] ?? $exam->title,
                'subject_id' => $validated['subjectId'] ?? $exam->subject_id,
                'year' => $validated['year'] ?? $exam->year,
                'type' => $validated['type'] ?? $exam->type,
                'description' => $validated['description'] ?? $exam->description,
                'is_featured' => $validated['isFeatured'] ?? $exam->is_featured,
                'is_new' => $validated['isNew'] ?? $exam->is_new,
                'updated_by' => $user?->id,
            ];

            $exam->update($updateData);

            // Update subject paper count if subject changed
            if (isset($validated['subjectId']) && $validated['subjectId'] !== $exam->getOriginal('subject_id')) {
                Subject::find($exam->getOriginal('subject_id'))?->recalculatePaperCount();
                Subject::find($validated['subjectId'])?->recalculatePaperCount();
            }

            $exam->load('subject');

            return $this->successResponse(
                $this->formatExam($exam),
                'Exam updated successfully'
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update exam: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Upload marking scheme.
     */
    public function uploadMarkingScheme(Request $request, string $id): JsonResponse
    {
        try {
            $exam = Exam::findOrFail($id);

            $validated = $request->validate([
                'markingSchemeFile' => 'required|file|mimes:pdf|max:51200',
            ]);

            // Delete old marking scheme if exists
            if ($exam->marking_scheme_path) {
                Storage::disk('public')->delete($exam->marking_scheme_path);
            }

            $file = $validated['markingSchemeFile'];
            $filename = time() . '_ms_' . $file->getClientOriginalName();
            $path = $file->storeAs('marking-schemes', $filename, 'public');

            $exam->update([
                'marking_scheme_path' => $path,
                'marking_scheme_name' => $file->getClientOriginalName(),
                'marking_scheme_size' => $file->getSize(),
                'has_marking_scheme' => true,
            ]);

            return $this->successResponse(
                $this->formatExam($exam),
                'Marking scheme uploaded successfully'
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to upload marking scheme: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Delete exam.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $exam = Exam::findOrFail($id);

            // Delete files
            if ($exam->pdf_path) {
                Storage::disk('public')->delete($exam->pdf_path);
            }
            if ($exam->marking_scheme_path) {
                Storage::disk('public')->delete($exam->marking_scheme_path);
            }

            $subjectId = $exam->subject_id;
            $exam->delete();

            // Update subject paper count
            Subject::find($subjectId)?->recalculatePaperCount();

            return $this->noContentResponse();
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete exam: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Record download.
     */
    public function recordDownload(Request $request, string $id): JsonResponse
    {
        try {
            $exam = Exam::findOrFail($id);
            $user = $request->user();

            $exam->incrementDownloadCount();

            // Record download if user is authenticated
            if ($user) {
                \App\Models\Download::create([
                    'user_id' => $user->id,
                    'exam_id' => $exam->id,
                ]);
            }

            return $this->successResponse(null, 'Download recorded');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to record download: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get exam statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalExams = Exam::count();
            $examsWithMarkingSchemes = Exam::where('has_marking_scheme', true)->count();
            $featuredExams = Exam::where('is_featured', true)->count();
            $newExams = Exam::where('is_new', true)->count();
            $totalDownloads = Exam::sum('download_count');
            $totalViews = Exam::sum('view_count');
            $totalBookmarks = Exam::sum('bookmark_count');

            // Calculate storage used
            $totalStorageUsed = Exam::sum('file_size') + Exam::sum('marking_scheme_size');

            return $this->successResponse([
                'totalExams' => $totalExams,
                'examsWithMarkingSchemes' => $examsWithMarkingSchemes,
                'featuredExams' => $featuredExams,
                'newExams' => $newExams,
                'newExamsToday' => 0, // Would need date filtering
                'newExamsThisWeek' => 0,
                'newExamsThisMonth' => 0,
                'totalDownloads' => $totalDownloads,
                'totalViews' => $totalViews,
                'totalBookmarks' => $totalBookmarks,
                'totalStorageUsed' => $totalStorageUsed,
                'totalStorageFormatted' => $this->formatBytes($totalStorageUsed),
            ], 'Statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve statistics: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Format exam for response.
     */
    private function formatExam(Exam $exam): array
    {
        return [
            'id' => $exam->id,
            'code' => $exam->code,
            'title' => $exam->title,
            'subjectId' => $exam->subject_id,
            'subjectName' => $exam->subject?->name,
            'year' => $exam->year,
            'type' => $exam->type,
            'hasMarkingScheme' => $exam->has_marking_scheme,
            'fileSize' => $exam->file_size,
            'fileSizeFormatted' => $exam->file_size_formatted,
            'markingSchemeSize' => $exam->marking_scheme_size,
            'markingSchemeSizeFormatted' => $exam->marking_scheme_size_formatted,
            'previewImage' => $exam->preview_image,
            'pdfUrl' => $exam->pdf_url,
            'markingSchemeUrl' => $exam->marking_scheme_url,
            'icon' => $exam->icon,
            'color' => $exam->color,
            'bgColor' => $exam->bg_color,
            'borderColor' => $exam->border_color,
            'isNew' => $exam->is_new,
            'isFeatured' => $exam->is_featured,
            'description' => $exam->description,
            'createdAt' => $exam->created_at?->toIso8601String(),
            'updatedAt' => $exam->updated_at?->toIso8601String(),
            'createdBy' => $exam->created_by,
            'updatedBy' => $exam->updated_by,
            'downloadCount' => $exam->download_count,
            'viewCount' => $exam->view_count,
            'bookmarkCount' => $exam->bookmark_count,
        ];
    }

    /**
     * Format bytes to human readable.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}

