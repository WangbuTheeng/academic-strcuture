<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeeStructure;
use App\Models\AcademicYear;

class SampleFeeStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYear = AcademicYear::first();
        
        if (!$academicYear) {
            $this->command->error('No academic year found. Please create an academic year first.');
            return;
        }

        // Check if fee structures already exist
        if (FeeStructure::count() > 0) {
            $this->command->info('Fee structures already exist.');
            return;
        }

        $feeStructures = [
            [
                'academic_year_id' => $academicYear->id,
                'fee_category' => 'tuition',
                'fee_name' => 'Monthly Tuition Fee',
                'description' => 'Regular monthly tuition fee for all students',
                'amount' => 5000.00,
                'billing_frequency' => 'monthly',
                'due_date_offset' => 30,
                'is_mandatory' => true,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'academic_year_id' => $academicYear->id,
                'fee_category' => 'admission',
                'fee_name' => 'Admission Fee',
                'description' => 'One-time admission fee for new students',
                'amount' => 2000.00,
                'billing_frequency' => 'annual',
                'due_date_offset' => 15,
                'is_mandatory' => true,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'academic_year_id' => $academicYear->id,
                'fee_category' => 'examination',
                'fee_name' => 'Examination Fee',
                'description' => 'Semester examination fee',
                'amount' => 1500.00,
                'billing_frequency' => 'semester',
                'due_date_offset' => 20,
                'is_mandatory' => false,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'academic_year_id' => $academicYear->id,
                'fee_category' => 'library',
                'fee_name' => 'Library Fee',
                'description' => 'Annual library access fee',
                'amount' => 800.00,
                'billing_frequency' => 'annual',
                'due_date_offset' => 30,
                'is_mandatory' => false,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'academic_year_id' => $academicYear->id,
                'fee_category' => 'laboratory',
                'fee_name' => 'Computer Lab Fee',
                'description' => 'Computer laboratory usage fee',
                'amount' => 1200.00,
                'billing_frequency' => 'semester',
                'due_date_offset' => 25,
                'is_mandatory' => false,
                'is_active' => true,
                'created_by' => 1,
            ],
        ];

        foreach ($feeStructures as $structure) {
            FeeStructure::create($structure);
        }

        $this->command->info('Created ' . count($feeStructures) . ' sample fee structures successfully.');
    }
}
