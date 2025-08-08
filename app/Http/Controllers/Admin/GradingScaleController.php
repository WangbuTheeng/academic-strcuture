<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradingScale;
use App\Models\GradeRange;
use App\Models\Level;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GradingScaleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage-system']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = GradingScale::with(['level', 'program', 'creator', 'gradeRanges']);

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
        }

        // Level filter
        if ($request->filled('level')) {
            $query->where('level_id', $request->level);
        }

        // Program filter
        if ($request->filled('program')) {
            $query->where('program_id', $request->program);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $gradingScales = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $levels = Level::all();
        $programs = Program::with('level')->get();

        return view('admin.grading-scales.index', compact('gradingScales', 'levels', 'programs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $levels = Level::all();
        $programs = Program::with('level')->get();

        return view('admin.grading-scales.create', compact('levels', 'programs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'level_id' => 'nullable|exists:levels,id',
            'program_id' => 'nullable|exists:programs,id',
            'pass_mark' => 'required|numeric|min:0|max:100',
            'max_marks' => 'required|numeric|min:1',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'grade_ranges' => 'required|array|min:1',
            'grade_ranges.*.grade' => 'required|string|max:5',
            'grade_ranges.*.min_percentage' => 'required|numeric|min:0|max:100',
            'grade_ranges.*.max_percentage' => 'required|numeric|min:0|max:100',
            'grade_ranges.*.gpa' => 'required|numeric|min:0|max:4',
            'grade_ranges.*.description' => 'nullable|string|max:255',
            'grade_ranges.*.is_passing' => 'boolean',
        ]);

        // Validate grade ranges don't overlap
        $this->validateGradeRanges($validated['grade_ranges']);

        DB::transaction(function () use ($validated) {
            // If this is set as default, unset other defaults for same scope
            if ($validated['is_default'] ?? false) {
                $this->unsetOtherDefaults($validated['level_id'] ?? null, $validated['program_id'] ?? null);
            }

            $gradingScale = GradingScale::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'level_id' => $validated['level_id'],
                'program_id' => $validated['program_id'],
                'pass_mark' => $validated['pass_mark'],
                'max_marks' => $validated['max_marks'],
                'is_default' => $validated['is_default'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
                'created_by' => auth()->id(),
            ]);

            // Create grade ranges
            foreach ($validated['grade_ranges'] as $rangeData) {
                $gradingScale->gradeRanges()->create([
                    'grade' => $rangeData['grade'],
                    'min_percentage' => $rangeData['min_percentage'],
                    'max_percentage' => $rangeData['max_percentage'],
                    'gpa' => $rangeData['gpa'],
                    'description' => $rangeData['description'],
                    'is_passing' => $rangeData['is_passing'] ?? true,
                ]);
            }
        });

        return redirect()->route('admin.grading-scales.index')
                        ->with('success', 'Grading scale created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(GradingScale $gradingScale)
    {
        $gradingScale->load(['level', 'program', 'creator', 'gradeRanges']);
        $statistics = $gradingScale->getStatistics();

        return view('admin.grading-scales.show', compact('gradingScale', 'statistics'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GradingScale $gradingScale)
    {
        $gradingScale->load(['gradeRanges']);
        $levels = Level::all();
        $programs = Program::with('level')->get();

        return view('admin.grading-scales.edit', compact('gradingScale', 'levels', 'programs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GradingScale $gradingScale)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'level_id' => 'nullable|exists:levels,id',
            'program_id' => 'nullable|exists:programs,id',
            'pass_mark' => 'required|numeric|min:0|max:100',
            'max_marks' => 'required|numeric|min:1',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'grade_ranges' => 'required|array|min:1',
            'grade_ranges.*.grade' => 'required|string|max:5',
            'grade_ranges.*.min_percentage' => 'required|numeric|min:0|max:100',
            'grade_ranges.*.max_percentage' => 'required|numeric|min:0|max:100',
            'grade_ranges.*.gpa' => 'required|numeric|min:0|max:4',
            'grade_ranges.*.description' => 'nullable|string|max:255',
            'grade_ranges.*.is_passing' => 'boolean',
        ]);

        // Validate grade ranges don't overlap
        $this->validateGradeRanges($validated['grade_ranges']);

        DB::transaction(function () use ($validated, $gradingScale) {
            // If this is set as default, unset other defaults for same scope
            if ($validated['is_default'] ?? false) {
                $this->unsetOtherDefaults(
                    $validated['level_id'] ?? null,
                    $validated['program_id'] ?? null,
                    $gradingScale->id
                );
            }

            $gradingScale->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'level_id' => $validated['level_id'],
                'program_id' => $validated['program_id'],
                'pass_mark' => $validated['pass_mark'],
                'max_marks' => $validated['max_marks'],
                'is_default' => $validated['is_default'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Delete existing grade ranges and create new ones
            $gradingScale->gradeRanges()->delete();

            foreach ($validated['grade_ranges'] as $rangeData) {
                $gradingScale->gradeRanges()->create([
                    'grade' => $rangeData['grade'],
                    'min_percentage' => $rangeData['min_percentage'],
                    'max_percentage' => $rangeData['max_percentage'],
                    'gpa' => $rangeData['gpa'],
                    'description' => $rangeData['description'],
                    'is_passing' => $rangeData['is_passing'] ?? true,
                ]);
            }
        });

        return redirect()->route('admin.grading-scales.index')
                        ->with('success', 'Grading scale updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GradingScale $gradingScale)
    {
        // Check if grading scale is being used
        if ($gradingScale->exams()->count() > 0) {
            return back()->with('error', 'Cannot delete grading scale that is being used by exams.');
        }

        $gradingScale->delete();

        return redirect()->route('admin.grading-scales.index')
                        ->with('success', 'Grading scale deleted successfully.');
    }

    /**
     * Toggle the active status of a grading scale.
     */
    public function toggleStatus(GradingScale $gradingScale)
    {
        $gradingScale->update(['is_active' => !$gradingScale->is_active]);

        $status = $gradingScale->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Grading scale {$status} successfully.");
    }

    /**
     * Set as default grading scale.
     */
    public function setDefault(GradingScale $gradingScale)
    {
        DB::transaction(function () use ($gradingScale) {
            // Unset other defaults for same scope
            $this->unsetOtherDefaults(
                $gradingScale->level_id,
                $gradingScale->program_id,
                $gradingScale->id
            );

            $gradingScale->update(['is_default' => true]);
        });

        return back()->with('success', 'Grading scale set as default successfully.');
    }

    /**
     * Validate that grade ranges don't overlap.
     */
    private function validateGradeRanges(array $ranges): void
    {
        // Sort ranges by min_percentage
        usort($ranges, function($a, $b) {
            return $a['min_percentage'] <=> $b['min_percentage'];
        });

        for ($i = 0; $i < count($ranges) - 1; $i++) {
            if ($ranges[$i]['max_percentage'] >= $ranges[$i + 1]['min_percentage']) {
                throw new \InvalidArgumentException('Grade ranges cannot overlap.');
            }
        }

        // Validate each range
        foreach ($ranges as $range) {
            if ($range['min_percentage'] > $range['max_percentage']) {
                throw new \InvalidArgumentException('Minimum percentage cannot be greater than maximum percentage.');
            }
        }
    }

    /**
     * Unset other default grading scales for the same scope.
     */
    private function unsetOtherDefaults($levelId, $programId, $excludeId = null): void
    {
        $query = GradingScale::where('is_default', true);

        if ($programId) {
            $query->where('program_id', $programId);
        } elseif ($levelId) {
            $query->where('level_id', $levelId)->whereNull('program_id');
        } else {
            $query->whereNull('level_id')->whereNull('program_id');
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $query->update(['is_default' => false]);
    }
}
