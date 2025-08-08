<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Level;
use App\Models\ClassModel;
use App\Models\Program;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage-students']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::with(['currentEnrollment.class', 'currentEnrollment.program']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Class filter
        if ($request->filled('class')) {
            $query->whereHas('currentEnrollment', function ($q) use ($request) {
                $q->where('class_id', $request->class);
            });
        }

        $students = $query->paginate(15);
        $classes = ClassModel::all();
        $statuses = ['active', 'inactive', 'graduated', 'transferred', 'dropped'];

        // Ensure classes is always a collection
        if (!$classes) {
            $classes = collect();
        }

        return view('admin.students.index', compact('students', 'classes', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $levels = Level::ordered()->get();
        $classes = ClassModel::with('level')->get();
        $programs = Program::all();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.students.create', compact('levels', 'classes', 'programs', 'academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Student creation attempt', ['request_data' => $request->all()]);

        try {
            $validated = $request->validate([
            // Personal Details
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'gender' => 'required|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:5',
            'religion' => 'nullable|string|max:50',
            'caste' => 'nullable|string|max:50',
            'nationality' => 'required|string|max:50',
            'mother_tongue' => 'nullable|string|max:50',

            // Contact Information
            'phone' => 'required|string|max:15',
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('students')->where(function ($query) {
                    return $query->where('school_id', auth()->user()->school_id);
                })
            ],
            'address' => 'required|string',
            'temporary_address' => 'nullable|string',

            // Emergency Contact
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:15',
            'emergency_contact_relation' => 'nullable|string|max:20',

            // Guardian Information
            'guardian_name' => 'required|string|max:100',
            'guardian_relation' => 'required|string|max:20',
            'guardian_phone' => 'required|string|max:15',
            'guardian_email' => 'nullable|email|max:100',

            // Legal Documentation
            'citizenship_number' => 'nullable|string|max:20',
            'citizenship_issue_date' => 'nullable|date',
            'citizenship_issue_district' => 'nullable|string|max:50',

            // Academic History
            'previous_school_name' => 'nullable|string|max:150',
            'transfer_certificate_no' => 'nullable|string|max:50',
            'transfer_certificate_date' => 'nullable|date',
            'migration_certificate_no' => 'nullable|string|max:50',

            // Special Needs
            'disability_status' => 'required|in:none,visual,hearing,mobility,learning,other',
            'special_needs' => 'nullable|string',

            // Admission Information
            'admission_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('student-photos', 'public');
            $validated['photo'] = $photoPath;
        }

        $student = Student::create($validated);

        \Log::info('Student created successfully', ['student_id' => $student->id, 'admission_number' => $student->admission_number]);

        return redirect()->route('admin.students.index')
                        ->with('success', 'Student registered successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Student creation validation failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Student creation failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create student: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $student->load(['enrollments.class', 'enrollments.program', 'enrollments.academicYear', 'documents', 'marks.exam', 'marks.subject']);

        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $levels = Level::ordered()->get();
        $classes = ClassModel::with('level')->get();
        $programs = Program::all();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.students.edit', compact('student', 'levels', 'classes', 'programs', 'academicYears'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            // Personal Details
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'gender' => 'required|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:5',
            'religion' => 'nullable|string|max:50',
            'caste' => 'nullable|string|max:50',
            'nationality' => 'required|string|max:50',
            'mother_tongue' => 'nullable|string|max:50',

            // Contact Information
            'phone' => 'required|string|max:15',
            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('students')->ignore($student->id)->where(function ($query) {
                    return $query->where('school_id', auth()->user()->school_id);
                })
            ],
            'address' => 'required|string',
            'temporary_address' => 'nullable|string',

            // Emergency Contact
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:15',
            'emergency_contact_relation' => 'nullable|string|max:20',

            // Guardian Information
            'guardian_name' => 'required|string|max:100',
            'guardian_relation' => 'required|string|max:20',
            'guardian_phone' => 'required|string|max:15',
            'guardian_email' => 'nullable|email|max:100',

            // Legal Documentation
            'citizenship_number' => 'nullable|string|max:20',
            'citizenship_issue_date' => 'nullable|date',
            'citizenship_issue_district' => 'nullable|string|max:50',

            // Academic History
            'previous_school_name' => 'nullable|string|max:150',
            'transfer_certificate_no' => 'nullable|string|max:50',
            'transfer_certificate_date' => 'nullable|date',
            'migration_certificate_no' => 'nullable|string|max:50',

            // Special Needs
            'disability_status' => 'required|in:none,visual,hearing,mobility,learning,other',
            'special_needs' => 'nullable|string',

            // Admission Information
            'admission_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,transferred,dropped',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }

            $photoPath = $request->file('photo')->store('student-photos', 'public');
            $validated['photo'] = $photoPath;
        }

        $student->update($validated);

        return redirect()->route('admin.students.index')
                        ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        // Delete photo if exists
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        return redirect()->route('admin.students.index')
                        ->with('success', 'Student deleted successfully.');
    }



    /**
     * Export students data
     */
    public function export(Request $request)
    {
        // This will be implemented later with Excel export functionality
        return redirect()->route('admin.students.index')
                        ->with('info', 'Export functionality will be implemented soon.');
    }

    /**
     * Bulk operations
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'students' => 'required|array',
            'students.*' => 'exists:students,id'
        ]);

        $students = Student::whereIn('id', $validated['students']);

        switch ($validated['action']) {
            case 'delete':
                $students->delete();
                $message = 'Selected students deleted successfully.';
                break;
            case 'activate':
                $students->update(['status' => 'active']);
                $message = 'Selected students activated successfully.';
                break;
            case 'deactivate':
                $students->update(['status' => 'inactive']);
                $message = 'Selected students deactivated successfully.';
                break;
        }

        return redirect()->route('admin.students.index')
                        ->with('success', $message);
    }
}
