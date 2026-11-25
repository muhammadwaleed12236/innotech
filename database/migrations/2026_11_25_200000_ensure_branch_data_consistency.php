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
        // 1. Ensure purchases has branch_id column with index
        if (Schema::hasTable('purchases')) {
            if (!Schema::hasColumn('purchases', 'branch_id')) {
                Schema::table('purchases', function (Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('id')->index();
                });
            }

            // Backfill null or 0 branch_id to default Branch 1
            DB::table('purchases')
                ->whereNull('branch_id')
                ->orWhere('branch_id', 0)
                ->update(['branch_id' => 1]);
        }

        // 2. Ensure sales has branch_id column with index
        if (Schema::hasTable('sales')) {
            if (!Schema::hasColumn('sales', 'branch_id')) {
                Schema::table('sales', function (Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('id')->index();
                });
            }

            // Backfill null or 0 branch_id to default Branch 1
            DB::table('sales')
                ->whereNull('branch_id')
                ->orWhere('branch_id', 0)
                ->update(['branch_id' => 1]);
        }

        // 3. Ensure warehouses has branch_id column and backfill
        if (Schema::hasTable('warehouses')) {
            if (!Schema::hasColumn('warehouses', 'branch_id')) {
                Schema::table('warehouses', function (Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('id')->index();
                });
            }

            DB::table('warehouses')
                ->whereNull('branch_id')
                ->orWhere('branch_id', 0)
                ->update(['branch_id' => 1]);
        }

        // 4. Ensure branches.user_id is synced to users.branch_id
        if (Schema::hasTable('branches') && Schema::hasTable('users') && Schema::hasColumn('users', 'branch_id')) {
            $branches = DB::table('branches')->get();
            foreach ($branches as $branch) {
                if (!empty($branch->user_id)) {
                    DB::table('users')
                        ->where('id', $branch->user_id)
                        ->update(['branch_id' => $branch->id]);
                }

                // Ensure every branch has at least one warehouse
                $hasWarehouse = DB::table('warehouses')->where('branch_id', $branch->id)->exists();
                if (!$hasWarehouse) {
                    DB::table('warehouses')->insert([
                        'branch_id' => $branch->id,
                        'warehouse_name' => $branch->name . ' Store',
                        'creater_id' => $branch->user_id ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive reverse needed for data synchronization
    }
};
