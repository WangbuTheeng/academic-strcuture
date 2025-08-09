<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentBill;
use App\Models\School;

class TestBillNumberGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:bill-numbers {--school-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test bill number generation for schools';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $schoolId = $this->option('school-id');

        if ($schoolId) {
            $school = School::find($schoolId);
            if (!$school) {
                $this->error("School with ID {$schoolId} not found.");
                return 1;
            }
            $schools = collect([$school]);
        } else {
            $schools = School::all();
        }

        $this->info('🧪 Testing Bill Number Generation');
        $this->newLine();

        foreach ($schools as $school) {
            $this->info("📍 Testing for school: {$school->name} (ID: {$school->id})");

            // Generate 5 test bill numbers for this school
            for ($i = 1; $i <= 5; $i++) {
                try {
                    $billNumber = StudentBill::generateBillNumber($school->id);
                    $this->line("  {$i}. Generated: {$billNumber}");

                    // Check if it's unique within the school
                    $exists = StudentBill::where('bill_number', $billNumber)
                                        ->where('school_id', $school->id)
                                        ->exists();

                    if ($exists) {
                        $this->error("    ❌ Duplicate found!");
                    } else {
                        $this->info("    ✅ Unique");
                    }
                } catch (\Exception $e) {
                    $this->error("  ❌ Error generating bill number: " . $e->getMessage());
                }
            }

            $this->newLine();
        }

        // Show current bill numbers for each school
        $this->info('📊 Current Bill Numbers by School:');
        foreach ($schools as $school) {
            $billCount = StudentBill::where('school_id', $school->id)->count();
            $latestBill = StudentBill::where('school_id', $school->id)
                                   ->orderBy('created_at', 'desc')
                                   ->first();

            $this->line("  {$school->name}: {$billCount} bills");
            if ($latestBill) {
                $this->line("    Latest: {$latestBill->bill_number}");
            }
        }

        return 0;
    }
}
