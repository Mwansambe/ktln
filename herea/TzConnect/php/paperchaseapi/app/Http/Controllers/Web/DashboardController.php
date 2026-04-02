<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use App\Models\Download;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get statistics
        $stats = [
            'totalExams' => Exam::count(),
            'totalSubjects' => Subject::count(),
            'totalUsers' => User::count(),
            'totalDownloads' => Download::count(),
            'featuredExams' => Exam::where('is_featured', true)->count(),
            'newExams' => Exam::where('is_new', true)->count(),
        ];

        // Get recent exams
        $recentExams = Exam::with(['subject', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent downloads
        $recentDownloads = Download::with(['user', 'exam'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get popular subjects
        $popularSubjects = Subject::withCount('exams')
            ->orderBy('exams_count', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentExams', 'recentDownloads', 'popularSubjects'));
    }
}

