<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $financeTables = [
            'fee_structures',
            'payments',
            'payment_receipts',
            'bill_items'
        ];

        foreach ($financeTables as $table) {
            if (Schema::hasTable($table)) {
                // Check if school_id column already exists
                if (!Schema::hasColumn($table, 'school_id')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->foreignId('school_id')->nullable()->after('id')->constrained('schools');
                        $table->index(['school_id']);
                    });
                }

                // Set default school_id for existing records
                DB::table($table)->whereNull('school_id')->update(['school_id' => 1]);

                // Make school_id non-nullable after setting defaults
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('school_id')->nullable(false)->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $financeTables = [
            'fee_structures',
            'payments',
            'payment_receipts',
            'bill_items'
        ];

        foreach ($financeTables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['school_id']);
                    $table->dropIndex(['school_id']);
                    $table->dropColumn('school_id');
                });
            }
        }
    }
};
