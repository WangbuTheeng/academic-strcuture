@extends('admin.shared.billing-template', [
    'documentTitle' => 'Student Bill - ' . $bill->bill_number,
    'documentType' => 'STUDENT FEE BILL',
    'instituteSettings' => $instituteSettings
])

@section('document-content')
<!-- Bill Information -->
<div class="document-info">
    <div class="info-section">
        <div class="info-title">Student Information</div>
        <div class="info-row">
            <span class="info-label">Name:</span>
            <span class="info-value">{{ $bill->student->full_name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Roll No:</span>
            <span class="info-value">{{ $bill->student->roll_number ?? $bill->student->admission_number ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Class:</span>
            <span class="info-value">{{ $bill->student->currentEnrollment->class->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Program:</span>
            <span class="info-value">{{ $bill->student->currentEnrollment->program->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Academic Year:</span>
            <span class="info-value">{{ $bill->academicYear->name ?? 'N/A' }}</span>
        </div>
    </div>

    <div class="info-section">
        <div class="info-title">Bill Details</div>
        <div class="info-row">
            <span class="info-label">Bill Number:</span>
            <span class="info-value">{{ $bill->bill_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Bill Date:</span>
            <span class="info-value">{{ $bill->bill_date->format('M d, Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Due Date:</span>
            <span class="info-value">{{ $bill->due_date->format('M d, Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value">
                <span class="status-badge status-{{ $bill->status }}">
                    {{ ucfirst($bill->status) }}
                </span>
            </span>
        </div>
    </div>
</div>

<!-- Bill Items -->
<div class="document-items">
    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Category</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bill->billItems as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td>{{ ucfirst($item->fee_category) }}</td>
                <td class="amount-cell">NRs. {{ number_format($item->final_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Total Section -->
<div class="total-section">
    <div class="total-row">
        <span>Subtotal:</span>
        <span>NRs. {{ number_format($bill->total_amount, 2) }}</span>
    </div>
    <div class="total-row">
        <span>Paid Amount:</span>
        <span>NRs. {{ number_format($bill->paid_amount, 2) }}</span>
    </div>
    <div class="total-row grand-total">
        <span>Balance Due:</span>
        <span>NRs. {{ number_format($bill->balance_amount, 2) }}</span>
    </div>
</div>

<!-- Payment Information -->
@if($bill->balance_amount > 0)
<div class="payment-info">
    <strong>Payment Instructions:</strong><br>
    Please pay the balance amount by {{ $bill->due_date->format('M d, Y') }} to avoid late fees.
    Payment can be made at the school office or through online banking.
</div>
@endif

@endsection
