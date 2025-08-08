<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Program;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use App\Models\Level;
use App\Services\AutoSubjectEnrollmentService;
use Illuminate\Http\Request;

class StudentEnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StudentEnrollment::with(['student', 'program', 'class', 'academicYear'])
            ->withValidStudents(); // Only include enrollments with valid students

        // Search functionality
        if ($request->filled('search')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('admission_number', 'like', "%{$request->search}%");
            });
        }

        // Program filter
        if ($request->filled('program')) {
            $query->where('program_id', $request->program);
        }

        // Class filter
        if ($request->filled('class')) {
            $query->where('class_id', $request->class);
        }

        // Academic Year filter
        if ($request->filled('academic_year')) {
            $query->where('academic_year_id', $request->academic_year);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->latest()->paginate(15);

        // Get filter options
        $programs = Program::with('department')->get();
        $classes = ClassModel::with('level')->get();
        $academicYears = AcademicYear::all();

        return view('admin.academic.enrollments.index', compact(
            'enrollments', 'programs', 'classes', 'academicYears'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get students for current school only (BelongsToSchool trait handles this automatically)
        $students = Student::where('status', 'active')->get();

        // Only show programs that have classes assigned for current school
        $programs = Program::with(['department', 'level', 'classes'])
                          ->whereHas('classes', function($query) {
                              $query->where('is_active', true);
                          })
                          ->get();

        // Get classes for current school (BelongsToSchool trait handles this automatically)
        $classes = ClassModel::with('level')->where('is_active', true)->get();

        // Get academic years for current school (BelongsToSchool trait handles this automatically)
        $academicYears = AcademicYear::all();

        // Get levels for current school (BelongsToSchool trait handles this automatically)
        $levels = Level::all();

        return view('admin.academic.enrollments.create', compact(
            'students', 'programs', 'classes', 'academicYears', 'levels'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'program_id' => 'required|exists:programs,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,transferred,dropped',
            'roll_number' => 'nullable|string|max:20',
            'section' => 'nullable|string|max:10',
        ]);

        // Generate roll number if not provided
        if (empty($validated['roll_number'])) {
            $validated['roll_number'] = $this->generateRollNumber($validated['class_id'], $validated['academic_year_id']);
        }

        // Check for duplicate roll number in the same class and academic year
        $existingEnrollment = StudentEnrollment::where('class_id', $validated['class_id'])
                                              ->where('academic_year_id', $validated['academic_year_id'])
                                              ->where('roll_no', $validated['roll_number'])
                                              ->first();

        if ($existingEnrollment) {
            return back()->withErrors([
                'roll_number' => 'This roll number is already assigned to another student in the same class and academic year.'
            ])->withInput();
        }

        // Map roll_number to roll_no for database
        $validated['roll_no'] = $validated['roll_number'];
        unset($validated['roll_number']);

        // Check for duplicate enrollment
        $existingEnrollment = StudentEnrollment::where('student_id', $validated['student_id'])
            ->where('program_id', $validated['program_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->first();

        if ($existingEnrollment) {
            return back()->withErrors(['student_id' => 'Student is already enrolled in this program for the selected academic year.']);
        }

        $enrollment = StudentEnrollment::create($validated);

        // Automatically enroll student in compulsory subjects
        $autoEnrollmentService = new AutoSubjectEnrollmentService();
        $result = $autoEnrollmentService->autoEnrollSubjects($enrollment, [
            'compulsory_only' => true,  // Only enroll compulsory subjects automatically
            'program_subjects' => true,
            'class_subjects' => true,
        ]);

        $message = 'Student enrolled successfully.';
        if ($result['success'] && $result['summary']['enrolled_count'] > 0) {
            $message .= ' ' . $result['summary']['enrolled_count'] . ' compulsory subjects automatically assigned.';
        }

        return redirect()->route('admin.enrollments.index')
                        ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentEnrollment $enrollment)
    {
        $enrollment->load(['student', 'program.department', 'class.level', 'academicYear', 'studentSubjects.subject']);

        // Check if student exists
        if (!$enrollment->student) {
            return redirect()->route('admin.enrollments.index')
                ->with('error', 'This enrollment has no associated student record.');
        }

        return view('admin.academic.enrollments.show', compact('enrollment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentEnrollment $enrollment)
    {
        // Get students for current school only (BelongsToSchool trait handles this automatically)
        $students = Student::where('status', 'active')->get();

        // Only show programs that have classes assigned for current school
        $programs = Program::with(['department', 'level', 'classes'])
                          ->whereHas('classes', function($query) {
                              $query->where('is_active', true);
                          })
                          ->get();

        // Get classes for current school (BelongsToSchool trait handles this automatically)
        $classes = ClassModel::with('level')->where('is_active', true)->get();

        // Get academic years for current school (BelongsToSchool trait handles this automatically)
        $academicYears = AcademicYear::all();

        return view('admin.academic.enrollments.edit', compact(
            'enrollment', 'students', 'programs', 'classes', 'academicYears'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentEnrollment $enrollment)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'program_id' => 'required|exists:programs,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,transferred,dropped',
            'roll_number' => 'nullable|string|max:20',
            'section' => 'nullable|string|max:10',
        ]);

        // Generate roll number if not provided
        if (empty($validated['roll_number'])) {
            $validated['roll_number'] = $this->generateRollNumber($validated['class_id'], $validated['academic_year_id'], $enrollment->id);
        }

        // Check for duplicate roll number in the same class and academic year (excluding current)
        if (!empty($validated['roll_number'])) {
            $existingRollEnrollment = StudentEnrollment::where('class_id', $validated['class_id'])
                                                      ->where('academic_year_id', $validated['academic_year_id'])
                                                      ->where('roll_no', $validated['roll_number'])
                                                      ->where('id', '!=', $enrollment->id)
                                                      ->first();

            if ($existingRollEnrollment) {
                return back()->withErrors([
                    'roll_number' => 'This roll number is already assigned to another student in the same class and academic year.'
                ])->withInput();
            }
        }

        // Map roll_number to roll_no for database
        $validated['roll_no'] = $validated['roll_number'];
        unset($validated['roll_number']);

        // Check for duplicate enrollment (excluding current)
        $existingEnrollment = StudentEnrollment::where('student_id', $validated['student_id'])
            ->where('program_id', $validated['program_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('id', '!=', $enrollment->id)
            ->first();

        if ($existingEnrollment) {
            return back()->withErrors(['student_id' => 'Student is already enrolled in this program for the selected academic year.']);
        }

        $enrollment->update($validated);

        return redirect()->route('admin.enrollments.index')
                        ->with('success', 'Enrollment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentEnrollment $enrollment)
    {
        // Check if enrollment has related data
        if ($enrollment->studentSubjects()->count() > 0) {
            return back()->with('error', 'Cannot delete enrollment. Student has subject enrollments.');
        }

        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')
                        ->with('success', 'Enrollment deleted successfully.');
    }

    /**
     * Get classes by program (AJAX endpoint).
     */
    public function getClassesByProgram(Request $request)
    {
        try {
            // Log the incoming request
            \Log::info('getClassesByProgram called', [
                'program_id' => $request->program_id,
                'user_id' => auth()->id(),
                'school_id' => auth()->user()->school_id ?? null
            ]);

            // Validate the request
            $request->validate([
                'program_id' => 'required|integer|exists:programs,id'
            ]);

            // Find the program (BelongsToSchool trait ensures it belongs to current school)
            $program = Program::find($request->program_id);

            if (!$program) {
                \Log::warning('Program not found', ['program_id' => $request->program_id]);
                return response()->json([]);
            }

            \Log::info('Program found', [
                'program_id' => $program->id,
                'program_name' => $program->name,
                'program_school_id' => $program->school_id ?? null
            ]);

            // Get classes associated with this program through the program_classes pivot table
            // The BelongsToSchool trait on ClassModel will automatically filter by school
            $classes = $program->classes()
                              ->where('is_active', true)
                              ->with('level')
                              ->get();

            \Log::info('Classes found', [
                'program_id' => $program->id,
                'classes_count' => $classes->count(),
                'classes' => $classes->pluck('name', 'id')->toArray()
            ]);

            // Format the response for the frontend
            $formattedClasses = $classes->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'level_name' => $class->level ? $class->level->name : '',
                    'display_name' => $class->level ? "{$class->name} ({$class->level->name})" : $class->name
                ];
            });

            \Log::info('Returning formatted classes', [
                'formatted_classes_count' => $formattedClasses->count()
            ]);

            return response()->json($formattedClasses);

        } catch (\Exception $e) {
            \Log::error('Error in getClassesByProgram', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Failed to load classes'], 500);
        }
    }

    /**
     * Bulk enrollment action.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,graduate,transfer,delete',
            'enrollments' => 'required|string',
        ]);

        $enrollmentIds = explode(',', $request->enrollments);
        $enrollments = StudentEnrollment::whereIn('id', $enrollmentIds);

        switch ($request->action) {
            case 'activate':
                $enrollments->update(['status' => 'active']);
                $message = 'Selected enrollments activated successfully.';
                break;
            case 'deactivate':
                $enrollments->update(['status' => 'inactive']);
                $message = 'Selected enrollments deactivated successfully.';
                break;
            case 'graduate':
                $enrollments->update(['status' => 'graduated']);
                $message = 'Selected students graduated successfully.';
                break;
            case 'transfer':
                $enrollments->update(['status' => 'transferred']);
                $message = 'Selected students marked as transferred.';
                break;
            case 'delete':
                // Check for related data before deletion
                $enrollmentsWithSubjects = StudentEnrollment::whereIn('id', $enrollmentIds)
                    ->whereHas('studentSubjects')
                    ->count();

                if ($enrollmentsWithSubjects > 0) {
                    return back()->with('error', 'Some enrollments cannot be deleted as they have subject enrollments.');
                }

                $enrollments->delete();
                $message = 'Selected enrollments deleted successfully.';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Generate a unique roll number for the class and academic year.
     * Format: [ClassCode][Year][Sequential Number]
     * Example: CS25001, CS25002, CS25003 (Sequential enrollment order)
     */
    private function generateRollNumber($classId, $academicYearId, $excludeId = null)
    {
        // Get the class and academic year for generating roll number
        $class = ClassModel::find($classId);
        $academicYear = AcademicYear::find($academicYearId);

        if (!$class || !$academicYear) {
            return 'AUTO' . time(); // Fallback
        }

        // Get the highest existing roll number for this class and academic year
        $query = StudentEnrollment::where('class_id', $classId)
                                  ->where('academic_year_id', $academicYearId);

        // Exclude current enrollment when editing
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // Generate roll number pattern: ClassCode + Year + Sequential Number
        $yearSuffix = substr($academicYear->name, -2); // Last 2 digits of year
        $basePattern = $class->code . $yearSuffix;

        // Get the highest number for this pattern (more efficient than checking all)
        $lastRollNumber = $query->where('roll_no', 'like', $basePattern . '%')
                               ->orderBy('roll_no', 'desc')
                               ->value('roll_no');

        if ($lastRollNumber) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastRollNumber, strlen($basePattern));
            $nextNumber = $lastNumber + 1;
        } else {
            // First student in this class/year
            $nextNumber = 1;
        }

        // Format: ClassCode + Year + 3-digit sequential number
        return $basePattern . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Show bulk enrollment form.
     */
    public function bulkCreate()
    {
        // Get students who are not enrolled in the current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        if (!$currentAcademicYear) {
            return redirect()->route('admin.enrollments.index')
                           ->with('error', 'No current academic year found. Please set a current academic year first.');
        }

        // Get students not enrolled in current academic year
        $enrolledStudentIds = StudentEnrollment::where('academic_year_id', $currentAcademicYear->id)
                                              ->pluck('student_id')
                                              ->toArray();

        $students = Student::whereNotIn('id', $enrolledStudentIds)
                          ->where('status', 'active')
                          ->orderBy('first_name')
                          ->get();

        // Get programs, classes, and levels
        $programs = Program::where('is_active', true)->with('department')->get();
        $classes = ClassModel::with('level')->where('is_active', true)->get();
        $levels = Level::all();

        return view('admin.academic.enrollments.bulk-create', compact(
            'students', 'programs', 'classes', 'levels', 'currentAcademicYear'
        ));
    }

    /**
     * Store bulk enrollments.
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'program_id' => 'required|exists:programs,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'enrollment_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,transferred,dropped',
            'section' => 'nullable|string|max:10',
            'auto_assign_subjects' => 'boolean',
        ]);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $autoEnrollmentService = new AutoSubjectEnrollmentService();

        foreach ($validated['student_ids'] as $studentId) {
            try {
                // Check if student is already enrolled
                $existingEnrollment = StudentEnrollment::where('student_id', $studentId)
                    ->where('program_id', $validated['program_id'])
                    ->where('academic_year_id', $validated['academic_year_id'])
                    ->first();

                if ($existingEnrollment) {
                    $student = Student::find($studentId);
                    $errors[] = "Student {$student->full_name} is already enrolled in this program.";
                    $errorCount++;
                    continue;
                }

                // Generate roll number
                $rollNumber = $this->generateRollNumber($validated['class_id'], $validated['academic_year_id']);

                // Create enrollment
                $enrollment = StudentEnrollment::create([
                    'student_id' => $studentId,
                    'program_id' => $validated['program_id'],
                    'class_id' => $validated['class_id'],
                    'academic_year_id' => $validated['academic_year_id'],
                    'enrollment_date' => $validated['enrollment_date'],
                    'status' => $validated['status'],
                    'section' => $validated['section'],
                    'roll_no' => $rollNumber,
                ]);

                // Auto-assign subjects if requested
                if ($request->boolean('auto_assign_subjects', true)) {
                    $autoEnrollmentService->autoEnrollSubjects($enrollment, [
                        'compulsory_only' => true,
                        'program_subjects' => true,
                        'class_subjects' => true,
                    ]);
                }

                $successCount++;

            } catch (\Exception $e) {
                $student = Student::find($studentId);
                $errors[] = "Failed to enroll {$student->full_name}: " . $e->getMessage();
                $errorCount++;
            }
        }

        $message = "Bulk enrollment completed. {$successCount} students enrolled successfully.";
        if ($errorCount > 0) {
            $message .= " {$errorCount} students failed to enroll.";
        }

        $alertType = $errorCount > 0 ? 'warning' : 'success';

        return redirect()->route('admin.enrollments.index')
                        ->with($alertType, $message)
                        ->with('bulk_errors', $errors);
    }

    /**
     * Get the next available roll number for a class and academic year (API endpoint).
     */
    public function getNextRollNumber(Request $request)
    {
        $classId = $request->get('class_id');
        $academicYearId = $request->get('academic_year_id');
        $excludeId = $request->get('exclude_id'); // For editing existing enrollment

        if (!$classId || !$academicYearId) {
            return response()->json(['roll_number' => '']);
        }

        $rollNumber = $this->generateRollNumber($classId, $academicYearId, $excludeId);

        return response()->json(['roll_number' => $rollNumber]);
    }
}
