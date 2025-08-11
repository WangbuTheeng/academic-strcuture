@extends('layouts.admin')

@section('title', 'Student Financial History - ' . $student->full_name)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-graduate text-primary me-2"></i>{{ $student->full_name }}
            </h1>
            <p class="text-muted mb-0">Complete Financial History & Bill Details</p>
            <small class="text-muted">
                Admission: {{ $student->admission_number }} | 
                Class: {{ $student->currentEnrollment?->class?->name ?? 'N/A' }} |
                Program: {{ $student->currentEnrollment?->program?->name ?? 'N/A' }}
            </small>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.fees.payments.create', ['student_id' => $student->id]) }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Record Payment
            </a>
            <a href="{{ route('admin.student-bills.create', ['student_id' => $student->id]) }}" class="btn btn-success">
                <i class="fas fa-file-invoice me-2"></i>Create Bill
            </a>
            <a href="{{ route('admin.student-bills.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Bills
            </a>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Billed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($totalBilled, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Paid</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($totalPaid, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-{{ $totalDue > 0 ? 'danger' : 'success' }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $totalDue > 0 ? 'danger' : 'success' }} text-uppercase mb-1">
                                Outstanding Due</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($totalDue, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-{{ $totalDue > 0 ? 'exclamation-triangle' : 'check-double' }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Payment Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalBilled > 0 ? number_format(($totalPaid / $totalBilled) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h4 class="text-primary">{{ $bills->count() }}</h4>
                            <small class="text-muted">Total Bills</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-success">{{ $paidBills }}</h4>
                            <small class="text-muted">Paid Bills</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-warning">{{ $bills->whereIn('status', ['pending', 'partial'])->count() }}</h4>
                            <small class="text-muted">Pending Bills</small>
                        </div>
                        <div class="col-md-3">
                            <h4 class="text-danger">{{ $overdueBills }}</h4>
                            <small class="text-muted">Overdue Bills</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Bills by Academic Year -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Bills by Academic Year
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($billsByYear as $year => $yearBills)
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2">
                            {{ $year }} 
                            <span class="badge badge-secondary">{{ $yearBills->count() }} bills</span>
                            <span class="badge badge-success">Rs. {{ number_format($yearBills->sum('paid_amount'), 2) }} paid</span>
                            @if($yearBills->sum('balance_amount') > 0)
                            <span class="badge badge-danger">Rs. {{ number_format($yearBills->sum('balance_amount'), 2) }} due</span>
                            @endif
                        </h6>
                        
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bill #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Paid</th>
                                        <th>Due</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($yearBills->sortByDesc('bill_date') as $bill)
                                    <tr>
                                        <td>
                                            <strong>{{ $bill->bill_number }}</strong>
                                            @if($bill->due_date && $bill->due_date->isPast() && $bill->balance_amount > 0)
                                            <br><small class="text-danger">Due: {{ $bill->due_date->format('M d, Y') }}</small>
                                            @elseif($bill->due_date)
                                            <br><small class="text-muted">Due: {{ $bill->due_date->format('M d, Y') }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $bill->bill_date->format('M d, Y') }}</td>
                                        <td>Rs. {{ number_format($bill->total_amount, 2) }}</td>
                                        <td class="text-success">Rs. {{ number_format($bill->paid_amount, 2) }}</td>
                                        <td class="{{ $bill->balance_amount > 0 ? 'text-danger' : 'text-success' }}">
                                            Rs. {{ number_format($bill->balance_amount, 2) }}
                                        </td>
                                        <td>
                                            @switch($bill->status)
                                                @case('paid')
                                                    <span class="badge badge-success">Paid</span>
                                                    @break
                                                @case('partial')
                                                    <span class="badge bg-warning text-dark">Partial</span>
                                                    @break
                                                @case('overdue')
                                                    <span class="badge badge-danger">Overdue</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ ucfirst($bill->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.student-bills.show', $bill) }}" 
                                                   class="btn btn-outline-info btn-sm" title="View Bill">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($bill->balance_amount > 0)
                                                <a href="{{ route('admin.fees.payments.create', ['student_id' => $student->id, 'bill_id' => $bill->id]) }}" 
                                                   class="btn btn-outline-success btn-sm" title="Pay Bill">
                                                    <i class="fas fa-credit-card"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Payment History -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Recent Payments
                    </h6>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    @if($allPayments->count() > 0)
                        @foreach($allPayments->take(20) as $payment)
                        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-success">Rs. {{ number_format($payment->amount, 2) }}</div>
                                <small class="text-muted">{{ $payment->payment_date->format('M d, Y') }}</small><br>
                                <small class="text-primary">{{ $payment->bill_info->bill_number }}</small><br>
                                <span class="badge badge-info badge-sm">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                @if($payment->reference_number)
                                <br><small class="text-muted">Ref: {{ $payment->reference_number }}</small>
                                @endif
                            </div>
                            <div class="text-end">
                                <span class="badge badge-{{ $payment->status === 'verified' ? 'success' : 'warning' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                        
                        @if($allPayments->count() > 20)
                        <div class="text-center">
                            <small class="text-muted">Showing recent 20 payments of {{ $allPayments->count() }} total</small>
                        </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-credit-card fa-3x mb-3"></i>
                            <p>No payments recorded yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.badge-sm {
    font-size: 0.7em;
}
</style>
@endpush
