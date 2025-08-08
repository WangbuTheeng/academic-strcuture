<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
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
        // Ensure we're working with the current school's data
        $query = AcademicYear::withCount(['enrollments', 'exams']);

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'current') {
                $query->current();
            } elseif ($request->status === 'past') {
                $query->where('is_current', false)->where('end_date', '<', now());
            } elseif ($request->status === 'future') {
                $query->where('is_current', false)->where('start_date', '>', now());
            }
        }

        $academicYears = $query->orderBy('start_date', 'desc')->paginate(15);

        return view('admin.academic.academic-years.index', compact('academicYears'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.academic.academic-years.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user has school_id
        if (!auth()->user()->school_id) {
            \Log::error('Academic Year Creation Error: User has no school_id', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email
            ]);

            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Your account is not associated with a school. Please contact the administrator.');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('academic_years')->where(function ($query) {
                    return $query->where('school_id', auth()->user()->school_id);
                })
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ], [
            'name.unique' => 'This academic year already exists in your school. Please choose a different year.',
            'end_date.after' => 'The end date must be after the start date.',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Add school_id to the validated data
                $validated['school_id'] = auth()->user()->school_id;

                // If this is set as current, unset all other current academic years for this school
                if ($validated['is_current'] ?? false) {
                    AcademicYear::where('school_id', auth()->user()->school_id)
                               ->where('is_current', true)
                               ->update(['is_current' => false]);
                }

                $academicYear = AcademicYear::create($validated);

                \Log::info('Academic Year Created Successfully', [
                    'academic_year_id' => $academicYear->id,
                    'name' => $academicYear->name,
                    'school_id' => $academicYear->school_id,
                    'user_id' => auth()->id()
                ]);
            });

            return redirect()->route('admin.academic-years.index')
                            ->with('success', 'Academic year created successfully.');

        } catch (\Exception $e) {
            \Log::error('Academic Year Creation Error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'school_id' => auth()->user()->school_id ?? 'NULL',
                'validated_data' => $validated ?? 'NULL',
                'exception' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Failed to create academic year: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        $academicYear->load(['enrollments.student', 'enrollments.class', 'enrollments.program', 'exams']);

        return view('admin.academic.academic-years.show', compact('academicYear'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic.academic-years.edit', compact('academicYear'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('academic_years')->ignore($academicYear->id)->where(function ($query) {
                    return $query->where('school_id', auth()->user()->school_id);
                })
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        DB::transaction(function () use ($validated, $academicYear) {
            // If this is set as current, unset all other current academic years
            if ($validated['is_current'] ?? false) {
                AcademicYear::where('is_current', true)->where('id', '!=', $academicYear->id)->update(['is_current' => false]);
            }

            $academicYear->update($validated);
        });

        return redirect()->route('admin.academic-years.index')
                        ->with('success', 'Academic year updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {
        // Check if academic year has related data
        if ($academicYear->enrollments()->count() > 0 || $academicYear->exams()->count() > 0) {
            return back()->with('error', 'Cannot delete academic year. It has related enrollments or exams.');
        }

        // Don't allow deletion of current academic year
        if ($academicYear->is_current) {
            return back()->with('error', 'Cannot delete the current academic year.');
        }

        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
                        ->with('success', 'Academic year deleted successfully.');
    }

    /**
     * Set academic year as current.
     */
    public function setCurrent(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {
            // Unset all other current academic years
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
            
            // Set this as current
            $academicYear->update(['is_current' => true]);
        });

        return back()->with('success', 'Academic year set as current successfully.');
    }

    /**
     * Handle bulk actions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete',
            'academic_years' => 'required|array',
            'academic_years.*' => 'exists:academic_years,id',
        ]);

        $academicYears = AcademicYear::whereIn('id', $request->academic_years);

        switch ($request->action) {
            case 'delete':
                // Check for related data and current status before deletion
                $academicYearsWithRelations = AcademicYear::whereIn('id', $request->academic_years)
                    ->where(function($query) {
                        $query->whereHas('enrollments')
                              ->orWhereHas('exams')
                              ->orWhere('is_current', true);
                    })
                    ->count();

                if ($academicYearsWithRelations > 0) {
                    return back()->with('error', 'Some academic years cannot be deleted as they have related data or are current.');
                }

                $academicYears->delete();
                $message = 'Selected academic years deleted successfully.';
                break;
        }

        return back()->with('success', $message);
    }
}
