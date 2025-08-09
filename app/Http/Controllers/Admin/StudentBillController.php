<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentBill;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\BillItem;
use App\Services\BillService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentBillController extends Controller
{
    protected $billService;

    public function __construct(BillService $billService)
    {
        $this->middleware('auth');
        // Temporarily removed permission check for testing
        // $this->middleware('can:manage-fees');
        $this->billService = $billService;
    }

    /**
     * Display a listing of student bills.
     */
    public function index(Request $request)
    {
        // Only show bills for current school
        $query = StudentBill::with(['student', 'academicYear', 'class', 'program'])
                           ->where('school_id', auth()->user()->school_id);

        // Filter by academic year
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('bill_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('bill_date', '<=', $request->date_to);
        }

        // Search by student name or bill number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bill_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('admission_number', 'like', "%{$search}%");
                  });
            });
        }

        $bills = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $academicYears = AcademicYear::orderBy('name')->get();
        $statuses = [
            'pending' => 'Pending',
            'partial' => 'Partially Paid',
            'paid' => 'Paid',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled'
        ];

        // Get summary statistics
        $totalBills = StudentBill::count();
        $totalAmount = StudentBill::sum('total_amount');
        $paidAmount = StudentBill::sum('paid_amount');
        $pendingAmount = StudentBill::sum('balance_amount');

        return view('admin.student-bills.index', compact(
            'bills',
            'academicYears',
            'statuses',
            'totalBills',
            'totalAmount',
            'paidAmount',
            'pendingAmount'
        ));
    }

    /**
     * Show the form for creating a new student bill.
     */
    public function create()
    {
        // Only show students and fee structures from current school
        $students = Student::active()
                          ->where('school_id', auth()->user()->school_id)
                          ->orderBy('first_name')->get();
        $academicYears = AcademicYear::orderBy('name')->get();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Get active fee structures for current school only
        $feeStructures = FeeStructure::active()
            ->where('school_id', auth()->user()->school_id)
            ->with(['academicYear', 'level', 'program', 'class'])
            ->orderBy('fee_category')
            ->orderBy('fee_name')
            ->get();

        return view('admin.student-bills.create', compact(
            'students',
            'academicYears',
            'currentAcademicYear',
            'feeStructures'
        ));
    }

    /**
     * Store a newly created student bill.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'bill_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:bill_date',
            'fee_structure_ids' => 'nullable|array',
            'fee_structure_ids.*' => 'exists:fee_structures,id',
            'custom_fees' => 'nullable|array',
            'custom_fees.*.name' => 'required_with:custom_fees|string|max:200',
            'custom_fees.*.amount' => 'required_with:custom_fees|numeric|min:0.01',
            'custom_fees.*.category' => 'required_with:custom_fees|string|max:100',
        ]);

        // Validate that at least one fee is selected
        if (empty($request->fee_structure_ids) && empty($request->custom_fees)) {
            return back()->withErrors(['fee_items' => 'Please select at least one fee structure or add a custom fee.'])->withInput();
        }

        try {
            // Get student enrollment details
            $student = Student::findOrFail($request->student_id);
            $enrollment = $student->enrollments()
                ->where('academic_year_id', $request->academic_year_id)
                ->first();

            // Prepare bill data
            $billData = [
                'student_id' => $request->student_id,
                'academic_year_id' => $request->academic_year_id,
                'class_id' => $enrollment?->class_id,
                'program_id' => $enrollment?->program_id,
                'description' => $request->description,
                'bill_date' => $request->bill_date,
                'due_date' => $request->due_date,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ];

            // Get fee structures
            $feeStructures = [];
            if (!empty($request->fee_structure_ids)) {
                $feeStructures = FeeStructure::whereIn('id', $request->fee_structure_ids)->get();
            }

            // Get custom fees
            $customFees = $request->custom_fees ?? [];

            // Create bill using service
            $bill = $this->billService->createBill($billData, $feeStructures, $customFees);

            return redirect()->route('admin.student-bills.index')
                            ->with('success', 'Student bill created successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create bill: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified student bill.
     */
    public function show(StudentBill $bill)
    {
        // Ensure bill belongs to current school
        if ($bill->school_id !== auth()->user()->school_id) {
            abort(404);
        }

        $bill->load([
            'student',
            'academicYear',
            'class',
            'program',
            'billItems',
            'payments.verifier',
            'creator'
        ]);

        return view('admin.student-bills.show', compact('bill'));
    }

    /**
     * Show attractive bill preview.
     */
    public function preview(StudentBill $bill)
    {
        $bill->load([
            'student.currentEnrollment.class',
            'student.currentEnrollment.program',
            'academicYear',
            'billItems'
        ]);

        // Get institute settings
        $instituteSettings = \App\Models\InstituteSettings::current() ?? (object) [
            'institution_name' => 'Academic Institution',
            'institution_address' => 'Institution Address',
            'institution_phone' => '+977-1-XXXXXXX',
            'institution_email' => 'info@institution.edu.np',
        ];

        return view('admin.student-bills.bill-preview', [
            'bill' => $bill,
            'instituteSettings' => $instituteSettings
        ]);
    }

    /**
     * Show the form for editing the specified student bill.
     */
    public function edit(StudentBill $bill)
    {
        if ($bill->is_locked) {
            return redirect()->back()
                ->with('error', 'This bill is locked and cannot be edited.');
        }

        // Prevent editing bills that have been fully paid
        if ($bill->status === 'paid' || $bill->paid_amount >= $bill->total_amount) {
            return redirect()->back()
                ->with('error', 'This bill cannot be edited as it has been fully paid.');
        }

        // Prevent editing bills that have any payments
        if ($bill->paid_amount > 0) {
            return redirect()->back()
                ->with('error', 'This bill cannot be edited as it has received payments. Please contact administrator if changes are needed.');
        }

        $bill->load(['billItems']);
        $students = Student::active()->orderBy('first_name')->get();
        $academicYears = AcademicYear::orderBy('name')->get();

        return view('admin.student-bills.edit', compact(
            'bill',
            'students',
            'academicYears'
        ));
    }

    /**
     * Update the specified student bill.
     */
    public function update(Request $request, StudentBill $bill)
    {
        if ($bill->is_locked) {
            return redirect()->back()
                ->with('error', 'This bill is locked and cannot be updated.');
        }

        // Prevent updating bills that have been fully paid
        if ($bill->status === 'paid' || $bill->paid_amount >= $bill->total_amount) {
            return redirect()->back()
                ->with('error', 'This bill cannot be updated as it has been fully paid.');
        }

        // Prevent updating bills that have any payments
        if ($bill->paid_amount > 0) {
            return redirect()->back()
                ->with('error', 'This bill cannot be updated as it has received payments. Please contact administrator if changes are needed.');
        }

        $request->validate([
            'bill_title' => 'required|string|max:200',
            'bill_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:bill_date',
            'fee_items' => 'required|array|min:1',
            'fee_items.*.description' => 'required|string|max:200',
            'fee_items.*.amount' => 'required|numeric|min:0',
            'fee_items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $bill) {
            // Update bill details
            $bill->update([
                'bill_title' => $request->bill_title,
                'description' => $request->description,
                'bill_date' => $request->bill_date,
                'due_date' => $request->due_date,
                'updated_by' => Auth::id(),
            ]);

            // Delete existing bill items
            $bill->billItems()->delete();

            // Create new bill items
            $totalAmount = 0;
            foreach ($request->fee_items as $item) {
                $itemTotal = $item['amount'] * $item['quantity'];
                $totalAmount += $itemTotal;

                BillItem::create([
                    'bill_id' => $bill->id,
                    'fee_structure_id' => $item['fee_structure_id'] ?? null,
                    'fee_category' => $item['fee_category'] ?? 'miscellaneous',
                    'description' => $item['description'],
                    'unit_amount' => $item['amount'],
                    'quantity' => $item['quantity'],
                    'total_amount' => $itemTotal,
                    'final_amount' => $itemTotal,
                ]);
            }

            // Update bill amounts
            $bill->update([
                'total_amount' => $totalAmount,
                'balance_amount' => $totalAmount - $bill->paid_amount,
            ]);

            // Update status
            $bill->updateAmounts();
        });

        return redirect()->route('admin.student-bills.show', $bill)
            ->with('success', 'Student bill updated successfully.');
    }

    /**
     * Remove the specified student bill.
     */
    public function destroy(StudentBill $bill)
    {
        if ($bill->is_locked) {
            return redirect()->back()
                ->with('error', 'This bill cannot be deleted as it is locked.');
        }

        if ($bill->paid_amount > 0) {
            return redirect()->back()
                ->with('error', 'This bill cannot be deleted as it has received payments.');
        }

        if ($bill->status === 'paid') {
            return redirect()->back()
                ->with('error', 'This bill cannot be deleted as it has been fully paid.');
        }

        $bill->delete();

        return redirect()->route('admin.student-bills.index')
            ->with('success', 'Student bill deleted successfully.');
    }

    /**
     * Generate bills for multiple students.
     */
    public function bulkGenerate()
    {
        $academicYears = AcademicYear::orderBy('name')->get();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Get school-specific data
        $levels = \App\Models\Level::orderBy('order')->get();
        $programs = \App\Models\Program::orderBy('name')->get();
        $classes = \App\Models\ClassModel::with('level')->orderBy('name')->get();

        // Only show fee structures from current school
        $feeStructures = FeeStructure::active()
            ->where('school_id', auth()->user()->school_id)
            ->with(['level', 'program', 'class'])
            ->orderBy('fee_category')
            ->get();

        return view('admin.student-bills.bulk-generate', compact(
            'academicYears',
            'currentAcademicYear',
            'levels',
            'programs',
            'classes',
            'feeStructures'
        ));
    }

    /**
     * Process bulk bill generation.
     */
    public function processBulkGenerate(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'level_id' => 'nullable|exists:levels,id',
            'program_id' => 'nullable|exists:programs,id',
            'class_id' => 'nullable|exists:classes,id',
            'fee_structures' => 'required|array|min:1',
            'fee_structures.*' => 'exists:fee_structures,id',
            'bill_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:bill_date',
            'bill_title' => 'nullable|string|max:255',
        ]);

        $generatedCount = 0;
        $generatedBillIds = [];
        $errors = [];

        try {
            DB::transaction(function () use ($request, &$generatedCount, &$generatedBillIds, &$errors) {
            // Get students based on filters (only from current school)
            $studentsQuery = Student::active()
                ->where('school_id', auth()->user()->school_id)
                ->whereHas('enrollments', function ($q) use ($request) {
                    $q->where('academic_year_id', $request->academic_year_id);
                    
                    if ($request->filled('level_id')) {
                        $q->whereHas('program', function ($pq) use ($request) {
                            $pq->where('level_id', $request->level_id);
                        });
                    }
                    
                    if ($request->filled('program_id')) {
                        $q->where('program_id', $request->program_id);
                    }
                    
                    if ($request->filled('class_id')) {
                        $q->where('class_id', $request->class_id);
                    }
                });

            $students = $studentsQuery->with(['currentEnrollment'])->get();
            // Only get fee structures from current school
            $feeStructures = FeeStructure::whereIn('id', $request->fee_structures)
                                        ->where('school_id', auth()->user()->school_id)
                                        ->get();

            foreach ($students as $student) {
                $enrollment = $student->currentEnrollment;
                
                // Check if bill already exists for this period
                $existingBill = StudentBill::where('student_id', $student->id)
                    ->where('academic_year_id', $request->academic_year_id)
                    ->where('bill_date', $request->bill_date)
                    ->exists();

                if ($existingBill) {
                    continue; // Skip if bill already exists
                }

                // Create bill
                $bill = StudentBill::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $request->academic_year_id,
                    'class_id' => $enrollment?->class_id,
                    'program_id' => $enrollment?->program_id,
                    'bill_title' => $request->bill_title ?: 'Academic Fee Bill - ' . Carbon::parse($request->bill_date)->format('M Y'),
                    'bill_date' => $request->bill_date,
                    'due_date' => $request->due_date,
                    'created_by' => Auth::id(),
                ]);

                // Add fee items
                $totalAmount = 0;
                foreach ($feeStructures as $feeStructure) {
                    BillItem::create([
                        'bill_id' => $bill->id,
                        'fee_structure_id' => $feeStructure->id,
                        'fee_category' => $feeStructure->fee_category,
                        'description' => $feeStructure->fee_name,
                        'unit_amount' => $feeStructure->amount,
                        'quantity' => 1,
                        'total_amount' => $feeStructure->amount,
                        'final_amount' => $feeStructure->amount,
                    ]);

                    $totalAmount += $feeStructure->amount;
                }

                // Update bill total
                $bill->update([
                    'total_amount' => $totalAmount,
                    'balance_amount' => $totalAmount,
                ]);

                $generatedBillIds[] = $bill->id;
                $generatedCount++;
            }
            });

        } catch (\Exception $e) {
            \Log::error('Bulk bill generation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to generate bills: ' . $e->getMessage()]);
        }

        // Check if auto-print is requested
        if ($request->has('auto_print') && !empty($generatedBillIds)) {
            $billIdsString = implode(',', $generatedBillIds);
            return redirect()->route('admin.student-bills.print-bulk', ['bill_ids' => $billIdsString])
                ->with('success', "Successfully generated {$generatedCount} bills. Opening print view...");
        }

        return redirect()->route('admin.student-bills.index')
            ->with('success', "Successfully generated {$generatedCount} bills.");
    }

    /**
     * Get fee structures by filters (AJAX).
     */
    public function getFeeStructures(Request $request)
    {
        $query = FeeStructure::active();

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $feeStructures = $query->orderBy('fee_category')->get();

        return response()->json($feeStructures);
    }

    /**
     * Preview students for bulk generation
     */
    public function previewStudents(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'level_id' => 'nullable|exists:levels,id',
            'program_id' => 'nullable|exists:programs,id',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        // Get students based on filters (only from current school)
        $studentsQuery = Student::active()
            ->where('school_id', auth()->user()->school_id)
            ->whereHas('enrollments', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);

                if ($request->filled('level_id')) {
                    $q->whereHas('program', function ($pq) use ($request) {
                        $pq->where('level_id', $request->level_id);
                    });
                }

                if ($request->filled('program_id')) {
                    $q->where('program_id', $request->program_id);
                }

                if ($request->filled('class_id')) {
                    $q->where('class_id', $request->class_id);
                }
            });

        $students = $studentsQuery->with(['currentEnrollment.class'])->get();

        $studentData = $students->map(function ($student) {
            return [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'admission_number' => $student->admission_number,
                'class_name' => $student->currentEnrollment?->class?->name ?? 'N/A'
            ];
        });

        return response()->json([
            'success' => true,
            'students' => $studentData,
            'count' => $students->count()
        ]);
    }

    /**
     * Print bulk bills
     */
    public function printBulkBills(Request $request)
    {
        // Get bill IDs from request, session, or query parameters
        $billIds = $request->input('bill_ids', session('bill_ids', []));

        // For GET requests, try to get from query parameters
        if (empty($billIds) && $request->isMethod('GET')) {
            $billIds = $request->query('bill_ids', []);
            if (is_string($billIds)) {
                $billIds = explode(',', $billIds);
            }
        }

        // Ensure billIds is always an array and filter out empty values
        if (!is_array($billIds)) {
            $billIds = [];
        }
        $billIds = array_filter($billIds, function($id) {
            return !empty($id) && is_numeric($id);
        });

        if (empty($billIds)) {
            // For debugging, show available bills if none selected
            if ($request->has('debug')) {
                $availableBills = StudentBill::where('school_id', auth()->user()->school_id)
                                           ->latest()
                                           ->take(5)
                                           ->get(['id', 'bill_number', 'student_id']);

                return response()->json([
                    'error' => 'No bills selected for printing',
                    'available_bills' => $availableBills,
                    'request_data' => $request->all()
                ]);
            }

            return back()->withErrors(['error' => 'No bills selected for printing.']);
        }

        // Get bills for current school only
        $bills = StudentBill::whereIn('id', $billIds)
                          ->where('school_id', auth()->user()->school_id)
                          ->with(['student', 'academicYear', 'class', 'program', 'items.feeStructure'])
                          ->get();

        if ($bills->isEmpty()) {
            return back()->withErrors(['error' => 'No bills found for printing.']);
        }

        return view('admin.student-bills.bulk-print', compact('bills'));
    }
}
