<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display permission management interface.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('-', $permission->name)[0];
        });

        return view('admin.permissions.index', compact('roles', 'permissions'));
    }

    /**
     * Update role permissions.
     */
    public function updateRolePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permissions updated for {$role->name} role.");
    }

    /**
     * Assign specific permissions to a user.
     */
    public function assignUserPermissions(Request $request, User $user)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User permissions updated successfully.');
    }

    /**
     * Show user permission management.
     */
    public function showUserPermissions(User $user)
    {
        $allPermissions = Permission::all()->groupBy(function ($permission) {
            return explode('-', $permission->name)[0];
        });
        
        $userPermissions = $user->getAllPermissions();
        $rolePermissions = $user->getPermissionsViaRoles();
        $directPermissions = $user->getDirectPermissions();

        return view('admin.permissions.user', compact(
            'user', 'allPermissions', 'userPermissions', 'rolePermissions', 'directPermissions'
        ));
    }

    /**
     * Create a new permission.
     */
    public function createPermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'description' => 'nullable|string',
        ]);

        Permission::create(['name' => $validated['name']]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Delete a permission.
     */
    public function deletePermission(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }

    /**
     * Bulk assign permissions to multiple users.
     */
    public function bulkAssignPermissions(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
            'action' => 'required|in:assign,revoke',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();
        $permissions = $validated['permissions'];

        foreach ($users as $user) {
            if ($validated['action'] === 'assign') {
                $user->givePermissionTo($permissions);
            } else {
                $user->revokePermissionTo($permissions);
            }
        }

        $action = $validated['action'] === 'assign' ? 'assigned to' : 'revoked from';
        return redirect()->route('admin.permissions.index')
            ->with('success', "Permissions {$action} " . count($users) . " users.");
    }

    /**
     * Get permission suggestions based on role.
     */
    public function getPermissionSuggestions(Request $request)
    {
        $role = $request->get('role');
        
        $suggestions = [
            'admin' => Permission::all()->pluck('name'),
            'principal' => [
                'view-users', 'view-students', 'manage-students',
                'view-exams', 'approve-results', 'publish-results',
                'apply-grace-marks', 'view-reports', 'generate-reports',
                'view-analytics', 'manage-teachers', 'assign-teachers',
                'view-teacher-assignments', 'edit-teacher-assignments'
            ],
            'teacher' => [
                'view-students', 'view-exams', 'enter-marks', 'view-reports'
            ],
            'student' => [
                'view-reports'
            ]
        ];

        return response()->json([
            'permissions' => $suggestions[$role] ?? []
        ]);
    }
}
