@extends('admin_panel.layout.app')

@section('content')
<style>
    .kpi-card {
        border-radius: 12px;
        border: none;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    .status-pill {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
    }
    @media print {
        .no-print, .header, .sidebar, .page-header, .filter-card, .pagination, .page-btn {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .print-header {
            display: block !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
    .print-header {
        display: none;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid">

            {{-- Printable Header --}}
            <div class="print-header mb-4 text-center border-bottom pb-3">
                <h2 class="fw-bold mb-1">Warehouse Stock & Working Audit Report</h2>
                <p class="text-muted mb-0">Generated on: {{ now()->format('d M, Y h:i A') }} | User: {{ auth()->user()->name }}</p>
                @if($selectedWarehouseId !== 'all')
                    @php $swh = $allWarehouses->firstWhere('id', $selectedWarehouseId); @endphp
                    <h5 class="text-primary mt-2">Warehouse: {{ $swh->warehouse_name ?? 'Specific' }} ({{ $swh->location ?? 'Main' }})</h5>
                @endif
            </div>

            {{-- Page Header --}}
            <div class="page-header row align-items-center mb-3 no-print">
                <div class="page-title col-lg-6">
                    <h4 class="fw-bold mb-1"><i class="fas fa-boxes-stacked text-primary me-2"></i>Warehouse Working & Stock Report</h4>
                    <h6 class="text-muted mb-0">Comprehensive multi-warehouse inventory levels, valuations & movements</h6>
                </div>
                <div class="page-btn d-flex justify-content-end col-lg-6 gap-2">
                    <button onclick="window.print()" class="btn btn-outline-dark">
                        <i class="fas fa-print me-1"></i> Print / PDF
                    </button>
                    <a href="{{ route('warehouse.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Warehouse List
                    </a>
                    <a href="{{ route('stock_transfers.create') }}" class="btn btn-primary">
                        <i class="fas fa-exchange-alt me-1"></i> Transfer Stock
                    </a>
                </div>
            </div>

            {{-- Summary KPI Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card kpi-card shadow-sm p-3 bg-white border-start border-primary border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-bold text-uppercase">Total Boxes / Cartons</span>
                                <h3 class="fw-extrabold text-primary mb-0 mt-1">{{ number_format($totalBoxes, 2) }}</h3>
                            </div>
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                                <i class="fas fa-box fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card kpi-card shadow-sm p-3 bg-white border-start border-success border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-bold text-uppercase">Total Stock Pieces</span>
                                <h3 class="fw-extrabold text-success mb-0 mt-1">{{ number_format($totalPieces) }}</h3>
                            </div>
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                                <i class="fas fa-cubes fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card kpi-card shadow-sm p-3 bg-white border-start border-info border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-bold text-uppercase">Active Stocked SKUs</span>
                                <h3 class="fw-extrabold text-info mb-0 mt-1">{{ number_format($stockedProductsCount) }}</h3>
                            </div>
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info">
                                <i class="fas fa-tags fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card kpi-card shadow-sm p-3 bg-white border-start border-warning border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small fw-bold text-uppercase">Total Stock Valuation</span>
                                <h3 class="fw-extrabold text-dark mb-0 mt-1">{{ number_format($inventoryValue, 2) }} <small class="fs-6 text-muted">PKR</small></h3>
                            </div>
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                                <i class="fas fa-coins fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 filter-card no-print">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('warehouse.working_report') }}" id="reportFilterForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-secondary mb-1">Warehouse</label>
                                <select name="warehouse_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" {{ $selectedWarehouseId === 'all' ? 'selected' : '' }}>All Warehouses</option>
                                    @foreach($allWarehouses as $wh)
                                        <option value="{{ $wh->id }}" {{ (string)$selectedWarehouseId === (string)$wh->id ? 'selected' : '' }}>
                                            {{ $wh->warehouse_name }} ({{ $wh->location ?? 'Main' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-secondary mb-1">Category</label>
                                <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" {{ $categoryId === 'all' ? 'selected' : '' }}>All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-secondary mb-1">Stock Status</label>
                                <select name="stock_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="all" {{ $stockFilter === 'all' ? 'selected' : '' }}>All Levels</option>
                                    <option value="in_stock" {{ $stockFilter === 'in_stock' ? 'selected' : '' }}>In Stock Only (>0)</option>
                                    <option value="low_stock" {{ $stockFilter === 'low_stock' ? 'selected' : '' }}>Low Stock Alerts</option>
                                    <option value="zero_stock" {{ $stockFilter === 'zero_stock' ? 'selected' : '' }}>Zero / Out of Stock</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-secondary mb-1">Search Product / SKU</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Search item or SKU..." value="{{ $search }}">
                                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                                </div>
                            </div>

                            <div class="col-md-2 text-end">
                                <a href="{{ route('warehouse.working_report') }}" class="btn btn-sm btn-outline-secondary w-100">
                                    <i class="fas fa-redo me-1"></i> Reset Filters
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Main Stock Inventory Table --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-table-list text-primary me-2"></i>Live Warehouse Inventory Detail
                    </h5>
                    <span class="badge bg-secondary">{{ $stocks->total() }} Records Found</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="ps-3" style="width: 40px;">#</th>
                                    <th>Warehouse</th>
                                    <th>Product Details</th>
                                    <th>Category & Unit</th>
                                    <th class="text-center">Boxes / Cartons</th>
                                    <th class="text-center">Pcs / Box</th>
                                    <th class="text-center">Total Pieces</th>
                                    <th class="text-end">Unit Cost (PKR)</th>
                                    <th class="text-end">Estimated Value</th>
                                    <th class="pe-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocks as $index => $item)
                                    @php
                                        $prod = $item->product;
                                        $ppb = ($prod && $prod->pieces_per_box > 0) ? $prod->pieces_per_box : 1;
                                        $cost = (float)($prod->purchase_price_per_piece ?? 0);
                                        $totalVal = $item->total_pieces * $cost;
                                        $alertQty = (float)($prod->alert_carton_quantity ?? 0);
                                        $currentBoxes = $ppb > 0 ? ($item->total_pieces / $ppb) : $item->quantity;
                                        
                                        $isLowStock = ($alertQty > 0 && $currentBoxes <= $alertQty);
                                        $isOutOfStock = ($item->total_pieces <= 0);
                                    @endphp
                                    <tr>
                                        <td class="ps-3 text-muted">{{ $stocks->firstItem() + $index }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->warehouse->warehouse_name ?? 'Warehouse #'.$item->warehouse_id }}</div>
                                            <small class="text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $item->warehouse->location ?? 'Main' }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-primary">{{ $prod->item_name ?? 'Unknown Product' }}</div>
                                            <small class="font-monospace text-muted">SKU: {{ $prod->item_code ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <div><span class="badge bg-light text-dark border">{{ $prod->category_relation->name ?? 'Uncategorized' }}</span></div>
                                            <small class="text-secondary">{{ $prod->unit->name ?? $prod->size_mode ?? 'Pieces' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info bg-opacity-10 text-dark fw-bold px-2 py-1">
                                                {{ number_format($currentBoxes, 2) }} Cartons
                                            </span>
                                        </td>
                                        <td class="text-center text-muted font-monospace">
                                            {{ $ppb }}
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-extrabold {{ $isOutOfStock ? 'text-danger' : ($isLowStock ? 'text-warning' : 'text-success') }} fs-6">
                                                {{ number_format($item->total_pieces) }}
                                            </span>
                                        </td>
                                        <td class="text-end text-muted font-monospace">
                                            {{ number_format($cost, 2) }}
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            {{ number_format($totalVal, 2) }}
                                        </td>
                                        <td class="pe-3 text-center">
                                            @if($isOutOfStock)
                                                <span class="badge bg-danger status-pill">Out of Stock</span>
                                            @elseif($isLowStock)
                                                <span class="badge bg-warning text-dark status-pill"><i class="fas fa-exclamation-triangle me-1"></i>Low Stock</span>
                                            @else
                                                <span class="badge bg-success status-pill"><i class="fas fa-check-circle me-1"></i>Normal</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fs-1 mb-2 d-block opacity-50"></i>
                                            <h6 class="fw-bold">No Warehouse Stock Records Found</h6>
                                            <p class="small mb-0">Try clearing filters or adding opening stock / purchases to this warehouse.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($stocks->hasPages())
                    <div class="card-footer bg-white border-top py-3 d-flex justify-content-end no-print">
                        {{ $stocks->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            {{-- Recent Warehouse Stock Transfers / Movements --}}
            @if(isset($recentTransfers) && $recentTransfers->count() > 0)
                <div class="card border-0 shadow-sm rounded-3 mb-4 no-print">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-truck-ramp-box text-info me-2"></i>Recent Stock Movements & Transfers
                        </h6>
                        <a href="{{ route('stock_transfers.index') }}" class="btn btn-sm btn-outline-primary">View All Transfers</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light small text-muted">
                                    <tr>
                                        <th class="ps-3">Date</th>
                                        <th>Product</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th class="text-center">Transferred Qty</th>
                                        <th class="pe-3 text-end">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransfers as $tr)
                                        <tr>
                                            <td class="ps-3 small text-muted">{{ $tr->created_at ? $tr->created_at->format('d M, Y H:i') : '—' }}</td>
                                            <td class="fw-semibold text-primary">{{ $tr->product->item_name ?? 'Product #'.$tr->product_id }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $tr->fromWarehouse->warehouse_name ?? 'WH #'.$tr->from_warehouse_id }}</span></td>
                                            <td>
                                                @if($tr->to_shop)
                                                    <span class="badge bg-success text-white"><i class="fas fa-store me-1"></i>Shop / Counter</span>
                                                @else
                                                    <span class="badge bg-light text-dark border">{{ $tr->toWarehouse->warehouse_name ?? 'WH #'.$tr->to_warehouse_id }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center fw-bold text-dark">{{ number_format($tr->total_pieces ?? $tr->quantity ?? 0) }} Pcs</td>
                                            <td class="pe-3 text-end small text-muted">{{ $tr->remarks ?: 'Stock Transfer' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
