<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoice_series') && Schema::hasColumn('invoice_series', 'branch_id')) {
            $indexes = collect(DB::select('SHOW INDEX FROM `invoice_series`'))->pluck('Key_name')->unique()->toArray();

            if (in_array('invoice_series_prefix_unique', $indexes)) {
                Schema::table('invoice_series', function (Blueprint $table) {
                    $table->dropUnique('invoice_series_prefix_unique');
                });
            }

            if (!in_array('invoice_series_branch_prefix_unique', $indexes)) {
                Schema::table('invoice_series', function (Blueprint $table) {
                    $table->unique(['branch_id', 'prefix'], 'invoice_series_branch_prefix_unique');
                });
            }
        }
    }

    public function down(): void
    {
        // Keep composite unique index
    }
};
