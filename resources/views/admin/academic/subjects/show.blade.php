@extends('layouts.admin')

@section('title', 'Subject Details - ' . $subject->name)
@section('page-title', 'Subject Details')

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $subject->name }}</h1>
            <p class="mb-0 text-muted">Subject Code: {{ $subject->code }}</p>
        </div>
        <div>
            <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Subject
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Subjects
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Subject Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-book me-2"></i>Subject Information
                    </h6>
                    <span class="badge bg-{{ $subject->is_active ? 'success' : 'danger' }} fs-6">
                        {{ $subject->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="card-body">
                    <!-- Basic Information Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-primary h-100">
                                <div class="card-body py-3">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Subject Code</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subject->code }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-success h-100">
                                <div class="card-body py-3">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Credit Hours</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subject->credit_hours }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-info h-100">
                                <div class="card-body py-3">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Subject Type</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ ucfirst($subject->subject_type) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-warning h-100">
                                <div class="card-body py-3">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Department</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $subject->department->name ?? 'Not Assigned' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-secondary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Basic Details
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="fw-bold text-muted" style="width: 40%;">Subject Name:</td>
                                        <td>{{ $subject->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Subject Code:</td>
                                        <td><span class="badge bg-primary">{{ $subject->code }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Department:</td>
                                        <td>{{ $subject->department->name ?? 'Not Assigned' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Credit Hours:</td>
                                        <td><span class="badge bg-success">{{ $subject->credit_hours }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-secondary mb-3">
                                <i class="fas fa-cogs me-2"></i>Configuration
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="fw-bold text-muted" style="width: 40%;">Subject Type:</td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($subject->subject_type) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Status:</td>
                                        <td>
                                            <span class="badge bg-{{ $subject->is_active ? 'success' : 'danger' }}">
                                                <i class="fas fa-{{ $subject->is_active ? 'check' : 'times' }} me-1"></i>
                                                {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Has Practical:</td>
                                        <td>
                                            <span class="badge bg-{{ $subject->is_practical ? 'success' : 'secondary' }}">
                                                <i class="fas fa-{{ $subject->is_practical ? 'check' : 'times' }} me-1"></i>
                                                {{ $subject->is_practical ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-muted">Has Internal:</td>
                                        <td>
                                            <span class="badge bg-{{ $subject->has_internal ? 'success' : 'secondary' }}">
                                                <i class="fas fa-{{ $subject->has_internal ? 'check' : 'times' }} me-1"></i>
                                                {{ $subject->has_internal ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($subject->max_theory || $subject->max_practical || $subject->max_assess)
                    <hr class="my-4">
                    <h6 class="font-weight-bold text-secondary mb-3">
                        <i class="fas fa-chart-bar me-2"></i>Assessment Structure
                    </h6>
                    <div class="row">
                        @if($subject->max_theory)
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-primary h-100">
                                <div class="card-body text-center py-4">
                                    <div class="h3 text-primary mb-2">
                                        <i class="fas fa-book-open me-2"></i>{{ $subject->max_theory }}
                                    </div>
                                    <div class="text-xs font-weight-bold text-primary text-uppercase">Theory Marks</div>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($subject->max_practical)
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-success h-100">
                                <div class="card-body text-center py-4">
                                    <div class="h3 text-success mb-2">
                                        <i class="fas fa-flask me-2"></i>{{ $subject->max_practical }}
                                    </div>
                                    <div class="text-xs font-weight-bold text-success text-uppercase">Practical Marks</div>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($subject->max_assess)
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-info h-100">
                                <div class="card-body text-center py-4">
                                    <div class="h3 text-info mb-2">
                                        <i class="fas fa-clipboard-check me-2"></i>{{ $subject->max_assess }}
                                    </div>
                                    <div class="text-xs font-weight-bold text-info text-uppercase">Assessment Marks</div>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Programs List -->
            @if($subject->programs && $subject->programs->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Associated Programs ({{ $subject->programs->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($subject->programs as $program)
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-primary">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $program->name }}</div>
                                            <small class="text-muted">
                                                {{ $program->duration_years }} years • {{ ucfirst($program->degree_type) }}
                                                @if($program->pivot->is_compulsory)
                                                    <span class="badge bg-primary ms-1">Compulsory</span>
                                                @else
                                                    <span class="badge bg-secondary ms-1">Elective</span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Teacher Assignments -->
            @if($subject->teacherSubjects && $subject->teacherSubjects->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Teacher Assignments ({{ $subject->teacherSubjects->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Teacher</th>
                                    <th>Class</th>
                                    <th>Academic Year</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subject->teacherSubjects as $assignment)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $assignment->user->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $assignment->user->email ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $assignment->class->name ?? 'N/A' }}</td>
                                    <td>{{ $assignment->academicYear->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Statistics Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-chart-pie me-2"></i>Subject Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="card border-left-primary">
                                <div class="card-body py-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Programs</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subject->programs->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="card border-left-success">
                                <div class="card-body py-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Teachers</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subject->teacherSubjects->count() }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($subject->max_theory || $subject->max_practical || $subject->max_assess)
                        <div class="col-12">
                            <div class="card border-left-info">
                                <div class="card-body py-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Marks</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ ($subject->max_theory ?? 0) + ($subject->max_practical ?? 0) + ($subject->max_assess ?? 0) }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit Subject
                        </a>
                        @if($subject->is_active)
                            <button class="btn btn-warning btn-sm" onclick="toggleStatus(false)">
                                <i class="fas fa-pause"></i> Deactivate Subject
                            </button>
                        @else
                            <button class="btn btn-success btn-sm" onclick="toggleStatus(true)">
                                <i class="fas fa-play"></i> Activate Subject
                            </button>
                        @endif
                        <button class="btn btn-danger btn-sm" onclick="deleteSubject()">
                            <i class="fas fa-trash"></i> Delete Subject
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleStatus(activate) {
        if (confirm(`Are you sure you want to ${activate ? 'activate' : 'deactivate'} this subject?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.subjects.update", $subject) }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PATCH';
            
            const statusField = document.createElement('input');
            statusField.type = 'hidden';
            statusField.name = 'is_active';
            statusField.value = activate ? '1' : '0';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            form.appendChild(statusField);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    function deleteSubject() {
        if (confirm('Are you sure you want to delete this subject? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.subjects.destroy", $subject) }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
