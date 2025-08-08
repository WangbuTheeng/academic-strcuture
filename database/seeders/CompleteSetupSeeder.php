<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Level;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\GradingScale;
use App\Models\GradeRange;

class CompleteSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Completing Academic Management System Setup...');

        // 1. Create Institute Settings
        $this->createInstituteSettings();

        // 2. Create Academic Year
        $this->createAcademicYear();

        // 3. Create Grading Scale
        $this->createGradingScale();

        // 4. Create Sample Academic Structure
        $this->createAcademicStructure();

        $this->command->info('✅ Setup completed successfully!');
        $this->command->info('📚 You can now access the admin panel at: /admin');
        $this->command->info('🎓 Default admin credentials: admin@example.com / password');
    }

    private function createInstituteSettings()
    {
        if (!DB::table('institute_settings')->exists()) {
            DB::table('institute_settings')->insert([
                'institution_name' => 'Academic Management System',
                'institution_address' => 'Kathmandu, Nepal',
                'institution_phone' => '01-1234567',
                'institution_email' => 'info@school.edu.np',
                'principal_name' => 'Principal Name',
                'academic_year_start_month' => 4,
                'academic_year_end_month' => 3,
                'setup_completed' => true,
                'setup_completed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✅ Institute settings created');
        }
    }

    private function createAcademicYear()
    {
        if (!AcademicYear::exists()) {
            AcademicYear::create([
                'name' => '2081',
                'start_date' => '2024-04-14',
                'end_date' => '2025-04-13',
                'is_current' => true,
            ]);
            $this->command->info('✅ Academic year created');
        }
    }

    private function createGradingScale()
    {
        if (!GradingScale::exists()) {
            $gradingScale = GradingScale::create([
                'name' => 'Standard Grading Scale',
                'description' => 'General purpose grading scale suitable for most institutions',
                'level_id' => null, // Global scale
                'program_id' => null,
                'pass_mark' => 40.00,
                'max_marks' => 100.00,
                'is_default' => true,
                'is_active' => true,
            ]);

            // Create grade ranges
            $grades = [
                ['grade' => 'A+', 'min_percentage' => 90, 'max_percentage' => 100, 'gpa' => 4.0, 'description' => 'Outstanding'],
                ['grade' => 'A', 'min_percentage' => 80, 'max_percentage' => 89, 'gpa' => 3.6, 'description' => 'Excellent'],
                ['grade' => 'B+', 'min_percentage' => 70, 'max_percentage' => 79, 'gpa' => 3.2, 'description' => 'Very Good'],
                ['grade' => 'B', 'min_percentage' => 60, 'max_percentage' => 69, 'gpa' => 2.8, 'description' => 'Good'],
                ['grade' => 'C+', 'min_percentage' => 50, 'max_percentage' => 59, 'gpa' => 2.4, 'description' => 'Satisfactory'],
                ['grade' => 'C', 'min_percentage' => 40, 'max_percentage' => 49, 'gpa' => 2.0, 'description' => 'Acceptable'],
                ['grade' => 'D', 'min_percentage' => 32, 'max_percentage' => 39, 'gpa' => 1.6, 'description' => 'Partially Acceptable'],
                ['grade' => 'E', 'min_percentage' => 0, 'max_percentage' => 31, 'gpa' => 0.0, 'description' => 'Insufficient'],
            ];

            foreach ($grades as $grade) {
                GradeRange::create([
                    'grading_scale_id' => $gradingScale->id,
                    'grade' => $grade['grade'],
                    'min_percentage' => $grade['min_percentage'],
                    'max_percentage' => $grade['max_percentage'],
                    'gpa' => $grade['gpa'],
                    'description' => $grade['description'],
                ]);
            }

            $this->command->info('✅ Grading scale created with grade ranges');
        }
    }

    private function createAcademicStructure()
    {
        // Create sample faculties if none exist
        if (!Faculty::exists()) {
            $faculties = [
                ['name' => 'Faculty of Science', 'code' => 'FOS'],
                ['name' => 'Faculty of Management', 'code' => 'FOM'],
                ['name' => 'Faculty of Humanities', 'code' => 'FOH'],
            ];

            foreach ($faculties as $facultyData) {
                $faculty = Faculty::create($facultyData);

                // Create departments for each faculty
                $departments = [];
                if ($faculty->code === 'FOS') {
                    $departments = [
                        ['name' => 'Computer Science Department', 'code' => 'CS'],
                        ['name' => 'Mathematics Department', 'code' => 'MATH'],
                    ];
                } elseif ($faculty->code === 'FOM') {
                    $departments = [
                        ['name' => 'Business Administration', 'code' => 'BA'],
                        ['name' => 'Accounting Department', 'code' => 'ACC'],
                    ];
                } else {
                    $departments = [
                        ['name' => 'English Department', 'code' => 'ENG'],
                        ['name' => 'Social Studies Department', 'code' => 'SS'],
                    ];
                }

                foreach ($departments as $deptData) {
                    Department::create([
                        'faculty_id' => $faculty->id,
                        'name' => $deptData['name'],
                        'code' => $deptData['code'],
                    ]);
                }
            }

            $this->command->info('✅ Sample academic structure created');
        }
    }
}
