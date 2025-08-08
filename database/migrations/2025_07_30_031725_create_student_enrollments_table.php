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
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_id')->unsigned();
            $table->bigInteger('academic_year_id')->unsigned();
            $table->bigInteger('class_id')->unsigned();
            $table->bigInteger('program_id')->unsigned();
            $table->string('roll_no', 10);
            $table->string('section', 5)->nullable();
            $table->date('enrollment_date');
            $table->enum('status', ['active', 'dropped', 'transferred'])->default('active');
            $table->enum('academic_standing', ['good', 'probation', 'repeat', 'dismissed'])->nullable();
            $table->integer('backlog_count')->default(0);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');

            $table->unique(['student_id', 'academic_year_id']);
            $table->unique(['class_id', 'academic_year_id', 'roll_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
