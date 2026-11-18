<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesOfficer;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TradeDiscountReportController extends Controller
{
    /**
     * Display the Trade Discount Report page.
     */
    public function index(Request $request)
    {
        $salesOfficers = SalesOfficer::orderBy('name')->get();
        $customers = Customer::orderBy('customer_name')->get();
        $warehouses = Warehouse::orderBy('warehouse_name')->get();

        $minDate = Sale::whereNotIn('sale_status', ['cancelled'])->min('created_at');
        $fromDate = $request->get('from_date', $minDate ? Carbon::parse($minDate)->format('Y-m-d') : Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->format('Y-m-d'));

        return view('admin_panel.reporting.trade_discount_report', compact(
            'salesOfficers',
            'customers',
            'warehouses',
            'fromDate',
            'toDate'
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
            'group_by' => $data['group_by'],
            'code_label' => $data['code_label'],
            'name_label' => $data['name_label'],
            'rows' => $data['rows'],
            'totals' => $data['totals'],
            'print_date' => Carbon::now()->format('d F , Y h:i a'),
            'count' => count($data['rows']),
        ]);
    }

    /**
     * Export Trade Discount Report as Excel-compatible CSV.
     */
    public function exportCsv(Request $request)
    {
        $data = $this->buildReportData($request);
        $rows = $data['rows'];
        $totals = $data['totals'];
        $codeLabel = $data['code_label'];
        $nameLabel = $data['name_label'];

        $filename = 'trade_discount_report_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($rows, $totals, $codeLabel, $nameLabel) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Header line 1
            fputcsv($handle, [
                $codeLabel,
                $nameLabel,
                'Gross Sales Exc GST',
                'Gross Sales Inc GST',
                'Net Amount',
                'Discount Regular',
                'Discount Regular %',
                'Discount Special',
                'Discount Special %',
                'Discount Total',
                'Discount Total %',
                'Trade Offer Unit',
                'Trade Offer Unit Value',
                'Trade Offer In Value',
                'Trade Offer Total',
                'Total T.O %',
                'Trade Activity Value'
            ]);

            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r['entity_code'] ?? ($r['order_booker_code'] ?? ''),
                    $r['entity_name'] ?? ($r['order_booker_name'] ?? ''),
                    number_format($r['gross_exc_gst'], 2, '.', ''),
                    number_format($r['gross_inc_gst'], 2, '.', ''),
                    number_format($r['net_amount'], 2, '.', ''),
                    number_format($r['reg_discount'], 2, '.', ''),
                    number_format($r['reg_discount_pct'], 2, '.', '') . ' %',
                    number_format($r['sp_discount'], 2, '.', ''),
                    number_format($r['sp_discount_pct'], 2, '.', '') . ' %',
                    number_format($r['total_discount'], 2, '.', ''),
                    number_format($r['total_discount_pct'], 2, '.', '') . ' %',
                    number_format($r['to_unit'], 0, '.', ''),
                    number_format($r['to_unit_value'], 2, '.', ''),
                    number_format($r['to_in_value'], 2, '.', ''),
                    number_format($r['to_total'], 2, '.', ''),
                    number_format($r['to_total_pct'], 2, '.', '') . ' %',
                    number_format($r['trade_activity_val'], 2, '.', ''),
                ]);
            }

            // Summary row
            fputcsv($handle, [
                'Total:',
                '',
                number_format($totals['gross_exc_gst'], 2, '.', ''),
                number_format($totals['gross_inc_gst'], 2, '.', ''),
                number_format($totals['net_amount'], 2, '.', ''),
                number_format($totals['reg_discount'], 2, '.', ''),
                number_format($totals['reg_discount_pct'], 2, '.', '') . ' %',
                number_format($totals['sp_discount'], 2, '.', ''),
                number_format($totals['sp_discount_pct'], 2, '.', '') . ' %',
                number_format($totals['total_discount'], 2, '.', ''),
                number_format($totals['total_discount_pct'], 2, '.', '') . ' %',
                number_format($totals['to_unit'], 0, '.', ''),
                number_format($totals['to_unit_value'], 2, '.', ''),
                number_format($totals['to_in_value'], 2, '.', ''),
                number_format($totals['to_total'], 2, '.', ''),
                number_format($totals['to_total_pct'], 2, '.', '') . ' %',
                number_format($totals['trade_activity_val'], 2, '.', ''),
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Core aggregation logic for Trade Discount Report.
     */
    protected function buildReportData(Request $request): array
    {
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $salesOfficerId = $request->get('sales_officer_id');
        $customerId = $request->get('customer_id');
        $warehouseId = $request->get('warehouse_id');
        $groupBy = $request->get('group_by', 'customer'); // default: customer

        $query = Sale::with(['customer_relation.salesOfficer', 'salesOfficer', 'items'])
            ->whereNotIn('sale_status', ['cancelled']);

        if (!empty($fromDate)) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('created_at', '<=', $toDate);
        }
        if (!empty($customerId) && $customerId !== 'all') {
            $query->where('customer_id', $customerId);
        }
        if (!empty($warehouseId) && $warehouseId !== 'all') {
            $query->whereHas('items', function ($itemQ) use ($warehouseId) {
                $itemQ->where('warehouse_id', $warehouseId);
            });
        }
        if (!empty($salesOfficerId) && $salesOfficerId !== 'all') {
            $query->where(function ($q) use ($salesOfficerId) {
                $q->where('sales_officer_id', $salesOfficerId)
                  ->orWhere(function ($sub) use ($salesOfficerId) {
                      $sub->where(function ($nullQ) {
                          $nullQ->whereNull('sales_officer_id')
                                ->orWhere('sales_officer_id', 0)
                                ->orWhere('sales_officer_id', '');
                      })->whereHas('customer_relation', function ($custQ) use ($salesOfficerId) {
                          $custQ->where('sales_officer_id', $salesOfficerId);
                      });
                  });
            });
        }

        $sales = $query->orderBy('created_at', 'asc')->get();

        // Optional distributor code prefix from settings
        $distributorCode = Setting::where('key', 'distributor_code')->value('value') ?: 'D0043';

        $grouped = [];

        foreach ($sales as $sale) {
            if ($groupBy === 'invoice') {
                $entityId = 'inv_' . $sale->id;
                $entityCode = $sale->invoice_no ?: ('INV-' . $sale->id);
                $custName = $sale->customer_relation ? $sale->customer_relation->customer_name : (!empty($sale->walkin_name) ? $sale->walkin_name : 'Walk-in Customer');
                $entityName = $custName . ' (' . Carbon::parse($sale->created_at)->format('d-M-Y') . ')';
            } elseif ($groupBy === 'order_booker') {
                $officer = $sale->salesOfficer ?: ($sale->customer_relation->salesOfficer ?? null);
                $officerId = $officer ? $officer->id : 0;
                $entityId = 'ob_' . $officerId;
                $entityName = $officer ? $officer->name : 'Direct / Unassigned';
                if ($officer) {
                    $entityCode = !empty($officer->code) ? $officer->code : ($distributorCode . 'OB' . $officer->id);
                } else {
                    $entityCode = 'DIRECT';
                }
            } else {
                // Default: Customer Wise
                if ($sale->customer_relation) {
                    $cust = $sale->customer_relation;
                    $entityId = 'cust_' . $cust->id;
                    $entityCode = !empty($cust->customer_id) ? $cust->customer_id : ('CUST-' . str_pad($cust->id, 4, '0', STR_PAD_LEFT));
                    $entityName = $cust->customer_name;
                } elseif (!empty($sale->walkin_name)) {
                    $entityId = 'walkin_' . md5($sale->walkin_name);
                    $entityCode = 'WALK-IN';
                    $entityName = $sale->walkin_name;
                } else {
                    $entityId = 'walkin_direct';
                    $entityCode = 'WALK-IN';
                    $entityName = 'Walk-in Customer';
                }
            }

            if (!isset($grouped[$entityId])) {
                $grouped[$entityId] = [
                    'entity_id'          => $entityId,
                    'entity_code'        => $entityCode,
                    'entity_name'        => $entityName,
                    'customer_code'      => $entityCode,
                    'customer_name'      => $entityName,
                    'order_booker_code'  => $entityCode,
                    'order_booker_name'  => $entityName,
                    'gross_exc_gst'      => 0.0,
                    'gross_inc_gst'      => 0.0,
                    'net_amount'         => 0.0,
                    'reg_discount'       => 0.0,
                    'sp_discount'        => 0.0,
                    'to_unit'            => 0.0,
                    'to_unit_value'      => 0.0,
                    'to_in_value'        => 0.0,
                    'trade_activity_val' => 0.0,
                    'invoice_count'      => 0,
                ];
            }

            // Calculations per sale
            $grossExc = (float) ($sale->total_exclusive_gst > 0 ? $sale->total_exclusive_gst : $sale->total_gross);
            $taxes = (float) ($sale->total_sales_tax + $sale->total_further_tax + ($sale->total_adv_tax ?? 0));
            $grossInc = (float) ($grossExc + $taxes);
            $net = (float) $sale->total_net;
            $regDisc = (float) $sale->total_reg_discount;
            $spDisc = (float) $sale->total_sp_discount;
            $bonusQty = (float) $sale->total_bonus_qty;
            $extraDiscount = (float) $sale->total_extradiscount;
            $tradeActivity = (float) $sale->total_trade_activity;

            // Unit value calculation for bonus items
            $bonusValue = 0.0;
            foreach ($sale->items as $item) {
                if ($item->bonus_qty > 0) {
                    $itemPrice = (float) ($item->price > 0 ? $item->price : ($item->retail_price ?? 0));
                    $bonusValue += (float) ($item->bonus_qty * $itemPrice);
                }
            }

            $grouped[$entityId]['gross_exc_gst']      += $grossExc;
            $grouped[$entityId]['gross_inc_gst']      += $grossInc;
            $grouped[$entityId]['net_amount']         += $net;
            $grouped[$entityId]['reg_discount']       += $regDisc;
            $grouped[$entityId]['sp_discount']        += $spDisc;
            $grouped[$entityId]['to_unit']            += $bonusQty;
            $grouped[$entityId]['to_unit_value']      += $bonusValue;
            $grouped[$entityId]['to_in_value']        += $extraDiscount;
            $grouped[$entityId]['trade_activity_val'] += $tradeActivity;
            $grouped[$entityId]['invoice_count']      += 1;
        }

        // Finalize rows with percentages
        $rows = [];
        $totals = [
            'gross_exc_gst'      => 0.0,
            'gross_inc_gst'      => 0.0,
            'net_amount'         => 0.0,
            'reg_discount'       => 0.0,
            'reg_discount_pct'   => 0.0,
            'sp_discount'        => 0.0,
            'sp_discount_pct'    => 0.0,
            'total_discount'     => 0.0,
            'total_discount_pct' => 0.0,
            'to_unit'            => 0.0,
            'to_unit_value'      => 0.0,
            'to_in_value'        => 0.0,
            'to_total'           => 0.0,
            'to_total_pct'       => 0.0,
            'trade_activity_val' => 0.0,
            'invoice_count'      => 0,
        ];

        foreach ($grouped as $row) {
            $net = $row['net_amount'];
            $regDisc = $row['reg_discount'];
            $spDisc = $row['sp_discount'];
            $totalDisc = $regDisc + $spDisc;

            $toUnit = $row['to_unit'];
            $toUnitVal = $row['to_unit_value'];
            $toInVal = $row['to_in_value'];
            $toTotal = $toUnitVal + $toInVal;

            // Percentages calculated over Net Amount as shown in the standard report
            $regDiscPct = $net > 0 ? ($regDisc / $net) * 100 : 0.0;
            $spDiscPct = $net > 0 ? ($spDisc / $net) * 100 : 0.0;
            $totalDiscPct = $net > 0 ? ($totalDisc / $net) * 100 : 0.0;
            $toTotalPct = $net > 0 ? ($toTotal / $net) * 100 : 0.0;

            $row['reg_discount_pct']   = round($regDiscPct, 2);
            $row['sp_discount_pct']    = round($spDiscPct, 2);
            $row['total_discount']     = round($totalDisc, 2);
            $row['total_discount_pct'] = round($totalDiscPct, 2);
            $row['to_total']           = round($toTotal, 2);
            $row['to_total_pct']       = round($toTotalPct, 2);

            $rows[] = $row;

            // Aggregate totals
            $totals['gross_exc_gst']      += $row['gross_exc_gst'];
            $totals['gross_inc_gst']      += $row['gross_inc_gst'];
            $totals['net_amount']         += $net;
            $totals['reg_discount']       += $regDisc;
            $totals['sp_discount']        += $spDisc;
            $totals['total_discount']     += $totalDisc;
            $totals['to_unit']            += $toUnit;
            $totals['to_unit_value']      += $toUnitVal;
            $totals['to_in_value']        += $toInVal;
            $totals['to_total']           += $toTotal;
            $totals['trade_activity_val'] += $row['trade_activity_val'];
            $totals['invoice_count']      += $row['invoice_count'];
        }

        // Grand total percentages
        $grandNet = $totals['net_amount'];
        $totals['reg_discount_pct']   = $grandNet > 0 ? round(($totals['reg_discount'] / $grandNet) * 100, 2) : 0.0;
        $totals['sp_discount_pct']    = $grandNet > 0 ? round(($totals['sp_discount'] / $grandNet) * 100, 2) : 0.0;
        $totals['total_discount_pct'] = $grandNet > 0 ? round(($totals['total_discount'] / $grandNet) * 100, 2) : 0.0;
        $totals['to_total_pct']       = $grandNet > 0 ? round(($totals['to_total'] / $grandNet) * 100, 2) : 0.0;

        $codeLabel = match($groupBy) {
            'invoice' => 'Invoice No',
            'order_booker' => 'Order Booker Code',
            default => 'Customer Code',
        };
        $nameLabel = match($groupBy) {
            'invoice' => 'Customer (Date)',
            'order_booker' => 'Order Booker Name',
            default => 'Customer Name',
        };

        return [
            'group_by'   => $groupBy,
            'code_label' => $codeLabel,
            'name_label' => $nameLabel,
            'rows'       => $rows,
            'totals'     => $totals
        ];
    }
}
