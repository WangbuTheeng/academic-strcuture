<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentBill;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\BillItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentBillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Temporarily removed permission check for testing
        // $this->middleware('can:manage-fees');
    }

    /**
     * Display a listing of student bills.
     */
    public function index(Request $request)
    {
        $query = StudentBill::with(['student', 'academicYear', 'class', 'program']);

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
        $students = Student::active()->orderBy('first_name')->get();
        $academicYears = AcademicYear::orderBy('name')->get();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Get active fee structures
        $feeStructures = FeeStructure::active()
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

        DB::transaction(function () use ($request) {
            // Get student enrollment details
            $student = Student::findOrFail($request->student_id);
            $enrollment = $student->enrollments()
                ->where('academic_year_id', $request->academic_year_id)
                ->first();

            // Create the bill (bill number will be auto-generated by model)
            $bill = StudentBill::create([
                'student_id' => $request->student_id,
                'academic_year_id' => $request->academic_year_id,
                'class_id' => $enrollment?->class_id,
                'program_id' => $enrollment?->program_id,
                'description' => $request->description,
                'bill_date' => $request->bill_date,
                'due_date' => $request->due_date,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            $totalAmount = 0;

            // Add predefined fee structures
            if (!empty($request->fee_structure_ids)) {
                $feeStructures = FeeStructure::whereIn('id', $request->fee_structure_ids)->get();

                foreach ($feeStructures as $structure) {
                    $totalAmount += $structure->amount;

                    BillItem::create([
                        'bill_id' => $bill->id,
                        'fee_structure_id' => $structure->id,
                        'fee_category' => $structure->fee_category,
                        'description' => $structure->fee_name,
                        'unit_amount' => $structure->amount,
                        'quantity' => 1,
                        'total_amount' => $structure->amount,
                        'final_amount' => $structure->amount,
                    ]);
                }
            }

            // Add custom fees
            if (!empty($request->custom_fees)) {
                foreach ($request->custom_fees as $customFee) {
                    if (!empty($customFee['name']) && !empty($customFee['amount'])) {
                        $amount = (float) $customFee['amount'];
                        $totalAmount += $amount;

                        BillItem::create([
                            'bill_id' => $bill->id,
                            'fee_structure_id' => null,
                            'fee_category' => $customFee['category'],
                            'description' => $customFee['name'],
                            'unit_amount' => $amount,
                            'quantity' => 1,
                            'total_amount' => $amount,
                            'final_amount' => $amount,
                        ]);
                    }
                }
            }

            // Update bill total
            $bill->update([
                'total_amount' => $totalAmount,
                'balance_amount' => $totalAmount,
            ]);
        });

        return redirect()->route('admin.student-bills.index')
            ->with('success', 'Student bill created successfully.');
    }

    /**
     * Display the specified student bill.
     */
    public function show(StudentBill $studentBill)
    {
        $studentBill->load([
            'student',
            'academicYear',
            'class',
            'program',
            'billItems',
            'payments.verifier',
            'creator'
        ]);

        return view('admin.student-bills.show', compact('studentBill'));
    }

    /**
     * Show attractive bill preview.
     */
    public function preview(StudentBill $studentBill)
    {
        $studentBill->load([
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
            'bill' => $studentBill,
            'instituteSettings' => $instituteSettings
        ]);
    }

    /**
     * Show the form for editing the specified student bill.
     */
    public function edit(StudentBill $studentBill)
    {
        if ($studentBill->is_locked) {
            return redirect()->back()
                ->with('error', 'This bill is locked and cannot be edited.');
        }

        $studentBill->load(['billItems']);
        $students = Student::active()->orderBy('first_name')->get();
        $academicYears = AcademicYear::orderBy('name')->get();

        return view('admin.student-bills.edit', compact(
            'studentBill',
            'students',
            'academicYears'
        ));
    }

    /**
     * Update the specified student bill.
     */
    public function update(Request $request, StudentBill $studentBill)
    {
        if ($studentBill->is_locked) {
            return redirect()->back()
                ->with('error', 'This bill is locked and cannot be updated.');
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

        DB::transaction(function () use ($request, $studentBill) {
            // Update bill details
            $studentBill->update([
                'bill_title' => $request->bill_title,
                'description' => $request->description,
                'bill_date' => $request->bill_date,
                'due_date' => $request->due_date,
                'updated_by' => Auth::id(),
            ]);

            // Delete existing bill items
            $studentBill->billItems()->delete();

            // Create new bill items
            $totalAmount = 0;
            foreach ($request->fee_items as $item) {
                $itemTotal = $item['amount'] * $item['quantity'];
                $totalAmount += $itemTotal;

                BillItem::create([
                    'bill_id' => $studentBill->id,
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
            $studentBill->update([
                'total_amount' => $totalAmount,
                'balance_amount' => $totalAmount - $studentBill->paid_amount,
            ]);

            // Update status
            $studentBill->updateAmounts();
        });

        return redirect()->route('admin.student-bills.show', $studentBill)
            ->with('success', 'Student bill updated successfully.');
    }

    /**
     * Remove the specified student bill.
     */
    public function destroy(StudentBill $studentBill)
    {
        if ($studentBill->is_locked || $studentBill->paid_amount > 0) {
            return redirect()->back()
                ->with('error', 'This bill cannot be deleted as it has payments or is locked.');
        }

        $studentBill->delete();

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
        $feeStructures = FeeStructure::active()
            ->with(['level', 'program', 'class'])
            ->orderBy('fee_category')
            ->get();

        return view('admin.student-bills.bulk-generate', compact(
            'academicYears',
            'currentAcademicYear',
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
        ]);

        $generatedCount = 0;

        DB::transaction(function () use ($request, &$generatedCount) {
            // Get students based on filters
            $studentsQuery = Student::active()
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
            $feeStructures = FeeStructure::whereIn('id', $request->fee_structures)->get();

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
                    'bill_title' => 'Academic Fee Bill - ' . Carbon::parse($request->bill_date)->format('M Y'),
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

                $generatedCount++;
            }
        });

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
}
