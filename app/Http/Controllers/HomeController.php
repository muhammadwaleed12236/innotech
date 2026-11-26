<?php

namespace App\Http\Controllers;

use App\Models\BusinessActivity;
use App\Models\DashboardTask;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\SaleReturn;
use App\Models\PurchaseReturn;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Account;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\ExpenseVoucher;
use App\Models\VoucherMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request = null)
    {
        $request = $request ?: request();
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $usertype = $user->usertype;
        $userId = $user->id;

        if ($usertype == 'user') {
            return view('user_panel.dashboard', compact('userId'));
        } elseif ($usertype == 'admin' || $user->hasRole('admin') || $user->hasRole('Super Admin')) {

            // Super Admin vs Branch Admin Access Management
            $isSuperAdmin = $user->isSuperAdmin();
            $allBranches = \App\Models\Branch::orderBy('name')->get();

            if ($isSuperAdmin) {
                $selectedBranchId = $request->get('branch_id', 'all');
                if ($selectedBranchId !== 'all' && !empty($selectedBranchId)) {
                    $currentBranchId = (int) $selectedBranchId;
                    $currentBranch = $allBranches->firstWhere('id', $currentBranchId);
                } else {
                    $currentBranchId = null; // null represents Enterprise / All Branches
                    $currentBranch = null;
                }
            } else {
                // Non-superadmin / Branch admin is strictly scoped to their own branch
                $currentBranchId = $user->getEffectiveBranchId() ?: -1;
                $currentBranch = $currentBranchId > 0 ? $allBranches->firstWhere('id', $currentBranchId) : null;
                $selectedBranchId = $currentBranchId > 0 ? (string) $currentBranchId : 'none';
            }

            // Query Branch Scoping Helper
            $scopeBranch = function ($query, $column = 'branch_id') use ($currentBranchId, $isSuperAdmin) {
                if ($currentBranchId !== null) {
                    $query->where($column, $currentBranchId);
                } elseif (!$isSuperAdmin) {
                    $query->whereRaw('1 = 0');
                }
                return $query;
            };

            // Smart Compact Currency Formatter (e.g. Rs. 45.2M, Rs. 250K, Rs. 4,623)
            $formatCompactCurrency = function ($val) {
                $num = (float) $val;
                if ($num >= 1000000000) {
                    return 'Rs. ' . number_format($num / 1000000000, 2) . 'B';
                } elseif ($num >= 1000000) {
                    return 'Rs. ' . number_format($num / 1000000, 2) . 'M';
                } elseif ($num >= 1000) {
                    return 'Rs. ' . number_format($num / 1000, 1) . 'K';
                } else {
                    return 'Rs. ' . number_format($num, 0);
                }
            };

            // Auto-backfill real activities from existing DB records if activity log is clean
            try {
                if (BusinessActivity::count() < 10) {
                    BusinessActivity::backfillFromExisting();
                }
            } catch (\Throwable $e) {}

            // Date Range Filter (Default: All Time to match actual modules)
            $filterPeriod = $request->get('period', 'all');
            $customFrom = $request->get('from_date');
            $customTo = $request->get('to_date');

            $today = Carbon::today();
            switch ($filterPeriod) {
                case 'all':
                    $filterPeriod = 'all';
                    $startDate = Carbon::create(2000, 1, 1)->startOfDay();
                    $endDate = Carbon::now()->addYears(5)->endOfDay();
                    $prevStartDate = Carbon::create(1990, 1, 1)->startOfDay();
                    $prevEndDate = Carbon::create(1999, 12, 31)->endOfDay();
                    $periodLabel = 'All Time';
                    break;
                case 'today':
                    $startDate = $today->copy()->startOfDay();
                    $endDate = $today->copy()->endOfDay();
                    $prevStartDate = $today->copy()->subDay()->startOfDay();
                    $prevEndDate = $today->copy()->subDay()->endOfDay();
                    $periodLabel = 'Today (' . $today->format('d M Y') . ')';
                    break;
                case 'yesterday':
                    $yesterday = $today->copy()->subDay();
                    $startDate = $yesterday->copy()->startOfDay();
                    $endDate = $yesterday->copy()->endOfDay();
                    $prevStartDate = $yesterday->copy()->subDay()->startOfDay();
                    $prevEndDate = $yesterday->copy()->subDay()->endOfDay();
                    $periodLabel = 'Yesterday (' . $yesterday->format('d M Y') . ')';
                    break;
                case 'this_week':
                    $startDate = $today->copy()->startOfWeek();
                    $endDate = $today->copy()->endOfWeek();
                    $prevStartDate = $today->copy()->subWeek()->startOfWeek();
                    $prevEndDate = $today->copy()->subWeek()->endOfWeek();
                    $periodLabel = 'This Week (' . $startDate->format('d M') . ' - ' . $endDate->format('d M') . ')';
                    break;
                case 'this_month':
                    $startDate = $today->copy()->startOfMonth();
                    $endDate = $today->copy()->endOfMonth();
                    $prevStartDate = $today->copy()->subMonth()->startOfMonth();
                    $prevEndDate = $today->copy()->subMonth()->endOfMonth();
                    $periodLabel = 'This Month (' . $today->format('F Y') . ')';
                    break;
                case 'last_month':
                    $lastMonth = $today->copy()->subMonth();
                    $startDate = $lastMonth->copy()->startOfMonth();
                    $endDate = $lastMonth->copy()->endOfMonth();
                    $prevStartDate = $lastMonth->copy()->subMonth()->startOfMonth();
                    $prevEndDate = $lastMonth->copy()->subMonth()->endOfMonth();
                    $periodLabel = 'Last Month (' . $startDate->format('F Y') . ')';
                    break;
                case 'this_year':
                    $startDate = $today->copy()->startOfYear();
                    $endDate = $today->copy()->endOfYear();
                    $prevStartDate = $today->copy()->subYear()->startOfYear();
                    $prevEndDate = $today->copy()->subYear()->endOfYear();
                    $periodLabel = 'This Year (' . $today->year . ')';
                    break;
                case 'custom':
                    $startDate = $customFrom ? Carbon::parse($customFrom)->startOfDay() : $today->copy()->startOfMonth();
                    $endDate = $customTo ? Carbon::parse($customTo)->endOfDay() : $today->copy()->endOfMonth();
                    $diffDays = max(1, $startDate->diffInDays($endDate) + 1);
                    $prevEndDate = $startDate->copy()->subDay()->endOfDay();
                    $prevStartDate = $prevEndDate->copy()->subDays($diffDays - 1)->startOfDay();
                    $periodLabel = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
                    break;
                default:
                    $filterPeriod = 'all';
                    $startDate = Carbon::create(2000, 1, 1)->startOfDay();
                    $endDate = Carbon::now()->addYears(5)->endOfDay();
                    $prevStartDate = Carbon::create(1990, 1, 1)->startOfDay();
                    $prevEndDate = Carbon::create(1999, 12, 31)->endOfDay();
                    $periodLabel = 'All Time';
                    break;
            }

            $startDtStr = $startDate->format('Y-m-d H:i:s');
            $endDtStr = $endDate->format('Y-m-d H:i:s');
            $prevStartDtStr = $prevStartDate->format('Y-m-d H:i:s');
            $prevEndDtStr = $prevEndDate->format('Y-m-d H:i:s');
            $startDateOnly = $startDate->format('Y-m-d');
            $endDateOnly = $endDate->format('Y-m-d');
            $prevStartDateOnly = $prevStartDate->format('Y-m-d');
            $prevEndDateOnly = $prevEndDate->format('Y-m-d');

            // Growth % calculation helper
            $calcGrowth = function ($curr, $prev) {
                $curr = (float) $curr;
                $prev = (float) $prev;
                if ($prev > 0) {
                    $pct = round((($curr - $prev) / $prev) * 100, 1);
                    return [
                        'value' => abs($pct),
                        'direction' => $pct >= 0 ? 'up' : 'down',
                        'raw' => $pct,
                        'text' => ($pct >= 0 ? '+' : '-') . abs($pct) . '%'
                    ];
                }
                return [
                    'value' => $curr > 0 ? 100 : 0,
                    'direction' => 'up',
                    'raw' => $curr > 0 ? 100 : 0,
                    'text' => $curr > 0 ? '+100%' : '0%'
                ];
            };

            // Quick Business Counts
            $categoryCount = $user->can('categories.view') ? DB::table('categories')->count() : null;
            $subcategoryCount = $user->can('subcategories.view') ? DB::table('subcategories')->count() : null;
            $productCount = $user->can('products.view') ? $scopeBranch(DB::table('products'))->where('is_active', 1)->count() : null;
            $totalCatalogProducts = $user->can('products.view') ? $scopeBranch(DB::table('products'))->count() : null;
            $customerscount = $user->can('customers.view') ? $scopeBranch(DB::table('customers'))->count() : null;
            $newCustomersCount = $user->can('customers.view') ? $scopeBranch(DB::table('customers'))->whereBetween('created_at', [$startDtStr, $endDtStr])->count() : 0;
            $vendorCount = $user->can('vendors.view') ? $scopeBranch(DB::table('vendors'))->count() : null;
            $newVendorsCount = $user->can('vendors.view') ? $scopeBranch(DB::table('vendors'))->whereBetween('created_at', [$startDtStr, $endDtStr])->count() : 0;
            $employeeCount = $user->can('hr.employees.view') ? (
                \Illuminate\Support\Facades\Schema::hasTable('hr_employees') ? DB::table('hr_employees')->count() : DB::table('users')->count()
            ) : null;

            // 1. SALES METRICS
            $salesPeriod = 0;
            $salesPrevPeriod = 0;
            $salesToday = 0;
            $salesThisWeek = 0;
            $salesThisMonth = 0;
            $salesReturnsPeriod = 0;
            $salesReturnsPrevPeriod = 0;
            $salesCashPeriod = 0;
            $salesCreditPeriod = 0;
            $salesChartStats = [
                'daily' => ['series' => [], 'categories' => [], 'sales' => array_fill(0, 7, 0), 'purchases' => array_fill(0, 7, 0)],
                'weekly' => ['series' => [], 'categories' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'], 'sales' => array_fill(0, 4, 0), 'purchases' => array_fill(0, 4, 0)],
                'monthly' => ['series' => [], 'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], 'sales' => array_fill(0, 12, 0), 'purchases' => array_fill(0, 12, 0)],
            ];
            $salesByCategory = collect();
            $topProducts = collect();
            $topCustomers = collect();
            $recentSales = collect();

            if ($user->can('sales.view')) {
                $salesQuery = $scopeBranch(DB::table('sales'))
                    ->whereNotIn('sale_status', ['cancelled', 'returned']);
                if ($filterPeriod !== 'all') {
                    $salesQuery->whereBetween('created_at', [$startDtStr, $endDtStr]);
                }
                $salesRaw = (float) $salesQuery->sum('total_net');

                $salesPrevPeriod = (float) $scopeBranch(DB::table('sales'))
                    ->whereBetween('created_at', [$prevStartDtStr, $prevEndDtStr])
                    ->whereNotIn('sale_status', ['cancelled', 'returned'])
                    ->sum('total_net');

                $salesToday = (float) $scopeBranch(DB::table('sales'))
                    ->whereDate('created_at', $today)
                    ->whereNotIn('sale_status', ['cancelled', 'returned'])
                    ->sum('total_net');

                $salesThisWeek = (float) $scopeBranch(DB::table('sales'))
                    ->whereBetween('created_at', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()])
                    ->whereNotIn('sale_status', ['cancelled', 'returned'])
                    ->sum('total_net');

                $salesThisMonth = (float) $scopeBranch(DB::table('sales'))
                    ->whereBetween('created_at', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
                    ->whereNotIn('sale_status', ['cancelled', 'returned'])
                    ->sum('total_net');

                // Cash vs Credit Sales
                $salesCashQuery = $scopeBranch(DB::table('sales'))
                    ->whereNotIn('sale_status', ['cancelled', 'returned']);
                if ($filterPeriod !== 'all') {
                    $salesCashQuery->whereBetween('created_at', [$startDtStr, $endDtStr]);
                }
                $salesCashPeriod = (float) $salesCashQuery->sum('cash');
                $salesCreditPeriod = max(0, $salesRaw - $salesCashPeriod);

                if ($user->can('sales.returns.view')) {
                    $srQuery = DB::table('sale_returns')
                        ->leftJoin('sales', 'sales.id', '=', 'sale_returns.sale_id');
                    if ($currentBranchId !== null) {
                        $srQuery->where('sales.branch_id', $currentBranchId);
                    } elseif (!$isSuperAdmin) {
                        $srQuery->whereRaw('1 = 0');
                    }
                    if ($filterPeriod !== 'all') {
                        $srQuery->whereBetween('sale_returns.created_at', [$startDtStr, $endDtStr]);
                    }
                    $salesReturnsPeriod = (float) $srQuery->sum('sale_returns.net_amount');

                    $srPrevQuery = DB::table('sale_returns')
                        ->leftJoin('sales', 'sales.id', '=', 'sale_returns.sale_id')
                        ->whereBetween('sale_returns.created_at', [$prevStartDtStr, $prevEndDtStr]);
                    if ($currentBranchId !== null) {
                        $srPrevQuery->where('sales.branch_id', $currentBranchId);
                    } elseif (!$isSuperAdmin) {
                        $srPrevQuery->whereRaw('1 = 0');
                    }
                    $salesReturnsPrevPeriod = (float) $srPrevQuery->sum('sale_returns.net_amount');
                }

                $salesPeriod = max(0, $salesRaw - $salesReturnsPeriod);

                // Sales vs Purchase Comparative Trajectory (Last 7 Days)
                $dailyLabels = collect(range(6, 0))->map(fn($i) => Carbon::today()->subDays($i)->format('d M'));
                $dailySalesData = collect(range(6, 0))->map(function ($i) use ($scopeBranch) {
                    $date = Carbon::today()->subDays($i)->format('Y-m-d');
                    return (float) $scopeBranch(DB::table('sales'))
                        ->whereDate('created_at', $date)
                        ->whereNotIn('sale_status', ['cancelled', 'returned'])
                        ->sum('total_net');
                });

                $dailyPurchasesData = collect(range(6, 0))->map(function ($i) use ($user, $scopeBranch) {
                    if (!$user->can('purchases.view')) return 0;
                    $date = Carbon::today()->subDays($i)->format('Y-m-d');
                    return (float) $scopeBranch(DB::table('purchases'))
                        ->whereDate('purchase_date', $date)
                        ->whereIn('status_purchase', ['approved', 'posted', 'Partial'])
                        ->sum('net_amount');
                });

                $weeklyLabels = ['3 Weeks Ago', '2 Weeks Ago', 'Last Week', 'This Week'];
                $weeklySalesData = collect([3, 2, 1, 0])->map(function ($i) use ($scopeBranch) {
                    $wStart = Carbon::now()->startOfWeek()->subWeeks($i);
                    $wEnd = $wStart->copy()->endOfWeek();
                    return (float) $scopeBranch(DB::table('sales'))
                        ->whereBetween('created_at', [$wStart, $wEnd])
                        ->whereNotIn('sale_status', ['cancelled', 'returned'])
                        ->sum('total_net');
                });
                $weeklyPurchData = collect([3, 2, 1, 0])->map(function ($i) use ($user, $scopeBranch) {
                    if (!$user->can('purchases.view')) return 0;
                    $wStart = Carbon::now()->startOfWeek()->subWeeks($i);
                    $wEnd = $wStart->copy()->endOfWeek();
                    return (float) $scopeBranch(DB::table('purchases'))
                        ->whereBetween('purchase_date', [$wStart->format('Y-m-d'), $wEnd->format('Y-m-d')])
                        ->whereIn('status_purchase', ['approved', 'posted', 'Partial'])
                        ->sum('net_amount');
                });

                $months = range(1, 12);
                $monthLabels = collect($months)->map(fn($m) => Carbon::create()->month($m)->format('M'));
                $monthlySalesData = collect($months)->map(function ($m) use ($scopeBranch) {
                    return (float) $scopeBranch(DB::table('sales'))
                        ->whereMonth('created_at', $m)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->whereNotIn('sale_status', ['cancelled', 'returned'])
                        ->sum('total_net');
                });
                $monthlyPurchData = collect($months)->map(function ($m) use ($user, $scopeBranch) {
                    if (!$user->can('purchases.view')) return 0;
                    return (float) $scopeBranch(DB::table('purchases'))
                        ->whereMonth('purchase_date', $m)
                        ->whereYear('purchase_date', Carbon::now()->year)
                        ->whereIn('status_purchase', ['approved', 'posted', 'Partial'])
                        ->sum('net_amount');
                });

                $salesChartStats = [
                    'daily' => [
                        'categories' => $dailyLabels,
                        'sales' => $dailySalesData,
                        'purchases' => $dailyPurchasesData
                    ],
                    'weekly' => [
                        'categories' => $weeklyLabels,
                        'sales' => $weeklySalesData,
                        'purchases' => $weeklyPurchData
                    ],
                    'monthly' => [
                        'categories' => $monthLabels,
                        'sales' => $monthlySalesData,
                        'purchases' => $monthlyPurchData
                    ]
                ];

                // Sales by Category
                $salesByCategory = DB::table('sale_items')
                    ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->leftJoin('products', 'products.id', '=', 'sale_items.product_id')
                    ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                    ->whereBetween('sales.created_at', [$startDtStr, $endDtStr])
                    ->whereNotIn('sales.sale_status', ['cancelled', 'returned'])
                    ->when($currentBranchId, fn($q) => $q->where('sales.branch_id', $currentBranchId))
                    ->select(DB::raw('COALESCE(categories.name, "General") as category_name'), DB::raw('SUM(sale_items.total) as total_amount'))
                    ->groupBy('category_name')
                    ->orderByDesc('total_amount')
                    ->limit(6)
                    ->get();

                // Top Selling Products
                $topProducts = DB::table('sale_items')
                    ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->whereBetween('sales.created_at', [$startDtStr, $endDtStr])
                    ->whereNotIn('sales.sale_status', ['cancelled', 'returned'])
                    ->when($currentBranchId, fn($q) => $q->where('sales.branch_id', $currentBranchId))
                    ->select(
                        DB::raw('COALESCE(sale_items.product_name, "Item") as product_name'),
                        DB::raw('SUM(sale_items.qty) as total_qty'),
                        DB::raw('SUM(sale_items.total) as total_revenue')
                    )
                    ->groupBy('product_name')
                    ->orderByDesc('total_revenue')
                    ->limit(6)
                    ->get();

                // Top Customers by Value
                $topCustomers = DB::table('sales')
                    ->join('customers', 'sales.customer_id', '=', 'customers.id')
                    ->whereBetween('sales.created_at', [$startDtStr, $endDtStr])
                    ->whereNotIn('sales.sale_status', ['cancelled', 'returned'])
                    ->when($currentBranchId, fn($q) => $q->where('sales.branch_id', $currentBranchId))
                    ->select(
                        'customers.id',
                        'customers.customer_name',
                        'customers.customer_id as cust_code',
                        DB::raw('COUNT(sales.id) as total_orders'),
                        DB::raw('SUM(sales.total_net) as total_sales')
                    )
                    ->groupBy('customers.id', 'customers.customer_name', 'customers.customer_id')
                    ->orderByDesc('total_sales')
                    ->limit(6)
                    ->get();

                // Recent Sales
                $recentSales = Sale::with('customer_relation')
                    ->when($currentBranchId, fn($q) => $q->where('branch_id', $currentBranchId))
                    ->latest()
                    ->limit(6)
                    ->get();
            }

            // 2. PURCHASES METRICS
            $purchasesPeriod = 0;
            $purchasesPrevPeriod = 0;
            $purchasesToday = 0;
            $purchasesThisWeek = 0;
            $purchasesThisMonth = 0;
            $purchasesReturnsPeriod = 0;
            $purchasesReturnsPrevPeriod = 0;
            $purchasesLifetime = 0;
            $salesLifetime = 0;
            $recentPurchases = collect();
            $topSuppliers = collect();

            if ($user->can('purchases.view')) {
                $purchasesQuery = $scopeBranch(DB::table('purchases'))
                    ->whereNotIn('status_purchase', ['cancelled', 'draft']);
                if ($filterPeriod !== 'all') {
                    $purchasesQuery->whereBetween('purchase_date', [$startDateOnly, $endDateOnly]);
                }
                $purchasesRaw = (float) $purchasesQuery->sum('net_amount');

                $purchasesPrevPeriod = (float) $scopeBranch(DB::table('purchases'))
                    ->whereBetween('purchase_date', [$prevStartDateOnly, $prevEndDateOnly])
                    ->whereNotIn('status_purchase', ['cancelled', 'draft'])
                    ->sum('net_amount');

                $purchasesToday = (float) $scopeBranch(DB::table('purchases'))
                    ->whereDate('purchase_date', $today)
                    ->whereNotIn('status_purchase', ['cancelled', 'draft'])
                    ->sum('net_amount');

                $purchasesThisWeek = (float) $scopeBranch(DB::table('purchases'))
                    ->whereBetween('purchase_date', [$today->copy()->startOfWeek()->format('Y-m-d'), $today->copy()->endOfWeek()->format('Y-m-d')])
                    ->whereNotIn('status_purchase', ['cancelled', 'draft'])
                    ->sum('net_amount');

                $purchasesThisMonth = (float) $scopeBranch(DB::table('purchases'))
                    ->whereBetween('purchase_date', [$today->copy()->startOfMonth()->format('Y-m-d'), $today->copy()->endOfMonth()->format('Y-m-d')])
                    ->whereNotIn('status_purchase', ['cancelled', 'draft'])
                    ->sum('net_amount');

                if ($user->can('purchase.returns.view')) {
                    $prQuery = DB::table('purchase_returns')
                        ->join('purchases', 'purchases.id', '=', 'purchase_returns.purchase_id');
                    if ($currentBranchId !== null) {
                        $prQuery->where('purchases.branch_id', $currentBranchId);
                    }
                    if ($filterPeriod !== 'all') {
                        $prQuery->whereBetween('purchase_returns.return_date', [$startDateOnly, $endDateOnly]);
                    }
                    $purchasesReturnsPeriod = (float) $prQuery->sum('purchase_returns.net_amount');

                    $prPrevQuery = DB::table('purchase_returns')
                        ->join('purchases', 'purchases.id', '=', 'purchase_returns.purchase_id');
                    if ($currentBranchId !== null) {
                        $prPrevQuery->where('purchases.branch_id', $currentBranchId);
                    }
                    $purchasesReturnsPrevPeriod = (float) $prPrevQuery
                        ->whereBetween('purchase_returns.return_date', [$prevStartDateOnly, $prevEndDateOnly])
                        ->sum('purchase_returns.net_amount');
                }

                // Net purchases matching PurchaseController logic
                $purchasesPeriod = max(0, $purchasesRaw - $purchasesReturnsPeriod);

                // Lifetime totals for reference
                $purchasesLifetime = (float) $scopeBranch(DB::table('purchases'))
                    ->whereNotIn('status_purchase', ['cancelled', 'draft'])
                    ->sum('net_amount');
                $salesLifetime = (float) $scopeBranch(DB::table('sales'))
                    ->whereNotIn('sale_status', ['cancelled', 'returned'])
                    ->sum('total_net');

                // Top Suppliers by Purchases
                $topSuppliersQuery = DB::table('purchases')
                    ->join('vendors', 'purchases.vendor_id', '=', 'vendors.id')
                    ->whereNotIn('purchases.status_purchase', ['cancelled', 'draft'])
                    ->when($currentBranchId, fn($q) => $q->where('purchases.branch_id', $currentBranchId));
                if ($filterPeriod !== 'all') {
                    $topSuppliersQuery->whereBetween('purchases.purchase_date', [$startDateOnly, $endDateOnly]);
                }
                $topSuppliers = $topSuppliersQuery->select(
                        'vendors.id',
                        'vendors.name as vendor_name',
                        DB::raw('COUNT(purchases.id) as total_bills'),
                        DB::raw('SUM(purchases.net_amount) as total_purchases')
                    )
                    ->groupBy('vendors.id', 'vendors.name')
                    ->orderByDesc('total_purchases')
                    ->limit(6)
                    ->get();

                $recentPurchases = Purchase::with('vendor')
                    ->when($currentBranchId, fn($q) => $q->where('branch_id', $currentBranchId))
                    ->latest()
                    ->limit(6)
                    ->get();
            }

            // 3. EXPENSES METRICS
            $expensesPeriod = 0;
            $expensesPrevPeriod = 0;
            $expensesToday = 0;
            $expenseBreakdown = collect();

            if ($user->can('expense.voucher.view') || $user->can('vouchers.view') || $user->can('all.vouchers.view')) {
                $hasExpVoucherBranch = \Illuminate\Support\Facades\Schema::hasColumn('expense_vouchers', 'branch_id');
                $hasVoucherMasterBranch = \Illuminate\Support\Facades\Schema::hasColumn('voucher_masters', 'branch_id');

                $expTodayV1 = DB::table('expense_vouchers')->whereDate('entry_date', $today);
                $expTodayV2 = DB::table('voucher_masters')->where('voucher_type', 'expense')->whereDate('date', $today);
                if ($currentBranchId !== null) {
                    if ($hasExpVoucherBranch) {
                        $expTodayV1->where('branch_id', $currentBranchId);
                    }
                    if ($hasVoucherMasterBranch) {
                        $expTodayV2->where('branch_id', $currentBranchId);
                    }
                }
                $expensesToday = (float)$expTodayV1->sum('total_amount') + (float)$expTodayV2->sum('total_amount');

                $expV1Query = DB::table('expense_vouchers');
                $expV2Query = DB::table('voucher_masters')->where('voucher_type', 'expense');
                if ($currentBranchId !== null) {
                    if ($hasExpVoucherBranch) {
                        $expV1Query->where('branch_id', $currentBranchId);
                    }
                    if ($hasVoucherMasterBranch) {
                        $expV2Query->where('branch_id', $currentBranchId);
                    }
                }
                if ($filterPeriod !== 'all') {
                    $expV1Query->whereBetween('entry_date', [$startDateOnly, $endDateOnly]);
                    $expV2Query->whereBetween('date', [$startDateOnly, $endDateOnly]);
                }
                $expensesPeriod = (float)$expV1Query->sum('total_amount') + (float)$expV2Query->sum('total_amount');

                $expV1Prev = DB::table('expense_vouchers')->whereBetween('entry_date', [$prevStartDateOnly, $prevEndDateOnly]);
                $expV2Prev = DB::table('voucher_masters')->where('voucher_type', 'expense')->whereBetween('date', [$prevStartDateOnly, $prevEndDateOnly]);
                if ($currentBranchId !== null) {
                    if ($hasExpVoucherBranch) {
                        $expV1Prev->where('branch_id', $currentBranchId);
                    }
                    if ($hasVoucherMasterBranch) {
                        $expV2Prev->where('branch_id', $currentBranchId);
                    }
                }
                $expensesPrevPeriod = (float)$expV1Prev->sum('total_amount') + (float)$expV2Prev->sum('total_amount');

                $expenseBreakdownQuery = DB::table('expense_vouchers');
                if ($currentBranchId !== null && $hasExpVoucherBranch) {
                    $expenseBreakdownQuery->where('branch_id', $currentBranchId);
                }
                if ($filterPeriod !== 'all') {
                    $expenseBreakdownQuery->whereBetween('entry_date', [$startDateOnly, $endDateOnly]);
                }
                $expenseBreakdown = $expenseBreakdownQuery
                    ->select('type as name', DB::raw('SUM(total_amount) as total'))
                    ->groupBy('type')
                    ->orderByDesc('total')
                    ->limit(5)
                    ->get();
            }

            // 4. PROFIT CALCULATIONS
            $grossProfitPeriod = null;
            $netProfitPeriod = null;
            $grossProfitPrevPeriod = null;
            $netProfitPrevPeriod = null;

            if ($user->can('profit.loss.report.view') || ($user->can('sales.view') && $user->can('purchases.view'))) {
                // Calculate Real Gross Profit for selected period (Sale Items vs Purchase Cost)
                $calcPeriodGross = function ($startStr, $endStr) use ($currentBranchId) {
                    $saleItemsQuery = DB::table('sale_items')
                        ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                        ->leftJoin('products', 'products.id', '=', 'sale_items.product_id')
                        ->whereBetween('sales.created_at', [$startStr, $endStr])
                        ->whereNotIn('sales.sale_status', ['cancelled', 'returned'])
                        ->when($currentBranchId, fn($q) => $q->where('sales.branch_id', $currentBranchId))
                        ->select(
                            'sale_items.price',
                            'sale_items.color',
                            'sale_items.total_pieces',
                            'sale_items.total',
                            'sale_items.is_manual',
                            'sale_items.purchase_price as manual_purchase_price',
                            'sales.total_extradiscount',
                            'sales.total_bill_amount',
                            'products.size_mode',
                            'products.pieces_per_m2',
                            'products.purchase_price_per_m2',
                            'products.purchase_price_per_piece'
                        )->get();

                    $totRev = 0;
                    $totCost = 0;
                    foreach ($saleItemsQuery as $si) {
                        $qty = (float)($si->total_pieces ?? 1);
                        if ($si->is_manual) {
                            $purchPrice = (float) $si->manual_purchase_price;
                        } else {
                            $purchPrice = 0;
                            if (!empty($si->color)) {
                                $decoded = base64_decode($si->color, true);
                                $json = $decoded ? json_decode($decoded, true) : json_decode($si->color, true);
                                if (is_array($json)) {
                                    $purchPrice = (float)($json['purch_price'] ?? 0);
                                }
                            }
                            if ($purchPrice <= 0) {
                                if ($si->size_mode === 'by_size') {
                                    $purchPrice = (float)($si->pieces_per_m2 ?? 0) * (float)($si->purchase_price_per_m2 ?? 0);
                                } else {
                                    $purchPrice = (float)($si->purchase_price_per_piece ?? 0);
                                }
                            }
                        }

                        $itemNet = (float) $si->total;
                        if ($si->total_bill_amount > 0 && $si->total_extradiscount > 0) {
                            $proportion = $itemNet / (float) $si->total_bill_amount;
                            $itemNet -= ($si->total_extradiscount * $proportion);
                        }

                        $totRev += $itemNet;
                        $totCost += ($purchPrice * $qty);
                    }

                    return $totRev - $totCost;
                };

                $grossProfitPeriod = $calcPeriodGross($startDtStr, $endDtStr);
                $netProfitPeriod = $grossProfitPeriod - $expensesPeriod;

                $grossProfitPrevPeriod = $calcPeriodGross($prevStartDtStr, $prevEndDtStr);
                $netProfitPrevPeriod = $grossProfitPrevPeriod - $expensesPrevPeriod;
            }

            // 5. RECEIVABLES & PAYABLES
            $totalReceivables = null;
            if ($user->can('recovery.report.view') || $user->can('customer.ledger.view') || $user->can('customers.view')) {
                if ($currentBranchId !== null) {
                    // Branch-scoped receivables: strictly from this branch's sales
                    $totalReceivables = (float) DB::table('sales')
                        ->where('branch_id', $currentBranchId)
                        ->whereNotIn('sale_status', ['cancelled', 'returned'])
                        ->selectRaw('COALESCE(SUM(GREATEST(total_net - COALESCE(cash, 0), 0)), 0) as due')
                        ->value('due');
                } else {
                    $customerIds = DB::table('customers')->pluck('id');
                    $totalReceivables = 0;
                    foreach ($customerIds as $cid) {
                        $latest = DB::table('customer_ledgers')->where('customer_id', $cid)->latest('id')->first();
                        if ($latest) {
                            $totalReceivables += (float)$latest->closing_balance;
                        } else {
                            $c = DB::table('customers')->where('id', $cid)->first();
                            $totalReceivables += (float)($c->opening_balance ?? 0);
                        }
                    }
                    if ($totalReceivables <= 0) {
                        $totalReceivables = (float) DB::table('sales')
                            ->whereNotIn('sale_status', ['cancelled', 'returned'])
                            ->selectRaw('COALESCE(SUM(total_net - COALESCE(cash, 0)), 0) as due')
                            ->value('due');
                    }
                }
            }

            $totalPayables = null;
            if ($user->can('payable.report.view') || $user->can('vendor.ledger.view') || $user->can('vendors.view')) {
                if ($currentBranchId !== null) {
                    // Branch-scoped payables: strictly from this branch's purchases
                    $totalPayables = (float) DB::table('purchases')
                        ->where('branch_id', $currentBranchId)
                        ->whereNotIn('status_purchase', ['cancelled', 'draft'])
                        ->sum('due_amount');
                } else {
                    $vendorIds = DB::table('vendors')->pluck('id');
                    $totalPayables = 0;
                    foreach ($vendorIds as $vid) {
                        $latest = DB::table('vendor_ledgers')->where('vendor_id', $vid)->latest('id')->first();
                        if ($latest) {
                            $totalPayables += (float)$latest->closing_balance;
                        } else {
                            $v = DB::table('vendors')->where('id', $vid)->first();
                            $totalPayables += (float)($v->opening_balance ?? 0);
                        }
                    }
                    if ($totalPayables <= 0) {
                        $totalPayables = (float) DB::table('purchases')
                            ->whereNotIn('status_purchase', ['cancelled', 'draft'])
                            ->sum('due_amount');
                    }
                }
            }

            // 6. CASH & BANK LIQUIDITY
            $cashAccounts = collect();
            $bankAccounts = collect();
            $totalCashBalance = 0;
            $totalBankBalance = 0;
            $totalCashAndBankBalance = null;

            if ($user->can('chart.of.accounts.view') || $user->can('balance.sheet.report.view') || $user->can('all.vouchers.view')) {
                $cashAccounts = Account::where('status', 1)
                    ->where(function($q) {
                        $q->where('title', 'like', '%cash%')
                          ->orWhereHas('head', fn($h) => $h->where('name', 'Cash'));
                    })->get();
                $totalCashBalance = (float) $cashAccounts->sum('current_balance');

                $bankAccounts = Account::where('status', 1)
                    ->where(function($q) {
                        $q->where('title', 'like', '%bank%')
                          ->orWhere('title', 'like', '%cheque%')
                          ->orWhereHas('head', fn($h) => $h->whereIn('name', ['Bank', 'Cheque']));
                    })->get();
                $totalBankBalance = (float) $bankAccounts->sum('current_balance');
                $totalCashAndBankBalance = $totalCashBalance + $totalBankBalance;
            }

            // 7. STOCK VALUATION & LOW STOCK ALERTS
            $totalStockValue = null;
            $totalStockPieces = 0;
            $lowStockProducts = collect();
            $outOfStockCount = 0;
            $warehouseStockStats = collect();

            if ($user->can('warehouse.stock.view') || $user->can('stocks.view') || $user->can('inventory.onhand') || $user->can('products.view') || $user->can('item.stock.report.view') || $user->hasRole('admin') || $user->hasRole('Super Admin')) {
                if ($currentBranchId !== null) {
                    // Branch-scoped stock valuation & pieces
                    $totalStockValue = (float) (DB::table('warehouse_stocks')
                        ->join('warehouses', 'warehouses.id', '=', 'warehouse_stocks.warehouse_id')
                        ->join('products', 'products.id', '=', 'warehouse_stocks.product_id')
                        ->where('warehouses.branch_id', $currentBranchId)
                        ->where('warehouse_stocks.total_pieces', '>', 0)
                        ->selectRaw('COALESCE(SUM(warehouse_stocks.total_pieces * (
                            CASE 
                                WHEN products.size_mode IN ("by_size", "by_m2") THEN COALESCE(products.pieces_per_m2, 0) * COALESCE(products.purchase_price_per_m2, 0)
                                WHEN COALESCE(products.purchase_price_per_piece, 0) > 0 THEN products.purchase_price_per_piece
                                WHEN COALESCE(products.purchase_price_per_box, 0) > 0 AND COALESCE(products.pieces_per_box, 0) > 0 THEN (products.purchase_price_per_box / products.pieces_per_box)
                                WHEN COALESCE(products.trade_price, 0) > 0 THEN products.trade_price
                                ELSE 0
                            END
                        )), 0) as total')
                        ->value('total') ?? 0);

                    $totalStockPieces = (float) DB::table('warehouse_stocks')
                        ->join('warehouses', 'warehouses.id', '=', 'warehouse_stocks.warehouse_id')
                        ->where('warehouses.branch_id', $currentBranchId)
                        ->sum('warehouse_stocks.total_pieces');
                } else {
                    try {
                        $stockReq = new Request([
                            'warehouse_id' => 'all',
                            'report_mode' => 'summary'
                        ]);
                        $reportingCtrl = app(ReportingController::class);
                        $stockRes = $reportingCtrl->fetchItemStock($stockReq);
                        $stockData = $stockRes->getData(true);
                        $totalStockValue = (float) ($stockData['grand_total'] ?? 0);
                    } catch (\Throwable $e) {
                        $totalStockValue = (float) (DB::table('warehouse_stocks')
                            ->join('products', 'products.id', '=', 'warehouse_stocks.product_id')
                            ->where('warehouse_stocks.total_pieces', '>', 0)
                            ->selectRaw('COALESCE(SUM(warehouse_stocks.total_pieces * (
                                CASE 
                                    WHEN products.size_mode IN ("by_size", "by_m2") THEN COALESCE(products.pieces_per_m2, 0) * COALESCE(products.purchase_price_per_m2, 0)
                                    WHEN COALESCE(products.purchase_price_per_piece, 0) > 0 THEN products.purchase_price_per_piece
                                    WHEN COALESCE(products.purchase_price_per_box, 0) > 0 AND COALESCE(products.pieces_per_box, 0) > 0 THEN (products.purchase_price_per_box / products.pieces_per_box)
                                    WHEN COALESCE(products.trade_price, 0) > 0 THEN products.trade_price
                                    ELSE 0
                                END
                            )), 0) as total')
                            ->value('total') ?? 0);
                    }

                    $totalStockPieces = (float) DB::table('warehouse_stocks')->sum('total_pieces');
                }

                $branchWhIds = $currentBranchId !== null
                    ? DB::table('warehouses')->where('branch_id', $currentBranchId)->pluck('id')->toArray()
                    : null;

                // Low stock products
                $lowStockProducts = Product::withSum(['warehouseStocks' => function ($q) use ($branchWhIds) {
                        if ($branchWhIds !== null) {
                            $q->whereIn('warehouse_id', empty($branchWhIds) ? [-1] : $branchWhIds);
                        }
                    }], 'total_pieces')
                    ->whereNotNull('alert_carton_quantity')
                    ->where('is_active', 1)
                    ->get()
                    ->filter(function ($p) {
                        $stock = (float) ($p->warehouse_stocks_sum_total_pieces ?? 0);
                        $ppb = $p->pieces_per_box > 0 ? $p->pieces_per_box : 1;
                        $cartons = floor($stock / $ppb);
                        $p->current_cartons = $cartons;
                        return $cartons < $p->alert_carton_quantity;
                    })
                    ->sortBy(function ($p) {
                        return $p->current_cartons - $p->alert_carton_quantity;
                    })
                    ->take(8)
                    ->values();

                // Out of stock products count
                $outOfStockCount = Product::withSum(['warehouseStocks' => function ($q) use ($branchWhIds) {
                        if ($branchWhIds !== null) {
                            $q->whereIn('warehouse_id', empty($branchWhIds) ? [-1] : $branchWhIds);
                        }
                    }], 'total_pieces')
                    ->where('is_active', 1)
                    ->get()
                    ->filter(fn($p) => (float)($p->warehouse_stocks_sum_total_pieces ?? 0) <= 0)
                    ->count();

                // Warehouse distribution
                $warehouseStockStats = DB::table('warehouse_stocks')
                    ->join('warehouses', 'warehouse_stocks.warehouse_id', '=', 'warehouses.id')
                    ->when($currentBranchId, fn($q) => $q->where('warehouses.branch_id', $currentBranchId))
                    ->select(
                        'warehouses.warehouse_name',
                        DB::raw('COALESCE(SUM(warehouse_stocks.total_pieces), 0) as total_qty')
                    )
                    ->groupBy('warehouses.id', 'warehouses.warehouse_name')
                    ->orderByDesc('total_qty')
                    ->get();
            }

            // 8. CASH FLOW (Inflow vs Outflow)
            $paymentInPeriod = 0;
            $paymentOutPeriod = 0;
            if ($user->can('vouchers.view') || $user->can('all.vouchers.view') || $user->can('receipts.voucher.view') || $user->can('payment.voucher.view')) {
                $v2Receipts = (float) DB::table('voucher_masters')->where('voucher_type', 'receipt')->whereBetween('date', [$startDateOnly, $endDateOnly])->sum('total_amount');
                $v1Receipts = (float) DB::table('receipts_vouchers')->whereBetween('receipt_date', [$startDtStr, $endDtStr])->sum('total_amount');
                $custPayments = (float) DB::table('customer_payments')->whereBetween('payment_date', [$startDtStr, $endDtStr])->sum('amount');
                $paymentInPeriod = $v2Receipts + $v1Receipts + $custPayments;

                $v2Payments = (float) DB::table('voucher_masters')->whereIn('voucher_type', ['payment', 'expense'])->whereBetween('date', [$startDateOnly, $endDateOnly])->sum('total_amount');
                $v1Payments = (float) DB::table('payment_vouchers')->whereBetween('receipt_date', [$startDtStr, $endDtStr])->sum('total_amount');
                $v1Expenses = (float) DB::table('expense_vouchers')->whereBetween('entry_date', [$startDateOnly, $endDateOnly])->sum('total_amount');
                $vendorPayments = (float) DB::table('vendor_payments')->whereBetween('payment_date', [$startDtStr, $endDtStr])->sum('amount');
                $paymentOutPeriod = $v2Payments + $v1Payments + $v1Expenses + $vendorPayments;
            }

            // 9. RECENT UNIFIED TRANSACTIONS (Top 12 recent records across modules)
            $recentTransactions = collect();
            try {
                $txnSales = Sale::with('customer_relation')
                    ->when($currentBranchId, fn($q) => $q->where('branch_id', $currentBranchId))
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(fn($s) => [
                        'id' => $s->id,
                        'type' => 'Sale',
                        'ref' => $s->invoice_no ?: ('INV-' . $s->id),
                        'party' => $s->customer_relation ? $s->customer_relation->customer_name : ($s->walkin_name ?: 'Walk-in'),
                        'amount' => (float) $s->total_net,
                        'status' => ucfirst($s->sale_status ?: 'Completed'),
                        'badge' => 'success',
                        'icon' => 'fa-receipt',
                        'date' => Carbon::parse($s->created_at)->format('d M, h:i A'),
                        'url' => route('sales.invoice', $s->id),
                        'created_at' => $s->created_at,
                    ]);

                $txnPurchases = Purchase::with('vendor')
                    ->when($currentBranchId, fn($q) => $q->where('branch_id', $currentBranchId))
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(fn($p) => [
                        'id' => $p->id,
                        'type' => 'Purchase',
                        'ref' => $p->invoice_no ?: ('PO-' . $p->id),
                        'party' => $p->vendor ? $p->vendor->name : 'Supplier',
                        'amount' => (float) $p->net_amount,
                        'status' => ucfirst($p->status_purchase ?: 'Posted'),
                        'badge' => 'primary',
                        'icon' => 'fa-cart-shopping',
                        'date' => Carbon::parse($p->created_at)->format('d M, h:i A'),
                        'url' => route('purchase.invoice', $p->id),
                        'created_at' => $p->created_at,
                    ]);

                $txnExpenses = ExpenseVoucher::latest()->limit(5)->get()->map(fn($e) => [
                    'id' => $e->id,
                    'type' => 'Expense',
                    'ref' => 'EXP-' . $e->id,
                    'party' => $e->type ?: 'Expense',
                    'amount' => (float) $e->total_amount,
                    'status' => 'Paid',
                    'badge' => 'danger',
                    'icon' => 'fa-money-bill-wave',
                    'date' => Carbon::parse($e->created_at)->format('d M, h:i A'),
                    'url' => '#',
                    'created_at' => $e->created_at,
                ]);

                $recentTransactions = $txnSales->concat($txnPurchases)->concat($txnExpenses)
                    ->sortByDesc('created_at')
                    ->take(10)
                    ->values();
            } catch (\Throwable $e) {}

            // 10. REAL-TIME ACTIVITY FEED (From business_activities table)
            $liveActivities = BusinessActivity::with(['user', 'branch', 'warehouse'])
                ->when($currentBranchId, fn($q) => $q->where('branch_id', $currentBranchId))
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            // 11. TODAY'S TASKS & REMINDERS
            $tasks = DashboardTask::with('assignee')
                ->when($currentBranchId, fn($q) => $q->where('branch_id', $currentBranchId))
                ->orderByRaw("FIELD(status, 'pending', 'in_progress', 'completed', 'cancelled')")
                ->orderBy('due_date', 'asc')
                ->limit(10)
                ->get();

            $taskPendingCount = DashboardTask::where('status', 'pending')
                ->when($currentBranchId, fn($q) => $q->where('branch_id', $currentBranchId))
                ->count();
            $taskCompletedCount = DashboardTask::where('status', 'completed')
                ->when($currentBranchId, fn($q) => $q->where('branch_id', $currentBranchId))
                ->count();
            $taskOverdueCount = DashboardTask::where('status', 'pending')
                ->where('due_date', '<', Carbon::today()->format('Y-m-d'))
                ->when($currentBranchId, fn($q) => $q->where('branch_id', $currentBranchId))
                ->count();

            $systemUsers = User::select('id', 'name')->orderBy('name')->get();

            // 12. SMART ALERTS & NOTIFICATIONS
            $smartAlerts = collect();
            if ($lowStockProducts->count() > 0) {
                $smartAlerts->push([
                    'type' => 'warning',
                    'icon' => 'fa-triangle-exclamation',
                    'title' => 'Low Stock Warning',
                    'message' => "{$lowStockProducts->count()} items have dropped below safety carton threshold.",
                    'action_url' => route('product'),
                    'action_text' => 'Inspect Stock',
                ]);
            }
            if ($totalReceivables > 0) {
                $smartAlerts->push([
                    'type' => 'info',
                    'icon' => 'fa-hand-holding-dollar',
                    'title' => 'Outstanding Receivables',
                    'message' => 'Total outstanding client balances stand at PKR ' . number_format($totalReceivables, 0),
                    'action_url' => route('report.customer.ledger'),
                    'action_text' => 'View Ledgers',
                ]);
            }
            if ($totalPayables > 0) {
                $smartAlerts->push([
                    'type' => 'danger',
                    'icon' => 'fa-clock-rotate-left',
                    'title' => 'Accounts Payable Due',
                    'message' => 'Total vendor liabilities stand at PKR ' . number_format($totalPayables, 0),
                    'action_url' => route('report.vendor.ledger'),
                    'action_text' => 'Payable Report',
                ]);
            }

            // 13. FMCG PRODUCT BATCHES & EXPIRY WATCHDOG
            $now = Carbon::today();
            $expiryBatches = collect();
            $expiredBatches = collect();
            $nearExpiryBatches = collect();
            $safeBatches = collect();
            $urgentExpiringList = collect();

            if (\Illuminate\Support\Facades\Schema::hasTable('product_batches')) {
                $batchQuery = DB::table('product_batches')
                    ->join('products', 'products.id', '=', 'product_batches.product_id')
                    ->leftJoin('warehouses', 'warehouses.id', '=', 'product_batches.warehouse_id');

                if ($currentBranchId !== null) {
                    $batchQuery->where('warehouses.branch_id', $currentBranchId);
                }

                $expiryBatches = $batchQuery->select(
                    'product_batches.*',
                    'products.item_name as product_name',
                    'products.trade_price',
                    'warehouses.warehouse_name'
                )->get();

                $expiredBatches = $expiryBatches->filter(function ($b) use ($now) {
                    return !empty($b->expiry_date) && Carbon::parse($b->expiry_date)->lt($now) && (float)$b->qty > 0;
                });

                $nearExpiryBatches = $expiryBatches->filter(function ($b) use ($now) {
                    if (empty($b->expiry_date) || (float)$b->qty <= 0) return false;
                    $exp = Carbon::parse($b->expiry_date);
                    return $exp->gte($now) && $exp->lte($now->copy()->addDays(90));
                });

                $safeBatches = $expiryBatches->filter(function ($b) use ($now) {
                    if (empty($b->expiry_date) || (float)$b->qty <= 0) return false;
                    return Carbon::parse($b->expiry_date)->gt($now->copy()->addDays(90));
                });

                $urgentExpiringList = $expiryBatches->filter(fn($b) => !empty($b->expiry_date) && (float)$b->qty > 0)
                    ->sortBy('expiry_date')
                    ->take(5)
                    ->map(function ($b) use ($now) {
                        $exp = Carbon::parse($b->expiry_date);
                        $daysLeft = (int) $now->diffInDays($exp, false);
                        return [
                            'product' => $b->product_name,
                            'batch_no' => $b->batch_no,
                            'warehouse' => $b->warehouse_name ?: 'Main Store',
                            'qty' => (float) $b->qty,
                            'expiry_date' => $exp->format('d M Y'),
                            'days_left' => $daysLeft,
                            'cost_val' => (float)$b->qty * (float)$b->cost_price,
                            'status' => $daysLeft < 0 ? 'expired' : ($daysLeft <= 30 ? 'critical' : ($daysLeft <= 90 ? 'warning' : 'safe')),
                        ];
                    })
                    ->values();
            }

            // Expiry Summary KPI
            $expiredQty = (float) $expiredBatches->sum('qty');
            $expiredVal = (float) $expiredBatches->sum(fn($b) => (float)$b->qty * (float)$b->cost_price);
            $nearExpiryQty = (float) $nearExpiryBatches->sum('qty');
            $nearExpiryVal = (float) $nearExpiryBatches->sum(fn($b) => (float)$b->qty * (float)$b->cost_price);
            $safeQty = (float) $safeBatches->sum('qty');

            $expiryStats = [
                'expired_count' => $expiredBatches->count(),
                'expired_qty' => $expiredQty,
                'expired_val' => $expiredVal,
                'expired_val_formatted' => $formatCompactCurrency($expiredVal),
                'near_count' => $nearExpiryBatches->count(),
                'near_qty' => $nearExpiryQty,
                'near_val' => $nearExpiryVal,
                'near_val_formatted' => $formatCompactCurrency($nearExpiryVal),
                'safe_count' => $safeBatches->count(),
                'safe_qty' => $safeQty,
                'total_tracked_batches' => $expiryBatches->count(),
            ];

            if ($expiredBatches->count() > 0) {
                $smartAlerts->push([
                    'type' => 'danger',
                    'icon' => 'fa-triangle-exclamation',
                    'title' => 'Expired Products Warning',
                    'message' => "{$expiredBatches->count()} batch(es) have passed expiry date ({$expiredQty} units). Action required for disposal or vendor return.",
                    'action_url' => '#',
                    'action_text' => 'Review Expired',
                ]);
            }
            if ($nearExpiryBatches->count() > 0) {
                $smartAlerts->push([
                    'type' => 'warning',
                    'icon' => 'fa-clock-rotate-left',
                    'title' => 'Near Expiry Alert (Within 90 Days)',
                    'message' => "{$nearExpiryBatches->count()} batch(es) nearing shelf-life expiry ({$nearExpiryQty} units). Prioritize dispatch.",
                    'action_url' => '#',
                    'action_text' => 'View Batches',
                ]);
            }

            // 14. MULTI-BRANCH ENTERPRISE MATRIX
            $multiBranchesSummary = collect();
            $totEnterpriseSales = 0;
            $branchesToSummarize = $isSuperAdmin ? $allBranches : ($currentBranchId ? $allBranches->where('id', $currentBranchId) : collect());
            foreach ($branchesToSummarize as $b) {
                $bSales = (float) DB::table('sales')
                    ->where('branch_id', $b->id)
                    ->whereBetween('created_at', [$startDtStr, $endDtStr])
                    ->whereNotIn('sale_status', ['cancelled', 'returned'])
                    ->sum('total_net');

                $bPurchases = (float) DB::table('purchases')
                    ->where('branch_id', $b->id)
                    ->whereBetween('purchase_date', [$startDateOnly, $endDateOnly])
                    ->whereIn('status_purchase', ['approved', 'posted', 'Partial'])
                    ->sum('net_amount');

                $bWarehousesCount = DB::table('warehouses')->where('branch_id', $b->id)->count();

                $multiBranchesSummary->push([
                    'id' => $b->id,
                    'name' => $b->name,
                    'address' => $b->address,
                    'number' => $b->number,
                    'sales' => $bSales,
                    'sales_formatted' => $formatCompactCurrency($bSales),
                    'purchases' => $bPurchases,
                    'purchases_formatted' => $formatCompactCurrency($bPurchases),
                    'warehouses_count' => $bWarehousesCount,
                    'is_selected' => ($currentBranchId !== null && (int)$currentBranchId === (int)$b->id),
                ]);

                $totEnterpriseSales += $bSales;
            }

            // Calculate branch share %
            $multiBranchesSummary = $multiBranchesSummary->map(function ($b) use ($totEnterpriseSales) {
                $share = $totEnterpriseSales > 0 ? round(($b['sales'] / $totEnterpriseSales) * 100, 1) : 0;
                $b['sales_share'] = $share;
                return $b;
            });

            // Initial task seeding if database has 0 tasks (Authentic FMCG Tasks)
            if (DashboardTask::count() === 0) {
                try {
                    DashboardTask::create([
                        'title' => 'Inspect Near-Expiry Batches in Main Warehouse',
                        'description' => 'Review batch shelf-life for food & dairy items expiring soon.',
                        'priority' => 'urgent',
                        'status' => 'pending',
                        'due_date' => Carbon::today(),
                    ]);
                    DashboardTask::create([
                        'title' => 'Dispatch Branch Stock Transfer #TR-104 to Lahore Branch',
                        'description' => 'Coordinate delivery van for fast-moving items transfer.',
                        'priority' => 'high',
                        'status' => 'pending',
                        'due_date' => Carbon::today(),
                    ]);
                    DashboardTask::create([
                        'title' => 'Follow up Pending Recovery from City Distributors',
                        'description' => 'Recovery officer assigned to clear overdue trade balances.',
                        'priority' => 'high',
                        'status' => 'pending',
                        'due_date' => Carbon::tomorrow(),
                    ]);
                    DashboardTask::create([
                        'title' => 'Reorder Fast-Moving Stock (Sunridge & Nestle)',
                        'description' => 'Carton levels running close to minimum safety threshold.',
                        'priority' => 'medium',
                        'status' => 'pending',
                        'due_date' => Carbon::today()->addDays(2),
                    ]);
                } catch (\Throwable $e) {}
            }

            // Top Selling Products formatted with progress bar percentages
            $displayTopProducts = collect();
            if ($topProducts->count() > 0) {
                $maxQty = (float) $topProducts->max('total_qty') ?: 1;
                $colors = ['#2563eb', '#10b981', '#f97316', '#8b5cf6', '#06b6d4'];
                $icons = ['fa-box-open', 'fa-cubes-stacked', 'fa-bottle-water', 'fa-jar', 'fa-basket-shopping'];
                $idx = 0;
                foreach ($topProducts->take(4) as $tp) {
                    $pct = min(100, max(15, round(($tp->total_qty / $maxQty) * 100)));
                    $displayTopProducts->push([
                        'name' => $tp->product_name,
                        'qty' => number_format($tp->total_qty) . ' Pcs',
                        'pct' => $pct,
                        'color' => $colors[$idx % count($colors)],
                        'icon' => $icons[$idx % count($icons)],
                    ]);
                    $idx++;
                }
            } else {
                // Pull real active products from database catalog
                $realProds = Product::where('is_active', 1)->take(4)->get();
                $colors = ['#2563eb', '#10b981', '#f97316', '#8b5cf6'];
                $idx = 0;
                foreach ($realProds as $rp) {
                    $displayTopProducts->push([
                        'name' => $rp->item_name ?: 'Product #' . $rp->id,
                        'qty' => '0 Pcs',
                        'pct' => 10,
                        'color' => $colors[$idx % count($colors)],
                        'icon' => 'fa-box',
                    ]);
                    $idx++;
                }
            }

            // Stock Status for Donut
            $totalActiveProd = Product::where('is_active', 1)->count();
            $lowCount = $lowStockProducts->count();
            $outCount = $outOfStockCount;
            $inStockCount = max(0, $totalActiveProd - $lowCount - $outCount);
            if ($totalActiveProd > 0) {
                $inStockPct = round(($inStockCount / $totalActiveProd) * 100);
                $lowStockPct = round(($lowCount / $totalActiveProd) * 100);
                $outStockPct = max(0, 100 - $inStockPct - $lowStockPct);
            } else {
                $inStockPct = 0;
                $lowStockPct = 0;
                $outStockPct = 0;
            }
            $stockStatus = [
                'in_stock' => $inStockPct,
                'low_stock' => $lowStockPct,
                'out_of_stock' => $outStockPct,
            ];

            // Business Analytics Ratio (100% dynamic without dummy fallbacks)
            $totRev = $salesPeriod;
            $totExp = $expensesPeriod;
            $sumVol = $salesPeriod + $purchasesPeriod;
            $salesRatio = $sumVol > 0 ? round(($salesPeriod / $sumVol) * 100) : ($salesPeriod > 0 ? 100 : 0);
            $purchRatio = $sumVol > 0 ? (100 - $salesRatio) : ($purchasesPeriod > 0 ? 100 : 0);
            $businessAnalytics = [
                'sales_ratio' => $salesRatio,
                'purchases_ratio' => $purchRatio,
                'total_revenue' => $totRev,
                'total_revenue_formatted' => 'Rs. ' . number_format($totRev, 0),
                'total_expenses' => $totExp,
                'total_expenses_formatted' => 'Rs. ' . number_format($totExp, 0),
            ];

            // Financial Overview 3-Pill Summary (100% dynamic without dummy fallbacks)
            $finIncome = $salesPeriod;
            $finExpenses = $expensesPeriod;
            $finProfit = ($netProfitPeriod !== null) ? $netProfitPeriod : ($salesPeriod - $purchasesPeriod - $expensesPeriod);

            $financialOverview = [
                'income' => $finIncome,
                'income_formatted' => 'Rs. ' . number_format($finIncome, 0),
                'expenses' => $finExpenses,
                'expenses_formatted' => 'Rs. ' . number_format($finExpenses, 0),
                'profit' => $finProfit,
                'profit_formatted' => 'Rs. ' . number_format($finProfit, 0),
            ];

            // Dynamic Chart Datasets
            if ($filterPeriod === 'all' || $filterPeriod === 'this_year') {
                $chartOverviewLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                $chartOverviewSales = $salesChartStats['monthly']['sales'] ?? array_fill(0, 12, 0);
                $chartOverviewPurchases = $salesChartStats['monthly']['purchases'] ?? array_fill(0, 12, 0);
                $chartFinancialWaveLabels = ['Jan', 'Mar', 'May', 'Jul', 'Sep', 'Nov'];
                $chartFinancialWaveData = [
                    (float)($salesChartStats['monthly']['sales'][0] ?? 0),
                    (float)($salesChartStats['monthly']['sales'][2] ?? 0),
                    (float)($salesChartStats['monthly']['sales'][4] ?? 0),
                    (float)($salesChartStats['monthly']['sales'][6] ?? 0),
                    (float)($salesChartStats['monthly']['sales'][8] ?? 0),
                    (float)($salesChartStats['monthly']['sales'][10] ?? 0),
                ];
            } elseif ($filterPeriod === 'this_month' || $filterPeriod === 'last_month') {
                $chartOverviewLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
                $chartOverviewSales = $salesChartStats['weekly']['sales'] ?? array_fill(0, 4, 0);
                $chartOverviewPurchases = $salesChartStats['weekly']['purchases'] ?? array_fill(0, 4, 0);
                $chartFinancialWaveLabels = ['W1', 'W2', 'W3', 'W4'];
                $chartFinancialWaveData = $salesChartStats['weekly']['sales'] ?? array_fill(0, 4, 0);
            } else {
                $chartOverviewLabels = $salesChartStats['daily']['categories'] ?? [];
                $chartOverviewSales = $salesChartStats['daily']['sales'] ?? [];
                $chartOverviewPurchases = $salesChartStats['daily']['purchases'] ?? [];
                $chartFinancialWaveLabels = $salesChartStats['daily']['categories'] ?? [];
                $chartFinancialWaveData = $salesChartStats['daily']['sales'] ?? [];
            }

            // Growth Objects for KPI Cards
            $growth = [
                'sales' => $calcGrowth($salesPeriod, $salesPrevPeriod),
                'purchases' => $calcGrowth($purchasesPeriod, $purchasesPrevPeriod),
                'sales_returns' => $calcGrowth($salesReturnsPeriod, $salesReturnsPrevPeriod),
                'purchases_returns' => $calcGrowth($purchasesReturnsPeriod, $purchasesReturnsPrevPeriod),
                'expenses' => $calcGrowth($expensesPeriod, $expensesPrevPeriod),
                'net_profit' => $calcGrowth($netProfitPeriod, $netProfitPrevPeriod),
            ];

            return view('admin_panel.dashboard', compact(
                'isSuperAdmin',
                'allBranches',
                'selectedBranchId',
                'currentBranch',
                'filterPeriod',
                'periodLabel',
                'startDateOnly',
                'endDateOnly',
                'categoryCount',
                'subcategoryCount',
                'productCount',
                'totalCatalogProducts',
                'customerscount',
                'newCustomersCount',
                'vendorCount',
                'newVendorsCount',
                'employeeCount',
                'salesPeriod',
                'salesToday',
                'salesThisWeek',
                'salesThisMonth',
                'salesCashPeriod',
                'salesCreditPeriod',
                'salesReturnsPeriod',
                'purchasesPeriod',
                'purchasesToday',
                'purchasesThisWeek',
                'purchasesThisMonth',
                'purchasesReturnsPeriod',
                'purchasesLifetime',
                'salesLifetime',
                'expensesPeriod',
                'expensesToday',
                'grossProfitPeriod',
                'netProfitPeriod',
                'totalReceivables',
                'totalPayables',
                'totalCashBalance',
                'totalBankBalance',
                'totalCashAndBankBalance',
                'cashAccounts',
                'bankAccounts',
                'totalStockValue',
                'totalStockPieces',
                'lowStockProducts',
                'outOfStockCount',
                'warehouseStockStats',
                'salesChartStats',
                'chartOverviewLabels',
                'chartOverviewSales',
                'chartOverviewPurchases',
                'chartFinancialWaveLabels',
                'chartFinancialWaveData',
                'salesByCategory',
                'topProducts',
                'topCustomers',
                'topSuppliers',
                'expenseBreakdown',
                'paymentInPeriod',
                'paymentOutPeriod',
                'recentSales',
                'recentPurchases',
                'recentTransactions',
                'liveActivities',
                'tasks',
                'taskPendingCount',
                'taskCompletedCount',
                'taskOverdueCount',
                'systemUsers',
                'smartAlerts',
                'growth',
                'displayTopProducts',
                'stockStatus',
                'businessAnalytics',
                'financialOverview',
                'expiryStats',
                'urgentExpiringList',
                'multiBranchesSummary'
            ));
        } else {
            return redirect()->back()->with('error', 'Unauthorized access');
        }
    }

    /**
     * AJAX Endpoint: Fetch real-time live business activities.
     */
    public function liveActivities(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;
        $branchId = $request->get('branch_id');
        $module = $request->get('module', 'all');
        $afterId = (int) $request->get('after_id', 0);

        $query = BusinessActivity::with(['user', 'branch', 'warehouse'])
            ->orderBy('created_at', 'desc');

        // Apply access restrictions
        $effectiveBranchId = (!$isSuperAdmin && $user && method_exists($user, 'getEffectiveBranchId') && $user->getEffectiveBranchId())
            ? $user->getEffectiveBranchId()
            : ($branchId && $branchId !== 'all' ? (int) $branchId : null);

        if ($effectiveBranchId) {
            $query->where('branch_id', $effectiveBranchId);
        } elseif (!$isSuperAdmin) {
            $query->whereRaw('1 = 0');
        }

        if (!empty($module) && $module !== 'all') {
            $query->where('module', $module);
        }

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $activities = $query->limit(25)->get()->map(function ($act) {
            return [
                'id' => $act->id,
                'module' => $act->module,
                'activity_type' => $act->activity_type,
                'title' => $act->title,
                'description' => $act->description,
                'icon' => $act->icon ?: 'fa-bell',
                'badge_color' => $act->badge_color ?: 'primary',
                'user_name' => $act->user_name ?: ($act->user ? $act->user->name : 'System'),
                'reference_no' => $act->reference_no,
                'amount' => $act->amount ? number_format($act->amount, 2) : null,
                'time_ago' => Carbon::parse($act->created_at)->diffForHumans(),
                'datetime' => Carbon::parse($act->created_at)->format('d M Y, h:i A'),
                'branch' => $act->branch ? $act->branch->name : null,
                'action_url' => $act->action_url,
            ];
        });

        return response()->json([
            'ok' => true,
            'count' => count($activities),
            'latest_id' => $activities->max('id') ?? $afterId,
            'activities' => $activities,
        ]);
    }

    /**
     * AJAX Endpoint: Real-time quick KPI updates.
     */
    public function liveStats(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;
        $branchId = $request->get('branch_id');
        $effectiveBranchId = (!$isSuperAdmin && $user && method_exists($user, 'getEffectiveBranchId') && $user->getEffectiveBranchId())
            ? $user->getEffectiveBranchId()
            : ($branchId && $branchId !== 'all' ? (int) $branchId : null);

        $today = Carbon::today();
        $salesQuery = DB::table('sales')->whereDate('created_at', $today)->whereNotIn('sale_status', ['cancelled', 'returned']);
        $purchasesQuery = DB::table('purchases')->whereDate('purchase_date', $today)->whereIn('status_purchase', ['approved', 'posted', 'Partial']);
        $expensesQuery = DB::table('expense_vouchers')->whereDate('entry_date', $today);
        $hasExpBranch = \Illuminate\Support\Facades\Schema::hasColumn('expense_vouchers', 'branch_id');

        if ($effectiveBranchId) {
            $salesQuery->where('branch_id', $effectiveBranchId);
            $purchasesQuery->where('branch_id', $effectiveBranchId);
            if ($hasExpBranch) {
                $expensesQuery->where('branch_id', $effectiveBranchId);
            }
        } elseif (!$isSuperAdmin) {
            $salesQuery->whereRaw('1 = 0');
            $purchasesQuery->whereRaw('1 = 0');
            if ($hasExpBranch) {
                $expensesQuery->whereRaw('1 = 0');
            }
        }

        $salesToday = (float) $salesQuery->sum('total_net');
        $purchasesToday = (float) $purchasesQuery->sum('net_amount');
        $expensesToday = (float) $expensesQuery->sum('total_amount');

        return response()->json([
            'ok' => true,
            'sales_today' => number_format($salesToday, 0),
            'purchases_today' => number_format($purchasesToday, 0),
            'expenses_today' => number_format($expensesToday, 0),
            'timestamp' => Carbon::now()->format('h:i:s A'),
        ]);
    }

    /**
     * AJAX Endpoint: Store a new task.
     */
    public function storeTask(Request $request)
    {
        $user = Auth::user();
        $branchId = $request->get('branch_id') ?: ($user ? $user->branch_id : null);

        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task = DashboardTask::create([
            'branch_id' => $branchId,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority ?: 'medium',
            'status' => 'pending',
            'due_date' => $request->due_date,
            'assigned_to' => $request->assigned_to,
            'created_by' => Auth::id(),
        ]);

        try {
            BusinessActivity::log([
                'module' => 'general',
                'activity_type' => 'task_created',
                'title' => "Task Created: {$task->title}",
                'description' => "Task priority: " . strtoupper($task->priority) . ($task->due_date ? " due on {$task->due_date->format('d M Y')}" : ''),
                'icon' => 'fa-list-check',
                'badge_color' => 'indigo',
                'reference_no' => 'TSK-' . $task->id,
            ]);
        } catch (\Throwable $e) {}

        return response()->json([
            'ok' => true,
            'message' => 'Task created successfully',
            'task' => $task->load('assignee'),
        ]);
    }

    /**
     * AJAX Endpoint: Toggle task status (complete / pending).
     */
    public function toggleTask(Request $request, $id)
    {
        $task = DashboardTask::findOrFail($id);
        $newStatus = $task->status === 'completed' ? 'pending' : 'completed';
        $task->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === 'completed' ? now() : null,
        ]);

        return response()->json([
            'ok' => true,
            'status' => $newStatus,
            'message' => 'Task status updated',
        ]);
    }

    /**
     * AJAX Endpoint: Delete a task.
     */
    public function deleteTask(Request $request, $id)
    {
        $task = DashboardTask::findOrFail($id);
        $task->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Task deleted successfully',
        ]);
    }
}
