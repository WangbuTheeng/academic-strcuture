<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Register custom commands
Artisan::command('schools:fix-passwords {--dry-run : Show what would be fixed without making changes}', function () {
    $dryRun = $this->option('dry-run');

    if ($dryRun) {
        $this->info('🔍 DRY RUN MODE - No changes will be made');
    } else {
        $this->info('🔧 FIXING SCHOOL PASSWORDS');
    }

    $this->newLine();

    $schools = \App\Models\School::all();
    $fixedCount = 0;
    $skippedCount = 0;

    foreach ($schools as $school) {
        $this->info("🏫 Checking {$school->name} ({$school->code}):");

        // Try common passwords to see if they work
        $commonPasswords = [
            'password123', 'admin123', 'default123',
            $school->code, strtolower($school->code), $school->code . '123', 'school123'
        ];

        $passwordWorks = false;
        foreach ($commonPasswords as $password) {
            if (\Illuminate\Support\Facades\Hash::check($password, $school->password)) {
                $this->info("  ✅ Password works: {$password}");
                $passwordWorks = true;
                $skippedCount++;
                break;
            }
        }

        if (!$passwordWorks) {
            $this->warn("  ❌ Password appears to be double-hashed");

            if (!$dryRun) {
                // Reset to a known password
                $newPassword = 'password123';
                \Illuminate\Support\Facades\DB::table('schools')
                    ->where('id', $school->id)
                    ->update(['password' => \Illuminate\Support\Facades\Hash::make($newPassword)]);

                $this->info("  ✅ Fixed! New password: {$newPassword}");
                $fixedCount++;
            } else {
                $this->info("  🔧 Would fix this password");
                $fixedCount++;
            }
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
})->purpose('Fix double-hashed school passwords');
