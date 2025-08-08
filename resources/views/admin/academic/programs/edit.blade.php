@extends('layouts.admin')

@section('title', 'Edit Program')

@section('content')
<div class="container-fluid">
     <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">Edit Program</h1>
            <p class="mb-0 text-muted">Update program information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-info">
                <i class="fas fa-eye me-1"></i>
                View Program
            </a>
            <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Programs
            </a>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Program Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.programs.update', $program) }}">
                        @csrf
                        @method('PUT')

                        <!-- Program Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Program Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $program->name) }}" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="e.g., Bachelor of Computer Application" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Enter the full name of the program</div>
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
                                    <option value="{{ $department->id }}" 
                                            {{ old('department_id', $program->department_id) == $department->id ? 'selected' : '' }}>
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
                                    <option value="{{ $level->id }}" 
                                            {{ old('level_id', $program->level_id) == $level->id ? 'selected' : '' }}>
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
                            <div class="col-md-6 mb-3">
                                <label for="duration_years" class="form-label">
                                    Duration (Years) <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="duration_years" id="duration_years" 
                                       value="{{ old('duration_years', $program->duration_years) }}" min="1" max="10"
                                       class="form-control @error('duration_years') is-invalid @enderror" 
                                       placeholder="e.g., 4" required>
                                @error('duration_years')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Program duration in years</div>
                            </div>

                            <!-- Degree Type -->
                            <div class="col-md-6 mb-3">
                                <label for="degree_type" class="form-label">
                                    Degree Type <span class="text-danger">*</span>
                                </label>
                                <select name="degree_type" id="degree_type" 
                                        class="form-select @error('degree_type') is-invalid @enderror" required>
                                    <option value="">Select Type</option>
                                    <option value="school" {{ old('degree_type', $program->degree_type) == 'school' ? 'selected' : '' }}>School</option>
                                    <option value="college" {{ old('degree_type', $program->degree_type) == 'college' ? 'selected' : '' }}>College</option>
                                    <option value="bachelor" {{ old('degree_type', $program->degree_type) == 'bachelor' ? 'selected' : '' }}>Bachelor</option>
                                </select>
                                @error('degree_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Select the type of degree</div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Update Program
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
