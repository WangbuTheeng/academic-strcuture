<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\Program;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeStructureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Temporarily removed permission check for testing
        // $this->middleware('can:manage-fees');
    }

    /**
     * Display a listing of the fee structures.
     */
    public function index(Request $request)
    {
        $query = FeeStructure::with(['academicYear', 'level', 'program', 'class']);

        // Filter by academic year
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Filter by level
        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }

        // Filter by program
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        // Filter by fee category
        if ($request->filled('fee_category')) {
            $query->where('fee_category', $request->fee_category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fee_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $feeStructures = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $academicYears = AcademicYear::orderBy('name')->get();
        $levels = Level::orderBy('name')->get();
        $programs = Program::orderBy('name')->get();
        $feeCategories = FeeStructure::getFeeCategories();

        return view('admin.fee-structures.index', compact(
            'feeStructures',
            'academicYears',
            'levels',
            'programs',
            'feeCategories'
        ));
    }

    /**
     * Show the form for creating a new fee structure.
     */
    public function create()
    {
        $academicYears = AcademicYear::orderBy('name')->get();
        $levels = Level::orderBy('name')->get();
        $programs = Program::orderBy('name')->get();
        $classes = ClassModel::orderBy('name')->get();
        $feeCategories = FeeStructure::getFeeCategories();
        $billingFrequencies = FeeStructure::getBillingFrequencies();

        return view('admin.fee-structures.create', compact(
            'academicYears',
            'levels',
            'programs',
            'classes',
            'feeCategories',
            'billingFrequencies'
        ));
    }

    /**
     * Store a newly created fee structure in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'fee_category' => 'required|string|max:100',
            'fee_name' => 'required|string|max:150',
            'amount' => 'required|numeric|min:0',
            'billing_frequency' => 'required|in:monthly,quarterly,semester,annual',
            'due_date_offset' => 'required|integer|min:0',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'grace_period_days' => 'nullable|integer|min:0',
        ]);

        FeeStructure::create($request->all());

        return redirect()->route('admin.fees.structures.index')
            ->with('success', 'Fee structure created successfully.');
    }

    /**
     * Display the specified fee structure.
     */
    public function show(FeeStructure $feeStructure)
    {
        $feeStructure->load(['academicYear', 'level', 'program', 'class']);
        
        return view('admin.fee-structures.show', compact('feeStructure'));
    }

    /**
     * Show the form for editing the specified fee structure.
     */
    public function edit(FeeStructure $feeStructure)
    {
        $academicYears = AcademicYear::orderBy('name')->get();
        $levels = Level::orderBy('name')->get();
        $programs = Program::orderBy('name')->get();
        $classes = ClassModel::orderBy('name')->get();
        $feeCategories = FeeStructure::getFeeCategories();
        $billingFrequencies = FeeStructure::getBillingFrequencies();

        return view('admin.fee-structures.edit', compact(
            'feeStructure',
            'academicYears',
            'levels',
            'programs',
            'classes',
            'feeCategories',
            'billingFrequencies'
        ));
    }

    /**
     * Update the specified fee structure in storage.
     */
    public function update(Request $request, FeeStructure $feeStructure)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'fee_category' => 'required|string|max:100',
            'fee_name' => 'required|string|max:150',
            'amount' => 'required|numeric|min:0',
            'billing_frequency' => 'required|in:monthly,quarterly,semester,annual',
            'due_date_offset' => 'required|integer|min:0',
            'late_fee_amount' => 'nullable|numeric|min:0',
            'grace_period_days' => 'nullable|integer|min:0',
        ]);

        $feeStructure->update($request->all());

        return redirect()->route('admin.fees.structures.index')
            ->with('success', 'Fee structure updated successfully.');
    }

    /**
     * Remove the specified fee structure from storage.
     */
    public function destroy(FeeStructure $feeStructure)
    {
        $feeStructure->delete();

        return redirect()->route('admin.fees.structures.index')
            ->with('success', 'Fee structure deleted successfully.');
    }

    /**
     * Toggle the active status of the fee structure.
     */
    public function toggleStatus(FeeStructure $structure)
    {
        $structure->update([
            'is_active' => !$structure->is_active
        ]);

        $status = $structure->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Fee structure {$status} successfully.");
    }

    /**
     * Get programs by level (AJAX).
     */
    public function getProgramsByLevel(Request $request)
    {
        $programs = Program::where('level_id', $request->level_id)
                          ->orderBy('name')
                          ->get(['id', 'name']);

        return response()->json($programs);
    }

    /**
     * Get classes by program (AJAX).
     */
    public function getClassesByProgram(Request $request)
    {
        $classes = ClassModel::where('program_id', $request->program_id)
                            ->orderBy('name')
                            ->get(['id', 'name']);

        return response()->json($classes);
    }
}
