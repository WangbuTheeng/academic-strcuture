<?php

namespace App\Services;

use App\Models\School;
use App\Models\User;
use App\Models\SchoolStatistics;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class SchoolCreationService
{
    protected $schoolSetupService;
    protected $auditLogger;

    public function __construct(SchoolSetupService $schoolSetupService, AuditLogger $auditLogger)
    {
        $this->schoolSetupService = $schoolSetupService;
        $this->auditLogger = $auditLogger;
    }

    /**
     * Create a new school with all necessary setup
     */
    public function createSchool(array $data): array
    {
        DB::beginTransaction();
        
        try {
            // 1. Create school record
            $school = School::create([
                'name' => $data['name'],
                'code' => $data['code'] ?? $this->generateSchoolCode($data['name']),
                'password' => $data['password'], // Let the model mutator handle hashing
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => 'active',
                'created_by' => auth()->id(),
                'settings' => $this->getDefaultSettings()
            ]);
            
            // 2. Create default admin user
            $adminUser = $this->createDefaultAdmin($school, $data);
            
            // 3. Initialize school structure with selected levels
            $selectedLevels = $data['levels'] ?? ['school', 'college', 'bachelor'];
            $this->schoolSetupService->initializeSchoolStructure($school, $selectedLevels);
            
            // 4. Create statistics record
            $this->createSchoolStatistics($school);
            
            // 5. Log creation activity
            $this->auditLogger->logActivity('school_created', [
                'resource_type' => 'school',
                'resource_id' => $school->id,
                'new_values' => [
                    'name' => $school->name,
                    'code' => $school->code,
                    'status' => $school->status
                ],
                'category' => 'school_management',
                'severity' => 'info'
            ]);
            
            DB::commit();
            
            return [
                'school' => $school,
                'admin' => $adminUser,
                'credentials' => [
                    'school_id' => $school->code,
                    'school_password' => $data['password'],
                    'admin_email' => $adminUser->email,
                    'admin_password' => $data['admin_password']
                ]
            ];
            
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Generate a unique school code
     */
    public function generateSchoolCode(string $schoolName): string
    {
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $schoolName), 0, 3));
        $counter = 1;
        
        do {
            $code = $baseCode . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        } while (School::where('code', $code)->exists());
        
        return $code;
    }

    /**
     * Create default admin user for the school
     */
    private function createDefaultAdmin(School $school, array $data): User
    {
        $adminPassword = $data['admin_password'] ?? $this->generateSecurePassword();
        
        $adminUser = User::create([
            'name' => $data['admin_name'] ?? 'School Administrator',
            'email' => $data['admin_email'] ?? "admin@{$school->code}.school",
            'password' => Hash::make($adminPassword),
            'school_id' => $school->id,
            'email_verified_at' => now()
        ]);
        
        // Assign admin role
        $adminUser->assignRole('admin');
        
        return $adminUser;
    }

    /**
     * Create initial statistics record for the school
     */
    private function createSchoolStatistics(School $school): void
    {
        SchoolStatistics::create([
            'school_id' => $school->id,
            'total_students' => 0,
            'total_teachers' => 0,
            'total_classes' => 0,
            'total_subjects' => 0,
            'total_exams' => 0,
            'feature_usage' => json_encode([]),
            'performance_metrics' => json_encode([])
        ]);
    }

    /**
     * Get default settings for new schools
     */
    private function getDefaultSettings(): array
    {
        return [
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'currency' => 'USD',
            'language' => 'en',
            'academic_year_start' => '09-01',
            'features' => [
                'academic_structure' => true,
                'examinations' => true,
                'finance_management' => true,
                'student_management' => true,
                'teacher_management' => true,
                'reports' => true
            ],
            'notifications' => [
                'email_enabled' => true,
                'sms_enabled' => false
            ]
        ];
    }

    /**
     * Generate a secure password
     */
    private function generateSecurePassword(): string
    {
        return Str::random(8) . rand(100, 999) . '!';
    }

    /**
     * Update school information
     */
    public function updateSchool(School $school, array $data): School
    {
        $oldValues = $school->only(['name', 'email', 'phone', 'address', 'status']);
        
        $school->update([
            'name' => $data['name'] ?? $school->name,
            'email' => $data['email'] ?? $school->email,
            'phone' => $data['phone'] ?? $school->phone,
            'address' => $data['address'] ?? $school->address,
            'status' => $data['status'] ?? $school->status
        ]);
        
        // Log update activity
        $this->auditLogger->logActivity('school_updated', [
            'resource_type' => 'school',
            'resource_id' => $school->id,
            'old_values' => $oldValues,
            'new_values' => $school->only(['name', 'email', 'phone', 'address', 'status']),
            'category' => 'school_management',
            'severity' => 'info'
        ]);
        
        return $school;
    }

    /**
     * Delete a school and all its data
     */
    public function deleteSchool(School $school): bool
    {
        DB::beginTransaction();
        
        try {
            // Log deletion before actual deletion
            $this->auditLogger->logActivity('school_deleted', [
                'resource_type' => 'school',
                'resource_id' => $school->id,
                'old_values' => [
                    'name' => $school->name,
                    'code' => $school->code,
                    'status' => $school->status
                ],
                'category' => 'school_management',
                'severity' => 'warning'
            ]);
            
            // Delete school (cascade will handle related data)
            $school->delete();
            
            DB::commit();
            return true;
            
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
