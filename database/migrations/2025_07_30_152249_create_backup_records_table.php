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
        Schema::create('backup_records', function (Blueprint $table) {
            $table->id();

            // Backup information
            $table->string('name');
            $table->enum('type', ['full', 'database', 'files']);
            $table->text('description')->nullable();

            // Status and file information
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable(); // in bytes
            $table->text('error_message')->nullable();

            // Audit information
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['status']);
            $table->index(['type']);
            $table->index(['created_at']);
            $table->index(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_records');
    }
};
