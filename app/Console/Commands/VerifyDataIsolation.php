<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;
use App\Models\FeeStructure;
use App\Models\StudentBill;
use App\Models\Student;

class VerifyDataIsolation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:verify-isolation {--fix : Fix isolation issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify and optionally fix data isolation issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verifying Data Isolation...');
        $this->newLine();

        $issues = [];
        $fixMode = $this->option('fix');

        // Check Fee Structures
        $this->info('📋 Checking Fee Structures...');
        $feeStructuresWithoutSchool = FeeStructure::whereNull('school_id')->count();
        if ($feeStructuresWithoutSchool > 0) {
            $issues[] = "Fee Structures without school_id: {$feeStructuresWithoutSchool}";
            $this->warn("  ⚠️  Found {$feeStructuresWithoutSchool} fee structures without school_id");

            if ($fixMode) {
                $this->warn("  🔧 Fixing fee structures...");
                // This would need manual assignment based on business logic
                $this->warn("  ❌ Cannot auto-fix - requires manual school assignment");
            }
        } else {
            $this->info("  ✅ All fee structures have school_id");
        }

        // Check Student Bills
        $this->info('💰 Checking Student Bills...');
        $billsWithoutSchool = StudentBill::whereNull('school_id')->count();
        if ($billsWithoutSchool > 0) {
            $issues[] = "Student Bills without school_id: {$billsWithoutSchool}";
            $this->warn("  ⚠️  Found {$billsWithoutSchool} student bills without school_id");

            if ($fixMode) {
                $this->warn("  🔧 Fixing student bills...");
                $fixed = 0;
                StudentBill::whereNull('school_id')->chunk(100, function ($bills) use (&$fixed) {
                    foreach ($bills as $bill) {
                        if ($bill->student && $bill->student->school_id) {
                            $bill->update(['school_id' => $bill->student->school_id]);
                            $fixed++;
                        }
                    }
                });
                $this->info("  ✅ Fixed {$fixed} student bills");
            }
        } else {
            $this->info("  ✅ All student bills have school_id");
        }

        // Check Students
        $this->info('👥 Checking Students...');
        $studentsWithoutSchool = Student::whereNull('school_id')->count();
        if ($studentsWithoutSchool > 0) {
            $issues[] = "Students without school_id: {$studentsWithoutSchool}";
            $this->warn("  ⚠️  Found {$studentsWithoutSchool} students without school_id");

            if ($fixMode) {
                $this->warn("  ❌ Cannot auto-fix students - requires manual school assignment");
            }
        } else {
            $this->info("  ✅ All students have school_id");
        }

        // Check cross-school data leakage
        $this->info('🔒 Checking for cross-school data leakage...');
        $schools = School::all();

        foreach ($schools as $school) {
            $this->info("  📍 Checking school: {$school->name}");

            // Check if fee structures reference other schools' data
            $crossSchoolFees = FeeStructure::where('school_id', $school->id)
                ->whereHas('level', function($q) use ($school) {
                    $q->where('school_id', '!=', $school->id);
                })->count();

            if ($crossSchoolFees > 0) {
                $issues[] = "School {$school->name} has {$crossSchoolFees} fee structures referencing other schools' levels";
                $this->warn("    ⚠️  Found {$crossSchoolFees} fee structures with cross-school level references");
            }
        }

        $this->newLine();

        if (empty($issues)) {
            $this->info('🎉 Data isolation verification completed successfully!');
            $this->info('✅ No isolation issues found.');
        } else {
            $this->warn('⚠️  Data isolation issues found:');
            foreach ($issues as $issue) {
                $this->line("  • {$issue}");
            }

            if (!$fixMode) {
                $this->newLine();
                $this->info('💡 Run with --fix flag to attempt automatic fixes');
            }
        }

        return empty($issues) ? 0 : 1;
    }
}
