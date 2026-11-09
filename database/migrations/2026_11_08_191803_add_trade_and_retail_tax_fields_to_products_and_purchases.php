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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'retail_price')) {
                $table->decimal('retail_price', 15, 2)->default(0)->nullable()->after('sale_price_per_piece');
            }
            if (!Schema::hasColumn('products', 'trade_price')) {
                $table->decimal('trade_price', 15, 2)->default(0)->nullable()->after('retail_price');
            }
            if (!Schema::hasColumn('products', 'is_third_schedule')) {
                $table->boolean('is_third_schedule')->default(false)->after('trade_price');
            }
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_items', 'retail_price')) {
                $table->decimal('retail_price', 15, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('purchase_items', 'retail_value')) {
                $table->decimal('retail_value', 15, 2)->default(0)->after('retail_price');
            }
            if (!Schema::hasColumn('purchase_items', 'trade_value')) {
                $table->decimal('trade_value', 15, 2)->default(0)->after('retail_value');
            }
            if (!Schema::hasColumn('purchase_items', 'value_after_discount')) {
                $table->decimal('value_after_discount', 15, 2)->default(0)->after('item_discount');
            }
            if (!Schema::hasColumn('purchase_items', 'is_third_schedule')) {
                $table->boolean('is_third_schedule')->default(false)->after('value_after_discount');
            }
            if (!Schema::hasColumn('purchase_items', 'sales_tax_on_retail')) {
                $table->decimal('sales_tax_on_retail', 15, 2)->default(0)->after('gst_amount');
            }
            if (!Schema::hasColumn('purchase_items', 'sales_tax_on_trade')) {
                $table->decimal('sales_tax_on_trade', 15, 2)->default(0)->after('sales_tax_on_retail');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'total_retail_value')) {
                $table->decimal('total_retail_value', 15, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('purchases', 'total_trade_value')) {
                $table->decimal('total_trade_value', 15, 2)->default(0)->after('total_retail_value');
            }
            if (!Schema::hasColumn('purchases', 'total_value_after_discount')) {
                $table->decimal('total_value_after_discount', 15, 2)->default(0)->after('total_trade_value');
            }
            if (!Schema::hasColumn('purchases', 'total_sales_tax_on_retail')) {
                $table->decimal('total_sales_tax_on_retail', 15, 2)->default(0)->after('total_gst');
            }
            if (!Schema::hasColumn('purchases', 'total_sales_tax_on_trade')) {
                $table->decimal('total_sales_tax_on_trade', 15, 2)->default(0)->after('total_sales_tax_on_retail');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'total_sales_tax_on_trade')) {
                $table->dropColumn('total_sales_tax_on_trade');
            }
            if (Schema::hasColumn('purchases', 'total_sales_tax_on_retail')) {
                $table->dropColumn('total_sales_tax_on_retail');
            }
            if (Schema::hasColumn('purchases', 'total_value_after_discount')) {
                $table->dropColumn('total_value_after_discount');
            }
            if (Schema::hasColumn('purchases', 'total_trade_value')) {
                $table->dropColumn('total_trade_value');
            }
            if (Schema::hasColumn('purchases', 'total_retail_value')) {
                $table->dropColumn('total_retail_value');
            }
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_items', 'sales_tax_on_trade')) {
                $table->dropColumn('sales_tax_on_trade');
            }
            if (Schema::hasColumn('purchase_items', 'sales_tax_on_retail')) {
                $table->dropColumn('sales_tax_on_retail');
            }
            if (Schema::hasColumn('purchase_items', 'is_third_schedule')) {
                $table->dropColumn('is_third_schedule');
            }
            if (Schema::hasColumn('purchase_items', 'value_after_discount')) {
                $table->dropColumn('value_after_discount');
            }
            if (Schema::hasColumn('purchase_items', 'trade_value')) {
                $table->dropColumn('trade_value');
            }
            if (Schema::hasColumn('purchase_items', 'retail_value')) {
                $table->dropColumn('retail_value');
            }
            if (Schema::hasColumn('purchase_items', 'retail_price')) {
                $table->dropColumn('retail_price');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_third_schedule')) {
                $table->dropColumn('is_third_schedule');
            }
            if (Schema::hasColumn('products', 'trade_price')) {
                $table->dropColumn('trade_price');
            }
            if (Schema::hasColumn('products', 'retail_price')) {
                $table->dropColumn('retail_price');
            }
        });
    }
};
