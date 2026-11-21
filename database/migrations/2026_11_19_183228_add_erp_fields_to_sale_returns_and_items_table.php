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
        Schema::table('sale_returns', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_returns', 'sales_officer_id')) {
                $table->unsignedBigInteger('sales_officer_id')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('sale_returns', 'order_booker_id')) {
                $table->unsignedBigInteger('order_booker_id')->nullable()->after('sales_officer_id');
            }
            if (!Schema::hasColumn('sale_returns', 'salesman_id')) {
                $table->unsignedBigInteger('salesman_id')->nullable()->after('order_booker_id');
            }
            if (!Schema::hasColumn('sale_returns', 'walkin_name')) {
                $table->string('walkin_name')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('sale_returns', 'total_sales_tax')) {
                $table->decimal('total_sales_tax', 15, 2)->default(0)->after('item_discount');
            }
            if (!Schema::hasColumn('sale_returns', 'total_further_tax')) {
                $table->decimal('total_further_tax', 15, 2)->default(0)->after('total_sales_tax');
            }
            if (!Schema::hasColumn('sale_returns', 'total_adv_tax')) {
                $table->decimal('total_adv_tax', 15, 2)->default(0)->after('total_further_tax');
            }
            if (!Schema::hasColumn('sale_returns', 'total_reg_discount')) {
                $table->decimal('total_reg_discount', 15, 2)->default(0)->after('item_discount');
            }
            if (!Schema::hasColumn('sale_returns', 'total_sp_discount')) {
                $table->decimal('total_sp_discount', 15, 2)->default(0)->after('total_reg_discount');
            }
        });

        Schema::table('sale_return_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_return_items', 'trade_price')) {
                $table->decimal('trade_price', 15, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('sale_return_items', 'retail_price')) {
                $table->decimal('retail_price', 15, 2)->nullable()->after('trade_price');
            }
            if (!Schema::hasColumn('sale_return_items', 'reg_disc_percent')) {
                $table->decimal('reg_disc_percent', 8, 2)->default(0)->after('item_discount');
            }
            if (!Schema::hasColumn('sale_return_items', 'sp_disc_percent')) {
                $table->decimal('sp_disc_percent', 8, 2)->default(0)->after('reg_disc_percent');
            }
            if (!Schema::hasColumn('sale_return_items', 'sales_tax_percent')) {
                $table->decimal('sales_tax_percent', 8, 2)->default(0)->after('sp_disc_percent');
            }
            if (!Schema::hasColumn('sale_return_items', 'sales_tax_amount')) {
                $table->decimal('sales_tax_amount', 15, 2)->default(0)->after('sales_tax_percent');
            }
            if (!Schema::hasColumn('sale_return_items', 'further_tax_percent')) {
                $table->decimal('further_tax_percent', 8, 2)->default(0)->after('sales_tax_amount');
            }
            if (!Schema::hasColumn('sale_return_items', 'further_tax_amount')) {
                $table->decimal('further_tax_amount', 15, 2)->default(0)->after('further_tax_percent');
            }
            if (!Schema::hasColumn('sale_return_items', 'mrp_amount')) {
                $table->decimal('mrp_amount', 15, 2)->default(0)->after('further_tax_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->dropColumn([
                'sales_officer_id',
                'order_booker_id',
                'salesman_id',
                'walkin_name',
                'total_sales_tax',
                'total_further_tax',
                'total_adv_tax',
                'total_reg_discount',
                'total_sp_discount',
            ]);
        });

        Schema::table('sale_return_items', function (Blueprint $table) {
            $table->dropColumn([
                'trade_price',
                'retail_price',
                'reg_disc_percent',
                'sp_disc_percent',
                'sales_tax_percent',
                'sales_tax_amount',
                'further_tax_percent',
                'further_tax_amount',
                'mrp_amount',
            ]);
        });
    }
};
