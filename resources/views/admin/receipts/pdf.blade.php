@extends('admin.shared.billing-template', [
    'documentTitle' => 'Payment Receipt - ' . ($receipt->receipt_number ?? $receipt->id),
    'documentType' => 'PAYMENT RECEIPT',
    'instituteSettings' => $instituteSettings
])

@section('document-content')
<!-- Receipt Information -->
<div class="document-info">
    <div class="info-section">
        <div class="info-title">Receipt Information</div>
        <div class="info-row">
            <span class="info-label">Receipt Number:</span>
            <span class="info-value">{{ $receipt->receipt_number ?? $receipt->id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Receipt Date:</span>
            <span class="info-value">{{ $receipt->receipt_date->format('M d, Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Method:</span>
            <span class="info-value">{{ ucwords(str_replace('_', ' ', $receipt->payment_method)) }}</span>
        </div>
        @if($receipt->payment->reference_number)
        <div class="info-row">
            <span class="info-label">Reference Number:</span>
            <span class="info-value">{{ $receipt->payment->reference_number }}</span>
        </div>
        @endif
    </div>

    <div class="info-section">
        <div class="info-title">Student Information</div>
        <div class="info-row">
            <span class="info-label">Name:</span>
            <span class="info-value">{{ $receipt->payment->student->full_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Admission No:</span>
            <span class="info-value">{{ $receipt->payment->student->admission_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Class:</span>
            <span class="info-value">{{ $receipt->payment->student->currentEnrollment->class->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Program:</span>
            <span class="info-value">{{ $receipt->payment->student->currentEnrollment->program->name ?? 'N/A' }}</span>
        </div>
    </div>

<!-- Amount Section -->
<div class="total-section">
    <div class="total-row grand-total">
        <span>Amount Received:</span>
        <span>NRs. {{ number_format($receipt->amount, 2) }}</span>
    </div>
    <div style="text-align: center; margin-top: 10px; font-style: italic; color: #666;">
        ({{ ucwords(\App\Helpers\NumberHelper::numberToWords($receipt->amount ?? 0)) }} Only)
    </div>
</div>

<!-- Bill Details -->
<div class="document-items">
    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipt->payment->bill->billItems as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="amount-cell">NRs. {{ number_format($item->final_amount, 2) }}</td>
            </tr>
            @endforeach

            <tr style="border-top: 2px solid #34495e; font-weight: bold;">
                <td>Total Bill Amount</td>
                <td class="amount-cell">NRs. {{ number_format($receipt->payment->bill->total_amount, 2) }}</td>
            </tr>

            <tr class="paid">
                <td><strong>Amount Paid</strong></td>
                <td class="amount-cell"><strong>NRs. {{ number_format($receipt->amount, 2) }}</strong></td>
            </tr>

            @if($receipt->payment->bill->balance_amount > 0)
            <tr class="balance">
                <td>Remaining Balance</td>
                <td class="amount-cell">NRs. {{ number_format($receipt->payment->bill->balance_amount, 2) }}</td>
            </tr>
            @else
            <tr class="paid">
                <td><strong>Bill Status</strong></td>
                <td class="amount-cell"><strong>FULLY PAID</strong></td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
@endsection
