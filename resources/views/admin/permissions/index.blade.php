@extends('layouts.admin')

@section('title', 'Permission Management')
@section('page-title', 'Permission Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Permission Management</h1>
            <p class="mb-0 text-muted">Manage roles and permissions for the system</p>
        </div>
        <div>
            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                <i class="fas fa-plus"></i> Create Permission
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkAssignModal">
                <i class="fas fa-users"></i> Bulk Assign
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Role Permission Matrix -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Role Permission Matrix</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($roles as $role)
                    <div class="col-lg-6 mb-4">
                        <div class="card border-left-{{ $role->name == 'admin' ? 'danger' : ($role->name == 'principal' ? 'warning' : ($role->name == 'teacher' ? 'primary' : 'success')) }}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <span class="badge 
                                        @if($role->name == 'admin') bg-danger
                                        @elseif($role->name == 'principal') bg-warning
                                        @elseif($role->name == 'teacher') bg-primary
                                        @else bg-success
                                        @endif">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                    <span class="text-muted">({{ $role->permissions->count() }} permissions)</span>
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="editRolePermissions('{{ $role->id }}', '{{ $role->name }}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                            <div class="card-body">
                                @if($role->permissions->count() > 0)
                                    @foreach($permissions as $category => $categoryPermissions)
                                        @php
                                            $roleHasCategory = $role->permissions->whereIn('name', $categoryPermissions->pluck('name'))->count() > 0;
                                        @endphp
                                        @if($roleHasCategory)
                                            <div class="mb-3">
                                                <h6 class="text-primary">{{ ucfirst($category) }}</h6>
                                                <div class="row">
                                                    @foreach($categoryPermissions as $permission)
                                                        @if($role->hasPermissionTo($permission->name))
                                                            <div class="col-md-6">
                                                                <small class="badge bg-light text-dark mb-1">
                                                                    <i class="fas fa-check text-success me-1"></i>
                                                                    {{ str_replace('-', ' ', ucfirst($permission->name)) }}
                                                                </small>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">No permissions assigned</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Permission Categories -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Available Permissions by Category</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($permissions as $category => $categoryPermissions)
                    <div class="col-lg-4 mb-4">
                        <div class="card border-left-info">
                            <div class="card-header">
                                <h6 class="mb-0 text-info">
                                    <i class="fas fa-key me-2"></i>
                                    {{ ucfirst($category) }} Permissions
                                </h6>
                            </div>
                            <div class="card-body">
                                @foreach($categoryPermissions as $permission)
                                    <div class="mb-2">
                                        <span class="badge bg-light text-dark">
                                            {{ str_replace('-', ' ', ucfirst($permission->name)) }}
                                        </span>
                                        <div class="small text-muted">
                                            Used by: 
                                            @foreach($roles as $role)
                                                @if($role->hasPermissionTo($permission->name))
                                                    <span class="badge bg-secondary">{{ $role->name }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Edit Role Permissions Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Role Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div id="rolePermissionsContent">
                        <!-- Content will be loaded dynamically -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Permissions</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Permission Modal -->
<div class="modal fade" id="createPermissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.permissions.create') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="permission_name" class="form-label">Permission Name</label>
                        <input type="text" class="form-control" id="permission_name" name="name" 
                               placeholder="e.g., manage-custom-feature" required>
                        <div class="form-text">Use lowercase with hyphens (e.g., manage-custom-feature)</div>
                    </div>
                    <div class="mb-3">
                        <label for="permission_description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="permission_description" name="description" 
                                  placeholder="Brief description of what this permission allows"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Assign Modal -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Assign Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.permissions.bulk-assign') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        This will assign or revoke permissions directly to users, independent of their roles.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Select Users</label>
                        <select name="user_ids[]" class="form-control" multiple required>
                            @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Select Permissions</label>
                        <div class="row">
                            @foreach($permissions as $category => $categoryPermissions)
                                <div class="col-md-6">
                                    <h6 class="text-primary">{{ ucfirst($category) }}</h6>
                                    @foreach($categoryPermissions as $permission)
                                        <div class="form-check">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                                   id="bulk_{{ $permission->id }}" class="form-check-input">
                                            <label for="bulk_{{ $permission->id }}" class="form-check-label">
                                                {{ str_replace('-', ' ', ucfirst($permission->name)) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-control" required>
                            <option value="assign">Assign Permissions</option>
                            <option value="revoke">Revoke Permissions</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editRolePermissions(roleId, roleName) {
    // Set form action
    document.getElementById('editRoleForm').action = `/admin/permissions/roles/${roleId}/permissions`;
    
    // Load permissions for this role
    fetch(`/admin/permissions/roles/${roleId}/edit`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('rolePermissionsContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('editRoleModal')).show();
        });
}
</script>
@endsection
