<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\Student;
use App\Models\StudentBill;
use App\Models\InstituteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EnhancedPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Temporarily removed permission check for testing
        // $this->middleware('can:manage-fees');
    }

    /**
     * Enhanced payment dashboard with analytics.
     */
    public function dashboard(Request $request)
    {
        $dateRange = $request->get('date_range', 'today');
        
        // Calculate date range
        switch ($dateRange) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            default:
                $startDate = Carbon::today();
                $endDate = Carbon::today();
        }

        // Payment statistics
        $totalPayments = Payment::verified()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        $paymentCount = Payment::verified()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->count();

        $averagePayment = $paymentCount > 0 ? $totalPayments / $paymentCount : 0;

        // Payment method breakdown
        $paymentMethods = Payment::verified()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        // Hourly payment distribution (for today)
        $hourlyPayments = [];
        if ($dateRange === 'today') {
            $hourlyPayments = Payment::verified()
                ->whereDate('payment_date', Carbon::today())
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();
        }

        // Recent payments
        $recentPayments = Payment::with(['student', 'bill', 'creator'])
            ->verified()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Pending verifications
        $pendingVerifications = Payment::with(['student', 'bill'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.enhanced-payments.dashboard', compact(
            'totalPayments',
            'paymentCount',
            'averagePayment',
            'paymentMethods',
            'hourlyPayments',
            'recentPayments',
            'pendingVerifications',
            'dateRange'
        ));
    }

    /**
     * Enhanced payment entry with better UX.
     */
    public function enhancedEntry(Request $request)
    {
        $students = Student::active()
            ->with(['currentEnrollment.class', 'currentEnrollment.program'])
            ->orderBy('first_name')
            ->get();

        $paymentMethods = Payment::getPaymentMethods();
        
        // Get selected student's pending bills if provided
        $selectedStudent = null;
        $pendingBills = collect();
        
        if ($request->filled('student_id')) {
            $selectedStudent = Student::with(['pendingBills.billItems'])
                ->findOrFail($request->student_id);
            $pendingBills = $selectedStudent->pendingBills()
                ->orderBy('due_date')
                ->get();
        }

        return view('admin.enhanced-payments.entry', compact(
            'students',
            'paymentMethods',
            'selectedStudent',
            'pendingBills'
        ));
    }

    /**
     * Process enhanced payment with better validation.
     */
    public function processEnhancedPayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_method' => 'required|in:cash,bank_transfer,online,cheque,card,mobile_wallet',
            'payment_date' => 'required|date|before_or_equal:today',
            'total_amount' => 'required|numeric|min:0.01',
            'bill_payments' => 'required|array|min:1',
            'bill_payments.*.bill_id' => 'required|exists:student_bills,id',
            'bill_payments.*.amount' => 'required|numeric|min:0.01',
            'reference_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'cheque_number' => 'nullable|string|max:50',
            'cheque_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        // Validate total amount matches sum of bill payments
        $billPaymentsTotal = collect($request->bill_payments)->sum('amount');
        if (abs($request->total_amount - $billPaymentsTotal) > 0.01) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Total amount must equal the sum of bill payments.');
        }

        DB::transaction(function () use ($request) {
            $student = Student::findOrFail($request->student_id);
            
            // Create individual payments for each bill
            foreach ($request->bill_payments as $billPayment) {
                $bill = StudentBill::findOrFail($billPayment['bill_id']);
                
                // Validate payment amount doesn't exceed bill balance
                if ($billPayment['amount'] > $bill->balance_amount) {
                    throw new \Exception("Payment amount for bill {$bill->bill_number} exceeds balance.");
                }

                // Create payment record
                $payment = Payment::create([
                    'student_id' => $request->student_id,
                    'bill_id' => $bill->id,
                    'amount' => $billPayment['amount'],
                    'payment_date' => $request->payment_date,
                    'payment_method' => $request->payment_method,
                    'reference_number' => $request->reference_number,
                    'bank_name' => $request->bank_name,
                    'cheque_number' => $request->cheque_number,
                    'cheque_date' => $request->cheque_date,
                    'remarks' => $request->remarks,
                    'notes' => $request->notes,
                    'status' => 'verified', // Auto-verify for now
                    'is_verified' => true,
                    'verification_date' => now(),
                    'verified_by' => Auth::id(),
                    'created_by' => Auth::id(),
                    'school_id' => Auth::user()->school_id,
                ]);

                // Create receipt
                PaymentReceipt::create([
                    'payment_id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'receipt_date' => $payment->payment_date,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'issued_by' => Auth::id(),
                    'school_id' => Auth::user()->school_id,
                ]);

                // Update bill amounts
                $bill->updateAmounts();
            }
        });

        return redirect()->route('admin.enhanced-payments.dashboard')
            ->with('success', 'Payment processed successfully for multiple bills.');
    }

    /**
     * Mobile-friendly payment entry.
     */
    public function mobileEntry()
    {
        $paymentMethods = Payment::getPaymentMethods();
        
        return view('admin.enhanced-payments.mobile-entry', compact('paymentMethods'));
    }

    /**
     * Search students for mobile entry (AJAX).
     */
    public function searchStudents(Request $request)
    {
        $search = $request->get('search');
        
        $students = Student::active()
            ->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('admission_number', 'like', "%{$search}%");
            })
            ->with(['currentEnrollment.class', 'pendingBills'])
            ->limit(10)
            ->get();

        return response()->json($students->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->full_name,
                'admission_number' => $student->admission_number,
                'class' => $student->currentEnrollment?->class?->name,
                'total_outstanding' => $student->total_outstanding,
                'formatted_outstanding' => $student->formatted_total_outstanding,
                'pending_bills_count' => $student->pendingBills->count(),
            ];
        }));
    }

    /**
     * Get student payment history (AJAX).
     */
    public function getStudentPaymentHistory(Request $request)
    {
        $student = Student::findOrFail($request->student_id);
        
        $payments = $student->payments()
            ->with(['bill', 'creator'])
            ->verified()
            ->orderBy('payment_date', 'desc')
            ->limit(10)
            ->get();

        return response()->json($payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'payment_number' => $payment->payment_number,
                'amount' => $payment->amount,
                'formatted_amount' => $payment->formatted_amount,
                'payment_date' => $payment->payment_date->format('d/m/Y'),
                'payment_method' => $payment->payment_method_label,
                'bill_number' => $payment->bill->bill_number,
                'reference_number' => $payment->reference_number,
                'created_by' => $payment->creator->name,
            ];
        }));
    }

    /**
     * Process mobile payment entry.
     */
    public function processMobilePayment(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:cash,bank_transfer,online,cheque,card,mobile_wallet',
                'payment_date' => 'required|date|before_or_equal:today',
                'reference_number' => 'nullable|string|max:100',
                'remarks' => 'nullable|string|max:500',
            ]);

            // Get the oldest pending bill for the student
            $bill = StudentBill::where('student_id', $request->student_id)
                ->whereIn('status', ['pending', 'partial', 'overdue'])
                ->orderBy('due_date')
                ->first();

            if (!$bill) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending bills found for this student.'
                ]);
            }

            // Check if payment amount exceeds bill balance
            if ($request->amount > $bill->balance_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds bill balance of Rs. ' . number_format($bill->balance_amount, 2)
                ]);
            }

            DB::transaction(function () use ($request, $bill) {
                // Create payment record
                $payment = Payment::create([
                    'student_id' => $request->student_id,
                    'bill_id' => $bill->id,
                    'amount' => $request->amount,
                    'payment_date' => $request->payment_date,
                    'payment_method' => $request->payment_method,
                    'reference_number' => $request->reference_number,
                    'remarks' => $request->remarks,
                    'status' => 'verified', // Auto-verify for mobile entry
                    'is_verified' => true,
                    'verification_date' => now(),
                    'verified_by' => Auth::id(),
                    'created_by' => Auth::id(),
                    'school_id' => Auth::user()->school_id,
                ]);

                // Create receipt
                PaymentReceipt::create([
                    'payment_id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'receipt_date' => $payment->payment_date,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'issued_by' => Auth::id(),
                    'school_id' => Auth::user()->school_id,
                ]);

                // Update bill amounts
                $bill->updateAmounts();
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ]);
        } catch (\Exception $e) {
            Log::error('Mobile payment processing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing payment: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Bulk payment processing.
     */
    public function bulkPayment()
    {
        $academicYears = \App\Models\AcademicYear::orderBy('name')->get();
        $levels = \App\Models\Level::orderBy('name')->get();
        $paymentMethods = Payment::getPaymentMethods();

        return view('admin.enhanced-payments.bulk-payment', compact(
            'academicYears',
            'levels',
            'paymentMethods'
        ));
    }

    /**
     * Process bulk payment.
     */
    public function processBulkPayment(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'payment_method' => 'required|in:cash,bank_transfer,online,cheque,card,mobile_wallet',
            'payment_date' => 'required|date|before_or_equal:today',
            'amount_per_student' => 'required|numeric|min:0.01',
            'level_id' => 'nullable|exists:levels,id',
            'program_id' => 'nullable|exists:programs,id',
            'class_id' => 'nullable|exists:classes,id',
            'reference_prefix' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:500',
        ]);

        $processedCount = 0;
        $failedCount = 0;
        $errors = [];

        DB::transaction(function () use ($request, &$processedCount, &$failedCount, &$errors) {
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

            $students = $studentsQuery->with(['pendingBills'])->get();

            foreach ($students as $student) {
                try {
                    // Get the oldest pending bill
                    $bill = $student->pendingBills()
                        ->orderBy('due_date')
                        ->first();

                    if (!$bill) {
                        $errors[] = "No pending bills for {$student->full_name}";
                        $failedCount++;
                        continue;
                    }

                    if ($request->amount_per_student > $bill->balance_amount) {
                        $errors[] = "Payment amount exceeds balance for {$student->full_name}";
                        $failedCount++;
                        continue;
                    }

                    // Create payment
                    $payment = Payment::create([
                        'student_id' => $student->id,
                        'bill_id' => $bill->id,
                        'amount' => $request->amount_per_student,
                        'payment_date' => $request->payment_date,
                        'payment_method' => $request->payment_method,
                        'reference_number' => $request->reference_prefix ?
                            $request->reference_prefix . '-' . $student->admission_number : null,
                        'remarks' => $request->remarks,
                        'status' => 'verified',
                        'is_verified' => true,
                        'verification_date' => now(),
                        'verified_by' => Auth::id(),
                        'created_by' => Auth::id(),
                        'school_id' => Auth::user()->school_id,
                    ]);

                    // Create receipt
                    PaymentReceipt::create([
                        'payment_id' => $payment->id,
                        'student_id' => $payment->student_id,
                        'receipt_date' => $payment->payment_date,
                        'amount' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'issued_by' => Auth::id(),
                        'school_id' => Auth::user()->school_id,
                    ]);

                    // Update bill amounts
                    $bill->updateAmounts();

                    $processedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Error processing {$student->full_name}: " . $e->getMessage();
                    $failedCount++;
                }
            }
        });

        $message = "Bulk payment processed: {$processedCount} successful";
        if ($failedCount > 0) {
            $message .= ", {$failedCount} failed";
        }

        return redirect()->back()
            ->with('success', $message)
            ->with('errors', $errors);
    }

    /**
     * Payment analytics API.
     */
    public function analyticsApi(Request $request)
    {
        $period = $request->get('period', 'week');
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        switch ($period) {
            case 'day':
                $data = Payment::verified()
                    ->selectRaw('DATE(payment_date) as date, SUM(amount) as total, COUNT(*) as count')
                    ->whereDate('payment_date', '>=', Carbon::now()->subDays(7))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                break;
            case 'week':
                $data = Payment::verified()
                    ->selectRaw('YEARWEEK(payment_date) as week, SUM(amount) as total, COUNT(*) as count')
                    ->whereDate('payment_date', '>=', Carbon::now()->subWeeks(8))
                    ->groupBy('week')
                    ->orderBy('week')
                    ->get();
                break;
            case 'month':
                $data = Payment::verified()
                    ->selectRaw('YEAR(payment_date) as year, MONTH(payment_date) as month, SUM(amount) as total, COUNT(*) as count')
                    ->whereDate('payment_date', '>=', Carbon::now()->subMonths(12))
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'month')
                    ->get();
                break;
        }

        return response()->json($data);
    }
}
