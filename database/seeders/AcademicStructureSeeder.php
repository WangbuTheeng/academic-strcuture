<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AcademicStructureSeeder extends Seeder
{
    public function run()
    {
        // ⚠️ Clear in correct order due to foreign keys
        Schema::disableForeignKeyConstraints();
        DB::table('program_subjects')->truncate();
        DB::table('programs')->truncate();
        DB::table('classes')->truncate();
        DB::table('levels')->truncate();
        DB::table('academic_years')->truncate();
        DB::table('departments')->truncate();
        DB::table('faculties')->truncate();
        Schema::enableForeignKeyConstraints();

        // 📅 1. Academic Years
        $currentYear = date('Y');
        $currentBsYear = $currentYear - 1943; // Approx BS (e.g., 2025 → 2081)

        $academicYears = [
            [
                'name' => (string)($currentBsYear - 1),
                'start_date' => ($currentYear - 1) . '-04-01',
                'end_date' => $currentYear . '-03-31',
                'is_current' => false,
            ],
            [
                'name' => (string)$currentBsYear,
                'start_date' => $currentYear . '-04-01',
                'end_date' => ($currentYear + 1) . '-03-31',
                'is_current' => true,
            ],
        ];

        foreach ($academicYears as $year) {
            DB::table('academic_years')->insert(array_merge($year, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $ay2081 = DB::table('academic_years')->where('name', $currentBsYear - 1)->value('id');
        $ay2082 = DB::table('academic_years')->where('name', $currentBsYear)->value('id');

        // 🏛️ 2. Faculties
        $faculties = [
            ['name' => 'General Faculty', 'code' => 'GEN'],
            ['name' => 'Science Faculty', 'code' => 'SCI'],
            ['name' => 'Management Faculty', 'code' => 'MGT'],
        ];

        foreach ($faculties as $faculty) {
            DB::table('faculties')->insert(array_merge($faculty, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $generalFacultyId = DB::table('faculties')->where('code', 'GEN')->value('id');
        $scienceFacultyId = DB::table('faculties')->where('code', 'SCI')->value('id');
        $managementFacultyId = DB::table('faculties')->where('code', 'MGT')->value('id');

        // 🏢 3. Departments
        $departments = [
            ['faculty_id' => $generalFacultyId, 'name' => 'General Department', 'code' => 'GEN'],
            ['faculty_id' => $scienceFacultyId, 'name' => 'Science Department', 'code' => 'SCI'],
            ['faculty_id' => $managementFacultyId, 'name' => 'Management Department', 'code' => 'MGT'],
            ['faculty_id' => $scienceFacultyId, 'name' => 'Computer Science Department', 'code' => 'CS'],
        ];

        foreach ($departments as $department) {
            DB::table('departments')->insert(array_merge($department, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $generalDeptId = DB::table('departments')->where('code', 'GEN')->value('id');
        $scienceDeptId = DB::table('departments')->where('code', 'SCI')->value('id');
        $managementDeptId = DB::table('departments')->where('code', 'MGT')->value('id');
        $csDeptId = DB::table('departments')->where('code', 'CS')->value('id');

        // 🧩 4. Levels
        $levels = [
            ['name' => 'School', 'order' => 1],
            ['name' => 'College', 'order' => 2],
            ['name' => 'Bachelor', 'order' => 3],
        ];

        foreach ($levels as $level) {
            DB::table('levels')->insert(array_merge($level, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $schoolId = DB::table('levels')->where('name', 'School')->value('id');
        $collegeId = DB::table('levels')->where('name', 'College')->value('id');
        $bachelorId = DB::table('levels')->where('name', 'Bachelor')->value('id');

        // 🏫 5. Classes
        $classes = [
            // School: Nursery to 10
            ['level_id' => $schoolId, 'name' => 'Nursery', 'code' => 'N'],
            ['level_id' => $schoolId, 'name' => 'LKG', 'code' => 'LKG'],
            ['level_id' => $schoolId, 'name' => 'UKG', 'code' => 'UKG'],
            ['level_id' => $schoolId, 'name' => 'Class 1', 'code' => '1'],
            ['level_id' => $schoolId, 'name' => 'Class 2', 'code' => '2'],
            ['level_id' => $schoolId, 'name' => 'Class 3', 'code' => '3'],
            ['level_id' => $schoolId, 'name' => 'Class 4', 'code' => '4'],
            ['level_id' => $schoolId, 'name' => 'Class 5', 'code' => '5'],
            ['level_id' => $schoolId, 'name' => 'Class 6', 'code' => '6'],
            ['level_id' => $schoolId, 'name' => 'Class 7', 'code' => '7'],
            ['level_id' => $schoolId, 'name' => 'Class 8', 'code' => '8'],
            ['level_id' => $schoolId, 'name' => 'Class 9', 'code' => '9'],
            ['level_id' => $schoolId, 'name' => 'Class 10', 'code' => '10'],

            // College: 11–12
            ['level_id' => $collegeId, 'name' => 'Class 11 - Science', 'code' => '11S'],
            ['level_id' => $collegeId, 'name' => 'Class 11 - Management', 'code' => '11M'],
            ['level_id' => $collegeId, 'name' => 'Class 12 - Science', 'code' => '12S'],
            ['level_id' => $collegeId, 'name' => 'Class 12 - Management', 'code' => '12M'],

            // Bachelor
            ['level_id' => $bachelorId, 'name' => 'BBS Year 1', 'code' => 'BBS1'],
            ['level_id' => $bachelorId, 'name' => 'BBS Year 2', 'code' => 'BBS2'],
            ['level_id' => $bachelorId, 'name' => 'BBS Year 3', 'code' => 'BBS3'],
            ['level_id' => $bachelorId, 'name' => 'BBS Year 4', 'code' => 'BBS4'],
            ['level_id' => $bachelorId, 'name' => 'BCA Year 1', 'code' => 'BCA1'],
            ['level_id' => $bachelorId, 'name' => 'BCA Year 2', 'code' => 'BCA2'],
            ['level_id' => $bachelorId, 'name' => 'BCA Year 3', 'code' => 'BCA3'],
            ['level_id' => $bachelorId, 'name' => 'BCA Year 4', 'code' => 'BCA4'],
        ];

        foreach ($classes as $class) {
            DB::table('classes')->insert(array_merge($class, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // 📚 6. Programs
        $programs = [
            // School
            ['department_id' => $generalDeptId, 'name' => 'General School', 'code' => 'GS', 'duration_years' => 10, 'degree_type' => 'school', 'program_type' => 'yearly', 'is_active' => true],

            // College
            ['department_id' => $scienceDeptId, 'name' => 'Science', 'code' => 'SCI', 'duration_years' => 2, 'degree_type' => 'college', 'program_type' => 'yearly', 'is_active' => true],
            ['department_id' => $managementDeptId, 'name' => 'Management', 'code' => 'MGT', 'duration_years' => 2, 'degree_type' => 'college', 'program_type' => 'yearly', 'is_active' => true],

            // Bachelor
            ['department_id' => $managementDeptId, 'name' => 'BBS', 'code' => 'BBS', 'duration_years' => 4, 'degree_type' => 'bachelor', 'program_type' => 'semester', 'is_active' => true],
            ['department_id' => $csDeptId, 'name' => 'BCA', 'code' => 'BCA', 'duration_years' => 4, 'degree_type' => 'bachelor', 'program_type' => 'semester', 'is_active' => true],
        ];

        foreach ($programs as $program) {
            DB::table('programs')->insert(array_merge($program, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('✅ Academic structure seeded: ' . count($academicYears) . ' years, ' . count($levels) . ' levels, ' . count($classes) . ' classes, ' . count($programs) . ' programs');
    }
}