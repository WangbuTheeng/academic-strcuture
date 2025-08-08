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
        Schema::create('bill_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // Bill Reference
            $table->foreignId('bill_id')->constrained('student_bills')->onDelete('cascade');
            $table->foreignId('fee_structure_id')->nullable()->constrained('fee_structures')->onDelete('set null');
            
            // Item Details
            $table->string('fee_category', 100);
            $table->string('description', 200);
            $table->decimal('unit_amount', 10, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total_amount', 10, 2);
            
            // Discount Information
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            
            // Status
            $table->boolean('is_paid')->default(false);
            $table->decimal('paid_amount', 10, 2)->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['bill_id', 'fee_category']);
            $table->index(['is_paid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_items');
    }
};
