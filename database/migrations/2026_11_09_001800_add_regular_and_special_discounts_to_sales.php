<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'reg_disc_percent')) {
                $table->decimal('reg_disc_percent', 8, 2)->default(0)->after('trade_activity');
            }
            if (!Schema::hasColumn('sale_items', 'reg_disc_amount')) {
                $table->decimal('reg_disc_amount', 12, 2)->default(0)->after('reg_disc_percent');
            }
            if (!Schema::hasColumn('sale_items', 'sp_disc_percent')) {
                $table->decimal('sp_disc_percent', 8, 2)->default(0)->after('reg_disc_amount');
            }
            if (!Schema::hasColumn('sale_items', 'sp_disc_amount')) {
                $table->decimal('sp_disc_amount', 12, 2)->default(0)->after('sp_disc_percent');
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'total_reg_discount')) {
                $table->decimal('total_reg_discount', 12, 2)->default(0)->after('total_trade_activity');
            }
            if (!Schema::hasColumn('sales', 'total_sp_discount')) {
                $table->decimal('total_sp_discount', 12, 2)->default(0)->after('total_reg_discount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['reg_disc_percent', 'reg_disc_amount', 'sp_disc_percent', 'sp_disc_amount']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['total_reg_discount', 'total_sp_discount']);
        });
    }
};
