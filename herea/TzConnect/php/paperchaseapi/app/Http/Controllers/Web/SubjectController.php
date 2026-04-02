<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::query();

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Filter by active status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->boolean('status'));
        }

        $subjects = $query->withCount('exams')->orderBy('name')->paginate(15);

        return view('categories.index', compact('subjects'));
    }

    public function create()
    {
        $this->authorize('create', Subject::class);

        return view('categories.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Subject::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'bg_color' => 'nullable|string|max:7',
            'border_color' => 'nullable|string|max:7',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        Subject::create($validated);

        return redirect()->route('categories.index')->with('success', 'Subject created successfully.');
    }

    public function show(Subject $subject)
    {
        $subject->load(['exams' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        return view('categories.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        $this->authorize('update', $subject);

        return view('categories.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $this->authorize('update', $subject);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'bg_color' => 'nullable|string|max:7',
            'border_color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        if ($subject->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $subject->update($validated);

        return redirect()->route('categories.show', $subject)->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        $this->authorize('delete', $subject);

        // Check if subject has exams
        if ($subject->exams()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Cannot delete subject with existing exams.');
        }

        $subject->delete();

        return redirect()->route('categories.index')->with('success', 'Subject deleted successfully.');
    }

    public function recalculateCount(Subject $subject)
    {
        $this->authorize('update', $subject);

        $count = $subject->exams()->count();
        $subject->update(['exam_count' => $count]);

        return back()->with('success', "Exam count updated to {$count}.");
    }
}

