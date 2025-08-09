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
            // Add level_id field for level-based exams
            $table->foreignId('level_id')->nullable()->after('academic_year_id')->constrained('levels')->onDelete('cascade');

            // Add exam_scope field to distinguish between class-specific and level-wide exams
            $table->enum('exam_scope', ['class', 'level', 'school'])->default('class')->after('exam_type');

            // Add index for better performance
            $table->index(['level_id', 'exam_scope']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropIndex(['level_id', 'exam_scope']);
            $table->dropColumn(['exam_scope']);
            $table->dropForeign(['level_id']);
            $table->dropColumn('level_id');
        });
    }
};
