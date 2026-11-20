<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesOfficer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookerPortalController extends Controller
{
    /**
     * Helper to get currently logged in Order Booker
     */
    protected function getActiveBooker(): ?SalesOfficer
    {
        $user = Auth::user();
        if (!$user) return null;

        $officer = $user->salesOfficer ?? SalesOfficer::where('user_id', $user->id)->first();

        // Fallback for admin previewing portal: pick first active booker
        if (!$officer && ($user->usertype === 'admin' || $user->isSuperAdmin())) {
            $officer = SalesOfficer::bookers()->active()->first();
        }

        return $officer;
    }

    /**
     * Booker Portal Dashboard
     */
    public function dashboard(Request $request)
    {
        $booker = $this->getActiveBooker();
        if (!$booker) {
            return redirect()->route('dashboard')->with('error', 'No Order Booker profile linked to your account.');
        }

        $today = today();
        $startOfMonth = now()->startOfMonth();

        // Booker Stats
        $todayOrders = Sale::where('order_booker_id', $booker->id)
            ->whereDate('created_at', $today)
            ->get();

        $monthOrders = Sale::where('order_booker_id', $booker->id)
            ->where('created_at', '>=', $startOfMonth)
            ->get();

        $stats = [
            'today_orders_count' => $todayOrders->count(),
            'today_orders_val'   => (float)$todayOrders->sum('total_net'),
            'month_orders_count' => $monthOrders->count(),
            'month_orders_val'   => (float)$monthOrders->sum('total_net'),
            'pending_count'      => Sale::where('order_booker_id', $booker->id)->where('is_booking', 1)->where('sale_status', 'booked')->count(),
            'confirmed_count'    => Sale::where('order_booker_id', $booker->id)->where('sale_status', 'posted')->count(),
            'total_shops'        => Customer::where('sales_officer_id', $booker->id)->count(),
        ];

        // Recent 10 bookings
        $recentBookings = Sale::with(['customer_relation', 'items'])
            ->where('order_booker_id', $booker->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('booker_portal.dashboard', compact('booker', 'stats', 'recentBookings'));
    }

    /**
     * Show New Order Booking screen
     */
    public function createOrder(Request $request)
    {
        $booker = $this->getActiveBooker();
        if (!$booker) {
            return redirect()->route('dashboard')->with('error', 'Booker profile required.');
        }

        // Active customers (prioritize assigned ones)
        $customers = Customer::where('status', 1)
            ->select('id', 'customer_name', 'customer_id', 'contact_person', 'mobile', 'area', 'city', 'address', 'sales_officer_id')
            ->orderByRaw("sales_officer_id = {$booker->id} DESC")
            ->orderBy('customer_name')
            ->get();

        // Active products with stock & tax info
        $products = Product::where('is_active', 1)
            ->with(['unit', 'taxType'])
            ->withSum('warehouseStocks', 'total_pieces')
            ->select('id', 'item_code', 'item_name', 'trade_price', 'retail_price', 'purchase_price_per_piece', 'sale_price_per_piece', 'size_mode', 'pieces_per_box', 'tax_type_id', 'is_third_schedule', 'unit_id')
            ->orderBy('item_name')
            ->get()
            ->map(function ($p) {
                $tax = $p->taxType;
                $is3rd = $tax ? $tax->isThirdSchedule() : (bool)$p->is_third_schedule;
                $p->gst_rate = $tax ? $tax->getSaleGstRate() : 18.0;
                $p->is_3rd = $is3rd ? 1 : 0;
                $p->ppb = $p->pieces_per_box > 0 ? (int)$p->pieces_per_box : 1;
                $p->trade_rate = (float)($p->trade_price ?: $p->purchase_price_per_piece);
                $p->retail_rate = (float)($p->retail_price ?: $p->sale_price_per_piece);
                return $p;
            });

        $nextBookingNo = 'BK-' . date('ymd') . '-' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);

        return view('booker_portal.create_order', compact('booker', 'customers', 'products', 'nextBookingNo'));
    }

    /**
     * Store new order booking from Booker Portal
     */
    public function storeOrder(Request $request)
    {
        $booker = $this->getActiveBooker();
        if (!$booker) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or no booker linked.'], 403);
        }

        $validated = $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'booking_date'      => 'nullable|date',
            'delivery_date'     => 'nullable|date',
            'notes'             => 'nullable|string|max:500',
            'product_id'        => 'required|array|min:1',
            'product_id.*'      => 'required|exists:products,id',
            'qty_carton'        => 'nullable|array',
            'qty_loose'         => 'nullable|array',
            'price'             => 'required|array',
            'discount_percent'  => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $customer = Customer::findOrFail($validated['customer_id']);

            $totalGross = 0;
            $totalExclusiveGst = 0;
            $totalSalesTax = 0;
            $totalFurtherTax = 0;
            $totalDiscount = 0;
            $totalNet = 0;
            $totalItems = 0;

            $sale = new Sale();
            $sale->branch_id = auth()->user()->branch_id ?? 1;
            $sale->invoice_no = 'BK-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -5));
            $sale->customer_id = $customer->id;
            $sale->sales_officer_id = $booker->id;
            $sale->order_booker_id = $booker->id;
            $sale->is_booking = 1;
            $sale->sale_status = 'booked';
            $sale->source = 'order_booker_portal';
            $sale->reference = $validated['notes'] ?? ('Booked by ' . $booker->name . ' (' . $booker->code . ')');
            $sale->created_at = !empty($validated['booking_date']) ? \Carbon\Carbon::parse($validated['booking_date']) : now();
            $sale->due_date = !empty($validated['delivery_date']) ? \Carbon\Carbon::parse($validated['delivery_date']) : now()->addDays(1);
            $sale->save();

            $pids = $validated['product_id'];
            foreach ($pids as $idx => $pid) {
                $product = Product::with(['unit', 'taxType'])->find($pid);
                if (!$product) continue;

                $ppb = $product->pieces_per_box > 0 ? (int)$product->pieces_per_box : 1;
                $cartons = (float)($request->qty_carton[$idx] ?? 0);
                $loose = (float)($request->qty_loose[$idx] ?? 0);

                if ($cartons <= 0 && $loose <= 0) {
                    continue; // Skip zero qty
                }

                $totalPieces = ($cartons * $ppb) + $loose;
                $unitPrice = (float)($request->price[$idx] ?? ($product->trade_price ?: $product->purchase_price_per_piece));
                $discPct = (float)($request->discount_percent[$idx] ?? 0);

                // Line Gross
                if (in_array($product->size_mode, ['by_cartons', 'by_bandal']) && $ppb > 1) {
                    $piecePrice = $unitPrice / $ppb;
                    $lineGross = ($cartons * $unitPrice) + ($loose * $piecePrice);
                } else {
                    $lineGross = $totalPieces * $unitPrice;
                }

                $discAmt = round(($lineGross * $discPct) / 100, 2);
                $exclGst = max(0, round($lineGross - $discAmt, 2));

                // Tax Calculation
                $taxType = $product->taxType;
                $is3rd = $taxType ? $taxType->isThirdSchedule() : (bool)$product->is_third_schedule;
                $gstRate = $taxType ? $taxType->getSaleGstRate() : 18.0;

                $retailPrice = (float)($product->retail_price ?: $product->sale_price_per_piece);
                $mrpAmount = round($retailPrice * $totalPieces, 2);

                $gstBase = ($is3rd && $mrpAmount > 0) ? $mrpAmount : $exclGst;
                $salesTaxAmt = round($gstBase * ($gstRate / 100), 2);
                $furtherTaxAmt = 0; // 0 for booking stage / 3rd sched

                $lineTotal = round($exclGst + $salesTaxAmt, 2);

                // Save Item
                $item = new SaleItem();
                $item->sale_id = $sale->id;
                $item->product_id = $product->id;
                $item->product_name = $product->item_name;
                $item->size_mode = $product->size_mode;
                $item->unit_id = $product->unit_id;
                $item->qty = ($cartons > 0 && $loose > 0) ? "{$cartons}.{$loose}" : ($cartons > 0 ? (string)$cartons : (string)$loose);
                $item->total_pieces = $totalPieces;
                $item->loose_pieces = $loose;
                $item->price = $unitPrice;
                $item->price_per_piece = $ppb > 0 ? round($unitPrice / $ppb, 2) : $unitPrice;
                $item->retail_price = $retailPrice;
                $item->gross_amount = $lineGross;
                $item->discount_percent = $discPct;
                $item->discount_amount = $discAmt;
                $item->exclusive_gst_amount = $exclGst;
                $item->mrp_amount = $mrpAmount;
                $item->sales_tax_percent = $gstRate;
                $item->sales_tax_amount = $salesTaxAmt;
                $item->further_tax_percent = 0;
                $item->further_tax_amount = 0;
                $item->total = $lineTotal;
                $item->save();

                $totalGross += $lineGross;
                $totalDiscount += $discAmt;
                $totalExclusiveGst += $exclGst;
                $totalSalesTax += $salesTaxAmt;
                $totalNet += $lineTotal;
                $totalItems++;
            }

            if ($totalItems === 0) {
                throw new \Exception('Please add at least one product with quantity greater than zero.');
            }

            $sale->total_items = $totalItems;
            $sale->total_gross = $totalGross;
            $sale->total_reg_discount = $totalDiscount;
            $sale->total_exclusive_gst = $totalExclusiveGst;
            $sale->total_sales_tax = $totalSalesTax;
            $sale->total_further_tax = 0;
            $sale->total_net = $totalNet;
            $sale->total_bill_amount = $totalNet;
            $sale->save();

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order booked successfully! Booking Ref: ' . $sale->invoice_no,
                    'booking_id' => $sale->id,
                    'redirect_url' => route('booker.orders.show', $sale->id),
                ]);
            }

            return redirect()->route('booker.orders.show', $sale->id)->with('success', 'Order booked successfully! Ref: ' . $sale->invoice_no);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Orders listing for booker
     */
    public function orders(Request $request)
    {
        $booker = $this->getActiveBooker();
        if (!$booker) {
            return redirect()->route('dashboard')->with('error', 'Booker profile required.');
        }

        $query = Sale::with(['customer_relation', 'items'])
            ->where('order_booker_id', $booker->id)
            ->latest();

        // Filters
        $status = $request->get('status', 'all');
        if ($status === 'pending' || $status === 'booked') {
            $query->where('is_booking', 1)->where('sale_status', 'booked');
        } elseif ($status === 'confirmed' || $status === 'posted') {
            $query->where('sale_status', 'posted');
        }

        $time = $request->get('time', 'all');
        if ($time === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($time === 'yesterday') {
            $query->whereDate('created_at', today()->subDay());
        } elseif ($time === 'this_month') {
            $query->where('created_at', '>=', now()->startOfMonth());
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('invoice_no', 'like', "%{$q}%")
                    ->orWhereHas('customer_relation', function ($cq) use ($q) {
                        $cq->where('customer_name', 'like', "%{$q}%")
                            ->orWhere('mobile', 'like', "%{$q}%")
                            ->orWhere('area', 'like', "%{$q}%");
                    });
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('booker_portal.orders', compact('booker', 'orders', 'status', 'time'));
    }

    /**
     * View Booking Slip / Detail
     */
    public function showOrder($id)
    {
        $booker = $this->getActiveBooker();
        $user = Auth::user();

        $query = Sale::with(['customer_relation', 'items.product.unit', 'orderBooker', 'salesman']);
        
        // If not an admin user, restrict to the booker's own orders
        if ($booker && !($user && ($user->usertype === 'admin' || $user->isSuperAdmin()))) {
            $query->where('order_booker_id', $booker->id);
        }

        $order = $query->findOrFail($id);

        if (!$booker) {
            $booker = $order->orderBooker ?? $order->salesOfficer;
        }

        return view('booker_portal.order_detail', compact('booker', 'order'));
    }

    /**
     * Shops / Retailers list for booker
     */
    public function shops(Request $request)
    {
        $booker = $this->getActiveBooker();
        if (!$booker) {
            return redirect()->route('dashboard');
        }

        $query = Customer::where('status', 1);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('customer_name', 'like', "%{$q}%")
                    ->orWhere('mobile', 'like', "%{$q}%")
                    ->orWhere('area', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%");
            });
        }

        $shops = $query->orderByRaw("sales_officer_id = {$booker->id} DESC")
            ->orderBy('customer_name')
            ->paginate(30);

        return view('booker_portal.shops', compact('booker', 'shops'));
    }

    /**
     * Quick register a shop from the field
     */
    public function quickStoreShop(Request $request)
    {
        $booker = $this->getActiveBooker();
        if (!$booker) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'mobile'         => 'required|string|max:50',
            'area'           => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:500',
        ]);

        $nextId = Customer::max('id') + 1;
        $customerCode = 'CUST-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $customer = Customer::create([
            'customer_id'       => $customerCode,
            'customer_name'     => $validated['customer_name'],
            'contact_person'    => $validated['contact_person'] ?? null,
            'mobile'            => $validated['mobile'],
            'area'              => $validated['area'] ?? null,
            'city'              => $validated['city'] ?? 'Default',
            'address'           => $validated['address'] ?? null,
            'sales_officer_id'  => $booker->id,
            'status'            => 1,
            'filer_type'        => 'non-filer',
            'party_type'        => 'customer',
        ]);

        return response()->json([
            'success'  => true,
            'message'  => "Shop '{$customer->customer_name}' added successfully!",
            'customer' => $customer,
        ]);
    }
}
