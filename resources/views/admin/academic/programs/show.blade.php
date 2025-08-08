@extends('layouts.admin')

@section('title', 'Program Details')

@section('content')
<div class="container-fluid">
     <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-dark">{{ $program->name }}</h1>
            <p class="mb-0 text-muted">Program details and information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.programs.manage-structure', $program) }}" class="btn btn-primary">
                <i class="fas fa-cogs me-1"></i>
                Manage Structure
            </a>
            <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>
                Edit Program
            </a>
            <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Programs
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Program Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Program Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Program Name</label>
                            <p class="text-dark">{{ $program->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Department</label>
                            <p class="text-dark">{{ $program->department->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Faculty</label>
                            <p class="text-dark">{{ $program->department->faculty->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Level</label>
                            <p><span class="badge bg-info">{{ $program->level->name ?? 'N/A' }}</span></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Duration</label>
                            <p class="text-dark">{{ $program->duration_years }} years</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Degree Type</label>
                            <p><span class="badge bg-primary">{{ ucfirst($program->degree_type) }}</span></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Created</label>
                            <p class="text-dark">{{ $program->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <p class="text-dark">{{ $program->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subjects -->
            @if($program->subjects->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Program Subjects</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Subject Name</th>
                                    <th>Code</th>
                                    <th>Credit Hours</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($program->subjects as $subject)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $subject->name }}</td>
                                        <td><span class="badge bg-secondary">{{ $subject->code }}</span></td>
                                        <td>{{ $subject->credit_hours }}</td>
                                        <td><span class="badge bg-info">{{ ucfirst($subject->subject_type) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Associated Classes -->
            @if($program->classes && $program->classes->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Associated Classes ({{ $program->classes->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($program->classes as $class)
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-success">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-users fa-2x text-success"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">
                                                <a href="{{ route('admin.classes.show', $class) }}" class="text-decoration-none">
                                                    {{ $class->name }}
                                                </a>
                                            </div>
                                            <small class="text-muted">
                                                {{ $class->level->name ?? 'No Level' }} • {{ $class->code }}
                                                @if($class->pivot->year_no)
                                                    • Year {{ $class->pivot->year_no }}
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
            @else
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">Associated Classes</h6>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No classes assigned to this program yet.</p>
                    <a href="{{ route('admin.programs.manage-structure', $program) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Manage Program Structure
                    </a>
                </div>
            </div>
            @endif

            <!-- Program Subjects -->
            @if($program->subjects && $program->subjects->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Program Subjects ({{ $program->subjects->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Subject Name</th>
                                    <th>Code</th>
                                    <th>Credit Hours</th>
                                    <th>Type</th>
                                    <th>Year/Semester</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($program->subjects as $subject)
                                <tr>
                                    <td>
                                        <div class="fw-bold">
                                            <a href="{{ route('admin.subjects.show', $subject) }}" class="text-decoration-none">
                                                {{ $subject->name }}
                                            </a>
                                        </div>
                                        <small class="text-muted">{{ $subject->department->name ?? 'No Department' }}</small>
                                    </td>
                                    <td><span class="badge bg-info">{{ $subject->code }}</span></td>
                                    <td>{{ $subject->pivot->credit_hours ?? $subject->credit_hours }}</td>
                                    <td>
                                        <span class="badge bg-{{ $subject->pivot->is_compulsory ? 'primary' : 'secondary' }}">
                                            {{ $subject->pivot->is_compulsory ? 'Compulsory' : 'Elective' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($subject->pivot->year_no)
                                            <span class="badge bg-success">Year {{ $subject->pivot->year_no }}</span>
                                        @endif
                                        @if($subject->pivot->semester_id)
                                            <span class="badge bg-warning">Semester</span>
                                        @endif
                                        @if(!$subject->pivot->year_no && !$subject->pivot->semester_id)
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">Program Subjects</h6>
                </div>
                <div class="card-body text-center py-4">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No subjects assigned to this program yet.</p>
                    <a href="{{ route('admin.programs.manage-structure', $program) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Subjects
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Statistics Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4 mb-3">
                            <div class="border-end">
                                <div class="h4 mb-0 font-weight-bold text-primary">{{ $program->enrollments->count() }}</div>
                                <div class="small text-muted">Enrollments</div>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="border-end">
                                <div class="h4 mb-0 font-weight-bold text-success">{{ $program->classes->count() }}</div>
                                <div class="small text-muted">Classes</div>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="h4 mb-0 font-weight-bold text-info">{{ $program->subjects->count() }}</div>
                            <div class="small text-muted">Subjects</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Enrollments -->
            @if($program->enrollments->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Enrollments</h6>
                </div>
                <div class="card-body">
                    @foreach($program->enrollments->take(5) as $enrollment)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-primary rounded-circle">
                                    {{ strtoupper(substr($enrollment->student->name ?? 'N', 0, 1)) }}
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $enrollment->student->name ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $enrollment->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($program->enrollments->count() > 5)
                        <div class="text-center">
                            <small class="text-muted">and {{ $program->enrollments->count() - 5 }} more...</small>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.25rem;
}

.avatar-sm {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.text-dark {
    color: #212529 !important;
}
</style>
@endpush
@endsection
