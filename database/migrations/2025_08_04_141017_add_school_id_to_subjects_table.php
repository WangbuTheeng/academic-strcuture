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
        // Check if school_id column doesn't already exist
        if (Schema::hasTable('subjects') && !Schema::hasColumn('subjects', 'school_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->onDelete('cascade');
                $table->index(['school_id']);

                // Add composite unique constraints for multi-tenant uniqueness
                $table->unique(['name', 'code', 'school_id'], 'unique_subject_per_school');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('subjects') && Schema::hasColumn('subjects', 'school_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropUnique('unique_subject_per_school');
                $table->dropForeign(['school_id']);
                $table->dropIndex(['school_id']);
                $table->dropColumn('school_id');
            });
        }
    }
};
