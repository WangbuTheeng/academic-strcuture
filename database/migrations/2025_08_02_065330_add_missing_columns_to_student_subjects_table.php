<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_subjects', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('student_subjects', 'student_enrollment_id')) {
                $table->bigInteger('student_enrollment_id')->unsigned()->after('id');
            }
            if (!Schema::hasColumn('student_subjects', 'subject_id')) {
                $table->bigInteger('subject_id')->unsigned()->after('student_enrollment_id');
            }
            if (!Schema::hasColumn('student_subjects', 'date_added')) {
                $table->date('date_added')->default(now())->after('subject_id');
            }
            if (!Schema::hasColumn('student_subjects', 'status')) {
                $table->enum('status', ['active', 'dropped'])->default('active')->after('date_added');
            }
        });

        // Add foreign key constraints if they don't exist
        try {
            Schema::table('student_subjects', function (Blueprint $table) {
                $table->foreign('student_enrollment_id')->references('id')->on('student_enrollments')->onDelete('cascade');
                $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // Foreign keys might already exist, ignore the error
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_subjects', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['student_enrollment_id']);
            $table->dropForeign(['subject_id']);

            // Drop columns
            $table->dropColumn(['student_enrollment_id', 'subject_id', 'date_added', 'status']);
        });
    }
};
