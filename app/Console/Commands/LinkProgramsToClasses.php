<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Program;
use App\Models\ClassModel;
use App\Models\Level;

class LinkProgramsToClasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'programs:link-classes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link programs to classes based on their levels';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Linking programs to classes...');

        $programs = Program::with('level')->get();
        $classes = ClassModel::with('level')->get();

        $linked = 0;

        foreach ($programs as $program) {
            if (!$program->level) {
                $this->warn("Program '{$program->name}' has no level assigned. Skipping.");
                continue;
            }

            // Find classes that match this program's level
            $matchingClasses = $classes->where('level_id', $program->level_id);

            foreach ($matchingClasses as $class) {
                // Check if this program-class relationship already exists
                if (!$program->classes()->where('class_id', $class->id)->exists()) {
                    $program->classes()->attach($class->id, [
                        'year_no' => 1, // Default year
                        'semester_id' => null, // Can be set later if needed
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $this->info("Linked program '{$program->name}' to class '{$class->name}'");
                    $linked++;
                } else {
                    $this->comment("Program '{$program->name}' already linked to class '{$class->name}'");
                }
            }
        }

        $this->info("Linking complete! {$linked} new program-class relationships created.");

        // Show summary
        $this->table(
            ['Program', 'Level', 'Classes Count'],
            Program::with(['level', 'classes'])->get()->map(function ($program) {
                return [
                    $program->name,
                    $program->level->name ?? 'No Level',
                    $program->classes->count(),
                ];
            })->toArray()
        );

        return 0;
    }
}
