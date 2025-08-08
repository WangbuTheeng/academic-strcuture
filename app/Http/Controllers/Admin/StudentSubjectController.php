<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentEnrollment;
use App\Models\StudentSubject;
use App\Models\Subject;
use App\Models\Program;
use App\Services\AutoSubjectEnrollmentService;
use Illuminate\Http\Request;

class StudentSubjectController extends Controller
{
    /**
     * Display subject assignments for an enrollment.
     */
    public function index(StudentEnrollment $enrollment)
    {
        $enrollment->load(['student', 'program.department', 'class', 'academicYear']);

        $assignedSubjects = $enrollment->studentSubjects()->with('subject')->get();

        // Get available subjects for the program
        $availableSubjects = $enrollment->program->subjects()
            ->whereNotIn('subjects.id', $assignedSubjects->pluck('subject_id'))
            ->get();

        return view('admin.academic.student-subjects.index', compact(
            'enrollment', 'assignedSubjects', 'availableSubjects'
        ));
    }

    /**
     * Assign subjects to a student enrollment.
     */
    public function store(Request $request, StudentEnrollment $enrollment)
    {
        $request->validate([
            'subjects' => 'required|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

        foreach ($request->subjects as $subjectId) {
            // Check if already assigned
            $existing = StudentSubject::where('student_enrollment_id', $enrollment->id)
                ->where('subject_id', $subjectId)
                ->first();

            if (!$existing) {
                StudentSubject::create([
                    'student_enrollment_id' => $enrollment->id,
                    'subject_id' => $subjectId,
                    'date_added' => now(),
                    'status' => 'active'
                ]);
            }
        }

        return redirect()->route('admin.student-subjects.index', $enrollment)
                        ->with('success', 'Subjects assigned successfully.');
    }

    /**
     * Remove a subject assignment.
     */
    public function destroy(StudentEnrollment $enrollment, StudentSubject $studentSubject)
    {
        // Verify the student subject belongs to this enrollment
        if ($studentSubject->student_enrollment_id !== $enrollment->id) {
            return back()->with('error', 'Invalid subject assignment.');
        }

        $studentSubject->delete();

        return redirect()->route('admin.student-subjects.index', $enrollment)
                        ->with('success', 'Subject assignment removed successfully.');
    }

    /**
     * Update subject assignment status.
     */
    public function updateStatus(Request $request, StudentEnrollment $enrollment, StudentSubject $studentSubject)
    {
        $request->validate([
            'status' => 'required|in:active,dropped'
        ]);

        // Verify the student subject belongs to this enrollment
        if ($studentSubject->student_enrollment_id !== $enrollment->id) {
            return back()->with('error', 'Invalid subject assignment.');
        }

        $studentSubject->update(['status' => $request->status]);

        return redirect()->route('admin.student-subjects.index', $enrollment)
                        ->with('success', 'Subject status updated successfully.');
    }

    /**
     * Bulk assign subjects based on program requirements.
     */
    public function bulkAssignByProgram(StudentEnrollment $enrollment)
    {
        $autoEnrollmentService = new AutoSubjectEnrollmentService();
        $result = $autoEnrollmentService->autoEnrollSubjects($enrollment, [
            'compulsory_only' => false,  // Include all subjects (compulsory + elective)
            'program_subjects' => true,
            'class_subjects' => true,
        ]);

        if ($result['success']) {
            $message = 'Subjects assigned successfully. ';
            $message .= $result['summary']['enrolled_count'] . ' subjects enrolled, ';
            $message .= $result['summary']['skipped_count'] . ' already assigned.';
        } else {
            $message = 'Some subjects could not be assigned. Please check the logs.';
        }

        return redirect()->route('admin.student-subjects.index', $enrollment)
                        ->with($result['success'] ? 'success' : 'warning', $message);
    }

    /**
     * Preview what subjects would be enrolled (AJAX endpoint).
     */
    public function previewSubjectEnrollment(StudentEnrollment $enrollment)
    {
        $autoEnrollmentService = new AutoSubjectEnrollmentService();
        $preview = $autoEnrollmentService->previewSubjectEnrollment($enrollment, [
            'compulsory_only' => false,
            'program_subjects' => true,
            'class_subjects' => true,
        ]);

        return response()->json($preview);
    }

    /**
     * Get subjects by program (AJAX endpoint).
     */
    public function getSubjectsByProgram(Request $request)
    {
        $program = Program::find($request->program_id);

        if (!$program) {
            return response()->json([]);
        }

        $subjects = $program->subjects()->with('department')->get();

        return response()->json($subjects);
    }
}
