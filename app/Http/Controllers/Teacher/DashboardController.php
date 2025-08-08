<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherSubject;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\InstituteSettings;
use App\Services\SchoolContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $schoolContextService;

    public function __construct(SchoolContextService $schoolContextService)
    {
        $this->middleware(['auth', 'role:teacher']);
        $this->schoolContextService = $schoolContextService;
    }

    /**
     * Display teacher dashboard.
     */
    public function index()
    {
        $teacher = Auth::user();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Get school information
        $currentSchool = $this->schoolContextService->getCurrentSchool();
        $instituteSettings = InstituteSettings::where('school_id', $currentSchool?->id)->first();

        // Get teacher's assigned subjects for current academic year
        $assignedSubjects = TeacherSubject::with(['subject', 'class', 'class.level'])
            ->where('user_id', $teacher->id)
            ->where('academic_year_id', $currentAcademicYear->id ?? null)
            ->where('is_active', true)
            ->get();

        // Get upcoming exams for teacher's subjects
        $upcomingExams = Exam::with(['subject', 'class'])
            ->whereIn('subject_id', $assignedSubjects->pluck('subject_id'))
            ->whereIn('class_id', $assignedSubjects->pluck('class_id'))
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // Get recent exams where teacher can enter marks
        $activeExams = Exam::with(['subject', 'class'])
            ->whereIn('subject_id', $assignedSubjects->pluck('subject_id'))
            ->whereIn('class_id', $assignedSubjects->pluck('class_id'))
            ->where('result_status', 'ongoing')
            ->orderBy('start_date', 'desc')
            ->limit(5)
            ->get();

        // Get mark entry statistics
        $markStats = [
            'total_subjects' => $assignedSubjects->count(),
            'total_classes' => $assignedSubjects->pluck('class_id')->unique()->count(),
            'pending_marks' => $this->getPendingMarksCount($teacher->id),
            'submitted_marks' => $this->getSubmittedMarksCount($teacher->id),
        ];

        return view('teacher.dashboard', compact(
            'teacher',
            'assignedSubjects',
            'upcomingExams',
            'activeExams',
            'markStats',
            'currentAcademicYear',
            'currentSchool',
            'instituteSettings'
        ));
    }

    /**
     * Get count of pending marks for teacher.
     */
    private function getPendingMarksCount($teacherId)
    {
        $subjectIds = TeacherSubject::where('user_id', $teacherId)
            ->where('is_active', true)
            ->pluck('subject_id');

        return Mark::whereIn('subject_id', $subjectIds)
            ->where('created_by', $teacherId)
            ->where('status', 'draft')
            ->count();
    }

    /**
     * Get count of submitted marks for teacher.
     */
    private function getSubmittedMarksCount($teacherId)
    {
        $subjectIds = TeacherSubject::where('user_id', $teacherId)
            ->where('is_active', true)
            ->pluck('subject_id');

        return Mark::whereIn('subject_id', $subjectIds)
            ->where('created_by', $teacherId)
            ->whereIn('status', ['submitted', 'approved'])
            ->count();
    }

    /**
     * Display teacher profile.
     */
    public function profile()
    {
        $teacher = Auth::user();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Get all teacher's subject assignments
        $subjectAssignments = TeacherSubject::with([
            'subject', 
            'class', 
            'class.level', 
            'class.department',
            'academicYear'
        ])
        ->where('user_id', $teacher->id)
        ->orderBy('academic_year_id', 'desc')
        ->get()
        ->groupBy('academic_year_id');

        return view('teacher.profile', compact('teacher', 'subjectAssignments', 'currentAcademicYear'));
    }
}
