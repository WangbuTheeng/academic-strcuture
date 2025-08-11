@extends('layouts.admin')

@section('title', 'Fee Structures')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs text-primary me-2"></i>Fee Structures
            </h1>
            <p class="text-muted mb-0">Manage fee structures for different academic levels and programs</p>
        </div>
        <div>
            <a href="{{ route('admin.fees.structures.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Fee Structure
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Structures
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $feeStructures->total() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs fa-2x text-gray-300"></i>
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
                                Active Structures
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $feeStructures->where('is_active', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Fee Categories
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ count($feeCategories) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
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
                                Total Amount
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rs. {{ number_format($feeStructures->sum('amount'), 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.fees.structures.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="academic_year_id" class="form-label">Academic Year</label>
                    <select name="academic_year_id" id="academic_year_id" class="form-select">
                        <option value="">All Academic Years</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="level_id" class="form-label">Level</label>
                    <select name="level_id" id="level_id" class="form-select">
                        <option value="">All Levels</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}" {{ request('level_id') == $level->id ? 'selected' : '' }}>
                                {{ $level->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="fee_category" class="form-label">Fee Category</label>
                    <select name="fee_category" id="fee_category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($feeCategories as $key => $category)
                            <option value="{{ $key }}" {{ request('fee_category') == $key ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Search by fee name or description..." 
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Fee Structures Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-2"></i>Fee Structures List
            </h6>
        </div>
        <div class="card-body">
            @if($feeStructures->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Fee Name</th>
                                <th>Category</th>
                                <th>Academic Year</th>
                                <th>Level/Program</th>
                                <th>Amount</th>
                                <th>Frequency</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feeStructures as $feeStructure)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $feeStructure->fee_name }}</div>
                                        @if($feeStructure->description)
                                            <small class="text-muted">{{ Str::limit($feeStructure->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $feeStructure->fee_category_label }}</span>
                                        @if($feeStructure->is_mandatory)
                                            <span class="badge bg-warning ms-1">Mandatory</span>
                                        @endif
                                    </td>
                                    <td>{{ $feeStructure->academicYear->name ?? 'N/A' }}</td>
                                    <td>
                                        <div>
                                            @if($feeStructure->level)
                                                <strong>{{ $feeStructure->level->name }}</strong>
                                            @endif
                                            @if($feeStructure->program)
                                                <br><small class="text-muted">{{ $feeStructure->program->name }}</small>
                                            @endif
                                            @if($feeStructure->class)
                                                <br><small class="text-muted">{{ $feeStructure->class->name }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">{{ $feeStructure->formatted_amount }}</span>
                                        @if($feeStructure->late_fee_amount > 0)
                                            <br><small class="text-danger">Late Fee: {{ $feeStructure->formatted_late_fee_amount }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $feeStructure->billing_frequency_label }}</td>
                                    <td>
                                        @if($feeStructure->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.fees.structures.show', $feeStructure) }}" 
                                               class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.fees.structures.edit', $feeStructure) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.fees.structures.toggle-status', $feeStructure) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-{{ $feeStructure->is_active ? 'warning' : 'success' }}" 
                                                        title="{{ $feeStructure->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas fa-{{ $feeStructure->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.fees.structures.destroy', $feeStructure) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this fee structure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
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

                <!-- Enhanced Pagination -->
                <x-enhanced-pagination
                    :paginator="$feeStructures"
                    :route="route('admin.fees.structures.index')"
                />
            @else
                <div class="text-center py-5">
                    <i class="fas fa-cogs fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No Fee Structures Found</h5>
                    <p class="text-muted">Start by creating your first fee structure.</p>
                    <a href="{{ route('admin.fees.structures.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Fee Structure
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit form on filter change
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelects = document.querySelectorAll('#academic_year_id, #level_id, #fee_category, #status');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    });
</script>
@endpush
