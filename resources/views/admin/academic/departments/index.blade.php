@extends('layouts.admin')

@section('title', 'Department Management')

@section('content')
<div class="container-fluid">
     <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Department Management</h1>
            <p class="mb-0 text-muted">Manage academic departments within faculties</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="toggleBulkActions()" class="btn btn-secondary">
                <i class="fas fa-tasks me-1"></i>
                Bulk Actions
            </button>
            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Add Department
            </a>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.departments.index') }}">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-8 mb-3">
                        <label for="search" class="form-label">Search Departments</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Search by name, code, or faculty..."
                                   class="form-control">
                        </div>
                    </div>

                    <!-- Faculty Filter -->
                    <div class="col-md-4 mb-3">
                        <label for="faculty" class="form-label">Faculty</label>
                        <select name="faculty" id="faculty" class="form-select">
                            <option value="">All Faculties</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}" {{ request('faculty') == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>
                        Search
                    </button>
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions Bar (Hidden by default) -->
    <div id="bulk-actions-bar" class="card shadow mb-4" style="display: none;">
        <div class="card-body bg-warning-light">
            <form method="POST" action="{{ route('admin.departments.bulk-action') }}" onsubmit="return confirmBulkAction()">
                @csrf
                <div class="d-flex align-items-center gap-3">
                    <span class="text-warning fw-bold">
                        <span id="selected-count">0</span> departments selected
                    </span>
                    <select name="action" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Choose Action</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button type="submit" class="btn btn-warning btn-sm">
                        Apply
                    </button>
                    <button type="button" onclick="clearSelection()" class="btn btn-link btn-sm text-warning">
                        Clear Selection
                    </button>
                </div>
                <input type="hidden" name="departments" id="selected-departments" value="">
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

    <!-- Departments Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Departments List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="select-all" class="form-check-input" onchange="toggleAllDepartments()">
                            </th>
                            <th>Department</th>
                            <th>Faculty</th>
                            <th>Code</th>
                            <th>Programs</th>
                            <th>Classes</th>
                            <th>Subjects</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                            <tr>
                                <td>
                                    <input type="checkbox" class="department-checkbox form-check-input"
                                           value="{{ $department->id }}" onchange="updateSelection()">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-title bg-primary rounded-circle">
                                                {{ strtoupper(substr($department->name, 0, 2)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $department->name }}</div>
                                            <small class="text-muted">Created {{ $department->created_at->format('M d, Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $department->faculty->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $department->faculty->code ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    @if($department->code)
                                        <span class="badge bg-primary">{{ $department->code }}</span>
                                    @else
                                        <span class="text-muted">No code</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-graduation-cap text-success me-1"></i>
                                        {{ $department->programs_count }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-chalkboard text-info me-1"></i>
                                        {{ $department->classes_count }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-book text-purple me-1"></i>
                                        {{ $department->subjects_count }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.departments.show', $department) }}"
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.departments.edit', $department) }}"
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.departments.destroy', $department) }}"
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?')">
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
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-center">
                                        <i class="fas fa-building fa-3x text-gray-300 mb-3"></i>
                                        <h5 class="text-gray-600">No departments found</h5>
                                        <p class="text-muted mb-4">Get started by creating your first department.</p>
                                        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>Add Department
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
    @if($departments->hasPages())
        <div class="d-flex justify-content-center">
            {{ $departments->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- JavaScript for bulk actions -->
<script>
    let selectedDepartments = [];

    function toggleBulkActions() {
        const bulkBar = document.getElementById('bulk-actions-bar');
        if (bulkBar.style.display === 'none') {
            bulkBar.style.display = 'block';
        } else {
            bulkBar.style.display = 'none';
        }
    }

    function toggleAllDepartments() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.department-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });

        updateSelection();
    }

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.department-checkbox:checked');
        selectedDepartments = Array.from(checkboxes).map(cb => cb.value);

        document.getElementById('selected-count').textContent = selectedDepartments.length;
        document.getElementById('selected-departments').value = selectedDepartments.join(',');

        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.department-checkbox');
        const selectAll = document.getElementById('select-all');
        selectAll.checked = selectedDepartments.length === allCheckboxes.length;
        selectAll.indeterminate = selectedDepartments.length > 0 && selectedDepartments.length < allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.department-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function confirmBulkAction() {
        const action = document.querySelector('select[name="action"]').value;
        if (!action) {
            alert('Please select an action.');
            return false;
        }

        if (selectedDepartments.length === 0) {
            alert('Please select at least one department.');
            return false;
        }

        return confirm(`Are you sure you want to ${action} ${selectedDepartments.length} department(s)?`);
    }
</script>

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
}

.text-purple {
    color: #6f42c1 !important;
}

.bg-warning-light {
    background-color: #fff3cd !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #5a5c69;
    background-color: #f8f9fc;
}

.table td {
    vertical-align: middle;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.badge {
    font-size: 0.75rem;
}

.form-check-input:checked {
    background-color: #4e73df;
    border-color: #4e73df;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.text-gray-600 {
    color: #858796 !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}
</style>
@endpush
@endsection
