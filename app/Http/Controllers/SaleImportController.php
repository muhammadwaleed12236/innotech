<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\TaxType;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleImportController extends Controller
{
    /**
     * Resolve effective branch ID for current user.
     */
    private function resolveBranchId(?Request $request = null): ?int
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            if ($request && $request->filled('branch_id')) {
                return (int) $request->input('branch_id');
            }
            $firstBranch = Branch::orderBy('id')->first();

            return $firstBranch ? (int) $firstBranch->id : null;
        }

        return method_exists($user, 'getEffectiveBranchId')
            ? $user->getEffectiveBranchId()
            : ($user->branch_id ? (int) $user->branch_id : null);
    }

    /**
     * Parse an uploaded HTML or PDF invoice file and match customers/products against the DB.
     */
    public function parse(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:html,htm,pdf,txt|max:20480',
            'branch_id' => 'nullable|integer|exists:branches,id',
        ]);

        $branchId = $this->resolveBranchId($request);
        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $rawContent = file_get_contents($file->getRealPath());

        if ($rawContent === false || trim($rawContent) === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Uploaded file is empty.',
            ], 422);
        }

        if ($ext === 'pdf') {
            $parsedInvoices = $this->parsePdfContent($file->getRealPath(), $rawContent);
            if (empty($parsedInvoices)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Could not extract structured invoices from this PDF. Please upload the .html invoice file or a text-based PDF.',
                ], 422);
            }
        } else {
            $parsedInvoices = $this->parseHtmlContent($rawContent);
            if (empty($parsedInvoices)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No valid invoice blocks or line items were found in the HTML file.',
                ], 422);
            }
        }

        // Match customers, products, and duplicate invoice numbers in the active branch
        foreach ($parsedInvoices as &$inv) {
            $custData = $inv['customer'];
            $matchedCustomer = $this->findMatchingCustomer($custData, $branchId);
            if ($matchedCustomer) {
                $inv['customer_status'] = 'found';
                $inv['matched_customer_id'] = $matchedCustomer->id;
                $inv['matched_customer_name'] = $matchedCustomer->customer_name;
                $inv['matched_customer_code'] = $matchedCustomer->customer_id;
            } else {
                $inv['customer_status'] = 'missing';
                $inv['matched_customer_id'] = null;
            }

            $invoiceNo = trim((string) ($inv['meta']['invoice_no'] ?? ''));
            $inv['invoice_exists'] = $invoiceNo !== ''
                ? Sale::where('invoice_no', $invoiceNo)
                    ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                    ->exists()
                : false;

            $missingCount = 0;
            foreach ($inv['items'] as &$item) {
                $matchedProduct = $this->findMatchingProduct($item, $branchId);
                if ($matchedProduct) {
                    $item['product_status'] = 'found';
                    $item['matched_product_id'] = $matchedProduct->id;
                    $item['matched_product_name'] = $matchedProduct->item_name;
                    $item['matched_product_code'] = $matchedProduct->item_code;
                } else {
                    $item['product_status'] = 'missing';
                    $item['matched_product_id'] = null;
                    $missingCount++;
                }
            }
            unset($item);
            $inv['missing_items_count'] = $missingCount;
        }
        unset($inv);

        return response()->json([
            'ok' => true,
            'branch_id' => $branchId,
            'invoice_count' => count($parsedInvoices),
            'invoices' => $parsedInvoices,
        ]);
    }

    /**
     * Commit confirmed parsed invoices: create missing customers/products if requested, skip cancelled ones, and save Sales.
     */
    public function commit(Request $request)
    {
        $request->validate([
            'invoices' => 'required|array|min:1',
            'sale_status' => 'nullable|in:booked,posted',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'warehouse_id' => 'nullable|integer|exists:warehouses,id',
        ]);

        $branchId = $this->resolveBranchId($request);
        $saleStatus = $request->input('sale_status', 'booked');
        $warehouseId = $request->input('warehouse_id');
        if (! $warehouseId) {
            $wh = Warehouse::when($branchId, fn ($q) => $q->where('branch_id', $branchId))->orderBy('id')->first()
                ?? Warehouse::orderBy('id')->first();
            $warehouseId = $wh ? $wh->id : null;
        }

        $defaultUnitId = Unit::orderBy('id')->value('id') ?? 1;
        $defaultCategoryId = Category::orderBy('id')->value('id') ?? 1;
        $userId = Auth::id() ?? 1;

        $createdSales = [];
        $createdProductsCount = 0;
        $createdCustomersCount = 0;
        $skippedInvoicesCount = 0;

        try {
            DB::transaction(function () use (
                $request,
                $branchId,
                $saleStatus,
                $warehouseId,
                $defaultUnitId,
                $defaultCategoryId,
                $userId,
                &$createdSales,
                &$createdProductsCount,
                &$createdCustomersCount,
                &$skippedInvoicesCount
            ) {
                // Local caches so duplicate missing items/customers across multiple invoices in one file are created only once
                $createdProductCache = [];
                $createdCustomerCache = [];

                foreach ($request->input('invoices', []) as $inv) {
                    if (! empty($inv['skip_invoice'])) {
                        $skippedInvoicesCount++;
                        continue;
                    }

                    // 1. Resolve Customer
                    $customerId = ! empty($inv['matched_customer_id']) ? (int) $inv['matched_customer_id'] : null;
                    $custAction = $inv['customer_action'] ?? ($customerId ? 'existing' : 'create');

                    if (! $customerId) {
                        if ($custAction === 'cancel') {
                            $skippedInvoicesCount++;
                            continue;
                        }

                        $custData = $inv['customer'] ?? [];
                        $cacheKey = strtolower(trim(($custData['customer_code'] ?? '') . '|' . ($custData['customer_name'] ?? '')));
                        if (isset($createdCustomerCache[$cacheKey])) {
                            $customerId = $createdCustomerCache[$cacheKey];
                        } else {
                            // Re-check DB first
                            $existingCust = $this->findMatchingCustomer($custData, $branchId);
                            if ($existingCust) {
                                $customerId = $existingCust->id;
                            } else {
                                $code = trim((string) ($custData['customer_code'] ?? ''));
                                if ($code === '') {
                                    $code = 'CUST-' . str_pad((Customer::withoutGlobalScopes()->max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);
                                }
                                $filerRaw = strtolower(trim((string) ($custData['filer_type'] ?? '')));
                                $filerType = str_contains($filerRaw, 'non') ? 'Non-Filer' : ($filerRaw !== '' ? 'Filer' : null);

                                $newCust = Customer::create([
                                    'branch_id' => $branchId,
                                    'customer_id' => $code,
                                    'customer_name' => trim((string) ($custData['customer_name'] ?? 'Imported Customer')),
                                    'cnic' => trim((string) ($custData['cnic'] ?? '')) ?: null,
                                    'filer_type' => $filerType,
                                    'address' => trim((string) ($custData['address'] ?? '')) ?: null,
                                    'mobile' => trim((string) ($custData['mobile'] ?? '')) ?: null,
                                    'customer_type' => trim((string) ($custData['channel_type'] ?? '')) ?: 'Main Customer',
                                    'party_type' => 'customer',
                                    'status' => 'active',
                                ]);
                                $customerId = $newCust->id;
                                $createdCustomersCount++;
                            }
                            $createdCustomerCache[$cacheKey] = $customerId;
                        }
                    }

                    // 2. Resolve Items
                    $resolvedItems = [];
                    foreach ($inv['items'] ?? [] as $item) {
                        $action = $item['action'] ?? (! empty($item['matched_product_id']) ? 'existing' : 'cancel');
                        if ($action === 'cancel') {
                            continue;
                        }

                        $product = null;
                        if (! empty($item['matched_product_id'])) {
                            $product = Product::find((int) $item['matched_product_id']);
                        }

                        if (! $product && $action === 'create') {
                            $pKey = strtolower(trim(($item['item_code'] ?? '') . '|' . ($item['item_name'] ?? '')));
                            if (isset($createdProductCache[$pKey])) {
                                $product = $createdProductCache[$pKey];
                            } else {
                                $product = $this->findMatchingProduct($item, $branchId);
                                if (! $product) {
                                    $catId = $defaultCategoryId;
                                    $catName = trim((string) ($item['category'] ?? ''));
                                    if ($catName !== '') {
                                        $cat = Category::whereRaw('LOWER(name) = ?', [strtolower($catName)])->first();
                                        if (! $cat) {
                                            $cat = Category::create(['name' => $catName]);
                                        }
                                        $catId = $cat->id;
                                    }

                                    $itemCode = trim((string) ($item['item_code'] ?? ''));
                                    if ($itemCode === '') {
                                        $itemCode = 'IMP-' . str_pad((Product::withoutGlobalScopes()->max('id') ?? 0) + 1, 5, '0', STR_PAD_LEFT);
                                    }

                                    $taxTypeId = null;
                                    $isThirdSchedule = 0;
                                    $taxTypeStr = strtoupper(trim((string) ($item['tax_type'] ?? '')));
                                    if ($taxTypeStr !== '') {
                                        $tt = TaxType::whereRaw('UPPER(name) LIKE ?', ["%{$taxTypeStr}%"])->first();
                                        if ($tt) {
                                            $taxTypeId = $tt->id;
                                            $isThirdSchedule = method_exists($tt, 'isThirdSchedule') && $tt->isThirdSchedule() ? 1 : 0;
                                        }
                                    }

                                    $tradePrice = (float) ($item['trade_price'] ?? 0);
                                    $mrpVal = (float) ($item['mrp'] ?? 0);

                                    $product = Product::create([
                                        'branch_id' => $branchId,
                                        'creater_id' => $userId,
                                        'category_id' => $catId,
                                        'unit_id' => $defaultUnitId,
                                        'item_code' => $itemCode,
                                        'item_name' => trim((string) ($item['item_name'] ?? $itemCode)),
                                        'hs_code' => trim((string) ($item['hs_code'] ?? '')) ?: null,
                                        'tracking_type' => 'simple',
                                        'size_mode' => 'by_pieces',
                                        'pieces_per_box' => 1,
                                        'trade_price' => $tradePrice,
                                        'retail_price' => $mrpVal > 0 ? $mrpVal : $tradePrice,
                                        'sale_price_per_piece' => $tradePrice,
                                        'sale_price_per_box' => $tradePrice,
                                        'purchase_price_per_piece' => $tradePrice,
                                        'purchase_price_per_box' => $tradePrice,
                                        'tax_type_id' => $taxTypeId,
                                        'is_third_schedule' => $isThirdSchedule,
                                        'is_active' => 1,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                    $createdProductsCount++;
                                }
                                $createdProductCache[$pKey] = $product;
                            }
                        }

                        if (! $product) {
                            continue;
                        }

                        $qty = max(0, (float) ($item['qty'] ?? 0));
                        $price = (float) ($item['trade_price'] ?? 0);
                        $gross = (float) ($item['gross_amount'] ?? ($qty * $price));
                        $spDisc = (float) ($item['sp_disc_amount'] ?? 0);
                        $regDisc = (float) ($item['reg_disc_amount'] ?? 0);
                        $tradeOffer = (float) ($item['trade_offer'] ?? 0);
                        $tradeActivity = (float) ($item['trade_activity'] ?? 0);
                        $salesTax = (float) ($item['sales_tax_amount'] ?? 0);
                        $mrpAmount = (float) ($item['mrp'] ?? 0);
                        $furtherTax = (float) ($item['further_tax_amount'] ?? 0);
                        $netAmount = (float) ($item['net_amount'] ?? 0);

                        $resolvedItems[] = [
                            'product' => $product,
                            'qty' => $qty,
                            'bonus_qty' => (float) ($item['bonus_qty'] ?? 0),
                            'price' => $price,
                            'gross_amount' => $gross,
                            'sp_disc_amount' => $spDisc,
                            'reg_disc_amount' => $regDisc,
                            'discount_amount' => round($spDisc + $regDisc + $tradeOffer, 2),
                            'trade_activity' => $tradeActivity,
                            'sales_tax_amount' => $salesTax,
                            'mrp_amount' => $mrpAmount,
                            'further_tax_amount' => $furtherTax,
                            'total' => $netAmount > 0 ? $netAmount : round(max(0, $gross - $spDisc - $regDisc - $tradeOffer - $tradeActivity) + $salesTax + $furtherTax, 2),
                        ];
                    }

                    if (empty($resolvedItems)) {
                        $skippedInvoicesCount++;
                        continue;
                    }

                    // 3. Invoice Number & Totals
                    $rawInvNo = trim((string) ($inv['meta']['invoice_no'] ?? ''));
                    $invoiceNo = $rawInvNo !== '' ? $rawInvNo : Sale::generateInvoiceNo('INV');
                    if (Sale::where('invoice_no', $invoiceNo)->exists()) {
                        $invoiceNo = $invoiceNo . '-IMP-' . Str::upper(Str::random(4));
                    }

                    $totalGross = collect($resolvedItems)->sum('gross_amount');
                    $totalSpDisc = collect($resolvedItems)->sum('sp_disc_amount');
                    $totalRegDisc = collect($resolvedItems)->sum('reg_disc_amount');
                    $totalTradeActivity = collect($resolvedItems)->sum('trade_activity');
                    $totalSalesTax = collect($resolvedItems)->sum('sales_tax_amount');
                    $totalFurtherTax = collect($resolvedItems)->sum('further_tax_amount');
                    $totalMrpTax = collect($resolvedItems)->sum('mrp_amount');
                    $totalBonusQty = collect($resolvedItems)->sum('bonus_qty');
                    $totalItemsQty = collect($resolvedItems)->sum('qty');
                    $totalExtraDisc = round($totalSpDisc + $totalRegDisc, 2);
                    $summaryAdvTax = (float) ($inv['summary']['advance_income_tax'] ?? 0);
                    $totalNet = round(collect($resolvedItems)->sum('total') + $summaryAdvTax, 2);

                    // Parse invoice date if available
                    $createdAt = now();
                    if (! empty($inv['meta']['invoice_date'])) {
                        $dateStr = trim(explode(',', (string) $inv['meta']['invoice_date'])[0]);
                        try {
                            $createdAt = Carbon::parse($dateStr);
                        } catch (\Throwable $e) {
                            $createdAt = now();
                        }
                    }

                    $sale = Sale::create([
                        'branch_id' => $branchId,
                        'customer_id' => $customerId,
                        'invoice_no' => $invoiceNo,
                        'reference' => trim((string) ($inv['meta']['route'] ?? '')) ?: ('Imported: ' . ($rawInvNo ?: $invoiceNo)),
                        'sale_status' => $saleStatus,
                        'is_booking' => $saleStatus === 'booked' ? 1 : 0,
                        'source' => 'import',
                        'total_items' => $totalItemsQty,
                        'total_gross' => round($totalGross, 2),
                        'total_bill_amount' => round($totalNet, 2),
                        'total_extradiscount' => $totalExtraDisc,
                        'total_sp_discount' => round($totalSpDisc, 2),
                        'total_reg_discount' => round($totalRegDisc, 2),
                        'total_trade_activity' => round($totalTradeActivity, 2),
                        'total_sales_tax' => round($totalSalesTax, 2),
                        'total_further_tax' => round($totalFurtherTax, 2),
                        'total_mrp_tax' => round($totalMrpTax, 2),
                        'total_adv_tax' => round($summaryAdvTax, 2),
                        'total_bonus_qty' => $totalBonusQty,
                        'total_net' => round($totalNet, 2),
                    ]);
                    $sale->created_at = $createdAt;
                    $sale->save();

                    foreach ($resolvedItems as $ri) {
                        $prod = $ri['product'];

                        if ($saleStatus === 'posted' && $warehouseId) {
                            $whStock = WarehouseStock::where('product_id', $prod->id)
                                ->where('warehouse_id', $warehouseId)
                                ->lockForUpdate()
                                ->first();
                            $avail = $whStock ? (float) $whStock->total_pieces : 0;
                            if ($avail < $ri['qty']) {
                                throw new \RuntimeException("Insufficient stock for '{$prod->item_name}' (Available: {$avail}, Required: {$ri['qty']}). Import as Booked instead.");
                            }
                            $whStock->total_pieces = $avail - $ri['qty'];
                            $whStock->save();
                        }

                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'warehouse_id' => $warehouseId,
                            'product_id' => $prod->id,
                            'brand_id' => $prod->brand_id,
                            'category_id' => $prod->category_id,
                            'sub_category_id' => $prod->sub_category_id,
                            'unit_id' => $prod->unit_id ?? $defaultUnitId,
                            'qty' => $ri['qty'],
                            'total_pieces' => $ri['qty'],
                            'bonus_qty' => $ri['bonus_qty'],
                            'price' => $ri['price'],
                            'price_per_piece' => $ri['price'],
                            'gross_amount' => round($ri['gross_amount'], 2),
                            'discount_amount' => round($ri['discount_amount'], 2),
                            'sp_disc_amount' => round($ri['sp_disc_amount'], 2),
                            'reg_disc_amount' => round($ri['reg_disc_amount'], 2),
                            'trade_activity' => round($ri['trade_activity'], 2),
                            'sales_tax_amount' => round($ri['sales_tax_amount'], 2),
                            'mrp_amount' => round($ri['mrp_amount'], 2),
                            'further_tax_amount' => round($ri['further_tax_amount'], 2),
                            'total' => round($ri['total'], 2),
                        ]);
                    }

                    $createdSales[] = [
                        'id' => $sale->id,
                        'invoice_no' => $sale->invoice_no,
                        'total_net' => $sale->total_net,
                    ];
                }
            });
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'message' => count($createdSales) . ' invoice(s) imported successfully.',
            'created_sales' => $createdSales,
            'created_products' => $createdProductsCount,
            'created_customers' => $createdCustomersCount,
            'skipped_invoices' => $skippedInvoicesCount,
        ]);
    }

    /**
     * Parse HTML invoice blocks (.SingleLoadForm).
     */
    public function parseHtmlContent(string $html): array
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $blocks = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' SingleLoadForm ')]");

        if (! $blocks || $blocks->length === 0) {
            $blocks = $xpath->query('//body');
        }

        $invoices = [];
        foreach ($blocks as $block) {
            $customer = [
                'customer_name' => '',
                'customer_code' => '',
                'filer_type' => '',
                'address' => '',
                'channel_type' => '',
                'channel_name' => '',
                'sub_channel_name' => '',
                'mobile' => '',
                'strn' => '',
                'ntn' => '',
                'cnic' => '',
            ];
            $meta = [
                'invoice_no' => '',
                'order_booker' => '',
                'delivery_man' => '',
                'booking_date' => '',
                'invoice_date' => '',
                'delivery_date' => '',
                'route' => '',
            ];

            // Parse all storeinfo key-value rows inside this block
            $storeRows = $xpath->query(".//table[contains(concat(' ', normalize-space(@class), ' '), ' storeinfo ')]//tr", $block);
            foreach ($storeRows as $tr) {
                $th = $xpath->query('./th', $tr)->item(0);
                $td = $xpath->query('./td', $tr)->item(0);
                if (! $th || ! $td) {
                    continue;
                }
                $label = strtolower(trim(preg_replace('/\s+/', ' ', $th->textContent)));
                $value = trim(preg_replace('/\s+/', ' ', $td->textContent));

                if (str_starts_with($label, 'm/s')) {
                    if (preg_match('/^(.*?)\s*\[\s*([^\]]+)\s*\]$/', $value, $m)) {
                        $customer['customer_name'] = trim($m[1]);
                        $customer['customer_code'] = trim($m[2]);
                    } else {
                        $customer['customer_name'] = $value;
                    }
                } elseif (str_contains($label, 'income tax status')) {
                    $customer['filer_type'] = $value;
                } elseif (str_starts_with($label, 'address')) {
                    $customer['address'] = $value;
                } elseif (str_contains($label, 'channel type')) {
                    $customer['channel_type'] = $value;
                } elseif (str_contains($label, 'sub channel')) {
                    $customer['sub_channel_name'] = $value;
                } elseif (str_contains($label, 'channel name')) {
                    $customer['channel_name'] = $value;
                } elseif (str_contains($label, 'contact no')) {
                    $customer['mobile'] = $value;
                } elseif (str_contains($label, 'sales tax no')) {
                    $customer['strn'] = $value;
                } elseif (str_contains($label, 'ntn no')) {
                    $customer['ntn'] = $value;
                } elseif (str_contains($label, 'cnic no')) {
                    $customer['cnic'] = $value;
                } elseif (str_contains($label, 'invoice no')) {
                    $meta['invoice_no'] = $value;
                } elseif (str_contains($label, 'order booker')) {
                    $meta['order_booker'] = $value;
                } elseif (str_contains($label, 'delivery man')) {
                    $meta['delivery_man'] = $value;
                } elseif (str_contains($label, 'booking date')) {
                    $meta['booking_date'] = $value;
                } elseif (str_contains($label, 'invoice date')) {
                    $meta['invoice_date'] = $value;
                } elseif (str_contains($label, 'delivery date')) {
                    $meta['delivery_date'] = $value;
                } elseif (str_starts_with($label, 'route')) {
                    $meta['route'] = $value;
                }
            }

            // Parse line items from #report table (only direct tr children)
            $items = [];
            $currentCategory = '';
            $reportTables = $xpath->query(".//table[@id='report']", $block);
            if ($reportTables && $reportTables->length > 0) {
                $reportTable = $reportTables->item(0);
                $rows = $xpath->query('./tbody/tr | ./tr', $reportTable);
                foreach ($rows as $tr) {
                    $tds = $xpath->query('./td', $tr);
                    if (! $tds || $tds->length === 0) {
                        continue;
                    }

                    // Category header row (usually colspan or single td)
                    if ($tds->length === 1) {
                        $catText = trim(preg_replace('/\s+/', ' ', $tds->item(0)->textContent));
                        if ($catText !== '' && ! str_contains(strtolower($catText), 'total')) {
                            $currentCategory = $catText;
                        }
                        continue;
                    }

                    if ($tds->length < 12) {
                        continue;
                    }

                    $rawItem = trim(preg_replace('/\s+/', ' ', $tds->item(0)->textContent));
                    if ($rawItem === '' || str_starts_with(strtolower($rawItem), 'total')) {
                        continue;
                    }

                    $itemCode = '';
                    $itemName = $rawItem;
                    if (preg_match('/^(\d+)\s+(.+)$/', $rawItem, $m)) {
                        $itemCode = trim($m[1]);
                        $itemName = trim($m[2]);
                    }

                    // Nested tax table inside td[10]: S.Tax | MRP | 4%
                    $taxCell = $tds->item(10);
                    $nestedTds = $xpath->query('.//td', $taxCell);
                    $salesTax = 0.0;
                    $mrp = 0.0;
                    $furtherTax = 0.0;
                    if ($nestedTds && $nestedTds->length >= 3) {
                        $salesTax = $this->toFloat($nestedTds->item(0)->textContent);
                        $mrp = $this->toFloat($nestedTds->item(1)->textContent);
                        $furtherTax = $this->toFloat($nestedTds->item(2)->textContent);
                    } else {
                        $salesTax = $this->toFloat($taxCell->textContent);
                    }

                    $items[] = [
                        'category' => $currentCategory,
                        'raw_name' => $rawItem,
                        'item_code' => $itemCode,
                        'item_name' => $itemName,
                        'hs_code' => trim($tds->item(1)->textContent),
                        'qty' => $this->toFloat($tds->item(2)->textContent),
                        'trade_price' => $this->toFloat($tds->item(3)->textContent),
                        'gross_amount' => $this->toFloat($tds->item(4)->textContent),
                        'bonus_qty' => $this->toFloat($tds->item(5)->textContent),
                        'sp_disc_amount' => $this->toFloat($tds->item(6)->textContent),
                        'reg_disc_amount' => $this->toFloat($tds->item(7)->textContent),
                        'trade_offer' => $this->toFloat($tds->item(8)->textContent),
                        'trade_activity' => $this->toFloat($tds->item(9)->textContent),
                        'sales_tax_amount' => $salesTax,
                        'mrp' => $mrp,
                        'further_tax_amount' => $furtherTax,
                        'net_amount' => $this->toFloat($tds->item(11)->textContent),
                        'tax_type' => $tds->length > 12 ? trim($tds->item(12)->textContent) : '',
                    ];
                }
            }

            // Parse summary totals
            $summary = [
                'gross_amount' => 0.0,
                'total_discount' => 0.0,
                'special_discount' => 0.0,
                'regular_discount' => 0.0,
                'trade_offer' => 0.0,
                'trade_activity' => 0.0,
                'add_sales_tax' => 0.0,
                'additional_sales_tax' => 0.0,
                'advance_income_tax' => 0.0,
                'trading_terms' => 0.0,
                'net_invoice_amount' => 0.0,
            ];

            $allRows = $xpath->query('.//table[not(@id="report") and not(contains(@class, "storeinfo"))]//tr', $block);
            foreach ($allRows as $tr) {
                $cells = $xpath->query('./th|./td', $tr);
                if (! $cells || $cells->length < 2) {
                    continue;
                }
                $k = strtolower(trim(preg_replace('/\s+/', ' ', $cells->item(0)->textContent)));
                $v = $this->toFloat($cells->item($cells->length - 1)->textContent);
                if (str_contains($k, 'gross amount')) {
                    $summary['gross_amount'] = $v;
                } elseif (str_contains($k, 'total discount')) {
                    $summary['total_discount'] = $v;
                } elseif (str_contains($k, 'special discount')) {
                    $summary['special_discount'] = $v;
                } elseif (str_contains($k, 'regular discount')) {
                    $summary['regular_discount'] = $v;
                } elseif (str_contains($k, 'trade offer')) {
                    $summary['trade_offer'] = $v;
                } elseif (str_contains($k, 'trade activity')) {
                    $summary['trade_activity'] = $v;
                } elseif (str_contains($k, 'additional sales tax')) {
                    $summary['additional_sales_tax'] = $v;
                } elseif (str_contains($k, 'add sales tax')) {
                    $summary['add_sales_tax'] = $v;
                } elseif (str_contains($k, 'advance income tax')) {
                    $summary['advance_income_tax'] = $v;
                } elseif (str_contains($k, 'trading terms')) {
                    $summary['trading_terms'] = $v;
                } elseif (str_contains($k, 'net invoice amount')) {
                    $summary['net_invoice_amount'] = $v;
                }
            }

            if (empty($items) && $meta['invoice_no'] === '' && $customer['customer_name'] === '') {
                continue;
            }

            $invoices[] = [
                'meta' => $meta,
                'customer' => $customer,
                'items' => $items,
                'summary' => $summary,
            ];
        }

        return $invoices;
    }

    /**
     * Best-effort PDF text extraction (via pdftotext if installed, or PDF text-stream extraction).
     */
    private function parsePdfContent(string $filePath, string $rawBinary): array
    {
        $text = '';
        if (function_exists('shell_exec')) {
            $escaped = escapeshellarg($filePath);
            $out = @shell_exec("pdftotext -layout {$escaped} - 2>&1");
            if (is_string($out) && trim($out) !== '' && ! str_contains(strtolower($out), 'not recognized')) {
                $text = $out;
            }
        }

        if ($text === '') {
            // Fallback: extract literal strings inside PDF text objects
            if (preg_match_all('/\(([^()]*)\)\s*Tj/s', $rawBinary, $matches)) {
                $text = implode("\n", $matches[1]);
            }
        }

        if (trim($text) === '') {
            return [];
        }

        // If extracted text looks like HTML or has Invoice No # / M/S lines, parse basic structure
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $customer = [
            'customer_name' => '',
            'customer_code' => '',
            'filer_type' => '',
            'address' => '',
            'channel_type' => '',
            'channel_name' => '',
            'sub_channel_name' => '',
            'mobile' => '',
            'strn' => '',
            'ntn' => '',
            'cnic' => '',
        ];
        $meta = [
            'invoice_no' => '',
            'order_booker' => '',
            'delivery_man' => '',
            'booking_date' => '',
            'invoice_date' => '',
            'delivery_date' => '',
            'route' => '',
        ];
        $items = [];

        foreach ($lines as $line) {
            $clean = trim(preg_replace('/\s+/', ' ', $line));
            if ($clean === '') {
                continue;
            }
            if (preg_match('/M\/S\s*:\s*(.+?)(?:\s*\[\s*([^\]]+)\s*\])?$/i', $clean, $m)) {
                $customer['customer_name'] = trim($m[1]);
                if (! empty($m[2])) {
                    $customer['customer_code'] = trim($m[2]);
                }
            } elseif (preg_match('/Invoice No\s*#?\s*:\s*(\S+)/i', $clean, $m)) {
                $meta['invoice_no'] = trim($m[1]);
            } elseif (preg_match('/Contact No\s*:\s*(\S+)/i', $clean, $m)) {
                $customer['mobile'] = trim($m[1]);
            } elseif (preg_match('/Address\s*:\s*(.+)$/i', $clean, $m)) {
                $customer['address'] = trim($m[1]);
            } elseif (preg_match('/^(\d+)\s+([A-Za-z].+?)\s+(\d{4}\.\d{4})\s+(\d+(?:\.\d+)?)\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})/', $clean, $m)) {
                $items[] = [
                    'category' => '',
                    'raw_name' => $m[1] . ' ' . $m[2],
                    'item_code' => trim($m[1]),
                    'item_name' => trim($m[2]),
                    'hs_code' => trim($m[3]),
                    'qty' => $this->toFloat($m[4]),
                    'trade_price' => $this->toFloat($m[5]),
                    'gross_amount' => $this->toFloat($m[6]),
                    'bonus_qty' => 0.0,
                    'sp_disc_amount' => 0.0,
                    'reg_disc_amount' => 0.0,
                    'trade_offer' => 0.0,
                    'trade_activity' => 0.0,
                    'sales_tax_amount' => 0.0,
                    'mrp' => 0.0,
                    'further_tax_amount' => 0.0,
                    'net_amount' => $this->toFloat($m[6]),
                    'tax_type' => '',
                ];
            }
        }

        if (empty($items) && $meta['invoice_no'] === '' && $customer['customer_name'] === '') {
            return [];
        }

        return [[
            'meta' => $meta,
            'customer' => $customer,
            'items' => $items,
            'summary' => [
                'gross_amount' => collect($items)->sum('gross_amount'),
                'net_invoice_amount' => collect($items)->sum('net_amount'),
                'advance_income_tax' => 0.0,
            ],
        ]];
    }

    private function findMatchingCustomer(array $custData, ?int $branchId): ?Customer
    {
        $code = trim((string) ($custData['customer_code'] ?? ''));
        $name = trim((string) ($custData['customer_name'] ?? ''));
        $mobile = trim((string) ($custData['mobile'] ?? ''));

        $baseQuery = Customer::query();
        if ($branchId) {
            $baseQuery->where('branch_id', $branchId);
        }

        if ($code !== '') {
            $byCode = (clone $baseQuery)->where('customer_id', $code)->first();
            if ($byCode) {
                return $byCode;
            }
        }

        if ($name !== '') {
            $byName = (clone $baseQuery)->whereRaw('LOWER(customer_name) = ?', [strtolower($name)])->first();
            if ($byName) {
                return $byName;
            }
        }

        if ($mobile !== '') {
            $byMobile = (clone $baseQuery)->where('mobile', $mobile)->first();
            if ($byMobile) {
                return $byMobile;
            }
        }

        return null;
    }

    private function findMatchingProduct(array $item, ?int $branchId): ?Product
    {
        $code = trim((string) ($item['item_code'] ?? ''));
        $name = trim((string) ($item['item_name'] ?? ''));

        $baseQuery = Product::query();
        if ($branchId) {
            $baseQuery->where('branch_id', $branchId);
        }

        if ($code !== '') {
            $byCode = (clone $baseQuery)->where('item_code', $code)->first();
            if ($byCode) {
                return $byCode;
            }
        }

        if ($name !== '') {
            $byName = (clone $baseQuery)->whereRaw('LOWER(item_name) = ?', [strtolower($name)])->first();
            if ($byName) {
                return $byName;
            }
        }

        return null;
    }

    private function toFloat(string $val): float
    {
        $cleaned = str_replace([',', ' '], '', trim($val));
        if ($cleaned === '' || ! is_numeric($cleaned)) {
            if (preg_match('/-?\d+(?:\.\d+)?/', $cleaned, $m)) {
                return (float) $m[0];
            }

            return 0.0;
        }

        return (float) $cleaned;
    }
}
