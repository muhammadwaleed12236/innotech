<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\WarehouseStock;
use App\Models\StockMovement;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleReturnController extends Controller
{
    public function showReturnForm($id)
    {
        $sale = Sale::with([
            'customer_relation',
            'items.product.brand',
            'items.product.unit',
            'items.product.taxType',
            'orderBooker',
            'salesman',
            'warehouse'
        ])->findOrFail($id);

        $accounts = Account::whereHas('head', function($q) {
            $q->whereIn('name', ['Cash', 'Bank', 'cash', 'bank', 'Cheque', 'cheque']);
        })->where('status', 1)->orderBy('title')->get();

        $warehouses = Warehouse::all();
        
        // Calculate already returned quantities
        $pastReturns = SaleReturn::where('sale_id', $id)
            ->with('items')
            ->get();
        
        $returnedQtyMap = [];
        foreach ($pastReturns as $sr) {
            foreach ($sr->items as $srItem) {
                $key = (int)$srItem->product_id;
                if (!isset($returnedQtyMap[$key])) {
                    $returnedQtyMap[$key] = 0;
                }
                $returnedQtyMap[$key] += (float)$srItem->qty;
            }
        }
        
        // Format sale items with complete FMCG packaging and return limits
        $sale->items->each(function ($item) use ($returnedQtyMap) {
            $product = $item->product;
            $ppb = ($product && (float)$product->pieces_per_box > 0) ? (int)$product->pieces_per_box : 1;
            $item->ppb = $ppb;
            
            // Total sold pieces
            $totalPieces = (float)($item->total_pieces ?: ($item->qty ?: 0));
            $item->sold_total_pieces = $totalPieces;
            $item->sold_cartons = $ppb > 1 ? floor($totalPieces / $ppb) : 0;
            $item->sold_loose = $ppb > 1 ? ($totalPieces % $ppb) : (int)$totalPieces;
            
            // Already returned
            $prodId = (int)$item->product_id;
            $alreadyReturned = (float)($returnedQtyMap[$prodId] ?? 0);
            $item->already_returned_pieces = $alreadyReturned;
            $item->already_returned_cartons = $ppb > 1 ? floor($alreadyReturned / $ppb) : 0;
            $item->already_returned_loose = $ppb > 1 ? ($alreadyReturned % $ppb) : (int)$alreadyReturned;
            
            // Max returnable
            $maxReturnable = max(0, $totalPieces - $alreadyReturned);
            $item->max_returnable_pieces = $maxReturnable;
            $item->max_returnable_cartons = $ppb > 1 ? floor($maxReturnable / $ppb) : 0;
            $item->max_returnable_loose = $ppb > 1 ? ($maxReturnable % $ppb) : (int)$maxReturnable;
            
            // Pricing & Rates
            $tradeRate = (float)($item->price ?: ($product ? $product->trade_price : 0));
            $item->trade_rate = $tradeRate;
            $item->piece_rate = (float)($item->price_per_piece ?: ($ppb > 0 ? round($tradeRate / $ppb, 4) : $tradeRate));
            
            // Discount %
            $regDisc = (float)($item->reg_disc_percent ?: ($item->discount_percent ?: 0));
            $spDisc = (float)($item->sp_disc_percent ?: 0);
            $item->total_disc_pct = $regDisc + $spDisc;
            $item->discount_rate = $item->total_disc_pct;
            
            // Tax & MRP
            $taxType = $product ? $product->taxType : null;
            $item->gst_rate = (float)($item->sales_tax_percent ?: ($taxType ? $taxType->getSaleGstRate() : 0));
            $item->is_3rd = (bool)($item->mrp_amount > 0 || ($taxType && $taxType->isThirdSchedule()) || ($product && $product->is_third_schedule));
            $item->mrp_rate = (float)($item->retail_price ?: ($product ? ($product->retail_price ?: $product->sale_price_per_piece) : 0));
            
            // Meta labels
            $item->item_title = $item->product_name ?: ($product ? $product->item_name : 'Product');
            $item->brand_title = $product && $product->brand ? $product->brand->name : ($product->brand_name ?? '');
            $item->unit_title = $product && $product->unit ? $product->unit->name : 'pc';
            $item->batch_title = $item->batch_no ?: '—';
            $item->size_mode = $product ? ($product->size_mode ?? 'by_pieces') : ($item->size_mode ?? 'by_pieces');
        });
        
        return view('admin_panel.sale.sale_return.create', compact('sale', 'accounts', 'warehouses', 'returnedQtyMap'));
    }

    /**
     * Process the sale return
     */
    public function processSaleReturn(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'nullable|exists:sales,id',
            'customer_id' => 'nullable',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date' => 'required|date',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'color' => 'nullable|array',
            'qty' => 'required|array',
            'qty.*' => 'required|numeric|min:0',
            'price' => 'required|array',
            'price.*' => 'required|numeric|min:0',
            'item_disc' => 'nullable|array',
            'extra_discount' => 'nullable|numeric|min:0',
            'return_reason' => 'nullable|string',
            'payment_account_id' => 'nullable|array',
            'payment_amount' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // Generate Return Invoice Number
            $lastReturnId = SaleReturn::max('id') ?? 0;
            do {
                $lastReturnId++;
                $nextInvoice = 'SR-' . str_pad($lastReturnId, 4, '0', STR_PAD_LEFT);
            } while (SaleReturn::where('return_invoice', $nextInvoice)->exists());

            // Resolve Customer ID (fallback to Walking Customer if empty/null)
            $customerId = $validated['customer_id'] ?? null;
            if (!$customerId) {
                $walkingCustomer = \App\Models\Customer::where('customer_type', 'Walking Customer')->first();
                if ($walkingCustomer) {
                    $customerId = $walkingCustomer->id;
                } else {
                    $walkingCustomer = \App\Models\Customer::create([
                        'customer_id' => 'CUST-WALK',
                        'customer_name' => 'Walking Customer',
                        'customer_type' => 'Walking Customer',
                        'mobile' => '-',
                        'status' => 'active',
                        'opening_balance' => 0,
                    ]);
                    $customerId = $walkingCustomer->id;
                }
            }

            // Validate that at least one item has return quantity > 0
            $hasItems = false;
            foreach ($request->product_id as $idx => $productId) {
                $qty = (float) $request->qty[$idx];
                if ($qty > 0) {
                    $hasItems = true;
                    break;
                }
            }
            if (!$hasItems) {
                throw new \Exception("Please enter a return quantity for at least one item.");
            }

            // Create Sale Return Header
            $return = SaleReturn::create([
                'sale_id' => $validated['sale_id'] ?? null,
                'return_invoice' => $nextInvoice,
                'customer_id' => $customerId,
                'warehouse_id' => $validated['warehouse_id'],
                'return_date' => $validated['return_date'],
                'remarks' => $validated['return_reason'] ?? null,
                'status' => 'posted',
            ]);

            $sale = $validated['sale_id'] ? Sale::with('items')->find($validated['sale_id']) : null;
            $now = Carbon::now();
            $movements = [];
            $subtotal = 0;
            $totalItemDiscount = 0;

            // Process Each Return Item
            foreach ($request->product_id as $idx => $productId) {
                $qty = (float) $request->qty[$idx]; // Total pieces
                if ($qty <= 0) continue;

                $price = (float) $request->price[$idx];
                $itemDisc = (float) ($request->item_disc[$idx] ?? 0);
                // Get product for PPB and size_mode calculation
                $product = Product::find($productId);
                $sizeMode = $product->size_mode ?? 'by_pieces';
                $ppm2 = $product->m2_of_box ?? 0;

                $rColor = $request->color[$idx] ?? null;
                $vData = [];
                if (!empty($rColor)) {
                    $b64 = base64_decode($rColor, true);
                    $vData = ($b64 !== false) ? json_decode($b64, true) : json_decode($rColor, true);
                    if (!is_array($vData)) $vData = [];
                }

                $liveVariant = null;
                if ($product && !empty($product->color)) {
                    $prodVariants = json_decode($product->color, true);
                    if (is_array($prodVariants)) {
                        if (!empty($vData['barcode'])) {
                            $liveVariant = collect($prodVariants)->firstWhere('barcode', $vData['barcode']);
                        }
                        if (!$liveVariant && !empty($vData['name'])) {
                            $liveVariant = collect($prodVariants)->first(function($v) use ($vData) {
                                $n1 = strtolower(trim($v['name'] ?? ''));
                                $n2 = strtolower(trim($vData['name'] ?? ''));
                                return $n1 === $n2 || ($n1 && $n2 && (str_contains($n1, $n2) || str_contains($n2, $n1)));
                            });
                        }
                        if (!$liveVariant && !empty($vData['size']) && $vData['size'] !== '-') {
                            $liveVariant = collect($prodVariants)->first(function($v) use ($vData) {
                                $s1 = strtolower(trim($v['size'] ?? ''));
                                $s2 = strtolower(trim($vData['size'] ?? ''));
                                return $s1 === $s2 || ($s1 && $s2 && (str_starts_with($s1, $s2) || str_starts_with($s2, $s1)));
                            });
                        }
                        if (!$liveVariant && count($prodVariants) === 1) {
                            $liveVariant = $prodVariants[0];
                        }
                    }
                }

                $ppb = 1;
                if ($liveVariant && !empty($liveVariant['conv_factor']) && (float)$liveVariant['conv_factor'] > 0) {
                    $ppb = (float)$liveVariant['conv_factor'];
                } elseif ($liveVariant && !empty($liveVariant['pieces_per_box']) && (float)$liveVariant['pieces_per_box'] > 0) {
                    $ppb = (float)$liveVariant['pieces_per_box'];
                } elseif (!empty($vData['conv_factor']) && (float)$vData['conv_factor'] > 0) {
                    $ppb = (float)$vData['conv_factor'];
                } elseif (!empty($vData['pieces_per_box']) && (float)$vData['pieces_per_box'] > 0) {
                    $ppb = (float)$vData['pieces_per_box'];
                } elseif (!empty($vData['size']) && is_numeric($vData['size']) && (float)$vData['size'] > 0) {
                    $ppb = (float)$vData['size'];
                } elseif ($product && (float)$product->pieces_per_box > 0) {
                    $ppb = (float)$product->pieces_per_box;
                }

                if ($ppb <= 0) $ppb = 1;

                // Calculate boxes and loose pieces
                $boxes = (int)($request->return_carton[$idx] ?? floor($qty / $ppb));
                $loosePieces = (int)($request->return_loose[$idx] ?? ($qty % $ppb));

                // Calculate Line Total Logic based on size mode or direct line net
                if (!empty($request->line_total[$idx]) && (float)$request->line_total[$idx] > 0) {
                    $netLineTotal = (float)$request->line_total[$idx];
                    $lineTotal = (float)($request->line_gross[$idx] ?? $netLineTotal);
                } else {
                    if ($sizeMode === 'by_size') {
                        $lineTotal = round($ppm2 * $qty * $price, 2);
                    } elseif (in_array($sizeMode, ['by_cartons', 'by_bandal']) || $sizeMode === 'by_carton') {
                        $lineTotal = round(($ppb > 0 ? ($qty / $ppb) : $qty) * $price, 2);
                    } else {
                        $lineTotal = round($qty * $price, 2);
                    }
                    $netLineTotal = $lineTotal - $itemDisc;
                }

                // Create Return Item
                SaleReturnItem::create([
                    'sale_return_id' => $return->id,
                    'product_id' => $productId,
                    'product_name' => $product->item_name ?? $product->product_name ?? 'Product',
                    'color' => $request->color[$idx] ?? null,
                    'warehouse_id' => $validated['warehouse_id'],
                    'qty' => $qty,
                    'boxes' => $boxes,
                    'loose_pieces' => $loosePieces,
                    'price' => $price,
                    'item_discount' => $itemDisc,
                    'unit' => $product->unit->name ?? 'pc',
                    'line_total' => $netLineTotal,
                ]);

                // Calculate Stock Qty with Variant Conv Factor
                $stockQty = $qty;
                if ($sizeMode === 'by_kg' || $sizeMode === 'by_gm') {
                    $rColor = $request->color[$idx] ?? null;
                    if (!empty($rColor)) {
                        try {
                            $variantData = is_string($rColor) ? json_decode($rColor, true) : $rColor;
                            if (is_array($variantData) && isset($variantData['conv_factor'])) {
                                $factor = (float)$variantData['conv_factor'];
                                if ($factor > 0) {
                                    $stockQty = $qty * $factor;
                                }
                            }
                        } catch (\Exception $e) {}
                    }
                }

                // Update Stock (INCREMENT - goods coming back)
                $stock = WarehouseStock::where('warehouse_id', $validated['warehouse_id'])
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    // Use total_pieces as primary source of truth to avoid losing loose pieces, fallback only if zero
                    $currentTotalPieces = $stock->total_pieces;
                    if ($currentTotalPieces == 0 && $stock->quantity > 0) {
                        $currentTotalPieces = $stock->quantity * $ppb;
                    }
                    $newTotalPieces = $currentTotalPieces + $stockQty;
                    
                    $stock->total_pieces = $newTotalPieces;
                    $stock->quantity = $newTotalPieces / $ppb;
                    $stock->save();
                } else {
                    // Create new stock entry
                    WarehouseStock::create([
                        'warehouse_id' => $validated['warehouse_id'],
                        'product_id' => $productId,
                        'total_pieces' => $stockQty,
                        'quantity' => $stockQty / $ppb,
                        'price' => 0
                    ]);
                }

                // Restore serials back to available in the return warehouse if original sale had serials
                if ($sale) {
                    $origItem = $sale->items->where('product_id', $productId)->first();
                    if ($origItem && !empty($origItem->serials)) {
                        $serialsArr = is_string($origItem->serials) ? json_decode($origItem->serials, true) : (array) $origItem->serials;
                        if (is_array($serialsArr) && count($serialsArr) > 0) {
                            $toRestore = array_slice($serialsArr, 0, (int) $stockQty);
                            \App\Models\ProductSerial::where('product_id', $productId)
                                ->whereIn('serial_number', $toRestore)
                                ->update([
                                    'status' => 'available',
                                    'warehouse_id' => $validated['warehouse_id'],
                                ]);
                        }
                    }
                }

                // Stock Movement (IN - goods returned to warehouse)
                $movements[] = [
                    'product_id' => $productId,
                    'type' => 'in',
                    'qty' => $stockQty,
                    'ref_type' => 'SALE_RETURN',
                    'ref_id' => $return->id,
                    'note' => "Return #{$nextInvoice}",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $subtotal += $lineTotal;
                $totalItemDiscount += $itemDisc;
            }

            // Bulk Insert Stock Movements
            if (!empty($movements)) {
                DB::table('stock_movements')->insert($movements);
            }

            $netAmount = ($subtotal - $totalItemDiscount) - ($request->extra_discount ?? 0);

            // Handle Refund Payment (Payment Voucher)
            $totalPaid = 0;
            if (!empty($request->payment_account_id)) {
                // Calculate total refund amount submitted
                $tempTotalPaid = 0;
                foreach ($request->payment_amount as $pAmt) {
                    $tempTotalPaid += (float)$pAmt;
                }
                
                if ($tempTotalPaid > $netAmount) {
                    throw new \Exception("Refund amount cannot exceed the Net Return Amount of " . number_format($netAmount, 2));
                }

                $voucherService = app(\App\Services\VoucherService::class);
                $arId = app(\App\Services\BalanceService::class)->getAccountsReceivableId();

                foreach ($request->payment_account_id as $idx => $accId) {
                    $amt = (float) ($request->payment_amount[$idx] ?? 0);
                    if ($accId && $amt > 0) {
                        $totalPaid += $amt;
                        
                        // Create Payment Voucher via Service
                        $voucherData = [
                            'voucher_type' => \App\Models\VoucherMaster::TYPE_PAYMENT,
                            'date' => $validated['return_date'],
                            'status' => \App\Models\VoucherMaster::STATUS_POSTED,
                            'party_type' => \App\Models\Customer::class,
                            'party_id' => $customerId,
                            'remarks' => "Refund for Return #{$nextInvoice}",
                        ];

                        $lines = [
                            [
                                'account_id' => $accId, 
                                'debit' => 0,
                                'credit' => $amt,
                                'narration' => 'Cash Refund Paid'
                            ],
                            [
                                'account_id' => $arId, 
                                'debit' => $amt,
                                'credit' => 0,
                                'narration' => 'Refund to Customer'
                            ]
                        ];

                        $voucherService->createVoucher($voucherData, $lines, auth()->id());
                    }
                }
            }

            // Update Return Totals
            $return->update([
                'bill_amount' => $subtotal,
                'item_discount' => $totalItemDiscount,
                'net_amount' => $netAmount,
                'paid' => $totalPaid,
                'balance' => $netAmount - $totalPaid,
            ]);

            // Update Sale Status (only if it is a full return)
            if ($sale) {
                $totalSold = $sale->items->sum('total_pieces');
                $totalReturned = SaleReturnItem::join('sale_returns', 'sale_returns.id', '=', 'sale_return_items.sale_return_id')
                    ->where('sale_returns.sale_id', $sale->id)
                    ->sum('sale_return_items.qty');
                
                if ($totalReturned >= $totalSold) {
                    $sale->update(['sale_status' => 'returned']);
                }
            }

            // Create Journal Voucher (Credit Note)
            $transactionService = app(\App\Services\TransactionService::class);
            if (method_exists($transactionService, 'createSaleReturnVoucher')) {
                $transactionService->createSaleReturnVoucher($return);
            }

            // Update Customer Ledger (if exists)
            // Sale Return increases customer balance (they owe less or we owe them)
            $balanceChange = $netAmount - $totalPaid;

            DB::commit();

            $msg = "Sale Return #{$nextInvoice} processed successfully.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'redirect_url' => route('sale.return.view', $return->id),
                ]);
            }

            return redirect()->route('sale.return.view', $return->id)->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing return: ' . $e->getMessage());
        }
    }

    /**
     * Display all sale returns
     */
    public function saleReturnIndex()
    {
        $returns = SaleReturn::with(['customer', 'sale'])->latest()->get();
        
        // Calculate updated financial details
        $returns->each(function ($return) {
            if ($return->sale) {
                $sale = $return->sale;
                
                $return->original_net_amount = $sale->total_net;
                
                $totalReturned = SaleReturn::where('sale_id', $sale->id)
                    ->sum('net_amount');
                
                $return->new_net_amount = max(0, $sale->total_net - $totalReturned);
                $return->total_returned = $totalReturned;

                $originalDue = max(0, (float)$sale->total_net - ((float)$sale->cash + (float)$sale->card));
                $return->new_due_amount = max(0, $originalDue - $totalReturned);
            }
        });

        return view('admin_panel.sale.sale_return.index', compact('returns'));
    }

    /**
     * View a specific sale return
     */
    public function viewReturn($id)
    {
        $return = SaleReturn::with(['customer', 'sale', 'items.product'])->findOrFail($id);
        return view('admin_panel.sale.sale_return.show', compact('return'));
    }
}
