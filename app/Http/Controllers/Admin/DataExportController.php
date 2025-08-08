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
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DataExportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:export-data']);
    }

    /**
     * Display export/import dashboard.
     */
    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_marks' => Mark::count(),
            'total_exams' => Exam::count(),
            'total_classes' => ClassModel::count(),
        ];

        return view('admin.data-export.index', compact('stats'));
    }

    /**
     * Export students data.
     */
    public function exportStudents(Request $request)
    {
        $format = $request->get('format', 'csv');
        $classId = $request->get('class_id');
        $academicYearId = $request->get('academic_year_id');

        $query = Student::with(['currentEnrollment.class.level', 'currentEnrollment.program']);

        if ($classId) {
            $query->whereHas('currentEnrollment', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if ($academicYearId) {
            $query->whereHas('currentEnrollment.academicYear', function($q) use ($academicYearId) {
                $q->where('id', $academicYearId);
            });
        }

        $students = $query->get();

        if ($format === 'pdf') {
            return $this->exportStudentsToPdf($students);
        } else {
            return $this->exportStudentsToCsv($students);
        }
    }

    /**
     * Export marks data.
     */
    public function exportMarks(Request $request)
    {
        $format = $request->get('format', 'csv');
        $examId = $request->get('exam_id');
        $classId = $request->get('class_id');
        $subjectId = $request->get('subject_id');

        $query = Mark::with(['student', 'exam', 'subject'])->where('status', 'approved');

        if ($examId) {
            $query->where('exam_id', $examId);
        }

        if ($classId) {
            $query->whereHas('student', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $marks = $query->get();

        if ($format === 'pdf') {
            return $this->exportMarksToPdf($marks);
        } else {
            return $this->exportMarksToCsv($marks);
        }
    }

    /**
     * Export exam results.
     */
    public function exportResults(Request $request)
    {
        $format = $request->get('format', 'csv');
        $examId = $request->get('exam_id');
        $classId = $request->get('class_id');

        $query = Mark::with(['student.currentEnrollment.class', 'exam', 'subject'])
                    ->where('status', 'approved');

        if ($examId) {
            $query->where('exam_id', $examId);
        }

        if ($classId) {
            $query->whereHas('student.currentEnrollment', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        $marks = $query->get();

        // Group marks by student for result compilation
        $results = $marks->groupBy('student_id')->map(function($studentMarks) {
            $student = $studentMarks->first()->student;
            $totalMarks = $studentMarks->sum('obtained_marks');
            $maxMarks = $studentMarks->sum('total_marks');
            $percentage = $maxMarks > 0 ? ($totalMarks / $maxMarks) * 100 : 0;
            $overallResult = $studentMarks->contains('result', 'Fail') ? 'Fail' : 'Pass';

            return [
                'student' => $student,
                'marks' => $studentMarks,
                'total_marks' => $totalMarks,
                'max_marks' => $maxMarks,
                'percentage' => $percentage,
                'overall_result' => $overallResult,
            ];
        });

        if ($format === 'pdf') {
            return $this->exportResultsToPdf($results);
        } else {
            return $this->exportResultsToCsv($results);
        }
    }

    /**
     * Export analytics data.
     */
    public function exportAnalytics(Request $request)
    {
        $type = $request->get('type', 'overview');
        $format = $request->get('format', 'csv');
        $academicYearId = $request->get('academic_year_id');

        $academicYear = AcademicYear::findOrFail($academicYearId);

        switch ($type) {
            case 'class_performance':
                return $this->exportClassPerformance($academicYear, $format);
            case 'subject_performance':
                return $this->exportSubjectPerformance($academicYear, $format);
            case 'student_performance':
                return $this->exportStudentPerformance($academicYear, $format);
            default:
                return $this->exportOverviewAnalytics($academicYear, $format);
        }
    }

    /**
     * Import students data.
     */
    public function importStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'class_id' => 'required|exists:classes,id',
        ]);

        $file = $request->file('file');
        $classId = $request->class_id;

        try {
            $data = array_map('str_getcsv', file($file->path()));
            $headers = array_shift($data); // Remove header row

            $imported = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                try {
                    if (count($row) < 3) {
                        $errors[] = "Row " . ($index + 2) . ": Insufficient data";
                        continue;
                    }

                    Student::create([
                        'name' => $row[0],
                        'roll_number' => $row[1],
                        'email' => $row[2] ?? null,
                        'phone' => $row[3] ?? null,
                        'address' => $row[4] ?? null,
                        'class_id' => $classId,
                        'status' => 'active',
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Successfully imported {$imported} students.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " errors occurred.";
            }

            return back()->with('success', $message)->with('import_errors', $errors);

        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Import marks data.
     */
    public function importMarks(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $file = $request->file('file');
        $examId = $request->exam_id;
        $subjectId = $request->subject_id;

        try {
            $data = array_map('str_getcsv', file($file->path()));
            $headers = array_shift($data); // Remove header row

            $imported = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                try {
                    if (count($row) < 2) {
                        $errors[] = "Row " . ($index + 2) . ": Insufficient data";
                        continue;
                    }

                    $student = Student::where('roll_number', $row[0])->first();
                    if (!$student) {
                        $errors[] = "Row " . ($index + 2) . ": Student not found with roll number " . $row[0];
                        continue;
                    }

                    $obtainedMarks = floatval($row[1]);
                    $exam = Exam::findOrFail($examId);

                    // Calculate percentage and grade
                    $percentage = ($obtainedMarks / $exam->max_marks) * 100;
                    $gradeInfo = $exam->gradingScale ? $exam->gradingScale->calculateGrade($percentage) : [
                        'grade' => 'N/A',
                        'gpa' => 0,
                        'result' => $percentage >= 40 ? 'Pass' : 'Fail'
                    ];

                    Mark::updateOrCreate([
                        'student_id' => $student->id,
                        'exam_id' => $examId,
                        'subject_id' => $subjectId,
                    ], [
                        'obtained_marks' => $obtainedMarks,
                        'total_marks' => $exam->max_marks,
                        'percentage' => $percentage,
                        'grade' => $gradeInfo['grade'],
                        'gpa' => $gradeInfo['gpa'],
                        'result' => $gradeInfo['result'],
                        'status' => 'draft',
                        'entered_by' => auth()->id(),
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Successfully imported {$imported} marks.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " errors occurred.";
            }

            return back()->with('success', $message)->with('import_errors', $errors);

        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Export students to CSV.
     */
    private function exportStudentsToCsv($students)
    {
        $filename = 'students_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Name', 'Roll Number', 'Email', 'Phone', 'Address',
                'Class', 'Level', 'Program', 'Status', 'Created At'
            ]);

            // Add data rows
            foreach ($students as $student) {
                fputcsv($file, [
                    $student->name,
                    $student->roll_number,
                    $student->email,
                    $student->phone,
                    $student->address,
                    $student->class->name ?? '',
                    $student->class->level->name ?? '',
                    $student->program->name ?? '',
                    $student->status,
                    $student->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export students to PDF.
     */
    private function exportStudentsToPdf($students)
    {
        $data = [
            'students' => $students,
            'title' => 'Students List',
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('admin.exports.students-pdf', $data);
        return $pdf->download('students_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Export marks to CSV.
     */
    private function exportMarksToCsv($marks)
    {
        $filename = 'marks_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($marks) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Student Name', 'Roll Number', 'Class', 'Subject', 'Exam',
                'Obtained Marks', 'Total Marks', 'Percentage', 'Grade', 'Result', 'Status'
            ]);

            // Add data rows
            foreach ($marks as $mark) {
                fputcsv($file, [
                    $mark->student->name,
                    $mark->student->roll_number,
                    $mark->student->class->name ?? '',
                    $mark->subject->name,
                    $mark->exam->name,
                    $mark->obtained_marks,
                    $mark->total_marks,
                    number_format($mark->percentage, 2),
                    $mark->grade,
                    $mark->result,
                    $mark->status,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export marks to PDF.
     */
    private function exportMarksToPdf($marks)
    {
        $data = [
            'marks' => $marks,
            'title' => 'Marks Report',
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('admin.exports.marks-pdf', $data);
        return $pdf->download('marks_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Export results to CSV.
     */
    private function exportResultsToCsv($results)
    {
        $filename = 'results_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($results) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Student Name', 'Roll Number', 'Class', 'Total Marks',
                'Max Marks', 'Percentage', 'Overall Result'
            ]);

            // Add data rows
            foreach ($results as $result) {
                fputcsv($file, [
                    $result['student']->name,
                    $result['student']->roll_number,
                    $result['student']->class->name ?? '',
                    $result['total_marks'],
                    $result['max_marks'],
                    number_format($result['percentage'], 2),
                    $result['overall_result'],
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export results to PDF.
     */
    private function exportResultsToPdf($results)
    {
        $data = [
            'results' => $results,
            'title' => 'Results Report',
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('admin.exports.results-pdf', $data);
        return $pdf->download('results_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Export class performance.
     */
    private function exportClassPerformance($academicYear, $format)
    {
        $classPerformance = ClassModel::with(['students.marks' => function($q) use ($academicYear) {
            $q->whereHas('exam', function($eq) use ($academicYear) {
                $eq->where('academic_year_id', $academicYear->id);
            })->where('status', 'approved');
        }])->get()->map(function($class) {
            $allMarks = $class->students->flatMap->marks;
            $studentCount = $class->students->count();

            return [
                'class_name' => $class->name,
                'level' => $class->level->name,
                'total_students' => $studentCount,
                'average_percentage' => $allMarks->avg('percentage'),
                'pass_count' => $allMarks->where('result', 'Pass')->count(),
                'fail_count' => $allMarks->where('result', 'Fail')->count(),
            ];
        });

        if ($format === 'pdf') {
            $data = [
                'performance' => $classPerformance,
                'title' => 'Class Performance Report - ' . $academicYear->name,
                'generated_at' => now(),
            ];
            $pdf = Pdf::loadView('admin.exports.class-performance-pdf', $data);
            return $pdf->download('class_performance_' . date('Y-m-d_H-i-s') . '.pdf');
        } else {
            return $this->exportClassPerformanceToCsv($classPerformance);
        }
    }

    /**
     * Export class performance to CSV.
     */
    private function exportClassPerformanceToCsv($classPerformance)
    {
        $filename = 'class_performance_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($classPerformance) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Class Name', 'Level', 'Total Students', 'Average Percentage',
                'Pass Count', 'Fail Count'
            ]);

            foreach ($classPerformance as $performance) {
                fputcsv($file, [
                    $performance['class_name'],
                    $performance['level'],
                    $performance['total_students'],
                    number_format($performance['average_percentage'], 2),
                    $performance['pass_count'],
                    $performance['fail_count'],
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export subject performance.
     */
    private function exportSubjectPerformance($academicYear, $format)
    {
        // Implementation similar to class performance
        return response()->json(['message' => 'Subject performance export not implemented yet']);
    }

    /**
     * Export student performance.
     */
    private function exportStudentPerformance($academicYear, $format)
    {
        // Implementation similar to class performance
        return response()->json(['message' => 'Student performance export not implemented yet']);
    }

    /**
     * Export overview analytics.
     */
    private function exportOverviewAnalytics($academicYear, $format)
    {
        // Implementation for overview analytics export
        return response()->json(['message' => 'Overview analytics export not implemented yet']);
    }
}
