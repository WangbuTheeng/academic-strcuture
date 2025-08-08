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
        // Add soft deletes to bill_items table
        Schema::table('bill_items', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to payment_receipts table
        Schema::table('payment_receipts', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('payment_receipts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
