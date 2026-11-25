<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure expense_vouchers has branch_id column with index
        if (Schema::hasTable('expense_vouchers')) {
            if (!Schema::hasColumn('expense_vouchers', 'branch_id')) {
                Schema::table('expense_vouchers', function (Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')->nullable()->default(1)->after('id')->index();
                });
            }

            DB::table('expense_vouchers')
                ->whereNull('branch_id')
                ->orWhere('branch_id', 0)
                ->update(['branch_id' => 1]);
        }

        // 2. Ensure voucher_masters has branch_id column with index
        if (Schema::hasTable('voucher_masters')) {
            if (!Schema::hasColumn('voucher_masters', 'branch_id')) {
                Schema::table('voucher_masters', function (Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')->nullable()->default(1)->after('id')->index();
                });
            }

            DB::table('voucher_masters')
                ->whereNull('branch_id')
                ->orWhere('branch_id', 0)
                ->update(['branch_id' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('expense_vouchers') && Schema::hasColumn('expense_vouchers', 'branch_id')) {
            Schema::table('expense_vouchers', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('voucher_masters') && Schema::hasColumn('voucher_masters', 'branch_id')) {
            Schema::table('voucher_masters', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
    }
};
