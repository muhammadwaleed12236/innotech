<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Vendor;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
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
        $totalCustomHeadsAmt = 0;
        $periodCustomHeadsSummary = [];
        $periodOurSubheadsSummary = [];
        $headWiseDetails = [];
        $headSummaryTotals = [];

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

            // Extract item-level details
            $itemsList = [];
            $aggItemOurAmt = 0;
            $aggItemCustAmt = 0;
            $dynCustomHeads = [];
            $dynOurSubheads = [];

            foreach ($p->items as $item) {
                $tVal = (float)$item->trade_value;
                $iDiscAmt = (float)$item->item_discount;
                $iDiscPct = (float)$item->company_disc_percent;
                if ($iDiscPct <= 0 && $tVal > 0 && $iDiscAmt > 0) {
                    $iDiscPct = round(($iDiscAmt / $tVal) * 100, 2);
                }

                $iOurPct = (float)$item->our_disc_percent;
                $iOurAmt = (float)$item->our_disc_amount;
                $iCustPct = (float)$item->customer_disc_percent;
                $iCustAmt = (float)$item->customer_disc_amount;

                $customBreakdown = is_array($item->custom_disc_breakdown) ? $item->custom_disc_breakdown : (json_decode($item->custom_disc_breakdown ?? '[]', true) ?: []);
                $ourBreakdown = is_array($item->our_disc_breakdown) ? $item->our_disc_breakdown : (json_decode($item->our_disc_breakdown ?? '[]', true) ?: []);

                // Default fallback if no split set on item
                if ($iOurAmt <= 0 && $iCustAmt <= 0 && empty($customBreakdown) && $iDiscAmt > 0) {
                    $iOurPct = $iDiscPct;
                    $iOurAmt = $iDiscAmt;
                }

                $aggItemOurAmt += $iOurAmt;
                $aggItemCustAmt += $iCustAmt;

                foreach ($customBreakdown as $cb) {
                    $h = trim($cb['head'] ?? 'Other');
                    $amt = (float)($cb['amount'] ?? 0);
                    if (!isset($dynCustomHeads[$h])) $dynCustomHeads[$h] = ['head' => $h, 'amount' => 0, 'percent' => 0];
                    $dynCustomHeads[$h]['amount'] += $amt;
                }

                foreach ($ourBreakdown as $ob) {
                    $h = trim($ob['head'] ?? 'Subhead');
                    $amt = (float)($ob['amount'] ?? 0);
                    if (!isset($dynOurSubheads[$h])) $dynOurSubheads[$h] = ['head' => $h, 'amount' => 0, 'percent' => 0];
                    $dynOurSubheads[$h]['amount'] += $amt;
                }

                $itemsList[] = [
                    'id' => $item->id,
                    'product_name' => $item->product ? $item->product->name : ('Product #' . $item->product_id),
                    'product_code' => $item->product ? $item->product->code : '',
                    'batch_no' => $item->batch_no ?: '—',
                    'qty' => (float)$item->qty,
                    'unit' => $item->unit ?: 'Carton',
                    'trade_value' => $tVal,
                    'company_disc_percent' => $iDiscPct,
                    'company_disc_amount' => $iDiscAmt,
                    'our_disc_percent' => $iOurPct,
                    'our_disc_amount' => $iOurAmt,
                    'customer_disc_percent' => $iCustPct,
                    'customer_disc_amount' => $iCustAmt,
                    'custom_disc_breakdown' => $customBreakdown,
                    'our_disc_breakdown' => $ourBreakdown,
                    'our_disc_notes' => $item->our_disc_notes ?: '',
                ];

                $prodName = $item->product ? $item->product->name : ('Product #' . $item->product_id);
                $invNo = $p->invoice_no ?: ('PUR-' . $p->id);
                $invDate = $p->purchase_date ? Carbon::parse($p->purchase_date)->format('d-M-Y') : 'N/A';
                $vName = $p->vendor ? $p->vendor->name : 'N/A';
                $batchStr = $item->batch_no ?: '—';

                // Collect Owner Share Head
                if ($iOurAmt > 0 || $iOurPct > 0) {
                    $hName = 'Owner Share (Humara)';
                    $headWiseDetails[] = [
                        'purchase_id' => $p->id,
                        'item_id' => $item->id,
                        'invoice_no' => $invNo,
                        'date' => $invDate,
                        'vendor_name' => $vName,
                        'product_name' => $prodName,
                        'batch_no' => $batchStr,
                        'head' => $hName,
                        'head_type' => 'owner',
                        'percent' => $iOurPct,
                        'amount' => $iOurAmt,
                        'trade_value' => $tVal,
                    ];
                    if (!isset($headSummaryTotals[$hName])) {
                        $headSummaryTotals[$hName] = ['head' => $hName, 'type' => 'owner', 'total_amount' => 0, 'count' => 0, 'total_trade_value' => 0, 'invoices' => []];
                    }
                    $headSummaryTotals[$hName]['total_amount'] += $iOurAmt;
                    $headSummaryTotals[$hName]['total_trade_value'] += $tVal;
                    $headSummaryTotals[$hName]['count']++;
                    $headSummaryTotals[$hName]['invoices'][$p->id] = true;
                }

                // Collect Customer Share Head
                if ($iCustAmt > 0 || $iCustPct > 0) {
                    $hName = 'Customer Share (Market)';
                    $headWiseDetails[] = [
                        'purchase_id' => $p->id,
                        'item_id' => $item->id,
                        'invoice_no' => $invNo,
                        'date' => $invDate,
                        'vendor_name' => $vName,
                        'product_name' => $prodName,
                        'batch_no' => $batchStr,
                        'head' => $hName,
                        'head_type' => 'customer',
                        'percent' => $iCustPct,
                        'amount' => $iCustAmt,
                        'trade_value' => $tVal,
                    ];
                    if (!isset($headSummaryTotals[$hName])) {
                        $headSummaryTotals[$hName] = ['head' => $hName, 'type' => 'customer', 'total_amount' => 0, 'count' => 0, 'total_trade_value' => 0, 'invoices' => []];
                    }
                    $headSummaryTotals[$hName]['total_amount'] += $iCustAmt;
                    $headSummaryTotals[$hName]['total_trade_value'] += $tVal;
                    $headSummaryTotals[$hName]['count']++;
                    $headSummaryTotals[$hName]['invoices'][$p->id] = true;
                }

                // Collect Custom & Standard Heads (e.g. Del Norm, Del Return, Del Freight etc.)
                foreach ($customBreakdown as $cb) {
                    $h = trim($cb['head'] ?? '');
                    $amt = (float)($cb['amount'] ?? 0);
                    $pct = (float)($cb['percent'] ?? 0);
                    if ($h !== '' && ($amt > 0 || $pct > 0)) {
                        $headWiseDetails[] = [
                            'purchase_id' => $p->id,
                            'item_id' => $item->id,
                            'invoice_no' => $invNo,
                            'date' => $invDate,
                            'vendor_name' => $vName,
                            'product_name' => $prodName,
                            'batch_no' => $batchStr,
                            'head' => $h,
                            'head_type' => 'custom',
                            'percent' => $pct,
                            'amount' => $amt,
                            'trade_value' => $tVal,
                        ];
                        if (!isset($headSummaryTotals[$h])) {
                            $headSummaryTotals[$h] = ['head' => $h, 'type' => 'custom', 'total_amount' => 0, 'count' => 0, 'total_trade_value' => 0, 'invoices' => []];
                        }
                        $headSummaryTotals[$h]['total_amount'] += $amt;
                        $headSummaryTotals[$h]['total_trade_value'] += $tVal;
                        $headSummaryTotals[$h]['count']++;
                        $headSummaryTotals[$h]['invoices'][$p->id] = true;
                    }
                }
            }

            $ourDiscPct = (float)$p->our_disc_percent;
            $ourDiscAmt = (float)$p->our_disc_amount;
            $custDiscPct = (float)$p->customer_disc_percent;
            $custDiscAmt = (float)$p->customer_disc_amount;

            // If invoice-level was 0 but items had split, use aggregated item split
            if ($ourDiscAmt <= 0 && $custDiscAmt <= 0 && ($aggItemOurAmt > 0 || $aggItemCustAmt > 0)) {
                $ourDiscAmt = $aggItemOurAmt;
                $ourDiscPct = $baseValue > 0 ? round(($ourDiscAmt / $baseValue) * 100, 2) : 0;
                $custDiscAmt = $aggItemCustAmt;
                $custDiscPct = $baseValue > 0 ? round(($custDiscAmt / $baseValue) * 100, 2) : 0;
            }

            // Auto-calculate split if our and customer are still not set
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

            // Custom heads for invoice
            $invCustom = is_array($p->custom_disc_breakdown) ? $p->custom_disc_breakdown : (json_decode($p->custom_disc_breakdown ?? '[]', true) ?: []);
            if (empty($invCustom) && !empty($dynCustomHeads)) {
                if ($baseValue > 0) {
                    foreach ($dynCustomHeads as &$dch) {
                        $dch['percent'] = round(($dch['amount'] / $baseValue) * 100, 2);
                    }
                    unset($dch);
                }
                $invCustom = array_values($dynCustomHeads);
            }

            // Our subheads for invoice
            $invOurSub = is_array($p->our_disc_breakdown) ? $p->our_disc_breakdown : (json_decode($p->our_disc_breakdown ?? '[]', true) ?: []);
            if (empty($invOurSub) && !empty($dynOurSubheads)) {
                if ($baseValue > 0) {
                    foreach ($dynOurSubheads as &$doh) {
                        $doh['percent'] = round(($doh['amount'] / $baseValue) * 100, 2);
                    }
                    unset($doh);
                }
                $invOurSub = array_values($dynOurSubheads);
            }

            $invCustomAmt = 0;
            foreach ($invCustom as $ch) {
                $h = trim($ch['head'] ?? 'Other');
                $amt = (float)($ch['amount'] ?? 0);
                $invCustomAmt += $amt;
                if (!isset($periodCustomHeadsSummary[$h])) {
                    $periodCustomHeadsSummary[$h] = ['head' => $h, 'amount' => 0, 'count' => 0];
                }
                $periodCustomHeadsSummary[$h]['amount'] += $amt;
                $periodCustomHeadsSummary[$h]['count']++;
            }

            foreach ($invOurSub as $ob) {
                $h = trim($ob['head'] ?? 'Subhead');
                $amt = (float)($ob['amount'] ?? 0);
                if (!isset($periodOurSubheadsSummary[$h])) {
                    $periodOurSubheadsSummary[$h] = ['head' => $h, 'amount' => 0, 'count' => 0];
                }
                $periodOurSubheadsSummary[$h]['amount'] += $amt;
                $periodOurSubheadsSummary[$h]['count']++;
            }

            $settled = (float)$p->company_settled_amount;
            $pending = max(0, $companyDiscAmt - $settled);
            $settleStatus = $p->settlement_status ?: ($settled >= $companyDiscAmt && $companyDiscAmt > 0 ? 'settled' : ($settled > 0 ? 'partial' : 'pending'));

            $totalPurchaseValue += $baseValue;
            $totalCompanyDiscAmt += $companyDiscAmt;
            $totalOurDiscAmt += $ourDiscAmt;
            $totalCustomerPassThroughAmt += $custDiscAmt;
            $totalCustomHeadsAmt += $invCustomAmt;
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
                'custom_disc_amount' => round($invCustomAmt, 2),
                'custom_disc_breakdown' => $invCustom,
                'our_disc_breakdown' => $invOurSub,
                'our_disc_notes' => $p->our_disc_notes ?: '',
                'company_settled_amount' => $settled,
                'company_pending_amount' => $pending,
                'settlement_status' => $settleStatus,
                'items_count' => count($itemsList),
                'items' => $itemsList,
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

        // Finalize Head Summary Totals
        foreach ($headSummaryTotals as &$hst) {
            $hst['invoices_count'] = count($hst['invoices']);
            unset($hst['invoices']);
            $hst['avg_percent'] = $hst['total_trade_value'] > 0 ? round(($hst['total_amount'] / $hst['total_trade_value']) * 100, 2) : 0;
            $hst['total_amount'] = round($hst['total_amount'], 2);
            $hst['total_trade_value'] = round($hst['total_trade_value'], 2);
        }
        unset($hst);
        uasort($headSummaryTotals, function($a, $b) {
            return $b['total_amount'] <=> $a['total_amount'];
        });

        return response()->json([
            'ok' => true,
            'summary' => [
                'total_purchase_value' => round($totalPurchaseValue, 2),
                'total_company_disc_amt' => round($totalCompanyDiscAmt, 2),
                'total_our_disc_amt' => round($totalOurDiscAmt, 2),
                'total_customer_passthrough_amt' => round($totalCustomerPassThroughAmt, 2),
                'total_custom_heads_amt' => round($totalCustomHeadsAmt, 2),
                'custom_heads_summary' => array_values($periodCustomHeadsSummary),
                'our_subheads_summary' => array_values($periodOurSubheadsSummary),
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
            'head_wise_details' => $headWiseDetails,
            'head_summary_totals' => array_values($headSummaryTotals),
            'counts' => [
                'purchases_count' => count($purchaseInvoices),
                'head_records_count' => count($headWiseDetails),
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

        if ($request->has('custom_disc_breakdown')) {
            $cb = $request->input('custom_disc_breakdown');
            $purchase->custom_disc_breakdown = is_string($cb) ? (json_decode($cb, true) ?: []) : $cb;
            $this->syncHeads($purchase->custom_disc_breakdown);
        }

        if ($request->has('our_disc_breakdown')) {
            $ob = $request->input('our_disc_breakdown');
            $purchase->our_disc_breakdown = is_string($ob) ? (json_decode($ob, true) ?: []) : $ob;
        }

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
                'custom_disc_breakdown' => $purchase->custom_disc_breakdown,
                'our_disc_breakdown' => $purchase->our_disc_breakdown,
                'our_disc_notes' => $purchase->our_disc_notes,
                'company_settled_amount' => $purchase->company_settled_amount,
                'company_pending_amount' => max(0, $purchase->company_disc_amount - $purchase->company_settled_amount),
                'settlement_status' => $purchase->settlement_status,
            ]
        ]);
    }

    public function updateItemSplit(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:purchase_items,id',
            'our_disc_percent' => 'nullable|numeric|min:0|max:100',
            'customer_disc_percent' => 'nullable|numeric|min:0|max:100',
            'our_disc_notes' => 'nullable|string',
        ]);

        $item = PurchaseItem::findOrFail($request->item_id);
        $purchase = $item->purchase;

        $tVal = (float)$item->trade_value;
        $discAmt = (float)$item->item_discount;
        $discPct = (float)$item->company_disc_percent;
        if ($discPct <= 0 && $tVal > 0 && $discAmt > 0) {
            $discPct = round(($discAmt / $tVal) * 100, 2);
        }

        $ourPct = (float)($request->input('our_disc_percent') ?? 0);
        $ourAmt = $tVal > 0 ? round($tVal * ($ourPct / 100), 2) : 0;

        $custPct = (float)($request->input('customer_disc_percent') ?? 0);
        $custAmt = $tVal > 0 ? round($tVal * ($custPct / 100), 2) : 0;

        $customBreakdown = $request->input('custom_disc_breakdown');
        if (is_string($customBreakdown)) {
            $customBreakdown = json_decode($customBreakdown, true) ?: [];
        }

        $ourBreakdown = $request->input('our_disc_breakdown');
        if (is_string($ourBreakdown)) {
            $ourBreakdown = json_decode($ourBreakdown, true) ?: [];
        }

        $item->our_disc_percent = $ourPct;
        $item->our_disc_amount = $ourAmt;
        $item->customer_disc_percent = $custPct;
        $item->customer_disc_amount = $custAmt;
        $item->custom_disc_breakdown = $customBreakdown;
        $item->our_disc_breakdown = $ourBreakdown;
        if ($request->has('our_disc_notes')) {
            $item->our_disc_notes = $request->our_disc_notes;
        }
        $item->save();

        $this->syncHeads($customBreakdown);

        // Re-aggregate parent purchase totals
        $purchase->load('items');
        $aggOurAmt = (float)$purchase->items->sum('our_disc_amount');
        $aggCustAmt = (float)$purchase->items->sum('customer_disc_amount');
        $baseValue = (float)($purchase->total_trade_value > 0 ? $purchase->total_trade_value : ($purchase->subtotal > 0 ? $purchase->subtotal : $purchase->net_amount));

        $aggCustom = [];
        $aggOurSub = [];
        foreach ($purchase->items as $pi) {
            $cb = is_array($pi->custom_disc_breakdown) ? $pi->custom_disc_breakdown : (json_decode($pi->custom_disc_breakdown ?? '[]', true) ?: []);
            foreach ($cb as $c) {
                $h = trim($c['head'] ?? 'Other');
                $a = (float)($c['amount'] ?? 0);
                if (!isset($aggCustom[$h])) $aggCustom[$h] = ['head' => $h, 'amount' => 0, 'percent' => 0];
                $aggCustom[$h]['amount'] += $a;
            }

            $ob = is_array($pi->our_disc_breakdown) ? $pi->our_disc_breakdown : (json_decode($pi->our_disc_breakdown ?? '[]', true) ?: []);
            foreach ($ob as $o) {
                $h = trim($o['head'] ?? 'Subhead');
                $a = (float)($o['amount'] ?? 0);
                if (!isset($aggOurSub[$h])) $aggOurSub[$h] = ['head' => $h, 'amount' => 0, 'percent' => 0];
                $aggOurSub[$h]['amount'] += $a;
            }
        }

        if ($baseValue > 0) {
            foreach ($aggCustom as &$ac) {
                $ac['percent'] = round(($ac['amount'] / $baseValue) * 100, 2);
            }
            unset($ac);
            foreach ($aggOurSub as &$ao) {
                $ao['percent'] = round(($ao['amount'] / $baseValue) * 100, 2);
            }
            unset($ao);
        }

        $purchase->our_disc_amount = $aggOurAmt;
        $purchase->our_disc_percent = $baseValue > 0 ? round(($aggOurAmt / $baseValue) * 100, 2) : 0;
        $purchase->customer_disc_amount = $aggCustAmt;
        $purchase->customer_disc_percent = $baseValue > 0 ? round(($aggCustAmt / $baseValue) * 100, 2) : 0;
        $purchase->custom_disc_breakdown = array_values($aggCustom);
        $purchase->our_disc_breakdown = array_values($aggOurSub);
        $purchase->save();

        return response()->json([
            'ok' => true,
            'msg' => 'Item discount split updated successfully and purchase totals recalculated!',
        ]);
    }

    public function saveCustomHead(Request $request)
    {
        $head = trim($request->input('head', ''));
        if ($head === '') {
            return response()->json(['ok' => false, 'message' => 'Head name is required'], 422);
        }

        $existing = Setting::get('custom_discount_heads', []);
        if (is_string($existing)) {
            $existing = json_decode($existing, true) ?: [];
        }
        if (!is_array($existing)) {
            $existing = [];
        }

        $exists = false;
        foreach ($existing as $h) {
            if (strcasecmp($h, $head) === 0) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $existing[] = $head;
            Setting::set('custom_discount_heads', json_encode(array_values($existing)), 'accounting', 'Custom Discount Heads', 'json');
        }

        return response()->json(['ok' => true, 'head' => $head, 'heads' => $existing]);
    }

    public function deleteCustomHead(Request $request)
    {
        $head = trim($request->input('head', ''));
        $existing = Setting::get('custom_discount_heads', []);
        if (is_string($existing)) {
            $existing = json_decode($existing, true) ?: [];
        }
        if (!is_array($existing)) {
            $existing = [];
        }

        $filtered = array_values(array_filter($existing, function($h) use ($head) {
            return strcasecmp($h, $head) !== 0;
        }));

        Setting::set('custom_discount_heads', json_encode($filtered), 'accounting', 'Custom Discount Heads', 'json');

        return response()->json(['ok' => true, 'heads' => $filtered]);
    }

    private function syncHeads($customBreakdown)
    {
        if (empty($customBreakdown)) return;
        if (is_string($customBreakdown)) $customBreakdown = json_decode($customBreakdown, true) ?: [];
        if (!is_array($customBreakdown)) return;

        $existing = Setting::get('custom_discount_heads', []);
        if (is_string($existing)) $existing = json_decode($existing, true) ?: [];
        if (!is_array($existing)) $existing = [];

        $dirty = false;
        foreach ($customBreakdown as $ch) {
            $name = isset($ch['head']) ? trim($ch['head']) : '';
            if ($name !== '' && !in_array(strtolower($name), array_map('strtolower', $existing))) {
                $existing[] = $name;
                $dirty = true;
            }
        }
        if ($dirty) {
            Setting::set('custom_discount_heads', json_encode(array_values($existing)), 'accounting', 'Custom Discount Heads', 'json');
        }
    }

    public function exportCsv(Request $request)
    {
        $exportType = $request->input('type') ?: $request->input('export_type');
        $vendorId = $request->filled('vendor_id') ? $request->vendor_id : null;
        $selectedHead = $request->filled('head') ? trim($request->input('head')) : null;

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

        $purchases = $purchasesQuery->orderBy('purchase_date', 'desc')->get();

        if ($exportType === 'heads' || $exportType === 'head_wise') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="discount_madd_breakdown_report_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function () use ($purchases, $selectedHead) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [
                    'Date', 'Invoice #', 'Supplier / Principal', 'Product Name', 'Batch #',
                    'Discount Head / Madd', 'Head Type', 'Rate (%)', 'Discount Amount (PKR)', 'Trade Value (PKR)'
                ]);

                foreach ($purchases as $p) {
                    $invNo = $p->invoice_no ?: ('PUR-' . $p->id);
                    $invDate = $p->purchase_date ? Carbon::parse($p->purchase_date)->format('Y-m-d') : '';
                    $vName = $p->vendor ? $p->vendor->name : '';

                    foreach ($p->items as $item) {
                        $prodName = $item->product ? $item->product->name : ('Product #' . $item->product_id);
                        $batchStr = $item->batch_no ?: '—';
                        $tVal = (float)($item->trade_value > 0 ? $item->trade_value : ($item->subtotal > 0 ? $item->subtotal : $item->net_amount));
                        $iOurAmt = (float)$item->our_disc_amount;
                        $iOurPct = (float)$item->our_disc_percent;
                        $iCustAmt = (float)$item->customer_disc_amount;
                        $iCustPct = (float)$item->customer_disc_percent;

                        $customBreakdown = is_array($item->custom_disc_breakdown)
                            ? $item->custom_disc_breakdown
                            : (json_decode($item->custom_disc_breakdown ?? '[]', true) ?: []);

                        // Owner Share
                        if ($iOurAmt > 0 || $iOurPct > 0) {
                            $hName = 'Owner Share (Humara)';
                            if (!$selectedHead || strcasecmp($selectedHead, $hName) === 0) {
                                fputcsv($file, [
                                    $invDate, $invNo, $vName, $prodName, $batchStr,
                                    $hName, 'Owner Share', $iOurPct . '%', $iOurAmt, $tVal
                                ]);
                            }
                        }

                        // Customer Share
                        if ($iCustAmt > 0 || $iCustPct > 0) {
                            $hName = 'Customer Share (Market)';
                            if (!$selectedHead || strcasecmp($selectedHead, $hName) === 0) {
                                fputcsv($file, [
                                    $invDate, $invNo, $vName, $prodName, $batchStr,
                                    $hName, 'Customer Share', $iCustPct . '%', $iCustAmt, $tVal
                                ]);
                            }
                        }

                        // Custom / Standard Maddain
                        foreach ($customBreakdown as $cb) {
                            $h = trim($cb['head'] ?? '');
                            $amt = (float)($cb['amount'] ?? 0);
                            $pct = (float)($cb['percent'] ?? 0);
                            if ($h !== '' && ($amt > 0 || $pct > 0)) {
                                if (!$selectedHead || strcasecmp($selectedHead, $h) === 0) {
                                    fputcsv($file, [
                                        $invDate, $invNo, $vName, $prodName, $batchStr,
                                        $h, 'Standard / Custom Head', $pct . '%', $amt, $tVal
                                    ]);
                                }
                            }
                        }
                    }
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="discount_reconciliation_report_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($purchases) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Invoice #', 'Date', 'Supplier / Company', 'Invoice Trade Value (PKR)',
                'Company Disc %', 'Company Disc (PKR)', 'Our Share %', 'Our Share (PKR)',
                'Customer Share %', 'Customer Share (PKR)', 'Custom Heads (Expiry / Return etc)',
                'Our Share Maddain / Notes', 'Settled by Company (PKR)', 'Pending from Company (PKR)', 'Settlement Status'
            ]);

            foreach ($purchases as $p) {
                $base = (float)($p->total_trade_value ?: ($p->subtotal ?: $p->net_amount));
                $compAmt = (float)$p->company_disc_amount;
                $compPct = (float)$p->company_disc_percent;
                $settled = (float)$p->company_settled_amount;
                $pending = max(0, $compAmt - $settled);

                $customBreakdown = is_array($p->custom_disc_breakdown) ? $p->custom_disc_breakdown : (json_decode($p->custom_disc_breakdown ?? '[]', true) ?: []);
                $customStrParts = [];
                foreach ($customBreakdown as $cb) {
                    $customStrParts[] = ($cb['head'] ?? 'Head') . ': ' . ($cb['percent'] ?? 0) . '% (Rs ' . ($cb['amount'] ?? 0) . ')';
                }
                $customStr = implode('; ', $customStrParts);

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
                    $customStr,
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
