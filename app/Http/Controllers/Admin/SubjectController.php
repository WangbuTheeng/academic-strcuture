<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage-academic-structure']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subject::with('department')->withCount(['programs', 'teacherSubjects']);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Department filter
        if ($request->filled('department')) {
            $query->byDepartment($request->department);
        }

        // Subject type filter
        if ($request->filled('subject_type')) {
            $query->byType($request->subject_type);
        }

        // Active/Inactive filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $subjects = $query->paginate(15);
        $departments = Department::all();

        return view('admin.academic.subjects.index', compact('subjects', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        $subjectTypes = ['core', 'elective', 'practical', 'project'];

        return view('admin.academic.subjects.create', compact('departments', 'subjectTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:100',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('subjects')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
            ],
            'credit_hours' => 'required|numeric|min:0|max:10',
            'subject_type' => 'required|in:core,elective,practical,project',
            'max_assess' => 'nullable|integer|min:0|max:100',
            'max_theory' => 'nullable|integer|min:0|max:100',
            'max_practical' => 'nullable|integer|min:0|max:100',
            'is_practical' => 'boolean',
            'has_internal' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Add school_id to the validated data
        $validated['school_id'] = $schoolId;

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
                        ->with('success', 'Subject created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        $subject->load(['department', 'programs', 'teacherSubjects.user', 'marks']);

        return view('admin.academic.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        $departments = Department::all();
        $subjectTypes = ['core', 'elective', 'practical', 'project'];

        return view('admin.academic.subjects.edit', compact('subject', 'departments', 'subjectTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:100',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('subjects')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })->ignore($subject->id)
            ],
            'credit_hours' => 'required|numeric|min:0|max:10',
            'subject_type' => 'required|in:core,elective,practical,project',
            'max_assess' => 'nullable|integer|min:0|max:100',
            'max_theory' => 'nullable|integer|min:0|max:100',
            'max_practical' => 'nullable|integer|min:0|max:100',
            'is_practical' => 'boolean',
            'has_internal' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')
                        ->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        // Check if subject has related data
        if ($subject->programs()->count() > 0 || $subject->teacherSubjects()->count() > 0) {
            return back()->with('error', 'Cannot delete subject. It has related programs or teacher assignments.');
        }

        $subject->delete();

        return redirect()->route('admin.subjects.index')
                        ->with('success', 'Subject deleted successfully.');
    }

    /**
     * Handle bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'subjects' => 'required|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        $subjects = Subject::whereIn('id', $request->subjects);

        switch ($request->action) {
            case 'activate':
                $subjects->update(['is_active' => true]);
                $message = 'Selected subjects activated successfully.';
                break;
            case 'deactivate':
                $subjects->update(['is_active' => false]);
                $message = 'Selected subjects deactivated successfully.';
                break;
            case 'delete':
                // Check for related data before deletion
                $subjectsWithRelations = Subject::whereIn('id', $request->subjects)
                    ->whereHas('programs')
                    ->orWhereHas('teacherSubjects')
                    ->count();

                if ($subjectsWithRelations > 0) {
                    return back()->with('error', 'Some subjects cannot be deleted as they have related data.');
                }

                $subjects->delete();
                $message = 'Selected subjects deleted successfully.';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Update the status of the specified resource in storage.
     */
    public function updateStatus(Request $request, Subject $subject)
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $subject->update(['is_active' => $request->is_active]);

        return back()->with('success', 'Subject status updated successfully.');
    }
}
