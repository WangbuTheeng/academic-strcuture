<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->onDelete('cascade');
                    $table->index(['school_id']);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
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
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['school_id']);
                    $table->dropIndex(['school_id']);
                    $table->dropColumn('school_id');
                });
            }
        }
    }
};
