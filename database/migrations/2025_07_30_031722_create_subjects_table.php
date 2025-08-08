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
        Schema::create('subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('department_id')->unsigned();
            $table->string('name', 100);
            $table->string('code', 10);
            $table->decimal('credit_hours', 3, 1)->default(3.0);
            $table->enum('subject_type', ['core', 'elective', 'practical', 'project'])->default('core');
            $table->integer('max_assess')->default(0);
            $table->integer('max_theory')->default(0);
            $table->integer('max_practical')->default(0);
            $table->boolean('is_practical')->default(false);
            $table->boolean('has_internal')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
