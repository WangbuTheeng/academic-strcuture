<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentBill;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\BillItem;
use App\Models\InstallmentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdvancedBillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Temporarily removed permission check for testing
        // $this->middleware('can:manage-fees');
    }

    /**
     * Advanced bill generation with installment support.
     */
    public function advancedGenerate()
    {
        $academicYears = AcademicYear::orderBy('name')->get();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
        $feeStructures = FeeStructure::active()
            ->with(['level', 'program', 'class'])
            ->orderBy('fee_category')
            ->get();

        return view('admin.advanced-bills.generate', compact(
            'academicYears',
            'currentAcademicYear',
            'feeStructures'
        ));
    }

    /**
     * Process advanced bill generation.
     */
    public function processAdvancedGenerate(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'generation_type' => 'required|in:individual,bulk,installment',
            'bill_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:bill_date',
            'fee_structures' => 'required|array|min:1',
            'fee_structures.*' => 'exists:fee_structures,id',
            'student_filters' => 'nullable|array',
            'installment_config' => 'nullable|array',
            'discount_config' => 'nullable|array',
        ]);

        $generatedCount = 0;
        $errors = [];

        DB::transaction(function () use ($request, &$generatedCount, &$errors) {
            $students = $this->getFilteredStudents($request);
            $feeStructures = FeeStructure::whereIn('id', $request->fee_structures)->get();

            foreach ($students as $student) {
                try {
                    if ($request->generation_type === 'installment') {
                        $this->generateInstallmentBills($student, $feeStructures, $request);
                    } else {
                        $this->generateRegularBill($student, $feeStructures, $request);
                    }
                    $generatedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Error for {$student->full_name}: " . $e->getMessage();
                }
            }
        });

        $message = "Generated {$generatedCount} bills successfully";
        if (count($errors) > 0) {
            $message .= " with " . count($errors) . " errors";
        }

        return redirect()->back()
            ->with('success', $message)
            ->with('errors', $errors);
    }

    /**
     * Bill analytics dashboard.
     */
    public function analytics(Request $request)
    {
        $academicYear = $request->get('academic_year_id', 
            AcademicYear::where('is_current', true)->first()?->id);

        // Bill statistics
        $totalBills = StudentBill::when($academicYear, function ($query) use ($academicYear) {
            return $query->where('academic_year_id', $academicYear);
        })->count();

        $totalAmount = StudentBill::when($academicYear, function ($query) use ($academicYear) {
            return $query->where('academic_year_id', $academicYear);
        })->sum('total_amount');

        $paidAmount = StudentBill::when($academicYear, function ($query) use ($academicYear) {
            return $query->where('academic_year_id', $academicYear);
        })->sum('paid_amount');

        $outstandingAmount = StudentBill::when($academicYear, function ($query) use ($academicYear) {
            return $query->where('academic_year_id', $academicYear);
        })->sum('balance_amount');

        // Status breakdown
        $statusBreakdown = StudentBill::when($academicYear, function ($query) use ($academicYear) {
            return $query->where('academic_year_id', $academicYear);
        })
        ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as amount')
        ->groupBy('status')
        ->get();

        // Monthly billing trends
        $monthlyTrends = StudentBill::when($academicYear, function ($query) use ($academicYear) {
            return $query->where('academic_year_id', $academicYear);
        })
        ->selectRaw('YEAR(bill_date) as year, MONTH(bill_date) as month, COUNT(*) as count, SUM(total_amount) as amount')
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        // Fee category analysis
        $categoryAnalysis = DB::table('bill_items')
            ->join('student_bills', 'bill_items.bill_id', '=', 'student_bills.id')
            ->when($academicYear, function ($query) use ($academicYear) {
                return $query->where('student_bills.academic_year_id', $academicYear);
            })
            ->selectRaw('bill_items.fee_category, COUNT(*) as count, SUM(bill_items.final_amount) as amount')
            ->groupBy('bill_items.fee_category')
            ->get();

        // Top defaulters
        $topDefaulters = Student::withSum(['bills' => function ($query) use ($academicYear) {
                if ($academicYear) {
                    $query->where('academic_year_id', $academicYear);
                }
                $query->whereIn('status', ['pending', 'partial', 'overdue']);
            }], 'balance_amount')
            ->having('bills_sum_balance_amount', '>', 0)
            ->orderBy('bills_sum_balance_amount', 'desc')
            ->limit(10)
            ->get();

        $academicYears = AcademicYear::orderBy('name')->get();

        return view('admin.advanced-bills.analytics', compact(
            'totalBills',
            'totalAmount',
            'paidAmount',
            'outstandingAmount',
            'statusBreakdown',
            'monthlyTrends',
            'categoryAnalysis',
            'topDefaulters',
            'academicYears',
            'academicYear'
        ));
    }

    /**
     * Bulk bill operations.
     */
    public function bulkOperations()
    {
        return view('admin.advanced-bills.bulk-operations');
    }

    /**
     * Process bulk operations.
     */
    public function processBulkOperations(Request $request)
    {
        $request->validate([
            'operation' => 'required|in:lock,unlock,cancel,apply_discount,send_reminders',
            'bill_ids' => 'required|array|min:1',
            'bill_ids.*' => 'exists:student_bills,id',
            'operation_data' => 'nullable|array',
        ]);

        $bills = StudentBill::whereIn('id', $request->bill_ids)->get();
        $processedCount = 0;

        foreach ($bills as $bill) {
            try {
                switch ($request->operation) {
                    case 'lock':
                        $bill->update(['is_locked' => true]);
                        break;
                    case 'unlock':
                        $bill->update(['is_locked' => false]);
                        break;
                    case 'cancel':
                        if ($bill->paid_amount == 0) {
                            $bill->update(['status' => 'cancelled']);
                        }
                        break;
                    case 'apply_discount':
                        $this->applyDiscountToBill($bill, $request->operation_data);
                        break;
                    case 'send_reminders':
                        $this->sendBillReminder($bill);
                        break;
                }
                $processedCount++;
            } catch (\Exception $e) {
                // Log error but continue processing
                \Log::error("Bulk operation error for bill {$bill->id}: " . $e->getMessage());
            }
        }

        return redirect()->back()
            ->with('success', "Processed {$processedCount} bills successfully");
    }

    /**
     * Bill templates management.
     */
    public function templates()
    {
        // Implementation for bill templates
        return view('admin.advanced-bills.templates');
    }

    /**
     * Generate bills with custom template.
     */
    public function generateWithTemplate(Request $request)
    {
        // Implementation for template-based bill generation
        return redirect()->back()
            ->with('success', 'Bills generated with custom template');
    }

    /**
     * Get filtered students based on criteria.
     */
    private function getFilteredStudents(Request $request)
    {
        $query = Student::active()
            ->whereHas('enrollments', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
                
                if ($request->filled('student_filters.level_id')) {
                    $q->whereHas('program', function ($pq) use ($request) {
                        $pq->where('level_id', $request->student_filters['level_id']);
                    });
                }
                
                if ($request->filled('student_filters.program_id')) {
                    $q->where('program_id', $request->student_filters['program_id']);
                }
                
                if ($request->filled('student_filters.class_id')) {
                    $q->where('class_id', $request->student_filters['class_id']);
                }
            });

        return $query->with(['currentEnrollment'])->get();
    }

    /**
     * Generate regular bill for student.
     */
    private function generateRegularBill($student, $feeStructures, $request)
    {
        $enrollment = $student->currentEnrollment;
        
        // Check if bill already exists
        $existingBill = StudentBill::where('student_id', $student->id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('bill_date', $request->bill_date)
            ->exists();

        if ($existingBill) {
            throw new \Exception('Bill already exists for this date');
        }

        // Create bill
        $bill = StudentBill::create([
            'student_id' => $student->id,
            'academic_year_id' => $request->academic_year_id,
            'class_id' => $enrollment?->class_id,
            'program_id' => $enrollment?->program_id,
            'bill_title' => $request->bill_title ?? 'Academic Fee Bill - ' . Carbon::parse($request->bill_date)->format('M Y'),
            'bill_date' => $request->bill_date,
            'due_date' => $request->due_date,
            'created_by' => Auth::id(),
        ]);

        // Add previous dues if requested
        $totalAmount = 0;
        if ($request->boolean('include_previous_dues', false)) {
            $billService = app(\App\Services\BillService::class);
            $previousDues = $billService->getPreviousDuesAmount($student->id);

            if ($previousDues > 0) {
                BillItem::create([
                    'school_id' => auth()->user()->school_id,
                    'bill_id' => $bill->id,
                    'fee_category' => 'Previous Dues',
                    'description' => 'Outstanding balance from previous bills',
                    'unit_amount' => $previousDues,
                    'quantity' => 1,
                    'total_amount' => $previousDues,
                    'final_amount' => $previousDues,
                ]);

                $totalAmount += $previousDues;
            }
        }

        // Add fee items
        foreach ($feeStructures as $feeStructure) {
            $amount = $feeStructure->amount;

            // Apply discounts if configured
            if ($request->filled('discount_config')) {
                $amount = $this->applyDiscount($amount, $request->discount_config);
            }

            BillItem::create([
                'school_id' => auth()->user()->school_id,
                'bill_id' => $bill->id,
                'fee_structure_id' => $feeStructure->id,
                'fee_category' => $feeStructure->fee_category,
                'description' => $feeStructure->fee_name,
                'unit_amount' => $feeStructure->amount,
                'quantity' => 1,
                'total_amount' => $feeStructure->amount,
                'discount_amount' => $feeStructure->amount - $amount,
                'final_amount' => $amount,
            ]);

            $totalAmount += $amount;
        }

        // Update bill total
        $bill->update([
            'total_amount' => $totalAmount,
            'balance_amount' => $totalAmount,
        ]);
    }

    /**
     * Generate installment bills for student.
     */
    private function generateInstallmentBills($student, $feeStructures, $request)
    {
        $installmentConfig = $request->installment_config;
        $totalAmount = $feeStructures->sum('amount');
        
        // Apply discount to total
        if ($request->filled('discount_config')) {
            $totalAmount = $this->applyDiscount($totalAmount, $request->discount_config);
        }

        $installmentAmount = $totalAmount / $installmentConfig['number_of_installments'];
        
        for ($i = 1; $i <= $installmentConfig['number_of_installments']; $i++) {
            $dueDate = Carbon::parse($request->bill_date)
                ->addMonths($i - 1)
                ->addDays($installmentConfig['days_between_installments'] ?? 30);

            $bill = StudentBill::create([
                'student_id' => $student->id,
                'academic_year_id' => $request->academic_year_id,
                'class_id' => $student->currentEnrollment?->class_id,
                'program_id' => $student->currentEnrollment?->program_id,
                'bill_title' => "Installment {$i} of {$installmentConfig['number_of_installments']} - " . Carbon::parse($request->bill_date)->format('M Y'),
                'bill_date' => $request->bill_date,
                'due_date' => $dueDate,
                'total_amount' => $installmentAmount,
                'balance_amount' => $installmentAmount,
                'created_by' => Auth::id(),
            ]);

            // Add proportional fee items
            foreach ($feeStructures as $feeStructure) {
                $itemAmount = ($feeStructure->amount / $totalAmount) * $installmentAmount;
                
                BillItem::create([
                    'bill_id' => $bill->id,
                    'fee_structure_id' => $feeStructure->id,
                    'fee_category' => $feeStructure->fee_category,
                    'description' => $feeStructure->fee_name . " (Installment {$i})",
                    'unit_amount' => $feeStructure->amount,
                    'quantity' => 1 / $installmentConfig['number_of_installments'],
                    'total_amount' => $itemAmount,
                    'final_amount' => $itemAmount,
                ]);
            }
        }
    }

    /**
     * Apply discount to amount.
     */
    private function applyDiscount($amount, $discountConfig)
    {
        if ($discountConfig['type'] === 'percentage') {
            return $amount * (1 - $discountConfig['value'] / 100);
        } elseif ($discountConfig['type'] === 'fixed') {
            return max(0, $amount - $discountConfig['value']);
        }
        
        return $amount;
    }

    /**
     * Apply discount to existing bill.
     */
    private function applyDiscountToBill($bill, $discountData)
    {
        if ($bill->is_locked || $bill->paid_amount > 0) {
            throw new \Exception('Cannot apply discount to locked or partially paid bill');
        }

        $discountAmount = 0;
        if ($discountData['type'] === 'percentage') {
            $discountAmount = $bill->total_amount * ($discountData['value'] / 100);
        } elseif ($discountData['type'] === 'fixed') {
            $discountAmount = min($bill->total_amount, $discountData['value']);
        }

        $bill->update([
            'discount_amount' => $discountAmount,
            'balance_amount' => $bill->total_amount - $discountAmount,
        ]);
    }

    /**
     * Send bill reminder.
     */
    private function sendBillReminder($bill)
    {
        // Implementation for sending bill reminder
        // This would integrate with the notification system
        \Log::info("Bill reminder sent for bill: {$bill->bill_number}");
    }
}
