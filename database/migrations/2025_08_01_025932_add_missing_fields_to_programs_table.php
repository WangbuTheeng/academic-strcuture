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
            if (!Schema::hasColumn('programs', 'code')) {
                $table->string('code', 10)->nullable()->after('name');
            }
            if (!Schema::hasColumn('programs', 'program_type')) {
                $table->enum('program_type', ['semester', 'yearly'])->default('semester')->after('degree_type');
            }
            if (!Schema::hasColumn('programs', 'description')) {
                $table->text('description')->nullable()->after('program_type');
            }
            if (!Schema::hasColumn('programs', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });

        // Update existing programs with generated codes if they don't have codes
        $programs = \App\Models\Program::whereNull('code')->orWhere('code', '')->get();
        foreach ($programs as $program) {
            $code = strtoupper(substr($program->name, 0, 3)) . $program->id;
            $program->update(['code' => $code]);
        }

        // Add unique constraint to code if it doesn't exist
        if (!$this->hasUniqueConstraint('programs', 'code')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->string('code', 10)->unique()->change();
            });
        }
    }

    private function hasUniqueConstraint($table, $column)
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table} WHERE Column_name = '{$column}' AND Non_unique = 0");
        return count($indexes) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['code', 'program_type', 'description', 'is_active']);
        });
    }
};
