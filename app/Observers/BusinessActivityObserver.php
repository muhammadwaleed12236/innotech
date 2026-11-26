<?php

namespace App\Observers;

use App\Models\BusinessActivity;
use App\Models\Customer;
use App\Models\ExpenseVoucher;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VoucherMaster;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;

class BusinessActivityObserver
{
    private static function requestMeta(array $extra = []): array
    {
        $req = request();
        return array_merge([
            'ip' => $req ? $req->ip() : null,
            'user_agent' => $req ? substr((string) $req->userAgent(), 0, 200) : null,
        ], $extra);
    }

    private static function extractChanges(Model $model): array
    {
        $dirty = $model->getChanges();
        unset($dirty['updated_at'], $dirty['created_at'], $dirty['password'], $dirty['remember_token']);
        $changes = [];
        foreach ($dirty as $field => $newVal) {
            $changes[$field] = [
                'old' => $model->getOriginal($field),
                'new' => $newVal,
            ];
        }
        return $changes;
    }

    private static function registerUpdateAndDelete(string $modelClass, string $module, string $label, callable $refFn, ?callable $amountFn = null, ?callable $urlFn = null): void
    {
        $modelClass::updated(function ($model) use ($module, $label, $refFn, $amountFn, $urlFn) {
            try {
                $changes = self::extractChanges($model);
                if (empty($changes)) {
                    return;
                }
                $ref = $refFn($model);
                $changedFields = implode(', ', array_keys($changes));
                BusinessActivity::log([
                    'module' => $module,
                    'activity_type' => strtolower($label) . '_updated',
                    'title' => "{$label} Updated #{$ref}",
                    'description' => "Updated fields: {$changedFields}",
                    'icon' => 'fa-pen-to-square',
                    'badge_color' => 'warning',
                    'reference_no' => $ref,
                    'amount' => $amountFn ? $amountFn($model) : null,
                    'branch_id' => $model->branch_id ?? null,
                    'subject_type' => get_class($model),
                    'subject_id' => $model->id,
                    'action_url' => $urlFn ? $urlFn($model) : null,
                    'allow_repeat' => true,
                    'metadata' => self::requestMeta(['changes' => $changes]),
                ]);
            } catch (\Throwable $e) {}
        });

        $modelClass::deleted(function ($model) use ($module, $label, $refFn, $amountFn) {
            try {
                $ref = $refFn($model);
                BusinessActivity::log([
                    'module' => $module,
                    'activity_type' => strtolower($label) . '_deleted',
                    'title' => "{$label} Deleted #{$ref}",
                    'description' => "{$label} #{$ref} was deleted",
                    'icon' => 'fa-trash',
                    'badge_color' => 'danger',
                    'reference_no' => $ref,
                    'amount' => $amountFn ? $amountFn($model) : null,
                    'branch_id' => $model->branch_id ?? null,
                    'subject_type' => get_class($model),
                    'subject_id' => $model->id,
                    'allow_repeat' => true,
                    'metadata' => self::requestMeta(),
                ]);
            } catch (\Throwable $e) {}
        });
    }

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
                    'metadata' => self::requestMeta(),
                ]);
            } catch (\Throwable $e) {}
        });
        self::registerUpdateAndDelete(
            Sale::class,
            'sales',
            'Sale',
            fn ($s) => $s->invoice_no ?: ('INV-' . $s->id),
            fn ($s) => $s->total_net,
            fn ($s) => route('sales.invoice', $s->id)
        );

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
                    'metadata' => self::requestMeta(),
                ]);
            } catch (\Throwable $e) {}
        });
        self::registerUpdateAndDelete(
            Purchase::class,
            'purchases',
            'Purchase',
            fn ($p) => $p->invoice_no ?: ('PO-' . $p->id),
            fn ($p) => $p->net_amount,
            fn ($p) => route('purchase.invoice', $p->id)
        );

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
                    'metadata' => self::requestMeta(),
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
                    'metadata' => self::requestMeta(),
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
                    'branch_id' => $cust->branch_id ?? null,
                    'subject_type' => Customer::class,
                    'subject_id' => $cust->id,
                    'action_url' => route('customers.index'),
                    'metadata' => self::requestMeta(),
                ]);
            } catch (\Throwable $e) {}
        });
        self::registerUpdateAndDelete(
            Customer::class,
            'partners',
            'Customer',
            fn ($c) => $c->customer_id ?: ('CUST-' . $c->id),
            null,
            fn ($c) => route('customers.index')
        );

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
                    'branch_id' => $vend->branch_id ?? null,
                    'subject_type' => Vendor::class,
                    'subject_id' => $vend->id,
                    'action_url' => route('vendors-ledger'),
                    'metadata' => self::requestMeta(),
                ]);
            } catch (\Throwable $e) {}
        });
        self::registerUpdateAndDelete(
            Vendor::class,
            'partners',
            'Vendor',
            fn ($v) => 'VND-' . $v->id,
            null,
            fn ($v) => route('vendors-ledger')
        );

        // 7. Product
        Product::created(function ($prod) {
            try {
                $ref = $prod->item_code ?: ('PRD-' . $prod->id);
                BusinessActivity::log([
                    'module' => 'inventory',
                    'activity_type' => 'product_added',
                    'title' => "Product Added: {$prod->item_name}",
                    'description' => "Product {$prod->item_name} cataloged with code {$ref}",
                    'icon' => 'fa-box',
                    'badge_color' => 'cyan',
                    'reference_no' => $ref,
                    'branch_id' => $prod->branch_id ?? null,
                    'subject_type' => Product::class,
                    'subject_id' => $prod->id,
                    'action_url' => route('productview', $prod->id),
                    'metadata' => self::requestMeta(),
                ]);
            } catch (\Throwable $e) {}
        });
        self::registerUpdateAndDelete(
            Product::class,
            'inventory',
            'Product',
            fn ($p) => $p->item_code ?: ('PRD-' . $p->id),
            null,
            fn ($p) => route('productview', $p->id)
        );

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
                    'branch_id' => $exp->branch_id ?? null,
                    'subject_type' => ExpenseVoucher::class,
                    'subject_id' => $exp->id,
                    'metadata' => self::requestMeta(),
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
                    'branch_id' => $vm->branch_id ?? null,
                    'subject_type' => VoucherMaster::class,
                    'subject_id' => $vm->id,
                    'metadata' => self::requestMeta(),
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
                    'metadata' => self::requestMeta(),
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
                    'metadata' => self::requestMeta(),
                ]);
            } catch (\Throwable $e) {}
        });

        // 12. User Management
        User::created(function ($u) {
            try {
                BusinessActivity::log([
                    'module' => 'users',
                    'activity_type' => 'user_created',
                    'title' => "User Created: {$u->name}",
                    'description' => "New user account created ({$u->email})",
                    'icon' => 'fa-user-plus',
                    'badge_color' => 'info',
                    'reference_no' => 'USR-' . $u->id,
                    'branch_id' => $u->branch_id ?? null,
                    'subject_type' => User::class,
                    'subject_id' => $u->id,
                    'allow_repeat' => true,
                    'metadata' => self::requestMeta(),
                ]);
            } catch (\Throwable $e) {}
        });
        self::registerUpdateAndDelete(
            User::class,
            'users',
            'User',
            fn ($u) => 'USR-' . $u->id
        );

        // 13. Login & Logout Tracking
        Event::listen(Login::class, function (Login $event) {
            try {
                $u = $event->user;
                if (!$u) return;
                $branchId = method_exists($u, 'getEffectiveBranchId') ? $u->getEffectiveBranchId() : ($u->branch_id ?? null);
                BusinessActivity::log([
                    'module' => 'auth',
                    'activity_type' => 'user_login',
                    'title' => "User Logged In: {$u->name}",
                    'description' => "{$u->name} ({$u->email}) signed into the system",
                    'icon' => 'fa-right-to-bracket',
                    'badge_color' => 'success',
                    'user_id' => $u->id,
                    'user_name' => $u->name,
                    'reference_no' => 'LOGIN-' . $u->id,
                    'branch_id' => $branchId,
                    'allow_repeat' => true,
                    'metadata' => self::requestMeta(),
                ]);
            } catch (\Throwable $e) {}
        });

        Event::listen(Logout::class, function (Logout $event) {
            try {
                $u = $event->user;
                if (!$u) return;
                $branchId = method_exists($u, 'getEffectiveBranchId') ? $u->getEffectiveBranchId() : ($u->branch_id ?? null);
                BusinessActivity::log([
                    'module' => 'auth',
                    'activity_type' => 'user_logout',
                    'title' => "User Logged Out: {$u->name}",
                    'description' => "{$u->name} ({$u->email}) signed out",
                    'icon' => 'fa-right-from-bracket',
                    'badge_color' => 'secondary',
                    'user_id' => $u->id,
                    'user_name' => $u->name,
                    'reference_no' => 'LOGOUT-' . $u->id,
                    'branch_id' => $branchId,
                    'allow_repeat' => true,
                    'metadata' => self::requestMeta(),
                ]);
            } catch (\Throwable $e) {}
        });
    }
}
