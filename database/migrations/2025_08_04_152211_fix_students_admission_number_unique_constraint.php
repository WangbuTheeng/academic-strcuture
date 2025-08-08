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
        Schema::table('students', function (Blueprint $table) {
            // Drop the existing unique constraint on admission_number
            $table->dropUnique(['admission_number']);

            // Add composite unique constraint for admission_number + school_id
            $table->unique(['admission_number', 'school_id'], 'students_admission_school_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('students_admission_school_unique');

            // Add back the original unique constraint (this might fail if there are duplicates)
            $table->unique('admission_number');
        });
    }
};
