<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherSubject;
use App\Models\User;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherSubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage-teachers']);
    }

    /**
     * Display teacher-subject assignments.
     */
    public function index(Request $request)
    {
        $query = TeacherSubject::with(['teacher', 'subject', 'class', 'academicYear']);

        // Filter by academic year
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        } else {
            // Default to current academic year
            $currentYear = AcademicYear::where('is_current', true)->first();
            if ($currentYear) {
                $query->where('academic_year_id', $currentYear->id);
            }
        }

        // Filter by teacher
        if ($request->filled('teacher_id')) {
            $query->where('user_id', $request->teacher_id);
        }

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('teacher', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('subject', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $assignments = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $teachers = User::role('teacher')->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $classes = ClassModel::with('level')->where('is_active', true)->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.teacher-subjects.index', compact(
            'assignments', 'teachers', 'subjects', 'classes', 'academicYears'
        ));
    }

    /**
     * Show form to create new assignment.
     */
    public function create()
    {
        $teachers = User::role('teacher')->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $classes = ClassModel::with('level')->where('is_active', true)->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.teacher-subjects.create', compact(
            'teachers', 'subjects', 'classes', 'academicYears'
        ));
    }

    /**
     * Store new assignment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'is_active' => 'boolean',
        ]);

        // Check if teacher has teacher role
        $teacher = User::findOrFail($validated['user_id']);
        if (!$teacher->hasRole('teacher')) {
            return back()->withErrors(['user_id' => 'Selected user must have teacher role.']);
        }

        // Check for duplicate assignment
        $exists = TeacherSubject::where('user_id', $validated['user_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('class_id', $validated['class_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'This teacher is already assigned to this subject and class.']);
        }

        TeacherSubject::create($validated);

        return redirect()->route('admin.teacher-subjects.index')
            ->with('success', 'Teacher assignment created successfully.');
    }

    /**
     * Show assignment details.
     */
    public function show(TeacherSubject $teacherSubject)
    {
        $teacherSubject->load(['teacher', 'subject', 'class.level', 'academicYear']);
        
        return view('admin.teacher-subjects.show', compact('teacherSubject'));
    }

    /**
     * Show form to edit assignment.
     */
    public function edit(TeacherSubject $teacherSubject)
    {
        $teachers = User::role('teacher')->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $classes = ClassModel::with('level')->where('is_active', true)->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.teacher-subjects.edit', compact(
            'teacherSubject', 'teachers', 'subjects', 'classes', 'academicYears'
        ));
    }

    /**
     * Update assignment.
     */
    public function update(Request $request, TeacherSubject $teacherSubject)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'is_active' => 'boolean',
        ]);

        // Check if teacher has teacher role
        $teacher = User::findOrFail($validated['user_id']);
        if (!$teacher->hasRole('teacher')) {
            return back()->withErrors(['user_id' => 'Selected user must have teacher role.']);
        }

        // Check for duplicate assignment (excluding current)
        $exists = TeacherSubject::where('user_id', $validated['user_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('class_id', $validated['class_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('id', '!=', $teacherSubject->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'This teacher is already assigned to this subject and class.']);
        }

        $teacherSubject->update($validated);

        return redirect()->route('admin.teacher-subjects.index')
            ->with('success', 'Teacher assignment updated successfully.');
    }

    /**
     * Delete assignment.
     */
    public function destroy(TeacherSubject $teacherSubject)
    {
        $teacherSubject->delete();

        return redirect()->route('admin.teacher-subjects.index')
            ->with('success', 'Teacher assignment deleted successfully.');
    }

    /**
     * Bulk assign teachers to subjects.
     */
    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'teacher_ids' => 'required|array|min:1',
            'teacher_ids.*' => 'exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $created = 0;
        $skipped = 0;

        foreach ($validated['teacher_ids'] as $teacherId) {
            // Check if teacher has teacher role
            $teacher = User::findOrFail($teacherId);
            if (!$teacher->hasRole('teacher')) {
                $skipped++;
                continue;
            }

            // Check for duplicate
            $exists = TeacherSubject::where('user_id', $teacherId)
                ->where('subject_id', $validated['subject_id'])
                ->where('class_id', $validated['class_id'])
                ->where('academic_year_id', $validated['academic_year_id'])
                ->exists();

            if (!$exists) {
                TeacherSubject::create([
                    'user_id' => $teacherId,
                    'subject_id' => $validated['subject_id'],
                    'class_id' => $validated['class_id'],
                    'academic_year_id' => $validated['academic_year_id'],
                    'is_active' => true,
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        $message = "Bulk assignment completed. Created: {$created}, Skipped: {$skipped}";
        return back()->with('success', $message);
    }

    /**
     * Bulk assign multiple subjects to one teacher.
     */
    public function bulkAssignSubjects(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'is_active' => 'boolean',
        ]);

        // Check if teacher has teacher role
        $teacher = User::findOrFail($validated['user_id']);
        if (!$teacher->hasRole('teacher')) {
            return back()->withErrors(['user_id' => 'Selected user is not a teacher.']);
        }

        $created = 0;
        $skipped = 0;

        foreach ($validated['subject_ids'] as $subjectId) {
            // Check if assignment already exists
            $exists = TeacherSubject::where('user_id', $validated['user_id'])
                ->where('subject_id', $subjectId)
                ->where('class_id', $validated['class_id'])
                ->where('academic_year_id', $validated['academic_year_id'])
                ->exists();

            if (!$exists) {
                TeacherSubject::create([
                    'user_id' => $validated['user_id'],
                    'subject_id' => $subjectId,
                    'class_id' => $validated['class_id'],
                    'academic_year_id' => $validated['academic_year_id'],
                    'is_active' => $validated['is_active'] ?? true,
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        $message = "Bulk subject assignment completed. Created: {$created}, Skipped: {$skipped}";
        return redirect()->route('admin.teacher-subjects.index')->with('success', $message);
    }

    /**
     * Toggle assignment status.
     */
    public function toggleStatus(TeacherSubject $teacherSubject)
    {
        $teacherSubject->update(['is_active' => !$teacherSubject->is_active]);

        $status = $teacherSubject->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Assignment {$status} successfully.");
    }
}
