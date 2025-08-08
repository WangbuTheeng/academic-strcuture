@extends('layouts.admin')

@section('title', 'Student-wise Fee Report')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-graduate text-primary me-2"></i>Student-wise Fee Report
            </h1>
            <p class="text-muted mb-0">View fee collection and outstanding amounts by student</p>
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

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.reports.student-wise') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="academic_year_id" class="form-label">Academic Year</label>
                        <select class="form-select" id="academic_year_id" name="academic_year_id">
                            <option value="">All Years</option>
                            @foreach(\App\Models\AcademicYear::orderBy('name')->get() as $year)
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
                            @foreach(\App\Models\Level::orderBy('name')->get() as $level)
                                <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="program_id" class="form-label">Program</label>
                        <select class="form-select" id="program_id" name="program_id">
                            <option value="">All Programs</option>
                            @foreach(\App\Models\Program::orderBy('name')->get() as $program)
                                <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Payment Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partially Paid</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search Student</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Student name or admission number">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('admin.fees.reports.student-wise') }}" class="btn btn-secondary">
                            <i class="fas fa-undo me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Total Billed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">NRs. {{ number_format($totalBilled ?? 0, 2) }}</div>
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
                                Total Collected</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">NRs. {{ number_format($totalCollected ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                                Outstanding</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">NRs. {{ number_format($totalOutstanding ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student-wise Report Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Student-wise Fee Details</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Admission No</th>
                            <th>Class</th>
                            <th>Program</th>
                            <th>Total Billed</th>
                            <th>Total Paid</th>
                            <th>Outstanding</th>
                            <th>Collection %</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Sample data - replace with actual data from controller
                            $sampleStudents = collect([
                                (object) [
                                    'id' => 1,
                                    'full_name' => 'John Doe',
                                    'admission_number' => 'ADM001',
                                    'currentEnrollment' => (object) [
                                        'class' => (object) ['name' => 'Grade 10'],
                                        'program' => (object) ['name' => 'Science']
                                    ],
                                    'bills_sum_total_amount' => 50000,
                                    'payments_sum_amount' => 35000,
                                    'bills_sum_balance_amount' => 15000,
                                    'collection_percentage' => 70,
                                    'payment_status' => 'partial'
                                ]
                            ]);
                        @endphp
                        @forelse($sampleStudents as $student)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $student->full_name }}</div>
                            </td>
                            <td>{{ $student->admission_number }}</td>
                            <td>{{ $student->currentEnrollment->class->name ?? 'N/A' }}</td>
                            <td>{{ $student->currentEnrollment->program->name ?? 'N/A' }}</td>
                            <td>NRs. {{ number_format($student->bills_sum_total_amount ?? 0, 2) }}</td>
                            <td>NRs. {{ number_format($student->payments_sum_amount ?? 0, 2) }}</td>
                            <td>
                                @if(($student->bills_sum_balance_amount ?? 0) > 0)
                                    <span class="text-danger fw-bold">NRs. {{ number_format($student->bills_sum_balance_amount, 2) }}</span>
                                @else
                                    <span class="text-success fw-bold">NRs. 0.00</span>
                                @endif
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-{{ ($student->collection_percentage ?? 0) >= 80 ? 'success' : (($student->collection_percentage ?? 0) >= 50 ? 'warning' : 'danger') }}" 
                                         role="progressbar" style="width: {{ $student->collection_percentage ?? 0 }}%">
                                        {{ number_format($student->collection_percentage ?? 0, 1) }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $status = $student->payment_status ?? 'pending';
                                    $badgeClass = match($status) {
                                        'paid' => 'success',
                                        'partial' => 'warning',
                                        'overdue' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($status) }}</span>
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
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="viewDetails({{ $student->id }})" title="View Details">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No students found for the selected criteria</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function exportReport() {
    // Get current filter parameters
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    
    // Create download link
    const exportUrl = `{{ route('admin.fees.reports.student-wise') }}?${params.toString()}`;
    window.open(exportUrl, '_blank');
}

function viewDetails(studentId) {
    // Open student details in new tab
    window.open(`{{ url('admin/students') }}/${studentId}`, '_blank');
}

// Initialize DataTable
$(document).ready(function() {
    $('#dataTable').DataTable({
        "pageLength": 25,
        "order": [[ 6, "desc" ]], // Order by outstanding amount
        "columnDefs": [
            { "orderable": false, "targets": [9] }
        ]
    });
});
</script>
@endsection
