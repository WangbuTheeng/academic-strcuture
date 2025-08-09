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
        Schema::table('institute_settings', function (Blueprint $table) {
            // Add academic fields if they don't exist
            if (!Schema::hasColumn('institute_settings', 'school_motto')) {
                $table->string('school_motto', 500)->nullable()->after('principal_email');
            }
            if (!Schema::hasColumn('institute_settings', 'established_year')) {
                $table->integer('established_year')->nullable()->after('school_motto');
            }
            if (!Schema::hasColumn('institute_settings', 'affiliation')) {
                $table->string('affiliation', 255)->nullable()->after('established_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institute_settings', function (Blueprint $table) {
            $table->dropColumn(['school_motto', 'established_year', 'affiliation']);
        });
    }
};
