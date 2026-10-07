@extends('admin_panel.layout.app')

@section('title', 'Opening Stock Management')

@section('content')
<link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/fonts/inter/inter.css') }}">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
        --os-primary: #4f46e5;
        --os-primary-light: #eef2ff;
        --os-primary-dark: #3730a3;
        --os-secondary: #0ea5e9;
        --os-secondary-light: #f0f9ff;
        --os-success: #10b981;
        --os-success-light: #ecfdf5;
        --os-amber: #f59e0b;
        --os-amber-light: #fffbeb;
        --os-purple: #8b5cf6;
        --os-purple-light: #f5f3ff;
        --os-slate-900: #0f172a;
        --os-slate-800: #1e293b;
        --os-slate-700: #334155;
        --os-slate-600: #475569;
        --os-slate-100: #f1f5f9;
        --os-slate-50: #f8fafc;
        --os-border: #cbd5e1;
        --os-radius: 16px;
        --os-card-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.05), 0 4px 6px -2px rgba(15, 23, 42, 0.02);
    }

    body {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        background-color: #f6f8fa;
        color: var(--os-slate-800);
    }

    .os-container {
        padding: 10px 20px 40px 20px;
        max-width: 1540px;
        margin: 0 auto;
    }

    /* --- Hero Header Banner --- */
    .os-hero-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #312e81 100%);
        border-radius: var(--os-radius);
        padding: 26px 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.3);
        margin-bottom: 24px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .os-hero-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, rgba(0, 0, 0, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .os-hero-title {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0;
    }

    .os-hero-badge {
        background: rgba(99, 102, 241, 0.2);
        border: 1px solid rgba(129, 140, 248, 0.4);
        color: #a5b4fc;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 8px;
    }

    /* --- Stats Widget Grid --- */
    .os-stat-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 16px 20px;
        border: 1.5px solid var(--os-border);
        box-shadow: var(--os-card-shadow);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s ease;
    }

    .os-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px -5px rgba(15, 23, 42, 0.08);
    }

    .os-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .os-stat-val {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--os-slate-900);
        line-height: 1.2;
    }

    .os-stat-lbl {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--os-slate-600);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    /* --- Main Terminal Card --- */
    .os-terminal-card {
        background: #ffffff;
        border-radius: var(--os-radius);
        border: 1.5px solid var(--os-border);
        box-shadow: var(--os-card-shadow);
        overflow: hidden;
        margin-bottom: 28px;
    }

    .os-terminal-header {
        padding: 18px 28px;
        background: #ffffff;
        border-bottom: 1.5px solid var(--os-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .os-terminal-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--os-slate-900);
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .os-step-number {
        width: 30px;
        height: 30px;
        background: var(--os-primary-light);
        color: var(--os-primary);
        border-radius: 50%;
        display: inline-grid;
        place-items: center;
        font-size: 0.85rem;
        font-weight: 800;
    }

    /* --- Form Elements --- */
    .os-label {
        font-size: 0.74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--os-slate-700);
        margin-bottom: 6px;
        display: block;
    }

    .os-input {
        height: 42px;
        border-radius: 10px;
        border: 1.5px solid var(--os-border);
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--os-slate-900);
        padding: 0 12px;
        transition: all 0.15s;
        background-color: #ffffff;
    }

    .os-input:focus {
        border-color: var(--os-primary);
        box-shadow: 0 0 0 3.5px rgba(79, 70, 229, 0.12);
        outline: none;
    }

    /* --- Excel Spreadsheet Style Multi-Item Table --- */
    .multi-item-table {
        border: 2px solid #cbd5e1 !important;
        border-collapse: collapse !important;
    }

    .multi-item-table th {
        background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%) !important;
        color: #0f172a !important;
        font-size: 0.74rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        padding: 9px 12px !important;
        border: 1px solid #cbd5e1 !important;
        border-bottom: 2.5px solid #64748b !important;
        white-space: nowrap;
    }

    .multi-item-table td {
        padding: 6px 8px !important;
        vertical-align: middle !important;
        border: 1px solid #cbd5e1 !important;
        background: #ffffff;
    }

    .multi-item-table tr:nth-child(even) td {
        background: #f8fafc;
    }

    .multi-item-table tr:hover td {
        background: #f1f5f9 !important;
    }

    .item-row-idx {
        width: 24px;
        height: 24px;
        background: #cbd5e1;
        color: #0f172a;
        border-radius: 4px;
        display: inline-grid;
        place-items: center;
        font-size: 0.72rem;
        font-weight: 800;
    }

    .mode-badge {
        font-size: 0.72rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .mode-badge-simple { background: #e0f2fe; color: #0369a1; }
    .mode-badge-batch { background: #fef3c7; color: #b45309; }
    .mode-badge-serial { background: #f3e8ff; color: #6b21a8; }

    /* Serial Grid Modal Input */
    .serial-grid-box {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 14px;
        padding: 16px;
        max-height: 380px;
        overflow-y: auto;
    }

    .serial-item-cell {
        background: #ffffff;
        border: 1.5px solid var(--os-border);
        border-radius: 10px;
        padding: 4px 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .serial-item-cell:focus-within {
        border-color: var(--os-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .serial-idx {
        font-size: 0.72rem;
        font-weight: 800;
        background: #f1f5f9;
        color: var(--os-slate-600);
        padding: 3px 6px;
        border-radius: 6px;
        flex-shrink: 0;
    }

    .serial-cell-input {
        border: none;
        outline: none;
        width: 100%;
        height: 34px;
        font-family: monospace;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--os-slate-900);
        letter-spacing: 0.05em;
    }

    /* History Table */
    .history-card {
        background: #ffffff;
        border-radius: var(--os-radius);
        border: 1.5px solid var(--os-border);
        box-shadow: var(--os-card-shadow);
        overflow: hidden;
    }

    .history-table th {
        background: #f8fafc;
        color: var(--os-slate-700);
        font-size: 0.74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 14px 18px;
        border-bottom: 1.5px solid var(--os-border);
    }

    .history-table td {
        padding: 14px 18px;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
    }

    /* Select2 Custom */
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1.5px solid var(--os-border) !important;
        border-radius: 10px !important;
        padding: 6px 10px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--os-slate-900) !important;
        font-weight: 700 !important;
        font-size: 0.88rem !important;
        line-height: 28px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    /* Select2 Custom inside table cells */
    .multi-item-table .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1.5px solid var(--os-border) !important;
        border-radius: 6px !important;
        padding: 4px 8px !important;
    }

    .multi-item-table .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--os-slate-900) !important;
        font-weight: 700 !important;
        font-size: 0.84rem !important;
        line-height: 28px !important;
    }

    .multi-item-table .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
</style>

<div class="os-container">

    {{-- Hero Header --}}
    <div class="os-hero-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 2;">
            <div>
                <span class="os-hero-badge">INVENTORY CONTROL TERMINAL</span>
                <h2 class="os-hero-title">Opening Stock Management</h2>
                <p class="text-white-50 mb-0 mt-1" style="font-size: 0.92rem; max-width: 750px;">
                    Add initial inventory stock for single or <b>Multiple Products & Variants at once</b>. Supports Simple Stock, Batch Wise (with Expiry & MFG dates), and Mobile IMEI / Serial tracking.
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('product') }}" class="btn btn-outline-light px-3 py-2 fw-semibold rounded-3" style="font-size: 0.88rem;">
                    <i class="fas fa-boxes me-1"></i> Products Catalog
                </a>
                <button class="btn btn-light text-dark px-4 py-2 fw-bold rounded-3 shadow-sm" style="font-size: 0.88rem;" onclick="document.getElementById('terminalCard').scrollIntoView({behavior: 'smooth'})">
                    <i class="fas fa-plus-circle text-primary me-1"></i> Multi-Product Terminal
                </button>
            </div>
        </div>
    </div>

    {{-- Stat Widgets Grid --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="os-stat-card">
                <div class="os-stat-icon" style="background: #eef2ff; color: #4f46e5;">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <div class="os-stat-lbl">Total Stock Entries</div>
                    <div class="os-stat-val">{{ number_format($totalOpeningMovements) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="os-stat-card">
                <div class="os-stat-icon" style="background: #ecfdf5; color: #10b981;">
                    <i class="fas fa-cubes"></i>
                </div>
                <div>
                    <div class="os-stat-lbl">Total Units Initialized</div>
                    <div class="os-stat-val">{{ number_format($totalPiecesAdded, 0) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="os-stat-card">
                <div class="os-stat-icon" style="background: #fffbeb; color: #d97706;">
                    <i class="fas fa-tags"></i>
                </div>
                <div>
                    <div class="os-stat-lbl">Active Batches</div>
                    <div class="os-stat-val">{{ number_format($totalBatches) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="os-stat-card">
                <div class="os-stat-icon" style="background: #f5f3ff; color: #8b5cf6;">
                    <i class="fas fa-mobile-screen-button"></i>
                </div>
                <div>
                    <div class="os-stat-lbl">Tracked IMEIs / Serials</div>
                    <div class="os-stat-val">{{ number_format($totalSerials) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Multi-Product Opening Stock Terminal --}}
    <div class="os-terminal-card" id="terminalCard">
        <div class="os-terminal-header">
            <h5 class="os-terminal-title">
                <span class="os-step-number">01</span>
                Multi-Product & Variant Opening Stock Terminal
            </h5>
            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2 rounded-pill" style="font-size: 0.78rem;">
                <i class="fas fa-bolt me-1"></i> Multi-Item Batch Entry
            </span>
        </div>

        <div class="p-4 p-md-4">
            <form id="multiOpeningStockForm">
                @csrf

                {{-- Global Controls Row --}}
                <div class="row g-3 align-items-end mb-4 bg-light p-3 rounded-4 border">
                    <div class="col-md-3">
                        <label class="os-label">TARGET WAREHOUSE <span class="text-danger">*</span></label>
                        <select id="globalWarehouseSelect" class="form-select os-input fw-bold" required>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->warehouse_name }} ({{ $wh->location ?? 'Main' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="os-label">QUICK ADD PRODUCT TO ENTRY GRID</label>
                        <select id="quickProductSearch" class="form-select select2-quick-product" style="width: 100%;">
                            <option value="">Search & select product / variant to add row...</option>
                            @foreach($products as $p)
                                <option value="{{ $p['value'] }}" data-variant="{{ $p['variant_key'] }}">{{ $p['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 text-end d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary fw-bold os-input flex-fill" onclick="addBlankRow()">
                            <i class="fas fa-plus me-1"></i> Add Blank Row
                        </button>
                    </div>
                </div>

                {{-- Multi-Product Opening Stock Entry Table --}}
                <div class="table-responsive mb-4">
                    <table class="table multi-item-table border align-middle mb-0" id="multiStockTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 35px;">#</th>
                                <th style="width: 30%;">PRODUCT & VARIANT <span class="text-danger">*</span></th>
                                <th style="width: 210px;">TRACKING MODE</th>
                                <th>ENTRY DETAILS & SPECIFICATIONS</th>
                                <th class="text-end" style="width: 120px;">TOTAL (PKR)</th>
                                <th class="text-center" style="width: 45px;">ACT</th>
                            </tr>
                        </thead>
                        <tbody id="multiStockTbody">
                            <!-- Rows dynamically added here -->
                        </tbody>
                    </table>
                </div>

                {{-- Empty Table Placeholder State --}}
                <div id="emptyGridState" class="text-center py-5 border rounded-4 bg-light mb-4">
                    <i class="fas fa-dolly fs-1 text-muted mb-2 d-block"></i>
                    <h6 class="fw-bold text-dark mb-1">No Product Rows Added Yet</h6>
                    <p class="text-muted small mb-3">Use the product search bar above or click button below to add products and variants to Opening Stock!</p>
                    <button type="button" class="btn btn-primary fw-bold px-4 py-2 rounded-pill" onclick="addBlankRow()">
                        <i class="fas fa-plus me-1"></i> Add First Product Row
                    </button>
                </div>

                {{-- Live Summary Footer & Action Bar --}}
                <div class="d-flex justify-content-between align-items-center bg-dark text-white p-3 rounded-4 flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-4">
                        <div>
                            <small class="text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem;">TOTAL ROWS</small>
                            <div class="fw-extrabold fs-5 text-white" id="summaryTotalRows">0 Rows</div>
                        </div>
                        <div class="vr bg-secondary" style="height: 30px;"></div>
                        <div>
                            <small class="text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem;">TOTAL UNITS</small>
                            <div class="fw-extrabold fs-5 text-success" id="summaryTotalUnits">0 Units</div>
                        </div>
                        <div class="vr bg-secondary" style="height: 30px;"></div>
                        <div>
                            <small class="text-white-50 text-uppercase fw-bold" style="font-size: 0.7rem;">TOTAL ESTIMATED VALUE</small>
                            <div class="fw-extrabold fs-5 text-info" id="summaryTotalValue">0.00 PKR</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-light px-3 fw-semibold" onclick="clearAllGridRows()">
                            Clear Table
                        </button>
                        <button type="button" id="saveAllOpeningStockBtn" onclick="submitBatchOpeningStock()" class="btn btn-success px-5 py-2 fw-bold text-white shadow" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                            <i class="fas fa-check-circle me-1"></i> SAVE ALL OPENING STOCKS
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- History / Recent Opening Stock Table Card --}}
    <div class="history-card">
        <div class="os-terminal-header">
            <h5 class="os-terminal-title">
                <i class="fas fa-history text-primary"></i>
                Opening Stock Audit History Log
            </h5>
            <form action="{{ route('opening_stock.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control os-input" style="height: 38px; font-size: 0.82rem;" placeholder="Search product, SKU or batch...">
                <button type="submit" class="btn btn-primary px-3 py-1 fw-semibold" style="font-size: 0.82rem;">Filter</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table history-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">DATE & TIME</th>
                        <th>PRODUCT ITEM</th>
                        <th class="text-center">QTY ADDED</th>
                        <th>LOG & TRACKING DETAILS</th>
                        <th class="pe-4 text-end">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $m->created_at->format('d M Y') }}</div>
                                <small class="text-muted" style="font-size: 0.74rem;">{{ $m->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ $m->product->item_name ?? 'Product #'.$m->product_id }}</div>
                                <small class="text-muted">SKU: {{ $m->product->item_code ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 fs-6">
                                    +{{ number_format($m->qty, 0) }} Pcs
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $m->note }}</div>
                            </td>
                            <td class="pe-4 text-end">
                                <span class="badge bg-success px-3 py-1 rounded-pill">Posted</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-boxes-packing fs-1 text-muted mb-2 d-block"></i>
                                No opening stock records found. Use the terminal above to add stock!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movements->hasPages())
            <div class="p-3 bg-white border-top">
                {{ $movements->links() }}
            </div>
        @endif
    </div>

</div>

{{-- MODAL: ROW SERIALS / IMEIs MANAGER --}}
<div class="modal fade" id="rowSerialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div>
                    <h5 class="modal-title fw-bold" id="serialModalTitle"><i class="fas fa-mobile-screen-button text-primary me-2"></i>Serial / IMEI Manager</h5>
                    <small class="text-white-50" id="serialModalSubtitle">Configure unique IMEIs / Serial numbers for product row</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="closeRowSerialModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                {{-- Quick Tab: Grid Input vs Mass Bulk Paste --}}
                <ul class="nav nav-pills nav-fill mb-3" id="serialModalTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold" id="gridTabBtn" onclick="switchSerialModalView('grid')">
                            <i class="fas fa-grid-2 me-1"></i> Individual Cell Inputs
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" id="bulkTabBtn" onclick="switchSerialModalView('bulk')">
                            <i class="fas fa-paste me-1"></i> Mass Bulk Paste (100+ IMEIs)
                        </button>
                    </li>
                </ul>

                {{-- View 1: Grid --}}
                <div id="modalSerialGridView">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold fs-6" id="modalSerialCounter">0 / 0 Entered</span>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearModalSerials()">Clear All</button>
                    </div>
                    <div class="serial-grid-box" id="modalSerialGridBox">
                        <div class="row g-2" id="modalSerialRowsContainer">
                            <!-- Rendered dynamically -->
                        </div>
                    </div>
                </div>

                {{-- View 2: Bulk Paste --}}
                <div id="modalSerialBulkView" class="d-none">
                    <p class="text-muted small mb-2">
                        Paste list of IMEIs / Serials (one per line or comma separated).
                    </p>
                    <textarea id="modalBulkTextarea" class="form-control font-monospace p-3" rows="10" placeholder="e.g.&#10;860123456789012&#10;860123456789013&#10;860123456789014" style="font-size: 0.9rem; font-weight: 600;"></textarea>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill" id="modalBulkDetectedBadge">0 IMEIs Detected</span>
                        <button type="button" class="btn btn-outline-primary btn-sm fw-bold" onclick="applyModalBulkText()">Apply Paste to Grid</button>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-light border-0 p-3">
                <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal" onclick="closeRowSerialModal()">Cancel</button>
                <button type="button" class="btn btn-success px-4 fw-bold" onclick="saveModalSerialsToRow()">
                    <i class="fas fa-check me-1"></i> Save IMEIs to Row
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: ROW BATCH DETAILS MANAGER --}}
<div class="modal fade" id="rowBatchModal" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4">
                <div>
                    <h5 class="modal-title fw-bold" id="batchModalTitle"><i class="fas fa-tags text-warning me-2"></i>Batch Details Manager</h5>
                    <small class="text-white-50" id="batchModalSubtitle">Configure Batch Number, Dates, Quantity & Cost for product row</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="closeRowBatchModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="modalBatchForm" onsubmit="return false;">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="os-label mb-1">BATCH NUMBER <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" id="modalBatchNoInput" class="form-control os-input fw-bold" placeholder="e.g. BATCH-20261006-416" required>
                                <button type="button" class="btn btn-outline-secondary px-3 fw-bold" onclick="autoGenModalBatch()" title="Auto Generate Batch No">
                                    <i class="fas fa-magic me-1"></i> Auto Gen
                                </button>
                            </div>
                        </div>

                        <div class="col-6">
                            <label class="os-label mb-1">MANUFACTURING DATE (MFG)</label>
                            <input type="date" id="modalBatchMfgInput" class="form-control os-input">
                        </div>

                        <div class="col-6">
                            <label class="os-label mb-1">EXPIRY DATE <span class="text-danger">*</span></label>
                            <input type="date" id="modalBatchExpInput" class="form-control os-input" required>
                        </div>

                        <div class="col-6">
                            <label class="os-label mb-1">BATCH QUANTITY <span class="text-danger">*</span></label>
                            <input type="number" step="any" min="0.01" id="modalBatchQtyInput" class="form-control os-input fw-bold" placeholder="100" oninput="calcModalBatchTotal()" required>
                        </div>

                        <div class="col-6">
                            <label class="os-label mb-1">UNIT COST PRICE (PKR)</label>
                            <input type="number" step="any" min="0" id="modalBatchCostInput" class="form-control os-input" placeholder="0.00" oninput="calcModalBatchTotal()">
                        </div>

                        <div class="col-12">
                            <div class="p-3 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-secondary">TOTAL BATCH VALUE:</span>
                                <span class="fw-extrabold text-primary fs-5" id="modalBatchTotalDisplay">0.00 PKR</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="os-label mb-1">REMARKS / AUDIT NOTE</label>
                            <input type="text" id="modalBatchRemarksInput" class="form-control os-input" placeholder="e.g. Opening balance initialized via warehouse physical count" style="font-size: 0.85rem;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light border-0 p-3 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal" onclick="closeRowBatchModal()">Cancel</button>
                <button type="button" class="btn btn-success px-4 fw-bold" onclick="saveModalBatchToRow()">
                    <i class="fas fa-check me-1"></i> Save Batch to Row
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>
<script>
    let productsList = [];
    let rowCounter = 0;
    let activeSerialRowId = null;
    let activeBatchRowId = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Quick Add Select2
        $('.select2-quick-product').select2({
            placeholder: 'Search product by name or SKU to add row...',
            allowClear: true
        });

        $('#quickProductSearch').on('change', function() {
            const pVal = $(this).val();
            if (pVal) {
                addBlankRow();
                const lastRow = $('#multiStockTbody tr').last();
                lastRow.find('.row-product-select').val(pVal).trigger('change');
                $(this).val('').trigger('change');
            }
        });

        // Excel Spreadsheet Keyboard Navigation: Press Enter on inputs/selects to advance/add new row
        $(document).on('keydown', '#multiStockTable input, #multiStockTable select', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const currentTr = $(this).closest('tr');
                const isLast = currentTr.is(':last-child');
                if (isLast) {
                    addBlankRow();
                    const newTr = $('#multiStockTbody tr').last();
                    setTimeout(() => {
                        newTr.find('.row-product-select').focus();
                    }, 50);
                } else {
                    const nextTr = currentTr.next('tr');
                    nextTr.find('.row-product-select').focus();
                }
            }
        });

        // Initialize with 1 blank row by default
        addBlankRow();

        // Bulk paste counter listener
        $('#modalBulkTextarea').on('input', function() {
            const text = $(this).val();
            const list = text.split(/[\r\n,]+/).map(s => s.trim()).filter(s => s.length > 0);
            $('#modalBulkDetectedBadge').text(list.length + ' IMEIs Detected');
        });
    });

    function addBlankRow() {
        rowCounter++;
        const rowId = 'row_' + rowCounter;

        let productOptions = '<option value="">Select Product / Variant...</option>';
        @foreach($products as $p)
            productOptions += `<option value="{{ $p['value'] }}" data-variant="{{ $p['variant_key'] }}">{{ addslashes($p['label']) }}</option>`;
        @endforeach

        const html = `
            <tr id="${rowId}" data-row-id="${rowId}">
                <td class="text-center">
                    <span class="item-row-idx">${$('#multiStockTbody tr').length + 1}</span>
                </td>

                <td>
                    <select class="form-select os-input row-product-select fw-bold" name="items[${rowId}][product_id]" required>
                        ${productOptions}
                    </select>
                    <input type="hidden" class="row-variant-key-hidden" name="items[${rowId}][variant_key]" value="">
                </td>

                <td>
                    <select class="form-select os-input row-mode-select fw-bold" style="min-width: 190px;" name="items[${rowId}][tracking_type]" onchange="onRowModeChange('${rowId}')">
                        <option value="simple">📦 Simple</option>
                        <option value="batch">🏷️ Batch Wise</option>
                        <option value="serial">📱 Serial / IMEI</option>
                    </select>
                </td>

                <td>
                    {{-- SIMPLE MODE INPUTS --}}
                    <div class="row-mode-panel row-simple-panel d-flex align-items-end gap-2">
                        <div style="width: 100px;">
                            <label class="os-label mb-1">QTY *</label>
                            <input type="number" step="any" min="0.01" class="form-control os-input row-qty-input fw-bold" name="items[${rowId}][qty]" placeholder="Qty" oninput="calcRowTotal('${rowId}')" required>
                        </div>
                        <div style="width: 130px;">
                            <label class="os-label mb-1">UNIT COST (PKR)</label>
                            <input type="number" step="any" min="0" class="form-control os-input row-cost-input" name="items[${rowId}][cost_price]" placeholder="Cost" oninput="calcRowTotal('${rowId}')">
                        </div>
                    </div>

                    {{-- BATCH MODE INPUTS --}}
                    <div class="row-mode-panel row-batch-panel d-flex align-items-end gap-2 d-none">
                        <div style="width: 100px;">
                            <label class="os-label mb-1">QTY *</label>
                            <input type="number" step="any" min="0.01" class="form-control os-input row-qty-input fw-bold" name="items[${rowId}][batch_qty]" placeholder="100" oninput="calcRowTotal('${rowId}')">
                        </div>
                        <div style="width: 130px;">
                            <label class="os-label mb-1">UNIT COST (PKR)</label>
                            <input type="number" step="any" min="0" class="form-control os-input row-cost-input" name="items[${rowId}][batch_cost_price]" placeholder="0.00" oninput="calcRowTotal('${rowId}')">
                        </div>
                        <div style="flex: 1; min-width: 150px;">
                            <button type="button" class="btn btn-sm btn-outline-warning text-dark w-100 fw-bold row-batch-btn text-truncate" style="border-color:#f59e0b; background:#fffbeb; height: 42px;" onclick="openRowBatchModal('${rowId}')" title="Configure Batch No, Dates & Remarks">
                                <i class="fas fa-tags text-warning me-1"></i> Batch (<span class="row-batch-label">Configure</span>)
                            </button>
                            <input type="hidden" class="row-batch-no" name="items[${rowId}][batch_no]" value="">
                            <input type="hidden" class="row-mfg-date" name="items[${rowId}][mfg_date]" value="">
                            <input type="hidden" class="row-expiry-date" name="items[${rowId}][expiry_date]" value="">
                            <input type="hidden" class="row-remarks-hidden" name="items[${rowId}][remarks]" value="">
                        </div>
                    </div>

                    {{-- SERIAL MODE INPUTS --}}
                    <div class="row-mode-panel row-serial-panel d-flex align-items-end gap-2 d-none">
                        <div style="width: 100px;">
                            <label class="os-label mb-1">QTY *</label>
                            <input type="number" min="1" max="10000" value="1" class="form-control os-input row-qty-input row-serial-qty fw-bold text-primary" name="items[${rowId}][serial_qty]" onchange="syncSerialRowQty('${rowId}')" onkeyup="syncSerialRowQty('${rowId}')" placeholder="1000">
                        </div>
                        <div style="width: 130px;">
                            <label class="os-label mb-1">UNIT COST (PKR)</label>
                            <input type="number" step="any" min="0" class="form-control os-input row-cost-input" name="items[${rowId}][serial_cost_price]" placeholder="0.00" oninput="calcRowTotal('${rowId}')">
                        </div>
                        <div style="flex: 1; min-width: 150px;">
                            <button type="button" class="btn btn-sm btn-outline-purple w-100 fw-bold row-serial-btn text-truncate" style="border-color:#8b5cf6; color:#8b5cf6; height: 42px;" onclick="openRowSerialModal('${rowId}')" title="Manage IMEIs / Serials">
                                <i class="fas fa-mobile-screen-button me-1"></i> IMEIs (<span class="row-serial-count">0</span>)
                            </button>
                            <div class="row-serials-hidden-holder"></div>
                        </div>
                    </div>
                </td>

                <td class="text-end fw-extrabold text-primary fs-6 row-line-total">
                    0.00 PKR
                </td>

                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="removeRow('${rowId}')" title="Delete Row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#multiStockTbody').append(html);

        // Initialize Select2 on the newly created row's product dropdown
        $(`#${rowId} .row-product-select`).select2({
            placeholder: 'Select Product / Variant...',
            allowClear: true,
            width: '100%'
        }).on('change', function() {
            onRowProductChange(rowId);
        });

        $('#emptyGridState').addClass('d-none');
        updateRowIndexes();
        updateSummary();
    }

    function duplicateRowProduct(rowId) {
        const origRow = $(`#${rowId}`);
        const pId = origRow.find('.row-product-select').val();
        addBlankRow();
        const newRow = $('#multiStockTbody tr').last();
        if (pId) {
            newRow.find('.row-product-select').val(pId).trigger('change');
        }
    }

    function addProductRow(pData) {
        addBlankRow();
        const lastRow = $('#multiStockTbody tr').last();
        const rowId = lastRow.attr('data-row-id');

        const select = lastRow.find('.row-product-select');
        select.val(pData.id).trigger('change');
    }

    function removeRow(rowId) {
        $(`#${rowId}`).remove();
        if ($('#multiStockTbody tr').length === 0) {
            $('#emptyGridState').removeClass('d-none');
        }
        updateRowIndexes();
        updateSummary();
    }

    function clearAllGridRows() {
        $('#multiStockTbody').empty();
        $('#emptyGridState').removeClass('d-none');
        updateSummary();
    }

    function updateRowIndexes() {
        $('#multiStockTbody tr').each(function(idx) {
            $(this).find('.item-row-idx').text(idx + 1);
        });
    }

    function onRowProductChange(rowId) {
        const row = $(`#${rowId}`);
        const pVal = row.find('.row-product-select').val();
        if (!pVal) return;

        const opt = row.find('.row-product-select option:selected');
        const vKey = opt.attr('data-variant') || null;
        row.find('.row-variant-key-hidden').val(vKey);

        const globalWh = $('#globalWarehouseSelect').val();
        fetch(`{{ url('opening-stock/product-details') }}/${encodeURIComponent(pVal)}?warehouse_id=${globalWh}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                row.data('product-info', data);
                row.find('.row-cost-input').val(data.cost_price);
                if (data.selected_variant) {
                    row.find('.row-variant-key-hidden').val(data.selected_variant);
                }
                calcRowTotal(rowId);
            }
        });
    }

    function onRowModeChange(rowId) {
        const row = $(`#${rowId}`);
        const mode = row.find('.row-mode-select').val();

        row.find('.row-mode-panel').addClass('d-none');
        if (mode === 'simple') {
            row.find('.row-simple-panel').removeClass('d-none');
        } else if (mode === 'batch') {
            row.find('.row-batch-panel').removeClass('d-none');
            if (!row.find('.row-batch-no').val()) {
                autoGenRowBatch(rowId);
            }
        } else if (mode === 'serial') {
            row.find('.row-serial-panel').removeClass('d-none');
        }
        calcRowTotal(rowId);
    }

    function autoGenRowBatch(rowId) {
        const d = new Date();
        const code = 'BATCH-' + d.getFullYear() + String(d.getMonth()+1).padStart(2,'0') + String(d.getDate()).padStart(2,'0') + '-' + Math.floor(100 + Math.random()*900);
        $(`#${rowId}`).find('.row-batch-no').val(code);
    }

    /* BATCH DETAILS MODAL MANAGER FOR ROWS */
    function openRowBatchModal(rowId) {
        try {
            activeBatchRowId = rowId;
            const row = $(`#${rowId}`);
            const pName = row.find('.row-product-select option:selected').text();

            $('#batchModalTitle').html(`<i class="fas fa-tags text-warning me-2"></i>Batch Details Manager`);
            $('#batchModalSubtitle').text(`Configuring Batch for: ${pName || 'Product Row'}`);

            let batchNo = row.find('.row-batch-no').val();
            if (!batchNo) {
                const d = new Date();
                batchNo = 'BATCH-' + d.getFullYear() + String(d.getMonth()+1).padStart(2,'0') + String(d.getDate()).padStart(2,'0') + '-' + Math.floor(100 + Math.random()*900);
            }
            $('#modalBatchNoInput').val(batchNo);

            $('#modalBatchMfgInput').val(row.find('.row-mfg-date').val() || '');
            $('#modalBatchExpInput').val(row.find('.row-expiry-date').val() || '');

            let qty = parseFloat(row.find('.row-batch-panel .row-qty-input').val()) || 100;
            let cost = parseFloat(row.find('.row-batch-panel .row-cost-input').val()) || parseFloat(row.find('.row-cost-input').val()) || 0;

            $('#modalBatchQtyInput').val(qty);
            $('#modalBatchCostInput').val(cost);
            $('#modalBatchRemarksInput').val(row.find('.row-remarks-hidden').val() || 'Opening Stock Batch');

            calcModalBatchTotal();

            const modalEl = document.getElementById('rowBatchModal');
            let shown = false;
            if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                try { $('#rowBatchModal').modal('show'); shown = true; } catch(e) {}
            }
            if (!shown && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try {
                    const modal = bootstrap.Modal.getOrCreateInstance ? bootstrap.Modal.getOrCreateInstance(modalEl) : new bootstrap.Modal(modalEl);
                    modal.show();
                    shown = true;
                } catch(e) {}
            }
            if (!shown) {
                $(modalEl).addClass('show').css('display', 'block').attr('aria-modal', 'true').removeAttr('aria-hidden');
                if ($('.modal-backdrop').length === 0) $('body').append('<div class="modal-backdrop fade show"></div>');
                $('body').addClass('modal-open');
            }
        } catch(err) {
            console.error('Error opening batch modal:', err);
        }
    }

    function closeRowBatchModal() {
        try {
            if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                try { $('#rowBatchModal').modal('hide'); } catch(e) {}
            }
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try {
                    const modalEl = document.getElementById('rowBatchModal');
                    const modal = bootstrap.Modal.getInstance ? bootstrap.Modal.getInstance(modalEl) : null;
                    if (modal) modal.hide();
                } catch(e) {}
            }
        } catch(e) {}

        const modalEl = document.getElementById('rowBatchModal');
        if (modalEl) $(modalEl).removeClass('show').css('display', 'none').attr('aria-hidden', 'true').removeAttr('aria-modal');

        setTimeout(() => {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css({'overflow': '', 'padding-right': ''});
        }, 100);
    }

    function autoGenModalBatch() {
        const d = new Date();
        const code = 'BATCH-' + d.getFullYear() + String(d.getMonth()+1).padStart(2,'0') + String(d.getDate()).padStart(2,'0') + '-' + Math.floor(100 + Math.random()*900);
        $('#modalBatchNoInput').val(code);
    }

    function calcModalBatchTotal() {
        const q = parseFloat($('#modalBatchQtyInput').val()) || 0;
        const c = parseFloat($('#modalBatchCostInput').val()) || 0;
        const total = q * c;
        $('#modalBatchTotalDisplay').text(total.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' PKR');
    }

    function saveModalBatchToRow() {
        if (!activeBatchRowId) return;

        const batchNo = $('#modalBatchNoInput').val().trim();
        const mfgDate = $('#modalBatchMfgInput').val();
        const expDate = $('#modalBatchExpInput').val();
        const qty     = parseFloat($('#modalBatchQtyInput').val()) || 0;
        const cost    = parseFloat($('#modalBatchCostInput').val()) || 0;
        const remarks = $('#modalBatchRemarksInput').val();

        if (!batchNo) {
            Swal.fire({icon: 'warning', title: 'Batch Number Required', text: 'Please enter or generate a Batch Number.'});
            return;
        }
        if (qty <= 0) {
            Swal.fire({icon: 'warning', title: 'Invalid Quantity', text: 'Batch Quantity must be greater than 0.'});
            return;
        }

        const row = $(`#${activeBatchRowId}`);
        row.find('.row-batch-no').val(batchNo);
        row.find('.row-mfg-date').val(mfgDate);
        row.find('.row-expiry-date').val(expDate);
        row.find('.row-remarks-hidden').val(remarks);

        row.find('.row-batch-panel .row-qty-input').val(qty);
        row.find('.row-batch-panel .row-cost-input').val(cost);

        row.find('.row-batch-label').text(batchNo);

        calcRowTotal(activeBatchRowId);

        closeRowBatchModal();

        Swal.fire({
            icon: 'success',
            title: 'Batch Saved!',
            text: `Batch '${batchNo}' (${qty} units) updated for row!`,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1800
        });
    }

    function calcRowTotal(rowId) {
        const row = $(`#${rowId}`);
        const mode = row.find('.row-mode-select').val();

        let q = 0;
        let c = 0;

        if (mode === 'simple') {
            q = parseFloat(row.find('.row-simple-panel .row-qty-input').val()) || 0;
            c = parseFloat(row.find('.row-simple-panel .row-cost-input').val()) || 0;
        } else if (mode === 'batch') {
            q = parseFloat(row.find('.row-batch-panel .row-qty-input').val()) || 0;
            c = parseFloat(row.find('.row-batch-panel .row-cost-input').val()) || 0;
        } else if (mode === 'serial') {
            q = parseInt(row.find('.row-serial-qty').val()) || 0;
            c = parseFloat(row.find('.row-serial-panel .row-cost-input').val()) || 0;
        }

        const lineTotal = q * c;
        row.find('.row-line-total').text(lineTotal.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' PKR');
        updateSummary();
    }

    function updateSummary() {
        let totalRows = 0;
        let totalUnits = 0;
        let totalVal = 0;

        $('#multiStockTbody tr').each(function() {
            totalRows++;
            const rowId = $(this).attr('data-row-id');
            const mode = $(this).find('.row-mode-select').val();

            let q = 0;
            let c = 0;

            if (mode === 'simple') {
                q = parseFloat($(this).find('.row-simple-panel .row-qty-input').val()) || 0;
                c = parseFloat($(this).find('.row-simple-panel .row-cost-input').val()) || 0;
            } else if (mode === 'batch') {
                q = parseFloat($(this).find('.row-batch-panel .row-qty-input').val()) || 0;
                c = parseFloat($(this).find('.row-batch-panel .row-cost-input').val()) || 0;
            } else if (mode === 'serial') {
                q = parseInt($(this).find('.row-serial-qty').val()) || 0;
                c = parseFloat($(this).find('.row-serial-panel .row-cost-input').val()) || 0;
            }

            totalUnits += q;
            totalVal += (q * c);
        });

        $('#summaryTotalRows').text(`${totalRows} Rows`);
        $('#summaryTotalUnits').text(`${totalUnits.toLocaleString('en-US')} Units`);
        $('#summaryTotalValue').text(`${totalVal.toLocaleString('en-US', {minimumFractionDigits: 2})} PKR`);
    }

    /* SERIAL / IMEI MODAL MANAGER FOR ROWS */
    function openRowSerialModal(rowId) {
        try {
            activeSerialRowId = rowId;
            const row = $(`#${rowId}`);

            const pSelect = row.find('.row-product-select option:selected').text();
            const qty = parseInt(row.find('.row-serial-qty').val()) || 1;

            $('#serialModalTitle').html(`<i class="fas fa-mobile-screen-button text-primary me-2"></i>Serial / IMEI Manager`);
            $('#serialModalSubtitle').text(`Configuring ${qty} IMEIs for: ${pSelect}`);

            // Fetch existing serials saved in row hidden inputs
            const existingSerials = [];
            row.find('.row-serials-hidden-holder input').each(function() {
                existingSerials.push($(this).val());
            });

            renderModalSerialGrid(qty, existingSerials);
            switchSerialModalView('grid');

            const modalEl = document.getElementById('rowSerialModal');
            let shown = false;

            // 1. Try jQuery modal API
            if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                try {
                    $('#rowSerialModal').modal('show');
                    shown = true;
                } catch(e) {}
            }

            // 2. Try Bootstrap JS Modal API
            if (!shown && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try {
                    const modal = bootstrap.Modal.getOrCreateInstance ? bootstrap.Modal.getOrCreateInstance(modalEl) : new bootstrap.Modal(modalEl);
                    modal.show();
                    shown = true;
                } catch(e) {}
            }

            // 3. Fallback CSS trigger if JS framework fails
            if (!shown) {
                $(modalEl).addClass('show').css('display', 'block').attr('aria-modal', 'true').removeAttr('aria-hidden');
                if ($('.modal-backdrop').length === 0) {
                    $('body').append('<div class="modal-backdrop fade show"></div>');
                }
                $('body').addClass('modal-open');
            }
        } catch(err) {
            console.error('Error opening IMEI modal:', err);
        }
    }

    function closeRowSerialModal() {
        try {
            if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                try { $('#rowSerialModal').modal('hide'); } catch(e) {}
            }
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try {
                    const modalEl = document.getElementById('rowSerialModal');
                    const modal = bootstrap.Modal.getInstance ? bootstrap.Modal.getInstance(modalEl) : null;
                    if (modal) modal.hide();
                } catch(e) {}
            }
        } catch(e) {}

        const modalEl = document.getElementById('rowSerialModal');
        if (modalEl) {
            $(modalEl).removeClass('show').css('display', 'none').attr('aria-hidden', 'true').removeAttr('aria-modal');
        }

        setTimeout(() => {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css({'overflow': '', 'padding-right': ''});
        }, 100);
    }

    function renderModalSerialGrid(targetQty, existingList = []) {
        const container = $('#modalSerialRowsContainer');
        let html = '';

        for (let i = 0; i < targetQty; i++) {
            const val = existingList[i] || '';
            html += `
                <div class="col-md-3 col-sm-6">
                    <div class="serial-item-cell">
                        <span class="serial-idx">#${String(i + 1).padStart(2, '0')}</span>
                        <input type="text" class="serial-cell-input modal-serial-input" value="${val}" placeholder="IMEI / Serial No" oninput="updateModalSerialCounter()">
                    </div>
                </div>
            `;
        }
        container.html(html);
        updateModalSerialCounter();
    }

    function updateModalSerialCounter() {
        const row = $(`#${activeSerialRowId}`);
        const targetQty = parseInt(row.find('.row-serial-qty').val()) || 1;
        const inputs = document.querySelectorAll('.modal-serial-input');
        const filled = Array.from(inputs).map(i => i.value.trim()).filter(v => v.length > 0);

        $('#modalSerialCounter').text(`${filled.length} / ${targetQty} Entered`);
        if (filled.length === targetQty) {
            $('#modalSerialCounter').removeClass('bg-warning text-dark').addClass('bg-success text-white');
        } else {
            $('#modalSerialCounter').removeClass('bg-success text-white').addClass('bg-warning text-dark');
        }
    }

    function switchSerialModalView(view) {
        if (view === 'grid') {
            $('#gridTabBtn').addClass('active');
            $('#bulkTabBtn').removeClass('active');
            $('#modalSerialGridView').removeClass('d-none');
            $('#modalSerialBulkView').addClass('d-none');
        } else {
            $('#bulkTabBtn').addClass('active');
            $('#gridTabBtn').removeClass('active');
            $('#modalSerialBulkView').removeClass('d-none');
            $('#modalSerialGridView').addClass('d-none');
        }
    }

    function applyModalBulkText() {
        const text = $('#modalBulkTextarea').val();
        const list = text.split(/[\r\n,]+/).map(s => s.trim()).filter(s => s.length > 0);

        if (list.length === 0) {
            Swal.fire({icon: 'warning', title: 'Empty Input', text: 'Please paste serial numbers in the box.'});
            return;
        }

        const row = $(`#${activeSerialRowId}`);
        row.find('.row-serial-qty').val(list.length);

        renderModalSerialGrid(list.length, list);
        switchSerialModalView('grid');

        Swal.fire({
            icon: 'success',
            title: 'IMEIs Applied',
            text: `${list.length} IMEIs applied to grid! Click "Save IMEIs to Row" to confirm.`,
            timer: 1500,
            showConfirmButton: false
        });
    }

    function saveModalSerialsToRow() {
        if (!activeSerialRowId) return;

        // Auto-apply bulk text if bulk textarea has text
        const bulkText = $('#modalBulkTextarea').val();
        if (bulkText && bulkText.trim().length > 0) {
            const bulkList = bulkText.split(/[\r\n,]+/).map(s => s.trim()).filter(s => s.length > 0);
            if (bulkList.length > 0) {
                const existingInputs = document.querySelectorAll('.modal-serial-input');
                const existingFilled = Array.from(existingInputs).map(i => i.value.trim()).filter(v => v.length > 0);
                if (existingFilled.length === 0 || $('#bulkTabBtn').hasClass('active')) {
                    const row = $(`#${activeSerialRowId}`);
                    row.find('.row-serial-qty').val(bulkList.length);
                    renderModalSerialGrid(bulkList.length, bulkList);
                }
            }
        }

        const row = $(`#${activeSerialRowId}`);
        const inputs = document.querySelectorAll('.modal-serial-input');
        const serials = Array.from(inputs).map(i => i.value.trim()).filter(v => v.length > 0);

        if (serials.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No IMEIs Entered',
                text: 'Please enter or paste at least one IMEI / Serial number before saving.'
            });
            return;
        }

        // Update hidden inputs in row
        const holder = row.find('.row-serials-hidden-holder');
        holder.empty();

        serials.forEach(s => {
            holder.append(`<input type="hidden" name="items[${activeSerialRowId}][serials][]" value="${s}">`);
        });

        row.find('.row-serial-count').text(serials.length);
        row.find('.row-serial-qty').val(serials.length);

        calcRowTotal(activeSerialRowId);

        closeRowSerialModal();

        Swal.fire({
            icon: 'success',
            title: 'IMEIs Saved!',
            text: `${serials.length} IMEI / Serial number(s) attached to product row!`,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1800
        });
    }

    function clearModalSerials() {
        $('.modal-serial-input').val('');
        updateModalSerialCounter();
    }

    function syncSerialRowQty(rowId) {
        calcRowTotal(rowId);
    }

    /* SUBMIT ALL MULTI-ITEM OPENING STOCKS BATCH */
    function submitBatchOpeningStock() {
        const rows = $('#multiStockTbody tr');
        if (rows.length === 0) {
            Swal.fire({icon: 'warning', title: 'No Rows Added', text: 'Please add at least one product row to submit opening stock.'});
            return;
        }

        const globalWh = $('#globalWarehouseSelect').val();
        const payloadItems = [];

        let isValid = true;
        let validationMsg = '';

        rows.each(function(idx) {
            const rowId = $(this).attr('data-row-id');
            const pId = $(this).find('.row-product-select').val();
            const vKey = $(this).find('.row-variant-key-hidden').val() || null;
            const mode = $(this).find('.row-mode-select').val();

            if (!pId) {
                isValid = false;
                validationMsg = `Row #${idx + 1}: Please select a product.`;
                return false;
            }

            let q = 0;
            let c = 0;
            let batchNo = null;
            let mfgDate = null;
            let expDate = null;
            let serials = [];
            let rowRemarks = $(this).find('.row-remarks-input:visible').val() || $(this).find('.row-remarks-input').first().val() || 'Multi-Product Opening Stock Terminal Entry';

            if (mode === 'simple') {
                q = parseFloat($(this).find('.row-simple-panel .row-qty-input').val()) || 0;
                c = parseFloat($(this).find('.row-simple-panel .row-cost-input').val()) || 0;
            } else if (mode === 'batch') {
                batchNo = $(this).find('.row-batch-no').val();
                mfgDate = $(this).find('input[name="items['+rowId+'][mfg_date]"]').val();
                expDate = $(this).find('input[name="items['+rowId+'][expiry_date]"]').val();
                q = parseFloat($(this).find('.row-batch-panel .row-qty-input').val()) || 0;
                c = parseFloat($(this).find('.row-batch-panel .row-cost-input').val()) || 0;

                if (!batchNo) {
                    isValid = false;
                    validationMsg = `Row #${idx + 1}: Batch Number is required for Batch Wise tracking.`;
                    return false;
                }
            } else if (mode === 'serial') {
                c = parseFloat($(this).find('.row-serial-panel .row-cost-input').val()) || 0;
                $(this).find('.row-serials-hidden-holder input').each(function() {
                    serials.push($(this).val());
                });
                q = serials.length;

                if (q === 0) {
                    isValid = false;
                    validationMsg = `Row #${idx + 1}: Please click "Manage IMEIs" to enter IMEIs / Serial numbers.`;
                    return false;
                }
            }

            if (q <= 0) {
                isValid = false;
                validationMsg = `Row #${idx + 1}: Quantity must be greater than 0.`;
                return false;
            }

            payloadItems.push({
                product_id: pId,
                variant_key: vKey,
                warehouse_id: globalWh,
                tracking_type: mode,
                qty: q,
                cost_price: c,
                batch_no: batchNo,
                mfg_date: mfgDate,
                expiry_date: expDate,
                serials: serials,
                remarks: rowRemarks
            });
        });

        if (!isValid) {
            Swal.fire({icon: 'warning', title: 'Input Error', text: validationMsg});
            return;
        }

        const btn = $('#saveAllOpeningStockBtn');
        const origHtml = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Saving All...').prop('disabled', true);

        fetch("{{ route('opening_stock.store_batch') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: JSON.stringify({ items: payloadItems })
        })
        .then(r => r.json().then(data => ({status: r.status, body: data})))
        .then(({status, body}) => {
            if (status === 200 && body.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved Successfully!',
                    text: body.message || 'All Opening Stock entries saved successfully!',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    html: body.message || 'Error processing batch opening stock.'
                });
            }
        })
        .catch(err => {
            Swal.fire({icon: 'error', title: 'Server Error', text: 'An unexpected server error occurred.'});
        })
        .finally(() => {
            btn.html(origHtml).prop('disabled', false);
        });
    }
</script>
@endsection
