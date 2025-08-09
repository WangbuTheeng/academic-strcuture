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
        Schema::table('marksheet_templates', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('marksheet_templates', 'institute_settings_id')) {
                $table->foreignId('institute_settings_id')->nullable()->after('grading_scale_id')->constrained()->onDelete('cascade');
                $table->index(['institute_settings_id', 'is_active']);
            }

            if (!Schema::hasColumn('marksheet_templates', 'is_global')) {
                $table->boolean('is_global')->default(false)->after('is_active');
                $table->index(['is_global', 'is_active']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marksheet_templates', function (Blueprint $table) {
            $table->dropForeign(['institute_settings_id']);
            $table->dropIndex(['institute_settings_id', 'is_active']);
            $table->dropIndex(['is_global', 'is_active']);
            $table->dropColumn(['institute_settings_id', 'is_global']);
        });
    }
};
