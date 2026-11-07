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
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'opening_balance')) {
            DB::statement("ALTER TABLE `customers` MODIFY `opening_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00");
        }

        if (Schema::hasTable('vendor_ledgers') && Schema::hasColumn('vendor_ledgers', 'opening_balance')) {
            DB::statement("ALTER TABLE `vendor_ledgers` MODIFY `opening_balance` TEXT NULL");
        }

        if (Schema::hasTable('vendors') && Schema::hasColumn('vendors', 'opening_balance')) {
            DB::statement("ALTER TABLE `vendors` MODIFY `opening_balance` VARCHAR(255) NULL DEFAULT '0'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Defaults are safe to retain
    }
};
