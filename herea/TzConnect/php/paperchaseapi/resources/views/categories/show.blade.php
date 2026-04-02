@extends('layouts.app')

@section('title', $subject->name . ' - PaperChase')

@section('breadcrumb')
<li><a href="{{ route('categories.index') }}" class="text-gray-400 hover:text-gray-600">Categories</a></li>
<li><span class="text-gray-400">/</span></li>
<li class="ml-2 text-gray-900 font-medium">{{ $subject->name }}</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: {{ $subject->bg_color ?? '#e0e7ff' }}">
                <i data-lucide="folder" class="w-6 h-6" style="color: {{ $subject->color ?? '#4f46e5' }}"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $subject->name }}</h1>
                <p class="text-gray-500">{{ $subject->exam_count }} exams</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @can('update', $subject)
            <a href="{{ route('categories.edit', $subject) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="pencil" class="w-4 h-4 mr-2"></i>
                Edit
            </a>
            @endcan
            @can('delete', $subject)
            <form action="{{ route('categories.destroy', $subject) }}" method="POST" class="inline">
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

    @if($subject->description)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-2">Description</h2>
        <p class="text-gray-600">{{ $subject->description }}</p>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Exams in this Category</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Downloads</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subject->exams as $exam)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $subject->bg_color ?? '#e0e7ff' }}">
                                    <i data-lucide="file-text" class="w-5 h-5" style="color: {{ $subject->color ?? '#4f46e5' }}"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $exam->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $exam->code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $exam->year }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ str_replace('_', ' ', $exam->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($exam->download_count) }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('exams.show', $exam) }}" class="text-indigo-600 hover:text-indigo-700">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No exams in this category yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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

