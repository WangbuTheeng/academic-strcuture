<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'manage-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-users',

            // Student Management
            'manage-students',
            'create-students',
            'edit-students',
            'delete-students',
            'view-students',
            'manage-enrollments',

            // Academic Management
            'manage-academic-structure',
            'manage-academic-years',
            'manage-classes',
            'manage-subjects',
            'manage-programs',

            // Teacher Management
            'manage-teachers',
            'assign-teachers',
            'view-teacher-assignments',
            'edit-teacher-assignments',

            // Examination System
            'manage-exams',
            'create-exams',
            'edit-exams',
            'delete-exams',
            'view-exams',
            'enter-marks',
            'approve-results',
            'publish-results',
            'apply-grace-marks',

            // Reports & Analytics
            'view-reports',
            'generate-reports',
            'view-analytics',

            // System Administration
            'manage-system',
            'manage-backups',
            'view-audit-logs',
            'manage-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $principalRole = Role::create(['name' => 'principal']);
        $teacherRole = Role::create(['name' => 'teacher']);
        $studentRole = Role::create(['name' => 'student']);

        // Admin gets all permissions
        $adminRole->givePermissionTo(Permission::all());

        // Principal permissions
        $principalRole->givePermissionTo([
            'view-users',
            'view-students',
            'manage-students',
            'manage-teachers',
            'assign-teachers',
            'view-teacher-assignments',
            'edit-teacher-assignments',
            'view-exams',
            'approve-results',
            'publish-results',
            'apply-grace-marks',
            'view-reports',
            'generate-reports',
            'view-analytics',
        ]);

        // Teacher permissions
        $teacherRole->givePermissionTo([
            'view-students',
            'view-exams',
            'enter-marks',
            'view-reports',
        ]);

        // Student permissions
        $studentRole->givePermissionTo([
            'view-reports', // Only their own reports
        ]);

        // Create default admin user
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@academic.local',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create demo principal user
        $principal = User::create([
            'name' => 'Demo Principal',
            'email' => 'principal@academic.local',
            'password' => Hash::make('principal123'),
            'email_verified_at' => now(),
        ]);
        $principal->assignRole('principal');

        // Create demo teacher user
        $teacher = User::create([
            'name' => 'Demo Teacher',
            'email' => 'teacher@academic.local',
            'password' => Hash::make('teacher123'),
            'email_verified_at' => now(),
        ]);
        $teacher->assignRole('teacher');

        // Create demo student user
        $student = User::create([
            'name' => 'Demo Student',
            'email' => 'student@academic.local',
            'password' => Hash::make('student123'),
            'email_verified_at' => now(),
        ]);
        $student->assignRole('student');

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Demo users created:');
        $this->command->info('Admin: admin@academic.local / admin123');
        $this->command->info('Principal: principal@academic.local / principal123');
        $this->command->info('Teacher: teacher@academic.local / teacher123');
        $this->command->info('Student: student@academic.local / student123');
    }
}
