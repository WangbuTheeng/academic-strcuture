@extends('layouts.admin')

@section('title', 'Outstanding Dues Report')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-exclamation-triangle text-danger me-2"></i>Outstanding Dues Report
            </h1>
            <p class="text-muted mb-0">View all pending and overdue bills</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.fees.reports.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Reports
            </a>
            <button type="button" class="btn btn-success" onclick="exportReport()">
                <i class="fas fa-download me-2"></i>Export Excel
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Outstanding</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($totalOutstanding, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Overdue Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($overdueAmount, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.reports.outstanding-dues') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="academic_year_id" class="form-label">Academic Year</label>
                        <select class="form-select" id="academic_year_id" name="academic_year_id">
                            <option value="">All Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="level_id" class="form-label">Level</label>
                        <select class="form-select" id="level_id" name="level_id">
                            <option value="">All Levels</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.fees.reports.outstanding-dues') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Outstanding Bills Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Outstanding Bills
            </h6>
        </div>
        <div class="card-body">
            @if($outstandingBills->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Bill Number</th>
                                <th>Academic Year</th>
                                <th>Bill Date</th>
                                <th>Due Date</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Outstanding</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($outstandingBills as $bill)
                                <tr class="{{ $bill->due_date && $bill->due_date->isPast() ? 'table-danger' : '' }}">
                                    <td>
                                        <div class="fw-bold">{{ $bill->student->full_name }}</div>
                                        <small class="text-muted">{{ $bill->student->admission_number }}</small>
                                    </td>
                                    <td>{{ $bill->bill_number }}</td>
                                    <td>{{ $bill->academicYear->name }}</td>
                                    <td>{{ $bill->bill_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($bill->due_date)
                                            <span class="{{ $bill->due_date->isPast() ? 'text-danger fw-bold' : 'text-muted' }}">
                                                {{ $bill->due_date->format('M d, Y') }}
                                            </span>
                                            @if($bill->due_date->isPast())
                                                <br><small class="text-danger">{{ $bill->due_date->diffForHumans() }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">No due date</span>
                                        @endif
                                    </td>
                                    <td>Rs. {{ number_format($bill->total_amount, 2) }}</td>
                                    <td class="text-success">Rs. {{ number_format($bill->paid_amount, 2) }}</td>
                                    <td class="text-danger fw-bold">Rs. {{ number_format($bill->balance_amount, 2) }}</td>
                                    <td>
                                        @switch($bill->status)
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case('partial')
                                                <span class="badge bg-info">Partial</span>
                                                @break
                                            @case('overdue')
                                                <span class="badge bg-danger">Overdue</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($bill->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.fees.bills.show', $bill) }}" 
                                               class="btn btn-sm btn-outline-info" title="View Bill">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.fees.payments.create', ['student_id' => $bill->student_id, 'bill_id' => $bill->id]) }}" 
                                               class="btn btn-sm btn-outline-success" title="Add Payment">
                                                <i class="fas fa-credit-card"></i>
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
                    :paginator="$outstandingBills" 
                    :route="route('admin.fees.reports.outstanding-dues')" 
                />
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">No Outstanding Dues!</h5>
                    <p class="text-muted">All bills have been paid or no bills match your criteria.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function exportReport() {
    // Get current filter parameters
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    
    // Create download link
    const exportUrl = `{{ route('admin.fees.reports.outstanding-dues') }}?${params.toString()}`;
    window.open(exportUrl, '_blank');
}

// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
    const filterSelects = document.querySelectorAll('#academic_year_id, #level_id, #status');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endsection

<style>
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
