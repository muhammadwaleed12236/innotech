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
        // Change note to TEXT in stock_movements
        if (Schema::hasTable('stock_movements')) {
            DB::statement("ALTER TABLE `stock_movements` MODIFY `note` TEXT NULL;");
        }

        // Change remarks and variant_key to TEXT in product_batches
        if (Schema::hasTable('product_batches')) {
            DB::statement("ALTER TABLE `product_batches` MODIFY `remarks` TEXT NULL;");
            DB::statement("ALTER TABLE `product_batches` MODIFY `variant_key` TEXT NULL;");
        }

        // Change remarks and variant_key to TEXT in product_serials
        if (Schema::hasTable('product_serials')) {
            DB::statement("ALTER TABLE `product_serials` MODIFY `remarks` TEXT NULL;");
            DB::statement("ALTER TABLE `product_serials` MODIFY `variant_key` TEXT NULL;");
        }

        // Change remarks to TEXT in warehouse_stocks
        if (Schema::hasTable('warehouse_stocks')) {
            DB::statement("ALTER TABLE `warehouse_stocks` MODIFY `remarks` TEXT NULL;");
        }

        // Change variant_key and variant_name to TEXT in stock_adjustments if table exists
        if (Schema::hasTable('stock_adjustments')) {
            DB::statement("ALTER TABLE `stock_adjustments` MODIFY `variant_key` TEXT NULL;");
            DB::statement("ALTER TABLE `stock_adjustments` MODIFY `variant_name` TEXT NULL;");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
