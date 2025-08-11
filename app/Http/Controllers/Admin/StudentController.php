<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\School;
use App\Models\Level;
use App\Models\ClassModel;
use App\Models\Program;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use App\Imports\StudentsImport;

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
     * Search students for AJAX requests
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['students' => []]);
        }

        $students = Student::with(['currentEnrollment.class', 'currentEnrollment.program'])
            ->where('school_id', auth()->user()->school_id)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('admission_number', 'like', "%{$query}%");
            })
            ->active()
            ->limit(10)
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'full_name' => $student->full_name,
                    'admission_number' => $student->admission_number,
                    'current_class' => $student->currentEnrollment?->class?->name,
                    'current_program' => $student->currentEnrollment?->program?->name,
                ];
            });

        return response()->json(['students' => $students]);
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
        $format = $request->get('format', 'csv');
        $classId = $request->get('class_id');
        $status = $request->get('status');

        // Build query with same filters as index
        $query = Student::with(['currentEnrollment.class.level', 'currentEnrollment.program']);

        // Get school information
        $school = null;
        if (auth()->user()->school_id) {
            $school = School::find(auth()->user()->school_id);
            $query->where('school_id', auth()->user()->school_id);
        }

        // Apply filters
        if ($classId) {
            $query->whereHas('currentEnrollment', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $students = $query->get();

        if ($format === 'pdf') {
            return $this->exportStudentsToPdf($students, $school);
        } else {
            return $this->exportStudentsToCsv($students, $school);
        }
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

    /**
     * Show import form
     */
    public function showImport()
    {
        return view('admin.students.import');
    }

    /**
     * Process Excel import
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            // Get current school
            $school = School::find(auth()->user()->school_id);
            if (!$school) {
                return redirect()->back()->with('error', 'School not found.');
            }

            $file = $request->file('excel_file');
            $extension = $file->getClientOriginalExtension();

            // Process the import
            // By default, skip existing students for easier workflow
            $skipExisting = $request->get('skip_existing', true);
            $import = new StudentsImport($school, $skipExisting);

            if ($extension === 'csv') {
                // Handle CSV files
                $data = $this->processCsvFile($file);
            } else {
                // Handle Excel files (.xlsx, .xls)
                $data = $this->processExcelFile($file);
            }

            $import->processData($data);
            $results = $import->getResults();

            // Prepare detailed success message
            $message = "Import completed! {$results['success_count']} students imported successfully.";

            $hasIssues = false;

            if ($results['skipped_count'] > 0) {
                $message .= " {$results['skipped_count']} existing students were automatically skipped.";
            }

            if ($results['duplicate_count'] > 0) {
                $message .= " {$results['duplicate_count']} duplicate entries were found.";
                $hasIssues = true;
            }

            if ($results['error_count'] > 0) {
                $message .= " {$results['error_count']} rows had validation errors.";
                $hasIssues = true;
            }

            // Store detailed errors in session for display
            if ($hasIssues && !empty($results['errors'])) {
                session()->flash('import_errors', $results['errors']);
                session()->flash('import_summary', [
                    'success_count' => $results['success_count'],
                    'error_count' => $results['error_count'],
                    'duplicate_count' => $results['duplicate_count'],
                    'skipped_count' => $results['skipped_count'],
                    'total_processed' => $results['total_processed']
                ]);
            }

            return redirect()->route('admin.students.index')
                            ->with($hasIssues ? 'warning' : 'success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Process CSV file
     */
    private function processCsvFile($file)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');

        while (($row = fgetcsv($handle)) !== false) {
            $data[] = $row;
        }

        fclose($handle);
        return $data;
    }

    /**
     * Process Excel file using Laravel Excel
     */
    private function processExcelFile($file)
    {
        try {
            // Try using Laravel Excel if available
            if (class_exists('\Maatwebsite\Excel\Excel')) {
                $excel = app('\Maatwebsite\Excel\Excel');
                $data = $excel->load($file->getPathname())->get()->toArray();
                return $data;
            }
        } catch (\Exception $e) {
            // Fall back to basic processing
        }

        // Fallback: Convert Excel to CSV and process
        throw new \Exception('Excel file processing not available. Please use CSV format or install proper Excel support.');
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        // Generate CSV template (more reliable than Excel)
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_master_import_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, [
                'first_name', 'last_name', 'date_of_birth', 'gender', 'phone',
                'email', 'address', 'guardian_name', 'guardian_phone',
                'guardian_relation', 'admission_date', 'nationality', 'status'
            ]);

            // Add sample data demonstrating the workflow
            fputcsv($file, [
                'John', 'Doe', '2010-01-15', 'Male', '9841234567',
                'john.doe@example.com', '123 Main St, Kathmandu', 'Jane Doe', '9841234568',
                'Mother', '2024-04-01', 'Nepali', 'active'
            ]);

            fputcsv($file, [
                'Jane', 'Smith', '2011-03-20', 'Female', '9841234569',
                'jane.smith@example.com', '456 Oak Ave, Lalitpur', 'John Smith', '9841234570',
                'Father', '2024-04-01', 'Nepali', 'active'
            ]);

            fputcsv($file, [
                'Mike', 'Johnson', '2012-05-10', 'Male', '9841234571',
                'mike.johnson@example.com', '789 Pine St, Bhaktapur', 'Sarah Johnson', '9841234572',
                'Mother', '2024-04-01', 'Nepali', 'active'
            ]);

            // Add a comment row (will be skipped during import)
            fputcsv($file, [
                '# Add new students below this line - existing students will be automatically skipped during import'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export students to CSV.
     */
    private function exportStudentsToCsv($students, $school = null)
    {
        $filename = 'students_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($students, $school) {
            $file = fopen('php://output', 'w');

            // Add school header information
            if ($school) {
                fputcsv($file, ['SCHOOL INFORMATION']);
                fputcsv($file, ['School Name:', $school->name]);
                if ($school->address) {
                    fputcsv($file, ['Address:', $school->address]);
                }
                if ($school->phone) {
                    fputcsv($file, ['Phone:', $school->phone]);
                }
                if ($school->email) {
                    fputcsv($file, ['Email:', $school->email]);
                }
                fputcsv($file, ['Report Generated:', date('F j, Y \a\t g:i A')]);
                fputcsv($file, ['Total Students:', count($students)]);
                fputcsv($file, []); // Empty row for spacing
            }

            // Add CSV headers
            fputcsv($file, [
                'Admission Number', 'First Name', 'Last Name', 'Gender', 'Date of Birth',
                'Phone', 'Email', 'Address', 'Guardian Name', 'Guardian Phone',
                'Class', 'Level', 'Program', 'Status', 'Admission Date'
            ]);

            // Add data rows
            foreach ($students as $student) {
                fputcsv($file, [
                    $student->admission_number,
                    $student->first_name,
                    $student->last_name,
                    $student->gender,
                    $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '',
                    $student->phone,
                    $student->email,
                    $student->address,
                    $student->guardian_name,
                    $student->guardian_phone,
                    $student->currentEnrollment?->class?->name ?? 'Not Enrolled',
                    $student->currentEnrollment?->class?->level?->name ?? 'N/A',
                    $student->currentEnrollment?->program?->name ?? 'N/A',
                    $student->status,
                    $student->admission_date ? $student->admission_date->format('Y-m-d') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export students to PDF.
     */
    private function exportStudentsToPdf($students, $school = null)
    {
        // For now, we'll use a simple HTML to PDF approach
        // You can enhance this with a proper PDF library like DomPDF or wkhtmltopdf

        $html = view('admin.students.export-pdf', compact('students', 'school'))->render();

        // Simple PDF generation using browser print
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="students_' . date('Y-m-d_H-i-s') . '.html"');
    }
}
