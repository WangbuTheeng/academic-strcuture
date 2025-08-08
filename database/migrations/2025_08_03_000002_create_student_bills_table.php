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
        Schema::create('student_bills', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // Student and Academic Information
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('set null');
            
            // Bill Information
            $table->string('bill_number', 50)->unique(); // BILL-2025-001
            $table->string('bill_title', 200)->default('Student Fee Bill');
            $table->text('description')->nullable();
            
            // Financial Details
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('late_fee_amount', 8, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance_amount', 12, 2)->default(0);
            
            // Dates
            $table->date('bill_date');
            $table->date('due_date');
            $table->date('last_payment_date')->nullable();
            
            // Status and Tracking
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->boolean('is_locked')->default(false);
            $table->text('notes')->nullable();
            
            // Audit Information
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['student_id', 'academic_year_id']);
            $table->index(['status', 'due_date']);
            $table->index(['bill_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_bills');
    }
};
