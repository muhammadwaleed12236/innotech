<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Vendor;
use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $vendors = Vendor::orderBy('name')->get();
        $now = Carbon::now();
        $currentMonth = $now->format('Y-m');

        // Check if current month has any purchases
        $hasCurrentMonthPurchases = Purchase::whereBetween('purchase_date', [
            $now->copy()->startOfMonth()->format('Y-m-d'),
            $now->copy()->endOfMonth()->format('Y-m-d')
        ])->exists();

        if (!$hasCurrentMonthPurchases) {
            // Fallback to latest purchase date so user sees data immediately
            $latestPurchase = Purchase::orderBy('purchase_date', 'desc')->first();
            if ($latestPurchase && $latestPurchase->purchase_date) {
                $latestDate = Carbon::parse($latestPurchase->purchase_date);
                $currentMonth = $latestDate->format('Y-m');
                // Use the entire year of latest transaction or month to show all data
                $dateFrom = $latestDate->copy()->startOfYear()->format('Y-m-d');
                $dateTo = $latestDate->copy()->endOfMonth()->format('Y-m-d');
            } else {
                $dateFrom = $now->copy()->startOfYear()->format('Y-m-d');
                $dateTo = $now->copy()->format('Y-m-d');
            }
        } else {
            $dateFrom = $now->copy()->startOfMonth()->format('Y-m-d');
            $dateTo = $now->copy()->format('Y-m-d');
        }

        return view('admin_panel.reporting.discount_reconciliation', compact('vendors', 'currentMonth', 'dateFrom', 'dateTo'));
    }

    public function fetchData(Request $request)
    {
        $vendorId = $request->filled('vendor_id') ? $request->vendor_id : null;
        $statusFilter = $request->filled('settlement_status') ? $request->settlement_status : 'all';

        // 1. Fetch Purchases in period (flexible date filtering)
        $purchasesQuery = Purchase::with(['vendor', 'items.product']);

        if ($request->filled('date_from')) {
            $purchasesQuery->whereDate('purchase_date', '>=', Carbon::parse($request->date_from)->format('Y-m-d'));
        }
        if ($request->filled('date_to')) {
            $purchasesQuery->whereDate('purchase_date', '<=', Carbon::parse($request->date_to)->format('Y-m-d'));
        }

        if ($vendorId) {
            $purchasesQuery->where('vendor_id', $vendorId);
        }

        if ($statusFilter !== 'all') {
            $purchasesQuery->where('settlement_status', $statusFilter);
        }

        $purchases = $purchasesQuery->orderBy('purchase_date', 'desc')->get();

        $purchaseInvoices = [];
        $totalPurchaseValue = 0;
        $totalCompanyDiscAmt = 0;
        $totalOurDiscAmt = 0;
        $totalCustomerPassThroughAmt = 0;
        $totalCompanySettledAmt = 0;
        $totalCompanyPendingAmt = 0;

        foreach ($purchases as $p) {
            $baseValue = (float)($p->total_trade_value > 0 ? $p->total_trade_value : ($p->subtotal > 0 ? $p->subtotal : $p->net_amount));
            $companyDiscAmt = (float)$p->company_disc_amount;
            $companyDiscPct = (float)$p->company_disc_percent;

            // If not explicitly set, fallback to existing discount fields on purchase
            if ($companyDiscAmt <= 0 && $companyDiscPct <= 0) {
                $existingDisc = (float)($p->discount + $p->additional_discount);
                if ($existingDisc <= 0 && $p->items->count() > 0) {
                    $existingDisc = (float)$p->items->sum('item_discount');
                }
                $companyDiscAmt = $existingDisc;
                $companyDiscPct = $baseValue > 0 ? round(($companyDiscAmt / $baseValue) * 100, 2) : 0;
            }

            // Guard against corrupted discount exceeding base value
            if ($companyDiscAmt > $baseValue && $baseValue > 0) {
                $companyDiscAmt = $baseValue;
                $companyDiscPct = 100;
            }

            $ourDiscPct = (float)$p->our_disc_percent;
            $ourDiscAmt = (float)$p->our_disc_amount;
            $custDiscPct = (float)$p->customer_disc_percent;
            $custDiscAmt = (float)$p->customer_disc_amount;

            // Auto-calculate split if our and customer are not set yet
            if ($ourDiscAmt <= 0 && $custDiscAmt <= 0 && $companyDiscAmt > 0) {
                if ($ourDiscPct > 0) {
                    $ourDiscAmt = round($baseValue * ($ourDiscPct / 100), 2);
                    $custDiscPct = max(0, $companyDiscPct - $ourDiscPct);
                    $custDiscAmt = round($baseValue * ($custDiscPct / 100), 2);
                } elseif ($custDiscPct > 0) {
                    $custDiscAmt = round($baseValue * ($custDiscPct / 100), 2);
                    $ourDiscPct = max(0, $companyDiscPct - $custDiscPct);
                    $ourDiscAmt = round($baseValue * ($ourDiscPct / 100), 2);
                } else {
                    // Default: Our share = full company disc until customized
                    $ourDiscPct = $companyDiscPct;
                    $ourDiscAmt = $companyDiscAmt;
                    $custDiscPct = 0;
                    $custDiscAmt = 0;
                }
            }

            $settled = (float)$p->company_settled_amount;
            $pending = max(0, $companyDiscAmt - $settled);
            $settleStatus = $p->settlement_status ?: ($settled >= $companyDiscAmt && $companyDiscAmt > 0 ? 'settled' : ($settled > 0 ? 'partial' : 'pending'));

            $totalPurchaseValue += $baseValue;
            $totalCompanyDiscAmt += $companyDiscAmt;
            $totalOurDiscAmt += $ourDiscAmt;
            $totalCustomerPassThroughAmt += $custDiscAmt;
            $totalCompanySettledAmt += $settled;
            $totalCompanyPendingAmt += $pending;

            $purchaseInvoices[] = [
                'id' => $p->id,
                'invoice_no' => $p->invoice_no ?: ('PUR-' . $p->id),
                'purchase_date' => $p->purchase_date ? Carbon::parse($p->purchase_date)->format('d-M-Y') : 'N/A',
                'vendor_name' => $p->vendor ? $p->vendor->name : 'N/A',
                'base_value' => $baseValue,
                'company_disc_percent' => $companyDiscPct,
                'company_disc_amount' => $companyDiscAmt,
                'our_disc_percent' => $ourDiscPct,
                'our_disc_amount' => $ourDiscAmt,
                'customer_disc_percent' => $custDiscPct,
                'customer_disc_amount' => $custDiscAmt,
                'our_disc_notes' => $p->our_disc_notes ?: '',
                'company_settled_amount' => $settled,
                'company_pending_amount' => $pending,
                'settlement_status' => $settleStatus,
                'items_count' => $p->items->count(),
            ];
        }

        // 2. Fetch Sales in period (Discounts passed to customers)
        $salesQuery = Sale::where('sale_status', 'posted');

        if ($request->filled('date_from')) {
            $salesQuery->whereDate('created_at', '>=', Carbon::parse($request->date_from)->format('Y-m-d'));
        }
        if ($request->filled('date_to')) {
            $salesQuery->whereDate('created_at', '<=', Carbon::parse($request->date_to)->format('Y-m-d'));
        }

        $sales = $salesQuery->get();

        $totalSalesGross = 0;
        $totalSalesNet = 0;
        $totalRegDiscGiven = 0;
        $totalSpDiscGiven = 0;
        $totalTradeActivityGiven = 0;
        $totalExtraDiscGiven = 0;

        foreach ($sales as $s) {
            $totalSalesGross += (float)$s->total_gross;
            $totalSalesNet += (float)$s->total_net;
            $totalRegDiscGiven += (float)$s->total_reg_discount;
            $totalSpDiscGiven += (float)$s->total_sp_discount;
            $totalTradeActivityGiven += (float)$s->total_trade_activity;
            $totalExtraDiscGiven += (float)$s->total_extradiscount;
        }

        $totalCustomerDiscountGiven = $totalRegDiscGiven + $totalSpDiscGiven + $totalTradeActivityGiven + $totalExtraDiscGiven;

        // 3. Variance & Profit Calculations
        // Advance Discount Variance: Actual Discount given to customers vs Customer Discount funded by companies
        $advanceDiscountVariance = $totalCustomerDiscountGiven - $totalCustomerPassThroughAmt;

        // Estimated Cost of Goods Sold (Purchases Net Trade Value)
        $cogs = max(0, $totalPurchaseValue - $totalCompanyDiscAmt);
        $directTradingMargin = max(0, $totalSalesNet - $cogs);

        // Retained Discount Profit: Our share of company discount that stays with us
        $retainedDiscountProfit = $totalOurDiscAmt;

        // Total Net Profit: Trading Margin + Retained Company Discount Margin - (any extra advance discount given beyond company share)
        $adjustedNetProfit = ($totalSalesNet - $cogs) + $retainedDiscountProfit;

        return response()->json([
            'ok' => true,
            'summary' => [
                'total_purchase_value' => round($totalPurchaseValue, 2),
                'total_company_disc_amt' => round($totalCompanyDiscAmt, 2),
                'total_our_disc_amt' => round($totalOurDiscAmt, 2),
                'total_customer_passthrough_amt' => round($totalCustomerPassThroughAmt, 2),
                'total_company_settled_amt' => round($totalCompanySettledAmt, 2),
                'total_company_pending_amt' => round($totalCompanyPendingAmt, 2),

                // Customer Discount Given (Advance)
                'total_sales_gross' => round($totalSalesGross, 2),
                'total_sales_net' => round($totalSalesNet, 2),
                'total_reg_disc_given' => round($totalRegDiscGiven, 2),
                'total_sp_disc_given' => round($totalSpDiscGiven, 2),
                'total_trade_activity_given' => round($totalTradeActivityGiven, 2),
                'total_extra_disc_given' => round($totalExtraDiscGiven, 2),
                'total_customer_disc_given' => round($totalCustomerDiscountGiven, 2),

                // Reconciliations
                'advance_discount_variance' => round($advanceDiscountVariance, 2),
                'retained_discount_profit' => round($retainedDiscountProfit, 2),
                'direct_trading_margin' => round($directTradingMargin, 2),
                'adjusted_net_profit' => round($adjustedNetProfit, 2),
            ],
            'invoices' => $purchaseInvoices,
            'counts' => [
                'purchases_count' => count($purchaseInvoices),
                'sales_count' => $sales->count(),
            ]
        ]);
    }

    public function updateSplit(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'our_disc_percent' => 'nullable|numeric|min:0|max:100',
            'customer_disc_percent' => 'nullable|numeric|min:0|max:100',
            'our_disc_notes' => 'nullable|string',
            'company_settled_amount' => 'nullable|numeric|min:0',
            'settlement_status' => 'nullable|string|in:pending,partial,settled',
        ]);

        $purchase = Purchase::findOrFail($request->purchase_id);
        $baseValue = (float)($purchase->total_trade_value > 0 ? $purchase->total_trade_value : ($purchase->subtotal > 0 ? $purchase->subtotal : $purchase->net_amount));

        $companyDiscAmt = (float)$purchase->company_disc_amount;
        $companyDiscPct = (float)$purchase->company_disc_percent;

        if ($companyDiscAmt <= 0 && $companyDiscPct <= 0) {
            $companyDiscAmt = (float)($purchase->discount + $purchase->additional_discount);
            if ($companyDiscAmt <= 0) {
                $companyDiscAmt = (float)$purchase->items()->sum('item_discount');
            }
            $companyDiscPct = $baseValue > 0 ? round(($companyDiscAmt / $baseValue) * 100, 2) : 0;
            $purchase->company_disc_amount = $companyDiscAmt;
            $purchase->company_disc_percent = $companyDiscPct;
        }

        $ourPct = (float)($request->input('our_disc_percent') ?? 0);
        $custPct = (float)($request->input('customer_disc_percent') ?? 0);

        // If user entered Our %, auto-balance Customer % if not explicitly given
        if ($ourPct > 0 && !$request->filled('customer_disc_percent')) {
            $custPct = max(0, $companyDiscPct - $ourPct);
        } elseif ($custPct > 0 && !$request->filled('our_disc_percent')) {
            $ourPct = max(0, $companyDiscPct - $custPct);
        }

        $ourAmt = round($baseValue * ($ourPct / 100), 2);
        $custAmt = round($baseValue * ($custPct / 100), 2);

        $purchase->our_disc_percent = $ourPct;
        $purchase->our_disc_amount = $ourAmt;
        $purchase->customer_disc_percent = $custPct;
        $purchase->customer_disc_amount = $custAmt;

        if ($request->has('our_disc_notes')) {
            $purchase->our_disc_notes = $request->our_disc_notes;
        }

        if ($request->has('company_settled_amount')) {
            $purchase->company_settled_amount = (float)$request->company_settled_amount;
        }

        $status = $request->input('settlement_status');
        if (empty($status)) {
            $status = $purchase->company_settled_amount >= $purchase->company_disc_amount && $purchase->company_disc_amount > 0 ? 'settled' : ($purchase->company_settled_amount > 0 ? 'partial' : 'pending');
        }
        $purchase->settlement_status = $status;

        $purchase->save();

        return response()->json([
            'ok' => true,
            'msg' => 'Discount split and settlement updated successfully!',
            'purchase' => [
                'id' => $purchase->id,
                'our_disc_percent' => $purchase->our_disc_percent,
                'our_disc_amount' => $purchase->our_disc_amount,
                'customer_disc_percent' => $purchase->customer_disc_percent,
                'customer_disc_amount' => $purchase->customer_disc_amount,
                'our_disc_notes' => $purchase->our_disc_notes,
                'company_settled_amount' => $purchase->company_settled_amount,
                'company_pending_amount' => max(0, $purchase->company_disc_amount - $purchase->company_settled_amount),
                'settlement_status' => $purchase->settlement_status,
            ]
        ]);
    }

    public function exportCsv(Request $request)
    {
        $vendorId = $request->filled('vendor_id') ? $request->vendor_id : null;

        $purchasesQuery = Purchase::with(['vendor']);

        if ($request->filled('date_from')) {
            $purchasesQuery->whereDate('purchase_date', '>=', Carbon::parse($request->date_from)->format('Y-m-d'));
        }
        if ($request->filled('date_to')) {
            $purchasesQuery->whereDate('purchase_date', '<=', Carbon::parse($request->date_to)->format('Y-m-d'));
        }

        if ($vendorId) {
            $purchasesQuery->where('vendor_id', $vendorId);
        }

        $purchases = $purchasesQuery->orderBy('purchase_date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="discount_reconciliation_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($purchases) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Invoice #', 'Date', 'Supplier / Company', 'Invoice Trade Value (PKR)',
                'Company Disc %', 'Company Disc (PKR)', 'Our Share %', 'Our Share (PKR)',
                'Customer Share %', 'Customer Share (PKR)', 'Our Share Maddain / Notes',
                'Settled by Company (PKR)', 'Pending from Company (PKR)', 'Settlement Status'
            ]);

            foreach ($purchases as $p) {
                $base = (float)($p->total_trade_value ?: ($p->subtotal ?: $p->net_amount));
                $compAmt = (float)$p->company_disc_amount;
                $compPct = (float)$p->company_disc_percent;
                $settled = (float)$p->company_settled_amount;
                $pending = max(0, $compAmt - $settled);

                fputcsv($file, [
                    $p->invoice_no ?: ('PUR-' . $p->id),
                    $p->purchase_date ? Carbon::parse($p->purchase_date)->format('Y-m-d') : '',
                    $p->vendor ? $p->vendor->name : '',
                    $base,
                    $compPct . '%',
                    $compAmt,
                    $p->our_disc_percent . '%',
                    $p->our_disc_amount,
                    $p->customer_disc_percent . '%',
                    $p->customer_disc_amount,
                    $p->our_disc_notes ?: '',
                    $settled,
                    $pending,
                    ucfirst($p->settlement_status ?: 'Pending')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
