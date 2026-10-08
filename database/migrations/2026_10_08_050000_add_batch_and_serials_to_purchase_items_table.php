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
        if (Schema::hasTable('purchase_items')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                if (!Schema::hasColumn('purchase_items', 'batch_id')) {
                    $table->unsignedBigInteger('batch_id')->nullable()->after('product_id');
                }
                if (!Schema::hasColumn('purchase_items', 'batch_no')) {
                    $table->string('batch_no')->nullable()->after('batch_id');
                }
                if (!Schema::hasColumn('purchase_items', 'serials')) {
                    $table->text('serials')->nullable()->after('batch_no');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('purchase_items')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                if (Schema::hasColumn('purchase_items', 'batch_id')) {
                    $table->dropColumn(['batch_id', 'batch_no', 'serials']);
                }
            });
        }
    }
};
