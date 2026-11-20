<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductBooking;
use App\Models\Sale;
use App\Models\SalesOfficer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBookingController extends Controller
{
    /**
     * Display a comprehensive listing of market bookings.
     * Supports filtering by Booker, Status, Date presets/ranges, and Search.
     */
    public function index(Request $request)
    {
        $bookerId   = $request->get('booker_id', 'all');
        $status     = $request->get('status', 'all');
        $dateFilter = $request->get('date_filter', 'all');
        $startDate  = $request->get('start_date');
        $endDate    = $request->get('end_date');
        $search     = $request->get('q', '');

        $query = Sale::with(['customer_relation', 'items.product.unit', 'orderBooker', 'salesOfficer', 'salesman'])
            ->where('is_booking', 1);

        // Booker Filter
        if ($bookerId && $bookerId !== 'all') {
            $query->where(function($q) use ($bookerId) {
                $q->where('order_booker_id', $bookerId)
                  ->orWhere('sales_officer_id', $bookerId);
            });
        }

        // Status Filter
        if ($status === 'booked' || $status === 'pending') {
            $query->where('sale_status', 'booked');
        } elseif ($status === 'posted' || $status === 'confirmed') {
            $query->where('sale_status', 'posted');
        }

        // Date Filter
        if ($dateFilter === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($dateFilter === 'yesterday') {
            $query->whereDate('created_at', today()->subDay());
        } elseif ($dateFilter === 'this_week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($dateFilter === 'this_month') {
            $query->where('created_at', '>=', now()->startOfMonth());
        } elseif ($dateFilter === 'custom') {
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            } elseif ($startDate) {
                $query->where('created_at', '>=', $startDate . ' 00:00:00');
            } elseif ($endDate) {
                $query->where('created_at', '<=', $endDate . ' 23:59:59');
            }
        }

        // Search Filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('customer_relation', function($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%")
                         ->orWhere('mobile', 'like', "%{$search}%")
                         ->orWhere('area', 'like', "%{$search}%");
                  })
                  ->orWhereHas('orderBooker', function($bq) use ($search) {
                      $bq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->orderBy('id', 'desc')->get();

        // 1. KPI Stats (Realtime summary for cards)
        $todayDate = today();
        $stats = [
            'today_count'      => Sale::where('is_booking', 1)->whereDate('created_at', $todayDate)->count(),
            'today_val'        => (float)Sale::where('is_booking', 1)->whereDate('created_at', $todayDate)->sum('total_net'),
            'pending_count'    => Sale::where('is_booking', 1)->where('sale_status', 'booked')->count(),
            'pending_val'      => (float)Sale::where('is_booking', 1)->where('sale_status', 'booked')->sum('total_net'),
            'confirmed_count'  => Sale::where('is_booking', 1)->where('sale_status', 'posted')->count(),
            'confirmed_val'    => (float)Sale::where('is_booking', 1)->where('sale_status', 'posted')->sum('total_net'),
            'filtered_count'   => $bookings->count(),
            'filtered_val'     => (float)$bookings->sum('total_net'),
        ];

        // 2. All Active Order Bookers for filter dropdown
        $allBookers = SalesOfficer::bookers()->active()->orderBy('name')->get();

        // 3. Booker Breakdown ("Kis Kis ne kya kya book kara")
        $bookerStatsRaw = Sale::where('is_booking', 1)
            ->when($dateFilter === 'today', fn($q) => $q->whereDate('created_at', today()))
            ->when($dateFilter === 'yesterday', fn($q) => $q->whereDate('created_at', today()->subDay()))
            ->when($dateFilter === 'this_week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($dateFilter === 'this_month', fn($q) => $q->where('created_at', '>=', now()->startOfMonth()))
            ->when($dateFilter === 'custom' && $startDate, fn($q) => $q->where('created_at', '>=', $startDate . ' 00:00:00'))
            ->when($dateFilter === 'custom' && $endDate, fn($q) => $q->where('created_at', '<=', $endDate . ' 23:59:59'))
            ->select(
                'order_booker_id',
                DB::raw('COUNT(id) as total_orders'),
                DB::raw('SUM(total_net) as total_value'),
                DB::raw('SUM(CASE WHEN sale_status = "booked" THEN 1 ELSE 0 END) as pending_orders'),
                DB::raw('SUM(CASE WHEN sale_status = "posted" THEN 1 ELSE 0 END) as confirmed_orders')
            )
            ->whereNotNull('order_booker_id')
            ->groupBy('order_booker_id')
            ->get()
            ->keyBy('order_booker_id');

        $bookerBreakdown = [];
        foreach ($allBookers as $bk) {
            $raw = $bookerStatsRaw->get($bk->id);
            if ($raw && $raw->total_orders > 0) {
                $bookerBreakdown[] = [
                    'id'               => $bk->id,
                    'name'             => $bk->name,
                    'code'             => $bk->code,
                    'mobile'           => $bk->mobile,
                    'total_orders'     => (int)$raw->total_orders,
                    'total_value'      => (float)$raw->total_value,
                    'pending_orders'   => (int)$raw->pending_orders,
                    'confirmed_orders' => (int)$raw->confirmed_orders,
                ];
            }
        }

        $filters = compact('bookerId', 'status', 'dateFilter', 'startDate', 'endDate', 'search');

        return view('admin_panel.booking.index', compact('bookings', 'stats', 'allBookers', 'bookerBreakdown', 'filters'));
    }

    /**
     * AJAX endpoint to return full itemized details of a booking
     * Used for the "View Items / Slip" modal.
     */
    public function getBookingDetails($id)
    {
        $sale = Sale::with(['customer_relation', 'items.product.unit', 'orderBooker', 'salesOfficer', 'salesman'])->findOrFail($id);

        $items = [];
        $totalCartons = 0;
        $totalLoose = 0;
        $totalPieces = 0;

        foreach ($sale->items as $item) {
            $product = $item->product;
            $ppb = ($product && $product->pieces_per_box > 0) ? (int)$product->pieces_per_box : 1;
            
            $cartons = 0;
            $loose = 0;
            if ($item->total_pieces > 0 && $ppb > 1) {
                $cartons = (int)floor($item->total_pieces / $ppb);
                $loose = (int)($item->total_pieces % $ppb);
            } elseif ($item->qty) {
                if (str_contains((string)$item->qty, '.')) {
                    $parts = explode('.', (string)$item->qty);
                    $cartons = (int)$parts[0];
                    $loose = (int)$parts[1];
                } else {
                    $cartons = (int)$item->qty;
                }
            }

            $lineTotalPieces = $item->total_pieces ?: (($cartons * $ppb) + $loose);
            $totalCartons += $cartons;
            $totalLoose += $loose;
            $totalPieces += $lineTotalPieces;

            $items[] = [
                'id'               => $item->id,
                'product_id'       => $item->product_id,
                'product_name'     => $item->product_name ?: ($product->item_name ?? 'Product #' . $item->product_id),
                'item_code'        => $product->item_code ?? '—',
                'cartons'          => $cartons,
                'loose'            => $loose,
                'total_pieces'     => $lineTotalPieces,
                'ppb'              => $ppb,
                'unit'             => $product->unit->name ?? 'Pcs',
                'price'            => (float)$item->price,
                'discount_percent' => (float)$item->discount_percent,
                'discount_amount'  => (float)$item->discount_amount,
                'sales_tax_percent'=> (float)$item->sales_tax_percent,
                'sales_tax_amount' => (float)$item->sales_tax_amount,
                'gross_amount'     => (float)($item->gross_amount ?: ($item->price * ($lineTotalPieces ?: 1))),
                'total'            => (float)$item->total,
            ];
        }

        $booker = $sale->orderBooker ?? $sale->salesOfficer;

        return response()->json([
            'success' => true,
            'booking' => [
                'id'                   => $sale->id,
                'invoice_no'           => $sale->invoice_no,
                'booking_date'         => $sale->created_at ? $sale->created_at->format('d/m/Y h:i A') : '—',
                'delivery_date'        => $sale->due_date ? \Carbon\Carbon::parse($sale->due_date)->format('d/m/Y') : '—',
                'sale_status'          => $sale->sale_status,
                'reference'            => $sale->reference,
                'source'               => $sale->source,
                'total_items'          => count($items),
                'total_cartons'        => $totalCartons,
                'total_loose'          => $totalLoose,
                'total_pieces'         => $totalPieces,
                'total_gross'          => (float)$sale->total_gross,
                'total_discount'       => (float)($sale->total_reg_discount ?: $sale->total_discount),
                'total_sales_tax'      => (float)$sale->total_sales_tax,
                'total_net'            => (float)$sale->total_net,
                'booker' => [
                    'id'     => $booker->id ?? null,
                    'name'   => $booker->name ?? 'Direct / Admin',
                    'code'   => $booker->code ?? '',
                    'mobile' => $booker->mobile ?? '',
                ],
                'salesman' => [
                    'name' => $sale->salesman->name ?? 'Unassigned',
                ],
                'customer' => [
                    'id'      => $sale->customer_relation->id ?? null,
                    'name'    => $sale->customer_relation->customer_name ?? 'Walking Retailer',
                    'code'    => $sale->customer_relation->customer_id ?? '',
                    'mobile'  => $sale->customer_relation->mobile ?? '—',
                    'area'    => $sale->customer_relation->area ?? '',
                    'city'    => $sale->customer_relation->city ?? '',
                    'address' => $sale->customer_relation->address ?? '',
                ],
                'actions' => [
                    'confirm_url' => route('sales.confirm', $sale->id),
                    'edit_url'    => route('sales.edit', $sale->id),
                    'invoice_url' => route('sales.invoice', $sale->id),
                    'slip_url'    => route('booker.orders.show', $sale->id),
                ]
            ],
            'items' => $items,
        ]);
    }

    public function receipt($id)
    {
        $sale = Sale::with(['customer_relation', 'items.product.unit', 'orderBooker', 'salesOfficer'])->where('is_booking', 1)->find($id);
        if ($sale) {
            $booker = $sale->orderBooker ?? $sale->salesOfficer;
            return view('booker_portal.order_detail', ['order' => $sale, 'booker' => $booker]);
        }

        $booking = ProductBooking::with('customer_relation')->findOrFail($id);
        return view('admin_panel.booking.receipt', compact('booking'));
    }

    public function create()
    {
        $products = Product::get();
        $Customer = Customer::get();

        return view('admin_panel.booking.create', compact('products', 'Customer'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $product_ids = $request->product_id;
            $product_names = $request->product_id;
            $product_codes = $request->item_code;
            $brands = $request->uom;
            $units = $request->unit;
            $prices = $request->price;
            $discounts = $request->item_disc;
            $quantities = $request->qty;
            $totals = $request->total;
            $colors = $request->color;

            $combined_products = [];
            $combined_codes = [];
            $combined_brands = [];
            $combined_units = [];
            $combined_prices = [];
            $combined_discounts = [];
            $combined_qtys = [];
            $combined_totals = [];
            $combined_colors = [];

            $total_items = 0;

            foreach ($product_ids as $index => $product_id) {
                $qty = $quantities[$index] ?? 0;
                $price = $prices[$index] ?? 0;

                if (! $product_id || ! $qty || ! $price) {
                    continue;
                }

                $combined_products[] = $product_names[$index] ?? '';
                $combined_codes[] = $product_codes[$index] ?? '';
                $combined_brands[] = $brands[$index] ?? '';
                $combined_units[] = $units[$index] ?? '';
                $combined_prices[] = $prices[$index] ?? 0;
                $combined_discounts[] = $discounts[$index] ?? 0;
                $combined_qtys[] = $quantities[$index] ?? 0;
                $combined_totals[] = $totals[$index] ?? 0;

                $rowColors = $colors[$index] ?? [];
                $combined_colors[] = json_encode($rowColors);

                $total_items += $qty;
            }

            $booking = new ProductBooking;
            $booking->customer = $request->customer;
            $booking->reference = $request->reference;
            $booking->product = implode(',', $combined_products);
            $booking->product_code = implode(',', $combined_codes);
            $booking->brand = implode(',', $combined_brands);
            $booking->unit = implode(',', $combined_units);
            $booking->per_price = implode(',', $combined_prices);
            $booking->per_discount = implode(',', $combined_discounts);
            $booking->qty = implode(',', $combined_qtys);
            $booking->per_total = implode(',', $combined_totals);
            $booking->color = json_encode($combined_colors);

            $booking->total_amount_Words = $request->total_amount_Words;
            $booking->total_bill_amount = $request->total_subtotal;
            $booking->total_extradiscount = $request->total_extra_cost;
            $booking->total_net = $request->total_net;

            $booking->cash = $request->cash;
            $booking->card = $request->card;
            $booking->change = $request->change;
            $booking->total_items = $total_items;
            $booking->save();

            DB::commit();

            return back()->with('success', 'Product booking saved (no stock reduced).');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
}
