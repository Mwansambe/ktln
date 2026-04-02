<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Bookmark;
use App\Models\Download;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StatisticsController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get dashboard statistics.
     */
    public function dashboard(): JsonResponse
    {
        try {
            // User statistics
            $totalUsers = User::count();
            $activeUsers = User::where('is_active', true)->count();
            $newUsersToday = User::whereDate('created_at', today())->count();
            $newUsersThisWeek = User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

            // Exam statistics
            $totalExams = Exam::count();
            $examsWithMarkingSchemes = Exam::where('has_marking_scheme', true)->count();
            $featuredExams = Exam::where('is_featured', true)->count();
            $newExams = Exam::where('is_new', true)->count();
            $newExamsToday = Exam::whereDate('created_at', today())->count();
            $newExamsThisWeek = Exam::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();

            // Subject statistics
            $totalSubjects = Subject::count();
            $subjectsWithExams = Subject::where('paper_count', '>', 0)->count();
            $emptySubjects = Subject::where('paper_count', 0)->count();

            // Activity statistics
            $totalDownloads = Download::count();
            $totalBookmarks = Bookmark::count();
            $totalViews = Exam::sum('view_count');

            // Storage
            $totalStorageUsed = Exam::sum('file_size') + Exam::sum('marking_scheme_size');

            // Recent activity
            $recentExams = Exam::with('subject')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return $this->successResponse([
                'users' => [
                    'totalUsers' => $totalUsers,
                    'activeUsers' => $activeUsers,
                    'newUsersToday' => $newUsersToday,
                    'newUsersThisWeek' => $newUsersThisWeek,
                ],
                'exams' => [
                    'totalExams' => $totalExams,
                    'examsWithMarkingSchemes' => $examsWithMarkingSchemes,
                    'featuredExams' => $featuredExams,
                    'newExams' => $newExams,
                    'newExamsToday' => $newExamsToday,
                    'newExamsThisWeek' => $newExamsThisWeek,
                ],
                'subjects' => [
                    'totalSubjects' => $totalSubjects,
                    'subjectsWithExams' => $subjectsWithExams,
                    'emptySubjects' => $emptySubjects,
                ],
                'activity' => [
                    'totalDownloads' => $totalDownloads,
                    'totalBookmarks' => $totalBookmarks,
                    'totalViews' => $totalViews,
                ],
                'storage' => [
                    'totalStorageUsed' => $totalStorageUsed,
                    'totalStorageFormatted' => $this->formatBytes($totalStorageUsed),
                ],
                'recentExams' => $recentExams->map(fn($e) => [
                    'id' => $e->id,
                    'title' => $e->title,
                    'code' => $e->code,
                    'subjectName' => $e->subject?->name,
                    'type' => $e->type,
                    'year' => $e->year,
                    'downloadCount' => $e->download_count,
                    'createdAt' => $e->created_at?->toIso8601String(),
                ]),
            ], 'Dashboard statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve dashboard statistics: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get overview statistics.
     */
    public function overview(): JsonResponse
    {
        try {
            $totalUsers = User::count();
            $totalExams = Exam::count();
            $totalSubjects = Subject::count();
            $totalDownloads = Download::count();

            return $this->successResponse([
                'totalUsers' => $totalUsers,
                'totalExams' => $totalExams,
                'totalSubjects' => $totalSubjects,
                'totalDownloads' => $totalDownloads,
            ], 'Overview statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve overview: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Format bytes to human readable.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}

