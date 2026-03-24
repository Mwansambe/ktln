<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;

/**
 * SubjectController (API)
 * Provides subject categories to the mobile app.
 */
class SubjectController extends Controller
{
    /**
     * List all active subjects.
     * GET /api/subjects
     */
    public function index(): JsonResponse
    {
        $subjects = Subject::active()
            ->withCount(['exams' => fn($q) => $q->published()])
            ->orderBy('name')
            ->get()
            ->map(fn($s) => [
                'id'         => $s->id,
                'name'       => $s->name,
                'code'       => $s->code,
                'description'=> $s->description,
                'icon'       => $s->icon,
                'color'      => $s->color,
                'exam_count' => $s->exams_count,
            ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Subjects retrieved.',
            'data'    => ['subjects' => $subjects],
        ]);
    }
}
