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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_type', 100)->nullable(); // Type of user (super-admin, school-admin, etc.)
            $table->string('action', 255); // Action performed
            $table->string('resource_type', 100)->nullable(); // Type of resource affected
            $table->unsignedBigInteger('resource_id')->nullable(); // ID of affected resource
            $table->json('old_values')->nullable(); // Previous values
            $table->json('new_values')->nullable(); // New values
            $table->string('ip_address', 45)->nullable(); // IPv4 or IPv6
            $table->text('user_agent')->nullable(); // Browser/client info
            $table->string('session_id')->nullable(); // Session identifier
            $table->timestamp('timestamp')->useCurrent(); // When action occurred
            $table->enum('severity', ['info', 'warning', 'error', 'critical'])->default('info');
            $table->string('category', 100)->default('general'); // Category of action
            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['user_id', 'timestamp']);
            $table->index(['action', 'timestamp']);
            $table->index(['severity', 'timestamp']);
            $table->index(['category', 'timestamp']);
            $table->index(['resource_type', 'resource_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
