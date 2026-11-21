@extends('admin_panel.layout.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<style>
    :root {
        --pos-blue: #2563EB;
        --pos-blue-hover: #1D4ED8;
        --pos-blue-soft: #EFF6FF;
        --pos-green: #16A34A;
        --pos-green-soft: #F0FDF4;
        --pos-red: #DC2626;
        --pos-red-hover: #B91C1C;
        --pos-red-soft: #FEF2F2;
        --pos-orange: #F59E0B;
        --pos-orange-soft: #FFFBEB;
        --pos-text: #0F172A;
        --pos-muted: #64748B;
        --pos-border: #CBD5E1;
        --pos-bg: #F1F5F9;
        --pos-card: #FFFFFF;
        --pos-radius: 8px;
        --pos-radius-lg: 12px;
        --pos-shadow-sm: 0 1px 3px rgba(0,0,0,.05);
        --pos-shadow-md: 0 4px 14px rgba(15,23,42,.08);
        --pos-input-h: 38px;
    }

    body {
        background-color: var(--pos-bg) !important;
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        color: var(--pos-text) !important;
        -webkit-font-smoothing: antialiased;
    }

    .sale-page {
        max-width: 100%;
        margin: 0 auto;
    }

    /* ---------- CARDS & CONTAINERS ---------- */
    .sale-card {
        background: var(--pos-card);
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-lg);
        box-shadow: var(--pos-shadow-sm);
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    /* ---------- ERP HEADER & LEDGER KPI PILLS ---------- */
    .erp-header-card {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
    }
    .erp-field-label {
        display: flex;
        align-items: center;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        color: #475569;
        margin-bottom: 5px;
        white-space: nowrap;
    }
    .erp-status-strip {
        background: #F8FAFC;
        border-radius: 7px;
        padding: 6px 12px;
        border: 1px solid #E2E8F0;
    }
    .erp-kpi-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.3;
        white-space: nowrap;
        border: 1px solid transparent;
        transition: all 0.15s ease;
    }
    .erp-kpi-pill:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }
    .erp-kpi-danger {
        background: #FEF2F2;
        color: #DC2626;
        border-color: #FECACA;
    }
    .erp-kpi-primary {
        background: #EFF6FF;
        color: #2563EB;
        border-color: #BFDBFE;
    }
    .erp-kpi-success {
        background: #F0FDF4;
        color: #16A34A;
        border-color: #BBF7D0;
    }
    .erp-kpi-purple {
        background: #FAF5FF;
        color: #7E22CE;
        border-color: #E9D5FF;
    }
    .erp-kpi-pill .kpi-label {
        color: #64748B;
        font-weight: 600;
        font-size: 10.5px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .erp-kpi-pill .kpi-val {
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }
    .erp-kpi-pill .kpi-suffix {
        font-size: 11px;
        font-weight: 700;
    }

    /* Grouped Customer Search + Action Buttons (Auto-adaptive) */
    .cust-select-container {
        width: 100%;
    }
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
        height: var(--pos-input-h) !important;
    }
    .customer-input-group #btnOpenAddCustomerModal {
        border-radius: 0 !important;
        border-left: 1px solid var(--pos-border) !important;
        height: var(--pos-input-h) !important;
        padding: 0 10px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .customer-input-group #btnToggleCustomerInfo {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        border-top-right-radius: 6px !important;
        border-bottom-right-radius: 6px !important;
        border-left: 1px solid var(--pos-border) !important;
        height: var(--pos-input-h) !important;
        padding: 0 10px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* ---------- SALE TYPE SEGMENTED TOGGLE ---------- */
    .seg-toggle {
        display: flex;
        height: var(--pos-input-h);
        background: #F1F5F9;
        border: 1px solid var(--pos-border);
        border-radius: 6px;
        padding: 2px;
        width: 100%;
    }
    .seg-toggle .btn {
        flex: 1;
        border-radius: 4px;
        border: none;
        font-size: 12.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0 8px;
    }
    .seg-toggle .btn.btn-outline-primary {
        background: transparent;
        color: var(--pos-muted);
    }
    .seg-toggle .btn.btn-outline-primary:hover {
        background: rgba(37, 99, 235, .08);
        color: var(--pos-blue);
    }

    /* ---------- INPUTS & FORM CONTROLS ---------- */
    .sale-page .form-control,
    .sale-page .form-select {
        height: var(--pos-input-h);
        border: 1px solid var(--pos-border);
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 13px;
        font-weight: 500;
        color: var(--pos-text);
        background-color: #ffffff;
        box-shadow: none;
        transition: border-color .12s ease, box-shadow .12s ease;
    }
    .sale-page .form-control:focus,
    .sale-page .form-select:focus {
        border-color: var(--pos-blue);
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .15);
    }

    /* ---------- POS TABLE STYLING ---------- */
    .pos-table-wrap {
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius);
        overflow-x: auto;
        background: #ffffff;
    }
    .sales-table {
        margin-bottom: 0;
        font-size: 12px;
        white-space: nowrap;
        width: 100%;
    }
    .sales-table thead th {
        background: #F8FAFC !important;
        color: #475569 !important;
        font-weight: 700 !important;
        font-size: 11px !important;
        text-transform: uppercase;
        letter-spacing: .3px;
        padding: 8px 6px !important;
        border-bottom: 2px solid var(--pos-border) !important;
        vertical-align: middle;
    }
    .sales-table tbody td {
        padding: 4px 4px !important;
        vertical-align: middle;
        border-bottom: 1px solid #E2E8F0;
    }
    .sales-table tbody tr:hover {
        background-color: #F8FAFC;
    }
    .sales-table .form-control,
    .sales-table .form-select {
        height: 32px !important;
        font-size: 12px !important;
        padding: 2px 6px !important;
        border-radius: 4px !important;
    }

    /* Stock Bal. Cell */
    .c-stock {
        font-variant-numeric: tabular-nums;
        font-size: 11px;
        line-height: 1.2;
    }
    .stock-badge-green {
        background: #DCFCE7;
        color: #166534;
        border: 1px solid #BBF7D0;
        padding: 2px 5px;
        border-radius: 4px;
        font-weight: 700;
        display: inline-block;
    }
    .stock-badge-zero {
        background: #FEE2E2;
        color: #991B1B;
        border: 1px solid #FECACA;
        padding: 2px 5px;
        border-radius: 4px;
        font-weight: 700;
        display: inline-block;
    }

    /* Action delete btn */
    .btn-del-row {
        width: 24px;
        height: 24px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        border: 1px solid #FCA5A5;
        background: #FEF2F2;
        color: #DC2626;
        transition: all .15s ease;
    }
    .btn-del-row:hover {
        background: #DC2626;
        color: #ffffff;
    }

    /* Checkout summary */
    .checkout-card {
        background: #ffffff;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,.05);
    }
    .total-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
        font-size: 12.5px;
        border-bottom: 1px dashed #E2E8F0;
    }
    .total-summary-row:last-child {
        border-bottom: none;
    }
    .grand-net-pill {
        background: #FEF2F2;
        border: 2px solid #FCA5A5;
        border-radius: 8px;
        padding: 8px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .grand-net-pill .net-val {
        font-size: 20px;
        font-weight: 800;
        color: #DC2626;
        font-variant-numeric: tabular-nums;
    }

    /* Select2 custom adjustments */
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1px solid var(--pos-border) !important;
        border-radius: 6px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: 10px !important;
        color: var(--pos-text) !important;
        font-weight: 500 !important;
        font-size: 13px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid py-3 sale-page">

            <form id="saleReturnForm" action="{{ route('sale.return.store') }}" method="POST" autocomplete="off">
                @csrf
                <input type="hidden" name="action" id="action" value="sale_return">
                <input type="hidden" name="is_walkin" id="is_walkin" value="0">
                <input type="hidden" id="address" name="address">
                <input type="hidden" id="tel" name="tel">
                <input type="hidden" id="previousBalance" value="0">
                <input type="hidden" id="rangeBalance" value="0">

                {{-- ============================ TOP BAR ============================ --}}
                <div class="sale-card mb-3 p-2 px-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        {{-- Left: Return Title & Breadcrumb --}}
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-danger text-white px-2 py-1 fw-bold" style="font-size: 11px;">
                                <i class="fas fa-undo-alt me-1"></i> DIRECT SALE RETURN
                            </span>
                            <span class="text-muted small">|</span>
                            <span class="text-muted small fw-semibold">Receive return from any customer &amp; restore stock</span>
                        </div>

                        {{-- Middle & Right: Return Invoice No, Receiving Warehouse, Return Date, Back button --}}
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            {{-- Return Invoice No --}}
                            <div class="d-flex align-items-center gap-1">
                                <label class="field-label mb-0 text-danger fw-bold" style="font-size: 11px;">Return #</label>
                                <input type="text" name="return_invoice" id="returnInvoiceInput" class="form-control form-control-sm text-center fw-bold bg-light font-monospace text-danger" value="{{ $nextInvoiceNumber ?? 'SR-0001' }}" readonly style="width: 110px; height: 36px;">
                            </div>

                            {{-- Receiving Warehouse --}}
                            <div class="d-flex align-items-center gap-1">
                                <label class="field-label mb-0 text-dark fw-bold" style="font-size: 11px;">Receiving WH:</label>
                                <select name="warehouse_id" id="headerWarehouseSelect" class="form-select form-select-sm fw-bold" style="width: 150px; height: 36px;">
                                    @foreach($warehouse as $wh)
                                        <option value="{{ $wh->id }}" {{ (auth()->user()->warehouse_id ?? 1) == $wh->id ? 'selected' : '' }}>
                                            {{ $wh->warehouse_name ?? $wh->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Return Date --}}
                            <div class="d-flex align-items-center gap-1">
                                <label class="field-label mb-0 text-dark fw-bold" style="font-size: 11px;">Date:</label>
                                <input type="date" name="return_date" id="displayDateInput" class="form-control form-control-sm fw-bold text-center" value="{{ date('Y-m-d') }}" style="width: 130px; height: 36px;">
                            </div>

                            {{-- All Returns link --}}
                            <div>
                                <a href="{{ route('sale.return.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" style="height: 36px;">
                                    <i class="fas fa-list"></i>
                                    <span class="d-none d-md-inline small fw-bold">All Returns</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============================ CUSTOMER & ERP STATUS CARD ============================ --}}
                <div class="sale-card erp-header-card mb-3 p-3">
                    <div class="row g-2 align-items-end">
                        {{-- 1. Customer Input Group --}}
                        <div class="col-12 col-md-6 col-lg-4 col-xl-4">
                            <label class="erp-field-label" for="customerSelect">
                                <i class="fas fa-user-tag text-primary me-1"></i> Customer <span class="text-danger ms-1">*</span>
                            </label>
                            <div class="input-group customer-input-group">
                                <div id="customerInputWrapper" style="min-width: 0; width: 100%;">
                                    <input type="text" class="form-control d-none" name="walkin_name" id="walkinNameInput" value="Walk-in Customer" placeholder="Enter Walk-in Name...">
                                    <select class="form-select" id="customerSelect" name="customer" style="width:100%">
                                        <option value="">Select Customer...</option>
                                        @if(isset($customer) && count($customer) > 0)
                                            @foreach($customer as $c)
                                                <option value="{{ $c->id }}" 
                                                        data-mobile="{{ $c->mobile }}" 
                                                        data-address="{{ $c->address }}" 
                                                        data-code="{{ $c->customer_id }}"
                                                        data-prev="{{ $c->previous_balance ?? 0 }}" 
                                                        data-range="{{ $c->balance_range ?? 0 }}"
                                                        data-sales-officer="{{ $c->sales_officer_id ?? '' }}">
                                                    {{ $c->customer_id ? $c->customer_id . ' — ' : '' }}{{ $c->customer_name }} {{ $c->mobile ? '(' . $c->mobile . ')' : '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <button type="button" id="btnOpenAddCustomerModal"
                                        class="btn btn-outline-primary"
                                        data-toggle="modal" data-target="#addCustomerModal"
                                        data-bs-toggle="modal" data-bs-target="#addCustomerModal"
                                        title="Quick Add Customer (Alt+C or F2)">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" id="btnToggleCustomerInfo"
                                        class="btn btn-outline-secondary"
                                        data-toggle="modal" data-target="#modalCustomerDetails"
                                        data-bs-toggle="modal" data-bs-target="#modalCustomerDetails"
                                        title="View Customer Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        {{-- 2. Sale / Return Type --}}
                        <div class="col-6 col-md-6 col-lg-2 col-xl-2 wrapper-sale-type">
                            <label class="erp-field-label">
                                <i class="fas fa-layer-group text-secondary me-1"></i> Return Party Type
                            </label>
                            <div class="seg-toggle w-100" role="group" aria-label="Party Type">
                                <button type="button" class="btn btn-primary active text-white w-50" id="btnTypeCustomer">
                                    <i class="fas fa-users me-1"></i> Customer
                                </button>
                                <button type="button" class="btn btn-outline-primary w-50" id="btnTypeWalkin">
                                    <i class="fas fa-walking me-1"></i> Walk-in
                                </button>
                            </div>
                            <select class="d-none" id="partyTypeSelect" name="partyType">
                                @foreach(\App\Models\CustomerType::orderBy('name')->get() as $type)
                                    <option value="{{ $type->name }}" {{ $type->name === 'Main Customer' ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 3. Order Booker (S.R) --}}
                        <div class="col-6 col-md-6 col-lg-3 col-xl-3 wrapper-officer">
                            <label class="erp-field-label" for="orderBookerSelect">
                                <i class="fas fa-clipboard-list text-primary me-1"></i> Order Booker (S.R)
                            </label>
                            <select class="form-select form-select-sm" name="order_booker_id" id="orderBookerSelect" style="height: 38px; font-size: 12.5px; border-radius: 6px;">
                                <option value="">Direct / Select Booker...</option>
                                @if(isset($orderBookers) && count($orderBookers) > 0)
                                    @foreach($orderBookers as $ob)
                                        <option value="{{ $ob->id }}">
                                            [{{ $ob->code ?: ('B-' . $ob->id) }}] {{ $ob->name }}
                                        </option>
                                    @endforeach
                                @elseif(isset($salesOfficers))
                                    @foreach($salesOfficers as $so)
                                        <option value="{{ $so->id }}">{{ $so->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <input type="hidden" name="sales_officer_id" id="salesOfficerHidden" value="">
                        </div>

                        {{-- 4. Salesman / Delivery Person --}}
                        <div class="col-12 col-md-6 col-lg-3 col-xl-3 wrapper-salesman">
                            <label class="erp-field-label" for="salesmanSelect">
                                <i class="fas fa-truck text-success me-1"></i> Salesman / Delivery
                            </label>
                            <select class="form-select form-select-sm" name="salesman_id" id="salesmanSelect" style="height: 38px; font-size: 12.5px; border-radius: 6px;">
                                <option value="">Select Salesman...</option>
                                @if(isset($salesmen) && count($salesmen) > 0)
                                    @foreach($salesmen as $sm)
                                        <option value="{{ $sm->id }}">
                                            [{{ $sm->code ?: ('S-' . $sm->id) }}] {{ $sm->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    {{-- ============================ ERP STATUS & FINANCIAL STRIP ============================ --}}
                    <div class="erp-status-strip mt-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        {{-- Left: Customer Meta & Badges --}}
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div id="customerTaxBadgeContainer" class="d-none"></div>
                            <div id="customerQuickInfoContainer" class="d-flex flex-wrap gap-1 align-items-center d-none" style="font-size: 11px;">
                                <span class="badge bg-white text-secondary border me-1" id="cqi_address_badge" title="Customer Address">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i><span id="cqi_address_text">—</span>
                                </span>
                                <span class="badge bg-white text-primary border" id="cqi_credit_badge" title="Credit Limit">
                                    <i class="fas fa-shield-alt text-primary me-1"></i>Limit: <span id="cqi_credit_text">Unlimited</span>
                                </span>
                            </div>
                        </div>

                        {{-- Right: Balances formatted as ERP Financial KPI Badges --}}
                        <div class="d-flex align-items-center flex-wrap gap-2 ms-auto">
                            <div class="erp-kpi-pill erp-kpi-danger" title="Previous Balance Due">
                                <span class="kpi-label">Prev. Due:</span>
                                <span class="kpi-val" id="cc_prev_bal_val">Rs 0</span>
                                <span class="kpi-suffix" id="cc_prev_bal_suffix">Dr</span>
                            </div>
                            <div class="erp-kpi-pill erp-kpi-primary" title="Total Return Credit Value">
                                <span class="kpi-label">Return Value:</span>
                                <span class="kpi-val" id="cc_current_bill">Rs 0</span>
                            </div>
                            <div class="erp-kpi-pill erp-kpi-success" title="Cash/Bank Refund Paid Out">
                                <span class="kpi-label">Refund Paid:</span>
                                <span class="kpi-val" id="cc_paid_now">Rs 0</span>
                            </div>
                            <div class="erp-kpi-pill erp-kpi-purple" title="Net Closing Balance After Return">
                                <span class="kpi-label">Closing:</span>
                                <span class="kpi-val" id="cc_closing_bal_val">Rs 0</span>
                                <span class="kpi-suffix" id="cc_closing_bal_suffix">Dr</span>
                            </div>
                        </div>
                    </div>

                    <span id="cc_customer_name" class="d-none"></span>
                    <span id="ci_code" class="d-none"></span>
                    <span id="ci_name" class="d-none">—</span>
                    <span id="ci_mobile" class="d-none">—</span>
                    <span id="ci_address" class="d-none">—</span>
                </div>

                {{-- ============================ ITEMS RETURN SECTION ============================ --}}
                <div class="sale-card mb-3 p-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark fs-6"><i class="fas fa-boxes text-danger me-1"></i> Return Items Grid</span>
                            <span class="badge bg-light text-dark border px-2 py-1 font-monospace" id="itemsRowCount">1 Row</span>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary btn-sm px-3" data-bs-toggle="offcanvas" data-bs-target="#productSearchDrawer">
                                <i class="fas fa-th me-1"></i> Quick Product Drawer
                            </button>
                        </div>
                    </div>

                    <div class="pos-table-wrap">
                        <table class="table sales-table mb-0">
                            <thead>
                                <tr>
                                    <th class="col-index-th text-center" style="width: 24px;">#</th>
                                    <th class="col-product-th text-center" style="min-width: 140px;">Product / Variant Description</th>
                                    <th class="col-batch-th text-center" style="width: 60px;">Batch</th>
                                    <th class="col-price-th text-center" style="width: 55px;">Trade Price</th>
                                    <th class="c-stock-th text-center" style="width: 70px;" title="Current Warehouse Stock">Curr. Stock</th>
                                    <th class="col-qty-ctn-th text-center" style="width: 48px;">Return<br>CTNs</th>
                                    <th class="col-qty-pcs-th text-center" style="width: 48px;">Return<br>Pieces</th>
                                    <th class="col-total-qty-th text-center" style="width: 45px;">Total<br>Pcs</th>
                                    <th class="col-gross-th text-center" style="width: 60px;">Gross</th>
                                    <th class="col-trade-activity-th text-center d-none" style="width: 50px;">Trade Act</th>
                                    <th class="col-reg-disc-pct-th text-center" style="width: 40px;">Reg %</th>
                                    <th class="col-reg-disc-amt-th text-center" style="width: 50px;">Reg Disc</th>
                                    <th class="col-sp-disc-pct-th text-center" style="width: 40px;">Sp %</th>
                                    <th class="col-sp-disc-amt-th text-center" style="width: 50px;">Sp Disc</th>
                                    <th class="c-st-th text-center" style="width: 55px;">Sales Tax</th>
                                    <th class="col-mrp-th text-center" style="width: 50px;">MRP Tax</th>
                                    <th class="c-ft-th text-center" style="width: 55px;">URC Tax</th>
                                    <th class="col-adv-tax-th text-center" style="width: 50px;">Adv Tax</th>
                                    <th class="col-amount-th text-center" style="width: 65px;">Net Value</th>
                                    <th class="col-action-th text-center" style="width: 24px;"><i class="fas fa-trash text-danger"></i></th>
                                </tr>
                            </thead>
                            <tbody id="salesTableBody">
                                <tr class="sale-item-row" data-index="0">
                                    <!-- # -->
                                    <td class="col-index text-center fw-bold row-index-cell" style="font-size: 11px;">1</td>

                                    <!-- ITEM TITLE & SEARCH -->
                                    <td class="col-product">
                                        <select class="form-select product-select2 product" style="width: 100%;"></select>
                                        <input type="hidden" class="product-id-hidden" name="product_id[]">
                                        <input type="hidden" class="variant-data-hidden" name="color[]">
                                        <input type="hidden" class="batch-id-hidden" name="batch_id[]">
                                        <input type="hidden" class="batch-no-hidden" name="batch_no[]">
                                        <input type="hidden" class="serials-hidden" name="serials[]">
                                        <input type="hidden" class="item-code-display" name="item_code[]">
                                        <input type="hidden" class="size-display" name="size_display[]">
                                        <input type="hidden" class="size-h" name="size_h[]">
                                        <input type="hidden" class="size-w" name="size_w[]">
                                        <input type="hidden" class="size-mode-text" name="size_mode_text[]">
                                        <input type="hidden" class="variant-stock-value">
                                        <input type="hidden" class="pack-qty" name="pack_qty[]" value="1">
                                        <input type="hidden" class="hidden-sub-unit-mode" name="sub_unit_mode[]" value="main">
                                        <div class="row-tracking-badges mt-1"></div>
                                    </td>

                                    <input type="hidden" class="warehouse" name="warehouse_id[]" value="{{ auth()->user()->warehouse_id ?? 1 }}">

                                    <!-- BATCH NO -->
                                    <td class="col-batch text-center">
                                        <input type="text" class="form-control selected-batch-no text-center fw-semibold text-primary" readonly placeholder="–" style="background: #f0f7ff;">
                                    </td>

                                    <!-- TRADE PRICE / UNIT PRICE -->
                                    <td class="col-price">
                                        <input type="number" step="0.0001" class="form-control text-end fw-bold price" name="price[]" placeholder="0.00">
                                        <input type="hidden" class="trade-price" name="trade_price[]" value="0">
                                        <input type="hidden" class="retail-price" name="retail_price[]" value="0">
                                        <input type="hidden" class="orig-trade-price" value="0">
                                        <input type="hidden" class="orig-retail-price" value="0">
                                    </td>

                                    <!-- STOCK BALANCE -->
                                    <td class="c-stock text-center">
                                        <span class="stock-badge-zero stock-badge">0 Pcs</span>
                                        <input type="hidden" class="stock-input" value="0">
                                    </td>

                                    <!-- QTY IN CARTONS -->
                                    <td class="col-qty-ctn">
                                        <input type="number" step="1" min="0" class="form-control text-center carton" name="carton[]" placeholder="0">
                                    </td>

                                    <!-- QTY IN PIECES / LOOSE -->
                                    <td class="col-qty-pcs">
                                        <input type="number" step="1" min="0" class="form-control text-center loose-piece" name="loose_piece[]" placeholder="0">
                                        <input type="hidden" class="pcs-per-carton" value="1">
                                    </td>

                                    <!-- TOTAL PIECES -->
                                    <td class="col-total-qty">
                                        <input type="number" step="any" min="0" class="form-control text-center fw-bold total-pieces qty" name="total_pieces[]" placeholder="0">
                                        <input type="hidden" class="qty-hidden" name="qty[]" value="0">
                                    </td>

                                    <!-- GROSS AMOUNT -->
                                    <td class="col-gross">
                                        <input type="text" class="form-control text-end fw-semibold line-gross" name="line_gross[]" readonly placeholder="0.00">
                                    </td>

                                    <!-- TRADE ACTIVITY (HIDDEN BY DEFAULT) -->
                                    <td class="col-trade-activity d-none">
                                        <input type="number" step="0.01" class="form-control text-end trade-activity" name="trade_activity[]" placeholder="0.00" value="0">
                                    </td>

                                    <!-- REGULAR DISCOUNT % -->
                                    <td class="col-reg-disc-pct">
                                        <input type="number" step="0.01" min="0" max="100" class="form-control text-center reg-disc-percent" name="reg_disc_percent[]" placeholder="0">
                                    </td>

                                    <!-- REGULAR DISCOUNT AMOUNT -->
                                    <td class="col-reg-disc-amt">
                                        <input type="text" class="form-control text-end reg-disc-amt" name="reg_disc_amt[]" readonly placeholder="0.00">
                                    </td>

                                    <!-- SPECIAL DISCOUNT % -->
                                    <td class="col-sp-disc-pct">
                                        <input type="number" step="0.01" min="0" max="100" class="form-control text-center sp-disc-percent" name="sp_disc_percent[]" placeholder="0">
                                    </td>

                                    <!-- SPECIAL DISCOUNT AMOUNT -->
                                    <td class="col-sp-disc-amt">
                                        <input type="text" class="form-control text-end sp-disc-amt" name="sp_disc_amt[]" readonly placeholder="0.00">
                                    </td>

                                    <!-- SALES TAX (GST) -->
                                    <td class="c-st">
                                        <input type="text" class="form-control text-end sales-tax-amount" name="sales_tax_amount[]" readonly placeholder="0.00">
                                        <input type="hidden" class="sales-tax-percent" name="sales_tax_percent[]" value="0">
                                    </td>

                                    <!-- MRP AMOUNT -->
                                    <td class="col-mrp">
                                        <input type="text" class="form-control text-end mrp-amount" name="mrp_amount[]" readonly placeholder="0.00">
                                    </td>

                                    <!-- URC TAX (FURTHER TAX) -->
                                    <td class="c-ft">
                                        <input type="text" class="form-control text-end further-tax-amount" name="further_tax_amount[]" readonly placeholder="0.00">
                                        <input type="hidden" class="further-tax-percent" name="further_tax_percent[]" value="0">
                                    </td>

                                    <!-- ADVANCE TAX -->
                                    <td class="col-adv-tax">
                                        <input type="text" class="form-control text-end adv-tax-amount" name="adv_tax_amount[]" readonly placeholder="0.00">
                                        <input type="hidden" class="adv-tax-percent" name="adv_tax_percent[]" value="0">
                                    </td>

                                    <!-- NET AMOUNT -->
                                    <td class="col-amount">
                                        <input type="text" class="form-control text-end fw-bold line-total" name="line_total[]" readonly placeholder="0.00" style="color: #2563EB;">
                                    </td>

                                    <!-- ACTION / DELETE ROW -->
                                    <td class="col-action text-center">
                                        <button type="button" class="btn btn-del-row" title="Remove Item"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ============================ SETTLEMENT & TOTALS SECTION ============================ --}}
                <div class="row g-3 mb-4">
                    {{-- Left Column: Return Reason & Optional Refund Paid Now --}}
                    <div class="col-lg-7 col-md-12">
                        <div class="checkout-card p-3 h-100">
                            <span class="erp-field-label text-dark mb-2">
                                <i class="fas fa-hand-holding-usd text-success me-1"></i> Return Settlement &amp; Reason
                            </span>

                            {{-- Return Reason / Reference --}}
                            <div class="mb-3">
                                <label class="field-label mb-1" for="inputReturnReason">Return Reason / Notes</label>
                                <textarea name="return_reason" id="inputReturnReason" rows="2" class="form-control" placeholder="e.g. Market return, customer returned excess goods, packaging damage, replacement..."></textarea>
                            </div>

                            {{-- Settlement Mode Checkbox --}}
                            <div class="p-3 rounded bg-light border mb-3">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="toggleCashRefund" style="cursor: pointer;">
                                    <label class="form-check-label fw-bold text-dark" for="toggleCashRefund" style="cursor: pointer;">
                                        Issue Immediate Cash / Bank Refund to Customer
                                    </label>
                                </div>
                                <small class="text-muted d-block">
                                    Agar off hai: Full return amount customer ke ledger account mein <strong>Credit</strong> ho jayegi (Due balance kam ho jayega).<br>
                                    Agar on hai: Cash ya Bank se customer ko return ke paise ada kiye jayenge.
                                </small>

                                {{-- Refund Account & Amount (Visible only when toggle is on) --}}
                                <div id="refundPaymentDetails" class="row g-2 mt-2 pt-2 border-top d-none">
                                    <div class="col-md-6 col-12">
                                        <label class="field-label mb-1">Refund From Account <span class="text-danger">*</span></label>
                                        <select class="form-select form-select-sm" name="payment_account_id[]" id="refundAccountId">
                                            <option value="">-- Select Cash/Bank Account --</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->title }} ({{ $acc->head->name ?? 'Account' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label class="field-label mb-1">Refund Paid Amount (Rs.) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-end fw-bold" name="payment_amount[]" id="paidAmountInput" placeholder="0.00" value="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Grand Financial Totals & Post Button --}}
                    <div class="col-lg-5 col-md-12">
                        <div class="checkout-card p-3">
                            <span class="erp-field-label text-dark mb-2">
                                <i class="fas fa-calculator text-primary me-1"></i> Financial Summary
                            </span>

                            <div class="total-summary-row">
                                <span class="text-muted">Total Gross Amount:</span>
                                <span class="fw-bold font-monospace" id="grandTotalGross">Rs 0.00</span>
                            </div>
                            <div class="total-summary-row">
                                <span class="text-muted">Total Item Discounts:</span>
                                <span class="fw-bold font-monospace text-danger" id="grandTotalDiscount">Rs 0.00</span>
                            </div>
                            <div class="total-summary-row">
                                <span class="text-muted">Sales Tax (GST):</span>
                                <span class="fw-bold font-monospace text-primary" id="grandTotalSalesTax">Rs 0.00</span>
                            </div>
                            <div class="total-summary-row">
                                <span class="text-muted">URC Tax (Further Tax):</span>
                                <span class="fw-bold font-monospace text-warning" id="grandTotalFurtherTax">Rs 0.00</span>
                            </div>
                            <div class="total-summary-row">
                                <span class="text-muted">Advance Tax (WHT):</span>
                                <span class="fw-bold font-monospace text-info" id="grandTotalAdvTax">Rs 0.00</span>
                            </div>

                            <div class="my-3">
                                <div class="grand-net-pill">
                                    <div>
                                        <span class="small fw-bold text-danger text-uppercase d-block">Net Return Amount</span>
                                        <small class="text-muted" style="font-size: 10px;">Total credit/refund value</small>
                                    </div>
                                    <div class="net-val" id="grandTotalNet">Rs 0.00</div>
                                </div>
                            </div>

                            {{-- Hidden backend inputs for totals --}}
                            <input type="hidden" name="total_gross" id="inputTotalGross" value="0">
                            <input type="hidden" name="total_reg_discount" id="inputTotalRegDiscount" value="0">
                            <input type="hidden" name="total_sp_discount" id="inputTotalSpDiscount" value="0">
                            <input type="hidden" name="total_sales_tax" id="inputTotalSalesTax" value="0">
                            <input type="hidden" name="total_further_tax" id="inputTotalFurtherTax" value="0">
                            <input type="hidden" name="total_adv_tax" id="inputTotalAdvTax" value="0">
                            <input type="hidden" name="total_net" id="inputTotalNet" value="0">

                            {{-- POST RETURN BUTTON --}}
                            <button type="button" id="btnPostReturn" class="btn btn-danger btn-lg w-100 fw-bold shadow-sm" style="height: 48px; border-radius: 8px;">
                                <i class="fas fa-undo-alt me-2"></i> Post Direct Sale Return
                            </button>

                            <button type="button" id="btnResetForm" class="btn btn-outline-secondary w-100 mt-2 btn-sm">
                                <i class="fas fa-redo me-1"></i> Reset Grid
                            </button>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- Quick Product Offcanvas / Drawer --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="productSearchDrawer" aria-labelledby="productSearchDrawerLabel" style="width: 380px;">
    <div class="offcanvas-header border-bottom py-2">
        <h6 class="offcanvas-title fw-bold text-dark mb-0"><i class="fas fa-th text-danger me-2"></i>Product Catalog</h6>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-2">
        <div class="input-group input-group-sm mb-2">
            <input type="text" class="form-control" id="sidebarProductSearch" placeholder="Search product by name, barcode or SKU...">
            <button class="btn btn-danger px-2" type="button"><i class="fas fa-search"></i></button>
        </div>
        <div class="overflow-auto pe-1" id="sidebarProductContainer" style="max-height: calc(100vh - 120px);">
            @if(isset($allProducts) && count($allProducts) > 0)
                @foreach($allProducts as $prod)
                    <div class="pos-product-card p-2 mb-2 border rounded d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="d-block text-dark small" title="{{ $prod->item_name }}">{{ Str::limit($prod->item_name, 30) }}</strong>
                            <small class="text-muted">Code: {{ $prod->item_code ?: '—' }} | TP: Rs {{ number_format($prod->trade_price, 2) }}</small>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1 add-product-direct-btn" data-id="{{ $prod->id }}" title="Add to Grid">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: #2563EB !important; padding: 14px 18px;">
                <h5 class="modal-title font-weight-bold fw-bold text-white mb-0" id="addCustomerModalLabel" style="font-size: 1rem;">
                    <i class="fas fa-user-plus me-2 mr-2"></i>Quick Customer
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; line-height: 1; opacity: 0.9; cursor: pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="ajaxAddCustomerForm" autocomplete="off">
                    @csrf
                    <input type="hidden" name="party_type" value="customer">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label font-weight-bold fw-bold">Customer Type <span class="text-danger">*</span></label>
                            <select class="form-control form-select" name="customer_type" id="modalCustomerType" required>
                                @foreach(\App\Models\CustomerType::orderBy('name')->get() as $type)
                                    <option value="{{ $type->name }}" {{ $type->name === 'Main Customer' ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label font-weight-bold fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="customer_name" id="modalCustomerName" required placeholder="Customer Name">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label font-weight-bold fw-bold">Mobile</label>
                            <input type="text" class="form-control" name="mobile" placeholder="0300-1234567">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label font-weight-bold fw-bold">Order Booker (S.R)</label>
                            <select class="form-control form-select" name="sales_officer_id" id="modalSalesOfficer">
                                <option value="">Direct / Select Booker...</option>
                                @foreach(($orderBookers ?? $salesOfficers ?? []) as $ob)
                                    <option value="{{ $ob->id }}">{{ ($ob->code ? '['.$ob->code.'] ' : '') . $ob->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label font-weight-bold fw-bold">Opening Balance</label>
                            <input type="number" step="0.01" class="form-control" name="opening_balance" value="0">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label font-weight-bold fw-bold">Credit Limit</label>
                            <input type="number" step="0.01" class="form-control" name="balance_range" placeholder="0 = Unlimited" value="0">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label font-weight-bold fw-bold">NTN Number</label>
                            <input type="text" class="form-control" name="ntn_number" placeholder="NTN (e.g. 1234567-8)">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label font-weight-bold fw-bold">STRN Number</label>
                            <input type="text" class="form-control" name="strn_number" placeholder="STRN (FT Exempt)">
                        </div>
                        <div class="col-12">
                            <label class="form-label font-weight-bold fw-bold">Address</label>
                            <input type="text" class="form-control" name="address" placeholder="Address">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm fw-bold" id="btnSaveAjaxCustomer">
                    <i class="fas fa-save me-1 mr-1"></i> Save Customer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Details Modal -->
<div class="modal fade" id="modalCustomerDetails" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title mb-0 fw-bold"><i class="fas fa-id-card me-2"></i>Customer Information</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                    <span class="text-muted">Code:</span>
                    <strong class="font-monospace text-primary" id="ci_modal_code">—</strong>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                    <span class="text-muted">Name:</span>
                    <strong class="text-dark" id="ci_modal_name">—</strong>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                    <span class="text-muted">Mobile:</span>
                    <span id="ci_modal_mobile">—</span>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                    <span class="text-muted">Address:</span>
                    <span id="ci_modal_address">—</span>
                </div>
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                    <span class="text-muted">Credit Limit:</span>
                    <strong id="ci_modal_credit_limit">Unlimited</strong>
                </div>
                <div class="d-flex justify-content-between pt-1">
                    <span class="text-muted">Previous Balance:</span>
                    <strong class="font-monospace text-danger" id="ci_modal_prev">Rs 0.00</strong>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Variant Modal Placeholder for Shared Logic --}}
<div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2 bg-primary text-white">
                <h6 class="modal-title mb-0 fw-bold"><i class="fas fa-layer-group me-1"></i>Select Variant</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" id="variantModalBody">
                <!-- Variant buttons injected by shared_logic -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Flag for Return mode
    window.isReturnPage = true;
    window.allowZeroStockInSearch = true;
</script>

@include('admin_panel.sale.scripts.shared_logic')

<script>
$(document).ready(function() {

    // Initialize Customer Select2
    if (typeof window.initCustomerSelect2 === 'function') {
        window.initCustomerSelect2();
    } else {
        $('#customerSelect').select2({
            placeholder: 'Search by Name, Code, or Mobile...',
            allowClear: true,
            width: '100%'
        });
    }

    // Auto-sync Order Booker when Customer is selected
    $(document).on('change', '#customerSelect', function() {
        const $opt = $(this).find('option:selected');
        const officerId = $opt.attr('data-sales-officer');
        if (officerId) {
            $('#orderBookerSelect').val(officerId);
            $('#salesOfficerHidden').val(officerId);
        }
    });

    $(document).on('change', '#orderBookerSelect', function() {
        $('#salesOfficerHidden').val($(this).val());
    });

    // Toggle Immediate Cash/Bank Refund Input
    $('#toggleCashRefund').on('change', function() {
        if ($(this).is(':checked')) {
            $('#refundPaymentDetails').removeClass('d-none');
            // Auto fill refund amount with current net total
            let currentNet = parseFloat($('#inputTotalNet').val() || 0);
            if (currentNet > 0 && parseFloat($('#paidAmountInput').val() || 0) === 0) {
                $('#paidAmountInput').val(currentNet.toFixed(2));
            }
        } else {
            $('#refundPaymentDetails').addClass('d-none');
            $('#paidAmountInput').val('0.00');
            $('#refundAccountId').val('');
        }
        if (typeof updateGrandTotals === 'function') {
            updateGrandTotals();
        }
    });

    $('#paidAmountInput').on('input change', function() {
        if (typeof updateGrandTotals === 'function') {
            updateGrandTotals();
        }
    });

    // Custom Post Return Action
    $('#btnPostReturn').on('click', function(e) {
        e.preventDefault();

        // 1. Check if customer is selected
        const isWalkin = $('#is_walkin').val() === '1';
        const custVal = $('#customerSelect').val();
        if (!isWalkin && !custVal) {
            Swal.fire({
                icon: 'warning',
                title: 'Customer Required',
                text: 'Please select a customer or switch to Walk-in customer.'
            });
            $('#customerSelect').select2('open');
            return;
        }

        // 2. Validate at least one row has product and qty > 0
        let hasValidItems = false;
        let totalPcsCount = 0;
        $('#salesTableBody tr.sale-item-row').each(function() {
            let pid = $(this).find('.product-id-hidden').val();
            let qty = parseFloat($(this).find('.total-pieces').val() || 0);
            if (pid && qty > 0) {
                hasValidItems = true;
                totalPcsCount += qty;
            }
        });

        if (!hasValidItems) {
            Swal.fire({
                icon: 'warning',
                title: 'No Return Items',
                text: 'Please add at least one product with a return quantity greater than 0.'
            });
            return;
        }

        // 3. If cash refund is enabled, validate account
        if ($('#toggleCashRefund').is(':checked')) {
            let acc = $('#refundAccountId').val();
            let amt = parseFloat($('#paidAmountInput').val() || 0);
            let netTotal = parseFloat($('#inputTotalNet').val() || 0);

            if (!acc) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Refund Account Required',
                    text: 'Please select the Cash or Bank account from which refund is paid.'
                });
                $('#refundAccountId').focus();
                return;
            }
            if (amt <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Refund Amount Required',
                    text: 'Please enter a valid refund amount.'
                });
                $('#paidAmountInput').focus();
                return;
            }
            if (amt > netTotal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Excess Refund Amount',
                    text: 'Refund paid cannot exceed the Net Return Amount of Rs ' + netTotal.toFixed(2)
                });
                return;
            }
        }

        const netValText = $('#grandTotalNet').text();
        const customerName = isWalkin ? ($('#walkinNameInput').val() || 'Walk-in Customer') : ($('#customerSelect option:selected').text() || 'Customer');

        Swal.fire({
            title: 'Confirm Sale Return?',
            html: `Customer: <strong>${customerName}</strong><br>Total Return Value: <strong class="text-danger">${netValText}</strong><br><small class="text-muted">Stock will be restored to warehouse and customer ledger credited.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#64748B',
            confirmButtonText: '<i class="fas fa-check me-1"></i> Yes, Post Return'
        }).then((res) => {
            if (res.isConfirmed) {
                const $btn = $('#btnPostReturn');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Processing Return...');

                const formData = $('#saleReturnForm').serialize();

                $.ajax({
                    url: "{{ route('sale.return.store') }}",
                    type: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sale Return Posted!',
                            text: response.message || 'Sale return processed successfully.',
                            showCancelButton: true,
                            confirmButtonColor: '#2563EB',
                            cancelButtonColor: '#64748B',
                            confirmButtonText: '<i class="fas fa-file-invoice me-1"></i> View / Print Return',
                            cancelButtonText: '<i class="fas fa-plus me-1"></i> New Return'
                        }).then((action) => {
                            if (action.isConfirmed && response.redirect_url) {
                                window.location.href = response.redirect_url;
                            } else {
                                window.location.href = "{{ route('sale.return.create') }}";
                            }
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(origHtml);
                        let errMsg = 'Failed to process return.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errMsg, 'error');
                    }
                });
            }
        });
    });

    // Reset button
    $('#btnResetForm').on('click', function() {
        Swal.fire({
            title: 'Reset Return Grid?',
            text: 'All added items and values will be cleared.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626'
        }).then((res) => {
            if (res.isConfirmed) {
                window.location.reload();
            }
        });
    });

});
</script>
@endsection
