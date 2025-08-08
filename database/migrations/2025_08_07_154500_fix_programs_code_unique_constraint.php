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
        Schema::table('programs', function (Blueprint $table) {
            // Drop the existing global unique constraint on code
            $table->dropUnique(['code']);
            
            // Add composite unique constraint for code + school_id
            $table->unique(['code', 'school_id'], 'programs_code_school_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('programs_code_school_unique');
            
            // Add back the global unique constraint (this might fail if there are duplicates)
            $table->unique('code');
        });
    }
};
