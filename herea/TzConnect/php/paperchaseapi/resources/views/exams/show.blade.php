paperchase api@extends('layouts.app')

@section('title', $exam->title . ' - PaperChase')

@section('breadcrumb')
<li><a href="{{ route('exams.index') }}" class="text-gray-400 hover:text-gray-600">Exams</a></li>
<li><span class="text-gray-400">/</span></li>
<li class="ml-2 text-gray-900 font-medium">{{ $exam->title }}</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }}</h1>
            <p class="text-gray-500 mt-1">{{ $exam->code }}</p>
        </div>
        <div class="flex items-center gap-2">
            @can('update', $exam)
            <a href="{{ route('exams.edit', $exam) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="pencil" class="w-4 h-4 mr-2"></i>
                Edit
            </a>
            @endcan
            @can('delete', $exam)
            <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors" onclick="return confirm('Are you sure?')">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                    Delete
                </button>
            </form>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Exam Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Exam Details</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Subject</p>
                        <p class="font-medium text-gray-900">{{ $exam->subject->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Year</p>
                        <p class="font-medium text-gray-900">{{ $exam->year }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Type</p>
                        <p class="font-medium text-gray-900">{{ str_replace('_', ' ', $exam->type) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Created By</p>
                        <p class="font-medium text-gray-900">{{ $exam->creator->name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Created At</p>
                        <p class="font-medium text-gray-900">{{ $exam->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Last Updated</p>
                        <p class="font-medium text-gray-900">{{ $exam->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>

                @if($exam->description)
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-500">Description</p>
                    <p class="mt-1 text-gray-900">{{ $exam->description }}</p>
                </div>
                @endif
            </div>

            <!-- Downloads -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Downloads</h2>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="file-text" class="w-5 h-5 text-red-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $exam->pdf_name ?? 'Exam Paper' }}</p>
                                <p class="text-sm text-gray-500">{{ $exam->file_size ? number_format($exam->file_size / 1024, 0) . ' KB' : 'N/A' }}</p>
                            </div>
                        </div>
                        @if($exam->pdf_path)
                        <a href="{{ Storage::url($exam->pdf_path) }}" download class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                            Download
                        </a>
                        @else
                        <span class="text-gray-400">Not uploaded</span>
                        @endif
                    </div>

                    @if($exam->has_marking_scheme)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="file-check" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $exam->marking_scheme_name ?? 'Marking Scheme' }}</p>
                                <p class="text-sm text-gray-500">{{ $exam->marking_scheme_size ? number_format($exam->marking_scheme_size / 1024, 0) . ' KB' : 'N/A' }}</p>
                            </div>
                        </div>
                        @if($exam->marking_scheme_path)
                        <a href="{{ Storage::url($exam->marking_scheme_path) }}" download class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                            Download
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Downloads</span>
                        <span class="font-semibold text-gray-900">{{ number_format($exam->download_count) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Bookmarks</span>
                        <span class="font-semibold text-gray-900">{{ number_format($exam->bookmark_count) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Views</span>
                        <span class="font-semibold text-gray-900">{{ number_format($exam->view_count) }}</span>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Status</h2>
                
                <div class="space-y-2">
                    @if($exam->is_featured)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <i data-lucide="star" class="w-4 h-4 mr-1"></i>
                        Featured
                    </span>
                    @endif
                    
                    @if($exam->is_new)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i data-lucide="sparkles" class="w-4 h-4 mr-1"></i>
                        New
                    </span>
                    @endif
                    
                    @if($exam->has_marking_scheme)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                        Marking Scheme
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
@endsection

