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
        Schema::table('student_bills', function (Blueprint $table) {
            // Drop the existing global unique constraint on bill_number
            $table->dropUnique(['bill_number']);

            // Add composite unique constraint for bill_number + school_id
            $table->unique(['bill_number', 'school_id'], 'student_bills_bill_number_school_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_bills', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('student_bills_bill_number_school_unique');

            // Add back the global unique constraint (this might fail if there are duplicates)
            $table->unique('bill_number');
        });
    }
};
