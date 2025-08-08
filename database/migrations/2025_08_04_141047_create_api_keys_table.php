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
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name'); // Human-readable name for the key
            $table->string('key_hash')->unique(); // Hashed API key
            $table->json('permissions')->nullable(); // Specific permissions for this key
            $table->integer('rate_limit_per_minute')->default(60); // Rate limit
            $table->bigInteger('usage_count')->default(0); // Track usage
            $table->timestamp('last_used_at')->nullable(); // Last usage timestamp
            $table->timestamp('expires_at')->nullable(); // Expiration date
            $table->boolean('is_active')->default(true); // Active status
            $table->timestamps();

            // Indexes for performance
            $table->index(['key_hash']);
            $table->index(['user_id', 'is_active']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
