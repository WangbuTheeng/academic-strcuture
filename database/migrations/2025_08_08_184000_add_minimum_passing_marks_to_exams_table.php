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
            // Add minimum passing marks for theory and practical components
            $table->decimal('theory_pass_marks', 5, 2)->default(0)->after('theory_max');
            $table->decimal('practical_pass_marks', 5, 2)->default(0)->after('practical_max');
            $table->decimal('assess_pass_marks', 5, 2)->default(0)->after('assess_max');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['theory_pass_marks', 'practical_pass_marks', 'assess_pass_marks']);
        });
    }
};
