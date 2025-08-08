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
        Schema::create('school_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->integer('total_students')->default(0);
            $table->integer('total_teachers')->default(0);
            $table->integer('total_classes')->default(0);
            $table->integer('total_subjects')->default(0);
            $table->integer('total_exams')->default(0);
            $table->timestamp('last_login')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->json('feature_usage')->nullable(); // Track feature usage
            $table->json('performance_metrics')->nullable(); // Store performance data
            $table->timestamps();

            // Ensure one statistics record per school
            $table->unique('school_id');
            $table->index(['school_id', 'last_activity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_statistics');
    }
};
