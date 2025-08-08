@extends('layouts.admin')

@section('title', 'Level Details')
@section('page-title', 'Level Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $level->name }} Level</h1>
            <p class="mb-0 text-muted">Level details and associated classes</p>
        </div>
        <div>
            <a href="{{ route('admin.levels.edit', $level) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit"></i> Edit Level
            </a>
            <a href="{{ route('admin.levels.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Levels
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Level Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Level Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $level->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Order:</strong></td>
                            <td><span class="badge bg-primary">{{ $level->order }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Classes:</strong></td>
                            <td><span class="badge bg-info">{{ $level->classes->count() }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Created:</strong></td>
                            <td>{{ $level->created_at->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Updated:</strong></td>
                            <td>{{ $level->updated_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Associated Classes -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Associated Classes ({{ $level->classes->count() }})</h6>
                    <a href="{{ route('admin.classes.create', ['level_id' => $level->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Add Class
                    </a>
                </div>
                <div class="card-body">
                    @if($level->classes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Class Name</th>
                                        <th>Code</th>
                                        <th>Department</th>
                                        <th>Faculty</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($level->classes as $class)
                                        <tr>
                                            <td>
                                                <strong>{{ $class->name }}</strong>
                                            </td>
                                            <td>
                                                <code>{{ $class->code }}</code>
                                            </td>
                                            <td>
                                                @if($class->department)
                                                    {{ $class->department->name }}
                                                @else
                                                    <span class="text-muted">No department</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($class->department && $class->department->faculty)
                                                    {{ $class->department->faculty->name }}
                                                @else
                                                    <span class="text-muted">No faculty</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($class->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.classes.show', $class) }}" 
                                                       class="btn btn-sm btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.classes.edit', $class) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chalkboard fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-muted">No classes found</h5>
                            <p class="text-muted">This level doesn't have any classes yet.</p>
                            <a href="{{ route('admin.classes.create', ['level_id' => $level->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Class
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
