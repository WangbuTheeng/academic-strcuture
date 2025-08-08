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
        Schema::create('promotion_records', function (Blueprint $table) {
            $table->id();

            // Student and class information
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('to_class_id')->constrained('classes')->onDelete('cascade');

            // Academic year information
            $table->foreignId('from_academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('to_academic_year_id')->constrained('academic_years')->onDelete('cascade');

            // Promotion details
            $table->enum('status', ['promoted', 'retained', 'transferred'])->default('promoted');
            $table->text('remarks')->nullable();

            // Audit information
            $table->foreignId('promoted_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('promoted_at');

            $table->timestamps();

            // Indexes for performance
            $table->index(['student_id', 'from_academic_year_id']);
            $table->index(['status']);
            $table->index(['promoted_at']);

            // Ensure one promotion record per student per academic year
            $table->unique(['student_id', 'from_academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_records');
    }
};
