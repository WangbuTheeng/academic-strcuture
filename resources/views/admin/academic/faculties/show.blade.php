@extends('layouts.admin')

@section('title', 'Faculty Details - ' . $faculty->name)

@section('content')

 @include('admin.academic.partials.sub-navbar')
 
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Faculty Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.academic.index') }}">Academic Structure</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.faculties.index') }}">Faculties</a></li>
                    <li class="breadcrumb-item active">{{ $faculty->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.faculties.edit', $faculty) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Faculty
            </a>
            <a href="{{ route('admin.faculties.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Faculty Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Faculty Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Name:</strong></div>
                        <div class="col-sm-8">{{ $faculty->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Code:</strong></div>
                        <div class="col-sm-8">{{ $faculty->code }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Created:</strong></div>
                        <div class="col-sm-8">{{ $faculty->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4"><strong>Updated:</strong></div>
                        <div class="col-sm-8">{{ $faculty->updated_at->format('M d, Y') }}</div>
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
                            <div class="h4 mb-0 text-primary">{{ $faculty->departments->count() }}</div>
                            <div class="small text-muted">Departments</div>
                        </div>
                        <div class="col-4">
                            <div class="h4 mb-0 text-success">{{ $faculty->programs->count() }}</div>
                            <div class="small text-muted">Programs</div>
                        </div>
                        <div class="col-4">
                            <div class="h4 mb-0 text-info">{{ $faculty->classes->count() }}</div>
                            <div class="small text-muted">Classes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Departments -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Departments</h6>
                    <a href="{{ route('admin.departments.create', ['faculty' => $faculty->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Add Department
                    </a>
                </div>
                <div class="card-body">
                    @if($faculty->departments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Programs</th>
                                        <th>Classes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($faculty->departments as $department)
                                    <tr>
                                        <td>{{ $department->name }}</td>
                                        <td>{{ $department->code }}</td>
                                        <td>{{ $department->programs->count() }}</td>
                                        <td>{{ $department->classes->count() }}</td>
                                        <td>
                                            <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No departments found for this faculty.</p>
                            <a href="{{ route('admin.departments.create', ['faculty' => $faculty->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Department
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Programs -->
            @if($faculty->programs->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Programs</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Department</th>
                                    <th>Duration</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($faculty->programs as $program)
                                <tr>
                                    <td>{{ $program->name }}</td>
                                    <td>{{ $program->code }}</td>
                                    <td>{{ $program->department->name }}</td>
                                    <td>{{ $program->duration_years }} years</td>
                                    <td>
                                        <span class="badge badge-{{ $program->program_type === 'semester' ? 'primary' : 'secondary' }}">
                                            {{ ucfirst($program->program_type) }}
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
        </div>
    </div>
</div>
@endsection
