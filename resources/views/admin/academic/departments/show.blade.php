@extends('layouts.admin')

@section('title', 'Department Details - ' . $department->name)

@section('content')

 @include('admin.academic.partials.sub-navbar')
 
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Department Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.academic.index') }}">Academic Structure</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}">Departments</a></li>
                    <li class="breadcrumb-item active">{{ $department->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Department
            </a>
            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Department Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Department Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Name:</strong></div>
                        <div class="col-sm-8">{{ $department->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Code:</strong></div>
                        <div class="col-sm-8">{{ $department->code }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Faculty:</strong></div>
                        <div class="col-sm-8">
                            <a href="{{ route('admin.faculties.show', $department->faculty) }}" class="text-primary">
                                {{ $department->faculty->name }}
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Created:</strong></div>
                        <div class="col-sm-8">{{ $department->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Updated:</strong></div>
                        <div class="col-sm-8">{{ $department->updated_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h4 mb-0 text-primary">{{ $department->programs->count() }}</div>
                            <div class="small text-muted">Programs</div>
                        </div>
                        <div class="col-4">
                            <div class="h4 mb-0 text-success">{{ $department->classes->count() }}</div>
                            <div class="small text-muted">Classes</div>
                        </div>
                        <div class="col-4">
                            <div class="h4 mb-0 text-info">{{ $department->subjects->count() }}</div>
                            <div class="small text-muted">Subjects</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Programs -->
        <div class="col-lg-8">
            @if($department->programs->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Programs</h6>
                    <a href="{{ route('admin.programs.create', ['department' => $department->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Add Program
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Level</th>
                                    <th>Duration</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->programs as $program)
                                <tr>
                                    <td>{{ $program->name }}</td>
                                    <td>{{ $program->code }}</td>
                                    <td>{{ $program->level->name ?? 'N/A' }}</td>
                                    <td>{{ $program->duration_years }} years</td>
                                    <td>
                                        <span class="badge badge-{{ $program->program_type === 'semester' ? 'primary' : 'secondary' }}">
                                            {{ ucfirst($program->program_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $program->is_active ? 'success' : 'danger' }}">
                                            {{ $program->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Classes -->
            @if($department->classes->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Classes</h6>
                    <a href="{{ route('admin.classes.create', ['department' => $department->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Add Class
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Level</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->classes as $class)
                                <tr>
                                    <td>{{ $class->name }}</td>
                                    <td>{{ $class->code }}</td>
                                    <td>{{ $class->level->name ?? 'N/A' }}</td>
                                    <td>{{ $class->enrollments->count() }}</td>
                                    <td>
                                        <span class="badge badge-{{ $class->is_active ? 'success' : 'danger' }}">
                                            {{ $class->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Subjects -->
            @if($department->subjects->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Subjects</h6>
                    <a href="{{ route('admin.subjects.create', ['department' => $department->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Add Subject
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Credit Hours</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->subjects as $subject)
                                <tr>
                                    <td>{{ $subject->name }}</td>
                                    <td>{{ $subject->code }}</td>
                                    <td>
                                        <span class="badge badge-{{ $subject->subject_type === 'core' ? 'primary' : 'secondary' }}">
                                            {{ ucfirst($subject->subject_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $subject->credit_hours }}</td>
                                    <td>
                                        <span class="badge badge-{{ $subject->is_active ? 'success' : 'danger' }}">
                                            {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Empty State -->
            @if($department->programs->count() == 0 && $department->classes->count() == 0 && $department->subjects->count() == 0)
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No content found for this department</h5>
                    <p class="text-muted">Start by adding programs, classes, or subjects to this department.</p>
                    <div class="mt-3">
                        <a href="{{ route('admin.programs.create', ['department' => $department->id]) }}" class="btn btn-primary me-2">
                            <i class="fas fa-plus"></i> Add Program
                        </a>
                        <a href="{{ route('admin.classes.create', ['department' => $department->id]) }}" class="btn btn-success me-2">
                            <i class="fas fa-plus"></i> Add Class
                        </a>
                        <a href="{{ route('admin.subjects.create', ['department' => $department->id]) }}" class="btn btn-info">
                            <i class="fas fa-plus"></i> Add Subject
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
