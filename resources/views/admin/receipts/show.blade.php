@extends('layouts.admin')

@section('title', 'Receipt Details')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-receipt text-primary me-2"></i>Receipt #{{ $receipt->id }}
            </h1>
            <p class="text-muted mb-0">Payment receipt details and download options</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.receipts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Receipts
            </a>
            <a href="{{ url('admin/fees/receipts/' . $receipt->id . '/download') }}" class="btn btn-primary" target="_blank">
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
                        <div class="receipt-header text-center mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; margin: -16px -16px 20px -16px; border-radius: 8px 8px 0 0;">
                            @php
                                $instituteSettings = \App\Models\InstituteSettings::current() ?? (object) [
                                    'institution_name' => 'Academic Institution',
                                    'institution_address' => 'Institution Address',
                                    'institution_phone' => '+977-1-XXXXXXX',
                                    'institution_email' => 'info@institution.edu.np',
                                ];
                            @endphp
                            <div class="school-info">
                                <h2 style="margin: 0; font-size: 2rem; font-weight: bold;">{{ $instituteSettings->institution_name }}</h2>
                                <p style="margin: 5px 0; opacity: 0.9;">{{ $instituteSettings->institution_address }}</p>
                                <p style="margin: 5px 0; opacity: 0.9;">Phone: {{ $instituteSettings->institution_phone }} | Email: {{ $instituteSettings->institution_email }}</p>
                            </div>
                            <div style="background: rgba(255,255,255,0.2); padding: 10px; margin: 15px -20px -20px -20px; font-size: 1.2rem; font-weight: bold; letter-spacing: 1px;">
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
                        
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Payment for Bill #{{ $receipt->payment->bill->bill_number }}</td>
                                    <td>NRs. {{ number_format($receipt->amount, 2) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <th>Total Paid Amount</th>
                                    <th>NRs. {{ number_format($receipt->amount, 2) }}</th>
                                </tr>
                                @php
                                    $remainingBalance = $receipt->payment->bill->balance_amount;
                                @endphp
                                @if($remainingBalance > 0)
                                <tr class="table-warning">
                                    <th>Remaining Balance</th>
                                    <th>NRs. {{ number_format($remainingBalance, 2) }}</th>
                                </tr>
                                @else
                                <tr class="table-success">
                                    <th>Bill Status</th>
                                    <th>FULLY PAID</th>
                                </tr>
                                @endif
                            </tfoot>
                        </table>
                        
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

            <div class="card shadow">
                <div class="card-body">
                    <a href="{{ url('admin/fees/receipts/' . $receipt->id . '/download') }}" 
                       class="btn btn-primary btn-block" target="_blank">
                        <i class="fas fa-download me-2"></i>Download PDF
                    </a>
                    <a href="{{ route('admin.payments.show', $receipt->payment) }}" 
                       class="btn btn-info btn-block mt-2">
                        <i class="fas fa-eye me-2"></i>View Payment
                    </a>
                    <a href="{{ route('admin.student-bills.show', $receipt->payment->bill) }}" 
                       class="btn btn-success btn-block mt-2">
                        <i class="fas fa-file-invoice me-2"></i>View Bill
                    </a>
                    <button type="button" class="btn btn-secondary btn-block mt-2" onclick="printReceipt()">
                        <i class="fas fa-print me-2"></i>Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printReceipt() {
    const receiptContent = document.querySelector('.receipt-preview').innerHTML;
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Receipt #{{ $receipt->id }}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .table th { background-color: #f8f9fa; }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .mb-4 { margin-bottom: 20px; }
                .mt-4 { margin-top: 20px; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            ${receiptContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}
</script>

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
