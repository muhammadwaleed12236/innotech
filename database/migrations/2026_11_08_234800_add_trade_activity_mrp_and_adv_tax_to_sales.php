<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sale_items')) {
            Schema::table('sale_items', function (Blueprint $table) {
                if (!Schema::hasColumn('sale_items', 'trade_activity')) {
                    $table->decimal('trade_activity', 12, 2)->default(0)->after('gross_amount');
                }
                if (!Schema::hasColumn('sale_items', 'mrp_amount')) {
                    $table->decimal('mrp_amount', 12, 2)->default(0)->after('sales_tax_amount');
                }
                if (!Schema::hasColumn('sale_items', 'adv_tax_percent')) {
                    $table->decimal('adv_tax_percent', 5, 2)->default(0)->after('further_tax_amount');
                }
                if (!Schema::hasColumn('sale_items', 'adv_tax_amount')) {
                    $table->decimal('adv_tax_amount', 12, 2)->default(0)->after('adv_tax_percent');
                }
            });
        }

        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (!Schema::hasColumn('sales', 'total_trade_activity')) {
                    $table->decimal('total_trade_activity', 12, 2)->default(0)->after('total_gross');
                }
                if (!Schema::hasColumn('sales', 'total_mrp_tax')) {
                    $table->decimal('total_mrp_tax', 12, 2)->default(0)->after('total_sales_tax');
                }
                if (!Schema::hasColumn('sales', 'total_adv_tax')) {
                    $table->decimal('total_adv_tax', 12, 2)->default(0)->after('total_further_tax');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sale_items')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $columns = ['trade_activity', 'mrp_amount', 'adv_tax_percent', 'adv_tax_amount'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('sale_items', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                $columns = ['total_trade_activity', 'total_mrp_tax', 'total_adv_tax'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('sales', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
