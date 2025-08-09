<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FixSchoolPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schools:fix-passwords {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix double-hashed school passwords caused by the password mutator issue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        } else {
            $this->info('🔧 FIXING SCHOOL PASSWORDS');
        }
        
        $this->newLine();

        $schools = School::all();
        $fixedCount = 0;
        $skippedCount = 0;

        foreach ($schools as $school) {
            $this->info("🏫 Checking {$school->name} ({$school->code}):");
            
            // Try to detect if password is double-hashed by testing common patterns
            $isDoubleHashed = $this->isPasswordDoubleHashed($school);
            
            if ($isDoubleHashed) {
                $this->warn("  ❌ Password appears to be double-hashed");
                
                if (!$dryRun) {
                    // Reset to a known password that will be properly hashed
                    $newPassword = 'password123';
                    
                    // Temporarily disable the mutator by updating directly
                    DB::table('schools')
                        ->where('id', $school->id)
                        ->update(['password' => Hash::make($newPassword)]);
                    
                    $this->info("  ✅ Fixed! New password: {$newPassword}");
                    $fixedCount++;
                } else {
                    $this->info("  🔧 Would fix this password");
                    $fixedCount++;
                }
            } else {
                $this->info("  ✅ Password appears to be correctly hashed");
                $skippedCount++;
            }
        }

        $this->newLine();
        
        if ($dryRun) {
            $this->info("📊 DRY RUN RESULTS:");
            $this->info("  - Schools that would be fixed: {$fixedCount}");
            $this->info("  - Schools that are OK: {$skippedCount}");
            $this->newLine();
            $this->info("Run without --dry-run to apply fixes");
        } else {
            $this->info("📊 RESULTS:");
            $this->info("  - Schools fixed: {$fixedCount}");
            $this->info("  - Schools skipped: {$skippedCount}");
            
            if ($fixedCount > 0) {
                $this->newLine();
                $this->warn("⚠️  IMPORTANT: Fixed schools now have password 'password123'");
                $this->warn("   Super-admins should reset these passwords through the interface");
            }
        }

        return 0;
    }

    /**
     * Check if a school's password is double-hashed
     */
    private function isPasswordDoubleHashed(School $school): bool
    {
        // Common passwords that might have been used during school creation
        $commonPasswords = [
            'password123',
            'admin123',
            'default123',
            $school->code,
            strtolower($school->code),
            $school->code . '123',
            'school123'
        ];

        // If any common password works, it's probably correctly hashed
        foreach ($commonPasswords as $password) {
            if (Hash::check($password, $school->password)) {
                return false; // Password is correctly hashed
            }
        }

        // If no common password works, it's likely double-hashed
        // Additional check: double-hashed passwords are typically longer
        return strlen($school->password) > 60; // Normal bcrypt hashes are 60 chars
    }
}
