<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentReceipt;
use App\Models\Payment;
use App\Models\InstituteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Temporarily removed permission check for testing
        // $this->middleware('can:generate-receipts');
    }

    /**
     * Display a listing of receipts.
     */
    public function index(Request $request)
    {
        $query = PaymentReceipt::with(['payment.student', 'payment.bill', 'issuer']);

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('receipt_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('receipt_date', '<=', $request->date_to);
        }

        // Filter by receipt type
        if ($request->filled('type')) {
            if ($request->type === 'original') {
                $query->where('is_duplicate', false)->where('is_cancelled', false);
            } elseif ($request->type === 'duplicate') {
                $query->where('is_duplicate', true);
            } elseif ($request->type === 'cancelled') {
                $query->where('is_cancelled', true);
            }
        }

        // Search by receipt number or student name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhereHas('payment.student', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('admission_number', 'like', "%{$search}%");
                  });
            });
        }

        $receipts = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get summary statistics
        $totalReceipts = PaymentReceipt::active()->count();
        $totalAmount = PaymentReceipt::active()->sum('amount');
        $duplicateCount = PaymentReceipt::duplicate()->count();
        $cancelledCount = PaymentReceipt::cancelled()->count();

        return view('admin.receipts.index', compact(
            'receipts',
            'totalReceipts',
            'totalAmount',
            'duplicateCount',
            'cancelledCount'
        ));
    }

    /**
     * Display the specified receipt.
     */
    public function show(PaymentReceipt $receipt)
    {
        $receipt->load([
            'payment.student',
            'payment.bill.billItems',
            'issuer',
            'canceller'
        ]);

        return view('admin.receipts.show', compact('receipt'));
    }

    /**
     * Generate and download receipt PDF.
     */
    public function downloadPdf(PaymentReceipt $receipt)
    {
        $receipt->load([
            'payment.student',
            'payment.bill.billItems',
            'issuer'
        ]);

        $instituteSettings = InstituteSettings::first();

        $pdf = Pdf::loadView('admin.receipts.pdf', compact('receipt', 'instituteSettings'));
        
        $filename = 'receipt-' . $receipt->receipt_number . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Print receipt (view for printing).
     */
    public function print(PaymentReceipt $receipt)
    {
        $receipt->load([
            'payment.student',
            'payment.bill.billItems',
            'issuer'
        ]);

        $instituteSettings = InstituteSettings::first();

        return view('admin.receipts.print', compact('receipt', 'instituteSettings'));
    }

    /**
     * Generate duplicate receipt.
     */
    public function generateDuplicate(PaymentReceipt $originalReceipt)
    {
        if ($originalReceipt->is_cancelled) {
            return redirect()->back()
                ->with('error', 'Cannot generate duplicate of a cancelled receipt.');
        }

        $duplicateReceipt = PaymentReceipt::create([
            'payment_id' => $originalReceipt->payment_id,
            'student_id' => $originalReceipt->student_id,
            'receipt_date' => $originalReceipt->receipt_date,
            'amount' => $originalReceipt->amount,
            'payment_method' => $originalReceipt->payment_method,
            'is_duplicate' => true,
            'remarks' => 'Duplicate of receipt: ' . $originalReceipt->receipt_number,
            'receipt_data' => $originalReceipt->receipt_data,
            'issued_by' => Auth::id(),
        ]);

        return redirect()->route('admin.receipts.show', $duplicateReceipt)
            ->with('success', 'Duplicate receipt generated successfully.');
    }

    /**
     * Cancel a receipt.
     */
    public function cancel(Request $request, PaymentReceipt $receipt)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        if ($receipt->is_cancelled) {
            return redirect()->back()
                ->with('error', 'Receipt is already cancelled.');
        }

        $receipt->update([
            'is_cancelled' => true,
            'cancelled_date' => now(),
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Receipt cancelled successfully.');
    }

    /**
     * Bulk generate receipts for payments.
     */
    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'payment_ids' => 'required|array|min:1',
            'payment_ids.*' => 'exists:payments,id',
        ]);

        $generatedCount = 0;
        $payments = Payment::whereIn('id', $request->payment_ids)
            ->where('is_verified', true)
            ->whereDoesntHave('receipts')
            ->get();

        foreach ($payments as $payment) {
            PaymentReceipt::create([
                'payment_id' => $payment->id,
                'student_id' => $payment->student_id,
                'receipt_date' => $payment->payment_date,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'issued_by' => Auth::id(),
            ]);

            $generatedCount++;
        }

        return redirect()->back()
            ->with('success', "Successfully generated {$generatedCount} receipts.");
    }

    /**
     * Get receipt template for customization.
     */
    public function template()
    {
        $instituteSettings = InstituteSettings::first();
        
        // Sample receipt data for template preview
        $sampleReceipt = (object) [
            'receipt_number' => 'REC-2025-001',
            'receipt_date' => now(),
            'amount' => 5000.00,
            'payment_method' => 'cash',
            'payment' => (object) [
                'payment_number' => 'PAY-2025-001',
                'payment_date' => now(),
                'reference_number' => 'REF123456',
                'student' => (object) [
                    'full_name' => 'John Doe',
                    'admission_number' => 'ADM-2025-001',
                    'class' => (object) ['name' => 'Grade 10'],
                    'program' => (object) ['name' => 'Science Program'],
                ],
                'bill' => (object) [
                    'bill_number' => 'BILL-2025-001',
                    'bill_title' => 'Academic Fee Bill - Jan 2025',
                    'billItems' => collect([
                        (object) [
                            'description' => 'Tuition Fee',
                            'final_amount' => 3000.00,
                        ],
                        (object) [
                            'description' => 'Laboratory Fee',
                            'final_amount' => 1500.00,
                        ],
                        (object) [
                            'description' => 'Library Fee',
                            'final_amount' => 500.00,
                        ],
                    ])
                ]
            ],
            'issuer' => (object) [
                'name' => 'Admin User',
            ]
        ];

        return view('admin.receipts.template', compact('instituteSettings', 'sampleReceipt'));
    }

    /**
     * Update receipt template settings.
     */
    public function updateTemplate(Request $request)
    {
        $request->validate([
            'receipt_header' => 'nullable|string',
            'receipt_footer' => 'nullable|string',
            'show_logo' => 'boolean',
            'show_signature' => 'boolean',
            'signature_text' => 'nullable|string',
        ]);

        $settings = InstituteSettings::first();
        $receiptSettings = $settings->receipt_settings ?? [];

        $receiptSettings = array_merge($receiptSettings, [
            'header' => $request->receipt_header,
            'footer' => $request->receipt_footer,
            'show_logo' => $request->boolean('show_logo'),
            'show_signature' => $request->boolean('show_signature'),
            'signature_text' => $request->signature_text,
        ]);

        $settings->update(['receipt_settings' => $receiptSettings]);

        return redirect()->back()
            ->with('success', 'Receipt template updated successfully.');
    }
}
