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
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // Payment Reference
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            
            // Receipt Information
            $table->string('receipt_number', 50)->unique(); // REC-2025-001
            $table->date('receipt_date');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'online', 'cheque', 'card', 'mobile_wallet']);
            
            // Receipt Status
            $table->boolean('is_duplicate')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->date('cancelled_date')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            // Additional Information
            $table->text('remarks')->nullable();
            $table->json('receipt_data')->nullable(); // Store additional receipt data
            
            // Audit Information
            $table->foreignId('issued_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['payment_id']);
            $table->index(['student_id', 'receipt_date']);
            $table->index(['receipt_number']);
            $table->index(['is_duplicate', 'is_cancelled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
