<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class SubjectController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all subjects (non-paginated).
     */
    public function index(): JsonResponse
    {
        try {
            $subjects = Subject::orderBy('name')->get();
            return $this->successResponse(
                $subjects->map(fn($s) => $this->formatSubject($s)),
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
     * Get all subjects (paginated).
     */
    public function paginated(Request $request): JsonResponse
    {
        try {
            $page = $request->query('page', 0);
            $size = $request->query('size', 20);
            $sortBy = $request->query('sortBy', 'name');
            $sortDirection = $request->query('sortDirection', 'asc');

            $query = Subject::query();

            $sortColumn = match ($sortBy) {
                'paperCount' => 'paper_count',
                'createdAt' => 'created_at',
                'updatedAt' => 'updated_at',
                default => 'name',
            };

            $query->orderBy($sortColumn, $sortDirection === 'asc' ? 'asc' : 'desc');

            $subjects = $query->paginate($size, ['*'], 'page', $page + 1);

            return $this->successResponse([
                'content' => collect($subjects->items())->map(fn($s) => $this->formatSubject($s)),
                'pageNumber' => $subjects->currentPage() - 1,
                'pageSize' => $subjects->perPage(),
                'totalElements' => $subjects->total(),
                'totalPages' => $subjects->lastPage(),
                'first' => $subjects->onFirstPage(),
                'last' => $subjects->onLastPage(),
                'empty' => $subjects->isEmpty(),
            ], 'Subjects retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve subjects: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get popular subjects (ordered by exam count).
     */
    public function popular(): JsonResponse
    {
        try {
            $subjects = Subject::withCount('exams')
                ->orderBy('exams_count', 'desc')
                ->get();
            return $this->successResponse(
                $subjects->map(fn($s) => $this->formatSubject($s)),
                'Popular subjects retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve popular subjects: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get top subjects.
     */
    public function top(Request $request): JsonResponse
    {
        try {
            $limit = $request->query('limit', 10);
            $subjects = Subject::withCount('exams')
                ->orderBy('exams_count', 'desc')
                ->limit($limit)
                ->get();
            return $this->successResponse(
                $subjects->map(fn($s) => $this->formatSubject($s)),
                'Top subjects retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve top subjects: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get subjects with exams.
     */
    public function withExams(Request $request): JsonResponse
    {
        try {
            $minCount = $request->query('count', 0);
            $subjects = Subject::withCount('exams')
                ->having('exams_count', '>=', $minCount)
                ->orderBy('exams_count', 'desc')
                ->get();
            return $this->successResponse(
                $subjects->map(fn($s) => $this->formatSubject($s)),
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
     * Get empty subjects.
     */
    public function empty(): JsonResponse
    {
        try {
            $subjects = Subject::withCount('exams')
                ->having('exams_count', 0)
                ->orderBy('name')
                ->get();
            return $this->successResponse(
                $subjects->map(fn($s) => $this->formatSubject($s)),
                'Empty subjects retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve empty subjects: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Search subjects.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $keyword = $request->query('keyword', '');
            $page = $request->query('page', 0);
            $size = $request->query('size', 20);

            $query = Subject::where('name', 'ilike', "%{$keyword}%")
                ->orWhere('description', 'ilike', "%{$keyword}%");

            $subjects = $query->orderBy('name')->paginate($size, ['*'], 'page', $page + 1);

            return $this->successResponse([
                'content' => collect($subjects->items())->map(fn($s) => $this->formatSubject($s)),
                'pageNumber' => $subjects->currentPage() - 1,
                'pageSize' => $subjects->perPage(),
                'totalElements' => $subjects->total(),
                'totalPages' => $subjects->lastPage(),
                'first' => $subjects->onFirstPage(),
                'last' => $subjects->onLastPage(),
                'empty' => $subjects->isEmpty(),
            ], 'Search results retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Search failed: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get subject by ID.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $subject = Subject::findOrFail($id);
            return $this->successResponse(
                $this->formatSubject($subject),
                'Subject retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Subject not found',
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Get subject by name.
     */
    public function showByName(string $name): JsonResponse
    {
        try {
            $subject = Subject::where('name', $name)->firstOrFail();
            return $this->successResponse(
                $this->formatSubject($subject),
                'Subject retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Subject not found',
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Create new subject.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:subjects',
                'icon' => 'sometimes|string|max:50',
                'color' => 'sometimes|string|max:20',
                'bgColor' => 'sometimes|string|max:20',
                'borderColor' => 'sometimes|string|max:20',
                'description' => 'sometimes|string|nullable',
            ]);

            $subject = Subject::create([
                'name' => $validated['name'],
                'icon' => $validated['icon'] ?? 'Folder',
                'color' => $validated['color'] ?? '#3B82F6',
                'bg_color' => $validated['bgColor'] ?? '#EFF6FF',
                'border_color' => $validated['borderColor'] ?? '#BFDBFE',
                'description' => $validated['description'] ?? null,
                'paper_count' => 0,
            ]);

            return $this->createdResponse(
                $this->formatSubject($subject),
                'Subject created successfully'
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to create subject: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update subject.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $subject = Subject::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255|unique:subjects,name,' . $id,
                'icon' => 'sometimes|string|max:50',
                'color' => 'sometimes|string|max:20',
                'bgColor' => 'sometimes|string|max:20',
                'borderColor' => 'sometimes|string|max:20',
                'description' => 'sometimes|string|nullable',
            ]);

            $subject->update([
                'name' => $validated['name'] ?? $subject->name,
                'icon' => $validated['icon'] ?? $subject->icon,
                'color' => $validated['color'] ?? $subject->color,
                'bg_color' => $validated['bgColor'] ?? $subject->bg_color,
                'border_color' => $validated['borderColor'] ?? $subject->border_color,
                'description' => $validated['description'] ?? $subject->description,
            ]);

            return $this->successResponse(
                $this->formatSubject($subject),
                'Subject updated successfully'
            );
        } catch (ValidationException $e) {
            return $this->errorResponse(
                $e->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update subject: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Delete subject.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $subject = Subject::findOrFail($id);
            $subject->delete();

            return $this->noContentResponse();
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete subject: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Check name availability.
     */
    public function checkName(Request $request): JsonResponse
    {
        try {
            $name = $request->query('name');
            
            if (!$name) {
                return $this->errorResponse(
                    'Name parameter is required',
                    Response::HTTP_BAD_REQUEST
                );
            }

            $exists = Subject::where('name', $name)->exists();

            return $this->successResponse([
                'name' => $name,
                'available' => !$exists,
            ], 'Name availability checked');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to check name availability: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Recalculate paper count for a subject.
     */
    public function recalculateCount(string $id): JsonResponse
    {
        try {
            $subject = Subject::findOrFail($id);
            $subject->recalculatePaperCount();

            return $this->successResponse(
                $this->formatSubject($subject),
                'Paper count recalculated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to recalculate paper count: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Recalculate all paper counts.
     */
    public function recalculateAllCounts(): JsonResponse
    {
        try {
            $subjects = Subject::all();
            
            foreach ($subjects as $subject) {
                $subject->recalculatePaperCount();
            }

            return $this->successResponse(null, 'All paper counts recalculated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to recalculate paper counts: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get subject statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalSubjects = Subject::count();
            $totalExams = \App\Models\Exam::count();
            $averageExamsPerSubject = $totalSubjects > 0 ? round($totalExams / $totalSubjects, 2) : 0;
            $mostPopular = Subject::orderBy('paper_count', 'desc')->first();
            $subjectsWithExams = Subject::where('paper_count', '>', 0)->count();
            $emptySubjects = Subject::where('paper_count', 0)->count();
            $totalDownloads = \App\Models\Download::count();

            return $this->successResponse([
                'totalSubjects' => $totalSubjects,
                'totalExams' => $totalExams,
                'averageExamsPerSubject' => $averageExamsPerSubject,
                'mostPopularCategory' => $mostPopular ? $this->formatSubject($mostPopular) : null,
                'categoriesWithExams' => $subjectsWithExams,
                'emptyCategories' => $emptySubjects,
                'totalDownloads' => $totalDownloads,
            ], 'Statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve statistics: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Format subject for response.
     */
    private function formatSubject(Subject $subject): array
    {
        $examCount = $subject->exams_count ?? $subject->exams()->count() ?? 0;
        return [
            'id' => $subject->id,
            'name' => $subject->name,
            'icon' => $subject->icon,
            'color' => $subject->color,
            'bgColor' => $subject->bg_color,
            'borderColor' => $subject->border_color,
            'paperCount' => $examCount,
            'description' => $subject->description,
            'createdAt' => $subject->created_at?->toIso8601String(),
            'updatedAt' => $subject->updated_at?->toIso8601String(),
            'totalDownloads' => $examCount * 10, // Estimated
            'activeExams' => $examCount,
        ];
    }
}

