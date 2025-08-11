@extends('layouts.admin')

@section('title', 'Payment Receipts')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-receipt text-primary me-2"></i>Payment Receipts
            </h1>
            <p class="text-muted mb-0">View and manage payment receipts</p>
        </div>
        <div>
            <a href="{{ route('admin.fees.overview') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Overview
            </a>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter Receipts</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.receipts.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Receipt number, student name..." 
                               class="form-control">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" name="date_from" id="date_from" 
                               value="{{ request('date_from') }}" 
                               class="form-control">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" name="date_to" id="date_to" 
                               value="{{ request('date_to') }}" 
                               class="form-control">
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

                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="{{ route('admin.fees.receipts.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Receipts Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Payment Receipts
            </h6>
        </div>
        <div class="card-body">
            @if(isset($receipts) && $receipts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Receipt #</th>
                                <th>Student</th>
                                <th>Payment Date</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipts as $receipt)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $receipt->receipt_number }}</div>
                                        <small class="text-muted">{{ $receipt->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $receipt->payment->student->full_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $receipt->payment->student->admission_number ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $receipt->payment->payment_date->format('M d, Y') ?? 'N/A' }}</td>
                                    <td>
                                        <span class="fw-bold text-success">NRs. {{ number_format($receipt->amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $receipt->payment->payment_method ?? 'N/A')) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Generated</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.fees.receipts.show', $receipt) }}" 
                                               class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.fees.receipts.download', $receipt) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Download PDF">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Enhanced Pagination -->
                <x-enhanced-pagination
                    :paginator="$receipts"
                    :route="route('admin.fees.receipts.index')"
                />
            @else
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Receipts Found</h5>
                    <p class="text-muted">No payment receipts match your search criteria.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit form on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelects = document.querySelectorAll('#payment_method');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    });
</script>
@endpush
