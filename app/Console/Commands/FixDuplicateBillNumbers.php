<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentBill;
use Illuminate\Support\Facades\DB;

class FixDuplicateBillNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:fix-duplicates {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix duplicate bill numbers by regenerating them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        } else {
            $this->info('🔧 FIXING DUPLICATE BILL NUMBERS');
        }

        $this->newLine();

        // Find duplicate bill numbers
        $duplicates = DB::select("
            SELECT bill_number, COUNT(*) as count
            FROM student_bills
            GROUP BY bill_number
            HAVING COUNT(*) > 1
        ");

        if (empty($duplicates)) {
            $this->info('✅ No duplicate bill numbers found!');
            return 0;
        }

        $this->warn("Found " . count($duplicates) . " duplicate bill numbers:");

        foreach ($duplicates as $duplicate) {
            $this->line("  - {$duplicate->bill_number} ({$duplicate->count} occurrences)");
        }

        $this->newLine();

        if (!$dryRun && !$this->confirm('Do you want to proceed with fixing these duplicates?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $fixedCount = 0;

        foreach ($duplicates as $duplicate) {
            $bills = StudentBill::where('bill_number', $duplicate->bill_number)
                               ->orderBy('id')
                               ->get();

            // Keep the first bill with original number, fix the rest
            foreach ($bills->skip(1) as $bill) {
                $this->info("🔧 Fixing bill ID {$bill->id} (was: {$bill->bill_number})");

                if (!$dryRun) {
                    $newBillNumber = $this->generateUniqueBillNumber();
                    $bill->update(['bill_number' => $newBillNumber]);
                    $this->info("  ✅ New number: {$newBillNumber}");
                } else {
                    $this->info("  🔧 Would generate new bill number");
                }

                $fixedCount++;
            }
        }

        $this->newLine();

        if ($dryRun) {
            $this->info("📊 DRY RUN RESULTS:");
            $this->info("  - Bills that would be fixed: {$fixedCount}");
            $this->newLine();
            $this->info("Run without --dry-run to apply fixes");
        } else {
            $this->info("📊 RESULTS:");
            $this->info("  - Bills fixed: {$fixedCount}");
            $this->info("✅ All duplicate bill numbers have been resolved!");
        }

        return 0;
    }

    /**
     * Generate a unique bill number
     */
    private function generateUniqueBillNumber(): string
    {
        do {
            $billNumber = StudentBill::generateBillNumber();
            $exists = StudentBill::where('bill_number', $billNumber)->exists();
        } while ($exists);

        return $billNumber;
    }
}
