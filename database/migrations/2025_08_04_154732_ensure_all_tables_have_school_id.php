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
        $tablesToUpdate = [
            'levels', 'faculties', 'grading_scales', 'grade_ranges'
        ];

        foreach ($tablesToUpdate as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->onDelete('cascade');
                    $table->index(['school_id']);
                });

                echo "✅ Added school_id to {$table} table\n";
            }
        }

        // Update existing records to have school_id = 1 (default school)
        foreach ($tablesToUpdate as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
                DB::table($table)->whereNull('school_id')->update(['school_id' => 1]);
                echo "✅ Updated null school_id records in {$table} table\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablesToUpdate = [
            'levels', 'faculties', 'grading_scales', 'grade_ranges'
        ];

        foreach ($tablesToUpdate as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['school_id']);
                    $table->dropIndex(['school_id']);
                    $table->dropColumn('school_id');
                });
            }
        }
    }
};
