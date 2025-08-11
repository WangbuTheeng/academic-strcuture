@extends('layouts.admin')

@section('title', 'Student Bills')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-invoice text-primary me-2"></i>Student Bills
            </h1>
            <p class="text-muted mb-0">Manage student billing and payment tracking</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.student-bills.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Bill
            </a>
            <a href="{{ route('admin.student-bills.bulk-generate') }}" class="btn btn-success">
                <i class="fas fa-layer-group me-2"></i>Bulk Generate
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Bills
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalBills) }}
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Amount
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($totalAmount, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                Paid Amount
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($paidAmount, 2) }}
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Amount
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($pendingAmount, 2) }}
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

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.bills.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="academic_year_id" class="form-label">Academic Year</label>
                    <select name="academic_year_id" id="academic_year_id" class="form-select">
                        <option value="">All Academic Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Status</option>
                        @foreach($statuses as $key => $status)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" 
                           value="{{ request('date_from') }}">
                </div>

                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" 
                           value="{{ request('date_to') }}">
                </div>

                <div class="col-md-8">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Search by student name, admission number, or bill number..." 
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.fees.bills.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bills Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Student Bills List
            </h6>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-primary" onclick="selectAll()">
                    <i class="fas fa-check-square me-1"></i>Select All
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="clearSelection()">
                    <i class="fas fa-square me-1"></i>Clear
                </button>
                <button type="button" class="btn btn-outline-success" onclick="printSelectedBills()">
                    <i class="fas fa-print me-1"></i>Print Selected
                </button>
                @if(config('app.debug'))
                    <a href="{{ route('admin.student-bills.print-bulk', ['bill_ids' => '1,2', 'debug' => '1']) }}"
                       target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-bug me-1"></i>Test Print
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if($bills->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleAll(this)">
                                </th>
                                <th>Bill Details</th>
                                <th>Student</th>
                                <th>Academic Year</th>
                                <th>Amounts</th>
                                <th>Dates</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bills as $bill)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="bill_ids[]" value="{{ $bill->id }}" class="bill-checkbox">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $bill->bill_number }}</div>
                                        <div class="small text-muted">{{ $bill->bill_title }}</div>
                                        @if($bill->description)
                                            <div class="small text-muted">{{ Str::limit($bill->description, 40) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $bill->student->full_name ?? 'N/A' }}</div>
                                        <div class="small text-muted">{{ $bill->student->admission_number ?? 'N/A' }}</div>
                                        @if($bill->class)
                                            <div class="small text-muted">{{ $bill->class->name }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $bill->academicYear->name ?? 'N/A' }}</td>
                                    <td>
                                        <div class="small">
                                            <strong>Total:</strong> {{ $bill->formatted_total_amount }}
                                        </div>
                                        <div class="small text-success">
                                            <strong>Paid:</strong> {{ $bill->formatted_paid_amount }}
                                        </div>
                                        <div class="small text-warning">
                                            <strong>Balance:</strong> {{ $bill->formatted_balance_amount }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <strong>Bill:</strong> {{ $bill->bill_date?->format('d/m/Y') ?? 'N/A' }}
                                        </div>
                                        <div class="small">
                                            <strong>Due:</strong> {{ $bill->due_date?->format('d/m/Y') ?? 'N/A' }}
                                        </div>
                                        @if($bill->is_overdue)
                                            <div class="small text-danger">
                                                <strong>Overdue:</strong> {{ $bill->days_overdue }} days
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $bill->status === 'paid' ? 'success' : ($bill->status === 'overdue' ? 'danger' : ($bill->status === 'partial' ? 'info' : 'warning')) }}">
                                            {{ $bill->status_label }}
                                        </span>
                                        @if($bill->is_locked)
                                            <br><span class="badge bg-secondary mt-1">Locked</span>
                                        @endif
                                        @if($bill->paid_amount > 0 && $bill->status !== 'paid')
                                            <br><span class="badge bg-warning mt-1" title="Cannot edit - has payments">Has Payments</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.student-bills.preview', $bill) }}"
                                               class="btn btn-sm btn-outline-success" title="View Bill" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <a href="{{ route('admin.fees.bills.show', $bill) }}"
                                               class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.students.financial-history', $bill->student_id) }}"
                                               class="btn btn-sm btn-outline-secondary" title="Student History">
                                                <i class="fas fa-user-graduate"></i>
                                            </a>
                                            @if(!$bill->is_locked && $bill->status !== 'paid' && $bill->paid_amount == 0)
                                                <a href="{{ route('admin.fees.bills.edit', $bill) }}"
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if($bill->balance_amount > 0)
                                                <a href="{{ route('admin.fees.payments.create', ['student_id' => $bill->student_id]) }}" 
                                                   class="btn btn-sm btn-outline-success" title="Add Payment">
                                                    <i class="fas fa-credit-card"></i>
                                                </a>
                                            @endif
                                            @if(!$bill->is_locked && $bill->paid_amount == 0)
                                                <form action="{{ route('admin.fees.bills.destroy', $bill) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this bill?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
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

                <!-- Bulk Actions -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <label class="form-label me-2 mb-0">Bulk Actions:</label>
                            <select id="bulkAction" class="form-select form-select-sm me-2" style="width: auto;">
                                <option value="">Select Action</option>
                                <option value="send_reminders">Send Reminders</option>
                                <option value="export_selected">Export Selected</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-primary" onclick="executeBulkAction()">
                                <i class="fas fa-play me-1"></i>Execute
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Enhanced Pagination -->
                        <x-enhanced-pagination
                            :paginator="$bills"
                            :route="route('admin.fees.bills.index')"
                        />
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Bills Found</h5>
                    <p class="text-muted">Start by creating bills for students.</p>
                    <a href="{{ route('admin.fees.bills.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Bill
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Checkbox management
    function toggleAll(source) {
        const checkboxes = document.querySelectorAll('.bill-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }

    function selectAll() {
        const checkboxes = document.querySelectorAll('.bill-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        document.getElementById('selectAllCheckbox').checked = true;
    }

    function clearSelection() {
        const checkboxes = document.querySelectorAll('.bill-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        document.getElementById('selectAllCheckbox').checked = false;
    }

    // Bulk actions
    function executeBulkAction() {
        const action = document.getElementById('bulkAction').value;
        const selectedBills = Array.from(document.querySelectorAll('.bill-checkbox:checked')).map(cb => cb.value);

        if (!action) {
            alert('Please select an action.');
            return;
        }

        if (selectedBills.length === 0) {
            alert('Please select at least one bill.');
            return;
        }

        switch (action) {
            case 'send_reminders':
                sendReminders(selectedBills);
                break;
            case 'export_selected':
                exportSelected(selectedBills);
                break;
        }
    }

    function sendReminders(billIds) {
        // Implementation for sending reminders
        console.log('Sending reminders for bills:', billIds);
        alert('Reminder functionality will be implemented in the next phase.');
    }

    function exportSelected(billIds) {
        // Implementation for exporting selected bills
        console.log('Exporting bills:', billIds);
        alert('Export functionality will be implemented in the next phase.');
    }

    function printSelectedBills() {
        const selectedBills = getSelectedBills();

        if (selectedBills.length === 0) {
            alert('Please select at least one bill to print.');
            return;
        }

        // Create URL with bill IDs as query parameters
        const billIdsString = selectedBills.join(',');
        const printUrl = '{{ route("admin.student-bills.print-bulk") }}' + '?bill_ids=' + encodeURIComponent(billIdsString);

        // Open in new tab
        window.open(printUrl, '_blank');
    }

    // Auto-submit form on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelects = document.querySelectorAll('#academic_year_id, #status');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    });
</script>
@endpush
