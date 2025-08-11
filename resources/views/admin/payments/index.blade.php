@extends('layouts.admin')

@section('title', 'Payments Management')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-credit-card text-primary me-2"></i>Payments Management
            </h1>
            <p class="text-muted mb-0">Track and manage student payments</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.fees.payments.quick-entry') }}" class="btn btn-primary">
                <i class="fas fa-bolt me-2"></i>Quick Payment
            </a>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#studentHistoryModal">
                <i class="fas fa-user-graduate me-2"></i>Student History
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Payments Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($todayPayments ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
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
                                Pending Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pendingCount ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($monthlyPayments ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalCount ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter Payments</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.payments.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Payment number, student name..." 
                               class="form-control">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-select">
                            <option value="">All Methods</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="online" {{ request('payment_method') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" name="date_from" id="date_from" 
                               value="{{ request('date_from') }}" 
                               class="form-control">
                    </div>

                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="{{ route('admin.fees.payments.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Recent Payments
            </h6>
        </div>
        <div class="card-body">
            @if(isset($payments) && $payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Payment #</th>
                                <th>Student</th>
                                <th>Bill</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $payment->payment_number }}</div>
                                        @if($payment->reference_number)
                                            <small class="text-muted">Ref: {{ $payment->reference_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $payment->student->full_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $payment->student->admission_number ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $payment->bill->bill_number ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $payment->bill?->due_date?->format('Due: M d, Y') ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">Rs. {{ number_format($payment->amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                                    </td>
                                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td>
                                        @switch($payment->status)
                                            @case('completed')
                                                <span class="badge bg-success">Completed</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case('failed')
                                                <span class="badge bg-danger">Failed</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-secondary">Cancelled</span>
                                                @break
                                            @default
                                                <span class="badge bg-info">{{ ucfirst($payment->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.fees.payments.show', $payment) }}" 
                                               class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payment->status == 'pending')
                                                <form action="{{ route('admin.fees.payments.verify', $payment) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Verify">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($payments->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }}
                            of {{ $payments->total() }} results
                        </div>
                        <div class="pagination-wrapper">
                            {{ $payments->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-credit-card fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Payments Found</h5>
                    <p class="text-muted">No payments match your search criteria.</p>
                    <a href="{{ route('admin.fees.payments.quick-entry') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Payment
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Student History Modal -->
<div class="modal fade" id="studentHistoryModal" tabindex="-1" aria-labelledby="studentHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentHistoryModalLabel">
                    <i class="fas fa-user-graduate me-2"></i>View Student Financial History
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="studentHistoryForm">
                    <div class="mb-3">
                        <label for="studentSearch" class="form-label">Search Student</label>
                        <input type="text" class="form-control" id="studentSearch"
                               placeholder="Type student name or admission number...">
                        <div class="form-text">Start typing to search for students</div>
                    </div>

                    <div id="studentResults" class="list-group" style="display: none; max-height: 300px; overflow-y: auto;">
                        <!-- Student search results will appear here -->
                    </div>

                    <div id="noResults" class="text-center text-muted py-3" style="display: none;">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <p>No students found matching your search</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Fix pagination button sizes */
.pagination-wrapper .pagination {
    margin-bottom: 0;
}

.pagination-wrapper .page-link {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.5;
}

.pagination-wrapper .page-item.active .page-link {
    background-color: #4e73df;
    border-color: #4e73df;
}

.pagination-wrapper .page-link:hover {
    background-color: #f8f9fc;
    border-color: #dee2e6;
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
    // Auto-submit form on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelects = document.querySelectorAll('#status, #payment_method');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    });
</script>
@endpush
