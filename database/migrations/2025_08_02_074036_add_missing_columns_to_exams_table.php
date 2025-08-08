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
        Schema::table('exams', function (Blueprint $table) {
            // Add missing columns for approval and publishing workflow
            $table->bigInteger('approved_by')->unsigned()->nullable()->after('created_by');
            $table->bigInteger('published_by')->unsigned()->nullable()->after('approved_by');
            $table->timestamp('approved_at')->nullable()->after('published_by');
            $table->timestamp('published_at')->nullable()->after('approved_at');

            // Add missing has_assessment column
            $table->boolean('has_assessment')->default(false)->after('has_practical');

            // Add foreign key constraints
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('published_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['published_by']);
            $table->dropColumn(['approved_by', 'published_by', 'approved_at', 'published_at', 'has_assessment']);
        });
    }
};
