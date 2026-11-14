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
        if (!Schema::hasTable('tax_types')) {
            Schema::create('tax_types', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique();
                $table->string('title', 255);
                
                // Sales Tax (Purchase Invoice)
                $table->boolean('st_purchase_active')->default(false);
                $table->decimal('st_purchase_rate', 8, 2)->default(0.00);
                
                // Sales Tax (Sales Invoice)
                $table->boolean('st_sale_active')->default(false);
                $table->decimal('st_sale_rate', 8, 2)->default(0.00);
                
                // MRP Tax Applied
                $table->boolean('mrp_tax_active')->default(false);
                $table->decimal('mrp_tax_rate', 8, 2)->default(0.00);
                
                // Un-Registered S. Tax Applied
                $table->boolean('unreg_st_active')->default(false);
                $table->decimal('unreg_st_rate', 8, 2)->default(0.00);
                $table->string('unreg_st_applied_on', 30)->default('sales'); // purchases, sales, both
                $table->string('unreg_client_mode', 30)->default('add_unreg'); // add_unreg, less_reg
                
                // Excise Duty (Purchase / Sale Inv.)
                $table->boolean('excise_duty_active')->default(false);
                $table->decimal('excise_duty_rate', 8, 2)->default(0.00);
                
                // Advance Tax (Purchase Inv)
                $table->boolean('adv_tax_purchase_active')->default(true);
                $table->decimal('adv_tax_purchase_rate', 8, 2)->default(0.10);
                
                // Advance Tax (Sale Inv)
                $table->boolean('adv_tax_sale_active')->default(true);
                $table->decimal('adv_tax_sale_filer_rate', 8, 2)->default(0.50);
                $table->decimal('adv_tax_sale_non_filer_rate', 8, 2)->default(2.50);
                
                // Advance Tax Applied On
                // Options: gross_amount, net_tp, gross_tp, net_retail, gross_retail
                $table->string('adv_tax_applied_on', 50)->default('net_tp');
                
                // Applied From & Special Disc
                $table->date('applied_from')->nullable();
                $table->boolean('include_special_disc')->default(true);
                
                // Tax Applied On: trade_value or retail_value
                $table->string('tax_applied_on', 30)->default('trade_value');
                
                // Flags & Metadata
                $table->boolean('is_default')->default(false);
                $table->boolean('status')->default(true);
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Add tax_type_id to products
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'tax_type_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('tax_type_id')->nullable()->after('is_third_schedule');
            });
        }

        // Add tax_type_id to purchase_items
        if (Schema::hasTable('purchase_items') && !Schema::hasColumn('purchase_items', 'tax_type_id')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->unsignedBigInteger('tax_type_id')->nullable()->after('is_third_schedule');
            });
        }

        // Add tax_type_id to purchases
        if (Schema::hasTable('purchases') && !Schema::hasColumn('purchases', 'tax_type_id')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->unsignedBigInteger('tax_type_id')->nullable()->after('vendor_id');
            });
        }

        // Pre-seed default tax types if table is empty
        if (Schema::hasTable('tax_types') && DB::table('tax_types')->count() === 0) {
            $now = now();
            DB::table('tax_types')->insert([
                'code' => '0001',
                'title' => 'EXEMPT ITEMS',
                'st_purchase_active' => false,
                'st_purchase_rate' => 0.00,
                'st_sale_active' => false,
                'st_sale_rate' => 0.00,
                'mrp_tax_active' => false,
                'mrp_tax_rate' => 0.00,
                'unreg_st_active' => false,
                'unreg_st_rate' => 0.00,
                'unreg_st_applied_on' => 'both',
                'unreg_client_mode' => 'add_unreg',
                'excise_duty_active' => false,
                'excise_duty_rate' => 0.00,
                'adv_tax_purchase_active' => true,
                'adv_tax_purchase_rate' => 0.10,
                'adv_tax_sale_active' => true,
                'adv_tax_sale_filer_rate' => 0.50,
                'adv_tax_sale_non_filer_rate' => 2.50,
                'adv_tax_applied_on' => 'net_tp',
                'applied_from' => '2024-07-02',
                'include_special_disc' => true,
                'tax_applied_on' => 'trade_value',
                'is_default' => false,
                'status' => true,
                'notes' => 'Items exempt from Sales Tax / GST',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('tax_types')->insert([
                'code' => '0006',
                'title' => 'NON THIRD SCHEDULE ITEMS',
                'st_purchase_active' => true,
                'st_purchase_rate' => 18.00,
                'st_sale_active' => true,
                'st_sale_rate' => 18.00,
                'mrp_tax_active' => false,
                'mrp_tax_rate' => 0.00,
                'unreg_st_active' => true,
                'unreg_st_rate' => 4.00,
                'unreg_st_applied_on' => 'sales',
                'unreg_client_mode' => 'add_unreg',
                'excise_duty_active' => false,
                'excise_duty_rate' => 0.00,
                'adv_tax_purchase_active' => true,
                'adv_tax_purchase_rate' => 0.10,
                'adv_tax_sale_active' => true,
                'adv_tax_sale_filer_rate' => 0.50,
                'adv_tax_sale_non_filer_rate' => 2.50,
                'adv_tax_applied_on' => 'net_tp',
                'applied_from' => '2025-10-02',
                'include_special_disc' => true,
                'tax_applied_on' => 'trade_value',
                'is_default' => true,
                'status' => true,
                'notes' => 'Standard 18% Sales Tax on Trade Value',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('tax_types')->insert([
                'code' => '0014',
                'title' => '3. THIRD SCHEDULE ITEMS( GFL ) ( 01-JUL-2026 )',
                'st_purchase_active' => true,
                'st_purchase_rate' => 18.00,
                'st_sale_active' => false,
                'st_sale_rate' => 0.00,
                'mrp_tax_active' => true,
                'mrp_tax_rate' => 18.00,
                'unreg_st_active' => false,
                'unreg_st_rate' => 0.00,
                'unreg_st_applied_on' => 'both',
                'unreg_client_mode' => 'add_unreg',
                'excise_duty_active' => false,
                'excise_duty_rate' => 0.00,
                'adv_tax_purchase_active' => true,
                'adv_tax_purchase_rate' => 0.10,
                'adv_tax_sale_active' => true,
                'adv_tax_sale_filer_rate' => 0.50,
                'adv_tax_sale_non_filer_rate' => 2.50,
                'adv_tax_applied_on' => 'net_tp',
                'applied_from' => '2026-07-01',
                'include_special_disc' => true,
                'tax_applied_on' => 'retail_value',
                'is_default' => false,
                'status' => true,
                'notes' => 'Third Schedule Items - Sales Tax / GST applied on Retail Price (MRP)',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('tax_types')->insert([
                'code' => '0008',
                'title' => 'REDUCED RATE ITEMS (10% GST)',
                'st_purchase_active' => true,
                'st_purchase_rate' => 10.00,
                'st_sale_active' => true,
                'st_sale_rate' => 10.00,
                'mrp_tax_active' => false,
                'mrp_tax_rate' => 0.00,
                'unreg_st_active' => true,
                'unreg_st_rate' => 4.00,
                'unreg_st_applied_on' => 'sales',
                'unreg_client_mode' => 'add_unreg',
                'excise_duty_active' => false,
                'excise_duty_rate' => 0.00,
                'adv_tax_purchase_active' => true,
                'adv_tax_purchase_rate' => 0.10,
                'adv_tax_sale_active' => true,
                'adv_tax_sale_filer_rate' => 0.50,
                'adv_tax_sale_non_filer_rate' => 2.50,
                'adv_tax_applied_on' => 'net_tp',
                'applied_from' => '2025-10-01',
                'include_special_disc' => true,
                'tax_applied_on' => 'trade_value',
                'is_default' => false,
                'status' => true,
                'notes' => 'Reduced Rate Items under Eighth Schedule (e.g. 10% Sales Tax on Trade Value)',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'tax_type_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('tax_type_id');
            });
        }

        if (Schema::hasTable('purchase_items') && Schema::hasColumn('purchase_items', 'tax_type_id')) {
            Schema::table('purchase_items', function (Blueprint $table) {
                $table->dropColumn('tax_type_id');
            });
        }

        if (Schema::hasTable('purchases') && Schema::hasColumn('purchases', 'tax_type_id')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->dropColumn('tax_type_id');
            });
        }

        Schema::dropIfExists('tax_types');
    }
};
