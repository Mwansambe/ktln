<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_exams'     => Exam::count(),
            'active_subjects' => Subject::active()->count(),
            'total_users'     => User::count(),
            'total_downloads' => Exam::sum('download_count'),
        ];

        $recentExams     = Exam::with('subject', 'uploader')->latest()->limit(5)->get();
        $recentActivities = $this->getRecentActivities();

        return view('admin.dashboard', compact('stats', 'recentExams', 'recentActivities'));
    }

    private function getRecentActivities(): array
    {
        // Combine recent exams and user activations into timeline
        $activities = [];

        Exam::with('uploader')->latest()->limit(5)->get()->each(function ($exam) use (&$activities) {
            $activities[] = [
                'type'        => 'exam_uploaded',
                'description' => ($exam->uploader?->name ?? 'Someone') . ' uploaded ' . $exam->title,
                'time'        => $exam->created_at->diffForHumans(),
            ];
        });

        usort($activities, fn($a, $b) => strcmp($a['time'], $b['time']));

        return array_slice($activities, 0, 10);
    }
}
