<?php

namespace App\Observers;

use App\Models\BusinessActivity;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\SaleReturn;
use App\Models\PurchaseReturn;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\ExpenseVoucher;
use App\Models\VoucherMaster;
use App\Models\StockTransfer;
use App\Models\StockAdjustment;

class BusinessActivityObserver
{
    public static function registerEvents(): void
    {
        // 1. Sale
        Sale::created(function ($sale) {
            try {
                $ref = $sale->invoice_no ?: ('INV-' . $sale->id);
                $cust = $sale->customer_relation ? $sale->customer_relation->customer_name : ($sale->walkin_name ?: 'Customer');
                BusinessActivity::log([
                    'module' => 'sales',
                    'activity_type' => 'sale_created',
                    'title' => "Sale Invoice #{$ref}",
                    'description' => "New sale generated for {$cust} with net amount PKR " . number_format($sale->total_net, 2),
                    'icon' => 'fa-receipt',
                    'badge_color' => 'success',
                    'reference_no' => $ref,
                    'amount' => $sale->total_net,
                    'branch_id' => $sale->branch_id ?? null,
                    'subject_type' => Sale::class,
                    'subject_id' => $sale->id,
                    'action_url' => route('sales.invoice', $sale->id),
                ]);
            } catch (\Throwable $e) {}
        });

        // 2. Purchase
        Purchase::created(function ($p) {
            try {
                $ref = $p->invoice_no ?: ('PO-' . $p->id);
                $vend = $p->vendor ? $p->vendor->name : 'Supplier';
                BusinessActivity::log([
                    'module' => 'purchases',
                    'activity_type' => 'purchase_created',
                    'title' => "Purchase Order #{$ref}",
                    'description' => "Procurement received from {$vend} worth PKR " . number_format($p->net_amount, 2),
                    'icon' => 'fa-cart-shopping',
                    'badge_color' => 'primary',
                    'reference_no' => $ref,
                    'amount' => $p->net_amount,
                    'branch_id' => $p->branch_id ?? null,
                    'subject_type' => Purchase::class,
                    'subject_id' => $p->id,
                    'action_url' => route('purchase.invoice', $p->id),
                ]);
            } catch (\Throwable $e) {}
        });

        // 3. Sale Return
        SaleReturn::created(function ($sr) {
            try {
                $ref = 'SR-' . $sr->id;
                BusinessActivity::log([
                    'module' => 'sales',
                    'activity_type' => 'sale_return',
                    'title' => "Sale Return #{$ref}",
                    'description' => "Return processed for PKR " . number_format($sr->total_net ?? $sr->net_amount ?? 0, 2),
                    'icon' => 'fa-rotate-left',
                    'badge_color' => 'rose',
                    'reference_no' => $ref,
                    'amount' => $sr->total_net ?? $sr->net_amount ?? 0,
                    'action_url' => route('sale.return.view', $sr->id),
                ]);
            } catch (\Throwable $e) {}
        });

        // 4. Purchase Return
        PurchaseReturn::created(function ($pr) {
            try {
                $ref = 'PR-' . $pr->id;
                BusinessActivity::log([
                    'module' => 'purchases',
                    'activity_type' => 'purchase_return',
                    'title' => "Purchase Return #{$ref}",
                    'description' => "Return to vendor processed for PKR " . number_format($pr->net_amount ?? 0, 2),
                    'icon' => 'fa-arrow-rotate-left',
                    'badge_color' => 'amber',
                    'reference_no' => $ref,
                    'amount' => $pr->net_amount ?? 0,
                    'action_url' => route('purchase.return.view', $pr->id),
                ]);
            } catch (\Throwable $e) {}
        });

        // 5. Customer
        Customer::created(function ($cust) {
            try {
                $ref = $cust->customer_id ?: ('CUST-' . $cust->id);
                BusinessActivity::log([
                    'module' => 'partners',
                    'activity_type' => 'customer_registered',
                    'title' => "Customer Registered: {$cust->customer_name}",
                    'description' => "Customer account registered ({$ref})",
                    'icon' => 'fa-user-check',
                    'badge_color' => 'emerald',
                    'reference_no' => $ref,
                    'action_url' => route('customers.index'),
                ]);
            } catch (\Throwable $e) {}
        });

        // 6. Vendor
        Vendor::created(function ($vend) {
            try {
                $ref = 'VND-' . $vend->id;
                BusinessActivity::log([
                    'module' => 'partners',
                    'activity_type' => 'supplier_registered',
                    'title' => "Supplier Onboarded: {$vend->name}",
                    'description' => "Vendor account created for {$vend->name}",
                    'icon' => 'fa-truck-fast',
                    'badge_color' => 'purple',
                    'reference_no' => $ref,
                    'action_url' => route('vendors-ledger'),
                ]);
            } catch (\Throwable $e) {}
        });

        // 7. Product
        Product::created(function ($prod) {
            try {
                $ref = $prod->code ?: ('PRD-' . $prod->id);
                BusinessActivity::log([
                    'module' => 'inventory',
                    'activity_type' => 'product_added',
                    'title' => "Product Added: {$prod->name}",
                    'description' => "Product {$prod->name} cataloged with code {$ref}",
                    'icon' => 'fa-box',
                    'badge_color' => 'cyan',
                    'reference_no' => $ref,
                    'action_url' => route('productview', $prod->id),
                ]);
            } catch (\Throwable $e) {}
        });

        // 8. Expense Voucher
        ExpenseVoucher::created(function ($exp) {
            try {
                $ref = 'EXP-' . $exp->id;
                BusinessActivity::log([
                    'module' => 'finance',
                    'activity_type' => 'expense_recorded',
                    'title' => "Expense Recorded ({$exp->type})",
                    'description' => "Expense voucher #{$ref} recorded for PKR " . number_format($exp->total_amount, 2),
                    'icon' => 'fa-money-bill-wave',
                    'badge_color' => 'danger',
                    'reference_no' => $ref,
                    'amount' => $exp->total_amount,
                ]);
            } catch (\Throwable $e) {}
        });

        // 9. Voucher Master
        VoucherMaster::created(function ($vm) {
            try {
                $ref = $vm->voucher_no ?: ('VCH-' . $vm->id);
                $type = ucfirst($vm->voucher_type ?? 'Voucher');
                BusinessActivity::log([
                    'module' => 'finance',
                    'activity_type' => 'voucher_' . ($vm->voucher_type ?? 'entry'),
                    'title' => "{$type} Voucher #{$ref}",
                    'description' => "{$type} voucher #{$ref} processed for PKR " . number_format($vm->total_amount, 2),
                    'icon' => 'fa-file-invoice-dollar',
                    'badge_color' => 'info',
                    'reference_no' => $ref,
                    'amount' => $vm->total_amount,
                ]);
            } catch (\Throwable $e) {}
        });

        // 10. Stock Transfer
        StockTransfer::created(function ($st) {
            try {
                $ref = 'ST-' . $st->id;
                BusinessActivity::log([
                    'module' => 'stock',
                    'activity_type' => 'stock_transfer',
                    'title' => "Warehouse Transfer #{$ref}",
                    'description' => "Stock movement between warehouses recorded",
                    'icon' => 'fa-dolly',
                    'badge_color' => 'blue',
                    'reference_no' => $ref,
                ]);
            } catch (\Throwable $e) {}
        });

        // 11. Stock Adjustment
        StockAdjustment::created(function ($sa) {
            try {
                $ref = 'SA-' . $sa->id;
                BusinessActivity::log([
                    'module' => 'stock',
                    'activity_type' => 'stock_adjustment',
                    'title' => "Stock Adjustment #{$ref}",
                    'description' => "Inventory reconciliation & adjustment performed",
                    'icon' => 'fa-sliders',
                    'badge_color' => 'orange',
                    'reference_no' => $ref,
                ]);
            } catch (\Throwable $e) {}
        });
    }
}
