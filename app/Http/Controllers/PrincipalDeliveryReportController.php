<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Vendor;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrincipalDeliveryReportController extends Controller
{
    /**
     * Display the Principal Delivery & Discount Statement Report view.
     */
    public function index(Request $request)
    {
        $vendors = Vendor::orderBy('name')->get();
        $currentMonth = Carbon::now()->format('Y-m');
        $dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');

        return view('admin_panel.reporting.principal_delivery_statement', compact(
            'vendors',
            'currentMonth',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Fetch report rows & column totals via AJAX.
     */
    public function fetchData(Request $request)
    {
        $data = $this->buildReportData($request);

        return response()->json([
            'ok' => true,
            'rows' => $data['rows'],
            'totals' => $data['totals'],
            'count' => count($data['rows']),
        ]);
    }

    /**
     * Export Statement as Excel-compatible CSV matching SAP columns exactly.
     */
    public function exportCsv(Request $request)
    {
        $data = $this->buildReportData($request);
        $rows = $data['rows'];
        $totals = $data['totals'];

        $filename = 'principal_delivery_statement_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $columns = [
            'Invoice Date',
            'Sold to party Customer',
            'Sold to party Customer Code',
            'Distributor Code (SAP)',
            'Distributor Name',
            'Ship to party Customer Code',
            'Ship to party Customer',
            'Delivery No',
            'Invoice No. (SAP)',
            'Order No',
            'Distribution Channel Description',
            'Business Unit Name',
            'Material',
            'Material Desc',
            'Material Tax Type',
            'Class',
            'Del Qty',
            'Del Gross Amount',
            'Del Norm Discount',
            'Del Sales Tax',
            'Del Sales Tax Un-Registered',
            'Del Discount on Return',
            'Del Display Discount',
            'Del Extra Discount',
            'Del Freight Discount',
            'Del Market Price Discount',
            'Del Trade Offer',
            'Del Income Tax',
            'Del Trade Activity Discount',
            'Del Direct Discount',
            'Del Distributor Comp',
            'Del DSF Income',
            'Del DSF Salary',
            'Del Freight Charges Claim',
            'Del JBP Claim',
            'Del Perf. Store Claims',
            'Del Price Adjustment Discount',
            'Del Progressive Rebate and ROI',
            'Del TCI MFG Claim',
            'Del VB Payout',
            'Del Net Amount'
        ];

        $callback = function () use ($columns, $rows, $totals) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens it properly
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($file, $columns);

            // Data Rows
            foreach ($rows as $r) {
                fputcsv($file, [
                    $r['invoice_date'],
                    $r['sold_to_customer'],
                    $r['sold_to_code'],
                    $r['distributor_code'],
                    $r['distributor_name'],
                    $r['ship_to_code'],
                    $r['ship_to_customer'],
                    $r['delivery_no'],
                    $r['invoice_no'],
                    $r['order_no'],
                    $r['dist_channel'],
                    $r['business_unit'],
                    $r['material_code'],
                    $r['material_desc'],
                    $r['tax_type'],
                    $r['class_name'],
                    $r['del_qty'],
                    $r['del_gross_amount'],
                    $r['del_norm_discount'],
                    $r['del_sales_tax'],
                    $r['del_sales_tax_unreg'],
                    $r['del_disc_on_return'],
                    $r['del_display_discount'],
                    $r['del_extra_discount'],
                    $r['del_freight_discount'],
                    $r['del_market_price_disc'],
                    $r['del_trade_offer'],
                    $r['del_income_tax'],
                    $r['del_trade_activity_disc'],
                    $r['del_direct_discount'],
                    $r['del_distributor_comp'],
                    $r['del_dsf_income'],
                    $r['del_dsf_salary'],
                    $r['del_freight_charges_claim'],
                    $r['del_jbp_claim'],
                    $r['del_perf_store_claims'],
                    $r['del_price_adj_discount'],
                    $r['del_progressive_rebate'],
                    $r['del_tci_mfg_claim'],
                    $r['del_vb_payout'],
                    $r['del_net_amount'],
                ]);
            }

            // Total Summary Row
            fputcsv($file, [
                'Total:',
                '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
                $totals['del_qty'],
                $totals['del_gross_amount'],
                $totals['del_norm_discount'],
                $totals['del_sales_tax'],
                $totals['del_sales_tax_unreg'],
                $totals['del_disc_on_return'],
                $totals['del_display_discount'],
                $totals['del_extra_discount'],
                $totals['del_freight_discount'],
                $totals['del_market_price_disc'],
                $totals['del_trade_offer'],
                $totals['del_income_tax'],
                $totals['del_trade_activity_disc'],
                $totals['del_direct_discount'],
                $totals['del_distributor_comp'],
                $totals['del_dsf_income'],
                $totals['del_dsf_salary'],
                $totals['del_freight_charges_claim'],
                $totals['del_jbp_claim'],
                $totals['del_perf_store_claims'],
                $totals['del_price_adj_discount'],
                $totals['del_progressive_rebate'],
                $totals['del_tci_mfg_claim'],
                $totals['del_vb_payout'],
                $totals['del_net_amount']
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Build rows & column totals for purchases & items.
     */
    private function buildReportData(Request $request)
    {
        $companyTitle = Setting::get('company_title', 'Jamal & Brothers');
        $distributorSapCode = Setting::get('distributor_sap_code', '1000203');

        $query = Purchase::with(['vendor', 'branch', 'items.product.category'])
            ->orderBy('purchase_date', 'asc')
            ->orderBy('id', 'asc');

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', Carbon::parse($request->date_from)->format('Y-m-d'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', Carbon::parse($request->date_to)->format('Y-m-d'));
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $purchases = $query->get();

        $rows = [];
        $totals = [
            'del_qty' => 0,
            'del_gross_amount' => 0,
            'del_norm_discount' => 0,
            'del_sales_tax' => 0,
            'del_sales_tax_unreg' => 0,
            'del_disc_on_return' => 0,
            'del_display_discount' => 0,
            'del_extra_discount' => 0,
            'del_freight_discount' => 0,
            'del_market_price_disc' => 0,
            'del_trade_offer' => 0,
            'del_income_tax' => 0,
            'del_trade_activity_disc' => 0,
            'del_direct_discount' => 0,
            'del_distributor_comp' => 0,
            'del_dsf_income' => 0,
            'del_dsf_salary' => 0,
            'del_freight_charges_claim' => 0,
            'del_jbp_claim' => 0,
            'del_perf_store_claims' => 0,
            'del_price_adj_discount' => 0,
            'del_progressive_rebate' => 0,
            'del_tci_mfg_claim' => 0,
            'del_vb_payout' => 0,
            'del_net_amount' => 0
        ];

        foreach ($purchases as $p) {
            $vName = $p->vendor ? $p->vendor->name : $companyTitle;
            $vCode = $p->vendor ? ($p->vendor->linked_customer_id ?: $p->vendor->id) : $distributorSapCode;
            $dCode = $p->branch_id ?: $distributorSapCode;
            $invDateStr = $p->purchase_date ? Carbon::parse($p->purchase_date)->format('n/j/Y') : '';
            $invNo = $p->invoice_no ?: ('PUR-' . $p->id);
            $orderNo = $p->purchase_order_no ?: ('ORD-' . $p->id);
            $deliveryNo = $p->purchase_order_no ?: $invNo;

            foreach ($p->items as $item) {
                $prod = $item->product;
                $matCode = $prod ? ($prod->item_code ?: $prod->id) : ('MAT-' . $item->product_id);
                $matDesc = $prod ? $prod->item_name : ('Product #' . $item->product_id);
                $catName = ($prod && $prod->category) ? $prod->category->name : 'Metro Town';
                $taxType = ($item->is_third_schedule || ($prod && $prod->is_third_schedule)) ? 'Third Schedule' : 'Standard Rate';

                $qty = (float)$item->qty;
                $gross = (float)($item->trade_value > 0 ? $item->trade_value : ($item->qty * $item->price));

                // Extract custom breakdown dictionary
                $cb = is_array($item->custom_disc_breakdown) ? $item->custom_disc_breakdown : (json_decode($item->custom_disc_breakdown ?? '[]', true) ?: []);
                $discMap = [];
                foreach ($cb as $entry) {
                    $h = strtolower(trim($entry['head'] ?? ''));
                    $a = (float)($entry['amount'] ?? 0);
                    if ($h !== '') {
                        $discMap[$h] = ($discMap[$h] ?? 0) + $a;
                    }
                }

                // If specific Del Norm Discount is not explicitly in custom breakdown, fallback to item_discount if available
                $normDisc = $discMap['del norm discount'] ?? 0;
                if ($normDisc == 0 && (float)$item->item_discount > 0 && empty($cb)) {
                    $normDisc = (float)$item->item_discount;
                }

                $salesTax = (float)($item->sales_tax_on_retail > 0 ? $item->sales_tax_on_retail : ($item->gst_amount > 0 ? $item->gst_amount : 0));
                $salesTaxUnreg = (float)($discMap['del sales tax un-registered'] ?? ($discMap['del sales tax unregistered'] ?? 0));
                $discReturn = (float)($discMap['del discount on return'] ?? ($discMap['del discount on return expiry'] ?? 0));
                $displayDisc = (float)($discMap['del display discount'] ?? 0);
                $extraDisc = (float)($discMap['del extra discount'] ?? 0);
                $freightDisc = (float)($discMap['del freight discount'] ?? 0);
                $marketPriceDisc = (float)($discMap['del market price discount'] ?? 0);
                $tradeOffer = (float)($discMap['del trade offer'] ?? 0);
                $incomeTax = (float)($discMap['del income tax'] ?? 0);
                $tradeActivityDisc = (float)($discMap['del trade activity discount'] ?? 0);
                $directDisc = (float)($discMap['del direct discount'] ?? 0);
                $distComp = (float)($discMap['del distributor comp'] ?? 0);
                $dsfIncome = (float)($discMap['del dsf income'] ?? 0);
                $dsfSalary = (float)($discMap['del dsf salary'] ?? 0);
                $freightChargesClaim = (float)($discMap['del freight charges claim'] ?? 0);
                $jbpClaim = (float)($discMap['del jbp claim'] ?? 0);
                $perfStoreClaims = (float)($discMap['del perf. store claims'] ?? ($discMap['del perf store claims'] ?? 0));
                $priceAdjDisc = (float)($discMap['del price adjustment discount'] ?? 0);
                $progRebate = (float)($discMap['del progressive rebate and roi'] ?? ($discMap['del progressive rebate'] ?? 0));
                $tciMfgClaim = (float)($discMap['del tci mfg claim'] ?? 0);
                $vbPayout = (float)($discMap['del vb payout'] ?? 0);

                // Del Net Amount
                $netAmount = (float)$item->line_total;
                if ($netAmount <= 0) {
                    $totD = $normDisc + $discReturn + $displayDisc + $extraDisc + $freightDisc + $marketPriceDisc + $tradeOffer + $tradeActivityDisc + $directDisc + $distComp + $dsfIncome + $dsfSalary + $freightChargesClaim + $jbpClaim + $perfStoreClaims + $priceAdjDisc + $progRebate + $tciMfgClaim + $vbPayout;
                    $netAmount = max(0, $gross - $totD + $salesTax + $salesTaxUnreg + $incomeTax);
                }

                $row = [
                    'purchase_id' => $p->id,
                    'item_id' => $item->id,
                    'invoice_date' => $invDateStr,
                    'sold_to_customer' => $vName,
                    'sold_to_code' => $vCode,
                    'distributor_code' => $dCode,
                    'distributor_name' => $companyTitle,
                    'ship_to_code' => $vCode,
                    'ship_to_customer' => $vName,
                    'delivery_no' => $deliveryNo,
                    'invoice_no' => $invNo,
                    'order_no' => $orderNo,
                    'dist_channel' => 'General Trade',
                    'business_unit' => 'Culinary',
                    'material_code' => $matCode,
                    'material_desc' => $matDesc,
                    'tax_type' => $taxType,
                    'class_name' => $catName,
                    'del_qty' => round($qty, 2),
                    'del_gross_amount' => round($gross, 2),
                    'del_norm_discount' => round($normDisc, 2),
                    'del_sales_tax' => round($salesTax, 2),
                    'del_sales_tax_unreg' => round($salesTaxUnreg, 2),
                    'del_disc_on_return' => round($discReturn, 2),
                    'del_display_discount' => round($displayDisc, 2),
                    'del_extra_discount' => round($extraDisc, 2),
                    'del_freight_discount' => round($freightDisc, 2),
                    'del_market_price_disc' => round($marketPriceDisc, 2),
                    'del_trade_offer' => round($tradeOffer, 2),
                    'del_income_tax' => round($incomeTax, 2),
                    'del_trade_activity_disc' => round($tradeActivityDisc, 2),
                    'del_direct_discount' => round($directDisc, 2),
                    'del_distributor_comp' => round($distComp, 2),
                    'del_dsf_income' => round($dsfIncome, 2),
                    'del_dsf_salary' => round($dsfSalary, 2),
                    'del_freight_charges_claim' => round($freightChargesClaim, 2),
                    'del_jbp_claim' => round($jbpClaim, 2),
                    'del_perf_store_claims' => round($perfStoreClaims, 2),
                    'del_price_adj_discount' => round($priceAdjDisc, 2),
                    'del_progressive_rebate' => round($progRebate, 2),
                    'del_tci_mfg_claim' => round($tciMfgClaim, 2),
                    'del_vb_payout' => round($vbPayout, 2),
                    'del_net_amount' => round($netAmount, 2),
                ];

                $rows[] = $row;

                // Accumulate totals
                $totals['del_qty'] += $row['del_qty'];
                $totals['del_gross_amount'] += $row['del_gross_amount'];
                $totals['del_norm_discount'] += $row['del_norm_discount'];
                $totals['del_sales_tax'] += $row['del_sales_tax'];
                $totals['del_sales_tax_unreg'] += $row['del_sales_tax_unreg'];
                $totals['del_disc_on_return'] += $row['del_disc_on_return'];
                $totals['del_display_discount'] += $row['del_display_discount'];
                $totals['del_extra_discount'] += $row['del_extra_discount'];
                $totals['del_freight_discount'] += $row['del_freight_discount'];
                $totals['del_market_price_disc'] += $row['del_market_price_disc'];
                $totals['del_trade_offer'] += $row['del_trade_offer'];
                $totals['del_income_tax'] += $row['del_income_tax'];
                $totals['del_trade_activity_disc'] += $row['del_trade_activity_disc'];
                $totals['del_direct_discount'] += $row['del_direct_discount'];
                $totals['del_distributor_comp'] += $row['del_distributor_comp'];
                $totals['del_dsf_income'] += $row['del_dsf_income'];
                $totals['del_dsf_salary'] += $row['del_dsf_salary'];
                $totals['del_freight_charges_claim'] += $row['del_freight_charges_claim'];
                $totals['del_jbp_claim'] += $row['del_jbp_claim'];
                $totals['del_perf_store_claims'] += $row['del_perf_store_claims'];
                $totals['del_price_adj_discount'] += $row['del_price_adj_discount'];
                $totals['del_progressive_rebate'] += $row['del_progressive_rebate'];
                $totals['del_tci_mfg_claim'] += $row['del_tci_mfg_claim'];
                $totals['del_vb_payout'] += $row['del_vb_payout'];
                $totals['del_net_amount'] += $row['del_net_amount'];
            }
        }

        // Round all totals
        foreach ($totals as $k => $v) {
            $totals[$k] = round($v, 2);
        }

        return [
            'rows' => $rows,
            'totals' => $totals
        ];
    }
}
