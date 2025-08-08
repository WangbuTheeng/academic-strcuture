<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GradingScalesSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('grade_ranges')->truncate();
        DB::table('grading_scales')->truncate();
        Schema::enableForeignKeyConstraints();

        $levels = DB::table('levels')->pluck('id', 'name');
        $programs = DB::table('programs')->pluck('id', 'name');

        $scales = [
            // School: 40% Pass
            [
                'name' => 'School Grading (40% Pass)',
                'description' => 'Standard grading scale for school level with 40% pass mark',
                'pass_mark' => 40.00,
                'level_id' => $levels['School'] ?? null,
                'program_id' => null,
                'is_default' => true,
                'grades' => [
                    ['grade' => 'A+', 'min_percentage' => 90, 'max_percentage' => 100, 'gpa' => 4.0],
                    ['grade' => 'A', 'min_percentage' => 80, 'max_percentage' => 89, 'gpa' => 3.6],
                    ['grade' => 'B+', 'min_percentage' => 70, 'max_percentage' => 79, 'gpa' => 3.2],
                    ['grade' => 'B', 'min_percentage' => 60, 'max_percentage' => 69, 'gpa' => 2.8],
                    ['grade' => 'C+', 'min_percentage' => 50, 'max_percentage' => 59, 'gpa' => 2.4],
                    ['grade' => 'C', 'min_percentage' => 40, 'max_percentage' => 49, 'gpa' => 2.0],
                    ['grade' => 'F', 'min_percentage' => 0, 'max_percentage' => 39, 'gpa' => 0.0],
                ],
            ],
            // BBS: 45% Pass
            [
                'name' => 'BBS Grading (45% Pass)',
                'description' => 'Grading scale for BBS program with 45% pass mark',
                'pass_mark' => 45.00,
                'level_id' => null,
                'program_id' => $programs['BBS'] ?? null,
                'is_default' => false,
                'grades' => [
                    ['grade' => 'A+', 'min_percentage' => 80, 'max_percentage' => 100, 'gpa' => 4.0],
                    ['grade' => 'A', 'min_percentage' => 70, 'max_percentage' => 79, 'gpa' => 3.6],
                    ['grade' => 'B+', 'min_percentage' => 60, 'max_percentage' => 69, 'gpa' => 3.2],
                    ['grade' => 'B', 'min_percentage' => 50, 'max_percentage' => 59, 'gpa' => 2.8],
                    ['grade' => 'C+', 'min_percentage' => 45, 'max_percentage' => 49, 'gpa' => 2.4],
                    ['grade' => 'F', 'min_percentage' => 0, 'max_percentage' => 44, 'gpa' => 0.0],
                ],
            ],
            // BCA: 40% Pass
            [
                'name' => 'BCA Grading (40% Pass)',
                'description' => 'Grading scale for BCA program with 40% pass mark',
                'pass_mark' => 40.00,
                'level_id' => null,
                'program_id' => $programs['BCA'] ?? null,
                'is_default' => false,
                'grades' => [
                    ['grade' => 'A+', 'min_percentage' => 85, 'max_percentage' => 100, 'gpa' => 4.0],
                    ['grade' => 'A', 'min_percentage' => 75, 'max_percentage' => 84, 'gpa' => 3.6],
                    ['grade' => 'B+', 'min_percentage' => 65, 'max_percentage' => 74, 'gpa' => 3.2],
                    ['grade' => 'B', 'min_percentage' => 55, 'max_percentage' => 64, 'gpa' => 2.8],
                    ['grade' => 'C+', 'min_percentage' => 45, 'max_percentage' => 54, 'gpa' => 2.4],
                    ['grade' => 'C', 'min_percentage' => 40, 'max_percentage' => 44, 'gpa' => 2.0],
                    ['grade' => 'F', 'min_percentage' => 0, 'max_percentage' => 39, 'gpa' => 0.0],
                ],
            ],
        ];

        foreach ($scales as $scale) {
            // Create the grading scale
            $gradingScaleId = DB::table('grading_scales')->insertGetId([
                'name' => $scale['name'],
                'description' => $scale['description'],
                'pass_mark' => $scale['pass_mark'],
                'level_id' => $scale['level_id'],
                'program_id' => $scale['program_id'],
                'is_default' => $scale['is_default'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create grade ranges for this grading scale
            foreach ($scale['grades'] as $grade) {
                DB::table('grade_ranges')->insert([
                    'grading_scale_id' => $gradingScaleId,
                    'grade' => $grade['grade'],
                    'min_percentage' => $grade['min_percentage'],
                    'max_percentage' => $grade['max_percentage'],
                    'gpa' => $grade['gpa'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Grading scales seeded: ' . count($scales) . ' scales with grade ranges.');
    }
}