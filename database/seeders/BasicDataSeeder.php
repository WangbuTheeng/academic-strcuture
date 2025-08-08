<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Level;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\ClassModel;
use App\Models\Program;
use App\Models\AcademicYear;

class BasicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Levels
        $schoolLevel = Level::create(['name' => 'School', 'order' => 1]);
        $collegeLevel = Level::create(['name' => 'College', 'order' => 2]);
        $bachelorLevel = Level::create(['name' => 'Bachelor', 'order' => 3]);

        // Create Faculties
        $scienceFaculty = Faculty::create(['name' => 'Faculty of Science', 'code' => 'FOS']);
        $managementFaculty = Faculty::create(['name' => 'Faculty of Management', 'code' => 'FOM']);
        $humanitiesFaculty = Faculty::create(['name' => 'Faculty of Humanities', 'code' => 'FOH']);

        // Create Departments
        $generalDept = Department::create(['faculty_id' => $scienceFaculty->id, 'name' => 'General Department', 'code' => 'GEN']);
        $computerDept = Department::create(['faculty_id' => $scienceFaculty->id, 'name' => 'Computer Department', 'code' => 'COMP']);
        $managementDept = Department::create(['faculty_id' => $managementFaculty->id, 'name' => 'Management Department', 'code' => 'MGMT']);

        // Create Classes
        // School Level Classes
        for ($i = 1; $i <= 10; $i++) {
            ClassModel::create([
                'level_id' => $schoolLevel->id,
                'department_id' => $generalDept->id,
                'name' => "Class $i",
                'code' => "C$i",
                'is_active' => true
            ]);
        }

        // College Level Classes
        ClassModel::create([
            'level_id' => $collegeLevel->id,
            'department_id' => $scienceFaculty->id,
            'name' => 'Class 11 Science',
            'code' => '11SCI',
            'is_active' => true
        ]);

        ClassModel::create([
            'level_id' => $collegeLevel->id,
            'department_id' => $managementFaculty->id,
            'name' => 'Class 11 Management',
            'code' => '11MGMT',
            'is_active' => true
        ]);

        ClassModel::create([
            'level_id' => $collegeLevel->id,
            'department_id' => $scienceFaculty->id,
            'name' => 'Class 12 Science',
            'code' => '12SCI',
            'is_active' => true
        ]);

        ClassModel::create([
            'level_id' => $collegeLevel->id,
            'department_id' => $managementFaculty->id,
            'name' => 'Class 12 Management',
            'code' => '12MGMT',
            'is_active' => true
        ]);

        // Bachelor Level Classes
        ClassModel::create([
            'level_id' => $bachelorLevel->id,
            'department_id' => $computerDept->id,
            'name' => 'BCA 1st Year',
            'code' => 'BCA1',
            'is_active' => true
        ]);

        ClassModel::create([
            'level_id' => $bachelorLevel->id,
            'department_id' => $managementDept->id,
            'name' => 'BBS 1st Year',
            'code' => 'BBS1',
            'is_active' => true
        ]);

        // Create Programs
        Program::create([
            'department_id' => $generalDept->id,
            'level_id' => $schoolLevel->id,
            'name' => 'General Education',
            'code' => 'GEN',
            'duration_years' => 10,
            'degree_type' => 'school',
            'program_type' => 'yearly',
            'is_active' => true
        ]);

        Program::create([
            'department_id' => $scienceFaculty->id,
            'level_id' => $collegeLevel->id,
            'name' => 'Science Program',
            'code' => 'SCI',
            'duration_years' => 2,
            'degree_type' => 'college',
            'program_type' => 'yearly',
            'is_active' => true
        ]);

        Program::create([
            'department_id' => $managementFaculty->id,
            'level_id' => $collegeLevel->id,
            'name' => 'Management Program',
            'code' => 'MGT',
            'duration_years' => 2,
            'degree_type' => 'college',
            'program_type' => 'yearly',
            'is_active' => true
        ]);

        Program::create([
            'department_id' => $computerDept->id,
            'level_id' => $bachelorLevel->id,
            'name' => 'Bachelor of Computer Application',
            'code' => 'BCA',
            'duration_years' => 4,
            'degree_type' => 'bachelor',
            'program_type' => 'semester',
            'is_active' => true
        ]);

        Program::create([
            'department_id' => $managementDept->id,
            'level_id' => $bachelorLevel->id,
            'name' => 'Bachelor of Business Studies',
            'code' => 'BBS',
            'duration_years' => 4,
            'degree_type' => 'bachelor',
            'program_type' => 'semester',
            'is_active' => true
        ]);

        // Create Academic Year
        AcademicYear::create([
            'name' => '2081',
            'start_date' => '2024-04-14',
            'end_date' => '2025-04-13',
            'is_current' => true
        ]);

        $this->command->info('Basic academic structure data created successfully!');
    }
}
