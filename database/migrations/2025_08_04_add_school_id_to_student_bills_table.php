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
        Schema::table('student_bills', function (Blueprint $table) {
            // Add school_id column if it doesn't exist
            if (!Schema::hasColumn('student_bills', 'school_id')) {
                $table->foreignId('school_id')->after('id')->constrained('schools')->onDelete('cascade');
                $table->index(['school_id', 'student_id']);
                $table->index(['school_id', 'status']);
                $table->index(['school_id', 'bill_date']);
            }
        });

        // Update existing records to have school_id based on student's school
        if (Schema::hasTable('student_bills') && Schema::hasTable('students')) {
            DB::statement('
                UPDATE student_bills sb
                JOIN students s ON sb.student_id = s.id
                SET sb.school_id = s.school_id
                WHERE sb.school_id IS NULL
            ');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_bills', function (Blueprint $table) {
            if (Schema::hasColumn('student_bills', 'school_id')) {
                $table->dropForeign(['school_id']);
                $table->dropIndex(['school_id', 'student_id']);
                $table->dropIndex(['school_id', 'status']);
                $table->dropIndex(['school_id', 'bill_date']);
                $table->dropColumn('school_id');
            }
        });
    }
};
