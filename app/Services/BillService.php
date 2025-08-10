<?php

namespace App\Services;

use App\Models\StudentBill;
use App\Models\BillItem;
use App\Models\Student;
use App\Models\FeeStructure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BillService
{
    /**
     * Create a student bill safely with proper error handling
     */
    public function createBill(array $billData, array $feeStructures = [], array $customFees = [], bool $includePreviousDues = false): StudentBill
    {
        return DB::transaction(function () use ($billData, $feeStructures, $customFees, $includePreviousDues) {
            // Ensure school_id is set
            if (!isset($billData['school_id'])) {
                $billData['school_id'] = auth()->user()->school_id;
            }

            // Create the bill
            $bill = StudentBill::create($billData);

            $totalAmount = 0;

            // Add previous dues if requested
            if ($includePreviousDues && isset($billData['student_id'])) {
                $previousDues = $this->getPreviousDuesAmount($billData['student_id']);
                if ($previousDues > 0) {
                    BillItem::create([
                        'school_id' => $billData['school_id'],
                        'bill_id' => $bill->id,
                        'fee_category' => 'Previous Dues',
                        'description' => 'Outstanding balance from previous bills',
                        'unit_amount' => $previousDues,
                        'quantity' => 1,
                        'total_amount' => $previousDues,
                        'final_amount' => $previousDues,
                    ]);

                    $totalAmount += $previousDues;
                }
            }
            
            // Add fee structure items
            foreach ($feeStructures as $feeStructure) {
                $feeStructureModel = null;
                $customAmount = null;

                if ($feeStructure instanceof FeeStructure) {
                    $feeStructureModel = $feeStructure;
                } elseif (is_array($feeStructure) && isset($feeStructure['id'])) {
                    // Handle array format with custom amount
                    $feeStructureModel = FeeStructure::find($feeStructure['id']);
                    $customAmount = $feeStructure['custom_amount'] ?? null;
                } else {
                    // Handle simple ID format
                    $feeStructureModel = FeeStructure::find($feeStructure);
                }

                if ($feeStructureModel) {
                    // Use custom amount if provided, otherwise use original amount
                    $finalAmount = $customAmount !== null ? $customAmount : $feeStructureModel->amount;

                    BillItem::create([
                        'school_id' => $billData['school_id'],
                        'bill_id' => $bill->id,
                        'fee_structure_id' => $feeStructureModel->id,
                        'fee_category' => $feeStructureModel->fee_category,
                        'description' => $feeStructureModel->fee_name,
                        'unit_amount' => $finalAmount,
                        'quantity' => 1,
                        'total_amount' => $finalAmount,
                        'final_amount' => $finalAmount,
                    ]);

                    $totalAmount += $finalAmount;
                }
            }
            
            // Add custom fee items
            foreach ($customFees as $customFee) {
                BillItem::create([
                    'school_id' => $billData['school_id'],
                    'bill_id' => $bill->id,
                    'fee_category' => $customFee['category'] ?? 'Custom',
                    'description' => $customFee['name'],
                    'unit_amount' => $customFee['amount'],
                    'quantity' => 1,
                    'total_amount' => $customFee['amount'],
                    'final_amount' => $customFee['amount'],
                ]);
                
                $totalAmount += $customFee['amount'];
            }
            
            // Update bill totals
            $bill->update([
                'total_amount' => $totalAmount,
                'balance_amount' => $totalAmount,
            ]);
            
            return $bill->fresh();
        });
    }
    
    /**
     * Create bills for multiple students
     */
    public function createBulkBills(array $students, array $billData, array $feeStructures = [], bool $includePreviousDues = false): array
    {
        $createdBills = [];
        $errors = [];
        
        foreach ($students as $student) {
            try {
                if ($student instanceof Student) {
                    $studentModel = $student;
                } else {
                    $studentModel = Student::find($student);
                }
                
                if (!$studentModel) {
                    $errors[] = "Student not found: " . ($student['id'] ?? $student);
                    continue;
                }
                
                // Check if bill already exists
                $existingBill = StudentBill::where('student_id', $studentModel->id)
                    ->where('academic_year_id', $billData['academic_year_id'])
                    ->where('bill_date', $billData['bill_date'])
                    ->exists();
                
                if ($existingBill) {
                    $errors[] = "Bill already exists for student: {$studentModel->full_name}";
                    continue;
                }
                
                // Prepare student-specific bill data
                $studentBillData = array_merge($billData, [
                    'student_id' => $studentModel->id,
                    'class_id' => $studentModel->currentEnrollment?->class_id,
                    'program_id' => $studentModel->currentEnrollment?->program_id,
                ]);
                
                $bill = $this->createBill($studentBillData, $feeStructures, [], $includePreviousDues);
                $createdBills[] = $bill;
                
            } catch (\Exception $e) {
                $studentName = isset($studentModel) ? $studentModel->full_name : 'Unknown';
                $errors[] = "Error creating bill for {$studentName}: " . $e->getMessage();
            }
        }
        
        return [
            'created_bills' => $createdBills,
            'errors' => $errors,
            'success_count' => count($createdBills),
            'error_count' => count($errors)
        ];
    }

    /**
     * Get the total outstanding amount for a student from previous bills
     */
    public function getPreviousDuesAmount(int $studentId): float
    {
        return StudentBill::where('student_id', $studentId)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->sum('balance_amount');
    }

    /**
     * Get detailed information about previous dues for a student
     */
    public function getPreviousDuesDetails(int $studentId): array
    {
        $previousBills = StudentBill::where('student_id', $studentId)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->with(['billItems'])
            ->orderBy('due_date', 'asc')
            ->get();

        $totalAmount = $previousBills->sum('balance_amount');
        $billCount = $previousBills->count();

        return [
            'total_amount' => $totalAmount,
            'bill_count' => $billCount,
            'bills' => $previousBills,
            'formatted_amount' => 'Rs. ' . number_format($totalAmount, 2)
        ];
    }
    
    /**
     * Create installment bills for a student
     */
    public function createInstallmentBills(Student $student, array $feeStructures, array $installmentConfig, array $billData): array
    {
        $createdBills = [];
        $totalAmount = collect($feeStructures)->sum('amount');
        $installmentAmount = $totalAmount / $installmentConfig['number_of_installments'];
        
        for ($i = 1; $i <= $installmentConfig['number_of_installments']; $i++) {
            $dueDate = Carbon::parse($billData['bill_date'])
                ->addMonths($i - 1)
                ->addDays($installmentConfig['days_between_installments'] ?? 30);
            
            $installmentBillData = array_merge($billData, [
                'student_id' => $student->id,
                'class_id' => $student->currentEnrollment?->class_id,
                'program_id' => $student->currentEnrollment?->program_id,
                'bill_title' => "Installment {$i} of {$installmentConfig['number_of_installments']} - " . Carbon::parse($billData['bill_date'])->format('M Y'),
                'due_date' => $dueDate,
                'total_amount' => $installmentAmount,
                'balance_amount' => $installmentAmount,
            ]);
            
            $bill = StudentBill::create($installmentBillData);
            
            // Add proportional fee items
            foreach ($feeStructures as $feeStructure) {
                $itemAmount = ($feeStructure->amount / $totalAmount) * $installmentAmount;
                
                BillItem::create([
                    'bill_id' => $bill->id,
                    'fee_structure_id' => $feeStructure->id,
                    'fee_category' => $feeStructure->fee_category,
                    'description' => $feeStructure->fee_name . " (Installment {$i})",
                    'unit_amount' => $feeStructure->amount,
                    'quantity' => 1 / $installmentConfig['number_of_installments'],
                    'total_amount' => $itemAmount,
                    'final_amount' => $itemAmount,
                ]);
            }
            
            $createdBills[] = $bill;
        }
        
        return $createdBills;
    }
    
    /**
     * Check if a bill number is unique within a school
     */
    public function isBillNumberUnique(string $billNumber, int $schoolId = null): bool
    {
        $query = StudentBill::where('bill_number', $billNumber);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return !$query->exists();
    }
    
    /**
     * Generate a safe bill number for a specific school
     */
    public function generateSafeBillNumber(int $schoolId = null): string
    {
        $maxAttempts = 10;
        $attempts = 0;

        // Use current user's school if not provided
        if (!$schoolId && auth()->check()) {
            $schoolId = auth()->user()->school_id;
        }

        do {
            $billNumber = StudentBill::generateBillNumber($schoolId);
            $isUnique = $this->isBillNumberUnique($billNumber, $schoolId);
            $attempts++;
        } while (!$isUnique && $attempts < $maxAttempts);

        if (!$isUnique) {
            throw new \Exception('Unable to generate unique bill number after ' . $maxAttempts . ' attempts');
        }

        return $billNumber;
    }
}
