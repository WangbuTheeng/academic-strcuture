<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class ProgramSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first program and first few subjects
        $program = Program::first();
        $subjects = Subject::take(3)->get();

        if (!$program || $subjects->count() === 0) {
            $this->command->info('No programs or subjects found. Please create them first.');
            return;
        }

        $this->command->info("Setting up subjects for program: {$program->name}");

        // Attach subjects to program
        foreach ($subjects as $index => $subject) {
            // Check if already attached
            if (!$program->subjects()->where('subject_id', $subject->id)->exists()) {
                $program->subjects()->attach($subject->id, [
                    'is_compulsory' => $index < 2, // First 2 are compulsory
                    'credit_hours' => 3,
                    'year_no' => 1
                ]);
                
                $compulsoryText = $index < 2 ? 'compulsory' : 'elective';
                $this->command->info("- Attached {$subject->name} ({$compulsoryText})");
            }
        }

        $this->command->info("Program now has {$program->subjects()->count()} subjects");
    }
}
