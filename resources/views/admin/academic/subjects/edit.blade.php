@extends('layouts.admin')

@section('title', 'Edit Subject - ' . $subject->name)

@section('content')
<div class="container-fluid px-4">
     <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Subject</h1>
            <p class="mb-0 text-muted">Update subject information and settings</p>
        </div>
        <div>
            <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Subject
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Subjects
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subject Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.subjects.update', $subject) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="department_id">Department <span class="text-danger">*</span></label>
                                    <select name="department_id" id="department_id" class="form-control @error('department_id') is-invalid @enderror" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id', $subject->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject_type">Subject Type <span class="text-danger">*</span></label>
                                    <select name="subject_type" id="subject_type" class="form-control @error('subject_type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        @foreach($subjectTypes as $type)
                                            <option value="{{ $type }}" {{ old('subject_type', $subject->subject_type) == $type ? 'selected' : '' }}>
                                                {{ ucfirst($type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">Subject Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $subject->name) }}" required maxlength="100">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="code">Subject Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" 
                                           value="{{ old('code', $subject->code) }}" required maxlength="10" style="text-transform: uppercase;">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="credit_hours">Credit Hours <span class="text-danger">*</span></label>
                                    <input type="number" name="credit_hours" id="credit_hours" class="form-control @error('credit_hours') is-invalid @enderror" 
                                           value="{{ old('credit_hours', $subject->credit_hours) }}" required min="0" max="10" step="0.5">
                                    @error('credit_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_theory">Theory Marks</label>
                                    <input type="number" name="max_theory" id="max_theory" class="form-control @error('max_theory') is-invalid @enderror" 
                                           value="{{ old('max_theory', $subject->max_theory) }}" min="0" max="100">
                                    @error('max_theory')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_practical">Practical Marks</label>
                                    <input type="number" name="max_practical" id="max_practical" class="form-control @error('max_practical') is-invalid @enderror" 
                                           value="{{ old('max_practical', $subject->max_practical) }}" min="0" max="100">
                                    @error('max_practical')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_assess">Assessment Marks</label>
                                    <input type="number" name="max_assess" id="max_assess" class="form-control @error('max_assess') is-invalid @enderror" 
                                           value="{{ old('max_assess', $subject->max_assess) }}" min="0" max="100">
                                    @error('max_assess')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Subject Options</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="is_practical" id="is_practical" class="form-check-input" 
                                                       value="1" {{ old('is_practical', $subject->is_practical) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_practical">
                                                    Has Practical
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="has_internal" id="has_internal" class="form-check-input" 
                                                       value="1" {{ old('has_internal', $subject->has_internal) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="has_internal">
                                                    Has Internal Assessment
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                                       value="1" {{ old('is_active', $subject->is_active) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Subject
                            </button>
                            <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Subject Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Subject Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-right">
                                <h4 class="text-primary">{{ $subject->programs->count() }}</h4>
                                <small class="text-muted">Programs</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $subject->teacherSubjects->count() }}</h4>
                            <small class="text-muted">Teachers</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-list"></i> All Subjects
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-uppercase subject code
    document.getElementById('code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
});
</script>
@endsection
