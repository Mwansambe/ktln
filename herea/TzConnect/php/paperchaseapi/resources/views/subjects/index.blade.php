@extends('layouts.app')

@section('title', 'Subjects - PaperChase')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Subjects</h1>
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('subjects.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Add Subject</a>
            @endif
        @endauth
    </div>
    
    <!-- Subjects Grid -->
    @if($subjects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($subjects as $subject)
            <a href="{{ route('exams.index') }}?subject={{ $subject->id }}" class="group">
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden cursor-pointer h-full">
                    <div class="p-6 flex flex-col h-full">
                        <div class="flex items-center mb-4">
                            <div 
                                class="w-12 h-12 rounded-full flex items-center justify-center text-2xl mr-4"
                                style="background-color: {{ $subject->bg_color ?? '#e0e7ff' }}; color: {{ $subject->color ?? '#4f46e5' }}"
                            >
                                {{ $subject->icon ?? '📚' }}
                            </div>
                            <h3 class="font-bold text-lg">{{ $subject->name }}</h3>
                        </div>
                        
                        @if($subject->description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $subject->description }}</p>
                        @endif
                        
                        <div class="mt-auto">
                            <p class="text-2xl font-bold text-indigo-600 mb-2">{{ $subject->exam_count }}</p>
                            <p class="text-gray-500 text-sm">{{ $subject->exam_count == 1 ? 'exam' : 'exams' }}</p>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $subjects->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">No subjects available yet.</p>
        </div>
    @endif
</div>
@endsection
