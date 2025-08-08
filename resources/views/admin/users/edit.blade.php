@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit User</h1>
            <p class="mb-0 text-muted">Update user information and roles</p>
        </div>
        <div>
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info me-2">
                <i class="fas fa-eye"></i> View User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>

    <!-- Edit User Form -->
    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Password</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Leave password fields empty to keep the current password.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
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
                        <div class="mb-3">
                            <label class="form-label">User Roles <span class="text-danger">*</span></label>
                            <div class="row">
                                @foreach($roles as $role)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                                   id="role_{{ $role->id }}" class="form-check-input"
                                                   {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                            <label for="role_{{ $role->id }}" class="form-check-label">
                                                <span class="fw-bold">{{ ucfirst($role->name) }}</span>
                                                <br>
                                                <small class="text-muted">
                                                    @if($role->name == 'admin')
                                                        Full system access and management
                                                    @elseif($role->name == 'principal')
                                                        Academic oversight and result approval
                                                    @elseif($role->name == 'teacher')
                                                        Mark entry and class management
                                                    @elseif($role->name == 'student')
                                                        View own results and information
                                                    @endif
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Permission Preview -->
                        <div class="mt-4">
                            <label class="form-label text-muted">Current Permissions</label>
                            <div class="border rounded p-3 bg-light">
                                @if($user->getAllPermissions()->count() > 0)
                                    <div class="row">
                                        @foreach($user->getAllPermissions()->chunk(ceil($user->getAllPermissions()->count() / 3)) as $permissionChunk)
                                            <div class="col-md-4">
                                                @foreach($permissionChunk as $permission)
                                                    <small class="badge bg-secondary me-1 mb-1">
                                                        {{ str_replace('-', ' ', ucfirst($permission->name)) }}
                                                    </small>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <small class="text-muted">No permissions assigned</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update User
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- User Preview -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Preview</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 60px; height: 60px; font-size: 1.5rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <h6 class="mb-1">{{ $user->name }}</h6>
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
                            <div class="h6 text-primary">{{ $user->created_at->format('M d, Y') }}</div>
                            <div class="text-muted small">Joined</div>
                        </div>
                        <div class="col-6">
                            <div class="h6 text-success">{{ $user->updated_at->format('M d, Y') }}</div>
                            <div class="text-muted small">Updated</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($user->hasRole('teacher'))
                            <a href="{{ route('admin.teacher-subjects.index', ['teacher_id' => $user->id]) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-chalkboard-teacher"></i> View Assignments
                            </a>
                        @endif
                        
                        <button type="button" class="btn btn-outline-warning btn-sm" 
                                onclick="alert('Password reset functionality coming soon!')">
                            <i class="fas fa-key"></i> Reset Password
                        </button>
                        
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                  onsubmit="return confirm('Are you sure you want to delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash"></i> Delete User
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
