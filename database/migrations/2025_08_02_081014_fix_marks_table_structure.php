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
        Schema::table('marks', function (Blueprint $table) {
            // Drop the computed total column and recreate as regular column
            $table->dropColumn('total');
        });

        Schema::table('marks', function (Blueprint $table) {
            // Add regular total column
            $table->decimal('total', 5, 2)->nullable()->after('practical_marks');

            // Update status enum to include more values
            $table->dropColumn('status');
        });

        Schema::table('marks', function (Blueprint $table) {
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft')->after('result');

            // Add missing columns
            $table->bigInteger('approved_by')->unsigned()->nullable()->after('created_by');
            $table->timestamp('submitted_at')->nullable()->after('approved_by');
            $table->timestamp('approved_at')->nullable()->after('submitted_at');
            $table->text('grace_reason')->nullable()->after('grace_marks');

            // Update foreign key for approved_by
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marks', function (Blueprint $table) {
            // Remove added columns
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approved_by', 'submitted_at', 'approved_at', 'grace_reason']);

            // Revert status enum
            $table->dropColumn('status');
        });

        Schema::table('marks', function (Blueprint $table) {
            $table->enum('status', ['draft', 'final'])->default('draft');

            // Drop regular total column
            $table->dropColumn('total');
        });

        Schema::table('marks', function (Blueprint $table) {
            // Recreate computed total column
            $table->decimal('total', 5, 2)->storedAs('COALESCE(assess_marks, 0) + COALESCE(theory_marks, 0) + COALESCE(practical_marks, 0)');
        });
    }
};
