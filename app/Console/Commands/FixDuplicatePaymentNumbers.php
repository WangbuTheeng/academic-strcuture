<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class FixDuplicatePaymentNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:fix-duplicates {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix duplicate payment numbers by regenerating them with school isolation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Checking for duplicate payment numbers...');
        
        // Find duplicate payment numbers
        $duplicates = DB::table('payments')
            ->select('payment_number', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_number')
            ->having('count', '>', 1)
            ->get();
            
        if ($duplicates->isEmpty()) {
            $this->info('No duplicate payment numbers found.');
            return 0;
        }
        
        $this->warn("Found {$duplicates->count()} duplicate payment numbers:");
        
        foreach ($duplicates as $duplicate) {
            $this->line("- {$duplicate->payment_number} ({$duplicate->count} occurrences)");
        }
        
        if ($dryRun) {
            $this->info('Dry run mode - no changes will be made.');
            $this->info('Run without --dry-run to fix the duplicates.');
            return 0;
        }
        
        $this->info('Fixing duplicate payment numbers...');
        
        foreach ($duplicates as $duplicate) {
            $payments = Payment::where('payment_number', $duplicate->payment_number)
                ->orderBy('id')
                ->get();
                
            // Keep the first payment with the original number, regenerate others
            $first = true;
            foreach ($payments as $payment) {
                if ($first) {
                    $this->line("Keeping original: {$payment->payment_number} (ID: {$payment->id})");
                    $first = false;
                    continue;
                }
                
                $oldNumber = $payment->payment_number;
                
                // Generate new payment number for this school
                $newNumber = Payment::generatePaymentNumber($payment->school_id);
                
                // Update the payment
                $payment->update(['payment_number' => $newNumber]);
                
                $this->line("Updated: {$oldNumber} -> {$newNumber} (ID: {$payment->id}, School: {$payment->school_id})");
            }
        }
        
        $this->info('Duplicate payment numbers have been fixed.');
        return 0;
    }
}
