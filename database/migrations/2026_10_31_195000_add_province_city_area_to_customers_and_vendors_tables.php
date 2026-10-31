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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'province')) {
                $table->string('province')->nullable()->after('address');
            }
            if (!Schema::hasColumn('customers', 'city')) {
                $table->string('city')->nullable()->after('province');
            }
            if (!Schema::hasColumn('customers', 'area')) {
                $table->string('area')->nullable()->after('city');
            }
        });

        Schema::table('vendors', function (Blueprint $table) {
            if (!Schema::hasColumn('vendors', 'province')) {
                $table->string('province')->nullable()->after('address');
            }
            if (!Schema::hasColumn('vendors', 'city')) {
                $table->string('city')->nullable()->after('province');
            }
            if (!Schema::hasColumn('vendors', 'area')) {
                $table->string('area')->nullable()->after('city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'area')) {
                $table->dropColumn('area');
            }
            if (Schema::hasColumn('customers', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('customers', 'province')) {
                $table->dropColumn('province');
            }
        });

        Schema::table('vendors', function (Blueprint $table) {
            if (Schema::hasColumn('vendors', 'area')) {
                $table->dropColumn('area');
            }
            if (Schema::hasColumn('vendors', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('vendors', 'province')) {
                $table->dropColumn('province');
            }
        });
    }
};
