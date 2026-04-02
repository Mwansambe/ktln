@extends('layouts.app')

@section('title', 'Categories - PaperChase')

@section('breadcrumb')
<li class="flex items-center">
    <span class="text-gray-400">/</span>
    <span class="ml-2 text-gray-900 font-medium">Categories</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
            <p class="text-gray-500 mt-1">Manage your exam subjects and categories.</p>
        </div>
        @can('create', \App\Models\Subject::class)
        <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add New Category
        </a>
        @endcan
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($subjects as $subject)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: {{ $subject->bg_color ?? '#e0e7ff' }}">
                        <i data-lucide="folder" class="w-6 h-6" style="color: {{ $subject->color ?? '#4f46e5' }}"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $subject->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $subject->exam_count }} exams</p>
                    </div>
                </div>
                @if(!$subject->is_active)
                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600">
                    Inactive
                </span>
                @endif
            </div>

            @if($subject->description)
            <p class="mt-3 text-sm text-gray-600 line-clamp-2">{{ $subject->description }}</p>
            @endif

            <div class="mt-4 flex items-center gap-2">
                <a href="{{ route('categories.show', $subject) }}" class="flex-1 px-3 py-2 text-center text-sm text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                    View
                </a>
                @can('update', $subject)
                <a href="{{ route('categories.edit', $subject) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                </a>
                @endcan
                @can('delete', $subject)
                <form action="{{ route('categories.destroy', $subject) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors" onclick="return confirm('Are you sure?')">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
                @endcan
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="folder-x" class="w-8 h-8 text-gray-400"></i>
                </div>
                <p class="text-gray-500">No categories yet</p>
                @can('create', \App\Models\Subject::class)
                <a href="{{ route('categories.create') }}" class="text-indigo-600 hover:text-indigo-700 text-sm mt-2 inline-block">
                    Create your first category
                </a>
                @endcan
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($subjects->hasPages())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-4">
        {{ $subjects->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
@endsection

