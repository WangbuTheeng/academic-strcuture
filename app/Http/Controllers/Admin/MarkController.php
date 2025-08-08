<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mark;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarkController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:enter-marks']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Exam::with(['academicYear', 'semester', 'class', 'subject'])
                     ->where('result_status', 'ongoing');

        // Filter by class if user is teacher
        if (auth()->user()->hasRole('teacher')) {
            // Get teacher's assigned subjects and classes
            $teacherSubjects = auth()->user()->teacherSubjects()->pluck('subject_id');
            $teacherClasses = auth()->user()->teacherSubjects()->pluck('class_id');

            $query->where(function($q) use ($teacherSubjects, $teacherClasses) {
                $q->whereIn('subject_id', $teacherSubjects)
                  ->orWhereIn('class_id', $teacherClasses)
                  ->orWhereNull('subject_id'); // Institution-wide exams
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Class filter
        if ($request->filled('class')) {
            $query->where('class_id', $request->class);
        }

        // Subject filter
        if ($request->filled('subject')) {
            $query->where('subject_id', $request->subject);
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $classes = ClassModel::with('level')->get();
        $subjects = Subject::with('department')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.marks.index', compact('exams', 'classes', 'subjects', 'academicYears'));
    }

    /**
     * Show mark entry form for specific exam.
     */
    public function create(Request $request)
    {
        $examId = $request->get('exam_id');
        $subjectId = $request->get('subject_id');
        $classId = $request->get('class_id');

        if (!$examId) {
            return redirect()->route('admin.marks.index')
                            ->with('error', 'Please select an exam first.');
        }

        $exam = Exam::with(['academicYear', 'semester', 'class', 'subject', 'gradingScale'])
                    ->findOrFail($examId);

        if (!$exam->can_enter_marks) {
            return redirect()->route('admin.marks.index')
                            ->with('error', 'Marks cannot be entered for this exam in its current status.');
        }

        // Get students based on exam scope
        $studentsQuery = Student::with(['currentEnrollment.class', 'currentEnrollment.program'])
                               ->where('status', 'active');

        if ($exam->class_id) {
            $studentsQuery->whereHas('currentEnrollment', function($q) use ($exam) {
                $q->where('class_id', $exam->class_id);
            });
        } elseif ($classId) {
            $studentsQuery->whereHas('currentEnrollment', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if ($exam->program_id) {
            $studentsQuery->whereHas('currentEnrollment', function($q) use ($exam) {
                $q->where('program_id', $exam->program_id);
            });
        }

        $students = $studentsQuery->with('currentEnrollment')
                                  ->get()
                                  ->sortBy('currentEnrollment.roll_no');

        // Get existing marks
        $existingMarks = Mark::where('exam_id', $examId)
                            ->when($subjectId, function($q) use ($subjectId) {
                                return $q->where('subject_id', $subjectId);
                            })
                            ->get()
                            ->keyBy(function($mark) {
                                return $mark->student_id . '_' . $mark->subject_id;
                            });

        // Get subjects for this exam
        $subjects = collect();
        if ($exam->subject_id) {
            $subjects = collect([$exam->subject]);
        } elseif ($subjectId) {
            $subjects = Subject::where('id', $subjectId)->get();
        } else {
            // Get all subjects for the class/program
            if ($exam->class_id) {
                $subjects = $exam->class->subjects;
            } else {
                $subjects = Subject::all();
            }
        }

        return view('admin.marks.create', compact(
            'exam', 'students', 'subjects', 'existingMarks', 'subjectId', 'classId'
        ));
    }

    /**
     * Store marks in storage.
     */
    public function store(Request $request)
    {
        $exam = Exam::findOrFail($request->exam_id);

        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.assess_marks' => [
                'nullable',
                'numeric',
                'min:0',
                $exam->assess_max ? 'max:' . $exam->assess_max : 'max:0'
            ],
            'marks.*.theory_marks' => [
                'nullable',
                'numeric',
                'min:0',
                $exam->theory_max ? 'max:' . $exam->theory_max : 'max:0'
            ],
            'marks.*.practical_marks' => [
                'nullable',
                'numeric',
                'min:0',
                $exam->practical_max ? 'max:' . $exam->practical_max : 'max:0'
            ],
            'marks.*.remarks' => 'nullable|string|max:500',
        ], [
            'marks.*.assess_marks.max' => 'Assessment marks cannot exceed ' . $exam->assess_max,
            'marks.*.theory_marks.max' => 'Theory marks cannot exceed ' . $exam->theory_max,
            'marks.*.practical_marks.max' => 'Practical marks cannot exceed ' . $exam->practical_max,
            'marks.*.assess_marks.min' => 'Assessment marks cannot be negative',
            'marks.*.theory_marks.min' => 'Theory marks cannot be negative',
            'marks.*.practical_marks.min' => 'Practical marks cannot be negative',
        ]);

        if (!$exam->can_enter_marks) {
            return back()->with('error', 'Marks cannot be entered for this exam.');
        }

        DB::transaction(function () use ($validated, $exam) {
            foreach ($validated['marks'] as $markData) {
                // Validate mark limits
                $errors = [];

                if (isset($markData['assess_marks']) && $markData['assess_marks'] > $exam->assess_max) {
                    $errors[] = "Assessment marks cannot exceed {$exam->assess_max}";
                }

                if (isset($markData['theory_marks']) && $markData['theory_marks'] > $exam->theory_max) {
                    $errors[] = "Theory marks cannot exceed {$exam->theory_max}";
                }

                if (isset($markData['practical_marks']) && $markData['practical_marks'] > $exam->practical_max) {
                    $errors[] = "Practical marks cannot exceed {$exam->practical_max}";
                }

                if (!empty($errors)) {
                    throw new \Exception(implode(', ', $errors));
                }

                // Create or update mark
                $mark = Mark::updateOrCreate(
                    [
                        'student_id' => $markData['student_id'],
                        'exam_id' => $validated['exam_id'],
                        'subject_id' => $validated['subject_id'],
                    ],
                    [
                        'assess_marks' => $markData['assess_marks'] ?? 0,
                        'theory_marks' => $markData['theory_marks'] ?? 0,
                        'practical_marks' => $markData['practical_marks'] ?? 0,
                        'remarks' => $markData['remarks'] ?? null,
                        'status' => 'draft',
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]
                );

                // Perform calculations
                $mark->performCalculations();
                $mark->save();
            }
        });

        return redirect()->route('admin.marks.exam-dashboard', ['exam' => $validated['exam_id']])
                        ->with('success', 'Marks saved successfully.');
    }

    /**
     * Display exam-specific marks dashboard with all subjects.
     */
    public function examDashboard(Exam $exam)
    {
        $exam->load(['academicYear', 'class', 'subject']);

        // Get all subjects for this exam's class through the class_subjects pivot table
        if ($exam->subject_id) {
            // If exam is for a specific subject, only show that subject
            $subjects = Subject::where('id', $exam->subject_id)->get();
        } else {
            // Get all subjects assigned to this class
            $subjects = Subject::whereHas('classes', function($query) use ($exam) {
                $query->where('class_id', $exam->class_id);
            })->get();

            // If no subjects found through class relationship, get all active subjects as fallback
            // This ensures the dashboard works even if class-subject relationships aren't properly set up
            if ($subjects->isEmpty()) {
                $subjects = Subject::where('is_active', true)->get();
            }
        }

        // Get mark entry status for each subject
        $subjectStatuses = [];
        foreach ($subjects as $subject) {
            $totalStudents = Student::whereHas('enrollments', function($query) use ($exam) {
                $query->where('academic_year_id', $exam->academic_year_id)
                      ->where('class_id', $exam->class_id);
            })->count();

            $marksEntered = Mark::where('exam_id', $exam->id)
                               ->where('subject_id', $subject->id)
                               ->count();

            $subjectStatuses[$subject->id] = [
                'subject' => $subject,
                'total_students' => $totalStudents,
                'marks_entered' => $marksEntered,
                'completion_percentage' => $totalStudents > 0 ? round(($marksEntered / $totalStudents) * 100, 1) : 0,
                'is_complete' => $marksEntered >= $totalStudents && $totalStudents > 0,
            ];
        }

        return view('admin.marks.exam-dashboard', compact('exam', 'subjectStatuses'));
    }

    /**
     * Display marks for a specific exam.
     */
    public function show(Exam $exam)
    {
        $exam->load([
            'academicYear', 'semester', 'class', 'subject', 'gradingScale',
            'marks.student.currentEnrollment.class', 'marks.subject'
        ]);

        $marksBySubject = $exam->marks->groupBy('subject_id');

        $statistics = [
            'total_marks_entered' => $exam->marks->count(),
            'subjects_covered' => $marksBySubject->count(),
            'average_percentage' => $exam->marks->avg('percentage'),
            'highest_percentage' => $exam->marks->max('percentage'),
            'lowest_percentage' => $exam->marks->min('percentage'),
            'pass_count' => $exam->marks->where('result', 'Pass')->count(),
            'fail_count' => $exam->marks->where('result', 'Fail')->count(),
        ];

        return view('admin.marks.show', compact('exam', 'marksBySubject', 'statistics'));
    }

    /**
     * Show the form for editing the specified mark.
     */
    public function edit(Mark $mark)
    {
        $mark->load(['student.currentEnrollment.class', 'exam.gradingScale', 'subject']);

        if ($mark->status !== 'draft') {
            return redirect()->route('admin.marks.show', $mark->exam)
                            ->with('error', 'Only draft marks can be edited.');
        }

        return view('admin.marks.edit', compact('mark'));
    }

    /**
     * Update the specified mark in storage.
     */
    public function update(Request $request, Mark $mark)
    {
        if ($mark->status !== 'draft') {
            return redirect()->route('admin.marks.show', $mark->exam)
                            ->with('error', 'Only draft marks can be updated.');
        }

        $validated = $request->validate([
            'assess_marks' => 'nullable|numeric|min:0|max:' . $mark->exam->assess_max,
            'theory_marks' => 'nullable|numeric|min:0|max:' . $mark->exam->theory_max,
            'practical_marks' => 'nullable|numeric|min:0|max:' . $mark->exam->practical_max,
            'remarks' => 'nullable|string|max:500',
        ]);

        // Calculate total and percentage
        $total = ($validated['assess_marks'] ?? 0) +
                ($validated['theory_marks'] ?? 0) +
                ($validated['practical_marks'] ?? 0);

        $percentage = $mark->exam->max_marks > 0 ? ($total / $mark->exam->max_marks) * 100 : 0;

        // Calculate grade based on grading scale or simple grading
        $grade = $this->calculateGrade($percentage, $mark->exam->gradingScale);

        // Determine result
        $result = $percentage >= 32 ? 'Pass' : 'Fail'; // Simple pass/fail logic

        $mark->update([
            'assess_marks' => $validated['assess_marks'],
            'theory_marks' => $validated['theory_marks'],
            'practical_marks' => $validated['practical_marks'],
            'total' => $total,
            'percentage' => $percentage,
            'grade' => $grade,
            'result' => $result,
            'remarks' => $validated['remarks'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.marks.show', $mark->exam)
                        ->with('success', 'Mark updated successfully.');
    }

    /**
     * Remove the specified mark from storage.
     */
    public function destroy(Mark $mark)
    {
        if ($mark->status !== 'draft') {
            return redirect()->route('admin.marks.show', $mark->exam)
                            ->with('error', 'Only draft marks can be deleted.');
        }

        $exam = $mark->exam;
        $mark->delete();

        return redirect()->route('admin.marks.show', $exam)
                        ->with('success', 'Mark deleted successfully.');
    }

    /**
     * Submit marks for approval.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $query = Mark::where('exam_id', $validated['exam_id'])
                    ->where('status', 'draft');

        if (isset($validated['subject_id'])) {
            $query->where('subject_id', $validated['subject_id']);
        }

        $marks = $query->get();

        if ($marks->isEmpty()) {
            return back()->with('error', 'No marks found to submit.');
        }

        DB::transaction(function () use ($marks) {
            foreach ($marks as $mark) {
                $mark->submit();
            }
        });

        return back()->with('success', "Successfully submitted {$marks->count()} marks for approval.");
    }

    /**
     * Approve marks.
     */
    public function approve(Request $request)
    {
        $validated = $request->validate([
            'mark_ids' => 'required|array',
            'mark_ids.*' => 'exists:marks,id',
        ]);

        $marks = Mark::whereIn('id', $validated['mark_ids'])
                    ->where('status', 'submitted')
                    ->get();

        DB::transaction(function () use ($marks) {
            foreach ($marks as $mark) {
                $mark->approve();
            }
        });

        return back()->with('success', "Successfully approved {$marks->count()} marks.");
    }

    /**
     * Get submitted marks for an exam (for approval).
     */
    public function getSubmittedMarks(Exam $exam)
    {
        $markIds = $exam->marks()
                       ->where('status', 'submitted')
                       ->pluck('id')
                       ->toArray();

        return response()->json([
            'mark_ids' => $markIds,
            'count' => count($markIds)
        ]);
    }

    /**
     * Apply grace marks.
     */
    public function applyGrace(Request $request)
    {
        $validated = $request->validate([
            'mark_id' => 'required|exists:marks,id',
            'grace_marks' => 'required|numeric|min:0|max:10',
            'grace_reason' => 'required|string|max:500',
        ]);

        $mark = Mark::findOrFail($validated['mark_id']);

        // Check permission
        if (!auth()->user()->hasRole(['admin', 'principal'])) {
            return back()->with('error', 'You do not have permission to apply grace marks.');
        }

        $mark->applyGraceMarks($validated['grace_marks'], $validated['grace_reason']);

        return back()->with('success', 'Grace marks applied successfully.');
    }

    /**
     * Submit all marks for an exam.
     */
    public function submitAllMarks(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        // Check if user has permission
        if (!auth()->user()->hasRole(['admin', 'teacher'])) {
            return back()->with('error', 'You do not have permission to submit marks.');
        }

        // Get all marks for this exam
        $marks = Mark::where('exam_id', $exam->id)->get();

        if ($marks->isEmpty()) {
            return back()->with('error', 'No marks found for this exam.');
        }

        // Get subjects for this exam (same logic as examDashboard method)
        if ($exam->subject_id) {
            // If exam is for a specific subject, only get that subject
            $examSubjects = Subject::where('id', $exam->subject_id)->pluck('id');
        } else {
            // Get all subjects assigned to this class
            $examSubjects = Subject::whereHas('classes', function($query) use ($exam) {
                $query->where('class_id', $exam->class_id);
            })->pluck('id');

            // If no subjects found through class relationship, get all active subjects as fallback
            if ($examSubjects->isEmpty()) {
                $examSubjects = Subject::where('is_active', true)->pluck('id');
            }
        }

        $markedSubjects = $marks->pluck('subject_id')->unique();

        if ($examSubjects->count() !== $markedSubjects->count()) {
            return back()->with('error', 'Please enter marks for all subjects before submitting.');
        }

        // Get students for this exam (similar to create method logic)
        $studentsQuery = Student::with(['currentEnrollment.class', 'currentEnrollment.program'])
                               ->where('status', 'active');

        if ($exam->class_id) {
            $studentsQuery->whereHas('currentEnrollment', function($query) use ($exam) {
                $query->where('class_id', $exam->class_id)
                      ->where('academic_year_id', $exam->academic_year_id);
            });
        } else {
            $studentsQuery->whereHas('currentEnrollment', function($query) use ($exam) {
                $query->where('academic_year_id', $exam->academic_year_id);
            });
        }

        $totalStudents = $studentsQuery->count();
        $expectedMarksCount = $totalStudents * $examSubjects->count();

        if ($marks->count() < $expectedMarksCount) {
            return back()->with('error', 'Please enter marks for all students in all subjects before submitting.');
        }

        // Validate all marks are within acceptable ranges
        foreach ($marks as $mark) {
            $errors = [];

            if ($mark->assess_marks > $exam->assess_max) {
                $errors[] = "Assessment marks for student {$mark->student->full_name} exceed maximum ({$exam->assess_max})";
            }
            if ($mark->theory_marks > $exam->theory_max) {
                $errors[] = "Theory marks for student {$mark->student->full_name} exceed maximum ({$exam->theory_max})";
            }
            if ($mark->practical_marks > $exam->practical_max) {
                $errors[] = "Practical marks for student {$mark->student->full_name} exceed maximum ({$exam->practical_max})";
            }

            if (!empty($errors)) {
                return back()->with('error', 'Validation errors found: ' . implode(', ', $errors));
            }
        }

        // Submit all marks
        DB::transaction(function () use ($marks) {
            foreach ($marks as $mark) {
                if ($mark->status === 'draft') {
                    $mark->update([
                        'status' => 'submitted',
                        'submitted_at' => now(),
                        'updated_by' => auth()->id(),
                    ]);
                }
            }
        });

        return back()->with('success', 'All marks have been submitted successfully.');
    }

    /**
     * Bulk mark operations.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:submit,approve,reject',
            'mark_ids' => 'required|array',
            'mark_ids.*' => 'exists:marks,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $marks = Mark::whereIn('id', $validated['mark_ids'])->get();

        DB::transaction(function () use ($marks, $validated) {
            foreach ($marks as $mark) {
                switch ($validated['action']) {
                    case 'submit':
                        if ($mark->status === 'draft') {
                            $mark->submit();
                        }
                        break;
                    case 'approve':
                        if ($mark->status === 'submitted') {
                            $mark->approve();
                        }
                        break;
                    case 'reject':
                        if ($mark->status === 'submitted') {
                            $mark->reject($validated['reason']);
                        }
                        break;
                }
            }
        });

        $actionText = ucfirst($validated['action']) . 'd';
        return back()->with('success', "{$actionText} {$marks->count()} marks successfully.");
    }

    /**
     * Calculate grade based on percentage and grading scale.
     */
    private function calculateGrade($percentage, $gradingScale = null)
    {
        // Simple A-F grading if no grading scale is provided
        if (!$gradingScale) {
            if ($percentage >= 90) return 'A+';
            if ($percentage >= 80) return 'A';
            if ($percentage >= 70) return 'B+';
            if ($percentage >= 60) return 'B';
            if ($percentage >= 50) return 'C+';
            if ($percentage >= 40) return 'C';
            if ($percentage >= 32) return 'D';
            return 'F';
        }

        // Use grading scale if available
        // This would need to be implemented based on your grading scale structure
        return 'C'; // Placeholder
    }
}
