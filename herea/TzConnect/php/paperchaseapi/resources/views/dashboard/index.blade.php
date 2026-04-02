@extends('layouts.app')

@section('title', 'Dashboard - PaperChase')

@section('breadcrumb')
<li class="flex items-center">
    <span class="text-gray-400">/</span>
    <span class="ml-2 text-gray-900 font-medium">Dashboard</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-500 mt-1">Welcome back! Here's what's happening with your exams.</p>
        </div>
        @can('create', \App\Models\Exam::class)
        <a href="{{ route('exams.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add New Exam
        </a>
        @endcan
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Exams -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Exams</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['totalExams']) }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6 text-indigo-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 flex items-center gap-1">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                    +12%
                </span>
                <span class="text-gray-400 ml-2">from last month</span>
            </div>
        </div>

        <!-- Total Subjects -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Subjects</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['totalSubjects']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="folder-open" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 flex items-center gap-1">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                    +5%
                </span>
                <span class="text-gray-400 ml-2">from last month</span>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['totalUsers']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 flex items-center gap-1">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                    +8%
                </span>
                <span class="text-gray-400 ml-2">from last month</span>
            </div>
        </div>

        <!-- Total Downloads -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Downloads</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['totalDownloads']) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="download" class="w-6 h-6 text-orange-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 flex items-center gap-1">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                    +24%
                </span>
                <span class="text-gray-400 ml-2">from last month</span>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Featured Exams -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100">Featured Exams</p>
                    <p class="text-4xl font-bold mt-1">{{ number_format($stats['featuredExams']) }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-lucide="star" class="w-7 h-7"></i>
                </div>
            </div>
        </div>

        <!-- New Exams -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100">New This Week</p>
                    <p class="text-4xl font-bold mt-1">{{ number_format($stats['newExams']) }}</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-lg flex items-center justify-center">
                    <i data-lucide="sparkles" class="w-7 h-7"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Exams -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Recent Exams</h2>
                <a href="{{ route('exams.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">View All</a>
            </div>
            <div class="p-6">
                @if($recentExams->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentExams as $exam)
                        <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $exam->subject->bg_color ?? '#e0e7ff' }}">
                                <i data-lucide="file-text" class="w-5 h-5" style="color: {{ $exam->subject->color ?? '#4f46e5' }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $exam->title }}</p>
                                <p class="text-xs text-gray-500">{{ $exam->subject->name ?? 'No Subject' }} • {{ $exam->year }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $exam->created_at->diffForHumans() }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="file-x" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <p class="text-gray-500">No exams yet</p>
                        @can('create', \App\Models\Exam::class)
                        <a href="{{ route('exams.create') }}" class="text-indigo-600 hover:text-indigo-700 text-sm mt-2 inline-block">
                            Create your first exam
                        </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>

        <!-- Popular Subjects -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Popular Subjects</h2>
                <a href="{{ route('categories.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">View All</a>
            </div>
            <div class="p-6">
                @if($popularSubjects->count() > 0)
                    <div class="space-y-4">
                        @foreach($popularSubjects as $subject)
                        <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $subject->bg_color ?? '#e0e7ff' }}">
                                <i data-lucide="folder" class="w-5 h-5" style="color: {{ $subject->color ?? '#4f46e5' }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $subject->name }}</p>
                                <p class="text-xs text-gray-500">{{ $subject->exam_count }} exams</p>
                            </div>
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(($subject->exam_count / max($popularSubjects->max('exam_count'), 1)) * 100, 100) }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="folder-x" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <p class="text-gray-500">No subjects yet</p>
                        @can('create', \App\Models\Subject::class)
                        <a href="{{ route('categories.create') }}" class="text-indigo-600 hover:text-indigo-700 text-sm mt-2 inline-block">
                            Create your first subject
                        </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Downloads -->
    @can('manage-content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Recent Downloads</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentDownloads as $download)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-xs font-medium">
                                    {{ strtoupper(substr($download->user->name ?? 'U', 0, 2)) }}
                                </div>
                                <span class="text-sm text-gray-900">{{ $download->user->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-900">{{ $download->exam->title ?? 'Unknown' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500">{{ $download->created_at->format('M d, Y H:i') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                            No downloads yet
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endcan
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
@endsection

