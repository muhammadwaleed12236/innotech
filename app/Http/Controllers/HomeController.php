<?php

namespace App\Http\Controllers;

use App\Models\Stock;
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

            // Date Range Filter (Default: This Month)
            $filterPeriod = $request->get('period', 'this_month');
            $customFrom = $request->get('from_date');
            $customTo = $request->get('to_date');

            $today = Carbon::today();
            switch ($filterPeriod) {
                case 'today':
                    $startDate = $today->copy()->startOfDay();
                    $endDate = $today->copy()->endOfDay();
                    $periodLabel = 'Today (' . $today->format('d M Y') . ')';
                    break;
                case 'yesterday':
                    $yesterday = $today->copy()->subDay();
                    $startDate = $yesterday->copy()->startOfDay();
                    $endDate = $yesterday->copy()->endOfDay();
                    $periodLabel = 'Yesterday (' . $yesterday->format('d M Y') . ')';
                    break;
                case 'this_week':
                    $startDate = $today->copy()->startOfWeek();
                    $endDate = $today->copy()->endOfWeek();
                    $periodLabel = 'This Week (' . $startDate->format('d M') . ' - ' . $endDate->format('d M') . ')';
                    break;
                case 'last_month':
                    $lastMonth = $today->copy()->subMonth();
                    $startDate = $lastMonth->copy()->startOfMonth();
                    $endDate = $lastMonth->copy()->endOfMonth();
                    $periodLabel = 'Last Month (' . $startDate->format('F Y') . ')';
                    break;
                case 'this_year':
                    $startDate = $today->copy()->startOfYear();
                    $endDate = $today->copy()->endOfYear();
                    $periodLabel = 'This Year (' . $today->year . ')';
                    break;
                case 'custom':
                    $startDate = $customFrom ? Carbon::parse($customFrom)->startOfDay() : $today->copy()->startOfMonth();
                    $endDate = $customTo ? Carbon::parse($customTo)->endOfDay() : $today->copy()->endOfMonth();
                    $periodLabel = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
                    break;
                case 'this_month':
                default:
                    $filterPeriod = 'this_month';
                    $startDate = $today->copy()->startOfMonth();
                    $endDate = $today->copy()->endOfMonth();
                    $periodLabel = 'This Month (' . $today->format('F Y') . ')';
                    break;
            }

            $startDtStr = $startDate->format('Y-m-d H:i:s');
            $endDtStr = $endDate->format('Y-m-d H:i:s');
            $startDateOnly = $startDate->format('Y-m-d');
            $endDateOnly = $endDate->format('Y-m-d');

            // Quick Business Counts (Strictly Permission-guarded)
            $categoryCount = $user->can('categories.view') ? DB::table('categories')->count() : null;
            $subcategoryCount = $user->can('subcategories.view') ? DB::table('subcategories')->count() : null;
            $productCount = $user->can('products.view') ? DB::table('products')->count() : null;
            $customerscount = $user->can('customers.view') ? DB::table('customers')->count() : null;
            $vendorCount = $user->can('vendors.view') ? DB::table('vendors')->count() : null;
            $employeeCount = $user->can('hr.employees.view') ? (
                \Illuminate\Support\Facades\Schema::hasTable('hr_employees') ? DB::table('hr_employees')->count() : DB::table('users')->count()
            ) : null;

            // 1. SALES METRICS (Requires 'sales.view')
            $salesPeriod = 0;
            $salesToday = 0;
            $salesReturnsPeriod = 0;
            $salesChartStats = ['daily' => ['series' => [], 'categories' => []]];
            $salesByCategory = collect();
            $topProducts = collect();
            $topCustomers = collect();
            $recentSales = collect();

            if ($user->can('sales.view')) {
                $salesPeriod = (float) DB::table('sales')
                    ->whereBetween('created_at', [$startDtStr, $endDtStr])
                    ->whereNotIn('sale_status', ['cancelled', 'returned'])
                    ->sum('total_net');

                $salesToday = (float) DB::table('sales')
                    ->whereDate('created_at', $today)
                    ->whereNotIn('sale_status', ['cancelled', 'returned'])
                    ->sum('total_net');

                if ($user->can('sales.returns.view')) {
                    $salesReturnsPeriod = (float) DB::table('sale_returns')
                        ->whereBetween('created_at', [$startDtStr, $endDtStr])
                        ->sum('net_amount');
                }

                // Sales vs Purchase Comparative Trajectory (Last 7 Days)
                $dailyLabels = collect(range(6, 0))->map(fn($i) => Carbon::today()->subDays($i)->format('d M'));
                $dailySalesData = collect(range(6, 0))->map(function ($i) {
                    $date = Carbon::today()->subDays($i)->format('Y-m-d');
                    return (float) DB::table('sales')
                        ->whereDate('created_at', $date)
                        ->whereNotIn('sale_status', ['cancelled', 'returned'])
                        ->sum('total_net');
                });

                $dailyPurchasesData = collect(range(6, 0))->map(function ($i) use ($user) {
                    if (!$user->can('purchases.view')) return 0;
                    $date = Carbon::today()->subDays($i)->format('Y-m-d');
                    return (float) DB::table('purchases')
                        ->whereDate('purchase_date', $date)
                        ->whereIn('status_purchase', ['approved', 'posted', 'Partial'])
                        ->sum('net_amount');
                });

                $weeklyLabels = ['2 Weeks Ago', 'Last Week', 'This Week'];
                $weeklySalesData = collect([2, 1, 0])->map(function ($i) {
                    $wStart = Carbon::now()->startOfWeek()->subWeeks($i);
                    $wEnd = $wStart->copy()->endOfWeek();
                    return (float) DB::table('sales')
                        ->whereBetween('created_at', [$wStart, $wEnd])
                        ->whereNotIn('sale_status', ['cancelled', 'returned'])
                        ->sum('total_net');
                });
                $weeklyPurchData = collect([2, 1, 0])->map(function ($i) use ($user) {
                    if (!$user->can('purchases.view')) return 0;
                    $wStart = Carbon::now()->startOfWeek()->subWeeks($i);
                    $wEnd = $wStart->copy()->endOfWeek();
                    return (float) DB::table('purchases')
                        ->whereBetween('purchase_date', [$wStart->format('Y-m-d'), $wEnd->format('Y-m-d')])
                        ->whereIn('status_purchase', ['approved', 'posted', 'Partial'])
                        ->sum('net_amount');
                });

                $months = range(1, Carbon::now()->month);
                $monthLabels = collect($months)->map(fn($m) => Carbon::create()->month($m)->format('M'));
                $monthlySalesData = collect($months)->map(function ($m) {
                    return (float) DB::table('sales')
                        ->whereMonth('created_at', $m)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->whereNotIn('sale_status', ['cancelled', 'returned'])
                        ->sum('total_net');
                });
                $monthlyPurchData = collect($months)->map(function ($m) use ($user) {
                    if (!$user->can('purchases.view')) return 0;
                    return (float) DB::table('purchases')
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
                    ->select(DB::raw('COALESCE(categories.name, "General") as category_name'), DB::raw('SUM(sale_items.total) as total_amount'))
                    ->groupBy('category_name')
                    ->orderByDesc('total_amount')
                    ->limit(5)
                    ->get();

                // Top Selling Products
                $topProducts = DB::table('sale_items')
                    ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->whereBetween('sales.created_at', [$startDtStr, $endDtStr])
                    ->whereNotIn('sales.sale_status', ['cancelled', 'returned'])
                    ->select('sale_items.product_name', DB::raw('SUM(sale_items.qty) as total_qty'), DB::raw('SUM(sale_items.total) as total_revenue'))
                    ->groupBy('sale_items.product_name')
                    ->orderByDesc('total_qty')
                    ->limit(6)
                    ->get();

                // Top Customers by Value
                $topCustomers = DB::table('sales')
                    ->join('customers', 'sales.customer_id', '=', 'customers.id')
                    ->whereBetween('sales.created_at', [$startDtStr, $endDtStr])
                    ->whereNotIn('sales.sale_status', ['cancelled', 'returned'])
                    ->select('customers.customer_name', DB::raw('COUNT(sales.id) as total_orders'), DB::raw('SUM(sales.total_net) as total_sales'))
                    ->groupBy('customers.id', 'customers.customer_name')
                    ->orderByDesc('total_sales')
                    ->limit(6)
                    ->get();

                // Recent Sales
                $recentSales = DB::table('sales')->latest()->limit(4)->get()->map(function($s) {
                    return [
                        'icon' => 'fa-receipt text-success',
                        'bg' => '#ecfdf5',
                        'title' => 'Sale #' . ($s->invoice_no ?: $s->id),
                        'subtitle' => 'Customer Sale',
                        'amount' => 'Rs ' . number_format($s->total_net, 0),
                        'time' => Carbon::parse($s->created_at)->diffForHumans()
                    ];
                });
            }

            // 2. PURCHASES METRICS (Requires 'purchases.view')
            $purchasesPeriod = 0;
            $purchasesToday = 0;
            $purchasesReturnsPeriod = 0;
            $recentPurchases = collect();

            if ($user->can('purchases.view')) {
                $purchasesPeriod = (float) DB::table('purchases')
                    ->whereBetween('purchase_date', [$startDateOnly, $endDateOnly])
                    ->whereIn('status_purchase', ['approved', 'posted', 'Partial'])
                    ->sum('net_amount');

                $purchasesToday = (float) DB::table('purchases')
                    ->whereDate('purchase_date', $today)
                    ->whereIn('status_purchase', ['approved', 'posted', 'Partial'])
                    ->sum('net_amount');

                if ($user->can('purchase.returns.view')) {
                    $purchasesReturnsPeriod = (float) DB::table('purchase_returns')
                        ->whereBetween('return_date', [$startDateOnly, $endDateOnly])
                        ->sum('net_amount');
                }

                $recentPurchases = DB::table('purchases')->latest()->limit(4)->get()->map(function($p) {
                    return [
                        'icon' => 'fa-cart-shopping text-primary',
                        'bg' => '#eef2ff',
                        'title' => 'Purchase #' . ($p->invoice_no ?: $p->id),
                        'subtitle' => 'Vendor Procurement',
                        'amount' => 'Rs ' . number_format($p->net_amount, 0),
                        'time' => Carbon::parse($p->created_at)->diffForHumans()
                    ];
                });
            }

            // 3. EXPENSES METRICS (Requires 'expense.voucher.view' or 'vouchers.view')
            $expensesPeriod = 0;
            $expensesToday = 0;
            $expenseBreakdown = collect();

            if ($user->can('expense.voucher.view') || $user->can('vouchers.view') || $user->can('all.vouchers.view')) {
                $expensesTodayV1 = (float) DB::table('expense_vouchers')->whereDate('entry_date', $today)->sum('total_amount');
                $expensesTodayV2 = (float) DB::table('voucher_masters')->where('voucher_type', 'expense')->whereDate('date', $today)->sum('total_amount');
                $expensesToday = $expensesTodayV1 + $expensesTodayV2;

                $expV1Period = (float) DB::table('expense_vouchers')->whereBetween('entry_date', [$startDateOnly, $endDateOnly])->sum('total_amount');
                $expV2Period = (float) DB::table('voucher_masters')->where('voucher_type', 'expense')->whereBetween('date', [$startDateOnly, $endDateOnly])->sum('total_amount');
                $expensesPeriod = $expV1Period + $expV2Period;

                $expenseBreakdown = DB::table('expense_vouchers')
                    ->whereBetween('entry_date', [$startDateOnly, $endDateOnly])
                    ->select('type as name', DB::raw('SUM(total_amount) as total'))
                    ->groupBy('type')
                    ->orderByDesc('total')
                    ->limit(5)
                    ->get();
            }

            // 4. PROFIT CALCULATIONS (Requires both sales.view and purchases.view OR profit.loss.report.view)
            $grossProfitPeriod = null;
            $netProfitPeriod = null;

            if ($user->can('profit.loss.report.view') || ($user->can('sales.view') && $user->can('purchases.view'))) {
                // Calculate Real Gross Profit for selected period (Sale Items vs Purchase Cost)
                $saleItemsQuery = DB::table('sale_items')
                    ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->leftJoin('products', 'products.id', '=', 'sale_items.product_id')
                    ->whereBetween('sales.created_at', [$startDtStr, $endDtStr])
                    ->whereNotIn('sales.sale_status', ['cancelled', 'returned'])
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

                // Deduct returned items in period
                $saleReturnsQuery = DB::table('sale_return_items')
                    ->join('sale_returns', 'sale_returns.id', '=', 'sale_return_items.sale_return_id')
                    ->leftJoin('products', 'products.id', '=', 'sale_return_items.product_id')
                    ->whereBetween('sale_returns.created_at', [$startDtStr, $endDtStr])
                    ->select(
                        'sale_return_items.qty',
                        'sale_return_items.line_total',
                        'sale_return_items.color',
                        'sale_return_items.is_manual',
                        'sale_return_items.purchase_price as manual_purchase_price',
                        'products.size_mode',
                        'products.pieces_per_m2',
                        'products.purchase_price_per_m2',
                        'products.purchase_price_per_piece'
                    )->get();

                foreach ($saleReturnsQuery as $sr) {
                    $qty = (float)($sr->qty ?? 1);
                    if ($sr->is_manual) {
                        $purchPrice = (float) $sr->manual_purchase_price;
                    } else {
                        $purchPrice = 0;
                        if (!empty($sr->color)) {
                            $decoded = base64_decode($sr->color, true);
                            $json = $decoded ? json_decode($decoded, true) : json_decode($sr->color, true);
                            if (is_array($json)) {
                                $purchPrice = (float)($json['purch_price'] ?? 0);
                            }
                        }
                        if ($purchPrice <= 0) {
                            if ($sr->size_mode === 'by_size') {
                                $purchPrice = (float)($sr->pieces_per_m2 ?? 0) * (float)($sr->purchase_price_per_m2 ?? 0);
                            } else {
                                $purchPrice = (float)($sr->purchase_price_per_piece ?? 0);
                            }
                        }
                    }

                    $totRev -= (float) $sr->line_total;
                    $totCost -= ($purchPrice * $qty);
                }

                $grossProfitPeriod = $totRev - $totCost;
                $netProfitPeriod = $grossProfitPeriod - $expensesPeriod;
            }

            // 5. RECEIVABLES & PAYABLES (Requires 'customer.ledger.view' / 'vendor.ledger.view' / 'recovery.report.view' / 'payable.report.view')
            $totalReceivables = null;
            if ($user->can('recovery.report.view') || $user->can('customer.ledger.view') || $user->can('customers.view')) {
                $totalReceivables = (float) (DB::table('journal_entries')
                    ->where('party_type', \App\Models\Customer::class)
                    ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as total')
                    ->value('total') ?? 0);
            }

            $totalPayables = null;
            if ($user->can('payable.report.view') || $user->can('vendor.ledger.view') || $user->can('vendors.view')) {
                $apId = app(\App\Services\BalanceService::class)->getAccountsPayableId();
                $totalPayables = (float) (DB::table('journal_entries')
                    ->where('party_type', \App\Models\Vendor::class)
                    ->where('account_id', $apId)
                    ->selectRaw('COALESCE(SUM(credit) - SUM(debit), 0) as total')
                    ->value('total') ?? 0);
            }

            // 6. CASH & BANK LIQUIDITY (Requires 'chart.of.accounts.view' or 'vouchers.view' or 'balance.sheet.report.view')
            $cashAndBankAccounts = collect();
            $totalCashAndBankBalance = null;

            if ($user->can('chart.of.accounts.view') || $user->can('balance.sheet.report.view') || $user->can('all.vouchers.view')) {
                $cashAndBankAccounts = \App\Models\Account::whereHas('head', function ($query) {
                    $query->whereIn('name', ['Cash', 'Bank']);
                })->where('status', 1)->get();

                $totalCashAndBankBalance = (float) $cashAndBankAccounts->sum('current_balance');
            }

            // 7. STOCK VALUATION & LOW STOCK ALERTS (Requires 'stocks.view' or 'warehouse.stock.view' or 'item.stock.report.view')
            $totalStockValue = null;
            $lowStockProducts = collect();

            if ($user->can('warehouse.stock.view') || $user->can('stocks.view') || $user->can('inventory.onhand') || $user->can('products.view')) {
                $totalStockValue = (float) (DB::table('warehouse_stocks')
                    ->join('products', 'products.id', '=', 'warehouse_stocks.product_id')
                    ->selectRaw('COALESCE(SUM(warehouse_stocks.total_pieces * (CASE WHEN products.size_mode = "by_size" THEN COALESCE(products.pieces_per_m2, 0) * COALESCE(products.purchase_price_per_m2, 0) ELSE COALESCE(products.purchase_price_per_piece, 0) END)), 0) as total')
                    ->value('total') ?? 0);

                $lowStockProducts = \App\Models\Product::withSum('warehouseStocks', 'total_pieces')
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
                    ->take(10)
                    ->values();
            }

            // 8. CASH FLOW (Payments In vs Payments Out)
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

            // Combine Recent Activities (Sales + Purchases)
            $recentActivities = $recentSales->concat($recentPurchases)->take(6)->values();

            return view('admin_panel.dashboard', compact(
                'filterPeriod',
                'periodLabel',
                'startDateOnly',
                'endDateOnly',
                'categoryCount',
                'subcategoryCount',
                'productCount',
                'customerscount',
                'vendorCount',
                'employeeCount',
                'salesPeriod',
                'salesToday',
                'salesReturnsPeriod',
                'purchasesPeriod',
                'purchasesToday',
                'purchasesReturnsPeriod',
                'expensesPeriod',
                'expensesToday',
                'grossProfitPeriod',
                'netProfitPeriod',
                'totalReceivables',
                'totalPayables',
                'totalCashAndBankBalance',
                'cashAndBankAccounts',
                'totalStockValue',
                'lowStockProducts',
                'salesChartStats',
                'salesByCategory',
                'topProducts',
                'topCustomers',
                'expenseBreakdown',
                'paymentInPeriod',
                'paymentOutPeriod',
                'recentActivities'
            ));
        } else {
            return redirect()->back()->with('error', 'Unauthorized access');
        }
    }
}
