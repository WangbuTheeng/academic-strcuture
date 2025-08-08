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
        Schema::create('exams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->enum('exam_type', ['assessment', 'terminal', 'quiz', 'project', 'practical', 'final']);
            $table->bigInteger('academic_year_id')->unsigned();
            $table->bigInteger('semester_id')->unsigned()->nullable();
            $table->bigInteger('class_id')->unsigned()->nullable();
            $table->bigInteger('program_id')->unsigned()->nullable();
            $table->bigInteger('subject_id')->unsigned()->nullable();
            $table->bigInteger('grading_scale_id')->unsigned()->nullable();

            $table->integer('max_marks');
            $table->integer('theory_max');
            $table->integer('practical_max')->default(0);
            $table->integer('assess_max')->default(0);
            $table->boolean('has_practical')->default(false);

            $table->dateTime('submission_deadline')->nullable();
            $table->enum('result_status', [
                'draft', 'scheduled', 'ongoing', 'submitted', 'approved', 'published', 'locked'
            ])->default('scheduled');
            $table->boolean('is_locked')->default(false);
            $table->timestamp('approval_date')->nullable();

            $table->date('start_date');
            $table->date('end_date');
            $table->text('remarks')->nullable();

            $table->bigInteger('created_by')->unsigned();
            $table->timestamps();

            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
            $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('set null');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('set null');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            $table->foreign('grading_scale_id')->references('id')->on('grading_scales')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
