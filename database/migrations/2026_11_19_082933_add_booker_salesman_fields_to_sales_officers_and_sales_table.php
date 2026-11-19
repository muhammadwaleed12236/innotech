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
        // 1. Update sales_officers table with Booker/Salesman fields
        Schema::table('sales_officers', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_officers', 'code')) {
                $table->string('code', 50)->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('sales_officers', 'type')) {
                $table->string('type', 30)->default('order_booker')->after('name_urdu'); // 'order_booker', 'salesman', 'both'
            }
            if (!Schema::hasColumn('sales_officers', 'reference')) {
                $table->string('reference', 255)->nullable()->after('type');
            }
            if (!Schema::hasColumn('sales_officers', 'cnic')) {
                $table->string('cnic', 50)->nullable()->after('mobile');
            }
            if (!Schema::hasColumn('sales_officers', 'email')) {
                $table->string('email', 150)->nullable()->after('cnic');
            }
            if (!Schema::hasColumn('sales_officers', 'address')) {
                $table->text('address')->nullable()->after('email');
            }
            if (!Schema::hasColumn('sales_officers', 'status')) {
                $table->boolean('status')->default(true)->after('commission_rate');
            }
            if (!Schema::hasColumn('sales_officers', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('status');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
        });

        // 2. Add order_booker_id and salesman_id to sales table
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'order_booker_id')) {
                $table->unsignedBigInteger('order_booker_id')->nullable()->after('sales_officer_id');
                $table->foreign('order_booker_id')->references('id')->on('sales_officers')->onDelete('set null');
            }
            if (!Schema::hasColumn('sales', 'salesman_id')) {
                $table->unsignedBigInteger('salesman_id')->nullable()->after('order_booker_id');
                $table->foreign('salesman_id')->references('id')->on('sales_officers')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'salesman_id')) {
                $table->dropForeign(['salesman_id']);
                $table->dropColumn('salesman_id');
            }
            if (Schema::hasColumn('sales', 'order_booker_id')) {
                $table->dropForeign(['order_booker_id']);
                $table->dropColumn('order_booker_id');
            }
        });

        Schema::table('sales_officers', function (Blueprint $table) {
            if (Schema::hasColumn('sales_officers', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            $cols = ['code', 'type', 'reference', 'cnic', 'email', 'address', 'status'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('sales_officers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
