<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\DataIsolationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDataIsolation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:fix-isolation {--school-id= : Fix isolation for specific school} {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix data isolation issues across schools';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dataIsolationService = app(DataIsolationService::class);
        $isDryRun = $this->option('dry-run');
        $schoolId = $this->option('school-id');

        $this->info('🔍 Checking data isolation...');

        if ($schoolId) {
            $school = School::find($schoolId);
            if (!$school) {
                $this->error("School with ID {$schoolId} not found.");
                return 1;
            }

            $this->checkSchoolIsolation($school, $dataIsolationService, $isDryRun);
        } else {
            $this->checkAllSchoolsIsolation($dataIsolationService, $isDryRun);
        }

        return 0;
    }

    private function checkSchoolIsolation(School $school, DataIsolationService $service, bool $isDryRun)
    {
        $this->info("Checking isolation for school: {$school->name} (ID: {$school->id})");

        $violations = $service->verifyDataIsolation($school->id);

        if (empty($violations)) {
            $this->info("✅ No isolation violations found for {$school->name}");
            return;
        }

        $this->warn("⚠️  Found " . count($violations) . " isolation violations for {$school->name}:");

        foreach ($violations as $violation) {
            $this->line("  - {$violation['message']}");
        }

        if (!$isDryRun) {
            if ($this->confirm("Fix these violations?")) {
                $fixed = $service->fixDataIsolation($school->id, $violations);
                $this->info("✅ Fixed " . count($fixed) . " violations");
            }
        } else {
            $this->info("🔍 Dry run mode - no changes made");
        }
    }

    private function checkAllSchoolsIsolation(DataIsolationService $service, bool $isDryRun)
    {
        $report = $service->getSystemIsolationReport();

        $this->info("📊 System-wide data isolation report:");
        $this->newLine();

        $totalViolations = 0;
        $schoolsWithViolations = 0;

        foreach ($report as $schoolReport) {
            $school = $schoolReport['school'];
            $violations = $schoolReport['violations'];
            $violationCount = $schoolReport['violation_count'];

            if ($violationCount > 0) {
                $schoolsWithViolations++;
                $totalViolations += $violationCount;

                $this->warn("⚠️  {$school['name']} ({$school['code']}): {$violationCount} violations");

                foreach ($violations as $violation) {
                    $this->line("    - {$violation['message']}");
                }

                if (!$isDryRun && $this->confirm("Fix violations for {$school['name']}?")) {
                    $fixed = $service->fixDataIsolation($school['id'], $violations);
                    $this->info("    ✅ Fixed " . count($fixed) . " violations");
                }
            } else {
                $this->info("✅ {$school['name']} ({$school['code']}): No violations");
            }
        }

        $this->newLine();
        $this->info("📈 Summary:");
        $this->info("  Total schools: " . count($report));
        $this->info("  Schools with violations: {$schoolsWithViolations}");
        $this->info("  Total violations: {$totalViolations}");

        if ($isDryRun && $totalViolations > 0) {
            $this->newLine();
            $this->info("🔧 To fix all violations, run:");
            $this->info("  php artisan data:fix-isolation");
        }
    }
}
