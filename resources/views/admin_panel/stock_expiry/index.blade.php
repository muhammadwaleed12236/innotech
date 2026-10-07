@extends('admin_panel.layout.app')

@section('title', 'Stock Expiry Management & Alerts')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/fonts/inter/inter.css') }}">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --exp-expired-red: #ef4444;
        --exp-expired-bg: #fef2f2;
        --exp-near-amber: #f59e0b;
        --exp-near-bg: #fffbeb;
        --exp-good-green: #10b981;
        --exp-good-bg: #ecfdf5;
        --exp-slate-900: #0f172a;
        --exp-slate-800: #1e293b;
        --exp-slate-700: #334155;
        --exp-border: #cbd5e1;
        --exp-radius: 16px;
        --exp-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.05), 0 4px 6px -2px rgba(15, 23, 42, 0.02);
    }

    body {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        background-color: #f8fafc;
        color: var(--exp-slate-800);
    }

    .exp-container {
        padding: 10px 20px 40px 20px;
        max-width: 1560px;
        margin: 0 auto;
    }

    /* --- Hero Header --- */
    .exp-hero-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #450a0a 100%);
        border-radius: var(--exp-radius);
        padding: 26px 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.3);
        margin-bottom: 24px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .exp-hero-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(239, 68, 68, 0.25) 0%, rgba(0, 0, 0, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .exp-hero-title {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0;
    }

    .exp-hero-badge {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid rgba(248, 113, 113, 0.4);
        color: #fca5a5;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 8px;
    }

    /* --- Notification Top Banner --- */
    .exp-alert-banner {
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .exp-alert-banner-danger {
        background: linear-gradient(90deg, #fef2f2 0%, #ffe4e6 100%);
        border: 1.5px solid #fecdd3;
        color: #9f1239;
    }

    .exp-alert-banner-warning {
        background: linear-gradient(90deg, #fffbeb 0%, #fef3c7 100%);
        border: 1.5px solid #fde68a;
        color: #92400e;
    }

    /* --- Stat Cards Grid --- */
    .exp-stat-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px;
        border: 1.5px solid var(--exp-border);
        box-shadow: var(--exp-shadow);
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .exp-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px -5px rgba(15, 23, 42, 0.08);
    }

    .exp-stat-card-red {
        border-left: 5px solid var(--exp-expired-red);
    }
    .exp-stat-card-amber {
        border-left: 5px solid var(--exp-near-amber);
    }
    .exp-stat-card-green {
        border-left: 5px solid var(--exp-good-green);
    }

    .exp-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 18px;
    }

    .exp-stat-val {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .exp-stat-lbl {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--exp-slate-700);
    }

    /* --- Filter & Tabs Bar --- */
    .exp-filter-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1.5px solid var(--exp-border);
        box-shadow: var(--exp-shadow);
        padding: 18px 24px;
        margin-bottom: 24px;
    }

    .status-tab-btn {
        padding: 8px 18px;
        border-radius: 30px;
        font-size: 0.84rem;
        font-weight: 700;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        text-decoration: none;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-tab-btn:hover {
        border-color: #94a3b8;
        color: #0f172a;
    }

    .status-tab-btn.active-all {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }

    .status-tab-btn.active-expired {
        background: var(--exp-expired-red);
        color: #ffffff;
        border-color: var(--exp-expired-red);
    }

    .status-tab-btn.active-near {
        background: var(--exp-near-amber);
        color: #ffffff;
        border-color: var(--exp-near-amber);
    }

    .status-tab-btn.active-good {
        background: var(--exp-good-green);
        color: #ffffff;
        border-color: var(--exp-good-green);
    }

    /* --- Batch Expiry Data Table --- */
    .exp-table-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1.5px solid var(--exp-border);
        box-shadow: var(--exp-shadow);
        overflow: hidden;
    }

    .exp-table th {
        background: #f8fafc;
        color: var(--exp-slate-700);
        font-size: 0.74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 14px 16px;
        border-bottom: 1.5px solid var(--exp-border);
        white-space: nowrap;
    }

    .exp-table td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .exp-table tr:hover td {
        background: #f8fafc;
    }

    .expiry-badge {
        font-size: 0.75rem;
        font-weight: 800;
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .expiry-badge-expired {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fca5a5;
    }

    .expiry-badge-near {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fcd34d;
    }

    .expiry-badge-good {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #6ee7b7;
    }
</style>

<div class="exp-container">

    {{-- Hero Header --}}
    <div class="exp-hero-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 2;">
            <div>
                <span class="exp-hero-badge"><i class="fas fa-shield-heart me-1"></i> INVENTORY QUALITY CONTROL</span>
                <h2 class="exp-hero-title">Stock Expiry Management & Alerts</h2>
                <p class="text-white-50 mb-0 mt-1" style="font-size: 0.92rem; max-width: 780px;">
                    Monitor expired items 🔴, near-expiry batches 🟡 (within {{ $thresholdDays }} days), and safe long-expiry stock 🟢. Prevent loss with real-time stock alert tracking.
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('opening_stock.index') }}" class="btn btn-outline-light px-3 py-2 fw-semibold rounded-3" style="font-size: 0.88rem;">
                    <i class="fas fa-boxes-packing me-1"></i> Opening Stock Terminal
                </a>
                <a href="{{ route('stock_adjustments.index') }}" class="btn btn-light text-dark px-4 py-2 fw-bold rounded-3 shadow-sm" style="font-size: 0.88rem;">
                    <i class="fas fa-sliders-h text-primary me-1"></i> Stock Adjustments
                </a>
            </div>
        </div>
    </div>

    {{-- Dynamic Notification Alert Banner --}}
    @if($expiredCount > 0)
        <div class="exp-alert-banner exp-alert-banner-danger">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger text-white d-grid place-items-center" style="width: 40px; height: 40px; font-size: 18px; flex-shrink: 0;">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-danger" style="font-size: 0.95rem;">CRITICAL STOCK ALERT: {{ $expiredCount }} EXPIRED BATCH(ES) DETECTED!</h6>
                    <p class="mb-0 small text-danger text-opacity-80">
                        Total <b>{{ number_format($expiredQty) }} Units</b> worth <b>PKR {{ number_format($expiredValue, 2) }}</b> have passed their expiration date. Please review for stock adjustment or disposal.
                    </p>
                </div>
            </div>
            <a href="{{ route('stock_expiry.index', ['status' => 'expired']) }}" class="btn btn-danger btn-sm fw-bold px-3 py-2 text-nowrap rounded-pill">
                View Expired Batches
            </a>
        </div>
    @elseif($nearCount > 0)
        <div class="exp-alert-banner exp-alert-banner-warning">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning text-dark d-grid place-items-center" style="width: 40px; height: 40px; font-size: 18px; flex-shrink: 0;">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">NEAR EXPIRY WARNING: {{ $nearCount }} BATCH(ES) EXPIRING SOON!</h6>
                    <p class="mb-0 small text-dark text-opacity-75">
                        Total <b>{{ number_format($nearQty) }} Units</b> worth <b>PKR {{ number_format($nearValue, 2) }}</b> will expire within the next {{ $thresholdDays }} days.
                    </p>
                </div>
            </div>
            <a href="{{ route('stock_expiry.index', ['status' => 'near_expiry']) }}" class="btn btn-warning btn-sm fw-bold px-3 py-2 text-nowrap rounded-pill text-dark">
                View Near Expiry Items
            </a>
        </div>
    @endif

    {{-- Stat Widgets Grid --}}
    <div class="row g-3 mb-4">
        {{-- Expired Card --}}
        <div class="col-md-4">
            <div class="exp-stat-card exp-stat-card-red">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="exp-stat-lbl">Expired Batches 🔴</div>
                        <div class="exp-stat-val text-danger mt-1">{{ number_format($expiredCount) }} Batches</div>
                    </div>
                    <div class="exp-stat-icon" style="background: #fef2f2; color: #ef4444;">
                        <i class="fas fa-calendar-xmark"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="text-muted small fw-semibold">{{ number_format($expiredQty) }} Total Units</span>
                    <span class="fw-bold text-danger fs-6">PKR {{ number_format($expiredValue, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Near Expiry Card --}}
        <div class="col-md-4">
            <div class="exp-stat-card exp-stat-card-amber">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="exp-stat-lbl">Near Expiry (Within {{ $thresholdDays }} Days) 🟡</div>
                        <div class="exp-stat-val text-warning mt-1" style="color: #d97706 !important;">{{ number_format($nearCount) }} Batches</div>
                    </div>
                    <div class="exp-stat-icon" style="background: #fffbeb; color: #d97706;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="text-muted small fw-semibold">{{ number_format($nearQty) }} Total Units</span>
                    <span class="fw-bold text-dark fs-6">PKR {{ number_format($nearValue, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Good Expiry Card --}}
        <div class="col-md-4">
            <div class="exp-stat-card exp-stat-card-green">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="exp-stat-lbl">Good / Long Expiry ({{ $thresholdDays }}+ Days) 🟢</div>
                        <div class="exp-stat-val text-success mt-1">{{ number_format($goodCount) }} Batches</div>
                    </div>
                    <div class="exp-stat-icon" style="background: #ecfdf5; color: #10b981;">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="text-muted small fw-semibold">{{ number_format($goodQty) }} Total Units</span>
                    <span class="fw-bold text-success fs-6">PKR {{ number_format($goodValue, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Control Card --}}
    <div class="exp-filter-card">
        <form action="{{ route('stock_expiry.index') }}" method="GET" id="expiryFilterForm">
            <div class="row g-3 align-items-center justify-content-between">
                
                {{-- Status Filter Pills --}}
                <div class="col-lg-6 col-12">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('stock_expiry.index', ['status' => 'all', 'threshold' => $thresholdDays, 'warehouse_id' => $warehouseId, 'search' => $search]) }}" 
                           class="status-tab-btn {{ $statusFilter == 'all' ? 'active-all' : '' }}">
                            <i class="fas fa-layer-group"></i> All Batches ({{ $expiredCount + $nearCount + $goodCount }})
                        </a>

                        <a href="{{ route('stock_expiry.index', ['status' => 'expired', 'threshold' => $thresholdDays, 'warehouse_id' => $warehouseId, 'search' => $search]) }}" 
                           class="status-tab-btn {{ $statusFilter == 'expired' ? 'active-expired' : '' }}">
                            🔴 Expired ({{ $expiredCount }})
                        </a>

                        <a href="{{ route('stock_expiry.index', ['status' => 'near_expiry', 'threshold' => $thresholdDays, 'warehouse_id' => $warehouseId, 'search' => $search]) }}" 
                           class="status-tab-btn {{ $statusFilter == 'near_expiry' ? 'active-near' : '' }}">
                            🟡 Near Expiry ({{ $nearCount }})
                        </a>

                        <a href="{{ route('stock_expiry.index', ['status' => 'good_expiry', 'threshold' => $thresholdDays, 'warehouse_id' => $warehouseId, 'search' => $search]) }}" 
                           class="status-tab-btn {{ $statusFilter == 'good_expiry' ? 'active-good' : '' }}">
                            🟢 Good Expiry ({{ $goodCount }})
                        </a>
                    </div>
                </div>

                {{-- Warehouse, Threshold & Search Controls --}}
                <div class="col-lg-6 col-12">
                    <div class="d-flex gap-2 flex-wrap flex-sm-nowrap justify-content-end">
                        <input type="hidden" name="status" value="{{ $statusFilter }}">

                        <select name="threshold" class="form-select fw-semibold" style="height: 40px; font-size: 0.85rem; width: 140px;" onchange="document.getElementById('expiryFilterForm').submit()">
                            <option value="15" {{ $thresholdDays == 15 ? 'selected' : '' }}>15 Days Alert</option>
                            <option value="30" {{ $thresholdDays == 30 ? 'selected' : '' }}>30 Days Alert</option>
                            <option value="60" {{ $thresholdDays == 60 ? 'selected' : '' }}>60 Days Alert</option>
                            <option value="90" {{ $thresholdDays == 90 ? 'selected' : '' }}>90 Days Alert</option>
                            <option value="180" {{ $thresholdDays == 180 ? 'selected' : '' }}>180 Days Alert</option>
                        </select>

                        <select name="warehouse_id" class="form-select fw-semibold" style="height: 40px; font-size: 0.85rem; width: 170px;" onchange="document.getElementById('expiryFilterForm').submit()">
                            <option value="">All Warehouses</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ $warehouseId == $wh->id ? 'selected' : '' }}>{{ $wh->warehouse_name }}</option>
                            @endforeach
                        </select>

                        <div class="input-group" style="width: 220px;">
                            <input type="text" name="search" value="{{ $search }}" class="form-control" style="height: 40px; font-size: 0.85rem;" placeholder="Search product / batch...">
                            <button type="submit" class="btn btn-dark px-3" style="height: 40px;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- Batch Expiry Table Card --}}
    <div class="exp-table-card">
        <div class="table-responsive">
            <table class="table exp-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 40px;">#</th>
                        <th>PRODUCT ITEM & SKU</th>
                        <th>WAREHOUSE</th>
                        <th>BATCH NUMBER</th>
                        <th>EXPIRY DATE</th>
                        <th class="text-center">EXPIRY STATUS</th>
                        <th class="text-center">STOCK QTY</th>
                        <th class="text-end">UNIT COST</th>
                        <th class="pe-4 text-end">ESTIMATED VALUE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $index => $b)
                        <tr>
                            <td class="ps-4 fw-bold text-muted" style="font-size: 0.78rem;">
                                {{ $batches->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="fw-extrabold text-primary" style="font-size: 0.92rem;">
                                    {{ $b->product->item_name ?? 'Product #'.$b->product_id }}
                                </div>
                                <div class="text-muted small">
                                    SKU: <span class="fw-semibold text-dark">{{ $b->product->item_code ?? '-' }}</span>
                                    @if($b->variant_key)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1">{{ $b->variant_key }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-bold px-3 py-1">
                                    <i class="fas fa-warehouse text-secondary me-1"></i> {{ $b->warehouse->warehouse_name ?? 'Main' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark font-monospace">{{ $b->batch_no }}</div>
                                @if($b->mfg_date)
                                    <small class="text-muted" style="font-size: 0.74rem;">MFG: {{ \Carbon\Carbon::parse($b->mfg_date)->format('d M Y') }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold {{ $b->expiry_status == 'expired' ? 'text-danger' : ($b->expiry_status == 'near_expiry' ? 'text-warning text-dark' : 'text-dark') }}">
                                    {{ \Carbon\Carbon::parse($b->expiry_date)->format('d M Y') }}
                                </div>
                                <small class="text-muted" style="font-size: 0.74rem;">
                                    {{ \Carbon\Carbon::parse($b->expiry_date)->diffForHumans() }}
                                </small>
                            </td>
                            <td class="text-center">
                                @if($b->expiry_status == 'expired')
                                    <span class="expiry-badge expiry-badge-expired">
                                        🔴 Expired ({{ abs($b->days_diff) }} days ago)
                                    </span>
                                @elseif($b->expiry_status == 'near_expiry')
                                    <span class="expiry-badge expiry-badge-near">
                                        🟡 Near Expiry ({{ $b->days_diff }} days left)
                                    </span>
                                @else
                                    <span class="expiry-badge expiry-badge-good">
                                        🟢 Good ({{ $b->days_diff }} days left)
                                    </span>
                                @endif
                            </td>
                            <td class="text-center fw-extrabold fs-6 text-dark">
                                {{ number_format($b->qty) }} <small class="text-muted font-normal">{{ $b->product->unit->name ?? 'Pcs' }}</small>
                            </td>
                            <td class="text-end fw-semibold text-muted">
                                {{ number_format($b->cost_price, 2) }} PKR
                            </td>
                            <td class="pe-4 text-end fw-extrabold text-dark fs-6">
                                {{ number_format($b->qty * $b->cost_price, 2) }} PKR
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-calendar-check fs-1 text-muted mb-2 d-block"></i>
                                <h6 class="fw-bold text-dark">No Batch Expiry Records Found</h6>
                                <p class="text-muted small mb-0">No product batches match your selected status or search filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($batches->hasPages())
            <div class="p-3 bg-white border-top">
                {{ $batches->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
