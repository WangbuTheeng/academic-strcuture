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
        Schema::create('marksheet_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->enum('template_type', ['modern', 'classic', 'minimal', 'custom'])->default('modern');
            $table->foreignId('grading_scale_id')->constrained()->onDelete('cascade');
            $table->json('settings'); // Template customization settings
            $table->longText('custom_css')->nullable(); // Custom CSS for advanced customization
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_default', 'is_active']);
            $table->index('template_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marksheet_templates');
    }
};
