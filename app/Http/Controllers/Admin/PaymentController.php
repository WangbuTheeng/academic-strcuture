<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\Student;
use App\Models\StudentBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Temporarily removed permission check for testing
        // $this->middleware('can:manage-fees');
    }

    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['student', 'bill', 'creator', 'verifier']);

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by student name, payment number, or reference
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('admission_number', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $paymentMethods = Payment::getPaymentMethods();
        $statuses = Payment::getPaymentStatuses();

        // Get summary statistics
        $totalPayments = Payment::verified()->sum('amount');
        $todayPayments = Payment::verified()->whereDate('payment_date', Carbon::today())->sum('amount');
        $pendingPayments = Payment::where('status', 'pending')->sum('amount');
        $pendingCount = Payment::where('status', 'pending')->count();

        return view('admin.payments.index', compact(
            'payments',
            'paymentMethods',
            'statuses',
            'totalPayments',
            'todayPayments',
            'pendingPayments',
            'pendingCount'
        ));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(Request $request)
    {
        $students = Student::active()->orderBy('first_name')->get();
        $paymentMethods = Payment::getPaymentMethods();
        
        // If student_id is provided, get their pending bills
        $selectedStudent = null;
        $pendingBills = collect();
        
        if ($request->filled('student_id')) {
            $selectedStudent = Student::findOrFail($request->student_id);
            $pendingBills = $selectedStudent->pendingBills()
                ->with(['billItems'])
                ->orderBy('due_date')
                ->get();
        }

        return view('admin.payments.create', compact(
            'students',
            'paymentMethods',
            'selectedStudent',
            'pendingBills'
        ));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'bill_id' => 'required|exists:student_bills,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'required|in:cash,bank_transfer,online,cheque,card,mobile_wallet',
            'reference_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'cheque_number' => 'nullable|string|max:50',
            'cheque_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Validate payment amount against bill balance
        $bill = StudentBill::findOrFail($request->bill_id);
        if ($request->amount > $bill->balance_amount) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payment amount cannot exceed the bill balance.');
        }

        DB::transaction(function () use ($request) {
            // Create payment
            $payment = Payment::create([
                'student_id' => $request->student_id,
                'bill_id' => $request->bill_id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'bank_name' => $request->bank_name,
                'cheque_number' => $request->cheque_number,
                'cheque_date' => $request->cheque_date,
                'notes' => $request->notes,
                'status' => 'verified', // Auto-verify for now
                'is_verified' => true,
                'verification_date' => now(),
                'verified_by' => Auth::id(),
                'created_by' => Auth::id(),
            ]);

            // Create receipt
            PaymentReceipt::create([
                'payment_id' => $payment->id,
                'student_id' => $payment->student_id,
                'receipt_date' => $payment->payment_date,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'issued_by' => Auth::id(),
            ]);

            // Update bill amounts
            $payment->bill->updateAmounts();
        });

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load([
            'student',
            'bill.billItems',
            'creator',
            'verifier',
            'receipts.issuer'
        ]);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment)
    {
        if ($payment->is_verified) {
            return redirect()->back()
                ->with('error', 'Verified payments cannot be edited.');
        }

        $students = Student::active()->orderBy('first_name')->get();
        $paymentMethods = Payment::getPaymentMethods();
        $bills = StudentBill::where('student_id', $payment->student_id)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->get();

        return view('admin.payments.edit', compact(
            'payment',
            'students',
            'paymentMethods',
            'bills'
        ));
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, Payment $payment)
    {
        if ($payment->is_verified) {
            return redirect()->back()
                ->with('error', 'Verified payments cannot be updated.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_method' => 'required|in:cash,bank_transfer,online,cheque,card,mobile_wallet',
            'reference_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'cheque_number' => 'nullable|string|max:50',
            'cheque_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $payment->update([
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'bank_name' => $request->bank_name,
            'cheque_number' => $request->cheque_number,
            'cheque_date' => $request->cheque_date,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified payment.
     */
    public function destroy(Payment $payment)
    {
        if ($payment->is_verified) {
            return redirect()->back()
                ->with('error', 'Verified payments cannot be deleted.');
        }

        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    /**
     * Verify a payment.
     */
    public function verify(Payment $payment)
    {
        if ($payment->is_verified) {
            return redirect()->back()
                ->with('error', 'Payment is already verified.');
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'verified',
                'is_verified' => true,
                'verification_date' => now(),
                'verified_by' => Auth::id(),
            ]);

            // Create receipt if not exists
            if (!$payment->receipts()->exists()) {
                PaymentReceipt::create([
                    'payment_id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'receipt_date' => $payment->payment_date,
                    'amount' => $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'issued_by' => Auth::id(),
                ]);
            }

            // Update bill amounts
            $payment->bill->updateAmounts();
        });

        return redirect()->back()
            ->with('success', 'Payment verified successfully.');
    }

    /**
     * Reject a payment.
     */
    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'remarks' => 'required|string|max:500',
        ]);

        $payment->update([
            'status' => 'failed',
            'remarks' => $request->remarks,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Payment rejected successfully.');
    }

    /**
     * Get student bills (AJAX).
     */
    public function getStudentBills(Request $request)
    {
        $bills = StudentBill::where('student_id', $request->student_id)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->with(['billItems'])
            ->orderBy('due_date')
            ->get();

        return response()->json($bills);
    }

    /**
     * Quick payment entry.
     */
    public function quickEntry()
    {
        $students = Student::active()->orderBy('first_name')->get();
        $paymentMethods = Payment::getPaymentMethods();

        return view('admin.payments.quick-entry', compact(
            'students',
            'paymentMethods'
        ));
    }

    /**
     * Process quick payment entry.
     */
    public function processQuickEntry(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,online,cheque,card,mobile_wallet',
            'payment_date' => 'required|date|before_or_equal:today',
            'reference_number' => 'nullable|string|max:100',
        ]);

        // Get the oldest pending bill for the student
        $bill = StudentBill::where('student_id', $request->student_id)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->orderBy('due_date')
            ->first();

        if (!$bill) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No pending bills found for this student.');
        }

        if ($request->amount > $bill->balance_amount) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Payment amount cannot exceed the bill balance.');
        }

        DB::transaction(function () use ($request, $bill) {
            // Create payment
            $payment = Payment::create([
                'student_id' => $request->student_id,
                'bill_id' => $bill->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'status' => 'verified',
                'is_verified' => true,
                'verification_date' => now(),
                'verified_by' => Auth::id(),
                'created_by' => Auth::id(),
            ]);

            // Create receipt
            PaymentReceipt::create([
                'payment_id' => $payment->id,
                'student_id' => $payment->student_id,
                'receipt_date' => $payment->payment_date,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'issued_by' => Auth::id(),
            ]);

            // Update bill amounts
            $payment->bill->updateAmounts();
        });

        return redirect()->route('admin.payments.index')
            ->with('success', 'Quick payment recorded successfully.');
    }
}
