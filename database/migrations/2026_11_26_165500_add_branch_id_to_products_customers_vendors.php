<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['products', 'customers', 'vendors'] as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'branch_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('id')->index();
                });

                // Assign existing records to Main Branch (id = 1) if it exists
                if (Schema::hasTable('branches') && DB::table('branches')->where('id', 1)->exists()) {
                    DB::table($tableName)->whereNull('branch_id')->update(['branch_id' => 1]);
                }
            }
        }
    }

    public function down(): void
    {
        foreach (['products', 'customers', 'vendors'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'branch_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('branch_id');
                });
            }
        }
    }
};
