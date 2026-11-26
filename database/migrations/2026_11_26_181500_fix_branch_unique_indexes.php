<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function replaceGlobalNameUniqueWithBranchUnique(string $tableName, string $oldIndexName, string $newIndexName): void
    {
        if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'branch_id')) {
            return;
        }

        $indexes = collect(DB::select("SHOW INDEX FROM `{$tableName}`"))->pluck('Key_name')->unique()->toArray();

        if (in_array($oldIndexName, $indexes)) {
            Schema::table($tableName, function (Blueprint $table) use ($oldIndexName) {
                $table->dropUnique($oldIndexName);
            });

            if (!in_array($newIndexName, $indexes)) {
                Schema::table($tableName, function (Blueprint $table) use ($newIndexName) {
                    $table->unique(['branch_id', 'name'], $newIndexName);
                });
            }
        }
    }

    public function up(): void
    {
        $this->replaceGlobalNameUniqueWithBranchUnique('customer_types', 'customer_types_name_unique', 'customer_types_branch_name_unique');
        $this->replaceGlobalNameUniqueWithBranchUnique('expense_categories', 'expense_categories_name_unique', 'expense_categories_branch_name_unique');
        $this->replaceGlobalNameUniqueWithBranchUnique('categories', 'categories_name_unique', 'categories_branch_name_unique');
        $this->replaceGlobalNameUniqueWithBranchUnique('brands', 'brands_name_unique', 'brands_branch_name_unique');
        $this->replaceGlobalNameUniqueWithBranchUnique('units', 'units_name_unique', 'units_branch_name_unique');
    }

    public function down(): void
    {
        // Keep composite unique indexes
    }
};
