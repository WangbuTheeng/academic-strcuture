<?php

namespace App\Services;

use App\Models\School;
use App\Models\User;
use App\Models\Level;
use App\Models\Faculty;
use App\Models\InstituteSettings;
use App\Models\GradingScale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolSetupService
{
    /**
     * Create a new school with default structure
     */
    public function createSchool(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create school
            $school = School::create([
                'name' => $data['name'],
                'code' => strtoupper($data['code']),
                'password' => $data['password'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => 'active',
                'created_by' => auth()->id(),
                'settings' => [
                    'timezone' => 'Asia/Kathmandu',
                    'academic_year_start' => 4, // Chaitra (April)
                    'academic_year_end' => 3,   // Falgun (March)
                    'default_language' => 'en',
                ]
            ]);

            // Create default admin user for school
            $adminEmail = $data['admin_email'] ?? 'admin@' . strtolower($data['code']) . '.school';
            $adminPassword = $data['admin_password'] ?? 'admin123';

            $admin = User::create([
                'name' => $data['admin_name'] ?? 'School Administrator',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'school_id' => $school->id,
                'email_verified_at' => now(),
            ]);
            $admin->assignRole('admin');

            // Create default academic structure
            $this->createDefaultAcademicStructure($school);

            // Create default institute settings
            $this->createDefaultInstituteSettings($school);

            // Create default grading scale
            $this->createDefaultGradingScale($school);

            return [
                'school' => $school,
                'admin' => $admin,
                'admin_email' => $adminEmail,
                'admin_password' => $adminPassword,
            ];
        });
    }

    /**
     * Create default academic structure for school
     */
    private function createDefaultAcademicStructure(School $school)
    {
        // Create default levels
        $levels = [
            [
                'name' => 'School Level',
                'description' => 'Basic Education (Classes 1-10)',
                'sort_order' => 1
            ],
            [
                'name' => 'College Level',
                'description' => 'Higher Secondary Education (Classes 11-12)',
                'sort_order' => 2
            ],
        ];

        foreach ($levels as $levelData) {
            Level::create([
                'name' => $levelData['name'],
                'description' => $levelData['description'],
                'sort_order' => $levelData['sort_order'],
                'school_id' => $school->id,
            ]);
        }

        // Create default faculties
        $faculties = [
            [
                'name' => 'Science',
                'description' => 'Science and Mathematics Faculty',
                'sort_order' => 1
            ],
            [
                'name' => 'Management',
                'description' => 'Business and Management Faculty',
                'sort_order' => 2
            ],
            [
                'name' => 'Humanities',
                'description' => 'Arts and Humanities Faculty',
                'sort_order' => 3
            ],
        ];

        foreach ($faculties as $facultyData) {
            Faculty::create([
                'name' => $facultyData['name'],
                'description' => $facultyData['description'],
                'sort_order' => $facultyData['sort_order'],
                'school_id' => $school->id,
            ]);
        }
    }

    /**
     * Create default institute settings for school
     */
    private function createDefaultInstituteSettings(School $school)
    {
        InstituteSettings::create([
            'institution_name' => $school->name,
            'institution_address' => $school->address ?? '',
            'institution_phone' => $school->phone ?? '',
            'institution_email' => $school->email ?? '',
            'principal_name' => 'Principal Name',
            'academic_year_start_month' => 4, // Chaitra (April)
            'academic_year_end_month' => 3,   // Falgun (March)
            'setup_completed' => false,
            'school_id' => $school->id,
        ]);
    }

    /**
     * Create default grading scale for school
     */
    private function createDefaultGradingScale(School $school)
    {
        $gradingScale = GradingScale::create([
            'name' => 'Default Grading Scale',
            'description' => 'Standard grading scale for ' . $school->name,
            'is_default' => true,
            'school_id' => $school->id,
        ]);

        // You can add default grade ranges here if needed
        // This would require a separate grades table related to grading_scales
    }

    /**
     * Update school information
     */
    public function updateSchool(School $school, array $data)
    {
        return DB::transaction(function () use ($school, $data) {
            $school->update([
                'name' => $data['name'],
                'code' => strtoupper($data['code']),
                'email' => $data['email'] ?? $school->email,
                'phone' => $data['phone'] ?? $school->phone,
                'address' => $data['address'] ?? $school->address,
                'status' => $data['status'] ?? $school->status,
            ]);

            // Update password if provided
            if (!empty($data['password'])) {
                $school->password = $data['password']; // Will be hashed by mutator
                $school->save();
            }

            return $school;
        });
    }

    /**
     * Deactivate school and its users
     */
    public function deactivateSchool(School $school)
    {
        return DB::transaction(function () use ($school) {
            // Update school status
            $school->update(['status' => 'inactive']);

            // Optionally deactivate all users in the school
            // User::where('school_id', $school->id)->update(['status' => 'inactive']);

            return $school;
        });
    }

    /**
     * Generate unique school code
     */
    public function generateSchoolCode($baseName)
    {
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $baseName), 0, 3));
        $counter = 1;

        do {
            $code = $baseCode . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $exists = School::where('code', $code)->exists();
            $counter++;
        } while ($exists && $counter <= 999);

        return $exists ? null : $code; // Return null if no available code found
    }

    /**
     * Get school statistics
     */
    public function getSchoolStats(School $school)
    {
        return [
            'total_users' => $school->users()->count(),
            'total_students' => $school->students()->count(),
            'active_users' => $school->users()->whereNotNull('email_verified_at')->count(),
            'admin_users' => $school->users()->role('admin')->count(),
            'teacher_users' => $school->users()->role('teacher')->count(),
            'student_users' => $school->users()->role('student')->count(),
        ];
    }

    /**
     * Initialize school structure with default data
     */
    public function initializeSchoolStructure(School $school, array $selectedLevels = null): void
    {
        // Set school context for all operations
        session(['school_context' => $school->id]);

        try {
            // Create default levels if they don't exist for this school
            $this->createDefaultLevels($school, $selectedLevels);

            // Create default faculties if they don't exist for this school
            $this->createDefaultFaculties($school);

            // Create default grading scales if they don't exist for this school
            $this->createDefaultGradingScales($school);

            // Create default institute settings for this school
            $this->createDefaultInstituteSettings($school);

        } finally {
            // Clear school context
            session()->forget('school_context');
        }
    }

    /**
     * Create default levels for the school
     */
    private function createDefaultLevels(School $school, array $selectedLevels = null): void
    {
        // If no levels specified, create all default levels
        if ($selectedLevels === null) {
            $selectedLevels = ['school', 'college', 'bachelor'];
        }

        $levelMapping = [
            'school' => ['name' => 'School', 'order' => 1],
            'college' => ['name' => 'College', 'order' => 2],
            'bachelor' => ['name' => 'Bachelor', 'order' => 3],
        ];

        foreach ($selectedLevels as $levelKey) {
            if (isset($levelMapping[$levelKey])) {
                $levelData = $levelMapping[$levelKey];

                Level::firstOrCreate(
                    [
                        'name' => $levelData['name'],
                        'school_id' => $school->id
                    ],
                    [
                        'order' => $levelData['order'],
                        'school_id' => $school->id
                    ]
                );
            }
        }
    }

    /**
     * Create default faculties for the school
     */
    private function createDefaultFaculties(School $school): void
    {
        $defaultFaculties = [
            ['name' => 'Science', 'code' => 'SCI'],
            ['name' => 'Management', 'code' => 'MGT'],
            ['name' => 'Humanities', 'code' => 'HUM'],
            ['name' => 'Education', 'code' => 'EDU'],
        ];

        foreach ($defaultFaculties as $facultyData) {
            Faculty::firstOrCreate(
                [
                    'name' => $facultyData['name'],
                    'school_id' => $school->id
                ],
                [
                    'code' => $facultyData['code'],
                    'school_id' => $school->id
                ]
            );
        }
    }

    /**
     * Create default grading scales for the school
     */
    private function createDefaultGradingScales(School $school): void
    {
        // Create a default grading scale for the school
        $gradingScale = GradingScale::firstOrCreate(
            [
                'name' => 'Default Grading Scale',
                'school_id' => $school->id
            ],
            [
                'name' => 'Default Grading Scale',
                'description' => 'Standard grading scale for ' . $school->name,
                'is_default' => true,
                'pass_mark' => 40.00,
                'max_marks' => 100.00,
                'is_active' => true,
                'school_id' => $school->id
            ]
        );

        // Create grade ranges for this grading scale
        $defaultGrades = [
            ['grade' => 'A+', 'min_percentage' => 90, 'max_percentage' => 100, 'gpa' => 4.0, 'description' => 'Outstanding'],
            ['grade' => 'A', 'min_percentage' => 80, 'max_percentage' => 89, 'gpa' => 3.6, 'description' => 'Excellent'],
            ['grade' => 'B+', 'min_percentage' => 70, 'max_percentage' => 79, 'gpa' => 3.2, 'description' => 'Very Good'],
            ['grade' => 'B', 'min_percentage' => 60, 'max_percentage' => 69, 'gpa' => 2.8, 'description' => 'Good'],
            ['grade' => 'C+', 'min_percentage' => 50, 'max_percentage' => 59, 'gpa' => 2.4, 'description' => 'Satisfactory'],
            ['grade' => 'C', 'min_percentage' => 40, 'max_percentage' => 49, 'gpa' => 2.0, 'description' => 'Acceptable'],
            ['grade' => 'D', 'min_percentage' => 32, 'max_percentage' => 39, 'gpa' => 1.6, 'description' => 'Partially Acceptable'],
            ['grade' => 'F', 'min_percentage' => 0, 'max_percentage' => 31, 'gpa' => 0.0, 'description' => 'Fail'],
        ];

        foreach ($defaultGrades as $gradeData) {
            \DB::table('grade_ranges')->updateOrInsert(
                [
                    'grading_scale_id' => $gradingScale->id,
                    'grade' => $gradeData['grade']
                ],
                [
                    'grading_scale_id' => $gradingScale->id,
                    'grade' => $gradeData['grade'],
                    'min_percentage' => $gradeData['min_percentage'],
                    'max_percentage' => $gradeData['max_percentage'],
                    'gpa' => $gradeData['gpa'],
                    'description' => $gradeData['description'],
                    'is_passing' => $gradeData['grade'] !== 'F',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }

}
