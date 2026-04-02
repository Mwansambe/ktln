@extends('layouts.app')

@section('title', 'Home - PaperChase')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Welcome to PaperChase</h1>
            <p class="text-xl mb-8 text-indigo-100">Your comprehensive exam paper management and learning resource platform</p>
            
            @if(!auth()->check())
                <div class="flex justify-center gap-4">
                    <a href="{{ route('login') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-indigo-50">Login</a>
                    <a href="{{ route('register') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700">Register</a>
                </div>
            @else
                <div class="flex justify-center gap-4">
                    <a href="{{ route('exams.index') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-indigo-50">Browse Exams</a>
                    <a href="{{ route('dashboard') }}" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700">Go to Dashboard</a>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Features Section -->
    <div class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Key Features</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="text-4xl mb-4">📚</div>
                    <h3 class="text-xl font-bold mb-2">Extensive Library</h3>
                    <p class="text-gray-600">Browse thousands of exam papers, practice tests, and study materials organized by subject and year.</p>
                </div>
                
                <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="text-4xl mb-4">⭐</div>
                    <h3 class="text-xl font-bold mb-2">Smart Bookmarking</h3>
                    <p class="text-gray-600">Bookmark your favorite papers for quick access and organize your study materials effectively.</p>
                </div>
                
                <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-xl font-bold mb-2">Analytics & Insights</h3>
                    <p class="text-gray-600">Track your downloads, view statistics, and monitor your learning progress with detailed analytics.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Exams Section -->
    @if($recentExams->count() > 0)
    <div class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold mb-12">Recent Exams</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($recentExams as $exam)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden">
                    <div class="p-6">
                        <h3 class="font-bold text-lg mb-2">{{ $exam->title }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $exam->subject->name ?? 'General' }}</p>
                        <div class="flex justify-between text-sm text-gray-500 mb-4">
                            <span>📅 {{ $exam->year }}</span>
                            <span>⬇️ {{ $exam->download_count }} downloads</span>
                        </div>
                        <a href="{{ route('exams.show', $exam->id) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">View Details →</a>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="text-center mt-8">
                <a href="{{ route('exams.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">View All Exams →</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
