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
        Schema::table('academic_years', function (Blueprint $table) {
            // Drop the existing global unique constraint on name
            $table->dropUnique(['name']);
            
            // Add composite unique constraint for name + school_id
            $table->unique(['name', 'school_id'], 'academic_years_name_school_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('academic_years_name_school_unique');
            
            // Add back the global unique constraint (this might fail if there are duplicates)
            $table->unique('name');
        });
    }
};
