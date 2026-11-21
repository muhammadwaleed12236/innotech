<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customer_claims')) {
            Schema::create('customer_claims', function (Blueprint $table) {
                $table->id();
                $table->string('claim_no')->unique(); // e.g. CLM-00001
                $table->date('claim_date');
                $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
                $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
                $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
                $table->string('claim_type')->default('damage'); // damage, expiry, breakage, defect, claim
                
                // Financials & Vendor Allowance Tracking
                $table->decimal('total_claim_amount', 15, 2)->default(0); // e.g. 20,000
                $table->decimal('company_allowance_before', 15, 2)->default(0); // e.g. 50,000
                $table->decimal('claim_adjusted', 15, 2)->default(0); // e.g. 20,000
                $table->decimal('company_remaining_balance', 15, 2)->default(0); // e.g. 30,000
                
                // Customer Settlement
                $table->string('settlement_type')->default('ledger_credit'); // ledger_credit, cash_refund, replacement
                $table->foreignId('refund_account_id')->nullable()->constrained('accounts')->nullOnDelete();
                $table->decimal('refund_amount', 15, 2)->default(0);
                
                $table->string('status')->default('received'); // received, verified, settled_with_company, rejected
                $table->text('remarks')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('customer_claim_items')) {
            Schema::create('customer_claim_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_claim_id')->constrained('customer_claims')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
                $table->string('damage_reason')->default('damaged'); // damaged, expired, broken, leakage, defect
                $table->string('unit')->nullable();
                $table->decimal('pieces_per_box', 12, 2)->default(1);
                $table->decimal('cartons', 12, 2)->default(0);
                $table->decimal('loose_pieces', 12, 2)->default(0);
                $table->decimal('total_pieces', 12, 2)->default(0);
                $table->decimal('rate', 15, 2)->default(0);
                $table->decimal('line_total', 15, 2)->default(0);
                $table->text('color')->nullable();
                $table->string('batch_no')->nullable();
                $table->date('expiry_date')->nullable();
                
                $table->decimal('company_disc_share_percent', 8, 2)->default(0);
                $table->decimal('company_disc_share_amount', 15, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_claim_items');
        Schema::dropIfExists('customer_claims');
    }
};
