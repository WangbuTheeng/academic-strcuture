<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\ClassModel;
use App\Models\Program;
use App\Models\Subject;
use App\Models\GradingScale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage-exams']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Exam::with(['academicYear', 'semester', 'class', 'program', 'subject', 'creator']);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Exam type filter
        if ($request->filled('exam_type')) {
            $query->byType($request->exam_type);
        }

        // Academic year filter
        if ($request->filled('academic_year')) {
            $query->byAcademicYear($request->academic_year);
        }

        // Class filter
        if ($request->filled('class')) {
            $query->byClass($request->class);
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $classes = ClassModel::with('level')->get();
        $examTypes = ['assessment', 'terminal', 'quiz', 'project', 'practical', 'final'];
        $statuses = ['draft', 'scheduled', 'ongoing', 'submitted', 'approved', 'published', 'locked'];

        return view('admin.exams.index', compact('exams', 'academicYears', 'classes', 'examTypes', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $classes = ClassModel::with('level')->get();
        $programs = Program::all();
        $subjects = Subject::with('department')->get();
        $gradingScales = GradingScale::all();

        return view('admin.exams.create', compact(
            'academicYears', 'classes', 'programs', 'subjects', 'gradingScales'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'exam_type' => 'required|in:assessment,terminal,quiz,project,practical,final',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'class_id' => 'nullable|exists:classes,id',
            'program_id' => 'nullable|exists:programs,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'max_marks' => 'required|numeric|min:1|max:1000',
            'theory_max' => 'required|numeric|min:0',
            'practical_max' => 'nullable|numeric|min:0',
            'assess_max' => 'nullable|numeric|min:0',
            'has_practical' => 'boolean',
            'has_assessment' => 'boolean',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'submission_deadline' => 'required|date|after_or_equal:end_date',
            'grading_scale_id' => 'nullable|exists:grading_scales,id',
        ]);

        // Validate mark distribution
        $totalComponents = $validated['theory_max'] +
                          ($validated['practical_max'] ?? 0) +
                          ($validated['assess_max'] ?? 0);

        if ($totalComponents != $validated['max_marks']) {
            return back()->withErrors([
                'max_marks' => 'The sum of theory, practical, and assessment marks must equal the maximum marks.'
            ])->withInput();
        }

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        $exam = Exam::create($validated);

        return redirect()->route('admin.exams.index')
                        ->with('success', 'Exam created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        $exam->load([
            'academicYear', 'semester', 'class', 'program', 'subject',
            'gradingScale', 'creator', 'approver', 'publisher',
            'marks.student'
        ]);

        $statistics = [
            'total_students' => $exam->marks()->distinct('student_id')->count(),
            'submitted_marks' => $exam->marks()->where('status', 'submitted')->count(),
            'average_marks' => $exam->marks()->avg('total'),
            'highest_marks' => $exam->marks()->max('total'),
            'lowest_marks' => $exam->marks()->min('total'),
            'pass_count' => $exam->marks()->where('result', 'Pass')->count(),
            'fail_count' => $exam->marks()->where('result', 'Fail')->count(),
        ];

        return view('admin.exams.show', compact('exam', 'statistics'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        if (!$exam->is_editable) {
            return redirect()->route('admin.exams.index')
                            ->with('error', 'This exam cannot be edited in its current status.');
        }

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $classes = ClassModel::with('level')->get();
        $programs = Program::all();
        $subjects = Subject::with('department')->get();
        $gradingScales = GradingScale::all();

        return view('admin.exams.edit', compact(
            'exam', 'academicYears', 'classes', 'programs', 'subjects', 'gradingScales'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        if (!$exam->is_editable) {
            return redirect()->route('admin.exams.index')
                            ->with('error', 'This exam cannot be edited in its current status.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'exam_type' => 'required|in:assessment,terminal,quiz,project,practical,final',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'class_id' => 'nullable|exists:classes,id',
            'program_id' => 'nullable|exists:programs,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'max_marks' => 'required|numeric|min:1|max:1000',
            'theory_max' => 'required|numeric|min:0',
            'practical_max' => 'nullable|numeric|min:0',
            'assess_max' => 'nullable|numeric|min:0',
            'has_practical' => 'boolean',
            'has_assessment' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'submission_deadline' => 'required|date|after_or_equal:end_date',
            'grading_scale_id' => 'nullable|exists:grading_scales,id',
        ]);

        // Validate mark distribution
        $totalComponents = $validated['theory_max'] +
                          ($validated['practical_max'] ?? 0) +
                          ($validated['assess_max'] ?? 0);

        if ($totalComponents != $validated['max_marks']) {
            return back()->withErrors([
                'max_marks' => 'The sum of theory, practical, and assessment marks must equal the maximum marks.'
            ])->withInput();
        }

        $exam->update($validated);

        return redirect()->route('admin.exams.index')
                        ->with('success', 'Exam updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        if (!$exam->is_editable) {
            return redirect()->route('admin.exams.index')
                            ->with('error', 'This exam cannot be deleted in its current status.');
        }

        // Check if exam has marks
        if ($exam->marks()->count() > 0) {
            return redirect()->route('admin.exams.index')
                            ->with('error', 'Cannot delete exam with existing marks.');
        }

        $exam->delete();

        return redirect()->route('admin.exams.index')
                        ->with('success', 'Exam deleted successfully.');
    }

    /**
     * Change exam status.
     */
    public function changeStatus(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,scheduled,ongoing,submitted,approved,published,locked',
            'reason' => 'nullable|string|max:500'
        ]);

        $oldStatus = $exam->status;
        $newStatus = $validated['status'];

        // Validate status transitions
        $allowedTransitions = [
            'draft' => ['scheduled'],
            'scheduled' => ['ongoing', 'draft'],
            'ongoing' => ['submitted'],
            'submitted' => ['approved', 'ongoing'],
            'approved' => ['published', 'submitted'],
            'published' => ['locked'],
            'locked' => [] // Only admin can unlock
        ];

        if (!auth()->user()->hasRole('admin') &&
            !in_array($newStatus, $allowedTransitions[$oldStatus] ?? [])) {
            return back()->with('error', 'Invalid status transition.');
        }

        // Update status and related fields
        $updateData = ['result_status' => $newStatus];

        if ($newStatus === 'approved') {
            $updateData['approved_by'] = auth()->id();
            $updateData['approved_at'] = now();
        } elseif ($newStatus === 'published') {
            $updateData['published_by'] = auth()->id();
            $updateData['published_at'] = now();
        }

        $exam->update($updateData);

        return back()->with('success', "Exam status changed from {$oldStatus} to {$newStatus}.");
    }

    /**
     * Bulk operations
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,change_status',
            'exams' => 'required|array',
            'exams.*' => 'exists:exams,id',
            'status' => 'required_if:action,change_status|in:draft,scheduled,ongoing,submitted,approved,published,locked'
        ]);

        $exams = Exam::whereIn('id', $validated['exams']);

        switch ($validated['action']) {
            case 'delete':
                $editableExams = $exams->where('result_status', 'draft')->get();
                if ($editableExams->count() !== count($validated['exams'])) {
                    return redirect()->route('admin.exams.index')
                                    ->with('error', 'Some exams cannot be deleted due to their status.');
                }

                $editableExams->each->delete();
                $message = 'Selected exams deleted successfully.';
                break;

            case 'change_status':
                $exams->update(['result_status' => $validated['status']]);
                $message = "Selected exams status changed to {$validated['status']}.";
                break;
        }

        return redirect()->route('admin.exams.index')
                        ->with('success', $message);
    }
}
