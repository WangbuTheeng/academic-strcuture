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
        Schema::table('exams', function (Blueprint $table) {
            // Check if semester_id column exists before trying to drop it
            if (Schema::hasColumn('exams', 'semester_id')) {
                // Try to drop foreign key constraint first (if it exists)
                try {
                    $table->dropForeign(['semester_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }

                // Drop the semester_id column
                $table->dropColumn('semester_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Add back the semester_id column
            $table->bigInteger('semester_id')->unsigned()->nullable()->after('academic_year_id');

            // Add back the foreign key constraint
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');
        });
    }
};
