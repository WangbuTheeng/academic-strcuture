@extends('layouts.admin')

@section('title', 'Edit Class - ' . $class->name)
@section('page-title', 'Edit Class')

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Class: {{ $class->name }}</h1>
            <p class="mb-0 text-muted">Update class information and settings</p>
        </div>
        <div>
            <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Details
            </a>
            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Classes
            </a>
        </div>
    </div>

    <!-- Edit Class Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Class Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.classes.update', $class) }}">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Class Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $class->name) }}" 
                                       placeholder="e.g., Class 10, BCA 1st Year"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Class Code <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code', $class->code) }}" 
                                       placeholder="e.g., C10, BCA1"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="level_id" class="form-label">Educational Level <span class="text-danger">*</span></label>
                                <select class="form-select @error('level_id') is-invalid @enderror" 
                                        id="level_id" 
                                        name="level_id" 
                                        required>
                                    <option value="">Select Level</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level->id }}" {{ old('level_id', $class->level_id) == $level->id ? 'selected' : '' }}>
                                            {{ $level->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('level_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-select @error('department_id') is-invalid @enderror" 
                                        id="department_id" 
                                        name="department_id" 
                                        required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id', $class->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }} ({{ $department->faculty->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', $class->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Status
                                    </label>
                                </div>
                                <small class="text-muted">Check to make this class active and available for enrollment</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Class
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Current Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Current Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-bold">Current Name:</td>
                            <td>{{ $class->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Current Code:</td>
                            <td><span class="badge bg-primary">{{ $class->code }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Current Level:</td>
                            <td>{{ $class->level->name ?? 'No Level Assigned' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Current Department:</td>
                            <td>{{ $class->department->name ?? 'Not Assigned' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Current Status:</td>
                            <td>
                                @if($class->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Warning Card -->
            @if($class->enrollments->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-exclamation-triangle"></i> Important Notice
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">
                        This class has <strong>{{ $class->enrollments->count() }} enrolled students</strong>. 
                        Changes to the level or department may affect:
                    </p>
                    <ul class="small mb-0">
                        <li>Student enrollment records</li>
                        <li>Academic program associations</li>
                        <li>Subject assignments</li>
                        <li>Examination schedules</li>
                    </ul>
                    <div class="alert alert-warning mt-3 mb-0">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            Please ensure all changes are coordinated with academic staff.
                        </small>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Stats -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Class Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-end">
                                <div class="h4 mb-0 text-primary">{{ $class->enrollments->count() }}</div>
                                <small class="text-muted">Students</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 mb-0 text-success">{{ $class->programs->count() }}</div>
                            <small class="text-muted">Programs</small>
                        </div>
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h4 mb-0 text-info">{{ $class->enrollments->where('status', 'active')->count() }}</div>
                                <small class="text-muted">Active</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0 text-warning">{{ $class->enrollments->where('status', '!=', 'active')->count() }}</div>
                            <small class="text-muted">Inactive</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
