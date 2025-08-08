<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FacultyController extends Controller
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
        $query = Faculty::withCount(['departments', 'programs', 'classes']);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $faculties = $query->paginate(15);

        return view('admin.academic.faculties.index', compact('faculties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.academic.faculties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $schoolId = session('school_context') ?? auth()->user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('faculties')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('faculties')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
            ],
        ]);

        Faculty::create($validated);

        return redirect()->route('admin.faculties.index')
                        ->with('success', 'Faculty created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty)
    {
        $faculty->load(['departments.programs', 'departments.classes']);

        return view('admin.academic.faculties.show', compact('faculty'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faculty $faculty)
    {
        return view('admin.academic.faculties.edit', compact('faculty'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faculty $faculty)
    {
        $schoolId = session('school_context') ?? auth()->user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('faculties')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })->ignore($faculty->id)
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('faculties')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })->ignore($faculty->id)
            ],
        ]);

        $faculty->update($validated);

        return redirect()->route('admin.faculties.index')
                        ->with('success', 'Faculty updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty)
    {
        // Check if faculty has departments
        if ($faculty->departments()->count() > 0) {
            return redirect()->route('admin.faculties.index')
                            ->with('error', 'Cannot delete faculty with existing departments.');
        }

        $faculty->delete();

        return redirect()->route('admin.faculties.index')
                        ->with('success', 'Faculty deleted successfully.');
    }

    /**
     * Bulk operations
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete',
            'faculties' => 'required|array',
            'faculties.*' => 'exists:faculties,id'
        ]);

        $faculties = Faculty::whereIn('id', $validated['faculties']);

        switch ($validated['action']) {
            case 'delete':
                // Check if any faculty has departments
                $facultiesWithDepartments = $faculties->whereHas('departments')->count();
                if ($facultiesWithDepartments > 0) {
                    return redirect()->route('admin.faculties.index')
                                    ->with('error', 'Cannot delete faculties that have departments.');
                }

                $faculties->delete();
                $message = 'Selected faculties deleted successfully.';
                break;
        }

        return redirect()->route('admin.faculties.index')
                        ->with('success', $message);
    }
}
