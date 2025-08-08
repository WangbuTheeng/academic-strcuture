@extends('layouts.admin')

@section('title', 'Add New Subject')

@section('content')
<div class="container-fluid px-4">
     <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Add New Subject</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.academic.index') }}">Academic Structure</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subject Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.subjects.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Subject Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Subject Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="department_id">Department <span class="text-danger">*</span></label>
                                    <select class="form-control @error('department_id') is-invalid @enderror" 
                                            id="department_id" name="department_id" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
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
                                    <select class="form-control @error('subject_type') is-invalid @enderror" 
                                            id="subject_type" name="subject_type" required>
                                        <option value="">Select Type</option>
                                        @foreach($subjectTypes as $type)
                                            <option value="{{ $type }}" {{ old('subject_type') == $type ? 'selected' : '' }}>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="credit_hours">Credit Hours <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" min="0" max="10" 
                                           class="form-control @error('credit_hours') is-invalid @enderror" 
                                           id="credit_hours" name="credit_hours" value="{{ old('credit_hours', 3) }}" required>
                                    @error('credit_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_theory">Max Theory Marks</label>
                                    <input type="number" min="0" max="100" 
                                           class="form-control @error('max_theory') is-invalid @enderror" 
                                           id="max_theory" name="max_theory" value="{{ old('max_theory', 0) }}">
                                    @error('max_theory')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_practical">Max Practical Marks</label>
                                    <input type="number" min="0" max="100" 
                                           class="form-control @error('max_practical') is-invalid @enderror" 
                                           id="max_practical" name="max_practical" value="{{ old('max_practical', 0) }}">
                                    @error('max_practical')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="max_assess">Max Assessment Marks</label>
                                    <input type="number" min="0" max="100" 
                                           class="form-control @error('max_assess') is-invalid @enderror" 
                                           id="max_assess" name="max_assess" value="{{ old('max_assess', 0) }}">
                                    @error('max_assess')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Options</label>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_practical" name="is_practical" value="1" {{ old('is_practical') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_practical">
                                            Has Practical Component
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="has_internal" name="has_internal" value="1" {{ old('has_internal', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_internal">
                                            Has Internal Assessment
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Subject
                            </button>
                            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
