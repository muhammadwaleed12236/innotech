<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('dashboard_tasks') && !Schema::hasColumn('dashboard_tasks', 'branch_id')) {
            Schema::table('dashboard_tasks', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('dashboard_tasks') && Schema::hasColumn('dashboard_tasks', 'branch_id')) {
            Schema::table('dashboard_tasks', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
    }
};
