@extends('layouts.admin')

@section('title', 'Receipt Details')

@section('content')
<div class="container-fluid">
    <!-- Success Alert for New Payments -->
    @if(session('success') && str_contains(session('success'), 'Payment recorded'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle fa-2x text-success me-3"></i>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-1">Payment Successful!</h5>
                <p class="mb-2">{{ session('success') }}</p>
                <div class="btn-group">
                    <a href="{{ route('admin.fees.receipts.print', $receipt) }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-print me-1"></i>Print Receipt
                    </a>
                    <a href="{{ route('admin.fees.receipts.download', $receipt) }}" class="btn btn-primary btn-sm" target="_blank">
                        <i class="fas fa-download me-1"></i>Download PDF
                    </a>
                    <a href="{{ route('admin.payments.create') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Another Payment
                    </a>
                </div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-receipt text-primary me-2"></i>Receipt #{{ $receipt->receipt_number }}
            </h1>
            <p class="text-muted mb-0">Payment receipt details and download options</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.fees.receipts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Receipts
            </a>
            <a href="{{ route('admin.fees.receipts.print', $receipt) }}" class="btn btn-success" target="_blank">
                <i class="fas fa-print me-2"></i>Print Receipt
            </a>
            <a href="{{ route('admin.fees.receipts.download', $receipt) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-download me-2"></i>Download PDF
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Receipt Preview -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Receipt Preview</h6>
                </div>
                <div class="card-body">
                    <!-- Receipt Content -->
                    <div class="receipt-preview p-4 border">
                        <!-- School Header -->
                        <div class="receipt-header mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; margin: -16px -16px 20px -16px; border-radius: 8px 8px 0 0;">
                            @php
                                $instituteSettings = \App\Models\InstituteSettings::current() ?? (object) [
                                    'institution_name' => 'Academic Institution',
                                    'institution_address' => 'Institution Address',
                                    'institution_phone' => '+977-1-XXXXXXX',
                                    'institution_email' => 'info@institution.edu.np',
                                ];
                            @endphp
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <!-- Logo Section -->
                                <div style="width: 70px; height: 70px; flex-shrink: 0;">
                                    @if(isset($instituteSettings->institution_logo) && $instituteSettings->institution_logo)
                                        <img src="{{ asset('storage/' . $instituteSettings->institution_logo) }}" alt="Institution Logo"
                                             style="width: 100%; height: 100%; object-fit: contain; border-radius: 10px; border: 3px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.1); padding: 2px;"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div style="width: 100%; height: 100%; background: rgba(255,255,255,0.2); color: white; display: none; align-items: center; justify-content: center; font-size: 22px; font-weight: bold; border-radius: 10px; border: 3px solid rgba(255,255,255,0.4);">
                                            {{ substr($instituteSettings->institution_name ?? 'AMS', 0, 3) }}
                                        </div>
                                    @else
                                        <div style="width: 100%; height: 100%; background: rgba(255,255,255,0.2); color: white; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: bold; border-radius: 10px; border: 3px solid rgba(255,255,255,0.4);">
                                            {{ substr($instituteSettings->institution_name ?? 'AMS', 0, 3) }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Institution Details -->
                                <div class="school-info text-center" style="flex: 1;">
                                    <h2 style="margin: 0; font-size: 2rem; font-weight: bold;">{{ $instituteSettings->institution_name }}</h2>
                                    <p style="margin: 5px 0; opacity: 0.9;">{{ $instituteSettings->institution_address }}</p>
                                    <p style="margin: 5px 0; opacity: 0.9;">Phone: {{ $instituteSettings->institution_phone }} | Email: {{ $instituteSettings->institution_email }}</p>
                                </div>
                            </div>
                            <div style="background: rgba(255,255,255,0.2); padding: 10px; margin: 15px -20px -20px -20px; font-size: 1.2rem; font-weight: bold; letter-spacing: 1px; text-align: center;">
                                PAYMENT RECEIPT
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <strong>Receipt No:</strong> {{ $receipt->id }}<br>
                                <strong>Date:</strong> {{ $receipt->receipt_date->format('M d, Y') }}<br>
                                <strong>Payment Method:</strong> {{ ucfirst($receipt->payment_method) }}
                            </div>
                            <div class="col-6 text-right">
                                <strong>Student:</strong> {{ $receipt->student->full_name }}<br>
                                <strong>Admission No:</strong> {{ $receipt->student->admission_number }}<br>
                                <strong>Class:</strong> {{ $receipt->student->currentEnrollment->class->name ?? 'N/A' }}
                            </div>
                        </div>
                        
                        <!-- Fee Details Table -->
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Fee Description</th>
                                    <th>Category</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($receipt->payment->bill->billItems as $item)
                                <tr>
                                    <td>{{ $item->description }}</td>
                                    <td>
                                        <span class="badge badge-secondary">{{ ucfirst($item->fee_category ?? 'General') }}</span>
                                    </td>
                                    <td class="text-right">NRs. {{ number_format($item->final_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="2">Bill Total Amount</th>
                                    <th class="text-right">NRs. {{ number_format($receipt->payment->bill->total_amount, 2) }}</th>
                                </tr>
                                <tr class="table-light">
                                    <th colspan="2">Total Amount Paid So Far</th>
                                    <th class="text-right">NRs. {{ number_format($receipt->payment->bill->paid_amount, 2) }}</th>
                                </tr>
                                <tr class="table-light">
                                    <th colspan="2">Amount Paid (This Receipt)</th>
                                    <th class="text-right">NRs. {{ number_format($receipt->amount, 2) }}</th>
                                </tr>
                                @php
                                    $remainingBalance = $receipt->payment->bill->balance_amount;
                                @endphp
                                @if($remainingBalance > 0)
                                <tr class="table-light">
                                    <th colspan="2">Remaining Balance</th>
                                    <th class="text-right">NRs. {{ number_format($remainingBalance, 2) }}</th>
                                </tr>
                                @else
                                <tr class="table-light">
                                    <th colspan="2">Bill Status</th>
                                    <th class="text-right">FULLY PAID</th>
                                </tr>
                                @endif
                            </tfoot>
                        </table>

                        @php
                            // Get other unpaid bills for this student
                            $otherDueBills = \App\Models\StudentBill::where('student_id', $receipt->student->id)
                                ->where('id', '!=', $receipt->payment->bill->id)
                                ->where('balance_amount', '>', 0)
                                ->with('billItems')
                                ->orderBy('due_date', 'asc')
                                ->limit(3)
                                ->get();
                        @endphp

                        @if($otherDueBills->count() > 0)
                        <!-- Other Due Bills -->
                        <div class="mt-4">
                            <h6><strong>Other Pending Bills</strong></h6>
                            <div class="alert alert-warning">
                                <strong>Note:</strong> Student has {{ $otherDueBills->count() }} other pending bill(s)
                                <table class="table table-sm mt-2 mb-0">
                                    <thead>
                                        <tr>
                                            <th>Bill #</th>
                                            <th>Description</th>
                                            <th>Due Date</th>
                                            <th class="text-right">Amount Due</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($otherDueBills as $bill)
                                        <tr>
                                            <td>{{ $bill->id }}</td>
                                            <td>{{ $bill->billItems->pluck('description')->join(', ') }}</td>
                                            <td>{{ $bill->due_date ? $bill->due_date->format('M d, Y') : 'N/A' }}</td>
                                            <td class="text-right">NRs. {{ number_format($bill->balance_amount, 2) }}</td>
                                        </tr>
                                        @endforeach
                                        <tr class="table-secondary">
                                            <th colspan="3">Total Outstanding</th>
                                            <th class="text-right">NRs. {{ number_format($otherDueBills->sum('balance_amount'), 2) }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <div class="row mt-4">
                            <div class="col-6">
                                <p><strong>Received by:</strong> {{ $receipt->issuer->name }}</p>
                                <p><strong>Signature:</strong> _________________</p>
                            </div>
                            <div class="col-6 text-right">
                                <p><strong>Date:</strong> {{ $receipt->receipt_date->format('M d, Y') }}</p>
                                <p class="text-muted small">This is a computer generated receipt</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Receipt Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Receipt ID:</strong> {{ $receipt->id }}</p>
                    <p><strong>Issue Date:</strong> {{ $receipt->receipt_date->format('M d, Y') }}</p>
                    <p><strong>Amount:</strong> NRs. {{ number_format($receipt->amount, 2) }}</p>
                    <p><strong>Payment Method:</strong> {{ ucfirst($receipt->payment_method) }}</p>
                    <p><strong>Issued By:</strong> {{ $receipt->issuer->name }}</p>
                    <p><strong>Created:</strong> {{ $receipt->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Student Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $receipt->student->full_name }}</p>
                    <p><strong>Admission Number:</strong> {{ $receipt->student->admission_number }}</p>
                    <p><strong>Class:</strong> {{ $receipt->student->currentEnrollment->class->name ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $receipt->student->email ?? 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $receipt->student->phone ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Payment ID:</strong> 
                        <a href="{{ route('admin.payments.show', $receipt->payment) }}">
                            #{{ $receipt->payment->id }}
                        </a>
                    </p>
                    <p><strong>Bill Number:</strong> 
                        <a href="{{ route('admin.student-bills.show', $receipt->payment->bill) }}">
                            {{ $receipt->payment->bill->bill_number }}
                        </a>
                    </p>
                    <p><strong>Payment Date:</strong> {{ $receipt->payment->payment_date->format('M d, Y') }}</p>
                    @if($receipt->payment->reference_number)
                    <p><strong>Reference:</strong> {{ $receipt->payment->reference_number }}</p>
                    @endif
                </div>
            </div>

            @if($pendingBills->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Other Pending Bills
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <small><i class="fas fa-info-circle me-1"></i>This student has {{ $pendingBills->count() }} other pending bill(s).</small>
                    </div>
                    @foreach($pendingBills as $bill)
                    <div class="border rounded p-2 mb-2 {{ $bill->is_overdue ? 'border-danger' : 'border-warning' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $bill->bill_number }}</strong>
                                <br>
                                <small class="text-muted">{{ $bill->bill_title }}</small>
                                <br>
                                <small class="text-muted">Due: {{ $bill->due_date->format('M d, Y') }}</small>
                            </div>
                            <div class="text-right">
                                <div class="font-weight-bold">NRs. {{ number_format($bill->balance_amount, 2) }}</div>
                                <span class="badge {{ $bill->status_badge_class }}">{{ ucfirst($bill->status) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="mt-2">
                        <strong>Total Outstanding: NRs. {{ number_format($pendingBills->sum('balance_amount'), 2) }}</strong>
                    </div>
                </div>
            </div>
            @endif

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.fees.receipts.print', $receipt) }}"
                       class="btn btn-success btn-block btn-lg" target="_blank">
                        <i class="fas fa-print me-2"></i>Print Receipt
                    </a>
                    <a href="{{ route('admin.fees.receipts.download', $receipt) }}"
                       class="btn btn-primary btn-block mt-2" target="_blank">
                        <i class="fas fa-download me-2"></i>Download PDF
                    </a>
                    <hr>
                    <a href="{{ route('admin.payments.show', $receipt->payment) }}"
                       class="btn btn-info btn-block">
                        <i class="fas fa-eye me-2"></i>View Payment Details
                    </a>
                    <a href="{{ route('admin.student-bills.show', $receipt->payment->bill) }}"
                       class="btn btn-outline-success btn-block mt-2">
                        <i class="fas fa-file-invoice me-2"></i>View Original Bill
                    </a>
                    <a href="{{ route('admin.payments.create', ['student_id' => $receipt->payment->student_id]) }}"
                       class="btn btn-outline-primary btn-block mt-2">
                        <i class="fas fa-plus me-2"></i>Add Another Payment
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>



<style>
.receipt-preview {
    background: white;
    min-height: 600px;
}

@media print {
    .receipt-preview {
        box-shadow: none !important;
        border: none !important;
    }
}
</style>
@endsection
