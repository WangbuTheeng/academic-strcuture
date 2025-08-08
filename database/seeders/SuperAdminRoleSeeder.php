<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super-admin permissions
        $superAdminPermissions = [
            'manage-schools',
            'create-schools',
            'edit-schools',
            'delete-schools',
            'view-all-schools',
            'manage-school-credentials',
            'view-global-analytics',
            'manage-system-settings',
            'view-system-logs',
            'manage-super-admin-users',
        ];

        foreach ($superAdminPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create super-admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Assign all permissions to super-admin
        $superAdminRole->givePermissionTo(Permission::all());

        // Create default super-admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@system.local'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('superadmin123'),
                'school_id' => null, // Super-admin doesn't belong to any school
                'email_verified_at' => now(),
            ]
        );

        // Assign super-admin role
        if (!$superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole('super-admin');
        }

        $this->command->info('Super-admin role and user created successfully!');
        $this->command->info('Super-admin login: superadmin@system.local / superadmin123');
    }
}
