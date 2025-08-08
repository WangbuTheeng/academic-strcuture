<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user only if it doesn't exist
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // Call essential seeders only
        $this->call([
            RolePermissionSeeder::class,
            SuperAdminRoleSeeder::class, // Add super-admin role and user
            AcademicStructureSeeder::class,
            SubjectsSeeder::class,
            GradingScalesSeeder::class,
            SampleStudentsSeeder::class,
            // TeachersSeeder::class, // Skip for now due to role issues
            // ExamsSeeder::class, // Skip for now
            // SetupWizardSeeder::class, // Skip for now
            // MarkEntrySeeder::class, // Skip for now
        ]);

        // Create admin user
        $this->call(\Database\Seeders\CreateAdminSeeder::class);
    }
}
