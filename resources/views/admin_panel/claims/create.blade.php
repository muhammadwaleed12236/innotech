@extends('admin_panel.layout.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<style>
    :root {
        --erp-blue: #2563EB;
        --erp-blue-soft: #EFF6FF;
        --erp-green: #16A34A;
        --erp-green-soft: #F0FDF4;
        --erp-red: #DC2626;
        --erp-red-soft: #FEF2F2;
        --erp-amber: #D97706;
        --erp-amber-soft: #FFFBEB;
        --erp-text: #0F172A;
        --erp-muted: #64748B;
        --erp-border: #E2E8F0;
        --erp-bg: #F8FAFC;
        --erp-card: #FFFFFF;
        --erp-radius: 6px;
        --erp-radius-lg: 10px;
        --erp-input-h: 36px;
    }

    body {
        background-color: var(--erp-bg) !important;
        font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        color: var(--erp-text) !important;
    }

    /* ---------- ERP CONTAINER & COMPACT SPACING ---------- */
    .claims-wrapper {
        padding-top: 10px;
        padding-bottom: 25px;
    }

    .erp-header-card {
        background: #ffffff;
        border: 1px solid var(--erp-border);
        border-radius: var(--erp-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    .erp-field-label {
        display: flex;
        align-items: center;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        color: #475569;
        margin-bottom: 4px;
        white-space: nowrap;
    }

    /* ---------- STATUS STRIP & ALLOWANCE HUD ---------- */
    .erp-status-strip {
        background: #F8FAFC;
        border-radius: 7px;
        padding: 5px 10px;
        border: 1px solid var(--erp-border);
    }
    .erp-kpi-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        line-height: 1.2;
        border: 1px solid transparent;
        white-space: nowrap;
    }
    .erp-kpi-pill .kpi-title {
        font-size: 10.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .erp-kpi-pill .kpi-val {
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }
    .erp-kpi-warning {
        background: #FFFBEB;
        color: #B45309;
        border-color: #FDE68A;
    }
    .erp-kpi-danger {
        background: #FEF2F2;
        color: #DC2626;
        border-color: #FECACA;
    }
    .erp-kpi-success {
        background: #F0FDF4;
        color: #15803D;
        border-color: #BBF7D0;
    }

    /* ---------- FORM CONTROLS ---------- */
    .form-control, .form-select {
        height: var(--erp-input-h);
        border: 1px solid var(--erp-border);
        border-radius: var(--erp-radius);
        font-size: 12.5px;
        color: var(--erp-text);
        background-color: #FFFFFF;
        padding: 4px 10px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #2563EB;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .12);
        outline: 0;
    }
    .form-control-sm, .form-select-sm {
        height: 32px;
        font-size: 12px;
        padding: 3px 8px;
    }

    /* ---------- SELECT2 CUSTOMIZATION ---------- */
    .select2-container .select2-selection--single {
        height: var(--erp-input-h) !important;
        border: 1px solid var(--erp-border) !important;
        border-radius: var(--erp-radius) !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--erp-text) !important;
        font-size: 12.5px !important;
        line-height: var(--erp-input-h) !important;
        padding-left: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: var(--erp-input-h) !important;
        right: 6px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #2563EB !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .12) !important;
    }

    /* Grouped Customer Search + Action Buttons */
    .customer-input-group {
        display: flex;
        flex-wrap: nowrap !important;
        align-items: center;
        width: 100%;
    }
    .customer-input-group #customerInputWrapper {
        flex: 1 1 auto;
        min-width: 0;
    }
    .customer-input-group #customerInputWrapper .select2-container--default .select2-selection--single {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        border-right: none !important;
    }
    .customer-input-group #btnOpenAddCustomerModal {
        border-radius: 0 !important;
        border-left: 1px solid var(--erp-border) !important;
        height: var(--erp-input-h) !important;
        padding: 0 10px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .customer-input-group #btnToggleCustomerInfo {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        border-left: 1px solid var(--erp-border) !important;
        height: var(--erp-input-h) !important;
        padding: 0 10px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* ---------- UNIFIED PRODUCT SEARCH BAR ---------- */
    .barcode-search-wrap {
        position: relative;
        width: 100%;
    }
    .barcode-search-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        font-size: 14px;
        pointer-events: none;
        z-index: 2;
    }
    .barcode-search-wrap .select2-container .select2-selection--single {
        height: 36px !important;
        border-radius: 6px !important;
        background-color: #FFFFFF !important;
    }
    .barcode-search-wrap .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 30px !important;
        font-size: 12.5px !important;
        line-height: 34px !important;
    }

    /* ---------- POS / ERP COMPACT TABLE ---------- */
    .pos-table-wrap {
        overflow-x: auto;
        border: 1px solid #CBD5E1;
        border-radius: 6px;
        background: #ffffff;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .claim-table {
        margin-bottom: 0;
        width: 100%;
        border-collapse: collapse !important;
        font-size: 12px;
    }
    .claim-table thead th {
        background: #F1F5F9 !important;
        color: #334155 !important;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 7px 6px !important;
        border-bottom: 2px solid #CBD5E1 !important;
        border-right: 1px solid #E2E8F0 !important;
        text-align: center;
        white-space: nowrap;
    }
    .claim-table thead th:last-child {
        border-right: none !important;
    }
    .claim-table tbody td {
        padding: 5px 6px !important;
        vertical-align: middle;
        border-bottom: 1px solid #E2E8F0 !important;
        border-right: 1px solid #F1F5F9 !important;
        background: #FFFFFF;
    }
    .claim-table tbody td:last-child {
        border-right: none !important;
    }
    .claim-table tbody tr:hover td {
        background: #FFF7ED !important;
    }
    .claim-table tfoot td {
        background: #F8FAFC !important;
        padding: 7px 8px !important;
        font-weight: 700;
        border-top: 2px solid #CBD5E1 !important;
        font-size: 12px;
    }

    /* Compact Inputs inside Table Cells */
    .tbl-input {
        height: 28px !important;
        padding: 2px 6px !important;
        font-size: 12px !important;
        border: 1px solid #CBD5E1 !important;
        border-radius: 4px !important;
        text-align: right;
        font-weight: 600;
        width: 100%;
        background: #FFFFFF;
        font-family: inherit;
    }
    .tbl-input:focus {
        border-color: #2563EB !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .15) !important;
        outline: 0;
    }
    .tbl-readonly {
        background: #F8FAFC !important;
        color: #1E293B !important;
        border: 1px solid #E2E8F0 !important;
        cursor: default;
    }

    /* ---------- SETTLEMENT SELECTION TILES ---------- */
    .settle-card {
        cursor: pointer;
        border: 1.5px solid #E2E8F0;
        border-radius: 8px;
        padding: 9px 12px;
        background: #FFFFFF;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .settle-card:hover {
        border-color: #CBD5E1;
        background: #F8FAFC;
    }
    .settle-card.active {
        border-color: #DC2626;
        background: #FEF2F2;
    }
    .settle-card input[type="radio"] {
        display: none;
    }

    /* ---------- SUMMARY CARD ---------- */
    .summary-card {
        background: #FFFFFF;
        border: 1px solid var(--erp-border);
        border-radius: var(--erp-radius-lg);
        padding: 14px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 0;
        font-size: 12.5px;
        color: #475569;
    }
    .summary-row.total-row {
        border-top: 1.5px dashed #CBD5E1;
        margin-top: 6px;
        padding-top: 9px;
        font-size: 16px;
        font-weight: 800;
        color: #DC2626;
    }

    /* ---------- RECONCILIATION RULE BOX ---------- */
    .reconciliation-rule-box {
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        border-radius: 6px;
        padding: 8px 11px;
        font-size: 11.5px;
        color: #92400E;
        line-height: 1.4;
    }
</style>

<div class="container-fluid claims-wrapper">

    {{-- ============================ TOP NAVIGATION BAR ============================ --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger px-2 py-0 text-uppercase" style="font-size: 9.5px; letter-spacing: 0.4px;">Quarantine &bull; Claims</span>
                <span class="badge bg-light text-dark border px-2 py-0 font-monospace" style="font-size: 11px;">{{ $nextClaimNo }}</span>
            </div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="fas fa-exclamation-triangle text-danger"></i> Customer Claim &amp; Damage Voucher
            </h5>
            <p class="text-muted mb-0" style="font-size: 11.5px;">
                Customer damage/expiry intake, customer balance settlement, and Company 3-tier discount allowance reconciliation
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('claims.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm fw-medium d-inline-flex align-items-center gap-1" style="height: 34px; border-radius: 6px;">
                <i class="fas fa-list text-muted me-1"></i> Claims Register
            </a>
            <a href="{{ route('claims.report') }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm fw-medium d-inline-flex align-items-center gap-1" style="height: 34px; border-radius: 6px;">
                <i class="fas fa-chart-pie text-primary me-1"></i> Allowance Report
            </a>
        </div>
    </div>

    <form id="claimDamageForm" action="{{ route('claims.store') }}" method="POST">
        @csrf

        {{-- ============================ CARD 1: VOUCHER CONFIG & ALLOWANCE HUD ============================ --}}
        <div class="erp-header-card p-3 mb-2">
            {{-- Row 1: The 5 core selection inputs in single responsive grid (4 + 3 + 2 + 1 + 2 = 12 cols) --}}
            <div class="row g-2 align-items-end mb-2">
                {{-- 1. Customer Selection --}}
                <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                    <label class="erp-field-label" for="customerSelect">
                        <i class="fas fa-user-tag text-primary me-1"></i> Customer <span class="text-danger ms-1">*</span>
                    </label>
                    <div class="input-group customer-input-group">
                        <div id="customerInputWrapper" style="min-width: 0; width: 100%;">
                            <select class="form-select select2-customer" id="customerSelect" name="customer_id" style="width:100%" required>
                                <option value="">Search Customer by Name, Mobile, Code...</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" 
                                            data-mobile="{{ $c->mobile ?? $c->phone ?? '' }}" 
                                            data-address="{{ $c->address ?? '' }}" 
                                            data-balance="{{ $c->current_balance ?? $c->previous_balance ?? 0 }}"
                                            data-sales-officer="{{ $c->sales_officer_id ?? '' }}">
                                        {{ $c->customer_id ? '['.$c->customer_id.'] ' : '' }}{{ $c->customer_name ?? $c->name }} {{ ($c->mobile ?? $c->phone) ? '('.($c->mobile ?? $c->phone).')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" id="btnOpenAddCustomerModal"
                                class="btn btn-outline-primary"
                                data-bs-toggle="modal" data-bs-target="#addCustomerModal"
                                title="Quick Add Customer (Alt+C or F2)">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" id="btnToggleCustomerInfo"
                                class="btn btn-outline-secondary"
                                data-bs-toggle="modal" data-bs-target="#modalCustomerDetails"
                                title="View Customer Ledger & Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- 2. Vendor / Company Selection --}}
                <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                    <label class="erp-field-label" for="vendorSelect">
                        <i class="fas fa-building text-warning me-1"></i> Vendor / Principal Company
                    </label>
                    <select name="vendor_id" id="vendorSelect" class="form-select select2-vendor" style="width: 100%;">
                        <option value="">Select Company / Vendor...</option>
                        @foreach ($vendors as $v)
                            <option value="{{ $v->id }}">{{ $v->name }} {{ $v->phone ? '('.$v->phone.')' : '' }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Quarantine Deposit Warehouse --}}
                <div class="col-6 col-md-4 col-lg-2 col-xl-2">
                    <label class="erp-field-label" for="warehouseSelect">
                        <i class="fas fa-warehouse text-danger me-1"></i> Quarantine Warehouse <span class="text-danger">*</span>
                    </label>
                    <select name="warehouse_id" id="warehouseSelect" class="form-select form-select-sm" style="height: 36px;" required>
                        @foreach ($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ (isset($damageWarehouse) && $wh->id == $damageWarehouse->id) ? 'selected' : '' }}>
                                {{ $wh->warehouse_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. Primary Claim Reason --}}
                <div class="col-6 col-md-4 col-lg-1 col-xl-1">
                    <label class="erp-field-label" for="claimTypeSelect">
                        <i class="fas fa-tag text-secondary me-1"></i> Reason
                    </label>
                    <select name="claim_type" id="claimTypeSelect" class="form-select form-select-sm" style="height: 36px;">
                        <option value="damage" selected>Damage</option>
                        <option value="expiry">Expiry</option>
                        <option value="leakage">Leakage</option>
                        <option value="defect">Defect</option>
                        <option value="claim">Claim</option>
                    </select>
                </div>

                {{-- 5. Claim Date --}}
                <div class="col-12 col-md-4 col-lg-2 col-xl-2">
                    <label class="erp-field-label">
                        <i class="fas fa-calendar-alt text-secondary me-1"></i> Date
                    </label>
                    <input type="date" name="claim_date" class="form-control" value="{{ date('Y-m-d') }}" style="height: 36px;" required>
                </div>
            </div>

            {{-- Row 2: Customer Due Info & 3-Tier Allowance Ribbon --}}
            <div class="erp-status-strip d-flex flex-wrap justify-content-between align-items-center gap-2">
                {{-- Left: Customer Ledger Snapshot --}}
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="badge bg-white text-secondary border px-2 py-1" style="font-size: 11px;">
                        <i class="fas fa-user text-primary me-1"></i> <span id="custBadgeName">Select Customer...</span>
                    </span>
                    <span class="badge bg-white text-danger border px-2 py-1" style="font-size: 11px;">
                        <i class="fas fa-money-bill-alt text-danger me-1"></i> Due Balance: <strong id="custBadgeBal">Rs 0.00</strong>
                    </span>
                </div>

                {{-- Right: Live 3-Tier Company Allowance HUD --}}
                <div class="d-flex align-items-center flex-wrap gap-2 ms-auto">
                    <div class="erp-kpi-pill erp-kpi-warning" title="Company Damage/Claim Allowance Fund (e.g. 5% discount allowance)">
                        <span class="kpi-title">Company Allowance:</span>
                        <span class="kpi-val" id="kpiCompanyAllowance">Rs 0.00</span>
                    </div>

                    <div class="erp-kpi-pill erp-kpi-danger" title="Total current customer damage claim value to deduct">
                        <span class="kpi-title">This Claim:</span>
                        <span class="kpi-val" id="kpiClaimValue">Rs 0.00</span>
                    </div>

                    <div class="erp-kpi-pill erp-kpi-success" title="Remaining Balance payable to Vendor after deduction">
                        <span class="kpi-title">Remaining Payable:</span>
                        <span class="kpi-val" id="kpiRemainingBalance">Rs 0.00</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================ CARD 2: UNIFIED ITEMS SECTION & PRODUCT SEARCH ============================ --}}
        <div class="erp-header-card p-3 mb-2">
            {{-- Toolbar: Search Bar on Left, Action Buttons on Right (Unified in same card) --}}
            <div class="row g-2 align-items-center mb-2">
                <div class="col-12 col-md-7 col-lg-8">
                    <div class="barcode-search-wrap">
                        <i class="fas fa-barcode barcode-search-icon"></i>
                        <select id="productSearchSelect" class="form-control" style="width: 100%;">
                            <option value="">Scan Barcode or Search Product by Name, SKU, Code (Press F2)...</option>
                        </select>
                    </div>
                </div>

                <div class="col-12 col-md-5 col-lg-4 text-md-end text-start d-flex align-items-center justify-content-md-end gap-2">
                    <span class="badge bg-light text-muted border py-1 px-2 d-none d-sm-inline-block" style="font-size: 11px; height: 32px; line-height: 22px;">
                        <i class="fas fa-keyboard text-secondary me-1"></i> <strong>F2</strong>
                    </span>
                    <button type="button" class="btn btn-outline-danger btn-sm px-3 d-inline-flex align-items-center gap-1" id="btnOpenProductDrawer" data-bs-toggle="offcanvas" data-bs-target="#productSearchDrawer" style="height: 34px; border-radius: 6px;">
                        <i class="fas fa-th"></i> Catalog
                    </button>
                    <button type="button" class="btn btn-danger btn-sm px-3 d-inline-flex align-items-center gap-1 shadow-sm" id="btnAddNewRow" style="height: 34px; border-radius: 6px;">
                        <i class="fas fa-plus"></i> Add Row
                    </button>
                </div>
            </div>

            {{-- The Table inside the Table Wrapper --}}
            <div class="pos-table-wrap">
                <table class="table claim-table align-middle" id="claimItemsTable">
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th style="min-width: 180px; text-align: left;">Product / Variant Description</th>
                            <th style="width: 110px;">Fault / Reason</th>
                            <th style="width: 95px;">Batch / Exp</th>
                            <th style="width: 70px;">Packing</th>
                            <th style="width: 65px;">Damage Ctn</th>
                            <th style="width: 65px;">Loose Pcs</th>
                            <th style="width: 70px;">Total Pcs</th>
                            <th style="width: 85px;">Unit Rate (Rs)</th>
                            <th style="width: 95px;">Line Total (Rs)</th>
                            <th style="width: 35px;">Act</th>
                        </tr>
                    </thead>
                    <tbody id="claimItemsBody">
                        <tr id="emptyRowPlaceholder">
                            <td colspan="11" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                <span class="fw-semibold" style="font-size: 12.5px;">No damaged items added to voucher yet.</span>
                                <div class="small text-muted mt-1" style="font-size: 11px;">Scan a barcode, search product above (F2), or click <strong>Catalog</strong> to pick items.</div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-start ps-2">
                                <span class="text-muted text-uppercase" style="font-size: 10.5px;">Total Items:</span>
                                <strong id="footerTotalItemsCount" class="ms-1 text-dark font-monospace">0</strong>
                            </td>
                            <td colspan="3" class="text-end text-muted small text-uppercase" style="font-size: 10.5px;">Subtotals:</td>
                            <td class="text-center">
                                <span id="footerTotalCartons" class="fw-bold text-dark font-monospace">0</span>
                            </td>
                            <td class="text-center">
                                <span id="footerTotalLoose" class="fw-bold text-dark font-monospace">0</span>
                            </td>
                            <td class="text-center">
                                <span id="footerTotalPieces" class="fw-bold text-danger font-monospace">0</span>
                            </td>
                            <td class="text-end text-muted small text-uppercase" style="font-size: 10.5px;">Net Claim:</td>
                            <td class="text-end pe-2">
                                <span id="footerGrandTotal" class="fw-bold text-danger font-monospace">Rs 0.00</span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ============================ CARD 3: SETTLEMENT MODE & FINANCIAL SUMMARY ============================ --}}
        <div class="row g-2 mb-3">
            {{-- Left: Customer Settlement Mode & Remarks --}}
            <div class="col-12 col-lg-7">
                <div class="summary-card h-100">
                    <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2" style="font-size: 13px;">
                        <i class="fas fa-hand-holding-usd text-primary"></i> Customer Settlement &amp; Disbursement Mode
                    </h6>

                    <div class="row g-2 mb-2">
                        <div class="col-12 col-md-4">
                            <label class="settle-card active w-100" id="labelSettleLedger">
                                <input type="radio" name="settlement_type" value="ledger_credit" checked>
                                <div class="text-primary fs-5"><i class="fas fa-file-invoice-dollar"></i></div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 12px;">Ledger Credit</div>
                                    <div class="text-muted" style="font-size: 10.5px;">Customer Udhaar Kam</div>
                                </div>
                            </label>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="settle-card w-100" id="labelSettleCash">
                                <input type="radio" name="settlement_type" value="cash_refund">
                                <div class="text-success fs-5"><i class="fas fa-money-bill-wave"></i></div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 12px;">Cash / Bank Refund</div>
                                    <div class="text-muted" style="font-size: 10.5px;">Disburse Refund Now</div>
                                </div>
                            </label>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="settle-card w-100" id="labelSettleReplacement">
                                <input type="radio" name="settlement_type" value="replacement">
                                <div class="text-warning fs-5"><i class="fas fa-exchange-alt"></i></div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 12px;">Stock Replace</div>
                                    <div class="text-muted" style="font-size: 10.5px;">Exchange with Fresh</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Cash Refund Details Box (Expands when Cash Refund selected) --}}
                    <div id="cashRefundSection" class="p-2 bg-light rounded-2 border mb-2" style="display: none;">
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <label class="erp-field-label">Disbursement Account <span class="text-danger">*</span></label>
                                <select name="refund_account_id" id="refundAccountId" class="form-select form-select-sm">
                                    <option value="">Select Payment Account...</option>
                                    @foreach ($payment_accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->title }} ({{ $acc->head->name ?? 'Account' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="erp-field-label">Immediate Refund Paid (Rs) <span class="text-danger">*</span></label>
                                <input type="number" step="any" name="refund_amount" id="refundAmountInput" class="form-control form-control-sm fw-bold text-danger text-end font-monospace" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="erp-field-label">Claim Notes &amp; Inspector Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2" style="height: 52px; font-size: 12px;" placeholder="e.g. Received from Customer, batch expired, company return voucher pending..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Right: Executive Reconciliation Summary Deck --}}
            <div class="col-12 col-lg-5">
                <div class="summary-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2" style="font-size: 13px;">
                            <i class="fas fa-calculator text-danger"></i> Voucher Financial Summary
                        </h6>

                        <div class="summary-row">
                            <span class="text-muted">Total Claim Products:</span>
                            <span class="fw-bold font-monospace text-dark" id="summaryTotalItems">0</span>
                        </div>

                        <div class="summary-row">
                            <span class="text-muted">Total Damaged Pieces:</span>
                            <span class="fw-bold font-monospace text-dark" id="summaryTotalPieces">0</span>
                        </div>

                        <div class="summary-row">
                            <span class="text-muted">Company Allowance Pool (5% Fund):</span>
                            <span class="fw-bold font-monospace text-warning" id="summaryCompanyAllowance">Rs 0.00</span>
                        </div>

                        <div class="summary-row total-row">
                            <span>Total Claim Voucher:</span>
                            <span class="font-monospace" id="summaryTotalClaim">Rs 0.00</span>
                        </div>

                        {{-- 3-Tier Allowance Rule Notice (As specifically requested by user) --}}
                        <div class="reconciliation-rule-box mt-2 mb-0" id="reconciliationRuleBox">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-info-circle text-warning fs-6 mt-1"></i>
                                <div id="companySettlementRuleText">
                                    Vendor select karne par Company 3-Tier Allowance (5% Damage Pool) aur baaqi payable balance live calculate hoga.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-2 border-top">
                        <button type="submit" class="btn btn-danger w-100 py-2 fs-6 shadow-sm fw-bold d-flex align-items-center justify-content-center gap-2" id="btnSubmitClaim" style="border-radius: 6px; height: 40px;">
                            <i class="fas fa-check-circle"></i> Post &amp; Process Customer Claim
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

{{-- ============================ QUICK PRODUCT OFFCANVAS CATALOG ============================ --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="productSearchDrawer" aria-labelledby="productSearchDrawerLabel" style="width: 380px;">
    <div class="offcanvas-header border-bottom py-2 bg-light">
        <h6 class="offcanvas-title fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size: 13.5px;">
            <i class="fas fa-th text-danger"></i> Product Catalog
        </h6>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-2">
        <div class="input-group input-group-sm mb-2">
            <input type="text" class="form-control" id="sidebarProductSearch" placeholder="Search catalog by name, code, SKU...">
            <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
        </div>

        <div class="overflow-auto pe-1" id="sidebarProductContainer" style="max-height: calc(100vh - 120px);">
            @if(isset($allProducts) && count($allProducts) > 0)
                @foreach($allProducts as $prod)
                    <div class="p-2 mb-2 border rounded bg-white shadow-sm d-flex justify-content-between align-items-center product-drawer-item"
                         data-id="{{ $prod->id }}"
                         data-name="{{ $prod->item_name }}"
                         data-sku="{{ $prod->sku ?? $prod->item_code ?? '' }}"
                         data-rate="{{ $prod->trade_price ?? $prod->purchase_price_per_piece ?? $prod->retail_price ?? 0 }}"
                         data-ppb="{{ $prod->pieces_per_box ?? 1 }}"
                         data-unit="{{ $prod->unit_name ?? 'Pcs' }}">
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 12.5px;">{{ $prod->item_name }}</div>
                            <small class="text-muted d-block font-monospace" style="font-size: 11px;">SKU: {{ $prod->sku ?? $prod->item_code ?? 'N/A' }} | PPB: {{ $prod->pieces_per_box ?? 1 }}</small>
                            <span class="badge bg-light text-primary border" style="font-size: 10.5px;">Rs {{ number_format($prod->trade_price ?? $prod->purchase_price_per_piece ?? $prod->retail_price ?? 0, 2) }}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-add-drawer-product px-2 py-1" style="font-size: 11px;">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4 text-muted small">No active products found.</div>
            @endif
        </div>
    </div>
</div>

{{-- ============================ QUICK ADD CUSTOMER MODAL ============================ --}}
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 bg-primary text-white">
                <h6 class="modal-title mb-0 fw-bold" style="font-size: 13.5px;"><i class="fas fa-user-plus me-2"></i>Quick Add Customer</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formQuickAddCustomer">
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label font-weight-bold fw-bold mb-1" style="font-size: 11.5px;">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="customer_name" required placeholder="Enter customer name...">
                        </div>
                        <div class="col-6">
                            <label class="form-label font-weight-bold fw-bold mb-1" style="font-size: 11.5px;">Mobile / Phone</label>
                            <input type="text" class="form-control form-control-sm" name="mobile" placeholder="0300-1234567">
                        </div>
                        <div class="col-6">
                            <label class="form-label font-weight-bold fw-bold mb-1" style="font-size: 11.5px;">Opening Balance</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" name="opening_balance" value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label font-weight-bold fw-bold mb-1" style="font-size: 11.5px;">Address / Area</label>
                            <input type="text" class="form-control form-control-sm" name="address" placeholder="Address...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold" id="btnSaveQuickCustomer">
                        <i class="fas fa-save me-1"></i> Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================ CUSTOMER DETAILS MODAL ============================ --}}
<div class="modal fade" id="modalCustomerDetails" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title mb-0 fw-bold" style="font-size: 13.5px;"><i class="fas fa-id-card me-2"></i>Customer Account Snapshot</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2" style="font-size: 12.5px;">
                    <span class="text-muted">Customer Name:</span>
                    <strong class="text-dark" id="modalCustName">—</strong>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2" style="font-size: 12.5px;">
                    <span class="text-muted">Mobile / Phone:</span>
                    <span id="modalCustPhone">—</span>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2" style="font-size: 12.5px;">
                    <span class="text-muted">Address:</span>
                    <span id="modalCustAddress">—</span>
                </div>
                <div class="d-flex justify-content-between pt-1" style="font-size: 12.5px;">
                    <span class="text-muted">Current Ledger Balance:</span>
                    <strong class="font-monospace text-danger fs-6" id="modalCustBal">Rs 0.00</strong>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let rowIndex = 0;
    let vendorAllowanceData = {
        vendor_payable: 0,
        company_allowance: 0,
        remaining_allowance: 0
    };

    // Initialize Select2 Controls
    $('.select2-customer').select2({
        placeholder: "Search Customer by Name, Mobile, Code...",
        allowClear: true,
        width: '100%'
    });

    $('.select2-vendor').select2({
        placeholder: "Select Company / Vendor...",
        allowClear: true,
        width: '100%'
    });

    // Customer Selection Change Listener
    $('#customerSelect').on('change', function() {
        const $opt = $(this).find('option:selected');
        const cid = $(this).val();

        if (cid && $opt.length) {
            const name = $opt.text().trim();
            const phone = $opt.attr('data-mobile') || '—';
            const address = $opt.attr('data-address') || '—';
            const bal = parseFloat($opt.attr('data-balance') || 0);

            $('#custBadgeName').text(name);
            $('#custBadgeBal').text('Rs ' + bal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            $('#modalCustName').text(name);
            $('#modalCustPhone').text(phone);
            $('#modalCustAddress').text(address);
            $('#modalCustBal').text('Rs ' + bal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        } else {
            $('#custBadgeName').text('Select Customer...');
            $('#custBadgeBal').text('Rs 0.00');
            $('#modalCustName, #modalCustPhone, #modalCustAddress').text('—');
            $('#modalCustBal').text('Rs 0.00');
        }
    });

    // Settlement Mode Tile Clicks
    $('.settle-card').on('click', function() {
        $('.settle-card').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
    });

    $('input[name="settlement_type"]').on('change', function() {
        if ($(this).val() === 'cash_refund') {
            $('#cashRefundSection').slideDown(200);
            const total = calculateTotalClaimAmount();
            $('#refundAmountInput').val(total > 0 ? total.toFixed(2) : '');
        } else {
            $('#cashRefundSection').slideUp(200);
            $('#refundAmountInput').val('');
        }
    });

    // Vendor / Company Change -> Live Allowance Reconcile
    $('#vendorSelect').on('change', function() {
        const vendorId = $(this).val();
        if (!vendorId) {
            vendorAllowanceData = { vendor_payable: 0, company_allowance: 0, remaining_allowance: 0 };
            recalculateGrandTotals();
            return;
        }

        $.ajax({
            url: `/claims/vendor-allowance/${vendorId}`,
            method: 'GET',
            success: function(res) {
                vendorAllowanceData = res || { vendor_payable: 0, company_allowance: 0, remaining_allowance: 0 };
                recalculateGrandTotals();
            },
            error: function() {
                vendorAllowanceData = { vendor_payable: 0, company_allowance: 0, remaining_allowance: 0 };
                recalculateGrandTotals();
            }
        });
    });

    // AJAX Product Search
    $('#productSearchSelect').select2({
        placeholder: "Scan Barcode or Search Product by Name, SKU, Code (Press F2)...",
        minimumInputLength: 1,
        width: '100%',
        ajax: {
            url: "{{ route('products.ajax.search') }}",
            dataType: 'json',
            delay: 150,
            data: function(params) {
                return {
                    q: params.term || '',
                    is_return: 1,
                    allow_zero_stock: 1
                };
            },
            processResults: function(data) {
                return {
                    results: (data.results || data || []).map(function(item) {
                        return {
                            id: item.id,
                            text: (item.item_code ? '['+item.item_code+'] ' : '') + (item.item_name || item.name || item.text) + (item.variant_name ? ' ('+item.variant_name+')' : ''),
                            data: item
                        };
                    })
                };
            },
            cache: true
        }
    });

    // When Product Selected in Search
    $('#productSearchSelect').on('select2:select', function(e) {
        const item = e.params.data.data || e.params.data;
        addProductRow(item);
        $(this).val(null).trigger('change');
    });

    // Keyboard Shortcuts
    $(document).on('keydown', function(e) {
        if (e.key === 'F2') {
            e.preventDefault();
            $('#productSearchSelect').select2('open');
        }
    });

    $('#btnAddNewRow').on('click', function() {
        $('#productSearchSelect').select2('open');
    });

    // Add Product Row function
    function addProductRow(item) {
        $('#emptyRowPlaceholder').hide();
        rowIndex++;

        const ppb = parseFloat(item.pieces_per_box || item.ppb || 1) || 1;
        let rate = parseFloat(item.trade_price || item.purchase_price_per_piece || item.retail_price || 0);
        if (ppb > 1 && item.trade_price_per_box && !item.trade_price) {
            rate = parseFloat(item.trade_price_per_box) / ppb;
        }

        const unitName = item.unit_name || (ppb > 1 ? 'Carton' : 'Pcs');
        const prodName = item.item_name || item.name || item.text;
        const sku = item.sku || item.item_code || 'N/A';

        const rowHtml = `
            <tr data-row-id="${rowIndex}" class="item-row">
                <td class="text-center text-muted fw-bold" style="font-size: 11px;">${rowIndex}</td>
                <td>
                    <div class="fw-bold text-dark" style="font-size: 12px; line-height: 1.2;">${prodName}</div>
                    <small class="text-muted font-monospace" style="font-size: 10.5px;">SKU: ${sku}</small>
                    <input type="hidden" name="product_id[]" value="${item.id}">
                    <input type="hidden" name="unit[]" value="${unitName}">
                    <input type="hidden" name="color[]" value="${item.variant_data || item.color || ''}">
                    <input type="hidden" class="row-ppb" name="pieces_per_box[]" value="${ppb}">
                </td>
                <td>
                    <select name="damage_reason[]" class="form-select form-select-sm" style="height: 28px; font-size: 11.5px; padding: 2px 4px;">
                        <option value="damage" selected>Damaged</option>
                        <option value="expiry">Expired</option>
                        <option value="leakage">Leakage</option>
                        <option value="defect">Defect</option>
                        <option value="claim">Claim</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="batch_no[]" class="tbl-input text-start" placeholder="Batch / Exp...">
                </td>
                <td class="text-center">
                    <span class="badge bg-light text-secondary border px-1 py-1" style="font-size: 10.5px;">
                        ${ppb > 1 ? '1 Ctn=' + ppb : unitName}
                    </span>
                </td>
                <td>
                    <input type="number" step="1" min="0" name="cartons[]" class="tbl-input input-carton" placeholder="0" value="1">
                </td>
                <td>
                    <input type="number" step="1" min="0" name="loose_pieces[]" class="tbl-input input-loose" placeholder="0" value="0">
                </td>
                <td>
                    <input type="number" step="any" min="0" name="qty[]" class="tbl-input tbl-readonly input-total-pcs text-center fw-bold text-danger font-monospace" value="${ppb}" readonly>
                </td>
                <td>
                    <input type="number" step="any" min="0" name="rate[]" class="tbl-input input-rate text-end font-monospace" value="${rate.toFixed(2)}">
                </td>
                <td>
                    <input type="number" step="any" min="0" name="line_total[]" class="tbl-input tbl-readonly input-line-total text-end fw-bold text-dark font-monospace" value="${(ppb * rate).toFixed(2)}" readonly>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm p-0 px-1 btn-remove-row" title="Remove Item" style="height: 24px; width: 24px;">
                        <i class="fas fa-trash-alt" style="font-size: 10px;"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#claimItemsBody').append(rowHtml);
        const $newRow = $('#claimItemsBody tr:last-child');
        calculateRow($newRow);
        recalculateGrandTotals();

        $newRow.find('.input-carton').focus().select();
    }

    // Row Calculation
    function calculateRow($row) {
        const ppb = parseFloat($row.find('.row-ppb').val()) || 1;
        const carton = parseFloat($row.find('.input-carton').val()) || 0;
        const loose = parseFloat($row.find('.input-loose').val()) || 0;
        const rate = parseFloat($row.find('.input-rate').val()) || 0;

        const totalPieces = (carton * ppb) + loose;
        $row.find('.input-total-pcs').val(totalPieces);

        const lineTotal = totalPieces * rate;
        $row.find('.input-line-total').val(lineTotal.toFixed(2));
    }

    // Table Input Event Delegation
    $('#claimItemsBody').on('input change', '.input-carton, .input-loose, .input-rate', function() {
        const $row = $(this).closest('tr');
        calculateRow($row);
        recalculateGrandTotals();
    });

    // Remove row
    $('#claimItemsBody').on('click', '.btn-remove-row', function() {
        $(this).closest('tr').remove();
        if ($('#claimItemsBody tr.item-row').length === 0) {
            $('#emptyRowPlaceholder').show();
        }
        recalculateGrandTotals();
    });

    // Add Product from Catalog Drawer
    $(document).on('click', '.btn-add-drawer-product', function() {
        const $card = $(this).closest('.product-drawer-item');
        const item = {
            id: $card.data('id'),
            item_name: $card.data('name'),
            sku: $card.data('sku'),
            trade_price: $card.data('rate'),
            pieces_per_box: $card.data('ppb'),
            unit_name: $card.data('unit')
        };
        addProductRow(item);

        const $btn = $(this);
        $btn.removeClass('btn-outline-danger').addClass('btn-success').html('<i class="fas fa-check"></i>');
        setTimeout(() => {
            $btn.removeClass('btn-success').addClass('btn-outline-danger').html('<i class="fas fa-plus"></i> Add');
        }, 1200);
    });

    // Drawer Search Filter
    $('#sidebarProductSearch').on('keyup', function() {
        const val = $(this).val().toLowerCase();
        $('.product-drawer-item').each(function() {
            const txt = $(this).text().toLowerCase();
            $(this).toggle(txt.indexOf(val) > -1);
        });
    });

    function calculateTotalClaimAmount() {
        let total = 0;
        $('#claimItemsBody tr.item-row').each(function() {
            total += parseFloat($(this).find('.input-line-total').val()) || 0;
        });
        return total;
    }

    // Grand Totals & 3-Tier Company Allowance Reconcile
    function recalculateGrandTotals() {
        const totalClaim = calculateTotalClaimAmount();
        const itemCount = $('#claimItemsBody tr.item-row').length;

        let totalCartons = 0;
        let totalLoose = 0;
        let totalPieces = 0;

        $('#claimItemsBody tr.item-row').each(function() {
            totalCartons += parseFloat($(this).find('.input-carton').val()) || 0;
            totalLoose += parseFloat($(this).find('.input-loose').val()) || 0;
            totalPieces += parseFloat($(this).find('.input-total-pcs').val()) || 0;
        });

        // Update Footers
        $('#footerTotalItemsCount').text(itemCount);
        $('#footerTotalCartons').text(totalCartons);
        $('#footerTotalLoose').text(totalLoose);
        $('#footerTotalPieces').text(totalPieces);
        $('#footerGrandTotal').text('Rs ' + totalClaim.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        // Base Allowance Pool
        const baseAllowance = parseFloat(vendorAllowanceData.company_allowance) > 0 
            ? parseFloat(vendorAllowanceData.remaining_allowance) 
            : parseFloat(vendorAllowanceData.vendor_payable || 0);

        const remainingBalance = Math.max(0, baseAllowance - totalClaim);

        $('#kpiCompanyAllowance').text('Rs ' + baseAllowance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#kpiClaimValue').text('Rs ' + totalClaim.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#kpiRemainingBalance').text('Rs ' + remainingBalance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        $('#summaryTotalItems').text(itemCount);
        $('#summaryTotalPieces').text(totalPieces);
        $('#summaryCompanyAllowance').text('Rs ' + baseAllowance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#summaryTotalClaim').text('Rs ' + totalClaim.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        // 3-Tier Allowance Rule Explanation (User Request Specification)
        const vendorName = $('#vendorSelect option:selected').text();
        if ($('#vendorSelect').val()) {
            $('#companySettlementRuleText').html(
                `<strong>${vendorName}</strong> ka Company Allowance Fund / Payable <strong>Rs ${baseAllowance.toLocaleString('en-US', { minimumFractionDigits: 2 })}</strong> tha. Isme se <strong>Rs ${totalClaim.toLocaleString('en-US', { minimumFractionDigits: 2 })}</strong> ka damage claim deduct ho gaya, abhi bhi vendor ko <strong>Rs ${remainingBalance.toLocaleString('en-US', { minimumFractionDigits: 2 })}</strong> baaqi payable hai.`
            );
        } else {
            $('#companySettlementRuleText').text('Vendor select karne par Company 3-Tier Allowance (5% Damage Pool) aur baaqi payable balance live calculate hoga.');
        }

        // Cash refund prefill
        if ($('input[name="settlement_type"]:checked').val() === 'cash_refund') {
            $('#refundAmountInput').val(totalClaim > 0 ? totalClaim.toFixed(2) : '');
        }
    }

    // Quick Add Customer Submission
    $('#formQuickAddCustomer').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#btnSaveQuickCustomer');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('customers.store') }}",
            method: "POST",
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Customer');
                if (res.success && res.customer) {
                    const c = res.customer;
                    const newOpt = new Option(c.customer_name + (c.mobile ? ' ('+c.mobile+')' : ''), c.id, true, true);
                    $(newOpt).attr('data-mobile', c.mobile || '');
                    $(newOpt).attr('data-address', c.address || '');
                    $(newOpt).attr('data-balance', c.previous_balance || 0);

                    $('#customerSelect').append(newOpt).trigger('change');
                    $('#addCustomerModal').modal('hide');
                    $('#formQuickAddCustomer')[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Customer Added',
                        text: 'Customer created and selected successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    $('#addCustomerModal').modal('hide');
                    location.reload();
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Customer');
                Swal.fire('Error', xhr.responseJSON?.message || 'Failed to add customer. Please check fields.', 'error');
            }
        });
    });

    // Form Submission with SweetAlert Confirmation
    $('#claimDamageForm').on('submit', function(e) {
        e.preventDefault();

        if (!$('#customerSelect').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Customer Required',
                text: 'Please select a customer for this claim.'
            });
            $('#customerSelect').select2('open');
            return false;
        }

        if ($('#claimItemsBody tr.item-row').length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Damaged Items',
                text: 'Please add at least one damaged/expired product to the claim.'
            });
            return false;
        }

        const totalClaim = calculateTotalClaimAmount();
        const customerName = $('#customerSelect option:selected').text();
        const settlementType = $('input[name="settlement_type"]:checked').val();

        if (settlementType === 'cash_refund') {
            if (!$('#refundAccountId').val()) {
                Swal.fire('Account Required', 'Please select a Cash / Bank disbursement account for the cash refund.', 'warning');
                return false;
            }
            if (parseFloat($('#refundAmountInput').val() || 0) <= 0) {
                Swal.fire('Refund Amount Required', 'Please specify a valid cash refund amount.', 'warning');
                return false;
            }
        }

        Swal.fire({
            title: 'Confirm Claim Voucher?',
            html: `Processing claim of <strong class="text-danger">Rs ${totalClaim.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong> for <strong>${customerName}</strong>.<br><br><small class="text-muted">Damaged goods will be deposited to Quarantine Warehouse and customer settlement processed.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#64748B',
            confirmButtonText: '<i class="fas fa-check me-1"></i> Yes, Process Voucher',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const $btn = $('#btnSubmitClaim');
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Processing Voucher...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Claim Voucher Created!',
                            text: response.message || 'Claim processed and inventory quarantined.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect || "{{ route('claims.index') }}";
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Post & Process Customer Claim');
                        const msg = xhr.responseJSON?.message || 'Failed to process claim. Please check items and try again.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    });

});
</script>
@endsection
