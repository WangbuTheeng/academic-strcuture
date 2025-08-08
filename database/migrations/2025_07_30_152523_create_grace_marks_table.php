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
        Schema::create('grace_marks', function (Blueprint $table) {
            $table->id();

            // Mark and grace mark information
            $table->foreignId('mark_id')->constrained()->onDelete('cascade');
            $table->decimal('grace_marks', 5, 2);
            $table->text('reason');
            $table->text('justification');

            // Status and approval information
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('approval_remarks')->nullable();
            $table->text('rejection_reason')->nullable();

            // Audit information
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');

            // Timestamps for workflow
            $table->timestamp('requested_at');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['status']);
            $table->index(['requested_at']);
            $table->index(['approved_at']);
            $table->index(['mark_id']);

            // Ensure one grace mark request per mark
            $table->unique(['mark_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grace_marks');
    }
};
