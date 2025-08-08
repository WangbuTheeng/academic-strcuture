@extends('layouts.admin')

@section('title', 'Student Enrollments')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">Student Enrollments</h1>
            <p class="mb-0 text-muted">Manage student program enrollments and class assignments</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="toggleBulkActions()" class="btn btn-secondary">
                <i class="fas fa-tasks me-1"></i>
                Bulk Actions
            </button>
            <a href="{{ route('admin.enrollments.bulk-create') }}" class="btn btn-success">
                <i class="fas fa-users me-1"></i>
                Bulk Enroll
            </a>
            <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Enroll Student
            </a>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.enrollments.index') }}">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search Students</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   placeholder="Name or admission number..." 
                                   class="form-control">
                        </div>
                    </div>

                    <!-- Program Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="program" class="form-label">Program</label>
                        <select name="program" id="program" class="form-select">
                            <option value="">All Programs</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ request('program') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }} ({{ $program->department->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Class Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="class" class="form-label">Class</label>
                        <select name="class" id="class" class="form-select">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Academic Year Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="academic_year" class="form-label">Academic Year</label>
                        <select name="academic_year" id="academic_year" class="form-select">
                            <option value="">All Years</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ request('academic_year') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                            <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                            <option value="dropped" {{ request('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>
                        Search
                    </button>
                    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions Bar (Hidden by default) -->
    <div id="bulk-actions-bar" class="card shadow mb-4" style="display: none;">
        <div class="card-body bg-warning-light">
            <form method="POST" action="{{ route('admin.enrollments.bulk-action') }}" onsubmit="return confirmBulkAction()">
                @csrf
                <div class="d-flex align-items-center gap-3">
                    <span class="text-warning fw-bold">
                        <span id="selected-count">0</span> enrollments selected
                    </span>
                    <select name="action" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Choose Action</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
                        <option value="graduate">Graduate</option>
                        <option value="transfer">Mark as Transferred</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button type="submit" class="btn btn-warning btn-sm">
                        Apply
                    </button>
                    <button type="button" onclick="clearSelection()" class="btn btn-link btn-sm text-warning">
                        Clear Selection
                    </button>
                </div>
                <input type="hidden" name="enrollments" id="selected-enrollments" value="">
            </form>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Enrollments Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Student Enrollments</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="select-all" class="form-check-input" onchange="toggleAllEnrollments()">
                            </th>
                            <th>Student</th>
                            <th>Program</th>
                            <th>Class</th>
                            <th>Academic Year</th>
                            <th>Roll No.</th>
                            <th>Status</th>
                            <th>Enrolled</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                            <tr>
                                <td>
                                    <input type="checkbox" class="enrollment-checkbox form-check-input" 
                                           value="{{ $enrollment->id }}" onchange="updateSelection()">
                                </td>
                                <td>
                                    @if($enrollment->student)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                @if($enrollment->student->photo)
                                                    <img class="rounded-circle border border-2 border-primary"
                                                         src="{{ Storage::url($enrollment->student->photo) }}"
                                                         alt="{{ $enrollment->student->full_name }}"
                                                         style="width: 40px; height: 40px; object-fit: cover;"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="avatar-title bg-primary rounded-circle d-none align-items-center justify-content-center"
                                                         style="width: 40px; height: 40px;">
                                                        <span class="text-white fw-bold">
                                                            {{ strtoupper(substr($enrollment->student->first_name ?? 'N', 0, 1) . substr($enrollment->student->last_name ?? 'A', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div class="avatar-title bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                                         style="width: 40px; height: 40px;">
                                                        <span class="text-white fw-bold">
                                                            {{ strtoupper(substr($enrollment->student->first_name ?? 'N', 0, 1) . substr($enrollment->student->last_name ?? 'A', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }}</div>
                                                <small class="text-muted">{{ $enrollment->student->admission_number }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Student record missing
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $enrollment->program->name }}</div>
                                    <small class="text-muted">{{ $enrollment->program->department->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $enrollment->class->name }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $enrollment->academicYear->name }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">{{ $enrollment->roll_no ?? 'N/A' }}</span>
                                    @if($enrollment->section)
                                        <br><small class="text-muted">Section: {{ $enrollment->section }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'active' => 'success',
                                            'inactive' => 'secondary',
                                            'graduated' => 'primary',
                                            'transferred' => 'warning',
                                            'dropped' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$enrollment->status] ?? 'secondary' }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $enrollment->enrollment_date->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.enrollments.show', $enrollment) }}"
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.student-subjects.index', $enrollment) }}"
                                           class="btn btn-sm btn-success" title="Manage Subjects">
                                            <i class="fas fa-book"></i>
                                        </a>
                                        <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.enrollments.destroy', $enrollment) }}"
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this enrollment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-center">
                                        <i class="fas fa-user-graduate fa-3x text-gray-300 mb-3"></i>
                                        <h5 class="text-gray-600">No enrollments found</h5>
                                        <p class="text-muted mb-4">Start by enrolling students in programs.</p>
                                        <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>Enroll Student
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($enrollments->hasPages())
        <div class="d-flex justify-content-center">
            {{ $enrollments->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- JavaScript for bulk actions -->
<script>
    let selectedEnrollments = [];

    function toggleBulkActions() {
        const bulkBar = document.getElementById('bulk-actions-bar');
        if (bulkBar.style.display === 'none') {
            bulkBar.style.display = 'block';
        } else {
            bulkBar.style.display = 'none';
        }
    }

    function toggleAllEnrollments() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.enrollment-checkbox');
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        
        updateSelection();
    }

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.enrollment-checkbox:checked');
        selectedEnrollments = Array.from(checkboxes).map(cb => cb.value);
        
        document.getElementById('selected-count').textContent = selectedEnrollments.length;
        document.getElementById('selected-enrollments').value = selectedEnrollments.join(',');
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.enrollment-checkbox');
        const selectAll = document.getElementById('select-all');
        selectAll.checked = selectedEnrollments.length === allCheckboxes.length;
        selectAll.indeterminate = selectedEnrollments.length > 0 && selectedEnrollments.length < allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.enrollment-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function confirmBulkAction() {
        const action = document.querySelector('select[name="action"]').value;
        if (!action) {
            alert('Please select an action.');
            return false;
        }
        
        if (selectedEnrollments.length === 0) {
            alert('Please select at least one enrollment.');
            return false;
        }
        
        return confirm(`Are you sure you want to ${action} ${selectedEnrollments.length} enrollment(s)?`);
    }
</script>

@push('styles')
<style>
.avatar-sm {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
}

.text-dark {
    color: #212529 !important;
}
</style>
@endpush
@endsection
