<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class BranchRolePermissionSeeder extends Seeder
{
    /**
     * Create branch-scoped roles and permissions so any user assigned to a branch
     * can manage their own branch's purchases, sales, stock, and reports without
     * seeing or affecting any other branch's data or activities.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Ensure all standard module permissions exist first
        $this->call(AllModulesPermissionsSeeder::class);

        // Branch-specific isolation permissions
        $branchPermissions = [
            'branch.own_only',
            'branch.dashboard.view',
            'branch.purchases.manage',
            'branch.sales.manage',
            'branch.stock.manage',
            'branch.vouchers.manage',
            'branch.reports.view',
            'activity.logs.view',
        ];

        foreach ($branchPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Per-branch access permission for each existing branch
        foreach (Branch::all() as $branch) {
            Permission::firstOrCreate(['name' => 'branch.' . $branch->id . '.access']);
        }

        // 1. Branch Admin / Manager Role (full operational control within assigned branch only)
        $branchAdminRole = Role::firstOrCreate(['name' => 'Branch Admin']);
        $branchAdminPerms = Permission::where(function ($q) {
            $q->where('name', 'like', 'home.%')
                ->orWhere('name', 'like', 'profile.%')
                ->orWhere('name', 'like', 'products.%')
                ->orWhere('name', 'like', 'categories.%')
                ->orWhere('name', 'like', 'subcategories.%')
                ->orWhere('name', 'like', 'brands.%')
                ->orWhere('name', 'like', 'units.%')
                ->orWhere('name', 'like', 'warehouse.view')
                ->orWhere('name', 'like', 'warehouse.stock.%')
                ->orWhere('name', 'like', 'opening.stock.%')
                ->orWhere('name', 'like', 'stock.transfer.%')
                ->orWhere('name', 'like', 'stock.adjust.%')
                ->orWhere('name', 'like', 'stocks.%')
                ->orWhere('name', 'like', 'purchases.%')
                ->orWhere('name', 'like', 'purchase.returns.%')
                ->orWhere('name', 'like', 'vendors.%')
                ->orWhere('name', 'like', 'vendor.bilties.%')
                ->orWhere('name', 'like', 'inward.gatepass.%')
                ->orWhere('name', 'like', 'sales.%')
                ->orWhere('name', 'like', 'sales.returns.%')
                ->orWhere('name', 'like', 'customers.%')
                ->orWhere('name', 'like', 'customer_types.%')
                ->orWhere('name', 'like', 'customer.ledger.%')
                ->orWhere('name', 'like', 'bookings.%')
                ->orWhere('name', 'like', 'vouchers.%')
                ->orWhere('name', 'like', 'expense.voucher.%')
                ->orWhere('name', 'like', 'receipts.voucher.%')
                ->orWhere('name', 'like', 'journal.voucher.%')
                ->orWhere('name', 'like', 'payment.voucher.%')
                ->orWhere('name', 'like', 'income.voucher.%')
                ->orWhere('name', 'like', 'item.stock.report.%')
                ->orWhere('name', 'like', 'purchase.report.%')
                ->orWhere('name', 'like', 'sale.report.%')
                ->orWhere('name', 'like', 'reporting.%')
                ->orWhere('name', 'like', 'recovery.report.%')
                ->orWhere('name', 'like', 'payable.report.%')
                ->orWhere('name', 'like', 'parties.balance.report.%')
                ->orWhere('name', 'like', 'inventory.onhand.%')
                ->orWhere('name', 'like', 'vendor.ledger.%')
                ->orWhere('name', 'like', 'users.view')
                ->orWhere('name', 'like', 'users.create')
                ->orWhere('name', 'like', 'users.edit')
                ->orWhereIn('name', [
                    'branch.own_only',
                    'branch.dashboard.view',
                    'branch.purchases.manage',
                    'branch.sales.manage',
                    'branch.stock.manage',
                    'branch.vouchers.manage',
                    'branch.reports.view',
                    'activity.logs.view',
                ]);
        })->get();
        $branchAdminRole->syncPermissions($branchAdminPerms);

        // 2. Branch Sales / Cashier Role
        $branchSalesRole = Role::firstOrCreate(['name' => 'Branch Sales']);
        $branchSalesPerms = Permission::whereIn('name', [
            'home.view',
            'profile.view',
            'profile.edit',
            'products.view',
            'stocks.view',
            'warehouse.stock.view',
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.returns.view',
            'sales.returns.create',
            'customers.view',
            'customers.create',
            'customers.edit',
            'customer.ledger.view',
            'bookings.view',
            'bookings.create',
            'receipts.voucher.view',
            'receipts.voucher.create',
            'sale.report.view',
            'branch.own_only',
            'branch.dashboard.view',
            'branch.sales.manage',
        ])->get();
        $branchSalesRole->syncPermissions($branchSalesPerms);

        // 3. Branch Purchase & Stock Role
        $branchPurchaseRole = Role::firstOrCreate(['name' => 'Branch Purchase']);
        $branchPurchasePerms = Permission::whereIn('name', [
            'home.view',
            'profile.view',
            'profile.edit',
            'products.view',
            'products.create',
            'stocks.view',
            'warehouse.stock.view',
            'warehouse.stock.edit',
            'opening.stock.view',
            'opening.stock.create',
            'stock.transfer.view',
            'stock.transfer.create',
            'stock.adjust.view',
            'stock.adjust.create',
            'purchases.view',
            'purchases.create',
            'purchases.edit',
            'purchase.returns.view',
            'purchase.returns.create',
            'vendors.view',
            'vendors.create',
            'vendors.edit',
            'vendor.ledger.view',
            'inward.gatepass.view',
            'inward.gatepass.create',
            'payment.voucher.view',
            'payment.voucher.create',
            'purchase.report.view',
            'item.stock.report.view',
            'inventory.onhand.view',
            'branch.own_only',
            'branch.dashboard.view',
            'branch.purchases.manage',
            'branch.stock.manage',
        ])->get();
        $branchPurchaseRole->syncPermissions($branchPurchasePerms);

        // Ensure Super Admin has all permissions
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->syncPermissions(Permission::all());
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
