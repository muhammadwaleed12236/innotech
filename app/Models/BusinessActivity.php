<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BusinessActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'module',
        'activity_type',
        'title',
        'description',
        'icon',
        'badge_color',
        'user_id',
        'user_name',
        'reference_no',
        'amount',
        'branch_id',
        'warehouse_id',
        'subject_type',
        'subject_id',
        'action_url',
        'metadata',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Scopes\BranchScope);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Log a new business activity.
     */
    public static function log(array $data): self
    {
        $userId = $data['user_id'] ?? (Auth::check() ? Auth::id() : null);
        $userName = $data['user_name'] ?? (Auth::check() ? Auth::user()->name : 'System');

        // Avoid logging exact duplicates within the same minute
        if (!empty($data['reference_no']) && !empty($data['activity_type'])) {
            $existing = self::where('reference_no', $data['reference_no'])
                ->where('activity_type', $data['activity_type'])
                ->first();
            if ($existing) {
                return $existing;
            }
        }

        return self::create([
            'module' => $data['module'] ?? 'general',
            'activity_type' => $data['activity_type'] ?? 'action',
            'title' => $data['title'] ?? 'Business Activity',
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? 'fa-bell',
            'badge_color' => $data['badge_color'] ?? 'primary',
            'user_id' => $userId,
            'user_name' => $userName,
            'reference_no' => $data['reference_no'] ?? null,
            'amount' => isset($data['amount']) ? (float) $data['amount'] : null,
            'branch_id' => $data['branch_id'] ?? null,
            'warehouse_id' => $data['warehouse_id'] ?? null,
            'subject_type' => $data['subject_type'] ?? null,
            'subject_id' => $data['subject_id'] ?? null,
            'action_url' => $data['action_url'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'created_at' => $data['created_at'] ?? now(),
        ]);
    }

    /**
     * Backfill activities from existing real records in database.
     */
    public static function backfillFromExisting(): void
    {
        // 1. Recent Sales
        try {
            $sales = Sale::with('customer_relation')->latest()->limit(25)->get();
            foreach ($sales as $sale) {
                $ref = $sale->invoice_no ?: ('INV-' . $sale->id);
                $cust = $sale->customer_relation ? $sale->customer_relation->customer_name : ($sale->walkin_name ?: 'Customer');
                $actionUrl = route('sales.invoice', $sale->id);
                self::log([
                    'module' => 'sales',
                    'activity_type' => 'sale_created',
                    'title' => "Sale Invoice #{$ref}",
                    'description' => "Invoice generated for {$cust} with net value PKR " . number_format($sale->total_net, 2),
                    'icon' => 'fa-receipt',
                    'badge_color' => 'success',
                    'reference_no' => $ref,
                    'amount' => $sale->total_net,
                    'branch_id' => $sale->branch_id ?? null,
                    'subject_type' => Sale::class,
                    'subject_id' => $sale->id,
                    'action_url' => $actionUrl,
                    'created_at' => $sale->created_at,
                ]);
            }
        } catch (\Throwable $e) {}

        // 2. Recent Purchases
        try {
            $purchases = Purchase::with('vendor')->latest()->limit(20)->get();
            foreach ($purchases as $p) {
                $ref = $p->invoice_no ?: ('PO-' . $p->id);
                $vend = $p->vendor ? $p->vendor->name : 'Supplier';
                $actionUrl = route('purchase.invoice', $p->id);
                self::log([
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
                    'action_url' => $actionUrl,
                    'created_at' => $p->created_at,
                ]);
            }
        } catch (\Throwable $e) {}

        // 3. Recent Sale Returns
        try {
            $returns = SaleReturn::latest()->limit(10)->get();
            foreach ($returns as $sr) {
                $ref = 'SR-' . $sr->id;
                self::log([
                    'module' => 'sales',
                    'activity_type' => 'sale_return',
                    'title' => "Sale Return #{$ref}",
                    'description' => "Sale return processed for PKR " . number_format($sr->total_net ?? $sr->net_amount ?? 0, 2),
                    'icon' => 'fa-rotate-left',
                    'badge_color' => 'rose',
                    'reference_no' => $ref,
                    'amount' => $sr->total_net ?? $sr->net_amount ?? 0,
                    'action_url' => route('sale.return.view', $sr->id),
                    'created_at' => $sr->created_at,
                ]);
            }
        } catch (\Throwable $e) {}

        // 4. Recent Purchase Returns
        try {
            $pReturns = PurchaseReturn::latest()->limit(10)->get();
            foreach ($pReturns as $pr) {
                $ref = 'PR-' . $pr->id;
                self::log([
                    'module' => 'purchases',
                    'activity_type' => 'purchase_return',
                    'title' => "Purchase Return #{$ref}",
                    'description' => "Return to vendor processed for PKR " . number_format($pr->net_amount ?? 0, 2),
                    'icon' => 'fa-arrow-rotate-left',
                    'badge_color' => 'amber',
                    'reference_no' => $ref,
                    'amount' => $pr->net_amount ?? 0,
                    'action_url' => route('purchase.return.view', $pr->id),
                    'created_at' => $pr->created_at,
                ]);
            }
        } catch (\Throwable $e) {}

        // 5. Recent Expense Vouchers
        try {
            $expenses = ExpenseVoucher::latest()->limit(15)->get();
            foreach ($expenses as $exp) {
                $ref = 'EXP-' . $exp->id;
                self::log([
                    'module' => 'finance',
                    'activity_type' => 'expense_recorded',
                    'title' => "Expense Recorded ({$exp->type})",
                    'description' => "Expense voucher #{$ref} recorded for PKR " . number_format($exp->total_amount, 2),
                    'icon' => 'fa-money-bill-wave',
                    'badge_color' => 'danger',
                    'reference_no' => $ref,
                    'amount' => $exp->total_amount,
                    'created_at' => $exp->created_at,
                ]);
            }
        } catch (\Throwable $e) {}

        // 6. Recent Voucher Masters
        try {
            $vouchers = VoucherMaster::latest()->limit(15)->get();
            foreach ($vouchers as $vm) {
                $ref = $vm->voucher_no ?: ('VCH-' . $vm->id);
                $type = ucfirst($vm->voucher_type ?? 'Voucher');
                $color = match($vm->voucher_type) {
                    'receipt' => 'success',
                    'payment' => 'danger',
                    'expense' => 'warning',
                    default => 'info',
                };
                self::log([
                    'module' => 'finance',
                    'activity_type' => 'voucher_' . ($vm->voucher_type ?? 'entry'),
                    'title' => "{$type} Voucher #{$ref}",
                    'description' => "Financial {$type} voucher #{$ref} processed for PKR " . number_format($vm->total_amount, 2),
                    'icon' => 'fa-file-invoice-dollar',
                    'badge_color' => $color,
                    'reference_no' => $ref,
                    'amount' => $vm->total_amount,
                    'created_at' => $vm->created_at,
                ]);
            }
        } catch (\Throwable $e) {}

        // 7. Recent Products Added
        try {
            $products = Product::latest()->limit(10)->get();
            foreach ($products as $prod) {
                $ref = $prod->code ?: ('PRD-' . $prod->id);
                self::log([
                    'module' => 'inventory',
                    'activity_type' => 'product_added',
                    'title' => "New Product Added: {$prod->name}",
                    'description' => "Product {$prod->name} ({$ref}) added into inventory catalog",
                    'icon' => 'fa-box',
                    'badge_color' => 'cyan',
                    'reference_no' => $ref,
                    'action_url' => route('productview', $prod->id),
                    'created_at' => $prod->created_at,
                ]);
            }
        } catch (\Throwable $e) {}

        // 8. Recent Customers Registered
        try {
            $customers = Customer::latest()->limit(10)->get();
            foreach ($customers as $cust) {
                $ref = $cust->customer_id ?: ('CUST-' . $cust->id);
                self::log([
                    'module' => 'partners',
                    'activity_type' => 'customer_registered',
                    'title' => "Customer Registered: {$cust->customer_name}",
                    'description' => "Account opened for {$cust->customer_name} ({$ref})",
                    'icon' => 'fa-user-check',
                    'badge_color' => 'emerald',
                    'reference_no' => $ref,
                    'action_url' => route('customers.index'),
                    'created_at' => $cust->created_at,
                ]);
            }
        } catch (\Throwable $e) {}

        // 9. Recent Suppliers Registered
        try {
            $vendors = Vendor::latest()->limit(10)->get();
            foreach ($vendors as $vend) {
                $ref = 'VND-' . $vend->id;
                self::log([
                    'module' => 'partners',
                    'activity_type' => 'supplier_registered',
                    'title' => "Supplier Onboarded: {$vend->name}",
                    'description' => "New vendor profile created for {$vend->name}",
                    'icon' => 'fa-truck-fast',
                    'badge_color' => 'purple',
                    'reference_no' => $ref,
                    'action_url' => route('vendors-ledger'),
                    'created_at' => $vend->created_at,
                ]);
            }
        } catch (\Throwable $e) {}

        // 10. Recent Stock Transfers
        try {
            $transfers = StockTransfer::latest()->limit(10)->get();
            foreach ($transfers as $st) {
                $ref = 'ST-' . $st->id;
                self::log([
                    'module' => 'stock',
                    'activity_type' => 'stock_transfer',
                    'title' => "Warehouse Stock Transfer #{$ref}",
                    'description' => "Inter-warehouse stock transfer movement recorded",
                    'icon' => 'fa-dolly',
                    'badge_color' => 'blue',
                    'reference_no' => $ref,
                    'created_at' => $st->created_at,
                ]);
            }
        } catch (\Throwable $e) {}

        // 11. Recent Stock Adjustments
        try {
            $adjustments = StockAdjustment::latest()->limit(10)->get();
            foreach ($adjustments as $sa) {
                $ref = 'SA-' . $sa->id;
                self::log([
                    'module' => 'stock',
                    'activity_type' => 'stock_adjustment',
                    'title' => "Stock Adjustment #{$ref}",
                    'description' => "Manual inventory reconciliation & adjustment performed",
                    'icon' => 'fa-sliders',
                    'badge_color' => 'orange',
                    'reference_no' => $ref,
                    'created_at' => $sa->created_at,
                ]);
            }
        } catch (\Throwable $e) {}
    }
}
