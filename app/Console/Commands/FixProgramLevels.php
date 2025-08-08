<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Program;
use App\Models\Level;

class FixProgramLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'programs:fix-levels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix programs that do not have levels assigned';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing programs without levels...');

        // Get all levels
        $levels = Level::all();
        $this->info('Available levels: ' . $levels->pluck('name')->join(', '));

        // Get programs without levels
        $programsWithoutLevels = Program::whereNull('level_id')->get();

        if ($programsWithoutLevels->isEmpty()) {
            $this->info('All programs already have levels assigned.');
            return 0;
        }

        foreach ($programsWithoutLevels as $program) {
            $this->info("Fixing program: {$program->name}");
            
            // Try to guess the level based on program name
            $levelId = $this->guessLevelForProgram($program, $levels);
            
            if ($levelId) {
                $program->update(['level_id' => $levelId]);
                $level = $levels->find($levelId);
                $this->info("  → Assigned level: {$level->name}");
            } else {
                $this->warn("  → Could not determine level for program: {$program->name}");
            }
        }

        $this->info('Program level fixing complete!');
        return 0;
    }

    private function guessLevelForProgram($program, $levels)
    {
        $name = strtolower($program->name);
        
        // School level programs
        if (str_contains($name, 'school') || str_contains($name, 'general')) {
            return $levels->where('name', 'School')->first()?->id;
        }
        
        // College level programs
        if (str_contains($name, 'science') || str_contains($name, 'management') || 
            str_contains($name, '+2') || str_contains($name, 'college')) {
            return $levels->where('name', 'College')->first()?->id;
        }
        
        // Bachelor level programs
        if (str_contains($name, 'bachelor') || str_contains($name, 'bbs') || 
            str_contains($name, 'bca') || str_contains($name, 'ba') || 
            str_contains($name, 'bsc') || str_contains($name, 'bcom')) {
            return $levels->where('name', 'Bachelor')->first()?->id;
        }
        
        // Master level programs
        if (str_contains($name, 'master') || str_contains($name, 'mbs') || 
            str_contains($name, 'mca') || str_contains($name, 'ma') || 
            str_contains($name, 'msc') || str_contains($name, 'mcom')) {
            return $levels->where('name', 'Master')->first()?->id;
        }
        
        return null;
    }
}
