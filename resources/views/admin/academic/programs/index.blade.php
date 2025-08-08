@extends('layouts.admin')

@section('title', 'Program Management')

@section('content')
<div class="container-fluid">
     <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">Program Management</h1>
            <p class="mb-0 text-muted">Manage academic programs and courses</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="toggleBulkActions()" class="btn btn-secondary">
                <i class="fas fa-tasks me-1"></i>
                Bulk Actions
            </button>
            <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Add Program
            </a>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.programs.index') }}">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Search Programs</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   placeholder="Search by name or code..." 
                                   class="form-control">
                        </div>
                    </div>

                    <!-- Department Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="department" class="form-label">Department</label>
                        <select name="department" id="department" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Level Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="level" class="form-label">Level</label>
                        <select name="level" id="level" class="form-select">
                            <option value="">All Levels</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" {{ request('level') == $level->id ? 'selected' : '' }}>
                                    {{ $level->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Program Type Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="program_type" class="form-label">Type</label>
                        <select name="program_type" id="program_type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($programTypes as $type)
                                <option value="{{ $type }}" {{ request('program_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
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
                    <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions Bar (Hidden by default) -->
    <div id="bulk-actions-bar" class="card shadow mb-4" style="display: none;">
        <div class="card-body bg-warning-light">
            <form method="POST" action="{{ route('admin.programs.bulk-action') }}" onsubmit="return confirmBulkAction()">
                @csrf
                <div class="d-flex align-items-center gap-3">
                    <span class="text-warning fw-bold">
                        <span id="selected-count">0</span> programs selected
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
                <input type="hidden" name="programs" id="selected-programs" value="">
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

    <!-- Programs Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Programs List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="select-all" class="form-check-input" onchange="toggleAllPrograms()">
                            </th>
                            <th>Program</th>
                            <th>Department</th>
                            <th>Level</th>
                            <th>Duration</th>
                            <th>Type</th>
                            <th>Enrollments</th>
                            <th>Subjects</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programs as $program)
                            <tr>
                                <td>
                                    <input type="checkbox" class="program-checkbox form-check-input" 
                                           value="{{ $program->id }}" onchange="updateSelection()">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-title bg-success rounded-circle">
                                                {{ strtoupper(substr($program->name, 0, 2)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $program->name }}</div>
                                            <small class="text-muted">{{ $program->degree_type }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $program->department->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $program->department->faculty->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $program->level->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $program->duration_years }}</span> years
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ ucfirst($program->degree_type) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users text-primary me-1"></i>
                                        {{ $program->enrollments_count }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-book text-success me-1"></i>
                                        {{ $program->subjects_count }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.programs.show', $program) }}" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.programs.edit', $program) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.programs.destroy', $program) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this program?')">
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
                                        <i class="fas fa-graduation-cap fa-3x text-gray-300 mb-3"></i>
                                        <h5 class="text-gray-600">No programs found</h5>
                                        <p class="text-muted mb-4">Get started by creating your first program.</p>
                                        <a href="{{ route('admin.programs.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>Add Program
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
    @if($programs->hasPages())
        <div class="d-flex justify-content-center">
            {{ $programs->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- JavaScript for bulk actions -->
<script>
    let selectedPrograms = [];

    function toggleBulkActions() {
        const bulkBar = document.getElementById('bulk-actions-bar');
        if (bulkBar.style.display === 'none') {
            bulkBar.style.display = 'block';
        } else {
            bulkBar.style.display = 'none';
        }
    }

    function toggleAllPrograms() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.program-checkbox');
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        
        updateSelection();
    }

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.program-checkbox:checked');
        selectedPrograms = Array.from(checkboxes).map(cb => cb.value);
        
        document.getElementById('selected-count').textContent = selectedPrograms.length;
        document.getElementById('selected-programs').value = selectedPrograms.join(',');
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.program-checkbox');
        const selectAll = document.getElementById('select-all');
        selectAll.checked = selectedPrograms.length === allCheckboxes.length;
        selectAll.indeterminate = selectedPrograms.length > 0 && selectedPrograms.length < allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.program-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function confirmBulkAction() {
        const action = document.querySelector('select[name="action"]').value;
        if (!action) {
            alert('Please select an action.');
            return false;
        }
        
        if (selectedPrograms.length === 0) {
            alert('Please select at least one program.');
            return false;
        }
        
        return confirm(`Are you sure you want to ${action} ${selectedPrograms.length} program(s)?`);
    }
</script>
@endsection
