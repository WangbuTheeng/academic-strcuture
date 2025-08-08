@extends('layouts.admin')

@section('title', 'Edit Fee Structure')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit text-primary me-2"></i>Edit Fee Structure
            </h1>
            <p class="text-muted mb-0">Update fee structure information</p>
        </div>
        <div>
            <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
            <a href="{{ route('admin.fees.structures.show', $feeStructure) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-2"></i>View Details
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit me-2"></i>Fee Structure Information
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fees.structures.update', $feeStructure) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">Select Academic Year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id', $feeStructure->academic_year_id) == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="fee_category" class="form-label">Fee Category <span class="text-danger">*</span></label>
                                <select name="fee_category" id="fee_category" class="form-select @error('fee_category') is-invalid @enderror" required>
                                    <option value="">Select Category</option>
                                    @foreach($feeCategories as $key => $category)
                                        <option value="{{ $key }}" {{ old('fee_category', $feeStructure->fee_category) == $key ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fee_category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="fee_name" class="form-label">Fee Name <span class="text-danger">*</span></label>
                                <input type="text" name="fee_name" id="fee_name" 
                                       class="form-control @error('fee_name') is-invalid @enderror" 
                                       value="{{ old('fee_name', $feeStructure->fee_name) }}" 
                                       placeholder="Enter fee name" required>
                                @error('fee_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="is_mandatory" class="form-label">Type</label>
                                <select name="is_mandatory" id="is_mandatory" class="form-select @error('is_mandatory') is-invalid @enderror">
                                    <option value="0" {{ old('is_mandatory', $feeStructure->is_mandatory) == '0' ? 'selected' : '' }}>Optional</option>
                                    <option value="1" {{ old('is_mandatory', $feeStructure->is_mandatory) == '1' ? 'selected' : '' }}>Mandatory</option>
                                </select>
                                @error('is_mandatory')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Optional description">{{ old('description', $feeStructure->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Academic Structure -->
                        <h6 class="text-primary mb-3">Academic Structure (Optional)</h6>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="level_id" class="form-label">Level</label>
                                <select name="level_id" id="level_id" class="form-select @error('level_id') is-invalid @enderror">
                                    <option value="">All Levels</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}" {{ old('level_id', $feeStructure->level_id) == $level->id ? 'selected' : '' }}>
                                            {{ $level->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('level_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="program_id" class="form-label">Program</label>
                                <select name="program_id" id="program_id" class="form-select @error('program_id') is-invalid @enderror">
                                    <option value="">All Programs</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" {{ old('program_id', $feeStructure->program_id) == $program->id ? 'selected' : '' }}>
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="class_id" class="form-label">Class</label>
                                <select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $feeStructure->class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Amount and Billing -->
                        <h6 class="text-primary mb-3">Amount & Billing</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount (Rs.) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="amount" step="0.01" min="0.01" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount', $feeStructure->amount) }}" 
                                       placeholder="0.00" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="billing_frequency" class="form-label">Billing Frequency <span class="text-danger">*</span></label>
                                <select name="billing_frequency" id="billing_frequency" class="form-select @error('billing_frequency') is-invalid @enderror" required>
                                    <option value="">Select Frequency</option>
                                    <option value="monthly" {{ old('billing_frequency', $feeStructure->billing_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('billing_frequency', $feeStructure->billing_frequency) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="semester" {{ old('billing_frequency', $feeStructure->billing_frequency) == 'semester' ? 'selected' : '' }}>Semester</option>
                                    <option value="annual" {{ old('billing_frequency', $feeStructure->billing_frequency) == 'annual' ? 'selected' : '' }}>Annual</option>
                                </select>
                                @error('billing_frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="due_date_offset" class="form-label">Due Date Offset (Days) <span class="text-danger">*</span></label>
                                <input type="number" name="due_date_offset" id="due_date_offset" min="0" 
                                       class="form-control @error('due_date_offset') is-invalid @enderror" 
                                       value="{{ old('due_date_offset', $feeStructure->due_date_offset) }}" 
                                       placeholder="30" required>
                                @error('due_date_offset')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="late_fee_amount" class="form-label">Late Fee Amount (Rs.)</label>
                                <input type="number" name="late_fee_amount" id="late_fee_amount" step="0.01" min="0" 
                                       class="form-control @error('late_fee_amount') is-invalid @enderror" 
                                       value="{{ old('late_fee_amount', $feeStructure->late_fee_amount) }}" 
                                       placeholder="0.00">
                                @error('late_fee_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="grace_period_days" class="form-label">Grace Period (Days)</label>
                                <input type="number" name="grace_period_days" id="grace_period_days" min="0" 
                                       class="form-control @error('grace_period_days') is-invalid @enderror" 
                                       value="{{ old('grace_period_days', $feeStructure->grace_period_days) }}" 
                                       placeholder="0">
                                @error('grace_period_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Effective Dates -->
                        <h6 class="text-primary mb-3">Effective Period (Optional)</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="effective_from" class="form-label">Effective From</label>
                                <input type="date" name="effective_from" id="effective_from" 
                                       class="form-control @error('effective_from') is-invalid @enderror" 
                                       value="{{ old('effective_from', $feeStructure->effective_from?->format('Y-m-d')) }}">
                                @error('effective_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="effective_to" class="form-label">Effective To</label>
                                <input type="date" name="effective_to" id="effective_to" 
                                       class="form-control @error('effective_to') is-invalid @enderror" 
                                       value="{{ old('effective_to', $feeStructure->effective_to?->format('Y-m-d')) }}">
                                @error('effective_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       class="form-check-input @error('is_active') is-invalid @enderror"
                                       {{ old('is_active', $feeStructure->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">
                                    Active (Fee structure is currently in use)
                                </label>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Fee Structure
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-2"></i>Current Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Current Amount</label>
                        <div class="h4 text-success">{{ $feeStructure->formatted_amount }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Current Status</label>
                        <div>
                            @if($feeStructure->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Created</label>
                        <div>{{ $feeStructure->created_at->format('M d, Y') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Last Updated</label>
                        <div>{{ $feeStructure->updated_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Load programs when level is selected
    document.getElementById('level_id').addEventListener('change', function() {
        const levelId = this.value;
        const programSelect = document.getElementById('program_id');
        const classSelect = document.getElementById('class_id');
        
        // Clear dependent dropdowns
        programSelect.innerHTML = '<option value="">All Programs</option>';
        classSelect.innerHTML = '<option value="">All Classes</option>';
        
        if (levelId) {
            fetch(`{{ route('admin.fees.programs-by-level') }}?level_id=${levelId}`)
                .then(response => response.json())
                .then(programs => {
                    programs.forEach(program => {
                        const option = document.createElement('option');
                        option.value = program.id;
                        option.textContent = program.name;
                        programSelect.appendChild(option);
                    });
                });
        }
    });

    // Load classes when program is selected
    document.getElementById('program_id').addEventListener('change', function() {
        const programId = this.value;
        const classSelect = document.getElementById('class_id');
        
        // Clear classes dropdown
        classSelect.innerHTML = '<option value="">All Classes</option>';
        
        if (programId) {
            fetch(`{{ route('admin.fees.classes-by-program') }}?program_id=${programId}`)
                .then(response => response.json())
                .then(classes => {
                    classes.forEach(cls => {
                        const option = document.createElement('option');
                        option.value = cls.id;
                        option.textContent = cls.name;
                        classSelect.appendChild(option);
                    });
                });
        }
    });
</script>
@endpush
