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
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // Academic Configuration
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('level_id')->nullable()->constrained('levels')->onDelete('cascade');
            $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('cascade');
            
            // Fee Details
            $table->string('fee_category', 100); // tuition, laboratory, library, examination, activity, transport, hostel, miscellaneous
            $table->string('fee_name', 150);
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_active')->default(true);
            
            // Due Date Configuration
            $table->integer('due_date_offset')->default(30); // Days from bill generation
            $table->enum('billing_frequency', ['monthly', 'quarterly', 'semester', 'annual'])->default('semester');
            
            // Late Fee Configuration
            $table->decimal('late_fee_amount', 8, 2)->default(0);
            $table->integer('grace_period_days')->default(0);
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['academic_year_id', 'level_id', 'program_id']);
            $table->index(['fee_category', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
