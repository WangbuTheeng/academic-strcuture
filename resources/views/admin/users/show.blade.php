@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">User Details</h1>
            <p class="mb-0 text-muted">View user information and permissions</p>
        </div>
        <div>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary me-2">
                <i class="fas fa-edit"></i> Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>

    <div class="row">
        <!-- User Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    <div class="mb-3">
                        @foreach($user->roles as $role)
                            <span class="badge 
                                @if($role->name == 'admin') bg-danger
                                @elseif($role->name == 'principal') bg-purple
                                @elseif($role->name == 'teacher') bg-primary
                                @elseif($role->name == 'student') bg-success
                                @else bg-secondary
                                @endif me-1">
                                {{ ucfirst($role->name) }}
                            </span>
                        @endforeach
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h5 text-primary">{{ $user->created_at->format('M d, Y') }}</div>
                            <div class="text-muted small">Joined</div>
                        </div>
                        <div class="col-6">
                            <div class="h5 text-success">{{ $user->updated_at->format('M d, Y') }}</div>
                            <div class="text-muted small">Last Updated</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Details -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Full Name</label>
                            <div class="fw-bold">{{ $user->name }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Email Address</label>
                            <div class="fw-bold">{{ $user->email }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Email Verified</label>
                            <div>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> Verified
                                    </span>
                                    <small class="text-muted d-block">{{ $user->email_verified_at->format('M d, Y H:i') }}</small>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Account Status</label>
                            <div>
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Active
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles and Permissions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Roles and Permissions</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label text-muted">Assigned Roles</label>
                        <div>
                            @forelse($user->roles as $role)
                                <span class="badge 
                                    @if($role->name == 'admin') bg-danger
                                    @elseif($role->name == 'principal') bg-purple
                                    @elseif($role->name == 'teacher') bg-primary
                                    @elseif($role->name == 'student') bg-success
                                    @else bg-secondary
                                    @endif me-2 mb-2 p-2">
                                    <i class="fas fa-user-tag me-1"></i>
                                    {{ ucfirst($role->name) }}
                                </span>
                            @empty
                                <span class="text-muted">No roles assigned</span>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <label class="form-label text-muted">Permissions</label>
                        <div class="row">
                            @forelse($user->getAllPermissions()->chunk(ceil($user->getAllPermissions()->count() / 3)) as $permissionChunk)
                                <div class="col-md-4">
                                    @foreach($permissionChunk as $permission)
                                        <div class="mb-1">
                                            <small class="badge bg-light text-dark">
                                                <i class="fas fa-key me-1"></i>
                                                {{ str_replace('-', ' ', ucfirst($permission->name)) }}
                                            </small>
                                        </div>
                                    @endforeach
                                </div>
                            @empty
                                <div class="col-12">
                                    <span class="text-muted">No permissions assigned</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teacher Assignments (if user is a teacher) -->
            @if($user->hasRole('teacher'))
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Teacher Assignments</h6>
                    </div>
                    <div class="card-body">
                        @if($user->teacherSubjects && $user->teacherSubjects->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Class</th>
                                            <th>Academic Year</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->teacherSubjects as $assignment)
                                            <tr>
                                                <td>{{ $assignment->subject->name }}</td>
                                                <td>{{ $assignment->class->name }}</td>
                                                <td>{{ $assignment->academicYear->name }}</td>
                                                <td>
                                                    @if($assignment->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">No subject assignments found.</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Activity Log -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Account Activity</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Created</label>
                            <div class="fw-bold">{{ $user->created_at->format('M d, Y H:i') }}</div>
                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Last Updated</label>
                            <div class="fw-bold">{{ $user->updated_at->format('M d, Y H:i') }}</div>
                            <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
