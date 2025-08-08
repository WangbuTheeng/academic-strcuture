<?php

namespace Database\Seeders;

use App\Models\GradingScale;
use App\Models\GradeRange;
use App\Models\Level;
use App\Models\Program;
use Illuminate\Database\Seeder;

class GradingScaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default grading scale
        $defaultScale = GradingScale::create([
            'name' => 'Standard Grading Scale',
            'description' => 'Default grading scale for all academic levels',
            'level_id' => null,
            'program_id' => null,
            'pass_mark' => 40.00,
            'max_marks' => 100.00,
            'is_default' => true,
            'is_active' => true,
            'created_by' => 1, // Admin user
        ]);

        // Create grade ranges for default scale
        $defaultGrades = [
            ['grade' => 'A+', 'min' => 90, 'max' => 100, 'gpa' => 4.0, 'description' => 'Outstanding', 'passing' => true],
            ['grade' => 'A', 'min' => 80, 'max' => 89, 'gpa' => 3.6, 'description' => 'Excellent', 'passing' => true],
            ['grade' => 'B+', 'min' => 70, 'max' => 79, 'gpa' => 3.2, 'description' => 'Very Good', 'passing' => true],
            ['grade' => 'B', 'min' => 60, 'max' => 69, 'gpa' => 2.8, 'description' => 'Good', 'passing' => true],
            ['grade' => 'C+', 'min' => 50, 'max' => 59, 'gpa' => 2.4, 'description' => 'Satisfactory', 'passing' => true],
            ['grade' => 'C', 'min' => 40, 'max' => 49, 'gpa' => 2.0, 'description' => 'Acceptable', 'passing' => true],
            ['grade' => 'D', 'min' => 30, 'max' => 39, 'gpa' => 1.6, 'description' => 'Partially Acceptable', 'passing' => false],
            ['grade' => 'F', 'min' => 0, 'max' => 29, 'gpa' => 0.0, 'description' => 'Fail', 'passing' => false],
        ];

        foreach ($defaultGrades as $gradeData) {
            GradeRange::create([
                'grading_scale_id' => $defaultScale->id,
                'grade' => $gradeData['grade'],
                'min_percentage' => $gradeData['min'],
                'max_percentage' => $gradeData['max'],
                'gpa' => $gradeData['gpa'],
                'description' => $gradeData['description'],
                'is_passing' => $gradeData['passing'],
            ]);
        }

        // Create high school specific grading scale
        $highSchoolLevel = Level::where('name', 'like', '%High%')->first();
        if ($highSchoolLevel) {
            $highSchoolScale = GradingScale::create([
                'name' => 'High School Grading Scale',
                'description' => 'Specialized grading scale for high school level',
                'level_id' => $highSchoolLevel->id,
                'program_id' => null,
                'pass_mark' => 35.00,
                'max_marks' => 100.00,
                'is_default' => false,
                'is_active' => true,
                'created_by' => 1,
            ]);

            // High school grade ranges (slightly different)
            $highSchoolGrades = [
                ['grade' => 'A+', 'min' => 90, 'max' => 100, 'gpa' => 4.0, 'description' => 'Distinction', 'passing' => true],
                ['grade' => 'A', 'min' => 80, 'max' => 89, 'gpa' => 3.6, 'description' => 'First Division', 'passing' => true],
                ['grade' => 'B+', 'min' => 70, 'max' => 79, 'gpa' => 3.2, 'description' => 'Second Division', 'passing' => true],
                ['grade' => 'B', 'min' => 60, 'max' => 69, 'gpa' => 2.8, 'description' => 'Second Division', 'passing' => true],
                ['grade' => 'C+', 'min' => 50, 'max' => 59, 'gpa' => 2.4, 'description' => 'Third Division', 'passing' => true],
                ['grade' => 'C', 'min' => 35, 'max' => 49, 'gpa' => 2.0, 'description' => 'Third Division', 'passing' => true],
                ['grade' => 'D', 'min' => 25, 'max' => 34, 'gpa' => 1.0, 'description' => 'Compartment', 'passing' => false],
                ['grade' => 'F', 'min' => 0, 'max' => 24, 'gpa' => 0.0, 'description' => 'Fail', 'passing' => false],
            ];

            foreach ($highSchoolGrades as $gradeData) {
                GradeRange::create([
                    'grading_scale_id' => $highSchoolScale->id,
                    'grade' => $gradeData['grade'],
                    'min_percentage' => $gradeData['min'],
                    'max_percentage' => $gradeData['max'],
                    'gpa' => $gradeData['gpa'],
                    'description' => $gradeData['description'],
                    'is_passing' => $gradeData['passing'],
                ]);
            }
        }

        // Create university specific grading scale
        $universityLevel = Level::where('name', 'like', '%University%')->orWhere('name', 'like', '%Bachelor%')->first();
        if ($universityLevel) {
            $universityScale = GradingScale::create([
                'name' => 'University Grading Scale',
                'description' => 'Grading scale for university level programs',
                'level_id' => $universityLevel->id,
                'program_id' => null,
                'pass_mark' => 45.00,
                'max_marks' => 100.00,
                'is_default' => false,
                'is_active' => true,
                'created_by' => 1,
            ]);

            // University grade ranges
            $universityGrades = [
                ['grade' => 'A+', 'min' => 85, 'max' => 100, 'gpa' => 4.0, 'description' => 'Excellent', 'passing' => true],
                ['grade' => 'A', 'min' => 75, 'max' => 84, 'gpa' => 3.7, 'description' => 'Very Good', 'passing' => true],
                ['grade' => 'B+', 'min' => 65, 'max' => 74, 'gpa' => 3.3, 'description' => 'Good', 'passing' => true],
                ['grade' => 'B', 'min' => 55, 'max' => 64, 'gpa' => 3.0, 'description' => 'Above Average', 'passing' => true],
                ['grade' => 'C+', 'min' => 50, 'max' => 54, 'gpa' => 2.7, 'description' => 'Average', 'passing' => true],
                ['grade' => 'C', 'min' => 45, 'max' => 49, 'gpa' => 2.0, 'description' => 'Below Average', 'passing' => true],
                ['grade' => 'D', 'min' => 35, 'max' => 44, 'gpa' => 1.0, 'description' => 'Poor', 'passing' => false],
                ['grade' => 'F', 'min' => 0, 'max' => 34, 'gpa' => 0.0, 'description' => 'Fail', 'passing' => false],
            ];

            foreach ($universityGrades as $gradeData) {
                GradeRange::create([
                    'grading_scale_id' => $universityScale->id,
                    'grade' => $gradeData['grade'],
                    'min_percentage' => $gradeData['min'],
                    'max_percentage' => $gradeData['max'],
                    'gpa' => $gradeData['gpa'],
                    'description' => $gradeData['description'],
                    'is_passing' => $gradeData['passing'],
                ]);
            }
        }

        $this->command->info('Grading scales and grade ranges seeded successfully!');
    }
}
