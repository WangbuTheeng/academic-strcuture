<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view-analytics']);
    }

    /**
     * Display analytics dashboard.
     */
    public function index(Request $request)
    {
        $academicYear = $this->getSelectedAcademicYear($request);

        // Get comprehensive analytics data
        $analytics = [
            'overview' => $this->getOverviewAnalytics($academicYear),
            'performance' => $this->getPerformanceAnalytics($academicYear),
            'trends' => $this->getTrendAnalytics($academicYear),
            'comparisons' => $this->getComparisonAnalytics($academicYear),
        ];

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.analytics.index', compact('analytics', 'academicYears', 'academicYear'));
    }

    /**
     * Get student performance analytics.
     */
    public function studentPerformance(Request $request)
    {
        $academicYear = $this->getSelectedAcademicYear($request);

        $query = Student::with(['currentEnrollment.class', 'marks' => function($q) use ($academicYear) {
            $q->whereHas('exam', function($eq) use ($academicYear) {
                $eq->where('academic_year_id', $academicYear->id);
            })->where('status', 'approved');
        }]);

        // Filter by class if specified
        if ($request->filled('class_id')) {
            $query->whereHas('currentEnrollment', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        $students = $query->get();

        // Calculate performance metrics for each student
        $performanceData = $students->map(function($student) {
            $marks = $student->marks;
            $totalMarks = $marks->sum('obtained_marks');
            $maxMarks = $marks->sum('total_marks');
            $averagePercentage = $maxMarks > 0 ? ($totalMarks / $maxMarks) * 100 : 0;

            return [
                'student' => $student,
                'total_marks' => $totalMarks,
                'max_marks' => $maxMarks,
                'average_percentage' => $averagePercentage,
                'subjects_count' => $marks->count(),
                'pass_count' => $marks->where('result', 'Pass')->count(),
                'fail_count' => $marks->where('result', 'Fail')->count(),
                'overall_result' => $marks->where('result', 'Fail')->count() > 0 ? 'Fail' : 'Pass',
            ];
        });

        $classes = ClassModel::with('level')->get();

        return view('admin.analytics.student-performance', compact('performanceData', 'classes', 'academicYear'));
    }

    /**
     * Get subject-wise analytics.
     */
    public function subjectAnalytics(Request $request)
    {
        $academicYear = $this->getSelectedAcademicYear($request);

        $subjectAnalytics = Subject::with(['marks' => function($q) use ($academicYear) {
            $q->whereHas('exam', function($eq) use ($academicYear) {
                $eq->where('academic_year_id', $academicYear->id);
            })->where('status', 'approved');
        }])->get()->map(function($subject) {
            $marks = $subject->marks;

            return [
                'subject' => $subject,
                'total_students' => $marks->count(),
                'pass_count' => $marks->where('result', 'Pass')->count(),
                'fail_count' => $marks->where('result', 'Fail')->count(),
                'pass_percentage' => $marks->count() > 0 ? ($marks->where('result', 'Pass')->count() / $marks->count()) * 100 : 0,
                'average_marks' => $marks->avg('obtained_marks'),
                'highest_marks' => $marks->max('obtained_marks'),
                'lowest_marks' => $marks->min('obtained_marks'),
                'average_percentage' => $marks->avg('percentage'),
                'grade_distribution' => $marks->groupBy('grade')->map->count(),
            ];
        });

        return view('admin.analytics.subject-analytics', compact('subjectAnalytics', 'academicYear'));
    }

    /**
     * Get class-wise analytics.
     */
    public function classAnalytics(Request $request)
    {
        $academicYear = $this->getSelectedAcademicYear($request);

        $classAnalytics = ClassModel::with(['enrollments.student.marks' => function($q) use ($academicYear) {
            $q->whereHas('exam', function($eq) use ($academicYear) {
                $eq->where('academic_year_id', $academicYear->id);
            })->where('status', 'approved');
        }])->get()->map(function($class) {
            $students = $class->enrollments->pluck('student');
            $allMarks = $students->flatMap->marks;
            $studentCount = $students->count();

            // Calculate class performance
            $studentResults = $students->map(function($student) {
                $studentMarks = $student->marks;
                $failCount = $studentMarks->where('result', 'Fail')->count();
                return $failCount > 0 ? 'Fail' : 'Pass';
            });

            return [
                'class' => $class,
                'total_students' => $studentCount,
                'total_marks_entries' => $allMarks->count(),
                'class_pass_count' => $studentResults->where('Pass')->count(),
                'class_fail_count' => $studentResults->where('Fail')->count(),
                'class_pass_percentage' => $studentCount > 0 ? ($studentResults->where('Pass')->count() / $studentCount) * 100 : 0,
                'average_class_percentage' => $allMarks->avg('percentage'),
                'highest_percentage' => $allMarks->max('percentage'),
                'lowest_percentage' => $allMarks->min('percentage'),
                'subject_wise_performance' => $allMarks->groupBy('subject.name')->map(function($subjectMarks) {
                    return [
                        'average_percentage' => $subjectMarks->avg('percentage'),
                        'pass_count' => $subjectMarks->where('result', 'Pass')->count(),
                        'fail_count' => $subjectMarks->where('result', 'Fail')->count(),
                    ];
                }),
            ];
        });

        return view('admin.analytics.class-analytics', compact('classAnalytics', 'academicYear'));
    }

    /**
     * Get exam analytics.
     */
    public function examAnalytics(Request $request)
    {
        $academicYear = $this->getSelectedAcademicYear($request);

        $examAnalytics = Exam::with(['marks' => function($q) {
            $q->where('status', 'approved');
        }])->where('academic_year_id', $academicYear->id)
        ->get()->map(function($exam) {
            $marks = $exam->marks;

            return [
                'exam' => $exam,
                'total_students' => $marks->count(),
                'submitted_marks' => $marks->count(),
                'pending_marks' => $exam->getExpectedStudentCount() - $marks->count(),
                'pass_count' => $marks->where('result', 'Pass')->count(),
                'fail_count' => $marks->where('result', 'Fail')->count(),
                'pass_percentage' => $marks->count() > 0 ? ($marks->where('result', 'Pass')->count() / $marks->count()) * 100 : 0,
                'average_marks' => $marks->avg('obtained_marks'),
                'average_percentage' => $marks->avg('percentage'),
                'highest_marks' => $marks->max('obtained_marks'),
                'lowest_marks' => $marks->min('obtained_marks'),
                'grade_distribution' => $marks->groupBy('grade')->map->count(),
                'completion_percentage' => $exam->getExpectedStudentCount() > 0 ? ($marks->count() / $exam->getExpectedStudentCount()) * 100 : 0,
            ];
        });

        return view('admin.analytics.exam-analytics', compact('examAnalytics', 'academicYear'));
    }

    /**
     * Get trend analytics.
     */
    public function trendAnalytics(Request $request)
    {
        $years = AcademicYear::orderBy('name')->take(5)->get();

        $trendData = $years->map(function($year) {
            $marks = Mark::whereHas('exam', function($q) use ($year) {
                $q->where('academic_year_id', $year->id);
            })->where('status', 'approved')->get();

            return [
                'academic_year' => $year->name,
                'total_students' => $marks->pluck('student_id')->unique()->count(),
                'total_marks' => $marks->count(),
                'average_percentage' => $marks->avg('percentage'),
                'pass_percentage' => $marks->count() > 0 ? ($marks->where('result', 'Pass')->count() / $marks->count()) * 100 : 0,
                'grade_distribution' => $marks->groupBy('grade')->map->count(),
            ];
        });

        return view('admin.analytics.trend-analytics', compact('trendData'));
    }

    /**
     * Export analytics data.
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'overview');
        $format = $request->get('format', 'excel');
        $academicYear = $this->getSelectedAcademicYear($request);

        // Generate export data based on type
        $data = $this->getExportData($type, $academicYear);

        if ($format === 'pdf') {
            return $this->exportToPdf($data, $type);
        } else {
            return $this->exportToExcel($data, $type);
        }
    }

    /**
     * Get selected academic year.
     */
    private function getSelectedAcademicYear(Request $request)
    {
        if ($request->filled('academic_year_id')) {
            return AcademicYear::findOrFail($request->academic_year_id);
        }

        return AcademicYear::where('is_current', true)->first()
               ?? AcademicYear::latest()->first();
    }

    /**
     * Get overview analytics.
     */
    private function getOverviewAnalytics($academicYear)
    {
        $totalStudents = Student::where('status', 'active')->count();
        $totalExams = Exam::where('academic_year_id', $academicYear->id)->count();
        $totalMarks = Mark::whereHas('exam', function($q) use ($academicYear) {
            $q->where('academic_year_id', $academicYear->id);
        })->where('status', 'approved')->count();

        $passCount = Mark::whereHas('exam', function($q) use ($academicYear) {
            $q->where('academic_year_id', $academicYear->id);
        })->where('status', 'approved')->where('result', 'Pass')->count();

        return [
            'total_students' => $totalStudents,
            'total_exams' => $totalExams,
            'total_marks' => $totalMarks,
            'pass_count' => $passCount,
            'fail_count' => $totalMarks - $passCount,
            'pass_percentage' => $totalMarks > 0 ? ($passCount / $totalMarks) * 100 : 0,
            'average_percentage' => Mark::whereHas('exam', function($q) use ($academicYear) {
                $q->where('academic_year_id', $academicYear->id);
            })->where('status', 'approved')->avg('percentage') ?? 0,
        ];
    }

    /**
     * Get performance analytics.
     */
    private function getPerformanceAnalytics($academicYear)
    {
        $marks = Mark::whereHas('exam', function($q) use ($academicYear) {
            $q->where('academic_year_id', $academicYear->id);
        })->where('status', 'approved')->get();

        return [
            'grade_distribution' => $marks->groupBy('grade')->map->count(),
            'subject_performance' => $marks->groupBy('subject.name')->map(function($subjectMarks) {
                return [
                    'average_percentage' => $subjectMarks->avg('percentage'),
                    'pass_count' => $subjectMarks->where('result', 'Pass')->count(),
                    'total_count' => $subjectMarks->count(),
                ];
            }),
            'class_performance' => $marks->groupBy('student.class.name')->map(function($classMarks) {
                return [
                    'average_percentage' => $classMarks->avg('percentage'),
                    'pass_count' => $classMarks->where('result', 'Pass')->count(),
                    'total_count' => $classMarks->count(),
                ];
            }),
        ];
    }

    /**
     * Get trend analytics.
     */
    private function getTrendAnalytics($academicYear)
    {
        $monthlyData = Mark::whereHas('exam', function($q) use ($academicYear) {
            $q->where('academic_year_id', $academicYear->id);
        })->where('status', 'approved')
        ->selectRaw('MONTH(created_at) as month, AVG(percentage) as avg_percentage, COUNT(*) as total_marks')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return [
            'monthly_performance' => $monthlyData,
            'improvement_trend' => $this->calculateImprovementTrend($academicYear),
        ];
    }

    /**
     * Get comparison analytics.
     */
    private function getComparisonAnalytics($academicYear)
    {
        $previousYear = AcademicYear::where('name', '<', $academicYear->name)
                                   ->orderBy('name', 'desc')
                                   ->first();

        if (!$previousYear) {
            return ['comparison_available' => false];
        }

        $currentYearStats = $this->getYearStats($academicYear);
        $previousYearStats = $this->getYearStats($previousYear);

        return [
            'comparison_available' => true,
            'current_year' => $currentYearStats,
            'previous_year' => $previousYearStats,
            'improvement' => [
                'pass_percentage' => $currentYearStats['pass_percentage'] - $previousYearStats['pass_percentage'],
                'average_percentage' => $currentYearStats['average_percentage'] - $previousYearStats['average_percentage'],
            ],
        ];
    }

    /**
     * Get year statistics.
     */
    private function getYearStats($academicYear)
    {
        $marks = Mark::whereHas('exam', function($q) use ($academicYear) {
            $q->where('academic_year_id', $academicYear->id);
        })->where('status', 'approved')->get();

        $passCount = $marks->where('result', 'Pass')->count();
        $totalCount = $marks->count();

        return [
            'total_marks' => $totalCount,
            'pass_count' => $passCount,
            'pass_percentage' => $totalCount > 0 ? ($passCount / $totalCount) * 100 : 0,
            'average_percentage' => $marks->avg('percentage') ?? 0,
        ];
    }

    /**
     * Calculate improvement trend.
     */
    private function calculateImprovementTrend($academicYear)
    {
        // This is a simplified trend calculation
        // In a real implementation, you might want more sophisticated analysis
        $marks = Mark::whereHas('exam', function($q) use ($academicYear) {
            $q->where('academic_year_id', $academicYear->id);
        })->where('status', 'approved')
        ->orderBy('created_at')
        ->get();

        if ($marks->count() < 2) {
            return ['trend' => 'insufficient_data'];
        }

        $firstHalf = $marks->take($marks->count() / 2);
        $secondHalf = $marks->skip($marks->count() / 2);

        $firstHalfAvg = $firstHalf->avg('percentage');
        $secondHalfAvg = $secondHalf->avg('percentage');

        $improvement = $secondHalfAvg - $firstHalfAvg;

        return [
            'trend' => $improvement > 0 ? 'improving' : ($improvement < 0 ? 'declining' : 'stable'),
            'improvement_percentage' => $improvement,
        ];
    }

    /**
     * Get export data.
     */
    private function getExportData($type, $academicYear)
    {
        switch ($type) {
            case 'student_performance':
                return $this->getStudentPerformanceExportData($academicYear);
            case 'subject_analytics':
                return $this->getSubjectAnalyticsExportData($academicYear);
            case 'class_analytics':
                return $this->getClassAnalyticsExportData($academicYear);
            default:
                return $this->getOverviewExportData($academicYear);
        }
    }

    /**
     * Export to PDF.
     */
    private function exportToPdf($data, $type)
    {
        // Implementation for PDF export
        // This would use DomPDF or similar
        return response()->json(['message' => 'PDF export not implemented yet']);
    }

    /**
     * Export to Excel.
     */
    private function exportToExcel($data, $type)
    {
        // Implementation for Excel export
        // This would use Laravel Excel or similar
        return response()->json(['message' => 'Excel export not implemented yet']);
    }

    /**
     * Get student performance export data.
     */
    private function getStudentPerformanceExportData($academicYear)
    {
        return Student::with(['marks' => function($q) use ($academicYear) {
            $q->whereHas('exam', function($eq) use ($academicYear) {
                $eq->where('academic_year_id', $academicYear->id);
            })->where('status', 'approved');
        }])->get();
    }

    /**
     * Get subject analytics export data.
     */
    private function getSubjectAnalyticsExportData($academicYear)
    {
        return Subject::with(['marks' => function($q) use ($academicYear) {
            $q->whereHas('exam', function($eq) use ($academicYear) {
                $eq->where('academic_year_id', $academicYear->id);
            })->where('status', 'approved');
        }])->get();
    }

    /**
     * Get class analytics export data.
     */
    private function getClassAnalyticsExportData($academicYear)
    {
        return ClassModel::with(['students.marks' => function($q) use ($academicYear) {
            $q->whereHas('exam', function($eq) use ($academicYear) {
                $eq->where('academic_year_id', $academicYear->id);
            })->where('status', 'approved');
        }])->get();
    }

    /**
     * Get overview export data.
     */
    private function getOverviewExportData($academicYear)
    {
        return [
            'overview' => $this->getOverviewAnalytics($academicYear),
            'performance' => $this->getPerformanceAnalytics($academicYear),
        ];
    }
}
