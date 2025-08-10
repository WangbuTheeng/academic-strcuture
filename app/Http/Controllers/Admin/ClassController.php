<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Level;
use App\Models\Department;
use App\Models\Program;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassController extends Controller
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
        $query = ClassModel::with(['level', 'department'])
                          ->withCount(['enrollments', 'programs']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('level', function ($levelQuery) use ($search) {
                      $levelQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department', function ($deptQuery) use ($search) {
                      $deptQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Level filter
        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        // Department filter
        if ($request->filled('department')) {
            $query->byDepartment($request->department);
        }

        // Active/Inactive filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $classes = $query->paginate(15);
        $levels = Level::ordered()->get();
        $departments = Department::all();

        return view('admin.academic.classes.index', compact('classes', 'levels', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $levels = Level::ordered()->get();
        $departments = Department::all();

        return view('admin.academic.classes.create', compact('levels', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:100',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('classes')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
            ],
            'is_active' => 'boolean',
        ]);

        // Add school_id to the validated data
        $validated['school_id'] = $schoolId;

        ClassModel::create($validated);

        return redirect()->route('admin.classes.index')
                        ->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassModel $class)
    {
        $class->load(['level', 'department', 'enrollments.student', 'programs', 'subjects']);
        $availableSubjects = Subject::where('is_active', true)
                                   ->whereNotIn('id', $class->subjects->pluck('id'))
                                   ->orderBy('name')
                                   ->get();

        return view('admin.academic.classes.show', compact('class', 'availableSubjects'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassModel $class)
    {
        $levels = Level::ordered()->get();
        $departments = Department::all();

        return view('admin.academic.classes.edit', compact('class', 'levels', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassModel $class)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:100',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('classes')->ignore($class->id)->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
            ],
            'is_active' => 'boolean',
        ]);

        $class->update($validated);

        return redirect()->route('admin.classes.index')
                        ->with('success', 'Class updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassModel $class)
    {
        // Check if class has related data
        if ($class->enrollments()->count() > 0) {
            return back()->with('error', 'Cannot delete class. It has student enrollments.');
        }

        $class->delete();

        return redirect()->route('admin.classes.index')
                        ->with('success', 'Class deleted successfully.');
    }

    /**
     * Handle bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'classes' => 'required|array',
            'classes.*' => 'exists:classes,id',
        ]);

        $classes = ClassModel::whereIn('id', $request->classes);

        switch ($request->action) {
            case 'activate':
                $classes->update(['is_active' => true]);
                $message = 'Selected classes activated successfully.';
                break;
            case 'deactivate':
                $classes->update(['is_active' => false]);
                $message = 'Selected classes deactivated successfully.';
                break;
            case 'delete':
                // Check for related data before deletion
                $classesWithEnrollments = ClassModel::whereIn('id', $request->classes)
                    ->whereHas('enrollments')
                    ->count();

                if ($classesWithEnrollments > 0) {
                    return back()->with('error', 'Some classes cannot be deleted as they have student enrollments.');
                }

                $classes->delete();
                $message = 'Selected classes deleted successfully.';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Add a subject to the class.
     */
    public function addSubject(Request $request, ClassModel $class)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'credit_hours' => 'nullable|numeric|min:0|max:10',
            'is_compulsory' => 'nullable|boolean',
        ]);

        // Check if subject is already assigned
        if ($class->subjects()->where('subject_id', $validated['subject_id'])->exists()) {
            return redirect()->back()->with('error', 'Subject is already assigned to this class.');
        }

        // Get the subject to use its default credit hours if not provided
        $subject = Subject::find($validated['subject_id']);

        $class->subjects()->attach($validated['subject_id'], [
            'credit_hours' => $validated['credit_hours'] ?? $subject->credit_hours ?? 3,
            'is_compulsory' => $validated['is_compulsory'] ?? true,
            'year_no' => null,
            'semester_id' => null,
            'sort_order' => 0,
        ]);

        return redirect()->back()->with('success', 'Subject added to class successfully.');
    }

    /**
     * Remove a subject from the class.
     */
    public function removeSubject(ClassModel $class, Subject $subject)
    {
        $class->subjects()->detach($subject->id);

        return redirect()->back()->with('success', 'Subject removed from class successfully.');
    }
}
