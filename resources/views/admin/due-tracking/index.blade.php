@extends('layouts.admin')

@section('title', 'Due Tracking')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Due Tracking
            </h1>
            <p class="text-muted mb-0">Monitor overdue payments and send reminders</p>
        </div>
        <div>
            <button class="btn btn-warning" onclick="sendBulkReminders()">
                <i class="fas fa-bell me-2"></i>Send Reminders
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Overdue Bills</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $overdueCount ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
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
                                Due This Week</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $dueThisWeekCount ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
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
                                Total Due Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($totalDueAmount ?? 0, 2) }}
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
                                Collection Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($collectionRate ?? 0, 1) }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Due Bills</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.due-tracking.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               placeholder="Student name, bill number..." 
                               class="form-control">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="due_status" class="form-label">Due Status</label>
                        <select name="due_status" id="due_status" class="form-select">
                            <option value="">All Bills</option>
                            <option value="overdue" {{ request('due_status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            <option value="due_this_week" {{ request('due_status') == 'due_this_week' ? 'selected' : '' }}>Due This Week</option>
                            <option value="due_next_week" {{ request('due_status') == 'due_next_week' ? 'selected' : '' }}>Due Next Week</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="level_id" class="form-label">Level</label>
                        <select name="level_id" id="level_id" class="form-select">
                            <option value="">All Levels</option>
                            @if(isset($levels))
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="program_id" class="form-label">Program</label>
                        <select name="program_id" id="program_id" class="form-select">
                            <option value="">All Programs</option>
                            @if(isset($programs))
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.fees.due-tracking.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs" id="dueTrackingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overdue-tab" data-bs-toggle="tab" data-bs-target="#overdue" type="button" role="tab" style="color: #000 !important;">
                        <i class="fas fa-exclamation-triangle me-2"></i>Overdue Bills ({{ $overdueCount }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="all-dues-tab" data-bs-toggle="tab" data-bs-target="#all-dues" type="button" role="tab" style="color: #000 !important;">
                        <i class="fas fa-users me-2"></i>All Students with Dues ({{ $studentsWithDues->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="defaulters-tab" data-bs-toggle="tab" data-bs-target="#defaulters" type="button" role="tab" style="color: #000 !important;">
                        <i class="fas fa-user-times me-2"></i>Top Defaulters
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="dueTrackingTabsContent">
                <!-- Overdue Bills Tab -->
                <div class="tab-pane fade show active" id="overdue" role="tabpanel">
                    <h6 class="mb-3">
                        <i class="fas fa-table me-2"></i>Overdue Bills
                    </h6>
        <div class="card-body">
            @if(isset($overdueBills) && $overdueBills->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>Student</th>
                                <th>Bill Details</th>
                                <th>Due Date</th>
                                <th>Amount Due</th>
                                <th>Days Overdue</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overdueBills as $bill)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input bill-checkbox" value="{{ $bill->id }}">
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $bill->student->full_name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $bill->student->admission_number ?? 'N/A' }}</small>
                                        <br><small class="text-muted">{{ $bill->student->level->name ?? 'N/A' }} - {{ $bill->student->program->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $bill->bill_number }}</div>
                                        <small class="text-muted">{{ $bill->description ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $bill->due_date->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $bill->due_date->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-danger">NRs. {{ number_format($bill->balance_amount, 2) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $daysOverdue = $bill->due_date->isPast() ? $bill->due_date->diffInDays(now()) : 0;
                                        @endphp
                                        @if($daysOverdue > 0)
                                            <span class="badge bg-danger">{{ $daysOverdue }} days</span>
                                        @else
                                            <span class="badge bg-success">Not overdue</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($bill->due_date->isPast())
                                            <span class="badge bg-danger">Overdue</span>
                                        @elseif($bill->due_date->isToday())
                                            <span class="badge bg-warning">Due Today</span>
                                        @elseif($bill->due_date->isTomorrow())
                                            <span class="badge bg-info">Due Tomorrow</span>
                                        @else
                                            <span class="badge bg-success">Upcoming</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.fees.bills.show', $bill) }}" 
                                               class="btn btn-sm btn-outline-info" title="View Bill">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    onclick="sendReminder({{ $bill->id }})" title="Send Reminder">
                                                <i class="fas fa-bell"></i>
                                            </button>
                                            <a href="{{ route('admin.fees.payments.quick-entry') }}?bill_id={{ $bill->id }}" 
                                               class="btn btn-sm btn-outline-success" title="Record Payment">
                                                <i class="fas fa-plus"></i>
                                            </a>
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
                            <span class="me-2">With selected:</span>
                            <button type="button" class="btn btn-sm btn-warning me-2" onclick="sendBulkReminders()">
                                <i class="fas fa-bell me-1"></i>Send Reminders
                            </button>
                            <button type="button" class="btn btn-sm btn-info" onclick="exportSelected()">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Pagination -->
                        @if($overdueBills->hasPages())
                            <div class="d-flex justify-content-end">
                                {{ $overdueBills->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Due Bills Found</h5>
                    <p class="text-muted">All bills are up to date or no bills match your criteria.</p>
                </div>
            @endif
                </div>

                <!-- All Students with Dues Tab -->
                <div class="tab-pane fade" id="all-dues" role="tabpanel">
                    <h6 class="mb-3">
                        <i class="fas fa-users me-2"></i>All Students with Remaining Dues
                    </h6>
                    @if($studentsWithDues->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Class</th>
                                        <th>Program</th>
                                        <th>Total Due Amount</th>
                                        <th>Number of Bills</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentsWithDues as $student)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $student->full_name }}</div>
                                            <small class="text-muted">{{ $student->admission_number }}</small>
                                        </td>
                                        <td>{{ $student->currentEnrollment->class->name ?? 'N/A' }}</td>
                                        <td>{{ $student->currentEnrollment->program->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="fw-bold text-danger">NRs. {{ number_format($student->pending_bills_sum_balance_amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $student->pending_bills_count }} bills</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.students.show', $student->id) }}"
                                                   class="btn btn-sm btn-outline-info" title="View Student">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.fees.payments.create', ['student_id' => $student->id]) }}"
                                                   class="btn btn-sm btn-outline-success" title="Add Payment">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                        onclick="sendReminder({{ $student->id }})" title="Send Reminder">
                                                    <i class="fas fa-bell"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No Students with Dues</h5>
                            <p class="text-muted">All students have cleared their dues.</p>
                        </div>
                    @endif
                </div>

                <!-- Top Defaulters Tab -->
                <div class="tab-pane fade" id="defaulters" role="tabpanel">
                    <h6 class="mb-3">
                        <i class="fas fa-user-times me-2"></i>Top Defaulters (Overdue Only)
                    </h6>
                    @if($topDefaulters->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Rank</th>
                                        <th>Student</th>
                                        <th>Class</th>
                                        <th>Program</th>
                                        <th>Overdue Amount</th>
                                        <th>Overdue Bills</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topDefaulters as $index => $student)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ $index < 3 ? 'danger' : 'warning' }}">
                                                #{{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $student->full_name }}</div>
                                            <small class="text-muted">{{ $student->admission_number }}</small>
                                        </td>
                                        <td>{{ $student->currentEnrollment->class->name ?? 'N/A' }}</td>
                                        <td>{{ $student->currentEnrollment->program->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="fw-bold text-danger">NRs. {{ number_format($student->pending_bills_sum_balance_amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ $student->pending_bills_count }} bills</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.students.show', $student->id) }}"
                                                   class="btn btn-sm btn-outline-info" title="View Student">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.fees.payments.create', ['student_id' => $student->id]) }}"
                                                   class="btn btn-sm btn-outline-success" title="Add Payment">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="sendUrgentReminder({{ $student->id }})" title="Send Urgent Reminder">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No Defaulters</h5>
                            <p class="text-muted">No students have overdue payments.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Select all functionality
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.bill-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Auto-submit form on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelects = document.querySelectorAll('#due_status, #level_id, #program_id');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    });

    function sendReminder(billId) {
        if (confirm('Send payment reminder for this bill?')) {
            // Implementation for sending individual reminder
            alert('Reminder sent successfully!');
        }
    }

    function sendBulkReminders() {
        const selectedBills = document.querySelectorAll('.bill-checkbox:checked');
        if (selectedBills.length === 0) {
            alert('Please select at least one bill.');
            return;
        }
        
        if (confirm(`Send payment reminders for ${selectedBills.length} selected bill(s)?`)) {
            // Implementation for sending bulk reminders
            alert('Reminders sent successfully!');
        }
    }

    function exportSelected() {
        const selectedBills = document.querySelectorAll('.bill-checkbox:checked');
        if (selectedBills.length === 0) {
            alert('Please select at least one bill.');
            return;
        }
        
        // Implementation for exporting selected bills
        alert('Export functionality will be implemented.');
    }

    // Send reminder to individual student
    function sendReminder(studentId) {
        if (confirm('Send payment reminder to this student?')) {
            fetch('{{ route("admin.fees.due-tracking.send-reminders") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    student_ids: [studentId]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Reminder sent successfully!');
                } else {
                    alert('Failed to send reminder: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending the reminder.');
            });
        }
    }

    // Send urgent reminder to defaulter
    function sendUrgentReminder(studentId) {
        if (confirm('Send urgent payment reminder to this student?')) {
            fetch('{{ route("admin.fees.due-tracking.send-reminders") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    student_ids: [studentId],
                    urgent: true
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Urgent reminder sent successfully!');
                } else {
                    alert('Failed to send urgent reminder: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending the urgent reminder.');
            });
        }
    }
</script>
@endpush
