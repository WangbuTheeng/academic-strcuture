@extends('layouts.admin')

@section('title', 'Create Fee Structure')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus text-primary me-2"></i>Create Fee Structure
            </h1>
            <p class="text-muted mb-0">Add a new fee structure for academic programs</p>
        </div>
        <div>
            <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>Fee Structure Details
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.fees.structures.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="academic_year_id" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">Select Academic Year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
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
                                    <option value="">Select Fee Category</option>
                                    @foreach($feeCategories as $key => $category)
                                        <option value="{{ $key }}" {{ old('fee_category') == $key ? 'selected' : '' }}>
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
                            <div class="col-md-12">
                                <label for="fee_name" class="form-label">Fee Name <span class="text-danger">*</span></label>
                                <input type="text" name="fee_name" id="fee_name" 
                                       class="form-control @error('fee_name') is-invalid @enderror" 
                                       value="{{ old('fee_name') }}" 
                                       placeholder="Enter fee name (e.g., Monthly Tuition Fee)" required>
                                @error('fee_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" rows="3" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          placeholder="Enter fee description (optional)">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="level_id" class="form-label">Level</label>
                                <select name="level_id" id="level_id" class="form-select @error('level_id') is-invalid @enderror">
                                    <option value="">All Levels</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
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
                                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
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
                                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount (Rs.) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="amount" step="0.01" min="0" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount') }}" 
                                       placeholder="0.00" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="billing_frequency" class="form-label">Billing Frequency <span class="text-danger">*</span></label>
                                <select name="billing_frequency" id="billing_frequency" class="form-select @error('billing_frequency') is-invalid @enderror" required>
                                    <option value="">Select Frequency</option>
                                    @foreach($billingFrequencies as $key => $frequency)
                                        <option value="{{ $key }}" {{ old('billing_frequency') == $key ? 'selected' : '' }}>
                                            {{ $frequency }}
                                        </option>
                                    @endforeach
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
                                       value="{{ old('due_date_offset', 30) }}" required>
                                <small class="form-text text-muted">Days from bill generation to due date</small>
                                @error('due_date_offset')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="late_fee_amount" class="form-label">Late Fee Amount (Rs.)</label>
                                <input type="number" name="late_fee_amount" id="late_fee_amount" step="0.01" min="0" 
                                       class="form-control @error('late_fee_amount') is-invalid @enderror" 
                                       value="{{ old('late_fee_amount', 0) }}" 
                                       placeholder="0.00">
                                @error('late_fee_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="grace_period_days" class="form-label">Grace Period (Days)</label>
                                <input type="number" name="grace_period_days" id="grace_period_days" min="0" 
                                       class="form-control @error('grace_period_days') is-invalid @enderror" 
                                       value="{{ old('grace_period_days', 0) }}">
                                <small class="form-text text-muted">Days before late fee applies</small>
                                @error('grace_period_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="is_mandatory" id="is_mandatory" 
                                           class="form-check-input" value="1" 
                                           {{ old('is_mandatory', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_mandatory">
                                        Mandatory Fee
                                    </label>
                                    <small class="form-text text-muted d-block">Required for all students in this category</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="is_active" 
                                           class="form-check-input" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Status
                                    </label>
                                    <small class="form-text text-muted d-block">Fee structure is currently active</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.fees.structures.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Fee Structure
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-2"></i>Help & Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">Fee Categories:</h6>
                    <ul class="list-unstyled small">
                        <li><strong>Tuition:</strong> Academic program fees</li>
                        <li><strong>Laboratory:</strong> Lab usage and equipment fees</li>
                        <li><strong>Library:</strong> Library access and services</li>
                        <li><strong>Examination:</strong> Assessment and certification</li>
                        <li><strong>Activity:</strong> Sports and extracurricular</li>
                        <li><strong>Transport:</strong> School transportation</li>
                        <li><strong>Hostel:</strong> Accommodation charges</li>
                        <li><strong>Miscellaneous:</strong> Other institutional charges</li>
                    </ul>

                    <h6 class="text-primary mt-3">Billing Frequency:</h6>
                    <ul class="list-unstyled small">
                        <li><strong>Monthly:</strong> Bills generated every month</li>
                        <li><strong>Quarterly:</strong> Bills generated every 3 months</li>
                        <li><strong>Semester:</strong> Bills generated per semester</li>
                        <li><strong>Annual:</strong> Bills generated once per year</li>
                    </ul>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Tip:</strong> Leave Level, Program, and Class empty to apply this fee structure to all students in the academic year.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Dynamic program loading based on level selection
    document.getElementById('level_id').addEventListener('change', function() {
        const levelId = this.value;
        const programSelect = document.getElementById('program_id');
        const classSelect = document.getElementById('class_id');
        
        // Reset program and class selects
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
                })
                .catch(error => console.error('Error loading programs:', error));
        }
    });

    // Dynamic class loading based on program selection
    document.getElementById('program_id').addEventListener('change', function() {
        const programId = this.value;
        const classSelect = document.getElementById('class_id');
        
        // Reset class select
        classSelect.innerHTML = '<option value="">All Classes</option>';
        
        if (programId) {
            fetch(`{{ route('admin.fees.classes-by-program') }}?program_id=${programId}`)
                .then(response => response.json())
                .then(classes => {
                    classes.forEach(classItem => {
                        const option = document.createElement('option');
                        option.value = classItem.id;
                        option.textContent = classItem.name;
                        classSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading classes:', error));
        }
    });
</script>
@endpush
