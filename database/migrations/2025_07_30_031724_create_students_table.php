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
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Personal Details
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('date_of_birth');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('blood_group', 5)->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('caste', 50)->nullable();
            $table->string('nationality', 50)->default('Nepali');
            $table->string('mother_tongue', 50)->nullable();

            // Contact Information
            $table->string('phone', 15);
            $table->string('email', 100)->nullable();
            $table->text('address');
            $table->text('temporary_address')->nullable();

            // Emergency Contact
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 15)->nullable();
            $table->string('emergency_contact_relation', 20)->nullable();

            // Guardian Information
            $table->string('guardian_name', 100);
            $table->string('guardian_relation', 20);
            $table->string('guardian_phone', 15);
            $table->string('guardian_email', 100)->nullable();

            // Legal Documentation
            $table->string('citizenship_number', 20)->nullable();
            $table->date('citizenship_issue_date')->nullable();
            $table->string('citizenship_issue_district', 50)->nullable();
            $table->string('citizenship_document', 255)->nullable();

            // Academic History
            $table->string('previous_school_name', 150)->nullable();
            $table->string('transfer_certificate_no', 50)->nullable();
            $table->date('transfer_certificate_date')->nullable();
            $table->string('migration_certificate_no', 50)->nullable();

            // Special Needs & Accessibility
            $table->enum('disability_status', ['none', 'visual', 'hearing', 'mobility', 'learning', 'other'])->default('none');
            $table->text('special_needs')->nullable();

            // Admission Information
            $table->string('admission_number', 20)->unique(); // ADM-2078-001
            $table->date('admission_date');
            $table->string('photo_url', 255)->nullable();
            $table->enum('status', ['active', 'inactive', 'graduated', 'transferred', 'dropped'])->default('active');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
