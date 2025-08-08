<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

echo "=== ADDING TEACHER MANAGEMENT PERMISSIONS ===\n";

// Create new teacher management permissions
$permissions = [
    'manage-teachers',
    'assign-teachers', 
    'view-teacher-assignments',
    'edit-teacher-assignments',
];

foreach ($permissions as $permissionName) {
    $permission = Permission::firstOrCreate(['name' => $permissionName]);
    echo "✅ Permission created/found: {$permissionName}\n";
}

// Assign permissions to roles
$adminRole = Role::where('name', 'admin')->first();
$principalRole = Role::where('name', 'principal')->first();

if ($adminRole) {
    foreach ($permissions as $permissionName) {
        if (!$adminRole->hasPermissionTo($permissionName)) {
            $adminRole->givePermissionTo($permissionName);
            echo "✅ Admin role given permission: {$permissionName}\n";
        } else {
            echo "ℹ️  Admin already has permission: {$permissionName}\n";
        }
    }
} else {
    echo "❌ Admin role not found\n";
}

if ($principalRole) {
    foreach ($permissions as $permissionName) {
        if (!$principalRole->hasPermissionTo($permissionName)) {
            $principalRole->givePermissionTo($permissionName);
            echo "✅ Principal role given permission: {$permissionName}\n";
        } else {
            echo "ℹ️  Principal already has permission: {$permissionName}\n";
        }
    }
} else {
    echo "❌ Principal role not found\n";
}

echo "\n=== PERMISSION SUMMARY ===\n";
echo "Total permissions created: " . count($permissions) . "\n";
echo "Admin permissions: " . ($adminRole ? $adminRole->permissions->count() : 0) . "\n";
echo "Principal permissions: " . ($principalRole ? $principalRole->permissions->count() : 0) . "\n";

echo "\n🎉 Teacher management permissions added successfully!\n";
?>
