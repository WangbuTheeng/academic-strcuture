<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\ClassModel;
use App\Models\Program;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;

class AcademicStructureController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage-academic-structure']);
    }

    /**
     * Display the academic structure dashboard.
     */
    public function index()
    {
        $stats = [
            'total_levels' => Level::count(),
            'total_faculties' => Faculty::count(),
            'total_departments' => Department::count(),
            'total_classes' => ClassModel::count(),
            'total_programs' => Program::count(),
            'total_subjects' => Subject::count(),
            'current_academic_year' => AcademicYear::current()->first(),
        ];

        // Get hierarchical structure for overview
        $levels = Level::with(['classes.department.faculty'])->ordered()->get();
        $faculties = Faculty::withCount(['departments', 'programs'])->get();
        $recentActivities = $this->getRecentActivities();

        return view('admin.academic.index', compact('stats', 'levels', 'faculties', 'recentActivities'));
    }

    /**
     * Get hierarchical structure data for API.
     */
    public function getHierarchy()
    {
        $hierarchy = Level::with([
            'classes.department.faculty',
            'classes.programs'
        ])->ordered()->get();

        return response()->json($hierarchy);
    }

    /**
     * Get academic structure statistics.
     */
    public function getStats()
    {
        $stats = [
            'levels' => Level::count(),
            'faculties' => Faculty::count(),
            'departments' => Department::count(),
            'classes' => ClassModel::count(),
            'programs' => Program::count(),
            'subjects' => Subject::count(),
            'academic_years' => AcademicYear::count(),
        ];

        return response()->json($stats);
    }

    /**
     * Validate academic structure integrity.
     */
    public function validateStructure()
    {
        $issues = [];

        // Check for orphaned departments
        $orphanedDepartments = Department::whereDoesntHave('faculty')->count();
        if ($orphanedDepartments > 0) {
            $issues[] = "Found {$orphanedDepartments} departments without faculty assignment.";
        }

        // Check for orphaned classes
        $orphanedClasses = ClassModel::whereDoesntHave('level')->count();
        if ($orphanedClasses > 0) {
            $issues[] = "Found {$orphanedClasses} classes without level assignment.";
        }

        // Check for orphaned programs
        $orphanedPrograms = Program::whereDoesntHave('department')->count();
        if ($orphanedPrograms > 0) {
            $issues[] = "Found {$orphanedPrograms} programs without department assignment.";
        }

        // Check for orphaned subjects
        $orphanedSubjects = Subject::whereDoesntHave('department')->count();
        if ($orphanedSubjects > 0) {
            $issues[] = "Found {$orphanedSubjects} subjects without department assignment.";
        }

        // Check for current academic year
        $currentAcademicYear = AcademicYear::current()->count();
        if ($currentAcademicYear === 0) {
            $issues[] = "No current academic year is set.";
        } elseif ($currentAcademicYear > 1) {
            $issues[] = "Multiple academic years are marked as current.";
        }

        return response()->json([
            'valid' => empty($issues),
            'issues' => $issues,
            'total_issues' => count($issues)
        ]);
    }

    /**
     * Display academic structure reports.
     */
    public function reports()
    {
        $stats = [
            'total_levels' => Level::count(),
            'total_faculties' => Faculty::count(),
            'total_departments' => Department::count(),
            'total_classes' => ClassModel::count(),
            'total_programs' => Program::count(),
            'total_subjects' => Subject::count(),
            'current_academic_year' => AcademicYear::current()->first(),
        ];

        // Get detailed statistics
        $facultyStats = Faculty::withCount(['departments', 'programs', 'classes'])->get();
        $departmentStats = Department::withCount(['programs', 'classes', 'subjects'])->get();
        $levelStats = Level::withCount(['classes'])->get();

        return view('admin.academic.reports', compact('stats', 'facultyStats', 'departmentStats', 'levelStats'));
    }

    /**
     * Get recent activities for dashboard.
     */
    private function getRecentActivities()
    {
        // This is a placeholder for activity logging
        // In a real implementation, you would have an activity log table
        return collect([
            [
                'action' => 'Faculty Created',
                'description' => 'Faculty of Science created',
                'time' => '2 hours ago',
                'type' => 'faculty'
            ],
            [
                'action' => 'Department Added',
                'description' => 'Computer Department added to Faculty of Science',
                'time' => '4 hours ago',
                'type' => 'department'
            ],
            [
                'action' => 'Subject Created',
                'description' => 'Mathematics subject added',
                'time' => '1 day ago',
                'type' => 'subject'
            ],
            [
                'action' => 'Academic Year Setup',
                'description' => 'Academic Year 2081 configured',
                'time' => '2 days ago',
                'type' => 'academic_year'
            ],
        ]);
    }
}
