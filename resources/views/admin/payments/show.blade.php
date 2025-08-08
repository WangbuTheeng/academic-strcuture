@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-receipt text-primary me-2"></i>Payment #{{ $payment->id }}
            </h1>
            <p class="text-muted mb-0">Payment details and verification status</p>
        </div>
        <div class="btn-group">
            @if(!$payment->is_verified && $payment->status === 'pending')
            <button type="button" class="btn btn-success" onclick="verifyPayment({{ $payment->id }})">
                <i class="fas fa-check me-2"></i>Verify Payment
            </button>
            <button type="button" class="btn btn-danger" onclick="rejectPayment({{ $payment->id }})">
                <i class="fas fa-times me-2"></i>Reject Payment
            </button>
            @endif
            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Payments
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Payment Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Student:</strong> {{ $payment->student->full_name }}</p>
                            <p><strong>Admission Number:</strong> {{ $payment->student->admission_number }}</p>
                            <p><strong>Class:</strong> {{ $payment->student->currentEnrollment->class->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Payment Date:</strong> {{ $payment->payment_date->format('M d, Y') }}</p>
                            <p><strong>Amount:</strong> NRs. {{ number_format($payment->amount, 2) }}</p>
                            <p><strong>Payment Method:</strong> {{ ucfirst($payment->payment_method) }}</p>
                        </div>
                    </div>
                    
                    @if($payment->reference_number)
                    <div class="row">
                        <div class="col-12">
                            <p><strong>Reference Number:</strong> {{ $payment->reference_number }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($payment->remarks)
                    <div class="row">
                        <div class="col-12">
                            <p><strong>Remarks:</strong> {{ $payment->remarks }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Bill Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bill Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Bill Number:</strong> 
                                <a href="{{ route('admin.student-bills.show', $payment->bill) }}">
                                    {{ $payment->bill->bill_number }}
                                </a>
                            </p>
                            <p><strong>Bill Date:</strong> {{ $payment->bill->bill_date->format('M d, Y') }}</p>
                            <p><strong>Due Date:</strong> {{ $payment->bill->due_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Amount:</strong> NRs. {{ number_format($payment->bill->total_amount, 2) }}</p>
                            <p><strong>Paid Amount:</strong> NRs. {{ number_format($payment->bill->paid_amount, 2) }}</p>
                            <p><strong>Balance Amount:</strong> NRs. {{ number_format($payment->bill->balance_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification History -->
            @if($payment->verification_history)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Verification History</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6>Payment Created</h6>
                                <p class="text-muted">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                                <p>Created by: {{ $payment->creator->name }}</p>
                            </div>
                        </div>
                        
                        @if($payment->is_verified)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>Payment Verified</h6>
                                <p class="text-muted">{{ $payment->verified_at->format('M d, Y H:i') }}</p>
                                <p>Verified by: {{ $payment->verifier->name }}</p>
                                @if($payment->verification_remarks)
                                <p><strong>Remarks:</strong> {{ $payment->verification_remarks }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Payment Status -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Status</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h4 class="text-primary">NRs. {{ number_format($payment->amount, 2) }}</h4>
                        <p class="text-muted">Payment Amount</p>
                        
                        <span class="badge badge-lg badge-{{ $payment->status === 'verified' ? 'success' : ($payment->status === 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                        
                        @if($payment->is_verified)
                        <div class="mt-3">
                            <i class="fas fa-check-circle text-success fa-2x"></i>
                            <p class="text-success mt-2">Payment Verified</p>
                        </div>
                        @elseif($payment->status === 'rejected')
                        <div class="mt-3">
                            <i class="fas fa-times-circle text-danger fa-2x"></i>
                            <p class="text-danger mt-2">Payment Rejected</p>
                        </div>
                        @else
                        <div class="mt-3">
                            <i class="fas fa-clock text-warning fa-2x"></i>
                            <p class="text-warning mt-2">Pending Verification</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Receipts -->
            @if($payment->receipts->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Receipts</h6>
                </div>
                <div class="card-body">
                    @foreach($payment->receipts as $receipt)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>Receipt #{{ $receipt->id }}</strong><br>
                            <small class="text-muted">{{ $receipt->receipt_date->format('M d, Y') }}</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.receipts.show', $receipt) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                        </div>
                    </div>
                    <hr>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="card shadow">
                <div class="card-body">
                    @if($payment->is_verified)
                    <a href="{{ route('admin.receipts.show', $payment->receipts->first()) }}" class="btn btn-success btn-block">
                        <i class="fas fa-receipt me-2"></i>View Receipt
                    </a>
                    @endif
                    
                    <a href="{{ route('admin.student-bills.show', $payment->bill) }}" class="btn btn-info btn-block mt-2">
                        <i class="fas fa-file-invoice me-2"></i>View Bill
                    </a>
                    
                    @if(!$payment->is_verified && $payment->status === 'pending')
                    <button type="button" class="btn btn-warning btn-block mt-2" onclick="editPayment()">
                        <i class="fas fa-edit me-2"></i>Edit Payment
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function verifyPayment(paymentId) {
    const remarks = prompt('Verification remarks (optional):');
    
    fetch(`{{ url('admin/fees/payments') }}/${paymentId}/verify`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ remarks: remarks })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error verifying payment');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error verifying payment');
    });
}

function rejectPayment(paymentId) {
    const reason = prompt('Please enter rejection reason:');
    if (reason) {
        fetch(`{{ url('admin/fees/payments') }}/${paymentId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ remarks: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error rejecting payment');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error rejecting payment');
        });
    }
}

function editPayment() {
    // Redirect to edit page or show edit modal
    alert('Edit functionality to be implemented');
}
</script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -29px;
    top: 17px;
    width: 2px;
    height: calc(100% + 5px);
    background-color: #e3e6f0;
}

.badge-lg {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}
</style>
@endsection
