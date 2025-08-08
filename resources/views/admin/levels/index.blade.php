@extends('layouts.admin')

@section('title', 'Manage Levels')
@section('page-title', 'Manage Levels')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Manage Levels</h1>
            <p class="mb-0 text-muted">Educational levels (School, College, Bachelor, etc.)</p>
        </div>
        <div>
            <a href="{{ route('admin.academic.index') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Back to Academic Structure
            </a>
            <a href="{{ route('admin.levels.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Level
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.levels.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Search by name...">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="{{ route('admin.levels.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Levels Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Levels ({{ $levels->total() }})</h6>
            
            @if($levels->count() > 0)
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown" disabled id="bulkActionBtn">
                        Bulk Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="submitBulkAction('delete')">
                            <i class="fas fa-trash text-danger"></i> Delete Selected
                        </a></li>
                    </ul>
                </div>
            @endif
        </div>
        <div class="card-body">
            @if($levels->count() > 0)
                <form id="bulkActionForm" method="POST" action="{{ route('admin.levels.bulk-action') }}">
                    @csrf
                    <input type="hidden" name="action" id="bulkAction">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>Order</th>
                                    <th>Name</th>
                                    <th>Classes</th>
                                    <th>Created</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($levels as $level)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_items[]" value="{{ $level->id }}" class="item-checkbox">
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $level->order }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $level->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $level->classes_count }} classes</span>
                                        </td>
                                        <td>{{ $level->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.levels.show', $level) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.levels.edit', $level) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($level->classes_count == 0)
                                                    <form method="POST" action="{{ route('admin.levels.destroy', $level) }}" 
                                                          style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-outline-secondary" disabled title="Cannot delete - has classes">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $levels->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-layer-group fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-muted">No levels found</h5>
                    <p class="text-muted">Start by creating your first educational level.</p>
                    <a href="{{ route('admin.levels.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Level
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Bulk action functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActionBtn = document.getElementById('bulkActionBtn');

    // Select all functionality
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionButton();
        });
    }

    // Individual checkbox functionality
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionButton);
    });

    function updateBulkActionButton() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        if (bulkActionBtn) {
            bulkActionBtn.disabled = checkedBoxes.length === 0;
        }
    }
});

function submitBulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one item.');
        return;
    }

    if (action === 'delete' && !confirm('Are you sure you want to delete the selected levels?')) {
        return;
    }

    document.getElementById('bulkAction').value = action;
    document.getElementById('bulkActionForm').submit();
}
</script>
@endsection
