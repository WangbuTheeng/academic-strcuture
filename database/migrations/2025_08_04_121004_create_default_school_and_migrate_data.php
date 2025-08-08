<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create default school for existing data
        $defaultSchool = School::create([
            'name' => 'Default School',
            'code' => 'DEFAULT001',
            'password' => 'default123',
            'email' => 'admin@default.school',
            'status' => 'active',
            'settings' => [
                'is_default' => true,
                'migrated_from_single_tenant' => true,
                'migration_date' => now()->toDateString()
            ],
        ]);

        // Migrate existing data to default school
        $this->migrateExistingData($defaultSchool->id);

        echo "✅ Default school created and existing data migrated successfully!\n";
        echo "📧 Default School Code: DEFAULT001\n";
        echo "🔑 Default School Password: default123\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove school_id from all tables
        $this->removeSchoolIdFromAllTables();

        // Delete default school
        School::where('code', 'DEFAULT001')->delete();

        echo "✅ Default school removed and school_id cleared from all tables\n";
    }

    /**
     * Migrate existing data to default school
     */
    private function migrateExistingData($schoolId)
    {
        // Core academic structure tables
        $coreAcademicTables = [
            'levels', 'faculties', 'departments', 'classes', 'programs',
            'academic_years', 'semesters', 'subjects', 'grading_scales',
            'program_subjects', 'program_classes', 'class_subjects'
        ];

        // Student and enrollment tables
        $studentTables = [
            'students', 'student_enrollments', 'student_subjects', 'student_documents'
        ];

        // Teaching and examination tables
        $teachingTables = [
            'teacher_subjects', 'exams', 'marks', 'mark_logs', 'grace_marks'
        ];

        // System and administrative tables
        $systemTables = [
            'institute_settings', 'backup_records', 'promotion_records',
            'fee_structures', 'student_bills', 'bill_items', 'payments', 'payment_receipts'
        ];

        // Combine all tables that need school_id
        $allTables = array_merge($coreAcademicTables, $studentTables, $teachingTables, $systemTables);

        foreach ($allTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                $count = DB::table($tableName)->whereNull('school_id')->count();
                if ($count > 0) {
                    DB::table($tableName)->whereNull('school_id')->update(['school_id' => $schoolId]);
                    echo "   📊 Migrated {$count} records in {$tableName}\n";
                }
            }
        }

        // Migrate existing users (except super-admin) to default school
        $userCount = DB::table('users')
            ->whereNull('school_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('model_has_roles')
                      ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                      ->whereColumn('model_has_roles.model_id', 'users.id')
                      ->where('roles.name', 'super-admin');
            })
            ->count();

        if ($userCount > 0) {
            DB::table('users')
                ->whereNull('school_id')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('model_has_roles')
                          ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                          ->whereColumn('model_has_roles.model_id', 'users.id')
                          ->where('roles.name', 'super-admin');
                })
                ->update(['school_id' => $schoolId]);

            echo "   👥 Migrated {$userCount} users to default school\n";
        }
    }

    /**
     * Remove school_id from all tables
     */
    private function removeSchoolIdFromAllTables()
    {
        $allTables = [
            'levels', 'faculties', 'departments', 'classes', 'programs',
            'academic_years', 'semesters', 'subjects', 'grading_scales',
            'program_subjects', 'program_classes', 'class_subjects',
            'students', 'student_enrollments', 'student_subjects', 'student_documents',
            'teacher_subjects', 'exams', 'marks', 'mark_logs', 'grace_marks',
            'institute_settings', 'backup_records', 'promotion_records',
            'fee_structures', 'student_bills', 'bill_items', 'payments', 'payment_receipts'
        ];

        foreach ($allTables as $tableName) {
            if (Schema::hasTable($tableName)) {
                DB::table($tableName)->update(['school_id' => null]);
            }
        }
    }
};
