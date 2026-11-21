<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerClaim;
use App\Models\CustomerClaimItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Vendor;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\Account;
use App\Models\StockMovement;
use App\Services\BalanceService;
use App\Services\VoucherService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClaimController extends Controller
{
    /**
     * Display a listing of claims & damages.
     */
    public function index(Request $request)
    {
        $query = CustomerClaim::with(['customer', 'vendor', 'warehouse', 'creator', 'items.product'])->latest('claim_date')->latest('id');

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('claim_type')) {
            $query->where('claim_type', $request->claim_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('claim_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('claim_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function($q) use ($term) {
                $q->where('claim_no', 'like', "%{$term}%")
                  ->orWhere('remarks', 'like', "%{$term}%")
                  ->orWhereHas('customer', function($cq) use ($term) {
                      $cq->where('customer_name', 'like', "%{$term}%")->orWhere('mobile', 'like', "%{$term}%");
                  })
                  ->orWhereHas('vendor', function($vq) use ($term) {
                      $vq->where('name', 'like', "%{$term}%");
                  });
            });
        }

        $claims = $query->paginate(20)->withQueryString();

        // Metric Totals
        $allClaims = CustomerClaim::all();
        $totalClaimsCount = $allClaims->count();
        $totalClaimValue = $allClaims->sum('total_claim_amount');
        $totalAdjustedCompany = $allClaims->sum('claim_adjusted');
        $totalRefundsPaid = $allClaims->sum('refund_amount');

        $customers = Customer::where('status', '!=', 'inactive')->orWhereNull('status')->orderBy('customer_name')->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('admin_panel.claims.index', compact(
            'claims',
            'totalClaimsCount',
            'totalClaimValue',
            'totalAdjustedCompany',
            'totalRefundsPaid',
            'customers',
            'vendors'
        ));
    }

    /**
     * Show the form for creating a new claim / damage.
     */
    public function create()
    {
        $lastClaim = CustomerClaim::latest('id')->first();
        $nextClaimNo = 'CLM-' . str_pad(((optional($lastClaim)->id ?? 0) + 1), 5, '0', STR_PAD_LEFT);

        $customers = Customer::where('status', '!=', 'inactive')->orWhereNull('status')->orderBy('customer_name')->get();
        $vendors = Vendor::orderBy('name')->get();
        
        // Ensure default Damage Warehouse exists
        $warehouses = Warehouse::orderBy('warehouse_name')->get();
        $damageWarehouse = Warehouse::where('warehouse_name', 'like', '%Damage%')
            ->orWhere('warehouse_name', 'like', '%Claim%')
            ->first();

        if (!$damageWarehouse && $warehouses->isNotEmpty()) {
            $damageWarehouse = $warehouses->first();
        }

        $payment_accounts = Account::whereHas('head', function($q) {
            $q->whereIn('name', ['Cash', 'Bank', 'cash', 'bank']);
        })->where('status', 1)->orderBy('title')->get();

        $allProducts = Product::where('is_active', true)->orderBy('item_name')->take(80)->get();
        $orderBookers = class_exists(\App\Models\SalesOfficer::class)
            ? \App\Models\SalesOfficer::active()->orderBy('name')->get()
            : collect();

        return view('admin_panel.claims.create', compact(
            'nextClaimNo',
            'customers',
            'vendors',
            'warehouses',
            'damageWarehouse',
            'payment_accounts',
            'allProducts',
            'orderBookers'
        ));
    }

    /**
     * Store a newly created claim.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'claim_date' => 'required|date',
            'claim_type' => 'required|string',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required',
            'qty' => 'required|array',
            'qty.*' => 'required|numeric|min:0.01',
            'rate' => 'required|array',
            'rate.*' => 'required|numeric|min:0',
            'settlement_type' => 'required|string|in:ledger_credit,cash_refund,replacement',
            'refund_account_id' => 'nullable|exists:accounts,id',
            'refund_amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $lastClaim = CustomerClaim::latest('id')->first();
            $claimNo = 'CLM-' . str_pad(((optional($lastClaim)->id ?? 0) + 1), 5, '0', STR_PAD_LEFT);

            // Fetch Vendor Allowance & Balance snapshot
            $vendorId = $validated['vendor_id'] ?? null;
            $allowanceData = $vendorId ? $this->getVendorAllowanceData($vendorId) : [
                'vendor_payable' => 0,
                'company_allowance' => 0,
                'claims_utilized' => 0,
                'remaining_allowance' => 0
            ];

            $companyAllowanceBefore = (float)($allowanceData['company_allowance'] > 0 ? $allowanceData['remaining_allowance'] : $allowanceData['vendor_payable']);

            // Calculate Item Totals
            $totalClaimAmount = 0;
            $itemsData = [];
            $now = now();
            $movements = [];

            foreach ($validated['product_id'] as $idx => $rawProductId) {
                $qty = (float)($validated['qty'][$idx] ?? 0);
                $rate = (float)($validated['rate'][$idx] ?? 0);

                if ($qty <= 0) continue;

                $colorField = $request->color[$idx] ?? null;
                $productId = $rawProductId;

                if (is_string($rawProductId) && str_contains($rawProductId, '|variant|')) {
                    $parts = explode('|variant|', $rawProductId);
                    $productId = (int)$parts[0];
                    if (empty($colorField) && !empty($parts[1])) {
                        $colorField = $parts[1];
                    }
                } else {
                    $productId = (int)$rawProductId;
                }

                $product = Product::find($productId);
                if (!$product) continue;

                $ppb = (float)($request->pieces_per_box[$idx] ?? ($product->pieces_per_box > 0 ? $product->pieces_per_box : 1));
                if ($ppb <= 0) $ppb = 1;

                $cartons = (float)($request->cartons[$idx] ?? floor($qty / $ppb));
                $loose = (float)($request->loose_pieces[$idx] ?? ($qty % $ppb));
                $damageReason = $request->damage_reason[$idx] ?? 'damaged';

                // Line Total
                $lineTotal = !empty($request->line_total[$idx]) && (float)$request->line_total[$idx] > 0
                    ? (float)$request->line_total[$idx]
                    : round($qty * $rate, 2);

                $totalClaimAmount += $lineTotal;

                $itemsData[] = [
                    'product_id' => $productId,
                    'vendor_id' => $vendorId,
                    'damage_reason' => $damageReason,
                    'unit' => $request->unit[$idx] ?? ($product->unit->name ?? 'Pcs'),
                    'pieces_per_box' => $ppb,
                    'cartons' => $cartons,
                    'loose_pieces' => $loose,
                    'total_pieces' => $qty,
                    'rate' => $rate,
                    'line_total' => $lineTotal,
                    'color' => $colorField,
                    'batch_no' => $request->batch_no[$idx] ?? null,
                    'expiry_date' => $request->expiry_date[$idx] ?? null,
                    'company_disc_share_percent' => (float)($product->purchase_discount_percent ?? 5.0),
                    'company_disc_share_amount' => round($lineTotal * 0.05, 2),
                ];

                // Deposit Damaged Stock into Damage/Selected Warehouse
                $stock = WarehouseStock::firstOrNew([
                    'warehouse_id' => $validated['warehouse_id'],
                    'product_id' => $productId
                ]);

                $currentPieces = (float)($stock->total_pieces ?? 0);
                $newPieces = $currentPieces + $qty;
                $stock->total_pieces = $newPieces;
                $stock->quantity = $newPieces / $ppb;
                if (is_null($stock->price) || $stock->price == 0) {
                    $stock->price = $rate;
                }
                $stock->save();

                // Stock Movement (IN to Damage Warehouse)
                $movements[] = [
                    'product_id' => $productId,
                    'type' => 'in',
                    'qty' => $qty,
                    'ref_type' => 'CUSTOMER_CLAIM',
                    'ref_id' => 0, // updated below
                    'note' => "Customer Claim & Damage (#{$claimNo})",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Vendor Claim Adjustment & Remaining Balance
            $claimAdjusted = $totalClaimAmount;
            $companyRemainingBalance = max(0, $companyAllowanceBefore - $claimAdjusted);

            // Refund Amount handling
            $refundAmount = 0;
            if ($validated['settlement_type'] === 'cash_refund') {
                $refundAmount = (float)($validated['refund_amount'] ?? $totalClaimAmount);
            }

            // Create Customer Claim Master
            $claim = CustomerClaim::create([
                'claim_no' => $claimNo,
                'claim_date' => $validated['claim_date'],
                'customer_id' => $validated['customer_id'],
                'vendor_id' => $vendorId,
                'warehouse_id' => $validated['warehouse_id'],
                'claim_type' => $validated['claim_type'],
                'total_claim_amount' => $totalClaimAmount,
                'company_allowance_before' => $companyAllowanceBefore,
                'claim_adjusted' => $claimAdjusted,
                'company_remaining_balance' => $companyRemainingBalance,
                'settlement_type' => $validated['settlement_type'],
                'refund_account_id' => $validated['refund_account_id'] ?? null,
                'refund_amount' => $refundAmount,
                'status' => 'received',
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => auth()->id() ?? 1,
            ]);

            // Save Items
            foreach ($itemsData as $iData) {
                $iData['customer_claim_id'] = $claim->id;
                CustomerClaimItem::create($iData);
            }

            // Update ref_id in movements and insert
            foreach ($movements as &$m) {
                $m['ref_id'] = $claim->id;
            }
            if (!empty($movements)) {
                DB::table('stock_movements')->insert($movements);
            }

            // Accounting & Ledgers
            $balanceService = app(BalanceService::class);
            $voucherService = app(VoucherService::class);
            $arId = $balanceService->getAccountsReceivableId();
            $salesRevId = $balanceService->getSalesRevenueId();

            // 1. Customer Credit Note / Ledger Adjustment
            if ($validated['settlement_type'] === 'ledger_credit' || $validated['settlement_type'] === 'replacement') {
                // Voucher: Debit Sales Return / Claims Expense, Credit Customer AR
                $voucherData = [
                    'voucher_type' => \App\Models\VoucherMaster::TYPE_JOURNAL,
                    'date' => $validated['claim_date'],
                    'status' => \App\Models\VoucherMaster::STATUS_POSTED,
                    'payment_from' => 'Customer',
                    'party_type' => \App\Models\Customer::class,
                    'party_id' => $validated['customer_id'],
                    'remarks' => "Customer Claim & Damage Credit Note #{$claimNo}",
                ];

                $lines = [
                    [
                        'account_id' => $salesRevId,
                        'debit' => $totalClaimAmount,
                        'credit' => 0,
                        'narration' => "Damage / Claim Return (#{$claimNo})"
                    ],
                    [
                        'account_id' => $arId,
                        'debit' => 0,
                        'credit' => $totalClaimAmount,
                        'narration' => "Claim Credit to Customer (#{$claimNo})"
                    ]
                ];

                $voucherService->createVoucher($voucherData, $lines, auth()->id() ?? 1);

                // Update Customer Ledger Balance
                $custLedger = \App\Models\CustomerLedger::where('customer_id', $validated['customer_id'])->latest('id')->first();
                $prevClosing = $custLedger ? (float)$custLedger->closing_balance : 0;
                \App\Models\CustomerLedger::create([
                    'customer_id' => $validated['customer_id'],
                    'admin_or_user_id' => auth()->id() ?? 1,
                    'previous_balance' => $prevClosing,
                    'closing_balance' => $prevClosing - $totalClaimAmount,
                ]);
            } elseif ($validated['settlement_type'] === 'cash_refund' && $refundAmount > 0) {
                // Immediate Cash/Bank Refund to customer
                $cashAccId = $validated['refund_account_id'] ?? $balanceService->getCashAccountId();
                $voucherData = [
                    'voucher_type' => \App\Models\VoucherMaster::TYPE_PAYMENT,
                    'date' => $validated['claim_date'],
                    'status' => \App\Models\VoucherMaster::STATUS_POSTED,
                    'payment_from' => 'Customer',
                    'party_type' => \App\Models\Customer::class,
                    'party_id' => $validated['customer_id'],
                    'remarks' => "Cash Refund for Claim #{$claimNo}",
                ];

                $lines = [
                    [
                        'account_id' => $salesRevId,
                        'debit' => $refundAmount,
                        'credit' => 0,
                        'narration' => "Damage Claim Refund Paid (#{$claimNo})"
                    ],
                    [
                        'account_id' => $cashAccId,
                        'debit' => 0,
                        'credit' => $refundAmount,
                        'narration' => "Refund Cash/Bank Out (#{$claimNo})"
                    ]
                ];

                $voucherService->createVoucher($voucherData, $lines, auth()->id() ?? 1);
            }

            // 2. Vendor Ledger Adjustment (Debit Note reducing Vendor Payable)
            if ($vendorId) {
                $apId = $balanceService->getAccountsPayableId();
                $purchExpenseId = $balanceService->getPurchaseExpenseId();

                $vendorVoucher = [
                    'voucher_type' => \App\Models\VoucherMaster::TYPE_JOURNAL,
                    'date' => $validated['claim_date'],
                    'status' => \App\Models\VoucherMaster::STATUS_POSTED,
                    'payment_from' => 'Vendor',
                    'party_type' => \App\Models\Vendor::class,
                    'party_id' => $vendorId,
                    'remarks' => "Debit Note: Claim / Damage Deduction against Company (#{$claimNo})",
                ];

                $vLines = [
                    [
                        'account_id' => $apId,
                        'debit' => $claimAdjusted,
                        'credit' => 0,
                        'narration' => "Debit Note: Customer Damage Claim (#{$claimNo})"
                    ],
                    [
                        'account_id' => $purchExpenseId,
                        'debit' => 0,
                        'credit' => $claimAdjusted,
                        'narration' => "Damage Claim Adjustment (#{$claimNo})"
                    ]
                ];

                $voucherService->createVoucher($vendorVoucher, $vLines, auth()->id() ?? 1);

                // Update Vendor Ledger Closing Balance
                $vendorLedger = \App\Models\VendorLedger::where('vendor_id', $vendorId)->latest('id')->first();
                $vPrevClosing = $vendorLedger ? (float)$vendorLedger->closing_balance : 0;
                \App\Models\VendorLedger::updateOrCreate(
                    ['vendor_id' => $vendorId],
                    [
                        'admin_or_user_id' => auth()->id() ?? 1,
                        'opening_balance' => $vendorLedger ? $vendorLedger->opening_balance : 0,
                        'previous_balance' => $vPrevClosing,
                        'closing_balance' => $vPrevClosing - $claimAdjusted, // Reduces payable
                    ]
                );
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Claim #{$claimNo} processed successfully. Rs " . number_format($totalClaimAmount, 2) . " adjusted.",
                    'redirect' => route('claims.index'),
                    'claim_id' => $claim->id,
                    'claim_no' => $claimNo,
                    'remaining_vendor_balance' => $companyRemainingBalance
                ]);
            }

            return redirect()->route('claims.index')->with('success', "Claim #{$claimNo} processed successfully.");

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error saving claim: ' . $e->getMessage()
                ], 422);
            }

            return back()->with('error', 'Error saving claim: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display printable Claim details & Debit Note.
     */
    public function show($id)
    {
        $claim = CustomerClaim::with(['customer', 'vendor', 'warehouse', 'creator', 'items.product'])->findOrFail($id);
        return view('admin_panel.claims.show', compact('claim'));
    }

    /**
     * AJAX endpoint to fetch Vendor Claim Allowance & Payable metrics.
     */
    public function getVendorAllowance($vendorId)
    {
        $data = $this->getVendorAllowanceData($vendorId);
        return response()->json($data);
    }

    /**
     * Compute vendor's damage/claim allowance pool from purchase 3-tier discounts and claims.
     */
    private function getVendorAllowanceData($vendorId)
    {
        $vendor = Vendor::find($vendorId);
        if (!$vendor) {
            return [
                'vendor_name' => 'Unknown',
                'vendor_payable' => 0,
                'company_allowance' => 0,
                'claims_utilized' => 0,
                'remaining_allowance' => 0,
            ];
        }

        $balanceService = app(BalanceService::class);
        $vendorPayable = $balanceService->getVendorBalance($vendorId);

        // Sum company discount amounts given by this vendor in purchases
        $totalCompanyDisc = (float) Purchase::where('vendor_id', $vendorId)
            ->whereIn('status_purchase', ['approved', 'Partial', 'Returned'])
            ->sum('company_disc_amount');

        if ($totalCompanyDisc == 0) {
            $totalCompanyDisc = (float) PurchaseItem::whereHas('purchase', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->sum('company_disc_amount');
        }

        // Also check if any custom discount heads (e.g. Return, Expiry, Claim) exist
        $customHeadsSum = 0;
        $purchasesWithCustom = Purchase::where('vendor_id', $vendorId)->whereNotNull('custom_disc_breakdown')->get();
        foreach ($purchasesWithCustom as $p) {
            $breakdown = is_array($p->custom_disc_breakdown) ? $p->custom_disc_breakdown : (json_decode($p->custom_disc_breakdown ?? '[]', true) ?: []);
            foreach ($breakdown as $b) {
                $name = strtolower($b['name'] ?? '');
                if (str_contains($name, 'return') || str_contains($name, 'expiry') || str_contains($name, 'claim') || str_contains($name, 'damage')) {
                    $customHeadsSum += (float)($b['amount'] ?? 0);
                }
            }
        }

        $totalAllowance = max($totalCompanyDisc, $customHeadsSum);

        // Claims previously utilized against this vendor
        $claimsUtilized = (float) CustomerClaim::where('vendor_id', $vendorId)
            ->where('status', '!=', 'rejected')
            ->sum('total_claim_amount');

        $remainingAllowance = max(0, $totalAllowance - $claimsUtilized);

        return [
            'vendor_name' => $vendor->name,
            'vendor_payable' => $vendorPayable,
            'company_allowance' => $totalAllowance,
            'claims_utilized' => $claimsUtilized,
            'remaining_allowance' => $remainingAllowance,
        ];
    }

    /**
     * Dedicated Claim & Damage Reconciliation Report.
     */
    public function report(Request $request)
    {
        $vendors = Vendor::orderBy('name')->get();
        $customers = Customer::where('status', '!=', 'inactive')->orWhereNull('status')->orderBy('customer_name')->get();

        $selectedVendorId = $request->vendor_id;
        $dateFrom = $request->date_from ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $dateTo = $request->date_to ?? Carbon::now()->format('Y-m-d');

        // Vendor-wise summary list
        $vendorSummaries = [];
        $vendorsToQuery = $selectedVendorId ? Vendor::where('id', $selectedVendorId)->get() : $vendors;

        foreach ($vendorsToQuery as $v) {
            $allowanceData = $this->getVendorAllowanceData($v->id);

            // Claims in selected period
            $claimsInPeriod = CustomerClaim::where('vendor_id', $v->id)
                ->whereBetween('claim_date', [$dateFrom, $dateTo])
                ->where('status', '!=', 'rejected')
                ->sum('total_claim_amount');

            // Total purchases in selected period
            $purchasesInPeriod = Purchase::where('vendor_id', $v->id)
                ->whereBetween('purchase_date', [$dateFrom, $dateTo])
                ->whereIn('status_purchase', ['approved', 'Partial', 'Returned'])
                ->sum('net_amount');

            $vendorSummaries[] = [
                'vendor' => $v,
                'total_purchases' => $purchasesInPeriod,
                'company_allowance' => $allowanceData['company_allowance'],
                'total_claims_all_time' => $allowanceData['claims_utilized'],
                'claims_in_period' => $claimsInPeriod,
                'remaining_allowance' => $allowanceData['remaining_allowance'],
                'vendor_payable' => $allowanceData['vendor_payable'],
                'net_payable_after_claims' => max(0, $allowanceData['vendor_payable'] - $claimsInPeriod)
            ];
        }

        // Top damaged products in period
        $topDamagedProducts = CustomerClaimItem::whereHas('claim', function($q) use ($dateFrom, $dateTo, $selectedVendorId) {
            $q->whereBetween('claim_date', [$dateFrom, $dateTo]);
            if ($selectedVendorId) $q->where('vendor_id', $selectedVendorId);
        })
        ->select('product_id', DB::raw('SUM(total_pieces) as total_qty'), DB::raw('SUM(line_total) as total_loss'))
        ->with('product')
        ->groupBy('product_id')
        ->orderByDesc('total_loss')
        ->limit(10)
        ->get();

        return view('admin_panel.claims.report', compact(
            'vendors',
            'customers',
            'vendorSummaries',
            'topDamagedProducts',
            'selectedVendorId',
            'dateFrom',
            'dateTo'
        ));
    }
}
