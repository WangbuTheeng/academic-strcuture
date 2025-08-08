@extends('layouts.admin')

@section('title', 'Student Management')

@section('content')

@php
use Illuminate\Support\Facades\Storage;
@endphp
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Student Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Students</li>
                </ol>
            </nav>
            <p class="text-muted">Manage student information, enrollment, and academic records</p>
        </div>
        <div>
            <button onclick="toggleBulkActions()" class="btn btn-secondary me-2">
                <i class="fas fa-tasks"></i> Bulk Actions
            </button>
            <div class="btn-group me-2" role="group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.students.export', ['format' => 'csv'] + request()->query()) }}">
                        <i class="fas fa-file-csv"></i> Export as CSV
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.students.export', ['format' => 'pdf'] + request()->query()) }}">
                        <i class="fas fa-file-pdf"></i> Export as PDF
                    </a></li>
                </ul>
            </div>
            <a href="{{ route('admin.students.show-import') }}" class="btn btn-info me-2">
                <i class="fas fa-upload"></i> Import
            </a>
            <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Student
            </a>
        </div>
    </div>
    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter Students</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.students.index') }}">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-6 mb-3">
                        <label for="search" class="form-label">Search Students</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Search by name, admission number, or phone..."
                                   class="form-control">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Status</option>
                            @if(isset($statuses) && is_array($statuses))
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Class Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="class" class="form-label">Class</label>
                        <select name="class" id="class" class="form-select">
                            <option value="">All Classes</option>
                            @forelse($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @empty
                                <option value="" disabled>No classes available</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions Bar (Hidden by default) -->
    <div id="bulk-actions-bar" class="card shadow mb-4" style="display: none;">
        <div class="card-body bg-warning-light">
            <form method="POST" action="{{ route('admin.students.bulk-action') }}" onsubmit="return confirmBulkAction()">
                @csrf
                <div class="d-flex align-items-center gap-3">
                    <span class="text-warning fw-bold">
                        <span id="selected-count">0</span> students selected
                    </span>
                    <select name="action" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Choose Action</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button type="submit" class="btn btn-warning btn-sm">
                        Apply
                    </button>
                    <button type="button" onclick="clearSelection()" class="btn btn-link btn-sm text-warning">
                        Clear Selection
                    </button>
                </div>
                <input type="hidden" name="students" id="selected-students" value="">
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

    <!-- Students Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Students List</h6>
        </div>
        <div class="card-body">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all" class="form-check-input" onchange="toggleAllStudents()">
                                </th>
                                <th>STUDENT</th>
                                <th>ADMISSION DETAILS</th>
                                <th>CONTACT</th>
                                <th>CURRENT CLASS</th>
                                <th>STATUS</th>
                                <th width="150">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="student-checkbox form-check-input"
                                               value="{{ $student->id }}" onchange="updateSelection()">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($student->photo)
                                                    <img class="rounded-circle" width="50" height="50"
                                                         src="{{ Storage::url($student->photo) }}"
                                                         alt="{{ $student->full_name }}">
                                                @else
                                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                                                         style="width: 50px; height: 50px;">
                                                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $student->full_name }}</div>
                                                <small class="text-muted">{{ $student->gender }} • {{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $student->admission_number }}</div>
                                        <small class="text-muted">{{ $student->admission_date ? $student->admission_date->format('M d, Y') : 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $student->phone }}</div>
                                        @if($student->email)
                                            <small class="text-muted">{{ $student->email }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->currentEnrollment)
                                            <div class="fw-bold">
                                                {{ $student->currentEnrollment->class->name ?? 'N/A' }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $student->currentEnrollment->program->name ?? 'N/A' }}
                                            </small>
                                        @else
                                            <span class="text-muted">Not Enrolled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'active' => 'success',
                                                'inactive' => 'warning',
                                                'graduated' => 'info',
                                                'transferred' => 'secondary',
                                                'dropped' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$student->status] ?? 'secondary' }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.students.destroy', $student) }}"
                                                  class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No students found</h5>
                                            <p class="text-muted">Get started by adding your first student.</p>
                                            <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Add Student
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No students found</h5>
                    <p class="text-muted">Get started by adding your first student.</p>
                    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Student
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    @if($students->hasPages())
        <div class="d-flex justify-content-center">
            {{ $students->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    let selectedStudents = [];

    function toggleBulkActions() {
        const bulkBar = document.getElementById('bulk-actions-bar');
        if (bulkBar.style.display === 'none') {
            bulkBar.style.display = 'block';
        } else {
            bulkBar.style.display = 'none';
        }
    }

    function toggleAllStudents() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.student-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });

        updateSelection();
    }

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.student-checkbox:checked');
        selectedStudents = Array.from(checkboxes).map(cb => cb.value);

        document.getElementById('selected-count').textContent = selectedStudents.length;
        document.getElementById('selected-students').value = selectedStudents.join(',');

        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.student-checkbox');
        const selectAll = document.getElementById('select-all');
        selectAll.checked = selectedStudents.length === allCheckboxes.length;
        selectAll.indeterminate = selectedStudents.length > 0 && selectedStudents.length < allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function confirmBulkAction() {
        const action = document.querySelector('select[name="action"]').value;
        if (!action) {
            alert('Please select an action.');
            return false;
        }

        if (selectedStudents.length === 0) {
            alert('Please select at least one student.');
            return false;
        }

        return confirm(`Are you sure you want to ${action} ${selectedStudents.length} student(s)?`);
    }
</script>
@endpush
