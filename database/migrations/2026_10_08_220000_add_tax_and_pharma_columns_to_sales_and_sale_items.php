<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                if (!Schema::hasColumn('sales', 'total_gross')) {
                    $table->decimal('total_gross', 12, 2)->default(0)->after('total_bill_amount');
                }
                if (!Schema::hasColumn('sales', 'total_exclusive_gst')) {
                    $table->decimal('total_exclusive_gst', 12, 2)->default(0)->after('total_gross');
                }
                if (!Schema::hasColumn('sales', 'total_sales_tax')) {
                    $table->decimal('total_sales_tax', 12, 2)->default(0)->after('total_exclusive_gst');
                }
                if (!Schema::hasColumn('sales', 'total_further_tax')) {
                    $table->decimal('total_further_tax', 12, 2)->default(0)->after('total_sales_tax');
                }
                if (!Schema::hasColumn('sales', 'total_bonus_qty')) {
                    $table->decimal('total_bonus_qty', 12, 2)->default(0)->after('total_further_tax');
                }
            });
        }

        if (Schema::hasTable('sale_items')) {
            Schema::table('sale_items', function (Blueprint $table) {
                if (!Schema::hasColumn('sale_items', 'bonus_qty')) {
                    $table->decimal('bonus_qty', 12, 2)->default(0)->after('qty');
                }
                if (!Schema::hasColumn('sale_items', 'mfg_date')) {
                    $table->string('mfg_date')->nullable()->after('serials');
                }
                if (!Schema::hasColumn('sale_items', 'exp_date')) {
                    $table->string('exp_date')->nullable()->after('mfg_date');
                }
                if (!Schema::hasColumn('sale_items', 'gross_amount')) {
                    $table->decimal('gross_amount', 12, 2)->default(0)->after('price');
                }
                if (!Schema::hasColumn('sale_items', 'exclusive_gst_amount')) {
                    $table->decimal('exclusive_gst_amount', 12, 2)->default(0)->after('discount_amount');
                }
                if (!Schema::hasColumn('sale_items', 'sales_tax_percent')) {
                    $table->decimal('sales_tax_percent', 5, 2)->default(0)->after('exclusive_gst_amount');
                }
                if (!Schema::hasColumn('sale_items', 'sales_tax_amount')) {
                    $table->decimal('sales_tax_amount', 12, 2)->default(0)->after('sales_tax_percent');
                }
                if (!Schema::hasColumn('sale_items', 'further_tax_percent')) {
                    $table->decimal('further_tax_percent', 5, 2)->default(0)->after('sales_tax_amount');
                }
                if (!Schema::hasColumn('sale_items', 'further_tax_amount')) {
                    $table->decimal('further_tax_amount', 12, 2)->default(0)->after('further_tax_percent');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn([
                    'total_gross',
                    'total_exclusive_gst',
                    'total_sales_tax',
                    'total_further_tax',
                    'total_bonus_qty'
                ]);
            });
        }

        if (Schema::hasTable('sale_items')) {
            Schema::table('sale_items', function (Blueprint $table) {
                $table->dropColumn([
                    'bonus_qty',
                    'mfg_date',
                    'exp_date',
                    'gross_amount',
                    'exclusive_gst_amount',
                    'sales_tax_percent',
                    'sales_tax_amount',
                    'further_tax_percent',
                    'further_tax_amount'
                ]);
            });
        }
    }
};
