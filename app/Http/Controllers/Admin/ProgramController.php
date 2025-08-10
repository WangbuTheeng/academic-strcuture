<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Department;
use App\Models\Level;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
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
        $query = Program::with(['department.faculty', 'level'])->withCount(['enrollments', 'subjects']);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Department filter
        if ($request->filled('department')) {
            $query->byDepartment($request->department);
        }

        // Level filter
        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        // Program type filter
        if ($request->filled('program_type')) {
            $query->byType($request->program_type);
        }

        // Active/Inactive filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $programs = $query->paginate(15);
        $departments = Department::all();
        $levels = Level::ordered()->get();
        $programTypes = ['semester', 'yearly'];

        return view('admin.academic.programs.index', compact('programs', 'departments', 'levels', 'programTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        $levels = Level::ordered()->get();
        $programTypes = ['semester', 'yearly'];

        return view('admin.academic.programs.create', compact('departments', 'levels', 'programTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'level_id' => 'required|exists:levels,id',
            'name' => 'required|string|max:100',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('programs')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
            ],
            'duration_years' => 'required|integer|min:1|max:10',
            'degree_type' => 'required|in:school,college,bachelor',
            'program_type' => 'required|in:semester,yearly',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Convert checkbox to boolean
        $validated['is_active'] = $request->has('is_active');

        // Add school_id to the validated data
        $validated['school_id'] = $schoolId;

        Program::create($validated);

        return redirect()->route('admin.programs.index')
                        ->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        $program->load(['department.faculty', 'level', 'enrollments.student', 'subjects', 'classes.level']);

        return view('admin.academic.programs.show', compact('program'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        $departments = Department::all();
        $levels = Level::ordered()->get();
        $programTypes = ['semester', 'yearly'];

        return view('admin.academic.programs.edit', compact('program', 'departments', 'levels', 'programTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'level_id' => 'required|exists:levels,id',
            'name' => 'required|string|max:100',
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('programs')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })->ignore($program->id)
            ],
            'duration_years' => 'required|integer|min:1|max:10',
            'program_type' => 'required|in:semester,yearly',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $program->update($validated);

        return redirect()->route('admin.programs.index')
                        ->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        // Check if program has related data
        if ($program->enrollments()->count() > 0) {
            return back()->with('error', 'Cannot delete program. It has student enrollments.');
        }

        $program->delete();

        return redirect()->route('admin.programs.index')
                        ->with('success', 'Program deleted successfully.');
    }

    /**
     * Handle bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'programs' => 'required|array',
            'programs.*' => 'exists:programs,id',
        ]);

        $programs = Program::whereIn('id', $request->programs);

        switch ($request->action) {
            case 'activate':
                $programs->update(['is_active' => true]);
                $message = 'Selected programs activated successfully.';
                break;
            case 'deactivate':
                $programs->update(['is_active' => false]);
                $message = 'Selected programs deactivated successfully.';
                break;
            case 'delete':
                // Check for related data before deletion
                $programsWithEnrollments = Program::whereIn('id', $request->programs)
                    ->whereHas('enrollments')
                    ->count();

                if ($programsWithEnrollments > 0) {
                    return back()->with('error', 'Some programs cannot be deleted as they have student enrollments.');
                }

                $programs->delete();
                $message = 'Selected programs deleted successfully.';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Show the program structure management page.
     */
    public function manageStructure(Program $program)
    {
        $program->load(['classes.level', 'classes.subjects', 'subjects', 'department', 'level']);

        $availableClasses = ClassModel::whereNotIn('id', $program->classes->pluck('id'))
                                     ->with('level')
                                     ->get();

        $availableSubjects = Subject::whereNotIn('id', $program->subjects->pluck('id'))
                                   ->get();

        return view('admin.academic.programs.manage-structure', compact(
            'program',
            'availableClasses',
            'availableSubjects'
        ));
    }

    /**
     * Add a class to the program.
     */
    public function addClass(Request $request, Program $program)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        // Check if class is already assigned
        if ($program->classes()->where('class_id', $validated['class_id'])->exists()) {
            return redirect()->back()->with('error', 'Class is already assigned to this program.');
        }

        $program->classes()->attach($validated['class_id']);

        return redirect()->back()->with('success', 'Class added to program successfully.');
    }

    /**
     * Remove a class from the program.
     */
    public function removeClass(Program $program, ClassModel $class)
    {
        $program->classes()->detach($class->id);

        return redirect()->back()->with('success', 'Class removed from program successfully.');
    }

    /**
     * Add a subject to the program.
     */
    public function addSubject(Request $request, Program $program)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'credit_hours' => 'required|numeric|min:0|max:10',
            'is_compulsory' => 'required|boolean',
        ]);

        // Check if subject is already assigned
        if ($program->subjects()->where('subject_id', $validated['subject_id'])->exists()) {
            return redirect()->back()->with('error', 'Subject is already assigned to this program.');
        }

        $program->subjects()->attach($validated['subject_id'], [
            'credit_hours' => $validated['credit_hours'],
            'is_compulsory' => $validated['is_compulsory'],
        ]);

        return redirect()->back()->with('success', 'Subject added to program successfully.');
    }

    /**
     * Remove a subject from the program.
     */
    public function removeSubject(Program $program, Subject $subject)
    {
        $program->subjects()->detach($subject->id);

        return redirect()->back()->with('success', 'Subject removed from program successfully.');
    }

    /**
     * Get classes for a program (API endpoint).
     */
    public function getClasses(Program $program)
    {
        $classes = $program->classes()->with('level')->get();

        return response()->json([
            'classes' => $classes
        ]);
    }
}
