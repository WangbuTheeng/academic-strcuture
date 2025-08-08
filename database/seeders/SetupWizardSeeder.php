<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupWizardSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('institute_settings')->truncate();
        Schema::enableForeignKeyConstraints();

        // Create default admin if not exists
        $adminEmail = 'admin@yourschool.edu.np';
        $existingAdmin = DB::table('users')->where('email', $adminEmail)->first();

        if (!$existingAdmin) {
            DB::table('users')->insert([
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => $adminEmail,
                'password' => bcrypt('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('institute_settings')->insert([
            'school_name' => 'Boudha English Secondary School',
            'school_name_np' => 'Boudha English माध्यामिक विद्यालय',
            'address' => 'Gokarna, Kathmandu, Nepal',
            'phone' => '01-5555555',
            'email' => 'info@bess.edu.np',
            'logo_path' => 'logos/bess-logo.png',
            'seal_path' => 'seals/bess-seal.png',
            'principal_name' => 'Dr. Wangbu Tamang',
            'principal_signature' => 'signatures/principal.png',
            'show_attendance' => false,
            'show_remarks' => true,
            'template_style' => 'modern',
            'enable_grace_marks' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Setup wizard data seeded: Institute settings and default admin.');
    }
}