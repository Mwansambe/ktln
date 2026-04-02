@extends('layouts.app')

@section('title', 'Edit Category - PaperChase')

@section('breadcrumb')
<li><a href="{{ route('categories.index') }}" class="text-gray-400 hover:text-gray-600">Categories</a></li>
<li><span class="text-gray-400">/</span></li>
<li><a href="{{ route('categories.show', $subject) }}" class="text-gray-400 hover:text-gray-600">{{ $subject->name }}</a></li>
<li><span class="text-gray-400">/</span></li>
<li class="ml-2 text-gray-900 font-medium">Edit</li>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Category</h1>
            <p class="text-gray-500 mt-1">Update category details.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <form method="POST" action="{{ route('categories.update', $subject) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name', $subject->name) }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >{{ old('description', $subject->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Text Color</label>
                    <input type="color" name="color" id="color" value="{{ old('color', $subject->color ?? '#4f46e5') }}" class="w-full h-10 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label for="bg_color" class="block text-sm font-medium text-gray-700 mb-2">Background Color</label>
                    <input type="color" name="bg_color" id="bg_color" value="{{ old('bg_color', $subject->bg_color ?? '#e0e7ff') }}" class="w-full h-10 rounded-lg border border-gray-300">
                </div>
                <div>
                    <label for="border_color" class="block text-sm font-medium text-gray-700 mb-2">Border Color</label>
                    <input type="color" name="border_color" id="border_color" value="{{ old('border_color', $subject->border_color ?? '#c7d2fe') }}" class="w-full h-10 rounded-lg border border-gray-300">
                </div>
            </div>

            <div class="flex items-center gap-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subject->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t">
                <a href="{{ route('categories.show', $subject) }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

