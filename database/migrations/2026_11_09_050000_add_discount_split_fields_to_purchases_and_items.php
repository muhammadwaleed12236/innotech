<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'company_disc_percent')) {
                $table->decimal('company_disc_percent', 8, 2)->default(0)->after('additional_discount');
            }
            if (!Schema::hasColumn('purchases', 'company_disc_amount')) {
                $table->decimal('company_disc_amount', 15, 2)->default(0)->after('company_disc_percent');
            }
            if (!Schema::hasColumn('purchases', 'our_disc_percent')) {
                $table->decimal('our_disc_percent', 8, 2)->default(0)->after('company_disc_amount');
            }
            if (!Schema::hasColumn('purchases', 'our_disc_amount')) {
                $table->decimal('our_disc_amount', 15, 2)->default(0)->after('our_disc_percent');
            }
            if (!Schema::hasColumn('purchases', 'customer_disc_percent')) {
                $table->decimal('customer_disc_percent', 8, 2)->default(0)->after('our_disc_amount');
            }
            if (!Schema::hasColumn('purchases', 'customer_disc_amount')) {
                $table->decimal('customer_disc_amount', 15, 2)->default(0)->after('customer_disc_percent');
            }
            if (!Schema::hasColumn('purchases', 'our_disc_notes')) {
                $table->text('our_disc_notes')->nullable()->after('customer_disc_amount');
            }
            if (!Schema::hasColumn('purchases', 'company_settled_amount')) {
                $table->decimal('company_settled_amount', 15, 2)->default(0)->after('our_disc_notes');
            }
            if (!Schema::hasColumn('purchases', 'settlement_status')) {
                $table->string('settlement_status', 20)->default('pending')->after('company_settled_amount');
            }
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_items', 'company_disc_percent')) {
                $table->decimal('company_disc_percent', 8, 2)->default(0)->after('item_discount');
            }
            if (!Schema::hasColumn('purchase_items', 'company_disc_amount')) {
                $table->decimal('company_disc_amount', 15, 2)->default(0)->after('company_disc_percent');
            }
            if (!Schema::hasColumn('purchase_items', 'our_disc_percent')) {
                $table->decimal('our_disc_percent', 8, 2)->default(0)->after('company_disc_amount');
            }
            if (!Schema::hasColumn('purchase_items', 'our_disc_amount')) {
                $table->decimal('our_disc_amount', 15, 2)->default(0)->after('our_disc_percent');
            }
            if (!Schema::hasColumn('purchase_items', 'customer_disc_percent')) {
                $table->decimal('customer_disc_percent', 8, 2)->default(0)->after('our_disc_amount');
            }
            if (!Schema::hasColumn('purchase_items', 'customer_disc_amount')) {
                $table->decimal('customer_disc_amount', 15, 2)->default(0)->after('customer_disc_percent');
            }
            if (!Schema::hasColumn('purchase_items', 'our_disc_notes')) {
                $table->text('our_disc_notes')->nullable()->after('customer_disc_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn([
                'company_disc_percent', 'company_disc_amount',
                'our_disc_percent', 'our_disc_amount',
                'customer_disc_percent', 'customer_disc_amount',
                'our_disc_notes', 'company_settled_amount', 'settlement_status'
            ]);
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn([
                'company_disc_percent', 'company_disc_amount',
                'our_disc_percent', 'our_disc_amount',
                'customer_disc_percent', 'customer_disc_amount',
                'our_disc_notes'
            ]);
        });
    }
};
