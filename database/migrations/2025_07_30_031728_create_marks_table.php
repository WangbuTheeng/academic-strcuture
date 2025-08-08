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
        Schema::create('marks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_id')->unsigned();
            $table->bigInteger('subject_id')->unsigned();
            $table->bigInteger('exam_id')->unsigned();

            $table->decimal('assess_marks', 5, 2)->nullable();
            $table->decimal('theory_marks', 5, 2)->nullable();
            $table->decimal('practical_marks', 5, 2)->nullable();

            $table->decimal('total', 5, 2)->storedAs('COALESCE(assess_marks, 0) + COALESCE(theory_marks, 0) + COALESCE(practical_marks, 0)');
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade', 5)->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->enum('result', ['Pass', 'Fail', 'Incomplete'])->nullable();

            $table->boolean('is_reexam')->default(false);
            $table->bigInteger('original_exam_id')->unsigned()->nullable();
            $table->decimal('grace_marks', 3, 2)->default(0.00);
            $table->text('carry_forward_reason')->nullable();

            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->bigInteger('created_by')->unsigned();
            $table->bigInteger('updated_by')->unsigned();

            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('original_exam_id')->references('id')->on('exams')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['student_id', 'subject_id', 'exam_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
