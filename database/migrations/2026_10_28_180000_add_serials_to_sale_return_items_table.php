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
        if (Schema::hasTable('sale_return_items') && !Schema::hasColumn('sale_return_items', 'serials')) {
            Schema::table('sale_return_items', function (Blueprint $table) {
                $table->text('serials')->nullable()->after('color');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sale_return_items') && Schema::hasColumn('sale_return_items', 'serials')) {
            Schema::table('sale_return_items', function (Blueprint $table) {
                $table->dropColumn('serials');
            });
        }
    }
};
