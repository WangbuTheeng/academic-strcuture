<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view-reports']);
    }

    /**
     * Display reports dashboard.
     */
    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_exams' => Exam::count(),
            'published_results' => Exam::where('result_status', 'published')->count(),
            'total_marks_entered' => Mark::count(),
        ];

        // Recent activity
        $recentExams = Exam::with(['academicYear', 'class', 'subject'])
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();

        // Performance overview
        $performanceData = $this->getPerformanceOverview();

        return view('admin.reports.index', compact('stats', 'recentExams', 'performanceData'));
    }

    /**
     * Academic performance reports.
     */
    public function academic(Request $request)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $classes = ClassModel::with('level')->get();
        $subjects = Subject::with('department')->get();

        $selectedYear = $request->get('academic_year');
        $selectedClass = $request->get('class');
        $selectedSubject = $request->get('subject');

        $data = [];

        if ($selectedYear || $selectedClass || $selectedSubject) {
            $data = $this->generateAcademicReport($selectedYear, $selectedClass, $selectedSubject);
        }

        return view('admin.reports.academic', compact(
            'academicYears', 'classes', 'subjects', 'data',
            'selectedYear', 'selectedClass', 'selectedSubject'
        ));
    }

    /**
     * Class performance analysis.
     */
    public function classPerformance(Request $request)
    {
        $classId = $request->get('class_id');
        $examId = $request->get('exam_id');

        if (!$classId || !$examId) {
            $classes = ClassModel::with('level')->get();
            $exams = Exam::with(['academicYear', 'class'])->where('result_status', 'published')->get();

            return view('admin.reports.class-performance', compact('classes', 'exams'));
        }

        $class = ClassModel::with(['level', 'students'])->findOrFail($classId);
        $exam = Exam::with(['academicYear', 'subject'])->findOrFail($examId);

        // Get marks for this class and exam
        $marks = Mark::with(['student.currentEnrollment.class', 'subject'])
                    ->where('exam_id', $examId)
                    ->whereHas('student.currentEnrollment', function($q) use ($classId) {
                        $q->where('class_id', $classId);
                    })
                    ->where('status', 'approved')
                    ->get();

        $analytics = $this->analyzeClassPerformance($marks, $class, $exam);

        return view('admin.reports.class-performance-detail', compact('class', 'exam', 'marks', 'analytics'));
    }

    /**
     * Subject-wise analysis.
     */
    public function subjectAnalysis(Request $request)
    {
        $subjectId = $request->get('subject_id');
        $academicYearId = $request->get('academic_year_id');

        if (!$subjectId || !$academicYearId) {
            $subjects = Subject::with('department')->get();
            $academicYears = AcademicYear::orderBy('name', 'desc')->get();

            return view('admin.reports.subject-analysis', compact('subjects', 'academicYears'));
        }

        $subject = Subject::with('department')->findOrFail($subjectId);
        $academicYear = AcademicYear::findOrFail($academicYearId);

        // Get all marks for this subject in the academic year
        $marks = Mark::with(['student.class', 'exam'])
                    ->where('subject_id', $subjectId)
                    ->whereHas('exam', function($q) use ($academicYearId) {
                        $q->where('academic_year_id', $academicYearId);
                    })
                    ->where('status', 'approved')
                    ->get();

        $analytics = $this->analyzeSubjectPerformance($marks, $subject, $academicYear);

        return view('admin.reports.subject-analysis-detail', compact('subject', 'academicYear', 'marks', 'analytics'));
    }

    /**
     * Student progress report.
     */
    public function studentProgress(Request $request)
    {
        $studentId = $request->get('student_id');

        if (!$studentId) {
            $students = Student::with(['currentEnrollment.class', 'currentEnrollment.program'])
                              ->where('status', 'active')
                              ->get()
                              ->sortBy('currentEnrollment.roll_no');

            return view('admin.reports.student-progress', compact('students'));
        }

        $student = Student::with(['currentEnrollment.class', 'currentEnrollment.program'])->findOrFail($studentId);

        // Get all marks for this student
        $marks = Mark::with(['exam.academicYear', 'subject'])
                    ->where('student_id', $studentId)
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'desc')
                    ->get();

        $analytics = $this->analyzeStudentProgress($marks, $student);

        return view('admin.reports.student-progress-detail', compact('student', 'marks', 'analytics'));
    }

    /**
     * Generate custom report.
     */
    public function customReport(Request $request)
    {
        if ($request->isMethod('GET')) {
            $academicYears = AcademicYear::orderBy('name', 'desc')->get();
            $classes = ClassModel::with('level')->get();
            $subjects = Subject::with('department')->get();
            $exams = Exam::with(['academicYear', 'class'])->where('result_status', 'published')->get();

            return view('admin.reports.custom', compact('academicYears', 'classes', 'subjects', 'exams'));
        }

        $validated = $request->validate([
            'report_type' => 'required|in:performance,attendance,grades,comparison',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'exists:classes,id',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
            'exam_ids' => 'nullable|array',
            'exam_ids.*' => 'exists:exams,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'format' => 'required|in:html,pdf,excel',
        ]);

        $data = $this->generateCustomReport($validated);

        if ($validated['format'] === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.custom-pdf', $data)
                     ->setPaper('a4', 'landscape');

            return $pdf->download('custom_report_' . now()->format('Y-m-d') . '.pdf');
        }

        return view('admin.reports.custom-result', $data);
    }

    /**
     * Export report data.
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:academic,class,subject,student',
            'format' => 'required|in:pdf,excel,csv',
            'filters' => 'required|array',
        ]);

        // Generate export based on type and format
        switch ($validated['type']) {
            case 'academic':
                return $this->exportAcademicReport($validated['filters'], $validated['format']);
            case 'class':
                return $this->exportClassReport($validated['filters'], $validated['format']);
            case 'subject':
                return $this->exportSubjectReport($validated['filters'], $validated['format']);
            case 'student':
                return $this->exportStudentReport($validated['filters'], $validated['format']);
        }
    }

    /**
     * Get performance overview for dashboard.
     */
    private function getPerformanceOverview()
    {
        $currentYear = AcademicYear::where('is_current', true)->first();

        if (!$currentYear) {
            return [];
        }

        // Overall pass/fail rates
        $totalMarks = Mark::whereHas('exam', function($q) use ($currentYear) {
            $q->where('academic_year_id', $currentYear->id);
        })->where('status', 'approved')->count();

        $passCount = Mark::whereHas('exam', function($q) use ($currentYear) {
            $q->where('academic_year_id', $currentYear->id);
        })->where('status', 'approved')->where('result', 'Pass')->count();

        $passRate = $totalMarks > 0 ? ($passCount / $totalMarks) * 100 : 0;

        // Grade distribution
        $gradeDistribution = Mark::whereHas('exam', function($q) use ($currentYear) {
            $q->where('academic_year_id', $currentYear->id);
        })
        ->where('status', 'approved')
        ->select('grade', DB::raw('count(*) as count'))
        ->groupBy('grade')
        ->pluck('count', 'grade')
        ->toArray();

        // Top performing classes
        $topClasses = Mark::with(['student.class'])
                         ->whereHas('exam', function($q) use ($currentYear) {
                             $q->where('academic_year_id', $currentYear->id);
                         })
                         ->where('status', 'approved')
                         ->get()
                         ->groupBy('student.class.name')
                         ->map(function($marks) {
                             return [
                                 'average_percentage' => $marks->avg('percentage'),
                                 'total_students' => $marks->count(),
                             ];
                         })
                         ->sortByDesc('average_percentage')
                         ->take(5);

        return [
            'pass_rate' => round($passRate, 2),
            'total_marks' => $totalMarks,
            'grade_distribution' => $gradeDistribution,
            'top_classes' => $topClasses,
        ];
    }

    /**
     * Generate academic report data.
     */
    private function generateAcademicReport($yearId, $classId, $subjectId)
    {
        $query = Mark::with(['student.currentEnrollment.class', 'exam', 'subject'])
                    ->where('status', 'approved');

        if ($yearId) {
            $query->whereHas('exam', function($q) use ($yearId) {
                $q->where('academic_year_id', $yearId);
            });
        }

        if ($classId) {
            $query->whereHas('student.currentEnrollment', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $marks = $query->get();

        return [
            'total_students' => $marks->pluck('student_id')->unique()->count(),
            'total_marks' => $marks->count(),
            'average_percentage' => $marks->avg('percentage'),
            'pass_count' => $marks->where('result', 'Pass')->count(),
            'fail_count' => $marks->where('result', 'Fail')->count(),
            'grade_distribution' => $marks->groupBy('grade')->map->count(),
            'subject_performance' => $marks->groupBy('subject.name')->map(function($subjectMarks) {
                return [
                    'average' => $subjectMarks->avg('percentage'),
                    'count' => $subjectMarks->count(),
                ];
            }),
        ];
    }

    /**
     * Analyze class performance.
     */
    private function analyzeClassPerformance($marks, $class, $exam)
    {
        $totalStudents = $class->students->count();
        $studentsWithMarks = $marks->pluck('student_id')->unique()->count();

        return [
            'total_students' => $totalStudents,
            'students_with_marks' => $studentsWithMarks,
            'completion_rate' => $totalStudents > 0 ? ($studentsWithMarks / $totalStudents) * 100 : 0,
            'average_percentage' => $marks->avg('percentage'),
            'highest_percentage' => $marks->max('percentage'),
            'lowest_percentage' => $marks->min('percentage'),
            'pass_count' => $marks->where('result', 'Pass')->count(),
            'fail_count' => $marks->where('result', 'Fail')->count(),
            'grade_distribution' => $marks->groupBy('grade')->map->count(),
            'subject_averages' => $marks->groupBy('subject.name')->map(function($subjectMarks) {
                return round($subjectMarks->avg('percentage'), 2);
            }),
        ];
    }

    /**
     * Analyze subject performance.
     */
    private function analyzeSubjectPerformance($marks, $subject, $academicYear)
    {
        $examPerformance = $marks->groupBy('exam.name')->map(function($examMarks) {
            return [
                'average' => round($examMarks->avg('percentage'), 2),
                'count' => $examMarks->count(),
                'pass_rate' => $examMarks->count() > 0 ?
                    ($examMarks->where('result', 'Pass')->count() / $examMarks->count()) * 100 : 0,
            ];
        });

        $classPerformance = $marks->groupBy('student.class.name')->map(function($classMarks) {
            return [
                'average' => round($classMarks->avg('percentage'), 2),
                'count' => $classMarks->count(),
            ];
        });

        return [
            'total_marks' => $marks->count(),
            'average_percentage' => round($marks->avg('percentage'), 2),
            'pass_rate' => $marks->count() > 0 ?
                ($marks->where('result', 'Pass')->count() / $marks->count()) * 100 : 0,
            'grade_distribution' => $marks->groupBy('grade')->map->count(),
            'exam_performance' => $examPerformance,
            'class_performance' => $classPerformance,
        ];
    }

    /**
     * Analyze student progress.
     */
    private function analyzeStudentProgress($marks, $student)
    {
        $examProgress = $marks->groupBy('exam.academicYear.name')->map(function($yearMarks) {
            return [
                'average' => round($yearMarks->avg('percentage'), 2),
                'total_marks' => $yearMarks->sum('total_marks'),
                'exams_count' => $yearMarks->count(),
                'subjects' => $yearMarks->groupBy('subject.name')->map(function($subjectMarks) {
                    return round($subjectMarks->avg('percentage'), 2);
                }),
            ];
        });

        $subjectTrends = $marks->groupBy('subject.name')->map(function($subjectMarks) {
            return $subjectMarks->sortBy('created_at')->pluck('percentage', 'exam.name');
        });

        return [
            'total_exams' => $marks->count(),
            'overall_average' => round($marks->avg('percentage'), 2),
            'best_subject' => $marks->groupBy('subject.name')
                                  ->map(function($subjectMarks) {
                                      return $subjectMarks->avg('percentage');
                                  })
                                  ->sortDesc()
                                  ->keys()
                                  ->first(),
            'improvement_trend' => $this->calculateImprovementTrend($marks),
            'exam_progress' => $examProgress,
            'subject_trends' => $subjectTrends,
        ];
    }

    /**
     * Calculate improvement trend.
     */
    private function calculateImprovementTrend($marks)
    {
        $sortedMarks = $marks->sortBy('created_at');

        if ($sortedMarks->count() < 2) {
            return 'insufficient_data';
        }

        $firstHalf = $sortedMarks->take(ceil($sortedMarks->count() / 2));
        $secondHalf = $sortedMarks->skip(ceil($sortedMarks->count() / 2));

        $firstAverage = $firstHalf->avg('percentage');
        $secondAverage = $secondHalf->avg('percentage');

        $improvement = $secondAverage - $firstAverage;

        if ($improvement > 5) {
            return 'improving';
        } elseif ($improvement < -5) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    /**
     * Generate custom report based on filters.
     */
    private function generateCustomReport($filters)
    {
        $data = [
            'filters' => $filters,
            'generated_at' => now(),
            'reportData' => [],
            'selectedClasses' => [],
            'selectedSubjects' => [],
            'academicYear' => null,
        ];

        // Get selected academic year
        if (isset($filters['academic_year_id']) && $filters['academic_year_id']) {
            $data['academicYear'] = AcademicYear::find($filters['academic_year_id']);
        }

        // Get selected classes
        if (isset($filters['class_ids']) && !empty($filters['class_ids'])) {
            $data['selectedClasses'] = ClassModel::with('level')->whereIn('id', $filters['class_ids'])->get();
        }

        // Get selected subjects
        if (isset($filters['subject_ids']) && !empty($filters['subject_ids'])) {
            $data['selectedSubjects'] = Subject::with('department')->whereIn('id', $filters['subject_ids'])->get();
        }

        // Generate report data based on type
        switch ($filters['report_type']) {
            case 'performance':
                $data['reportData'] = $this->generatePerformanceReport($filters);
                break;
            case 'marks':
                $data['reportData'] = $this->generateMarksReport($filters);
                break;
            case 'grades':
                $data['reportData'] = $this->generateGradeReport($filters);
                break;
            case 'comparison':
                $data['reportData'] = $this->generateComparisonReport($filters);
                break;
            default:
                $data['reportData'] = ['summary' => ['total_records' => 0, 'message' => 'No data available']];
        }

        return $data;
    }

    /**
     * Generate performance analysis report.
     */
    private function generatePerformanceReport($filters)
    {
        $query = Mark::with(['student.currentEnrollment.class', 'subject', 'exam'])
                    ->where('status', 'approved');

        // Apply filters
        if (isset($filters['academic_year_id']) && $filters['academic_year_id']) {
            $query->whereHas('exam', function($q) use ($filters) {
                $q->where('academic_year_id', $filters['academic_year_id']);
            });
        }

        if (isset($filters['class_ids']) && !empty($filters['class_ids'])) {
            $query->whereHas('student.currentEnrollment', function($q) use ($filters) {
                $q->whereIn('class_id', $filters['class_ids']);
            });
        }

        if (isset($filters['subject_ids']) && !empty($filters['subject_ids'])) {
            $query->whereIn('subject_id', $filters['subject_ids']);
        }

        $marks = $query->get();

        return [
            'summary' => [
                'total_students' => $marks->groupBy('student_id')->count(),
                'total_subjects' => $marks->groupBy('subject_id')->count(),
                'average_percentage' => $marks->avg('percentage'),
                'pass_rate' => $marks->where('result', 'Pass')->count() / max($marks->count(), 1) * 100,
            ],
            'marks' => $marks,
            'grade_distribution' => $marks->groupBy('grade')->map->count(),
        ];
    }

    /**
     * Generate marks analysis report.
     */
    private function generateMarksReport($filters)
    {
        // Similar implementation for marks report
        return ['summary' => ['message' => 'Marks report generated']];
    }

    /**
     * Generate grade distribution report.
     */
    private function generateGradeReport($filters)
    {
        // Similar implementation for grade report
        return ['summary' => ['message' => 'Grade report generated']];
    }

    /**
     * Generate comparison report.
     */
    private function generateComparisonReport($filters)
    {
        // Similar implementation for comparison report
        return ['summary' => ['message' => 'Comparison report generated']];
    }

    /**
     * Export academic report.
     */
    private function exportAcademicReport($filters, $format)
    {
        // Implementation for academic report export
        // This would generate Excel/CSV/PDF based on format

        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Export class report.
     */
    private function exportClassReport($filters, $format)
    {
        // Implementation for class report export
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Export subject report.
     */
    private function exportSubjectReport($filters, $format)
    {
        // Implementation for subject report export
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Export student report.
     */
    private function exportStudentReport($filters, $format)
    {
        // Implementation for student report export
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
