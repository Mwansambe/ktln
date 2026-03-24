<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount('exams')->latest()->paginate(20);
        $stats    = [
            'total'         => Subject::count(),
            'total_exams'   => \App\Models\Exam::count(),
            'avg_per_subject' => Subject::withCount('exams')->get()->avg('exams_count'),
        ];
        return view('admin.subjects.index', compact('subjects', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:20|unique:subjects,code',
            'description' => 'nullable|string|max:300',
            'color'       => 'nullable|string|max:10',
        ]);

        Subject::create(array_merge($request->only('name', 'code', 'description', 'color'), [
            'is_active'  => true,
            'created_by' => auth()->id(),
        ]));

        return redirect()->route('admin.categories')->with('success', 'Subject created.');
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'color' => 'nullable|string|max:10',
        ]);

        $subject->update($request->only('name', 'description', 'color', 'is_active'));

        return redirect()->route('admin.categories')->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->exams()->count() > 0) {
            return back()->with('error', 'Cannot delete subject with existing exams.');
        }
        $subject->delete();
        return redirect()->route('admin.categories')->with('success', 'Subject deleted.');
    }
}
