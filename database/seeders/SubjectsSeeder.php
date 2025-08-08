<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SubjectsSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('program_subjects')->truncate();
        DB::table('subjects')->truncate();
        Schema::enableForeignKeyConstraints();

        // 🔤 Define subjects
        $subjects = [
            // Core Subjects
            ['name' => 'English', 'code' => 'ENG', 'max_assess' => 20, 'max_theory' => 80, 'max_practical' => 0, 'is_practical' => false],
            ['name' => 'Nepali', 'code' => 'NEP', 'max_assess' => 20, 'max_theory' => 80, 'max_practical' => 0, 'is_practical' => false],
            ['name' => 'Mathematics', 'code' => 'MATH', 'max_assess' => 20, 'max_theory' => 80, 'max_practical' => 0, 'is_practical' => false],
            ['name' => 'Social Studies', 'code' => 'SOC', 'max_assess' => 20, 'max_theory' => 80, 'max_practical' => 0, 'is_practical' => false],
            ['name' => 'Science', 'code' => 'SCI', 'max_assess' => 25, 'max_theory' => 75, 'max_practical' => 0, 'is_practical' => false],

            // Science (Class 11–12)
            ['name' => 'Physics', 'code' => 'PHY', 'max_assess' => 25, 'max_theory' => 75, 'max_practical' => 25, 'is_practical' => true],
            ['name' => 'Chemistry', 'code' => 'CHE', 'max_assess' => 25, 'max_theory' => 75, 'max_practical' => 25, 'is_practical' => true],
            ['name' => 'Biology', 'code' => 'BIO', 'max_assess' => 25, 'max_theory' => 75, 'max_practical' => 25, 'is_practical' => true],
            ['name' => 'Computer Science', 'code' => 'CSCI', 'max_assess' => 25, 'max_theory' => 75, 'max_practical' => 25, 'is_practical' => true],

            // Management
            ['name' => 'Accountancy', 'code' => 'ACC', 'max_assess' => 25, 'max_theory' => 75, 'max_practical' => 0, 'is_practical' => false],
            ['name' => 'Business Studies', 'code' => 'BST', 'max_assess' => 25, 'max_theory' => 75, 'max_practical' => 0, 'is_practical' => false],
            ['name' => 'Economics', 'code' => 'ECO', 'max_assess' => 25, 'max_theory' => 75, 'max_practical' => 0, 'is_practical' => false],
            ['name' => 'Office Practice', 'code' => 'OP', 'max_assess' => 25, 'max_theory' => 75, 'max_practical' => 0, 'is_practical' => false],

            // BBS
            ['name' => 'Accounting I', 'code' => 'ACC1', 'max_assess' => 0, 'max_theory' => 100, 'max_practical' => 0, 'is_practical' => false],
            ['name' => 'Accounting II', 'code' => 'ACC2', 'max_assess' => 0, 'max_theory' => 100, 'max_practical' => 0, 'is_practical' => false],
            ['name' => 'Business Economics', 'code' => 'BE', 'max_assess' => 0, 'max_theory' => 100, 'max_practical' => 0, 'is_practical' => false],
            ['name' => 'Business Law', 'code' => 'BL', 'max_assess' => 0, 'max_theory' => 100, 'max_practical' => 0, 'is_practical' => false],
            ['name' => 'Finance', 'code' => 'FIN', 'max_assess' => 0, 'max_theory' => 100, 'max_practical' => 0, 'is_practical' => false],

            // BCA
            ['name' => 'Computer Programming', 'code' => 'CPROG', 'max_assess' => 20, 'max_theory' => 60, 'max_practical' => 40, 'is_practical' => true],
            ['name' => 'Database Management', 'code' => 'DBMS', 'max_assess' => 20, 'max_theory' => 60, 'max_practical' => 40, 'is_practical' => true],
            ['name' => 'Web Technology', 'code' => 'WEB', 'max_assess' => 20, 'max_theory' => 60, 'max_practical' => 40, 'is_practical' => true],
            ['name' => 'Software Engineering', 'code' => 'SE', 'max_assess' => 20, 'max_theory' => 60, 'max_practical' => 40, 'is_practical' => true],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->insert(array_merge($subject, [
                'has_internal' => $subject['max_assess'] > 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 🔄 Get IDs
        $programs = DB::table('programs')->pluck('id', 'name');
        $subjectsMap = DB::table('subjects')->pluck('id', 'code');

        // 🧩 Define program_subjects mapping
        $programSubjects = [
            // School: General
            ['program' => 'General School', 'subjects' => ['ENG', 'NEP', 'MATH', 'SOC', 'SCI']],

            // College: Science
            ['program' => 'Science', 'subjects' => ['ENG', 'NEP', 'MATH', 'PHY', 'CHE', 'BIO', 'CSCI']],

            // College: Management
            ['program' => 'Management', 'subjects' => ['ENG', 'NEP', 'MATH', 'ACC', 'BST', 'ECO', 'OP']],

            // BBS
            ['program' => 'BBS', 'subjects' => ['ACC1', 'ACC2', 'BE', 'BL', 'FIN']],

            // BCA
            ['program' => 'BCA', 'subjects' => ['ENG', 'CPROG', 'DBMS', 'WEB', 'SE']],
        ];

        foreach ($programSubjects as $ps) {
            $programId = $programs[$ps['program']] ?? null;
            if (!$programId) continue;

            foreach ($ps['subjects'] as $code) {
                $subjectId = $subjectsMap[$code] ?? null;
                if (!$subjectId) continue;

                DB::table('program_subjects')->insert([
                    'program_id' => $programId,
                    'subject_id' => $subjectId,
                    'is_compulsory' => true,
                    'credit_hours' => 3,
                    'year_no' => null,
                    'semester_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Subjects seeded: ' . count($subjects) . ' subjects, mapped to programs.');
    }
}