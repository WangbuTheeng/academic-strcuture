<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherSubject;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarkController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:teacher']);
    }

    /**
     * Display teacher's exams for mark entry.
     */
    public function index()
    {
        $teacher = Auth::user();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Get teacher's assigned subjects
        $assignedSubjects = TeacherSubject::with(['subject', 'class'])
            ->where('user_id', $teacher->id)
            ->where('academic_year_id', $currentAcademicYear->id ?? null)
            ->where('is_active', true)
            ->get();

        // Get exams for teacher's subjects
        $exams = Exam::with(['subject', 'class', 'academicYear'])
            ->whereIn('subject_id', $assignedSubjects->pluck('subject_id'))
            ->whereIn('class_id', $assignedSubjects->pluck('class_id'))
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        return view('teacher.marks.index', compact('exams', 'assignedSubjects'));
    }

    /**
     * Show mark entry form for specific exam.
     */
    public function create(Exam $exam)
    {
        $teacher = Auth::user();

        // Check if teacher is assigned to this subject and class
        $assignment = TeacherSubject::where('user_id', $teacher->id)
            ->where('subject_id', $exam->subject_id)
            ->where('class_id', $exam->class_id)
            ->where('is_active', true)
            ->first();

        if (!$assignment) {
            abort(403, 'You are not authorized to enter marks for this exam.');
        }

        if (!$exam->can_enter_marks) {
            return redirect()->route('teacher.marks.index')
                ->with('error', 'Mark entry is not allowed for this exam.');
        }

        // Get students enrolled in this class for current academic year
        $students = StudentEnrollment::with(['student'])
            ->where('class_id', $exam->class_id)
            ->where('academic_year_id', $exam->academic_year_id)
            ->where('status', 'active')
            ->get();

        // Get existing marks
        $existingMarks = Mark::where('exam_id', $exam->id)
            ->where('subject_id', $exam->subject_id)
            ->get()
            ->keyBy('student_id');

        return view('teacher.marks.create', compact('exam', 'students', 'existingMarks', 'assignment'));
    }

    /**
     * Store marks for exam.
     */
    public function store(Request $request, Exam $exam)
    {
        $teacher = Auth::user();

        // Verify teacher authorization
        $assignment = TeacherSubject::where('user_id', $teacher->id)
            ->where('subject_id', $exam->subject_id)
            ->where('class_id', $exam->class_id)
            ->where('is_active', true)
            ->first();

        if (!$assignment) {
            abort(403, 'You are not authorized to enter marks for this exam.');
        }

        $validated = $request->validate([
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.assess_marks' => 'nullable|numeric|min:0|max:' . ($exam->assess_max_marks ?? 100),
            'marks.*.theory_marks' => 'nullable|numeric|min:0|max:' . ($exam->theory_max_marks ?? 100),
            'marks.*.practical_marks' => 'nullable|numeric|min:0|max:' . ($exam->practical_max_marks ?? 100),
            'marks.*.remarks' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated, $exam, $teacher) {
            foreach ($validated['marks'] as $markData) {
                if (empty($markData['assess_marks']) && empty($markData['theory_marks']) && empty($markData['practical_marks'])) {
                    continue; // Skip if no marks entered
                }

                // Create or update mark
                $mark = Mark::updateOrCreate(
                    [
                        'student_id' => $markData['student_id'],
                        'exam_id' => $exam->id,
                        'subject_id' => $exam->subject_id,
                    ],
                    [
                        'assess_marks' => $markData['assess_marks'] ?? 0,
                        'theory_marks' => $markData['theory_marks'] ?? 0,
                        'practical_marks' => $markData['practical_marks'] ?? 0,
                        'remarks' => $markData['remarks'] ?? null,
                        'status' => 'draft',
                        'created_by' => $teacher->id,
                        'updated_by' => $teacher->id,
                    ]
                );

                // Perform calculations
                $mark->performCalculations();
                $mark->save();
            }
        });

        return redirect()->route('teacher.marks.create', $exam)
            ->with('success', 'Marks saved successfully.');
    }

    /**
     * Submit marks for approval.
     */
    public function submit(Request $request, Exam $exam)
    {
        $teacher = Auth::user();

        // Verify teacher authorization
        $assignment = TeacherSubject::where('user_id', $teacher->id)
            ->where('subject_id', $exam->subject_id)
            ->where('class_id', $exam->class_id)
            ->where('is_active', true)
            ->first();

        if (!$assignment) {
            abort(403, 'You are not authorized to submit marks for this exam.');
        }

        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        $marks = Mark::where('exam_id', $exam->id)
            ->where('subject_id', $exam->subject_id)
            ->where('created_by', $teacher->id)
            ->whereIn('student_id', $validated['student_ids'])
            ->where('status', 'draft')
            ->get();

        foreach ($marks as $mark) {
            $mark->submit();
        }

        return back()->with('success', 'Selected marks submitted for approval.');
    }

    /**
     * View results for teacher's subjects.
     */
    public function results()
    {
        $teacher = Auth::user();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Get teacher's assigned subjects
        $assignedSubjects = TeacherSubject::with(['subject', 'class'])
            ->where('user_id', $teacher->id)
            ->where('academic_year_id', $currentAcademicYear->id ?? null)
            ->where('is_active', true)
            ->get();

        // Get approved marks for teacher's subjects
        $results = Mark::with(['student', 'exam', 'subject'])
            ->whereIn('subject_id', $assignedSubjects->pluck('subject_id'))
            ->where('created_by', $teacher->id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('teacher.marks.results', compact('results', 'assignedSubjects'));
    }
}
