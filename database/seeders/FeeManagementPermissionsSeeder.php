<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FeeManagementPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create fee management permissions
        $permissions = [
            'manage-fees' => 'Manage Fee Structures and Billing',
            'view-fees' => 'View Fee Information',
            'create-bills' => 'Create Student Bills',
            'edit-bills' => 'Edit Student Bills',
            'delete-bills' => 'Delete Student Bills',
            'process-payments' => 'Process Payments',
            'verify-payments' => 'Verify Payments',
            'generate-receipts' => 'Generate Payment Receipts',
            'view-fee-reports' => 'View Fee Reports',
            'manage-fee-structures' => 'Manage Fee Structures',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        // Assign permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo(array_keys($permissions));
        }

        // Create accountant role if it doesn't exist
        $accountantRole = Role::firstOrCreate(['name' => 'accountant']);
        
        // Assign fee-related permissions to accountant role
        $accountantPermissions = [
            'view-fees',
            'create-bills',
            'edit-bills',
            'process-payments',
            'verify-payments',
            'generate-receipts',
            'view-fee-reports',
        ];
        
        $accountantRole->givePermissionTo($accountantPermissions);

        // Create cashier role if it doesn't exist
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);
        
        // Assign limited fee permissions to cashier role
        $cashierPermissions = [
            'view-fees',
            'process-payments',
            'generate-receipts',
        ];
        
        $cashierRole->givePermissionTo($cashierPermissions);

        $this->command->info('Fee management permissions created and assigned successfully!');
    }
}
