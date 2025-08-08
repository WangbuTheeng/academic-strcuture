<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;
use App\Models\Program;
use App\Models\ClassModel;
use App\Models\Level;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class FixSchoolDataIsolation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:fix-data-isolation {--school-id= : Specific school ID to fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix data isolation issues by ensuring each school has its own programs and classes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting school data isolation fix...');

        $schoolId = $this->option('school-id');
        
        if ($schoolId) {
            $schools = School::where('id', $schoolId)->get();
            if ($schools->isEmpty()) {
                $this->error("School with ID {$schoolId} not found.");
                return 1;
            }
        } else {
            $schools = School::where('status', 'active')->where('id', '!=', 1)->get();
        }

        foreach ($schools as $school) {
            $this->info("Processing school: {$school->name} (ID: {$school->id})");
            $this->fixSchoolData($school);
        }

        $this->info('School data isolation fix completed!');
        return 0;
    }

    private function fixSchoolData(School $school)
    {
        // Get template data from the default school (ID: 1)
        $templatePrograms = Program::where('school_id', 1)->with(['level', 'department', 'classes'])->get();
        $templateClasses = ClassModel::where('school_id', 1)->with('level')->get();

        $this->info("  Creating programs and classes for {$school->name}...");

        foreach ($templatePrograms as $templateProgram) {
            // Check if this program already exists for this school
            $existingProgram = Program::where('school_id', $school->id)
                ->where('name', $templateProgram->name)
                ->first();

            if (!$existingProgram) {
                // Generate unique code for this school
                $baseCode = substr($templateProgram->code, 0, 3);
                $uniqueCode = $baseCode . '_S' . $school->id . '_' . $templateProgram->id;

                // Create new program for this school
                $newProgram = Program::create([
                    'school_id' => $school->id,
                    'department_id' => $templateProgram->department_id,
                    'level_id' => $templateProgram->level_id,
                    'name' => $templateProgram->name,
                    'code' => $uniqueCode,
                    'duration_years' => $templateProgram->duration_years,
                    'degree_type' => $templateProgram->degree_type,
                    'program_type' => $templateProgram->program_type,
                    'description' => $templateProgram->description,
                    'is_active' => $templateProgram->is_active,
                ]);

                $this->info("    Created program: {$newProgram->name}");

                // Create classes for this program
                foreach ($templateProgram->classes as $templateClass) {
                    $existingClass = ClassModel::where('school_id', $school->id)
                        ->where('name', $templateClass->name)
                        ->where('level_id', $templateClass->level_id)
                        ->first();

                    if (!$existingClass) {
                        // Generate unique code for this class
                        $baseClassCode = substr($templateClass->code, 0, 3);
                        $uniqueClassCode = $baseClassCode . '_S' . $school->id . '_' . $templateClass->id;

                        $newClass = ClassModel::create([
                            'school_id' => $school->id,
                            'level_id' => $templateClass->level_id,
                            'department_id' => $templateClass->department_id,
                            'name' => $templateClass->name,
                            'code' => $uniqueClassCode,
                            'is_active' => $templateClass->is_active,
                        ]);

                        $this->info("      Created class: {$newClass->name}");
                        
                        // Link the class to the program
                        $newProgram->classes()->attach($newClass->id, [
                            'year_no' => $templateProgram->classes()->where('class_id', $templateClass->id)->first()->pivot->year_no ?? 1,
                            'semester_id' => $templateProgram->classes()->where('class_id', $templateClass->id)->first()->pivot->semester_id ?? null,
                        ]);
                    } else {
                        // Link existing class to the program if not already linked
                        if (!$newProgram->classes()->where('class_id', $existingClass->id)->exists()) {
                            $newProgram->classes()->attach($existingClass->id, [
                                'year_no' => $templateProgram->classes()->where('class_id', $templateClass->id)->first()->pivot->year_no ?? 1,
                                'semester_id' => $templateProgram->classes()->where('class_id', $templateClass->id)->first()->pivot->semester_id ?? null,
                            ]);
                        }
                    }
                }
            } else {
                $this->info("    Program already exists: {$existingProgram->name}");
            }
        }
    }
}
