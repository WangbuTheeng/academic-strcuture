<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataIsolationService
{
    /**
     * School-scoped tables that require data isolation
     */
    private const SCHOOL_SCOPED_TABLES = [
        'students',
        'teachers', 
        'classes',
        'subjects',
        'exams',
        'marks',
        'fees',
        'attendances',
        'assignments',
        'levels',
        'faculties',
        'grading_scales',
        'institute_settings',
        'academic_years',
        'sessions',
        'sections',
        'time_tables',
        'holidays',
        'events',
        'announcements',
        'library_books',
        'library_transactions',
        'transport_routes',
        'transport_vehicles',
        'hostel_rooms',
        'hostel_students'
    ];

    /**
     * Verify data isolation for a school
     */
    public function verifyDataIsolation(int $schoolId): array
    {
        $violations = [];
        
        foreach (self::SCHOOL_SCOPED_TABLES as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                $violations = array_merge($violations, $this->checkTableIsolation($table, $schoolId));
            }
        }
        
        return $violations;
    }

    /**
     * Check isolation for a specific table
     */
    private function checkTableIsolation(string $table, int $schoolId): array
    {
        $violations = [];
        
        try {
            // Check if table has school_id column
            if (!DB::getSchemaBuilder()->hasColumn($table, 'school_id')) {
                $violations[] = [
                    'table' => $table,
                    'type' => 'missing_school_id_column',
                    'message' => "Table {$table} is missing school_id column"
                ];
                return $violations;
            }

            // Check for records without school_id
            $recordsWithoutSchoolId = DB::table($table)
                ->whereNull('school_id')
                ->count();

            if ($recordsWithoutSchoolId > 0) {
                $violations[] = [
                    'table' => $table,
                    'type' => 'null_school_id',
                    'count' => $recordsWithoutSchoolId,
                    'message' => "Table {$table} has {$recordsWithoutSchoolId} records with null school_id"
                ];
            }

            // Check for records with different school_id (cross-contamination)
            $crossContamination = DB::table($table)
                ->where('school_id', '!=', $schoolId)
                ->whereNotNull('school_id')
                ->count();

            if ($crossContamination > 0) {
                $violations[] = [
                    'table' => $table,
                    'type' => 'cross_contamination',
                    'count' => $crossContamination,
                    'message' => "Table {$table} has {$crossContamination} records from other schools"
                ];
            }

        } catch (\Exception $e) {
            $violations[] = [
                'table' => $table,
                'type' => 'check_error',
                'message' => "Error checking table {$table}: " . $e->getMessage()
            ];
        }

        return $violations;
    }

    /**
     * Fix data isolation violations
     */
    public function fixDataIsolation(int $schoolId, array $violations): array
    {
        $fixed = [];
        
        foreach ($violations as $violation) {
            try {
                switch ($violation['type']) {
                    case 'null_school_id':
                        $this->fixNullSchoolId($violation['table'], $schoolId);
                        $fixed[] = $violation;
                        break;
                        
                    case 'cross_contamination':
                        // This is more serious - log but don't auto-fix
                        Log::critical('Data cross-contamination detected', [
                            'table' => $violation['table'],
                            'school_id' => $schoolId,
                            'violation' => $violation
                        ]);
                        break;
                }
            } catch (\Exception $e) {
                Log::error('Failed to fix data isolation violation', [
                    'violation' => $violation,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $fixed;
    }

    /**
     * Fix null school_id records by assigning them to the specified school
     */
    private function fixNullSchoolId(string $table, int $schoolId): void
    {
        DB::table($table)
            ->whereNull('school_id')
            ->update(['school_id' => $schoolId]);
    }

    /**
     * Ensure school context is set for current session
     */
    public function ensureSchoolContext(): void
    {
        if (!session('school_context') && auth()->check()) {
            $user = auth()->user();
            
            // Skip for super admins
            if ($user->hasRole('super-admin')) {
                return;
            }
            
            if ($user->school_id) {
                session(['school_context' => $user->school_id]);
            } else {
                throw new \Exception('User does not belong to any school');
            }
        }
    }

    /**
     * Clear school context
     */
    public function clearSchoolContext(): void
    {
        session()->forget('school_context');
    }

    /**
     * Get current school context
     */
    public function getCurrentSchoolContext(): ?int
    {
        return session('school_context');
    }

    /**
     * Set school context (for super admin operations)
     */
    public function setSchoolContext(int $schoolId): void
    {
        session(['school_context' => $schoolId]);
    }

    /**
     * Validate that user can access the current school context
     */
    public function validateSchoolAccess(): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Super admins can access any school
        if ($user->hasRole('super-admin')) {
            return true;
        }
        
        $schoolContext = session('school_context');
        
        // User must belong to the same school as the context
        return $user->school_id === $schoolContext;
    }

    /**
     * Get data isolation report for all schools
     */
    public function getSystemIsolationReport(): array
    {
        $schools = DB::table('schools')->select('id', 'name', 'code')->get();
        $report = [];
        
        foreach ($schools as $school) {
            $violations = $this->verifyDataIsolation($school->id);
            $report[] = [
                'school' => [
                    'id' => $school->id,
                    'name' => $school->name,
                    'code' => $school->code
                ],
                'violations' => $violations,
                'violation_count' => count($violations),
                'is_isolated' => empty($violations)
            ];
        }
        
        return $report;
    }

    /**
     * Generate admission number for a school
     */
    public function generateAdmissionNumber(int $schoolId, string $prefix = 'ADM'): string
    {
        $year = date('Y');
        $lastStudent = DB::table('students')
            ->where('school_id', $schoolId)
            ->where('admission_number', 'like', "{$prefix}-{$year}-%")
            ->orderBy('admission_number', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->admission_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%03d', $prefix, $year, $newNumber);
    }

    /**
     * Ensure unique admission number for school
     */
    public function ensureUniqueAdmissionNumber(int $schoolId, string $admissionNumber): string
    {
        $originalNumber = $admissionNumber;
        $counter = 1;
        
        while (DB::table('students')
            ->where('school_id', $schoolId)
            ->where('admission_number', $admissionNumber)
            ->exists()) {
            
            $admissionNumber = $originalNumber . '-' . $counter;
            $counter++;
        }
        
        return $admissionNumber;
    }
}
