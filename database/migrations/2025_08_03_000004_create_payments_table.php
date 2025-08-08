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
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // Student and Bill Information
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('bill_id')->constrained('student_bills')->onDelete('cascade');
            
            // Payment Details
            $table->string('payment_number', 50)->unique(); // PAY-2025-001
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'online', 'cheque', 'card', 'mobile_wallet'])->default('cash');
            
            // Payment Method Specific Information
            $table->string('reference_number', 100)->nullable(); // Bank reference, transaction ID, cheque number
            $table->string('bank_name', 100)->nullable();
            $table->string('cheque_number', 50)->nullable();
            $table->date('cheque_date')->nullable();
            
            // Status and Verification
            $table->enum('status', ['pending', 'verified', 'failed', 'cancelled'])->default('pending');
            $table->boolean('is_verified')->default(false);
            $table->date('verification_date')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Additional Information
            $table->text('notes')->nullable();
            $table->text('remarks')->nullable();
            
            // Audit Information
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['student_id', 'payment_date']);
            $table->index(['bill_id', 'status']);
            $table->index(['payment_number']);
            $table->index(['payment_method', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
