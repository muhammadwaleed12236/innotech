<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'tracking_type')) {
                    $table->string('tracking_type', 20)->default('simple')->after('item_name');
                }
            });

            // Backfill existing products based on product_batches and product_serials
            $batchProductIds = DB::table('product_batches')->distinct()->pluck('product_id')->toArray();
            if (!empty($batchProductIds)) {
                DB::table('products')->whereIn('id', $batchProductIds)->update(['tracking_type' => 'batch']);
            }

            $serialProductIds = DB::table('product_serials')->distinct()->pluck('product_id')->toArray();
            if (!empty($serialProductIds)) {
                DB::table('products')->whereIn('id', $serialProductIds)->update(['tracking_type' => 'serial']);
            }
        }

        if (Schema::hasTable('purchase_items')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                if (!Schema::hasColumn('purchase_items', 'mfg_date')) {
                    $table->date('mfg_date')->nullable()->after('serials');
                }
                if (!Schema::hasColumn('purchase_items', 'exp_date')) {
                    $table->date('exp_date')->nullable()->after('mfg_date');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'tracking_type')) {
                    $table->dropColumn('tracking_type');
                }
            });
        }

        if (Schema::hasTable('purchase_items')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                if (Schema::hasColumn('purchase_items', 'mfg_date')) {
                    $table->dropColumn(['mfg_date', 'exp_date']);
                }
            });
        }
    }
};
