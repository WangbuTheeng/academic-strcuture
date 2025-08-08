<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\InstituteSettings;
use App\Models\MarksheetTemplate;
use App\Models\GradingScale;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MarksheetController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view-reports']);
    }

    /**
     * Display marksheet generation interface.
     */
    public function index(Request $request)
    {
        $query = Exam::with(['academicYear', 'class', 'subject'])
                     ->whereIn('result_status', ['published', 'locked']);

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
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

        return view('admin.marksheets.index', compact('exams', 'academicYears', 'classes'));
    }

    /**
     * Show marksheet generation form for specific exam.
     */
    public function create(Request $request)
    {
        $examId = $request->get('exam') ?? $request->get('exam_id');
        $classId = $request->get('class_id');

        if (!$examId) {
            return redirect()->route('admin.marksheets.index')
                            ->with('error', 'Please select an exam first.');
        }

        $exam = Exam::with(['academicYear', 'class', 'subject', 'gradingScale'])
                    ->findOrFail($examId);

        if (!in_array($exam->result_status, ['published', 'locked'])) {
            return redirect()->route('admin.marksheets.index')
                            ->with('error', 'Marksheets can only be generated for published or locked exams.');
        }

        // Get all classes that have students with approved marks for this exam
        $availableClasses = ClassModel::with('level')
                                     ->whereHas('students.marks', function($q) use ($examId) {
                                         $q->where('exam_id', $examId)->where('status', 'approved');
                                     })
                                     ->get();

        // Get students with marks for this exam
        $studentsQuery = Student::with(['currentEnrollment.class', 'currentEnrollment.program', 'marks' => function($q) use ($examId) {
                                    $q->where('exam_id', $examId)->where('status', 'approved');
                                }])
                               ->where('status', 'active')
                               ->whereHas('marks', function($q) use ($examId) {
                                   $q->where('exam_id', $examId)->where('status', 'approved');
                               });

        // Filter by class if specified
        if ($exam->class_id) {
            $studentsQuery->whereHas('currentEnrollment', function($q) use ($exam) {
                $q->where('class_id', $exam->class_id);
            });
        } elseif ($classId) {
            $studentsQuery->whereHas('currentEnrollment', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        $students = $studentsQuery->with('currentEnrollment')
                                  ->get()
                                  ->sortBy('currentEnrollment.roll_no');

        // Filter students who have marks for this exam
        $studentsWithMarks = $students->filter(function($student) {
            return $student->marks->count() > 0;
        });

        // Get available templates (built-in + custom)
        $availableTemplates = collect([
            [
                'id' => 'modern',
                'name' => 'Modern Template',
                'description' => 'Clean, contemporary design with color-coded sections and QR verification',
                'type' => 'built-in',
                'icon' => 'fas fa-magic'
            ],
            [
                'id' => 'classic',
                'name' => 'Classic Template',
                'description' => 'Traditional academic format with institutional branding',
                'type' => 'built-in',
                'icon' => 'fas fa-university'
            ],
            [
                'id' => 'minimal',
                'name' => 'Minimal Template',
                'description' => 'Simple, efficient design for cost-effective printing',
                'type' => 'built-in',
                'icon' => 'fas fa-file-alt'
            ]
        ]);

        // Add custom templates
        $customTemplates = MarksheetTemplate::active()->get()->map(function($template) {
            return [
                'id' => 'custom_' . $template->id,
                'name' => $template->name,
                'description' => $template->description,
                'type' => 'custom',
                'icon' => 'fas fa-palette',
                'template_id' => $template->id
            ];
        });

        $availableTemplates = $availableTemplates->merge($customTemplates);

        // Get available grading scales
        $gradingScales = GradingScale::active()->get();

        return view('admin.marksheets.create', compact('exam', 'studentsWithMarks', 'classId', 'availableClasses', 'availableTemplates', 'gradingScales'));
    }

    /**
     * Generate individual marksheet.
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'exam_id' => 'required|exists:exams,id',
            'template' => 'required|string',
            'format' => 'required|in:pdf,html',
            'grading_scale_id' => 'nullable|exists:grading_scales,id',
        ]);

        $student = Student::with(['currentEnrollment.class', 'currentEnrollment.program'])->findOrFail($validated['student_id']);
        $exam = Exam::with(['academicYear', 'class', 'subject', 'gradingScale'])
                    ->findOrFail($validated['exam_id']);

        // Get institute settings
        $instituteSettings = InstituteSettings::current();

        // Get student marks for this exam
        $marks = Mark::with(['subject'])
                    ->where('student_id', $validated['student_id'])
                    ->where('exam_id', $validated['exam_id'])
                    ->where('status', 'approved')
                    ->get();

        if ($marks->isEmpty()) {
            return back()->with('error', 'No approved marks found for this student in the selected exam.');
        }

        // Calculate overall statistics
        $totalMarks = $marks->sum('total_marks');
        $maxMarks = $marks->sum(function($mark) {
            return $mark->exam->max_marks;
        });
        $overallPercentage = $maxMarks > 0 ? ($totalMarks / $maxMarks) * 100 : 0;

        // Use custom grading scale if provided, otherwise use exam's default
        $gradingScale = $validated['grading_scale_id']
            ? GradingScale::find($validated['grading_scale_id'])
            : $exam->gradingScale;

        // Determine overall grade
        $overallGrade = $this->calculateOverallGrade($overallPercentage, $gradingScale);
        $overallResult = $marks->contains('result', 'Fail') ? 'Fail' : 'Pass';

        // Generate overall remarks
        $failedSubjects = $marks->where('result', 'Fail')->count();
        $overallRemarks = $this->generateOverallRemarks($overallResult, $overallPercentage, $failedSubjects);

        // Determine if it's a custom template
        $isCustomTemplate = str_starts_with($validated['template'], 'custom_');
        $customTemplate = null;

        if ($isCustomTemplate) {
            $templateId = str_replace('custom_', '', $validated['template']);
            $customTemplate = MarksheetTemplate::findOrFail($templateId);
        }

        $data = [
            'student' => $student,
            'exam' => $exam,
            'marks' => $marks,
            'totalMarks' => $totalMarks,
            'maxMarks' => $maxMarks,
            'overallPercentage' => $overallPercentage,
            'overallGrade' => $overallGrade,
            'overallResult' => $overallResult,
            'overallRemarks' => $overallRemarks,
            'instituteSettings' => $instituteSettings,
            'generatedAt' => now(),
            'bikramSambatDate' => $this->convertToBikramSambat(now()),
            'template' => $customTemplate, // Add custom template data
            'gradingScale' => $gradingScale, // Add selected grading scale
        ];

        // Choose template view
        if ($isCustomTemplate) {
            // For custom templates, use the preview view with the template data
            $template = "admin.marksheets.customize.preview";
        } else {
            $template = "admin.marksheets.templates.{$validated['template']}";
        }

        if ($validated['format'] === 'pdf') {
            $pdf = Pdf::loadView($template, $data)
                     ->setPaper('a4', 'portrait')
                     ->setOptions([
                         'defaultFont' => 'DejaVu Sans',
                         'isHtml5ParserEnabled' => true,
                         'isPhpEnabled' => true,
                     ]);

            $templateName = $isCustomTemplate ? $customTemplate->name : $validated['template'];
            $filename = "marksheet_{$student->currentEnrollment->roll_no}_{$exam->name}_{$templateName}.pdf";

            return $pdf->download($filename);
        } else {
            return view($template, $data);
        }
    }

    /**
     * Generate bulk marksheets.
     */
    public function bulkGenerate(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'nullable|exists:classes,id',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'template' => 'required|string',
            'grading_scale_id' => 'nullable|exists:grading_scales,id',
        ]);

        $exam = Exam::with(['academicYear', 'class', 'subject', 'gradingScale'])
                    ->findOrFail($validated['exam_id']);

        // Use custom grading scale if provided, otherwise use exam's default
        $gradingScale = $validated['grading_scale_id']
            ? GradingScale::find($validated['grading_scale_id'])
            : $exam->gradingScale;

        // Determine if it's a custom template
        $isCustomTemplate = str_starts_with($validated['template'], 'custom_');
        $customTemplate = null;

        if ($isCustomTemplate) {
            $templateId = str_replace('custom_', '', $validated['template']);
            $customTemplate = MarksheetTemplate::findOrFail($templateId);
        }

        // Get institute settings
        $instituteSettings = InstituteSettings::current();

        $students = Student::with(['currentEnrollment.class', 'currentEnrollment.program'])
                          ->whereIn('id', $validated['student_ids'])
                          ->get()
                          ->sortBy('currentEnrollment.roll_no');

        $marksheets = [];

        foreach ($students as $student) {
            $marks = Mark::with(['subject'])
                        ->where('student_id', $student->id)
                        ->where('exam_id', $validated['exam_id'])
                        ->where('status', 'approved')
                        ->get();

            if ($marks->isNotEmpty()) {
                $totalMarks = $marks->sum('total_marks');
                $maxMarks = $marks->sum(function($mark) {
                    return $mark->exam->max_marks;
                });
                $overallPercentage = $maxMarks > 0 ? ($totalMarks / $maxMarks) * 100 : 0;
                $overallGrade = $this->calculateOverallGrade($overallPercentage, $gradingScale);
                $overallResult = $marks->contains('result', 'Fail') ? 'Fail' : 'Pass';

                // Generate overall remarks
                $failedSubjects = $marks->where('result', 'Fail')->count();
                $overallRemarks = $this->generateOverallRemarks($overallResult, $overallPercentage, $failedSubjects);

                $marksheets[] = [
                    'student' => $student,
                    'marks' => $marks,
                    'totalMarks' => $totalMarks,
                    'maxMarks' => $maxMarks,
                    'overallPercentage' => $overallPercentage,
                    'overallGrade' => $overallGrade,
                    'overallResult' => $overallResult,
                    'overallRemarks' => $overallRemarks,
                ];
            }
        }

        if (empty($marksheets)) {
            return back()->with('error', 'No approved marks found for the selected students.');
        }

        $data = [
            'exam' => $exam,
            'marksheets' => $marksheets,
            'instituteSettings' => $instituteSettings,
            'generatedAt' => now(),
            'bikramSambatDate' => $this->convertToBikramSambat(now()),
            'template' => $customTemplate,
            'gradingScale' => $gradingScale,
        ];

        // Choose template view for bulk generation
        if ($isCustomTemplate) {
            // For custom templates, use a generic bulk view that can handle custom templates
            $template = "admin.marksheets.templates.bulk_custom";
        } else {
            $template = "admin.marksheets.templates.bulk_{$validated['template']}";
        }

        $pdf = Pdf::loadView($template, $data)
                 ->setPaper('a4', 'portrait')
                 ->setOptions([
                     'defaultFont' => 'DejaVu Sans',
                     'isHtml5ParserEnabled' => true,
                     'isPhpEnabled' => true,
                 ]);

        $filename = "bulk_marksheets_{$exam->name}_{$validated['template']}_" . now()->format('Y-m-d') . ".pdf";

        return $pdf->download($filename);
    }

    /**
     * Preview marksheet template.
     */
    public function preview(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'exam_id' => 'required|exists:exams,id',
            'template' => 'required|string',
            'grading_scale_id' => 'nullable|exists:grading_scales,id',
        ]);

        $student = Student::with(['currentEnrollment.class', 'currentEnrollment.program'])->findOrFail($validated['student_id']);
        $exam = Exam::with(['academicYear', 'class', 'subject', 'gradingScale'])
                    ->findOrFail($validated['exam_id']);

        // Get institute settings
        $instituteSettings = InstituteSettings::current();

        $marks = Mark::with(['subject'])
                    ->where('student_id', $validated['student_id'])
                    ->where('exam_id', $validated['exam_id'])
                    ->where('status', 'approved')
                    ->get();

        if ($marks->isEmpty()) {
            return back()->with('error', 'No approved marks found for preview.');
        }

        $totalMarks = $marks->sum('total_marks');
        $maxMarks = $marks->sum(function($mark) {
            return $mark->exam->max_marks;
        });
        // Use custom grading scale if provided, otherwise use exam's default
        $gradingScale = $validated['grading_scale_id']
            ? GradingScale::find($validated['grading_scale_id'])
            : $exam->gradingScale;

        // Determine if it's a custom template
        $isCustomTemplate = str_starts_with($validated['template'], 'custom_');
        $customTemplate = null;

        if ($isCustomTemplate) {
            $templateId = str_replace('custom_', '', $validated['template']);
            $customTemplate = MarksheetTemplate::findOrFail($templateId);
        }

        $overallPercentage = $maxMarks > 0 ? ($totalMarks / $maxMarks) * 100 : 0;
        $overallGrade = $this->calculateOverallGrade($overallPercentage, $gradingScale);
        $overallResult = $marks->contains('result', 'Fail') ? 'Fail' : 'Pass';

        // Generate overall remarks
        $failedSubjects = $marks->where('result', 'Fail')->count();
        $overallRemarks = $this->generateOverallRemarks($overallResult, $overallPercentage, $failedSubjects);

        $data = [
            'student' => $student,
            'exam' => $exam,
            'marks' => $marks,
            'totalMarks' => $totalMarks,
            'maxMarks' => $maxMarks,
            'overallPercentage' => $overallPercentage,
            'overallGrade' => $overallGrade,
            'overallResult' => $overallResult,
            'overallRemarks' => $overallRemarks,
            'instituteSettings' => $instituteSettings,
            'generatedAt' => now(),
            'bikramSambatDate' => $this->convertToBikramSambat(now()),
            'template' => $customTemplate,
            'gradingScale' => $gradingScale,
            'isPreview' => true,
        ];

        // Choose template view
        if ($isCustomTemplate) {
            $template = "admin.marksheets.customize.preview";
        } else {
            $template = "admin.marksheets.templates.{$validated['template']}";
        }

        return view($template, $data);
    }

    /**
     * Calculate overall grade based on percentage.
     */
    private function calculateOverallGrade($percentage, $gradingScale)
    {
        if (!$gradingScale) {
            return 'N/A';
        }

        $gradeRange = $gradingScale->gradeRanges()
                                 ->where('min_percentage', '<=', $percentage)
                                 ->where('max_percentage', '>=', $percentage)
                                 ->first();

        return $gradeRange ? $gradeRange->grade : 'N/A';
    }

    /**
     * Generate overall remarks based on performance.
     */
    private function generateOverallRemarks($result, $percentage, $failedSubjects)
    {
        if ($result === 'Pass') {
            if ($percentage >= 90) {
                return 'Outstanding performance! Excellent work in all subjects.';
            } elseif ($percentage >= 80) {
                return 'Very good performance. Keep up the excellent work.';
            } elseif ($percentage >= 70) {
                return 'Good performance. Continue working hard.';
            } elseif ($percentage >= 60) {
                return 'Satisfactory performance. There is room for improvement.';
            } else {
                return 'Passed with minimum requirements. More effort needed.';
            }
        } else {
            if ($failedSubjects == 1) {
                return 'Failed in one subject. Focus on improvement in the failed subject.';
            } elseif ($failedSubjects <= 3) {
                return 'Failed in ' . $failedSubjects . ' subjects. Significant improvement needed.';
            } else {
                return 'Failed in multiple subjects. Comprehensive study plan required.';
            }
        }
    }

    /**
     * Convert Gregorian date to Bikram Sambat.
     */
    private function convertToBikramSambat($date)
    {
        // Simple conversion - in real implementation, use proper BS conversion library
        $bsYear = $date->year + 57;
        return $bsYear . '-' . $date->format('m-d') . ' BS';
    }
}
