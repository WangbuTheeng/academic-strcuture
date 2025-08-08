@extends('layouts.admin')

@section('title', 'Fee Structure Details')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-eye text-primary me-2"></i>Fee Structure Details
            </h1>
            <p class="text-muted mb-0">{{ $feeStructure->fee_name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <a href="{{ route('admin.fees.structures.edit', $feeStructure) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Fee Structure Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Fee Name</label>
                            <div class="fw-bold">{{ $feeStructure->fee_name }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Category</label>
                            <div>
                                <span class="badge bg-info">{{ $feeStructure->fee_category_label }}</span>
                                @if($feeStructure->is_mandatory)
                                    <span class="badge bg-warning ms-1">Mandatory</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Academic Year</label>
                            <div class="fw-bold">{{ $feeStructure->academicYear->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Status</label>
                            <div>
                                @if($feeStructure->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($feeStructure->description)
                    <div class="mb-3">
                        <label class="form-label text-muted">Description</label>
                        <div>{{ $feeStructure->description }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Academic Structure -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-graduation-cap me-2"></i>Academic Structure
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($feeStructure->level)
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Level</label>
                            <div class="fw-bold">{{ $feeStructure->level->name }}</div>
                        </div>
                        @endif

                        @if($feeStructure->program)
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Program</label>
                            <div class="fw-bold">{{ $feeStructure->program->name }}</div>
                        </div>
                        @endif

                        @if($feeStructure->class)
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Class</label>
                            <div class="fw-bold">{{ $feeStructure->class->name }}</div>
                        </div>
                        @endif
                    </div>

                    @if(!$feeStructure->level && !$feeStructure->program && !$feeStructure->class)
                    <div class="text-center text-muted">
                        <i class="fas fa-globe fa-2x mb-2"></i>
                        <div>Applies to All Academic Levels</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Amount Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-money-bill-wave me-2"></i>Amount Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="h2 text-success mb-0">{{ $feeStructure->formatted_amount }}</div>
                        <small class="text-muted">{{ $feeStructure->billing_frequency_label }}</small>
                    </div>

                    @if($feeStructure->late_fee_amount > 0)
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Late Fee:</span>
                            <span class="text-danger fw-bold">{{ $feeStructure->formatted_late_fee_amount }}</span>
                        </div>
                        @if($feeStructure->grace_period_days > 0)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Grace Period:</span>
                            <span>{{ $feeStructure->grace_period_days }} days</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Billing Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-calendar-alt me-2"></i>Billing Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Billing Frequency</label>
                        <div class="fw-bold">{{ $feeStructure->billing_frequency_label }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Due Date Offset</label>
                        <div>{{ $feeStructure->due_date_offset }} days after billing</div>
                    </div>

                    @if($feeStructure->effective_from)
                    <div class="mb-3">
                        <label class="form-label text-muted">Effective From</label>
                        <div>{{ $feeStructure->effective_from->format('M d, Y') }}</div>
                    </div>
                    @endif

                    @if($feeStructure->effective_to)
                    <div class="mb-3">
                        <label class="form-label text-muted">Effective To</label>
                        <div>{{ $feeStructure->effective_to->format('M d, Y') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.fees.structures.edit', $feeStructure) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Structure
                        </a>

                        <form action="{{ route('admin.fees.structures.toggle-status', $feeStructure) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-{{ $feeStructure->is_active ? 'warning' : 'success' }} w-100">
                                <i class="fas fa-{{ $feeStructure->is_active ? 'pause' : 'play' }} me-2"></i>
                                {{ $feeStructure->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.fees.structures.destroy', $feeStructure) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this fee structure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i>Delete Structure
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
