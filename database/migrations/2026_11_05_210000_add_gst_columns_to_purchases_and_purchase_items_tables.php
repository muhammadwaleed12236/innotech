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
        Schema::table('purchase_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_items', 'gst_percent')) {
                $table->decimal('gst_percent', 8, 2)->default(0)->after('item_discount');
            }
            if (!Schema::hasColumn('purchase_items', 'gst_amount')) {
                $table->decimal('gst_amount', 15, 2)->default(0)->after('gst_percent');
            }
            if (!Schema::hasColumn('purchase_items', 'exclusive_gst_amount')) {
                $table->decimal('exclusive_gst_amount', 15, 2)->default(0)->after('gst_amount');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'total_exclusive_gst')) {
                $table->decimal('total_exclusive_gst', 15, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('purchases', 'total_gst')) {
                $table->decimal('total_gst', 15, 2)->default(0)->after('total_exclusive_gst');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_items', 'exclusive_gst_amount')) {
                $table->dropColumn('exclusive_gst_amount');
            }
            if (Schema::hasColumn('purchase_items', 'gst_amount')) {
                $table->dropColumn('gst_amount');
            }
            if (Schema::hasColumn('purchase_items', 'gst_percent')) {
                $table->dropColumn('gst_percent');
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'total_gst')) {
                $table->dropColumn('total_gst');
            }
            if (Schema::hasColumn('purchases', 'total_exclusive_gst')) {
                $table->dropColumn('total_exclusive_gst');
            }
        });
    }
};
