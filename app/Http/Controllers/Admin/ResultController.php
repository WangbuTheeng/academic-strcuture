<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage-results']);
    }

    /**
     * Display result management dashboard.
     */
    public function index(Request $request)
    {
        $query = Exam::with(['academicYear', 'semester', 'class', 'subject'])
                     ->whereIn('result_status', ['ongoing', 'completed', 'published']);

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('result_status', $request->status);
        }

        // Academic year filter
        if ($request->filled('academic_year')) {
            $query->where('academic_year_id', $request->academic_year);
        }

        // Class filter
        if ($request->filled('class')) {
            $query->where('class_id', $request->class);
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $classes = ClassModel::with('level')->get();

        // Get statistics
        $stats = [
            'pending_approval' => Mark::where('status', 'submitted')->count(),
            'approved_marks' => Mark::where('status', 'approved')->count(),
            'published_results' => Exam::where('result_status', 'published')->count(),
            'ongoing_exams' => Exam::where('result_status', 'ongoing')->count(),
        ];

        return view('admin.results.index', compact('exams', 'academicYears', 'classes', 'stats'));
    }

    /**
     * Show result details for a specific exam.
     */
    public function show(Exam $exam)
    {
        $exam->load(['academicYear', 'class', 'subject', 'gradingScale']);

        // Get marks with student details
        $marks = Mark::with(['student.currentEnrollment.class', 'subject'])
                    ->where('exam_id', $exam->id)
                    ->where('status', 'approved')
                    ->orderBy('total', 'desc')
                    ->get();

        // Calculate statistics
        $statistics = [
            'total_students' => $marks->pluck('student_id')->unique()->count(),
            'total_marks' => $marks->count(),
            'average_percentage' => $marks->avg('percentage'),
            'highest_percentage' => $marks->max('percentage'),
            'lowest_percentage' => $marks->min('percentage'),
            'pass_count' => $marks->where('result', 'Pass')->count(),
            'fail_count' => $marks->where('result', 'Fail')->count(),
            'grade_distribution' => $marks->groupBy('grade')->map->count(),
        ];

        return view('admin.results.show', compact('exam', 'marks', 'statistics'));
    }

    /**
     * Publish exam results.
     */
    public function publish(Request $request, Exam $exam)
    {
        // Validate that all marks are approved
        $pendingMarks = $exam->marks()->where('status', '!=', 'approved')->count();

        if ($pendingMarks > 0) {
            return back()->with('error', "Cannot publish results. {$pendingMarks} marks are still pending approval.");
        }

        // Check if exam has any marks
        $totalMarks = $exam->marks()->count();
        if ($totalMarks === 0) {
            return back()->with('error', 'Cannot publish results. No marks have been entered for this exam.');
        }

        DB::transaction(function () use ($exam) {
            $exam->update([
                'result_status' => 'published',
                'published_by' => auth()->id(),
                'published_at' => now(),
            ]);

            // Log the publication
            activity()
                ->performedOn($exam)
                ->causedBy(auth()->user())
                ->withProperties(['action' => 'published_results'])
                ->log("Published results for exam: {$exam->name}");
        });

        return back()->with('success', 'Exam results published successfully.');
    }

    /**
     * Unpublish exam results.
     */
    public function unpublish(Exam $exam)
    {
        if ($exam->status !== 'published') {
            return back()->with('error', 'Only published results can be unpublished.');
        }

        DB::transaction(function () use ($exam) {
            $exam->update([
                'result_status' => 'approved',
                'published_by' => null,
                'published_at' => null,
            ]);

            // Log the unpublication
            activity()
                ->performedOn($exam)
                ->causedBy(auth()->user())
                ->withProperties(['action' => 'unpublished_results'])
                ->log("Unpublished results for exam: {$exam->name}");
        });

        return back()->with('success', 'Exam results unpublished successfully.');
    }

    /**
     * Lock exam results to prevent further modifications.
     */
    public function lock(Exam $exam)
    {
        if ($exam->status !== 'published') {
            return back()->with('error', 'Only published results can be locked.');
        }

        DB::transaction(function () use ($exam) {
            $exam->update([
                'result_status' => 'locked',
                'locked_by' => auth()->id(),
                'locked_at' => now(),
            ]);

            // Log the locking
            activity()
                ->performedOn($exam)
                ->causedBy(auth()->user())
                ->withProperties(['action' => 'locked_results'])
                ->log("Locked results for exam: {$exam->name}");
        });

        return back()->with('success', 'Exam results locked successfully. No further modifications are allowed.');
    }

    /**
     * Bulk approve marks for an exam.
     */
    public function bulkApprove(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'mark_ids' => 'required|array',
            'mark_ids.*' => 'exists:marks,id',
        ]);

        $approvedCount = 0;

        DB::transaction(function () use ($validated, $exam, &$approvedCount) {
            $marks = Mark::whereIn('id', $validated['mark_ids'])
                        ->where('exam_id', $exam->id)
                        ->where('status', 'pending')
                        ->get();

            foreach ($marks as $mark) {
                $mark->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
                $approvedCount++;
            }

            // Log the bulk approval
            activity()
                ->performedOn($exam)
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => 'bulk_approved_marks',
                    'count' => $approvedCount
                ])
                ->log("Bulk approved {$approvedCount} marks for exam: {$exam->name}");
        });

        return back()->with('success', "Successfully approved {$approvedCount} marks.");
    }

    /**
     * Generate result summary report.
     */
    public function summary(Exam $exam)
    {
        $exam->load(['academicYear', 'class', 'subject', 'gradingScale']);

        // Get comprehensive statistics
        $marks = Mark::with(['student.currentEnrollment.class', 'subject'])
                    ->where('exam_id', $exam->id)
                    ->where('status', 'approved')
                    ->get();

        $summary = [
            'exam' => $exam,
            'total_students' => $marks->pluck('student_id')->unique()->count(),
            'total_marks' => $marks->count(),
            'statistics' => [
                'average_percentage' => $marks->avg('percentage'),
                'median_percentage' => $marks->median('percentage'),
                'highest_percentage' => $marks->max('percentage'),
                'lowest_percentage' => $marks->min('percentage'),
                'standard_deviation' => $this->calculateStandardDeviation($marks->pluck('percentage')),
            ],
            'grade_distribution' => $marks->groupBy('grade')->map(function($gradeMarks) use ($marks) {
                return [
                    'count' => $gradeMarks->count(),
                    'percentage' => ($gradeMarks->count() / $marks->count()) * 100,
                ];
            }),
            'subject_performance' => $marks->groupBy('subject.name')->map(function($subjectMarks) {
                return [
                    'count' => $subjectMarks->count(),
                    'average' => $subjectMarks->avg('percentage'),
                    'pass_rate' => ($subjectMarks->where('result', 'Pass')->count() / $subjectMarks->count()) * 100,
                ];
            }),
            'class_performance' => $marks->groupBy('student.class.name')->map(function($classMarks) {
                return [
                    'count' => $classMarks->count(),
                    'average' => $classMarks->avg('percentage'),
                    'pass_rate' => ($classMarks->where('result', 'Pass')->count() / $classMarks->count()) * 100,
                ];
            }),
        ];

        return view('admin.results.summary', compact('summary'));
    }

    /**
     * Calculate standard deviation.
     */
    private function calculateStandardDeviation($values)
    {
        $count = $values->count();
        if ($count === 0) return 0;

        $mean = $values->avg();
        $variance = $values->map(function($value) use ($mean) {
            return pow($value - $mean, 2);
        })->avg();

        return sqrt($variance);
    }
}
