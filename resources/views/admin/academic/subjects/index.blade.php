@extends('layouts.admin')

@section('title', 'Subjects Management')

@section('content')
<div class="container-fluid px-4">
     <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">Subjects Management</h1>
            <p class="mb-0 text-muted">Manage academic subjects and courses</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="toggleBulkActions()" class="btn btn-secondary">
                <i class="fas fa-tasks me-1"></i>
                Bulk Actions
            </button>
            <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Add Subject
            </a>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.subjects.index') }}">
                <div class="row">
                    <!-- Search Input -->
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label">Search Subjects</label>
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

                    <!-- Subject Type Filter -->
                    <div class="col-md-2 mb-3">
                        <label for="subject_type" class="form-label">Type</label>
                        <select name="subject_type" id="subject_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="core" {{ request('subject_type') == 'core' ? 'selected' : '' }}>Core</option>
                            <option value="elective" {{ request('subject_type') == 'elective' ? 'selected' : '' }}>Elective</option>
                            <option value="practical" {{ request('subject_type') == 'practical' ? 'selected' : '' }}>Practical</option>
                            <option value="project" {{ request('subject_type') == 'project' ? 'selected' : '' }}>Project</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>
                        Search
                    </button>
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Subjects Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Subjects List</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleBulkActions()">
                    <i class="fas fa-tasks"></i> Bulk Actions
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($subjects->count() > 0)
                <!-- Bulk Actions -->
                <div id="bulk-actions" class="mb-3" style="display: none;">
                    <form method="POST" action="{{ route('admin.subjects.bulk-action') }}" onsubmit="return confirmBulkAction()">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <select name="action" class="form-control" required>
                                    <option value="">Select Action</option>
                                    <option value="activate">Activate</option>
                                    <option value="deactivate">Deactivate</option>
                                    <option value="delete">Delete</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Apply to Selected</button>
                            </div>
                        </div>
                        <input type="hidden" name="subjects" id="selected-subjects">
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Department</th>
                                <th>Type</th>
                                <th>Credit Hours</th>
                                <th>Programs</th>
                                <th>Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjects as $subject)
                            <tr>
                                <td>
                                    <input type="checkbox" class="subject-checkbox" value="{{ $subject->id }}">
                                </td>
                                <td>
                                    <strong>{{ $subject->name }}</strong>
                                    @if($subject->is_practical)
                                        <span class="badge badge-info badge-sm ml-1">Practical</span>
                                    @endif
                                </td>
                                <td>{{ $subject->code }}</td>
                                <td>{{ $subject->department->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $subject->subject_type === 'core' ? 'primary' : ($subject->subject_type === 'elective' ? 'secondary' : 'info') }}">
                                        {{ ucfirst($subject->subject_type) }}
                                    </span>
                                </td>
                                <td>{{ $subject->credit_hours }}</td>
                                <td>{{ $subject->programs_count ?? 0 }}</td>
                                <td>
                                    <span class="badge badge-{{ $subject->is_active ? 'success' : 'danger' }}">
                                        {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $subjects->firstItem() }} to {{ $subjects->lastItem() }} of {{ $subjects->total() }} results
                    </div>
                    <div>
                        {{ $subjects->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No subjects found.</p>
                    <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add First Subject
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleBulkActions() {
    const bulkActions = document.getElementById('bulk-actions');
    bulkActions.style.display = bulkActions.style.display === 'none' ? 'block' : 'none';
}

document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.subject-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
});

function confirmBulkAction() {
    const selectedSubjects = Array.from(document.querySelectorAll('.subject-checkbox:checked')).map(cb => cb.value);
    if (selectedSubjects.length === 0) {
        alert('Please select at least one subject.');
        return false;
    }
    document.getElementById('selected-subjects').value = JSON.stringify(selectedSubjects);
    return confirm('Are you sure you want to perform this action on selected subjects?');
}
</script>
@endpush
@endsection
