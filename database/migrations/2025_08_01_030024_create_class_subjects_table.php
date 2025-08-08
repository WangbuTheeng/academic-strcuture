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
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('class_id')->unsigned();
            $table->bigInteger('subject_id')->unsigned();
            $table->boolean('is_compulsory')->default(true);
            $table->decimal('credit_hours', 3, 1)->default(3.0);
            $table->integer('year_no')->nullable(); // For yearly programs
            $table->bigInteger('semester_id')->unsigned()->nullable(); // For semester programs
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');

            $table->unique(['class_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subjects');
    }
};
