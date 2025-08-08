<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TeachersSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('teacher_subjects')->truncate();
        // Delete users with teacher role through model_has_roles table
        $teacherRoleId = DB::table('roles')->where('name', 'teacher')->value('id');
        if ($teacherRoleId) {
            $teacherUserIds = DB::table('model_has_roles')
                ->where('role_id', $teacherRoleId)
                ->where('model_type', 'App\\Models\\User')
                ->pluck('model_id');
            DB::table('users')->whereIn('id', $teacherUserIds)->delete();
        }
        Schema::enableForeignKeyConstraints();

        $academicYear = DB::table('academic_years')->where('is_current', true)->first();
        if (!$academicYear) {
            $this->command->warn('❌ No current academic year found.');
            return;
        }

        $classes = DB::table('classes')->pluck('id', 'code');
        $subjects = DB::table('subjects')->pluck('id', 'code');
        $programs = DB::table('programs')->pluck('id', 'name');

        $teachersData = [
            // Science
            ['name' => 'Dr. Rajan Sharma', 'email' => 'rajan.sharma@school.edu.np', 'subjects' => ['PHY', 'CHE'], 'class_codes' => ['11S', '12S']],
            ['name' => 'Anita Karki', 'email' => 'anita.karki@school.edu.np', 'subjects' => ['BIO'], 'class_codes' => ['11S', '12S']],
            ['name' => 'Bikash Thapa', 'email' => 'bikash.thapa@school.edu.np', 'subjects' => ['CSCI'], 'class_codes' => ['11S', '12S', 'BCA1', 'BCA2']],

            // Management
            ['name' => 'Suman Poudel', 'email' => 'suman.poudel@school.edu.np', 'subjects' => ['ACC', 'ACC1', 'ACC2'], 'class_codes' => ['11M', '12M', 'BBS1', 'BBS2']],
            ['name' => 'Nisha Bhandari', 'email' => 'nisha.bhandari@school.edu.np', 'subjects' => ['BST', 'ECO'], 'class_codes' => ['11M', '12M']],
            ['name' => 'Hari Gautam', 'email' => 'hari.gautam@school.edu.np', 'subjects' => ['BL', 'FIN'], 'class_codes' => ['BBS1', 'BBS2', 'BBS3']],

            // BCA
            ['name' => 'Amit Regmi', 'email' => 'amit.regmi@school.edu.np', 'subjects' => ['CPROG', 'DBMS'], 'class_codes' => ['BCA1', 'BCA2', 'BCA3']],
            ['name' => 'Sarita Maharjan', 'email' => 'sarita.maharjan@school.edu.np', 'subjects' => ['WEB', 'SE'], 'class_codes' => ['BCA2', 'BCA3', 'BCA4']],

            // General
            ['name' => 'Gita Shrestha', 'email' => 'gita.shrestha@school.edu.np', 'subjects' => ['ENG', 'MATH'], 'class_codes' => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10']],
            ['name' => 'Rita Adhikari', 'email' => 'rita.adhikari@school.edu.np', 'subjects' => ['NEP', 'SOC', 'SCI'], 'class_codes' => ['6', '7', '8', '9', '10']],
        ];

        foreach ($teachersData as $data) {
            $userId = DB::table('users')->insertGetId([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign teacher role using Spatie permissions
            DB::table('model_has_roles')->insert([
                'role_id' => DB::table('roles')->where('name', 'teacher')->value('id'),
                'model_type' => 'App\\Models\\User',
                'model_id' => $userId,
            ]);

            foreach ($data['class_codes'] as $code) {
                $classId = $classes[$code] ?? null;
                if (!$classId) continue;

                foreach ($data['subjects'] as $subjectCode) {
                    $subjectId = $subjects[$subjectCode] ?? null;
                    if (!$subjectId) continue;

                    DB::table('teacher_subjects')->insert([
                        'user_id' => $userId,
                        'class_id' => $classId,
                        'subject_id' => $subjectId,
                        'academic_year_id' => $academicYear->id,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('✅ Teachers seeded: ' . count($teachersData) . ' teachers with subject assignments.');
    }
}