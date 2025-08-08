@extends('layouts.admin')

@section('title', 'Faculty Management')
@section('page-title', 'Faculty Management')

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Faculty Management</h1>
            <p class="mb-0 text-muted">Manage academic faculties and their organizational structure</p>
        </div>
        <div>
            <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Faculty
            </a>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.faculties.index') }}" class="row g-3">
                <div class="col-md-8">
                    <label for="search" class="form-label">Search Faculties</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Search by name or code..."
                               class="form-control">
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                    <a href="{{ route('admin.faculties.index') }}" class="btn btn-secondary">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Faculties Table Card -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Faculties List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Faculty</th>
                            <th>Code</th>
                            <th>Departments</th>
                            <th>Programs</th>
                            <th>Classes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faculties as $faculty)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <span class="text-white fw-bold">
                                                    {{ strtoupper(substr($faculty->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $faculty->name }}</div>
                                            <small class="text-muted">Created {{ $faculty->created_at->format('M d, Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $faculty->code }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <i class="fas fa-building me-1"></i>{{ $faculty->departments_count }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="fas fa-graduation-cap me-1"></i>{{ $faculty->programs_count }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">
                                        <i class="fas fa-chalkboard me-1"></i>{{ $faculty->classes_count }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.faculties.show', $faculty) }}"
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.faculties.edit', $faculty) }}"
                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.faculties.destroy', $faculty) }}"
                                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete this faculty?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-center">
                                        <i class="fas fa-university fa-3x text-gray-300 mb-3"></i>
                                        <h5 class="text-gray-600">No faculties found</h5>
                                        <p class="text-muted">Get started by creating your first faculty.</p>
                                        <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>Add Faculty
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($faculties->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $faculties->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- JavaScript for bulk actions -->
<script>
    let selectedFaculties = [];

    function toggleBulkActions() {
        const bulkBar = document.getElementById('bulk-actions-bar');
        bulkBar.classList.toggle('hidden');
    }

    function toggleAllFaculties() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.faculty-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });

        updateSelection();
    }

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.faculty-checkbox:checked');
        selectedFaculties = Array.from(checkboxes).map(cb => cb.value);

        document.getElementById('selected-count').textContent = selectedFaculties.length;
        document.getElementById('selected-faculties').value = selectedFaculties.join(',');

        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.faculty-checkbox');
        const selectAll = document.getElementById('select-all');
        selectAll.checked = selectedFaculties.length === allCheckboxes.length;
        selectAll.indeterminate = selectedFaculties.length > 0 && selectedFaculties.length < allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.faculty-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function confirmBulkAction() {
        const action = document.querySelector('select[name="action"]').value;
        if (!action) {
            alert('Please select an action.');
            return false;
        }

        if (selectedFaculties.length === 0) {
            alert('Please select at least one faculty.');
            return false;
        }

        return confirm(`Are you sure you want to ${action} ${selectedFaculties.length} faculty(ies)?`);
    }
</script>
@endsection
