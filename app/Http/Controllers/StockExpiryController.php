<?php

namespace App\Http\Controllers;

use App\Models\ProductBatch;
use App\Models\Warehouse;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StockExpiryController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $thresholdDays = (int) $request->get('threshold', 30);
        $nearThresholdDate = $today->copy()->addDays($thresholdDays);

        $statusFilter = $request->get('status', 'all'); // 'all', 'expired', 'near_expiry', 'good_expiry'
        $warehouseId  = $request->get('warehouse_id');
        $search       = $request->get('search');

        $query = ProductBatch::with(['product.unit', 'warehouse'])
            ->whereNotNull('expiry_date');

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('batch_no', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('item_name', 'like', "%{$search}%")
                        ->orWhere('item_code', 'like', "%{$search}%");
                  });
            });
        }

        // Stats calculation query across all non-deleted batches
        $allBatches = ProductBatch::with(['product', 'warehouse'])
            ->whereNotNull('expiry_date')
            ->get();

        $expiredCount   = 0;
        $expiredValue   = 0;
        $expiredQty     = 0;

        $nearCount      = 0;
        $nearValue      = 0;
        $nearQty        = 0;

        $goodCount      = 0;
        $goodValue      = 0;
        $goodQty        = 0;

        foreach ($allBatches as $b) {
            $exp = Carbon::parse($b->expiry_date)->startOfDay();
            $lineValue = (float) $b->qty * (float) $b->cost_price;

            if ($exp->lte($today)) {
                $expiredCount++;
                $expiredValue += $lineValue;
                $expiredQty   += $b->qty;
            } elseif ($exp->between($today->copy()->addDay(), $nearThresholdDate)) {
                $nearCount++;
                $nearValue += $lineValue;
                $nearQty   += $b->qty;
            } else {
                $goodCount++;
                $goodValue += $lineValue;
                $goodQty   += $b->qty;
            }
        }

        // Apply status filter to display query
        if ($statusFilter === 'expired') {
            $query->whereDate('expiry_date', '<=', $today);
        } elseif ($statusFilter === 'near_expiry') {
            $query->whereDate('expiry_date', '>', $today)
                  ->whereDate('expiry_date', '<=', $nearThresholdDate);
        } elseif ($statusFilter === 'good_expiry') {
            $query->whereDate('expiry_date', '>', $nearThresholdDate);
        }

        $batches = $query->orderBy('expiry_date', 'asc')->paginate(20);

        // Attach calculated helper properties to each batch
        $batches->getCollection()->transform(function ($b) use ($today, $nearThresholdDate) {
            $exp = Carbon::parse($b->expiry_date)->startOfDay();
            $b->days_diff = (int) $today->diffInDays($exp, false);

            if ($exp->lte($today)) {
                $b->expiry_status = 'expired'; // 🔴
                $b->status_label  = 'EXPIRED';
                $b->status_badge  = 'danger';
            } elseif ($exp->between($today->copy()->addDay(), $nearThresholdDate)) {
                $b->expiry_status = 'near_expiry'; // 🟡
                $b->status_label  = 'NEAR EXPIRY';
                $b->status_badge  = 'warning';
            } else {
                $b->expiry_status = 'good_expiry'; // 🟢
                $b->status_label  = 'GOOD / LONG EXPIRY';
                $b->status_badge  = 'success';
            }

            return $b;
        });

        $warehouses = Warehouse::all();

        // System Notifications trigger for expired/near-expiry items
        $this->syncExpiryNotifications($today, $nearThresholdDate);

        return view('admin_panel.stock_expiry.index', compact(
            'batches',
            'warehouses',
            'thresholdDays',
            'statusFilter',
            'warehouseId',
            'search',
            'expiredCount',
            'expiredValue',
            'expiredQty',
            'nearCount',
            'nearValue',
            'nearQty',
            'goodCount',
            'goodValue',
            'goodQty'
        ));
    }

    /**
     * Create/update system notifications for expired & near expiry products
     */
    private function syncExpiryNotifications($today, $nearThresholdDate)
    {
        try {
            $targetUsers = \App\Models\User::pluck('id')->toArray();
            if (empty($targetUsers)) return;

            $expiredBatches = ProductBatch::with('product')
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '<=', $today)
                ->limit(5)
                ->get();

            foreach ($expiredBatches as $eb) {
                $pName = $eb->product->item_name ?? 'Product #'.$eb->product_id;
                $exists = SystemNotification::where('source_type', 'App\Models\ProductBatch')
                    ->where('source_id', $eb->id)
                    ->where('type', 'batch_expired')
                    ->where('is_read', false)
                    ->exists();

                if (!$exists) {
                    $data = [
                        'title' => '🔴 Product Batch Expired!',
                        'message' => "Batch '{$eb->batch_no}' of '{$pName}' expired on {$eb->expiry_date}. Qty: {$eb->qty}",
                        'type' => 'batch_expired',
                        'source_id' => $eb->id,
                        'source_type' => 'App\Models\ProductBatch',
                        'action_url' => route('stock_expiry.index', ['status' => 'expired']),
                        'is_read' => false,
                    ];
                    SystemNotification::createForUsers($targetUsers, $data);
                }
            }

            $nearBatches = ProductBatch::with('product')
                ->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '>', $today)
                ->whereDate('expiry_date', '<=', $nearThresholdDate)
                ->limit(5)
                ->get();

            foreach ($nearBatches as $nb) {
                $pName = $nb->product->item_name ?? 'Product #'.$nb->product_id;
                $exists = SystemNotification::where('source_type', 'App\Models\ProductBatch')
                    ->where('source_id', $nb->id)
                    ->where('type', 'batch_near_expiry')
                    ->where('is_read', false)
                    ->exists();

                if (!$exists) {
                    $days = $today->diffInDays(Carbon::parse($nb->expiry_date), false);
                    $data = [
                        'title' => '🟡 Product Batch Near Expiry',
                        'message' => "Batch '{$nb->batch_no}' of '{$pName}' will expire in {$days} days ({$nb->expiry_date}). Qty: {$nb->qty}",
                        'type' => 'batch_near_expiry',
                        'source_id' => $nb->id,
                        'source_type' => 'App\Models\ProductBatch',
                        'action_url' => route('stock_expiry.index', ['status' => 'near_expiry']),
                        'is_read' => false,
                    ];
                    SystemNotification::createForUsers($targetUsers, $data);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Expiry Notifications Sync Error: '.$e->getMessage());
        }
    }
}
