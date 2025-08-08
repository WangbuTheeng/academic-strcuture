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
        Schema::create('institute_settings', function (Blueprint $table) {
            $table->id();

            // Institution Information
            $table->string('institution_name');
            $table->text('institution_address');
            $table->string('institution_phone')->nullable();
            $table->string('institution_email')->nullable();
            $table->string('institution_website')->nullable();
            $table->string('institution_logo')->nullable();
            $table->string('institution_seal')->nullable();

            // Principal Information
            $table->string('principal_name');
            $table->string('principal_phone')->nullable();
            $table->string('principal_email')->nullable();

            // Academic Configuration
            $table->integer('academic_year_start_month')->default(4); // Chaitra (April)
            $table->integer('academic_year_end_month')->default(3);   // Falgun (March)
            $table->foreignId('default_grading_scale_id')->nullable()->constrained('grading_scales')->onDelete('set null');

            // Setup Status
            $table->boolean('setup_completed')->default(false);
            $table->timestamp('setup_completed_at')->nullable();

            // Additional Settings (JSON)
            $table->json('settings_data')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['setup_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institute_settings');
    }
};
