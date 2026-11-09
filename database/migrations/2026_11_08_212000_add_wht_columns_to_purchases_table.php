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
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'wht_percent')) {
                $table->decimal('wht_percent', 8, 2)->default(0)->after('total_sales_tax_on_trade');
            }
            if (!Schema::hasColumn('purchases', 'wht_amount')) {
                $table->decimal('wht_amount', 15, 2)->default(0)->after('wht_percent');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'wht_amount')) {
                $table->dropColumn('wht_amount');
            }
            if (Schema::hasColumn('purchases', 'wht_percent')) {
                $table->dropColumn('wht_percent');
            }
        });
    }
};
