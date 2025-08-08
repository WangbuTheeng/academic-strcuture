<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
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
        $query = Department::with('faculty')->withCount(['programs', 'classes', 'subjects']);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Faculty filter
        if ($request->filled('faculty')) {
            $query->byFaculty($request->faculty);
        }

        $departments = $query->paginate(15);
        $faculties = Faculty::all();

        return view('admin.academic.departments.index', compact('departments', 'faculties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $faculties = Faculty::all();
        return view('admin.academic.departments.create', compact('faculties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'name' => 'required|string|max:100',
            'code' => [
                'nullable',
                'string',
                'max:10',
                Rule::unique('departments')->where(function ($query) {
                    return $query->where('school_id', auth()->user()->school_id);
                })
            ],
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        $department->load(['faculty', 'programs', 'classes', 'subjects']);

        return view('admin.academic.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        $faculties = Faculty::all();
        return view('admin.academic.departments.edit', compact('department', 'faculties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'name' => 'required|string|max:100',
            'code' => [
                'nullable',
                'string',
                'max:10',
                Rule::unique('departments')->ignore($department->id)->where(function ($query) {
                    return $query->where('school_id', auth()->user()->school_id);
                })
            ],
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        // Check if department has programs, classes, or subjects
        if ($department->programs()->count() > 0 ||
            $department->classes()->count() > 0 ||
            $department->subjects()->count() > 0) {
            return redirect()->route('admin.departments.index')
                            ->with('error', 'Cannot delete department with existing programs, classes, or subjects.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Department deleted successfully.');
    }

    /**
     * Bulk operations
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete',
            'departments' => 'required|array',
            'departments.*' => 'exists:departments,id'
        ]);

        $departments = Department::whereIn('id', $validated['departments']);

        switch ($validated['action']) {
            case 'delete':
                // Check if any department has related data
                $departmentsWithData = $departments->where(function ($query) {
                    $query->whereHas('programs')
                          ->orWhereHas('classes')
                          ->orWhereHas('subjects');
                })->count();

                if ($departmentsWithData > 0) {
                    return redirect()->route('admin.departments.index')
                                    ->with('error', 'Cannot delete departments that have programs, classes, or subjects.');
                }

                $departments->delete();
                $message = 'Selected departments deleted successfully.';
                break;
        }

        return redirect()->route('admin.departments.index')
                        ->with('success', $message);
    }
}
