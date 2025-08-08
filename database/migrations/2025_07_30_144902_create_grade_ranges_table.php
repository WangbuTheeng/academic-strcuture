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
        Schema::create('grade_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_scale_id')->constrained()->onDelete('cascade');
            $table->string('grade', 5); // A+, A, B+, B, C+, C, D+, D, F
            $table->decimal('min_percentage', 5, 2);
            $table->decimal('max_percentage', 5, 2);
            $table->decimal('gpa', 3, 2);
            $table->string('description')->nullable();
            $table->boolean('is_passing')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index(['grading_scale_id', 'min_percentage', 'max_percentage'], 'gr_scale_percentage_idx');
            $table->index(['grade'], 'gr_grade_idx');

            // Ensure no overlapping ranges within same grading scale
            $table->unique(['grading_scale_id', 'grade']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_ranges');
    }
};
