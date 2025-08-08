@extends('layouts.admin')

@section('title', 'Create New Program')

@section('content')
<div class="container-fluid">
     <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">Create New Program</h1>
            <p class="mb-0 text-muted">Add a new academic program</p>
        </div>
        <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Programs
        </a>
    </div>

    <!-- Create Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Program Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.programs.store') }}">
                        @csrf

                        <div class="row">
                            <!-- Program Name -->
                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label">
                                    Program Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="e.g., Bachelor of Computer Application" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Enter the full name of the program</div>
                            </div>

                            <!-- Program Code -->
                            <div class="col-md-4 mb-3">
                                <label for="code" class="form-label">
                                    Program Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="code" id="code" value="{{ old('code') }}"
                                       class="form-control @error('code') is-invalid @enderror"
                                       placeholder="e.g., BCA" required maxlength="10">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Short code for the program</div>
                            </div>
                        </div>

                        <!-- Department Selection -->
                        <div class="mb-3">
                            <label for="department_id" class="form-label">
                                Department <span class="text-danger">*</span>
                            </label>
                            <select name="department_id" id="department_id" 
                                    class="form-select @error('department_id') is-invalid @enderror" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }} ({{ $department->faculty->name ?? 'No Faculty' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the department this program belongs to</div>
                        </div>

                        <!-- Level Selection -->
                        <div class="mb-3">
                            <label for="level_id" class="form-label">
                                Level <span class="text-danger">*</span>
                            </label>
                            <select name="level_id" id="level_id" 
                                    class="form-select @error('level_id') is-invalid @enderror" required>
                                <option value="">Select Level</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('level_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the educational level for this program</div>
                        </div>

                        <div class="row">
                            <!-- Duration -->
                            <div class="col-md-4 mb-3">
                                <label for="duration_years" class="form-label">
                                    Duration (Years) <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="duration_years" id="duration_years"
                                       value="{{ old('duration_years') }}" min="1" max="10"
                                       class="form-control @error('duration_years') is-invalid @enderror"
                                       placeholder="e.g., 4" required>
                                @error('duration_years')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Program duration in years</div>
                            </div>

                            <!-- Degree Type -->
                            <div class="col-md-4 mb-3">
                                <label for="degree_type" class="form-label">
                                    Degree Type <span class="text-danger">*</span>
                                </label>
                                <select name="degree_type" id="degree_type"
                                        class="form-select @error('degree_type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="school" {{ old('degree_type') == 'school' ? 'selected' : '' }}>School</option>
                                    <option value="college" {{ old('degree_type') == 'college' ? 'selected' : '' }}>College</option>
                                    <option value="bachelor" {{ old('degree_type') == 'bachelor' ? 'selected' : '' }}>Bachelor</option>
                                </select>
                                @error('degree_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Select the type of degree</div>
                            </div>

                            <!-- Program Type -->
                            <div class="col-md-4 mb-3">
                                <label for="program_type" class="form-label">
                                    Program Type <span class="text-danger">*</span>
                                </label>
                                <select name="program_type" id="program_type"
                                        class="form-select @error('program_type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="semester" {{ old('program_type') == 'semester' ? 'selected' : '' }}>Semester</option>
                                    <option value="yearly" {{ old('program_type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                                @error('program_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Semester or yearly system</div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Enter program description...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Optional description of the program</div>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       class="form-check-input @error('is_active') is-invalid @enderror"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">
                                    Active Program
                                </label>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Check to make this program active</div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Create Program
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.form-label {
    font-weight: 600;
    color: #374151;
}

.form-text {
    font-size: 0.875rem;
    color: #6b7280;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.text-danger {
    color: #dc3545 !important;
}

.btn {
    font-weight: 500;
}
</style>
@endpush
@endsection
