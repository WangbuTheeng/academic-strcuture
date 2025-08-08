<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Exam;
use App\Models\AcademicYear;
use App\Models\InstituteSettings;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Faculty;
use App\Models\Level;
use App\Services\SchoolContextService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $schoolContextService;

    public function __construct(SchoolContextService $schoolContextService)
    {
        $this->middleware('auth');
        $this->schoolContextService = $schoolContextService;
    }

    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get comprehensive school-specific statistics
        $schoolStats = $this->schoolContextService->getSchoolStats();

        // Enhanced statistics for professional dashboard
        $stats = [
            'total_users' => $schoolStats['total_users'] ?? User::count(),
            'total_students' => $schoolStats['total_students'] ?? Student::count(),
            'active_students' => Student::where('status', 'active')->count(),
            'total_exams' => Exam::count(),
            'pending_results' => Exam::where('result_status', 'submitted')->count(),
            'admin_users' => $schoolStats['admin_users'] ?? 0,
            'teacher_users' => $schoolStats['teacher_users'] ?? 0,
            'student_users' => $schoolStats['student_users'] ?? 0,

            // Additional stats for professional dashboard
            'total_classes' => ClassModel::count(),
            'total_subjects' => Subject::count(),
            'total_faculties' => Faculty::count(),
            'total_levels' => Level::count(),
            'monthly_admissions' => Student::whereMonth('created_at', Carbon::now()->month)
                                         ->whereYear('created_at', Carbon::now()->year)
                                         ->count(),
        ];

        // Check if institute settings are configured for current school
        $instituteSettings = InstituteSettings::first();
        $needsSetup = !$instituteSettings || !$instituteSettings->institution_name;

        // Enhanced recent activities with real data
        $recentActivities = $this->getRecentActivities();

        // Current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        return view('admin.dashboard', compact('stats', 'recentActivities', 'currentAcademicYear', 'needsSetup', 'instituteSettings'));
    }

    /**
     * Get recent activities for the dashboard
     */
    private function getRecentActivities()
    {
        $activities = collect();

        // Recent students
        $recentStudents = Student::latest()->take(3)->get();
        foreach ($recentStudents as $student) {
            $activities->push([
                'action' => 'New Student Enrolled',
                'description' => "Student {$student->name} has been enrolled",
                'time' => $student->created_at->diffForHumans(),
                'type' => 'student'
            ]);
        }

        // Recent users
        $recentUsers = User::latest()->take(2)->get();
        foreach ($recentUsers as $user) {
            $activities->push([
                'action' => 'New User Created',
                'description' => "User {$user->name} has been added to the system",
                'time' => $user->created_at->diffForHumans(),
                'type' => 'user'
            ]);
        }

        // Recent exams
        $recentExams = Exam::latest()->take(2)->get();
        foreach ($recentExams as $exam) {
            $activities->push([
                'action' => 'Exam Created',
                'description' => "Exam '{$exam->name}' has been created",
                'time' => $exam->created_at->diffForHumans(),
                'type' => 'exam'
            ]);
        }

        // Sort by creation time and take the most recent 5
        return $activities->sortByDesc(function ($activity) {
            return Carbon::parse($activity['time']);
        })->take(5)->values();
    }

    /**
     * Display role-specific dashboard.
     */
    public function roleBasedDashboard()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return $this->index();
        } elseif ($user->hasRole('principal')) {
            return $this->principalDashboard();
        } elseif ($user->hasRole('teacher')) {
            return $this->teacherDashboard();
        } elseif ($user->hasRole('student')) {
            return $this->studentDashboard();
        }

        return $this->index();
    }

    /**
     * Principal dashboard.
     */
    private function principalDashboard()
    {
        $stats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('status', 'active')->count(),
            'pending_approvals' => Exam::where('result_status', 'submitted')->count(),
            'published_results' => Exam::where('result_status', 'published')->count(),
        ];

        return view('principal.dashboard', compact('stats'));
    }

    /**
     * Teacher dashboard.
     */
    private function teacherDashboard()
    {
        $user = auth()->user();

        // Get teacher's assigned subjects and classes
        $assignedSubjects = $user->teacherSubjects()->with(['subject', 'class'])->get();

        $stats = [
            'assigned_subjects' => $assignedSubjects->count(),
            'assigned_classes' => $assignedSubjects->pluck('class_id')->unique()->count(),
            'pending_marks' => 0, // Will be calculated based on actual exams
            'completed_exams' => 0, // Will be calculated based on actual exams
        ];

        return view('teacher.dashboard', compact('stats', 'assignedSubjects'));
    }

    /**
     * Student dashboard.
     */
    private function studentDashboard()
    {
        // For now, redirect to a simple student view
        // In the future, this will show student's marks, results, etc.
        return view('student.dashboard');
    }
}
