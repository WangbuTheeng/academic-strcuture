@extends('layouts.admin')

@section('title', 'Subject Details - ' . $subject->name)

@section('content')
<div class="container-fluid">
    <!-- Sub-Navigation -->
    @include('admin.academic.partials.sub-navbar')

    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Subject Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.academic.index') }}">Academic Structure</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
                    <li class="breadcrumb-item active">{{ $subject->name }}</li>
                </ol>
            </nav>
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
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subject Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Name:</strong></div>
                        <div class="col-sm-8">{{ $subject->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Code:</strong></div>
                        <div class="col-sm-8">{{ $subject->code }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Department:</strong></div>
                        <div class="col-sm-8">
                            @if($subject->department)
                                <a href="{{ route('admin.departments.show', $subject->department) }}" class="text-primary">
                                    {{ $subject->department->name }}
                                </a>
                            @else
                                <span class="text-muted">Not Assigned</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Type:</strong></div>
                        <div class="col-sm-8">
                            <span class="badge badge-info">{{ ucfirst($subject->subject_type) }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Credit Hours:</strong></div>
                        <div class="col-sm-8">{{ $subject->credit_hours }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Status:</strong></div>
                        <div class="col-sm-8">
                            @if($subject->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Structure -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Assessment Structure</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6"><strong>Theory Marks:</strong></div>
                        <div class="col-sm-6">{{ $subject->max_theory ?? 0 }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-6"><strong>Practical Marks:</strong></div>
                        <div class="col-sm-6">{{ $subject->max_practical ?? 0 }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-6"><strong>Assessment Marks:</strong></div>
                        <div class="col-sm-6">{{ $subject->max_assess ?? 0 }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-6"><strong>Total Marks:</strong></div>
                        <div class="col-sm-6">
                            <strong>{{ ($subject->max_theory ?? 0) + ($subject->max_practical ?? 0) + ($subject->max_assess ?? 0) }}</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-sm-6"><strong>Has Practical:</strong></div>
                        <div class="col-sm-6">
                            @if($subject->is_practical)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-6"><strong>Internal Assessment:</strong></div>
                        <div class="col-sm-6">
                            @if($subject->has_internal)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Associated Programs -->
        <div class="col-lg-8">
            @if($subject->programs && $subject->programs->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Associated Programs ({{ $subject->programs->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Program Name</th>
                                    <th>Duration</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subject->programs as $program)
                                <tr>
                                    <td>{{ $program->name }}</td>
                                    <td>{{ $program->duration_years }} years</td>
                                    <td>{{ ucfirst($program->degree_type) }}</td>
                                    <td>
                                        @if($program->pivot->is_compulsory)
                                            <span class="badge badge-primary">Compulsory</span>
                                        @else
                                            <span class="badge badge-secondary">Elective</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
                <div class="card-body text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No associations found for this subject</h5>
                    <p class="text-muted">This subject is not yet associated with any programs or assigned to teachers.</p>
                    <div class="mt-3">
                        <a href="{{ route('admin.programs.index') }}" class="btn btn-primary me-2">
                            <i class="fas fa-graduation-cap"></i> Manage Programs
                        </a>
                        <a href="{{ route('admin.teacher-subjects.index') }}" class="btn btn-success">
                            <i class="fas fa-chalkboard-teacher"></i> Assign Teachers
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
