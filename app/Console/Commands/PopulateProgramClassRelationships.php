<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Program;
use App\Models\ClassModel;
use Illuminate\Support\Facades\DB;

class PopulateProgramClassRelationships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'academic:populate-program-classes 
                            {--force : Force repopulation even if relationships exist}
                            {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate program-class relationships based on level and department matching';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');

        $this->info('Populating Program-Class Relationships...');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Check if relationships already exist
        $existingCount = DB::table('program_classes')->count();
        if ($existingCount > 0 && !$force) {
            $this->warn("Found {$existingCount} existing program-class relationships.");
            $this->warn('Use --force to repopulate or --dry-run to see what would be done.');
            return;
        }

        if ($force && !$dryRun) {
            $this->info('Clearing existing program-class relationships...');
            DB::table('program_classes')->truncate();
        }

        $programs = Program::with(['level', 'department'])->get();
        $classes = ClassModel::with(['level', 'department'])->where('is_active', true)->get();

        $relationships = [];
        $matchCount = 0;

        foreach ($programs as $program) {
            $this->info("Processing program: {$program->name}");
            
            $matchingClasses = $classes->filter(function ($class) use ($program) {
                // Match by level (required)
                $levelMatch = $program->level_id && $class->level_id && $program->level_id == $class->level_id;
                
                // Match by department (optional - if both have departments, they should match)
                $departmentMatch = true;
                if ($program->department_id && $class->department_id) {
                    $departmentMatch = $program->department_id == $class->department_id;
                }
                
                return $levelMatch && $departmentMatch;
            });

            foreach ($matchingClasses as $class) {
                $relationships[] = [
                    'program_id' => $program->id,
                    'class_id' => $class->id,
                    'year_no' => null,
                    'semester_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $matchCount++;
                $levelName = $class->level ? $class->level->name : 'N/A';
                $this->line("  → Matched with class: {$class->name} ({$levelName})");
            }

            if ($matchingClasses->isEmpty()) {
                $this->warn("  → No matching classes found for program: {$program->name}");
            }
        }

        if ($dryRun) {
            $this->info("\nDRY RUN RESULTS:");
            $this->info("Would create {$matchCount} program-class relationships");
            return;
        }

        if (!empty($relationships)) {
            $this->info("\nInserting {$matchCount} program-class relationships...");
            
            // Insert in chunks to avoid memory issues
            $chunks = array_chunk($relationships, 100);
            foreach ($chunks as $chunk) {
                DB::table('program_classes')->insert($chunk);
            }
            
            $this->info("✅ Successfully created {$matchCount} program-class relationships!");
        } else {
            $this->warn('No relationships to create.');
        }

        // Show summary
        $this->info("\nSummary:");
        $this->info("Programs processed: " . $programs->count());
        $this->info("Active classes: " . $classes->count());
        $this->info("Relationships created: {$matchCount}");
    }
}
