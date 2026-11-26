<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * All tables that should be strictly isolated per branch.
     */
    private array $tables = [
        // Catalog & Setup
        'categories',
        'subcategories',
        'brands',
        'units',
        'package_types',
        'customer_types',
        'sales_officers',
        'zones',
        'provinces',
        'cities',
        'areas',
        'narrations',
        'terms_and_conditions',
        'transports',
        // Transactions, Returns & Stock
        'sale_returns',
        'purchase_returns',
        'inward_gatepasses',
        'stock_transfers',
        'stock_adjustments',
        'product_bookings',
        'product_discounts',
        'customer_claims',
        'vendor_bilties',
        'invoice_series',
        // Accounting & Vouchers
        'account_heads',
        'accounts',
        'voucher_masters',
        'receipts_vouchers',
        'payment_vouchers',
        'expense_categories',
        'cheques',
        'customer_payments',
        'vendor_payments',
        'journal_entries',
        'day_closings',
        'system_notifications',
        // HR
        'hr_departments',
        'hr_designations',
        'hr_employees',
        'hr_shifts',
        'hr_holidays',
        'hr_salary_structures',
    ];

    public function up(): void
    {
        $hasMainBranch = Schema::hasTable('branches') && DB::table('branches')->where('id', 1)->exists();

        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'branch_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('branch_id')->nullable()->after('id')->index();
                });

                if ($hasMainBranch) {
                    DB::table($tableName)->whereNull('branch_id')->update(['branch_id' => 1]);
                }
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'branch_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('branch_id');
                });
            }
        }
    }
};
