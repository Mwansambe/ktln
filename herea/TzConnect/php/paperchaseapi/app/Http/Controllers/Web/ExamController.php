<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['subject', 'creator']);

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        // Filter by subject
        if ($request->has('subject') && $request->subject) {
            $query->where('subject_id', $request->subject);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(15);
        $subjects = Subject::orderBy('name')->get();
        $years = Exam::distinct()->pluck('year')->sort()->reverse();

        return view('exams.index', compact('exams', 'subjects', 'years'));
    }

    public function create()
    {
        $this->authorize('create', Exam::class);
        
        $subjects = Subject::orderBy('name')->get();
        $years = range(date('Y'), date('Y') - 10);
        $types = ['PRACTICE_PAPER', 'MOCK_PAPER', 'PAST_PAPER', 'NECTA_PAPER', 'REVISION_PAPER', 'JOINT_PAPER', 'PRE_NECTA'];

        return view('exams.create', compact('subjects', 'years', 'types'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Exam::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|unique:exams,code|max:50',
            'subject_id' => 'required|exists:subjects,id',
            'year' => 'required|string|max:10',
            'type' => 'required|string|in:PRACTICE_PAPER,MOCK_PAPER,PAST_PAPER,NECTA_PAPER,REVISION_PAPER,JOINT_PAPER,PRE_NECTA',
            'description' => 'nullable|string',
            'pdf' => 'nullable|file|mimes:pdf|max:51200',
            'marking_scheme' => 'nullable|file|mimes:pdf|max:51200',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
        ]);

        $exam = new Exam($validated);
        $exam->created_by = auth()->id();
        $exam->updated_by = auth()->id();

        // Handle PDF upload
        if ($request->hasFile('pdf')) {
            $path = $request->file('pdf')->store('exams', 'public');
            $exam->pdf_path = $path;
            $exam->pdf_name = $request->file('pdf')->getClientOriginalName();
            $exam->file_size = $request->file('pdf')->getSize();
        }

        // Handle marking scheme upload
        if ($request->hasFile('marking_scheme')) {
            $path = $request->file('marking_scheme')->store('marking-schemes', 'public');
            $exam->marking_scheme_path = $path;
            $exam->marking_scheme_name = $request->file('marking_scheme')->getClientOriginalName();
            $exam->marking_scheme_size = $request->file('marking_scheme')->getSize();
            $exam->has_marking_scheme = true;
        }

        $exam->save();

        // Update subject exam count
        $exam->subject->increment('exam_count');

        return redirect()->route('exams.index')->with('success', 'Exam created successfully.');
    }

    public function show(Exam $exam)
    {
        return view('exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $this->authorize('update', $exam);

        $subjects = Subject::orderBy('name')->get();
        $years = range(date('Y'), date('Y') - 10);
        $types = ['PRACTICE_PAPER', 'MOCK_PAPER', 'PAST_PAPER', 'NECTA_PAPER', 'REVISION_PAPER', 'JOINT_PAPER', 'PRE_NECTA'];

        return view('exams.edit', compact('exam', 'subjects', 'years', 'types'));
    }

    public function update(Request $request, Exam $exam)
    {
        $this->authorize('update', $exam);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|unique:exams,code,' . $exam->id . '|max:50',
            'subject_id' => 'required|exists:subjects,id',
            'year' => 'required|string|max:10',
            'type' => 'required|string|in:PRACTICE_PAPER,MOCK_PAPER,PAST_PAPER,NECTA_PAPER,REVISION_PAPER,JOINT_PAPER,PRE_NECTA',
            'description' => 'nullable|string',
            'pdf' => 'nullable|file|mimes:pdf|max:51200',
            'marking_scheme' => 'nullable|file|mimes:pdf|max:51200',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
        ]);

        $oldSubjectId = $exam->subject_id;
        $exam->fill($validated);
        $exam->updated_by = auth()->id();

        // Handle PDF upload
        if ($request->hasFile('pdf')) {
            // Delete old file
            if ($exam->pdf_path) {
                Storage::disk('public')->delete($exam->pdf_path);
            }
            $path = $request->file('pdf')->store('exams', 'public');
            $exam->pdf_path = $path;
            $exam->pdf_name = $request->file('pdf')->getClientOriginalName();
            $exam->file_size = $request->file('pdf')->getSize();
        }

        // Handle marking scheme upload
        if ($request->hasFile('marking_scheme')) {
            // Delete old file
            if ($exam->marking_scheme_path) {
                Storage::disk('public')->delete($exam->marking_scheme_path);
            }
            $path = $request->file('marking_scheme')->store('marking-schemes', 'public');
            $exam->marking_scheme_path = $path;
            $exam->marking_scheme_name = $request->file('marking_scheme')->getClientOriginalName();
            $exam->marking_scheme_size = $request->file('marking_scheme')->getSize();
            $exam->has_marking_scheme = true;
        }

        $exam->save();

        // Update subject exam counts
        if ($oldSubjectId != $exam->subject_id) {
            Subject::find($oldSubjectId)?->decrement('exam_count');
            $exam->subject->increment('exam_count');
        }

        return redirect()->route('exams.show', $exam)->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        $this->authorize('delete', $exam);

        // Delete files
        if ($exam->pdf_path) {
            Storage::disk('public')->delete($exam->pdf_path);
        }
        if ($exam->marking_scheme_path) {
            Storage::disk('public')->delete($exam->marking_scheme_path);
        }

        // Update subject count
        $exam->subject->decrement('exam_count');

        $exam->delete();

        return redirect()->route('exams.index')->with('success', 'Exam deleted successfully.');
    }
}

