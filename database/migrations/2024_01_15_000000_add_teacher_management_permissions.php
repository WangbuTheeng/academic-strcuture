<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create new teacher management permissions
        $permissions = [
            'manage-teachers',
            'assign-teachers', 
            'view-teacher-assignments',
            'edit-teacher-assignments',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole = Role::where('name', 'admin')->first();
        $principalRole = Role::where('name', 'principal')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        if ($principalRole) {
            $principalRole->givePermissionTo($permissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'manage-teachers',
            'assign-teachers',
            'view-teacher-assignments', 
            'edit-teacher-assignments',
        ];

        foreach ($permissions as $permission) {
            Permission::where('name', $permission)->delete();
        }
    }
};
