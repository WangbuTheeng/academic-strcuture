@extends('layouts.admin')

@section('title', 'Classes Management')
@section('page-title', 'Classes Management')

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Classes Management</h1>
            <p class="mb-0 text-muted">Manage academic classes across different levels and departments</p>
        </div>
        <div>
            <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Class
            </a>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter Classes</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.classes.index') }}">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search Classes</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   placeholder="Search by name or code..." 
                                   class="form-control">
                        </div>
                    </div>

                    <!-- Level Filter -->
                    <div class="col-md-2 mb-3">
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

                    <!-- Department Filter -->
                    <div class="col-md-2 mb-3">
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

                    <!-- Status Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Search Button -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions Bar (Hidden by default) -->
    <div id="bulk-actions-bar" class="alert alert-warning d-none mb-4">
        <form method="POST" action="{{ route('admin.classes.bulk-action') }}" onsubmit="return confirmBulkAction()">
            @csrf
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="fw-bold">
                    <span id="selected-count">0</span> classes selected
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
                <button type="button" onclick="clearSelection()" class="btn btn-outline-warning btn-sm">
                    Clear Selection
                </button>
            </div>
            <input type="hidden" name="classes" id="selected-classes" value="">
        </form>
    </div>

    <!-- Classes Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Classes List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="select-all" class="form-check-input" onchange="toggleAllClasses()">
                            </th>
                            <th>Class Details</th>
                            <th>Level & Department</th>
                            <th>Students</th>
                            <th>Programs</th>
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $class)
                            <tr>
                                <td>
                                    <input type="checkbox" class="class-checkbox form-check-input" 
                                           value="{{ $class->id }}" onchange="updateSelection()">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-chalkboard text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $class->name }}</div>
                                            <small class="text-muted">Code: {{ $class->code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $class->level->name ?? 'No Level' }}</div>
                                    <small class="text-muted">{{ $class->department->name ?? 'No Department' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $class->enrollments_count }} Students</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $class->programs_count }} Programs</span>
                                </td>
                                <td>
                                    @if($class->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.classes.show', $class) }}" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.classes.edit', $class) }}" 
                                           class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.classes.destroy', $class) }}" 
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this class?')">
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
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-center">
                                        <i class="fas fa-chalkboard fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No classes found</h5>
                                        <p class="text-muted mb-3">Get started by creating your first class.</p>
                                        <a href="{{ route('admin.classes.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Create Class
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($classes->hasPages())
            <div class="card-footer py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="text-muted small">
                            Showing {{ $classes->firstItem() }} to {{ $classes->lastItem() }} of {{ $classes->total() }} results
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            <nav aria-label="Classes pagination">
                                <ul class="pagination pagination-sm mb-0">
                                    {{-- Previous Page Link --}}
                                    @if ($classes->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="fas fa-chevron-left"></i>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $classes->appends(request()->query())->previousPageUrl() }}">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($classes->appends(request()->query())->getUrlRange(1, $classes->lastPage()) as $page => $url)
                                        @if ($page == $classes->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($classes->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $classes->appends(request()->query())->nextPageUrl() }}">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="fas fa-chevron-right"></i>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom pagination styling */
    .pagination-sm .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.25rem;
    }

    .pagination-sm .page-link i {
        font-size: 0.75rem;
    }

    .pagination .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .pagination .page-link {
        color: #5a5c69;
        border: 1px solid #e3e6f0;
        margin: 0 2px;
    }

    .pagination .page-link:hover {
        color: #3a3b45;
        background-color: #eaecf4;
        border-color: #d1d3e2;
    }

    .pagination .page-item.disabled .page-link {
        color: #858796;
        background-color: #f8f9fc;
        border-color: #e3e6f0;
    }
</style>
@endpush

@push('scripts')
<script>
    let selectedClasses = [];

    function toggleAllClasses() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.class-checkbox');
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        
        updateSelection();
    }

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.class-checkbox:checked');
        selectedClasses = Array.from(checkboxes).map(cb => cb.value);
        
        document.getElementById('selected-count').textContent = selectedClasses.length;
        document.getElementById('selected-classes').value = selectedClasses.join(',');
        
        // Show/hide bulk actions bar
        const bulkBar = document.getElementById('bulk-actions-bar');
        if (selectedClasses.length > 0) {
            bulkBar.classList.remove('d-none');
        } else {
            bulkBar.classList.add('d-none');
        }
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.class-checkbox');
        const selectAll = document.getElementById('select-all');
        selectAll.checked = selectedClasses.length === allCheckboxes.length;
        selectAll.indeterminate = selectedClasses.length > 0 && selectedClasses.length < allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.class-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function confirmBulkAction() {
        const action = document.querySelector('select[name="action"]').value;
        if (!action) {
            alert('Please select an action.');
            return false;
        }
        
        if (selectedClasses.length === 0) {
            alert('Please select at least one class.');
            return false;
        }
        
        return confirm(`Are you sure you want to ${action.replace('_', ' ')} ${selectedClasses.length} class(es)?`);
    }
</script>
@endpush
