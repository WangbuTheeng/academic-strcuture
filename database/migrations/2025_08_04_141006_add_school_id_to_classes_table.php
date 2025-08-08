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
        if (Schema::hasTable('classes') && !Schema::hasColumn('classes', 'school_id')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->onDelete('cascade');
                $table->index(['school_id']);

                // Add composite unique constraints for multi-tenant uniqueness
                $table->unique(['name', 'level_id', 'school_id'], 'unique_class_per_level_school');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('classes') && Schema::hasColumn('classes', 'school_id')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->dropUnique('unique_class_per_level_school');
                $table->dropForeign(['school_id']);
                $table->dropIndex(['school_id']);
                $table->dropColumn('school_id');
            });
        }
    }
};
