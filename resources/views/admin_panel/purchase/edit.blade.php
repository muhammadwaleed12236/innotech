@extends('admin_panel.layout.app')

@section('content')
  <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        /* ==================== EDIT PURCHASE — PRO ERP & EXCEL GRID UI ==================== */
        :root {
            --pos-blue: #2563EB;
            --pos-blue-hover: #1D4ED8;
            --pos-blue-soft: #EFF6FF;
            --pos-green: #16A34A;
            --pos-green-soft: #F0FDF4;
            --pos-red: #DC2626;
            --pos-red-soft: #FEF2F2;
            --pos-orange: #F59E0B;
            --pos-orange-soft: #FFFBEB;
            --pos-text: #0F172A;
            --pos-muted: #64748B;
            --pos-border: #CBD5E1;
            --pos-border-strong: #94A3B8;
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

        .purchase-page {
            max-width: 1560px;
            margin: 0 auto;
        }

        /* ---------- PAGE HEADER ---------- */
        .purchase-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 16px;
        }
        .purchase-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .purchase-title-ic {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--pos-blue-soft);
            color: var(--pos-blue);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            flex-shrink: 0;
            border: 1px solid #BFDBFE;
        }
        .purchase-title-main h5 {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -.3px;
            color: var(--pos-text);
            margin-bottom: 2px;
        }
        .purchase-subtitle {
            font-size: 12.5px;
            color: var(--pos-muted);
        }

        /* ---------- CARDS & CONTAINERS ---------- */
        .purchase-card {
            background: var(--pos-card);
            border: 1px solid var(--pos-border);
            border-radius: var(--pos-radius-lg);
            box-shadow: var(--pos-shadow-sm);
            padding: 16px;
            margin-bottom: 16px;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .card-title {
            font-size: 14px;
            font-weight: 800;
            color: var(--pos-text);
            text-transform: uppercase;
            letter-spacing: .4px;
            line-height: 1.3;
        }

        /* ---------- LABELS ---------- */
        .field-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--pos-muted);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: .3px;
            line-height: 1.2;
        }

        /* ---------- INPUTS & FORM CONTROLS ---------- */
        .purchase-page .form-control,
        .purchase-page .form-select {
            height: var(--pos-input-h);
            border: 1px solid var(--pos-border);
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--pos-text);
            background-color: #ffffff;
            box-shadow: none;
            transition: border-color .12s ease, box-shadow .12s ease;
        }
        .purchase-page .form-control::placeholder {
            color: #94A3B8;
            font-weight: 400;
        }
        .purchase-page .form-control:focus,
        .purchase-page .form-select:focus,
        .purchase-page .form-control:focus-visible {
            border: 2px solid var(--pos-blue) !important;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, .15) !important;
            outline: none !important;
            background-color: #ffffff !important;
        }
        .purchase-page .input-readonly,
        .purchase-page input[readonly] {
            background-color: #F8FAFC !important;
            color: #475569 !important;
            border-color: #CBD5E1 !important;
            cursor: default;
            font-weight: 600;
        }

        /* Select2 Vendor styling */
        #vendorSelectWrapper .select2-container--default .select2-selection--single {
            height: var(--pos-input-h) !important;
            border: 1px solid var(--pos-border) !important;
            border-radius: 6px !important;
            background-color: #ffffff !important;
            padding: 0 !important;
        }
        #vendorSelectWrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 10px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: var(--pos-text) !important;
        }

        /* ---------- PRODUCT TABLE (EXCEL GRID STYLE) ---------- */
        .pos-table-wrap {
            overflow-x: auto;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .sales-table {
            min-width: 950px;
            border-collapse: collapse !important;
            width: 100%;
            margin-bottom: 0;
            background: #ffffff;
        }
        .sales-table thead th {
            background: #F1F5F9 !important;
            color: #334155 !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: .5px;
            padding: 9px 8px !important;
            border: 1px solid #CBD5E1 !important;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }
        .sales-table thead th.col-product {
            text-align: left;
            padding-left: 12px !important;
        }
        .sales-table tbody td {
            padding: 3px 5px !important;
            height: 42px !important;
            border: 1px solid #CBD5E1 !important;
            vertical-align: middle;
            background: #ffffff;
        }
        .sales-table tbody tr:nth-child(even) td {
            background: #FAFCFE;
        }
        .sales-table tbody tr:hover td {
            background: #F1F5F9;
        }
        .row-index-cell {
            font-size: 12px;
            font-weight: 700;
            color: #64748B;
            text-align: center;
            background: #F8FAFC !important;
            width: 38px;
        }

        /* Table inputs — clean Excel grid cells that highlight with sharp blue outline on focus */
        .sales-table tbody .form-control,
        .sales-table tbody .form-select {
            height: 34px !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 4px !important;
            padding: 3px 7px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            background: #ffffff !important;
            box-shadow: none !important;
            color: #0F172A !important;
            width: 100% !important;
            transition: border-color .12s ease, box-shadow .12s ease;
        }
        .sales-table tbody .form-control:hover,
        .sales-table tbody .form-select:hover {
            border-color: #94A3B8 !important;
        }
        .sales-table tbody .form-control:focus,
        .sales-table tbody .form-select:focus,
        .sales-table tbody .form-control:focus-visible {
            border: 2px solid #2563EB !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, .15) !important;
            outline: none !important;
        }
        .sales-table tbody input[readonly],
        .sales-table tbody .input-readonly {
            background: #F8FAFC !important;
            color: #475569 !important;
            cursor: default !important;
            font-weight: 600 !important;
            border-color: #E2E8F0 !important;
        }
        .sales-table tbody input[readonly]:focus {
            border-color: #CBD5E1 !important;
            box-shadow: none !important;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="main-container bg-white border shadow-sm mx-auto p-2 rounded-3">

            <form id="purchaseForm" action="{{ route('purchase.update', $purchase->id) }}" method="POST" autocomplete="off">
                @csrf
                @method('PUT')

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                    <div>
                        <a href="{{ route('Purchase.home') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                    <h2 class="header-text text-secondary fw-bold mb-0">Edit Purchase #{{ $purchase->invoice_no }}</h2>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-secondary" id="entryDate">Date: {{ date('d/m/Y') }}</small>
                    </div>
                </div>

                                {{-- TOP HEADER & INVOICE / VENDOR CARD --}}
                <div class="card-panel shadow-sm mb-3 p-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fw-bold mb-1 text-muted small">System No.</label>
                            <input type="text" class="form-control input-readonly" name="invoice_no" value="{{ $purchase->invoice_no }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold mb-1 text-muted small">Vendor Inv#</label>
                            <input type="text" class="form-control" name="purchase_order_no" placeholder="Manual Ref" value="{{ $purchase->purchase_order_no }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold mb-1 text-muted small">Select Vendor</label>
                            <div class="d-flex align-items-center gap-1">
                                <div class="flex-grow-1">
                                    <select class="form-select select2" id="vendorSelect" name="vendor_id">
                                        <option value="" selected disabled>Select Vendor</option>
                                        @foreach ($Vendor as $v)
                                            <option value="{{ $v->id }}" data-phone="{{ $v->phone }}" data-address="{{ $v->address }}" {{ $purchase->vendor_id == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold mb-1 text-muted small">Date</label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d') : date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold mb-1 text-muted small">M.Bill / Remarks</label>
                            <input type="text" class="form-control" name="note" id="remarks" placeholder="Optional notes..." value="{{ $purchase->note }}">
                        </div>
                        <div class="col-md-3 mt-3">
                            <label class="form-label fw-bold mb-1 text-muted small">Warehouse</label>
                            <select name="warehouse_id" class="form-control select2">
                                @foreach ($Warehouse as $w)
                                    <option value="{{ $w->id }}"
                                        {{ $w->id == $purchase->warehouse_id ? 'selected' : '' }}>
                                        {{ $w->warehouse_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row g-3 pb-4 mb-3 mt-2">
                    <div class="col-12">
                        <div class="card-panel shadow-sm p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="section-title mb-0">Purchase Items</div>
                                <button type="button" class="btn btn-sm btn-primary px-3 shadow-sm"
                                    onclick="addBlankRow()">
                                    <i class="bi bi-plus-lg"></i> Add Row
                                </button>
                            </div>

                            <div class="table-responsive border rounded-3 bg-white">
                                <table class="table table-bordered sales-table mb-0" id="purchaseTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 38px;" class="text-center">#</th>
                                            <th class="col-product">Product & Variant</th>
                                            <th class="col-unit" style="width: 100px;">Unit</th>
                                            <th class="col-qty" style="width: 110px;">Qty</th>
                                            <th class="col-price" style="width: 130px;">Purchase Price</th>
                                            <th class="col-disc" style="width: 90px;">Disc %</th>
                                            <th class="col-disc-amt" style="width: 110px;">Disc Amt</th>
                                            <th class="col-amount" style="width: 130px;">Amount</th>
                                            <th class="col-action" style="width: 50px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="purchaseTableBody">
                                        @foreach ($purchase->items as $item)
                                            @php
                                                $sizeMode = $item->size_mode ?? 'by_pieces';
                                                $ppb = (float) ($item->pieces_per_box > 0 ? $item->pieces_per_box : 1);
                                                $uVal = strtolower($unitName ?? 'pcs');
                                                $isCtn = in_array($uVal, ['carton', 'ctn', 'box', 'bandal', 'bundal', 'bndl']) || in_array($sizeMode, ['by_cartons', 'by_bandal']);
                                                $isKg = in_array($uVal, ['kg', 'gm', 'g']);

                                                $displayQty = (float) $item->qty;
                                                if ($isCtn && ($item->loose_qty > 0 || $item->boxes_qty > 0)) {
                                                    $b = (int) $item->boxes_qty;
                                                    $l = (int) $item->loose_qty;
                                                    if ($l > 0) {
                                                        $displayQty = $b . '.' . $l;
                                                    } else {
                                                        $displayQty = $b;
                                                    }
                                                }

                                                $baseProductName = $item->product->item_name ?? 'Product';
                                                $variantNameDisplay = $baseProductName;
                                                $variantInfo = '';
                                                $rawVariantData = $item->color ?? '';
                                                $unitName = !empty($item->unit) ? $item->unit : ($item->product->unit->name ?? 'Pcs');

                                                if (!empty($item->color)) {
                                                    $decodedColor = base64_decode($item->color, true);
                                                    $vData = ($decodedColor !== false) ? json_decode($decodedColor, true) : null;
                                                    if (!$vData) {
                                                        $vData = json_decode($item->color, true);
                                                    }
                                                    if (is_array($vData)) {
                                                        $vName = trim($vData['name'] ?? ($vData['variant_name'] ?? ''));
                                                        $vColorName = trim($vData['color'] ?? '');
                                                        $vSize = trim($vData['size'] ?? '');
                                                        if (empty($item->unit) && !empty($vData['unit'])) {
                                                            $unitName = $vData['unit'];
                                                        }
                                                        $vParts = [];
                                                        $sStr = ($vSize !== '' && $vSize !== '-') ? " {$vSize}" : '';
                                                        $cStr = ($vColorName !== '' && $vColorName !== '-') ? " ({$vColorName})" : '';

                                                        if ($vName !== '') {
                                                            if (stripos($vName, $baseProductName) !== false) {
                                                                $variantNameDisplay = $vName;
                                                            } else {
                                                                $variantNameDisplay = $baseProductName . ' — ' . $vName;
                                                            }
                                                        } else {
                                                            $variantNameDisplay = $baseProductName;
                                                        }

                                                        if ($sStr !== '' && stripos($variantNameDisplay, trim($vSize)) === false) {
                                                            $variantNameDisplay .= $sStr;
                                                        }
                                                        if ($cStr !== '' && stripos($variantNameDisplay, trim($vColorName)) === false) {
                                                            $variantNameDisplay .= $cStr;
                                                        }

                                                        if ($vColorName && $vColorName !== '-') {
                                                            $vParts[] = 'Color: ' . $vColorName;
                                                        }
                                                        if ($vSize && $vSize !== '-') {
                                                            $vParts[] = 'Size: ' . $vSize;
                                                        }
                                                        if (!empty($vParts)) {
                                                            $variantInfo = implode(' | ', $vParts);
                                                        }
                                                    } elseif (is_string($item->color) && trim($item->color) !== '' && trim($item->color) !== '-') {
                                                        $variantNameDisplay = $baseProductName . ' (' . trim($item->color) . ')';
                                                        $variantInfo = trim($item->color);
                                                    }
                                                }

                                                $optionVal = $item->product_id;
                                                if (!empty($rawVariantData)) {
                                                    $encodedVar = (base64_decode($rawVariantData, true) !== false) ? $rawVariantData : base64_encode($rawVariantData);
                                                    $optionVal = $item->product_id . '|variant|' . $encodedVar;
                                                }

                                                $gross = $item->line_total + $item->item_discount;
                                                $dPct = $gross > 0 ? ($item->item_discount / $gross) * 100 : 0;
                                            @endphp
                                            <tr data-sizemode="{{ $sizeMode }}"
                                                data-pieces_per_m2="{{ $item->pieces_per_m2 }}">
                                                <td class="row-index-cell text-center">{{ $loop->iteration }}</td>
                                                <td>
                                                    <select class="form-select product-select2" name="product_id[]">
                                                        <option value="{{ $optionVal }}" selected>
                                                            {{ $variantNameDisplay }} ({{ $item->product->item_code ?? 'SKU' }})
                                                        </option>
                                                    </select>
                                                    <div class="variant-badge-wrapper px-2 py-1 small text-muted d-flex gap-2 align-items-center {{ empty($variantInfo) ? 'd-none' : '' }}">
                                                        <span class="badge bg-light text-dark border variant-badge">{{ $variantInfo }}</span>
                                                    </div>
                                                    {{-- Snapshots --}}
                                                    <input type="hidden" name="size_mode[]" class="hidden-size-mode"
                                                        value="{{ $sizeMode }}">
                                                    <input type="hidden" name="pieces_per_box[]"
                                                        class="hidden-pieces-per-box" value="{{ $ppb }}">
                                                    <input type="hidden" name="pieces_per_m2[]"
                                                        class="hidden-pieces-per-m2" value="{{ $item->pieces_per_m2 }}">
                                                    <input type="hidden" name="boxes_qty[]" class="hidden-boxes-qty" value="{{ $item->boxes_qty ?? 0 }}">
                                                    <input type="hidden" name="loose_qty[]" class="hidden-loose-qty" value="{{ $item->loose_qty ?? 0 }}">
                                                    <input type="hidden" name="length[]" class="hidden-length"
                                                        value="{{ $item->length }}">
                                                    <input type="hidden" name="width[]" class="hidden-width"
                                                        value="{{ $item->width }}">
                                                    <input type="hidden" name="color[]" class="hidden-variant-data"
                                                        value="{{ $rawVariantData }}">
                                                    <input type="hidden" class="product-id-hidden" value="{{ $item->product_id }}">
                                                    <input type="hidden" class="batch-id-hidden" name="batch_id[]" value="{{ $item->batch_id ?? '' }}">
                                                    <input type="hidden" class="batch-no-hidden" name="batch_no[]" value="{{ $item->batch_no ?? '' }}">
                                                    <input type="hidden" class="serials-hidden" name="serials[]" value="{{ isset($item->serials) ? json_encode($item->serials) : '' }}">
                                                    <div class="row-tracking-badges mt-1 d-flex flex-wrap gap-1">
                                                        @if(!empty($item->batch_no))
                                                            <span class="badge bg-info text-dark" style="font-size:0.7rem;"><i class="fas fa-layer-group me-1"></i>Batch: {{ $item->batch_no }}</span>
                                                        @endif
                                                        @if(!empty($item->serials) && count($item->serials) > 0)
                                                            <span class="badge bg-secondary text-light" style="font-size:0.7rem;" title="{{ implode(', ', $item->serials) }}"><i class="fas fa-barcode me-1"></i>{{ count($item->serials) }} IMEI(s)</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    @php
                                                        $uVal = strtolower($unitName ?? 'pcs');
                                                        $isCtn = in_array($uVal, ['carton', 'ctn', 'box', 'bandal', 'bundal', 'bndl']);
                                                        $isKg = in_array($uVal, ['kg', 'gm', 'g']);
                                                        $btnClass = $isCtn ? 'btn-outline-success' : ($isKg ? 'btn-outline-primary' : 'btn-outline-info');
                                                    @endphp
                                                    <button type="button" class="btn btn-sm {{ $btnClass }} fw-bold unit-toggle-btn py-0 px-2" data-unit="{{ $unitName }}" title="Click to toggle unit (Carton ↔ Pcs / Kg ↔ Gm)" style="font-size:0.75rem; min-width: 55px; cursor: pointer;">{{ $unitName }}</button>
                                                    <input type="hidden" name="unit[]" class="unit-input-val" value="{{ $unitName }}">
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0.0001" name="qty[]"
                                                        class="form-control text-center main-qty-input"
                                                        value="{{ $displayQty }}" placeholder="Qty">
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" name="price[]" class="form-control text-end price"
                                                            step="0.01" value="{{ (float) $item->price }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" name="item_discount[]" class="form-control text-end item-disc-percent"
                                                        step="0.01" value="{{ round($dPct, 2) }}">
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        class="form-control text-end input-readonly item-disc-amt"
                                                        value="{{ (float) $item->item_discount }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control text-end input-readonly row-total"
                                                        value="{{ (float) $item->line_total }}" readonly>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger remove-row border-0"><i
                                                             class="bi bi-x-lg"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold text-muted">Total Amount:</td>
                                            <td class="text-end fw-bold fs-6 text-dark" colspan="2"><span id="totalAmount">0.00</span>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUMMARY --}}
                <div class="row g-3 mt-1">
                    {{-- LEFT: Payment / Receipt Voucher --}}
                    <div class="col-lg-7">
                        <div class="card-panel shadow-sm">
                            <div class="section-title mb-3">Payment / Receipt Voucher</div>
                            <div id="paymentWrapper" class="border rounded p-3 bg-light mb-3">
                                @if (isset($existingPayments) && $existingPayments->isNotEmpty())
                                    @foreach ($existingPayments as $pIndex => $pDetail)
                                        <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                                            <select class="form-select rv-account" name="payment_account_id[]"
                                                style="max-width: 300px; flex-grow: 1;">
                                                <option value="" disabled>Select Account</option>
                                                @foreach ($accounts as $acc)
                                                    <option value="{{ $acc->id }}" {{ $acc->id == $pDetail->account_id ? 'selected' : '' }}>
                                                        {{ $acc->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="number" class="form-control text-end payment-amount"
                                                name="payment_amount[]" value="{{ (float) $pDetail->credit }}" placeholder="Amount" style="width:140px" step="0.01">
                                            @if ($loop->first)
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPayment">
                                                    <i class="bi bi-plus"></i> Add
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-payment">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                                        <select class="form-select rv-account" name="payment_account_id[]"
                                            style="max-width: 300px; flex-grow: 1;">
                                            <option value="" selected disabled>Select Account</option>
                                            @foreach ($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" class="form-control text-end payment-amount"
                                            name="payment_amount[]" placeholder="Amount" style="width:140px" step="0.01">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddPayment">
                                            <i class="bi bi-plus"></i> Add
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div class="text-end">
                                <span class="me-2 fw-bold text-muted">Total Paid:</span>
                                <span class="fw-bold fs-6 text-success" id="totalPaid">0.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Summary --}}
                    <div class="col-lg-5">
                        <div class="card-panel shadow-sm">
                            <div class="section-title mb-3">Summary</div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-medium">Total Qty (Pieces)</div>
                                <div class="col-5 text-end"><span id="tQty" class="fw-bold">0</span></div>
                            </div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-medium">Sub-Total</div>
                                <div class="col-5 text-end fw-bold"><span id="tSub">0.00</span></div>
                                <input type="hidden" name="subtotal" id="subtotalInput">
                            </div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-medium">Bill Discount</div>
                                <div class="col-5 text-end d-flex gap-1">
                                    @php
                                        $inlineVal = $purchase->items->sum('item_discount');
                                        $bSub = (float) $purchase->subtotal + $inlineVal;
                                        $bDisc = (float) $purchase->discount + $inlineVal;
                                        $bPct = $bSub > 0 ? ($bDisc / $bSub) * 100 : 0;
                                    @endphp
                                    <input type="number" class="form-control text-end form-control-sm"
                                        id="billDiscountPct" value="{{ round($bPct, 2) }}" placeholder="%" style="width: 70px;" step="0.01">
                                    <input type="number" class="form-control text-end form-control-sm"
                                        id="billDiscount" value="{{ (float) $bDisc }}" step="0.01">
                                    <input type="hidden" name="discount" id="discountInput" value="{{ (float) $purchase->discount }}">
                                </div>
                            </div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-medium">Extra Cost</div>
                                <div class="col-5 text-end">
                                    <input type="number" class="form-control text-end form-control-sm" name="extra_cost"
                                        id="extraCost" value="{{ (float) $purchase->extra_cost }}">
                                </div>
                            </div>
                            <hr class="my-2 border-secondary">
                            <div class="row py-2">
                                <div class="col-6 fw-bold fs-5 text-primary">Net Payable</div>
                                <div class="col-6 text-end fw-bold fs-5 text-primary"><span id="tPayable">0.00</span>
                                </div>
                                <input type="hidden" name="net_amount" id="netAmountInput">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success btn-submit-update px-5 fw-bold shadow-sm">
                        <i class="bi bi-save me-2"></i> Update Purchase
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Modal: Enter Batch Details for Purchase -->
    <div class="modal fade" id="modalSelectBatch" tabindex="-1" aria-labelledby="modalSelectBatchLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom bg-light px-3 py-2">
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalSelectBatchLabel">
                        <i class="fas fa-layer-group text-primary me-1"></i> Enter New Batch Details
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <input type="hidden" id="activeBatchRowIndex" value="">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Batch Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm font-monospace fw-bold" id="inputBatchNo" placeholder="e.g. BATCH-2026-001">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">MFG Date</label>
                            <input type="date" class="form-control form-control-sm" id="inputBatchMfgDate">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">Expiry Date</label>
                            <input type="date" class="form-control form-control-sm" id="inputBatchExpiryDate">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm fw-bold px-3" id="btnSaveBatchInfo">
                        <i class="fas fa-check me-1"></i> Save Batch Info
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Enter / Scan Serial & IMEI Numbers for Purchase -->
    <div class="modal fade" id="modalSelectSerial" tabindex="-1" aria-labelledby="modalSelectSerialLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom bg-light px-3 py-2">
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalSelectSerialLabel">
                        <i class="fas fa-barcode text-success me-1"></i> Add / Scan Serial & IMEI Numbers
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <input type="hidden" id="activeSerialRowIndex" value="">
                    
                    <!-- Single Barcode Scan Input -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Scan / Type Single IMEI (Press Enter to Add)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="fas fa-barcode"></i></span>
                            <input type="text" class="form-control font-monospace" id="inputSingleSerial" placeholder="Scan barcode or type IMEI number...">
                            <button type="button" class="btn btn-primary fw-bold" id="btnAddSingleSerial">Add IMEI</button>
                        </div>
                    </div>

                    <!-- Bulk Paste Input -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Or Paste Multiple IMEIs (One per line or comma separated)</label>
                        <textarea class="form-control font-monospace form-control-sm" id="inputBulkSerials" rows="3" placeholder="860000000000001&#10;860000000000002&#10;860000000000003"></textarea>
                        <div class="text-end mt-1">
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="btnApplyBulkSerials">
                                <i class="fas fa-plus-circle me-1"></i> Add Bulk IMEIs
                            </button>
                        </div>
                    </div>

                    <!-- List of Entered IMEIs -->
                    <div class="border rounded p-2 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                            <span class="fw-bold small text-secondary">Entered IMEIs List (<span id="purchaseSerialCount" class="text-success fs-6">0</span>)</span>
                            <button type="button" class="btn btn-link text-danger p-0 small text-decoration-none" id="btnClearAllSerials">Clear All</button>
                        </div>
                        <div id="enteredSerialsContainer" class="d-flex flex-wrap gap-1 overflow-auto p-1 bg-white border rounded" style="max-height: 180px; min-height: 80px;">
                            <div class="text-muted text-center w-100 py-3 small">No IMEIs added yet. Scan barcode or paste IMEIs above.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success btn-sm fw-bold px-3" id="btnApplySelectedSerials">
                        <i class="fas fa-check me-1"></i> Apply IMEIs to Purchase
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Init Global Select2
            $('.select2').select2({
                width: '100%',
                dropdownParent: $(document.body)
            });

            $(document).on('select2:open', function() {
                setTimeout(function() {
                    const searchInput = document.querySelector('.select2-container--open .select2-search__field');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }, 50);
            });

            // Initialize existing product selects
            $('.product-select2').each(function() {
                initProductSelect2($(this));
            });

            // Recalc rows & payments on initial load
            $('#purchaseTableBody tr').each(function() {
                recalcRow($(this));
            });
            recalcPayments();
            recalcAll();

            // Unit Toggle Handler (Carton ↔ Pcs / Kg ↔ Gm)
            $(document).on('click', '.unit-toggle-btn', function() {
                const $btn = $(this);
                const $row = $btn.closest('tr');
                const sizeMode = $row.data('sizemode') || $row.find('.hidden-size-mode').val();
                const packQty = parseFloat($row.find('.hidden-pieces-per-box').val()) || parseFloat($row.data('pieces_per_box')) || 1;
                let currentUnit = ($btn.attr('data-unit') || $btn.text() || '').trim();
                const $priceInp = $row.find('.price');
                let curPrice = parseFloat($priceInp.val()) || 0;

                const isCartonOrPcs = (['by_cartons', 'by_bandal'].includes(sizeMode) || packQty > 1 || ['carton', 'ctn', 'pcs', 'pc', 'piece'].includes(currentUnit.toLowerCase()));

                if (isCartonOrPcs) {
                    if (['carton', 'ctn', 'bandal', 'bundal', 'bndl'].includes(currentUnit.toLowerCase())) {
                        // Switch from Carton to Pcs
                        currentUnit = 'Pcs';
                        $btn.text('Pcs')
                            .removeClass('btn-outline-success btn-outline-primary')
                            .addClass('btn-outline-info')
                            .attr('data-unit', 'Pcs');
                        $row.find('.unit-input-val').val('Pcs');

                        if (packQty > 1 && curPrice > 0) {
                            let piecePrice = curPrice / packQty;
                            $priceInp.val(piecePrice % 1 === 0 ? piecePrice : piecePrice.toFixed(2));
                        }
                    } else {
                        // Switch from Pcs to Carton
                        currentUnit = (sizeMode === 'by_bandal') ? 'Bundal' : 'Carton';
                        $btn.text(currentUnit)
                            .removeClass('btn-outline-info btn-outline-primary')
                            .addClass('btn-outline-success')
                            .attr('data-unit', currentUnit);
                        $row.find('.unit-input-val').val(currentUnit);

                        if (packQty > 1 && curPrice > 0) {
                            let cartonPrice = curPrice * packQty;
                            $priceInp.val(cartonPrice % 1 === 0 ? cartonPrice : cartonPrice.toFixed(2));
                        }
                    }
                    recalcRow($row);
                    recalcAll();
                } else if (sizeMode === 'by_kg' || sizeMode === 'by_gm') {
                    if (currentUnit.toLowerCase() === 'kg') {
                        currentUnit = 'Gm';
                        $btn.text('Gm').removeClass('btn-outline-primary').addClass('btn-outline-info').attr('data-unit', 'Gm');
                    } else {
                        currentUnit = 'Kg';
                        $btn.text('Kg').removeClass('btn-outline-info').addClass('btn-outline-primary').attr('data-unit', 'Kg');
                    }
                    $row.find('.unit-input-val').val(currentUnit);
                    recalcRow($row);
                    recalcAll();
                }
            });

            function updateRowIndexes() {
                $('#purchaseTableBody tr').each(function(index) {
                    $(this).find('.row-index-cell').text(index + 1);
                });
            }

            // Add Row
            window.addBlankRow = function() {
                const html = `
                <tr>
                    <td class="row-index-cell text-center">1</td>
                    <td>
                        <select class="form-select product-select2" name="product_id[]"></select>
                        <div class="variant-badge-wrapper px-2 py-1 small text-muted d-flex gap-2 align-items-center d-none">
                            <span class="badge bg-light text-dark border variant-badge"></span>
                        </div>
                        <input type="hidden" name="size_mode[]" class="hidden-size-mode">
                        <input type="hidden" name="pieces_per_box[]" class="hidden-pieces-per-box" value="1">
                        <input type="hidden" name="pieces_per_m2[]" class="hidden-pieces-per-m2" value="0">
                        <input type="hidden" name="price_per_carton[]" class="hidden-price-per-carton" value="0">
                        <input type="hidden" name="boxes_qty[]" class="hidden-boxes-qty" value="0">
                        <input type="hidden" name="loose_qty[]" class="hidden-loose-qty" value="0">
                        <input type="hidden" name="length[]" class="hidden-length">
                        <input type="hidden" name="width[]" class="hidden-width">
                        <input type="hidden" name="color[]" class="hidden-variant-data">
                        <input type="hidden" class="product-id-hidden" value="">
                        <input type="hidden" class="batch-id-hidden" name="batch_id[]" value="">
                        <input type="hidden" class="batch-no-hidden" name="batch_no[]" value="">
                        <input type="hidden" class="serials-hidden" name="serials[]" value="">
                        <div class="row-tracking-badges mt-1 d-flex flex-wrap gap-1"></div>
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-outline-info fw-bold unit-toggle-btn py-0 px-2" data-unit="Pcs" title="Click to toggle unit (Carton ↔ Pcs / Kg ↔ Gm)" style="font-size:0.75rem; min-width: 55px; cursor: pointer;">Pcs</button>
                        <input type="hidden" name="unit[]" class="unit-input-val" value="Pcs">
                    </td>
                    <td>
                        <input type="number" step="any" min="0.01" name="qty[]" class="form-control text-center main-qty-input" value="1" placeholder="Qty">
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" name="price[]" class="form-control text-end price" value="0">
                        </div>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="item_discount[]" class="form-control text-end item-disc-percent" value="0">
                    </td>
                    <td>
                        <input type="number" class="form-control text-end input-readonly item-disc-amt" value="0.00" readonly>
                    </td>
                    <td>
                        <input type="number" class="form-control text-end input-readonly row-total" value="0.00" readonly>
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row border-0"><i class="bi bi-x-lg"></i></button>
                    </td>
                </tr>`;
                const $row = $(html);
                $('#purchaseTableBody').append($row);
                initProductSelect2($row.find('.product-select2'));
                recalcRow($row);
                recalcAll();
                updateRowIndexes();
            };

            // Remove Row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                recalcAll();
                updateRowIndexes();
            });

            // Inputs -> Calc
            $('#purchaseTableBody').on('input', '.main-qty-input, .price, .item-disc-percent', function() {
                recalcRow($(this).closest('tr'));
                recalcAll();
            });

            $('#billDiscount, #billDiscountPct, #extraCost').on('input', function() {
                recalcAll();
            });

            function normalizeDiscountInput() {
                let totalInlineDiscount = 0;
                $('#purchaseTableBody tr').each(function() {
                    const rowDiscAmt = parseFloat($(this).find('.item-disc-amt').val()) || 0;
                    totalInlineDiscount += rowDiscAmt;
                });

                let billDiscVal = parseFloat($('#billDiscount').val());
                if (isNaN(billDiscVal) || billDiscVal < totalInlineDiscount) {
                    $('#billDiscount').val(totalInlineDiscount.toFixed(2));
                }
                recalcAll();
            }

            $('#billDiscount, #billDiscountPct').on('blur', function() {
                normalizeDiscountInput();
            });

            $('#purchaseForm').on('submit', function() {
                normalizeDiscountInput();
            });

            // --- Payment Section Logic ---
            $('#btnAddPayment').on('click', function() {
                const row = `
                <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                    <select class="form-select rv-account" name="payment_account_id[]" style="max-width: 300px; flex-grow: 1;">
                        <option value="" selected disabled>Select Account</option>
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control text-end payment-amount" name="payment_amount[]" placeholder="Amount" style="width:140px" step="0.01">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-payment">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>`;
                $('#paymentWrapper').append(row);
            });

            $(document).on('click', '.remove-payment', function() {
                $(this).closest('.payment-row').remove();
                recalcPayments();
            });

            $(document).on('input', '.payment-amount', function() {
                recalcPayments();
            });

            function recalcPayments() {
                let total = 0;
                $('.payment-amount').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#totalPaid').text(total.toFixed(2));
            }

            function recalcRow($row) {
                const qtyStr = ($row.find('.main-qty-input').val() || '').toString();
                const qty = parseFloat(qtyStr) || 0;
                const price = parseFloat($row.find('.price').val()) || 0;
                const discPct = parseFloat($row.find('.item-disc-percent').val()) || 0;
                const sizeMode = $row.data('sizemode') || $row.find('.hidden-size-mode').val();
                const unitVal = ($row.find('.unit-input-val').val() || '').toLowerCase();
                const pieces_per_m2 = parseFloat($row.data('pieces_per_m2')) || parseFloat($row.find('.hidden-pieces-per-m2').val()) || 0;

                const ppb = parseFloat($row.find('.hidden-pieces-per-box').val()) || parseFloat($row.data('pieces_per_box')) || 1;

                let gross = 0;
                const isPiece = (unitVal === 'pcs' || unitVal === 'pc' || unitVal === 'piece');
                const isCarton = (unitVal === 'carton' || unitVal === 'ctn' || unitVal === 'box' || (!isPiece && sizeMode === 'by_cartons'));

                if (sizeMode === 'by_size') {
                    gross = (pieces_per_m2 || 1) * qty * price;
                } else if (unitVal === 'gm' || unitVal === 'g') {
                    gross = (qty / 1000.0) * price;
                } else if (isPiece) {
                    gross = qty * price;
                    const bQty = ppb > 1 ? Math.floor(qty / ppb) : 0;
                    $row.find('.hidden-boxes-qty').val(bQty);
                    $row.find('.hidden-loose-qty').val(qty);
                } else if (isCarton) {
                    let s = qtyStr.trim();
                    if (s.startsWith('.')) s = '0' + s;
                    if (s.includes('.')) {
                        const parts = s.split('.');
                        const boxes = parseInt(parts[0]) || 0;
                        const loose = parseInt(parts[1]) || 0;
                        const piecePrice = ppb > 0 ? (price / ppb) : price;
                        gross = (boxes * price) + (loose * piecePrice);
                        $row.find('.hidden-boxes-qty').val(boxes);
                        $row.find('.hidden-loose-qty').val(loose);
                    } else {
                        const boxes = parseInt(s) || 0;
                        gross = boxes * price;
                        $row.find('.hidden-boxes-qty').val(boxes);
                        $row.find('.hidden-loose-qty').val(0);
                    }
                } else {
                    gross = qty * price;
                }

                const discAmt = gross * (discPct / 100);
                const lineTotal = Math.max(0, gross - discAmt);

                $row.find('.item-disc-amt').val(discAmt.toFixed(2));
                $row.find('.row-total').val(lineTotal.toFixed(2));
            }

            function recalcAll() {
                let totalQty = 0;
                let subtotal = 0;
                let totalInlineDiscount = 0;

                $('#purchaseTableBody tr').each(function() {
                    const qty = parseFloat($(this).find('.main-qty-input').val()) || 0;
                    const total = parseFloat($(this).find('.row-total').val()) || 0;
                    const rowDiscAmt = parseFloat($(this).find('.item-disc-amt').val()) || 0;

                    totalQty += qty;
                    subtotal += total;
                    totalInlineDiscount += rowDiscAmt;
                });

                const grossSubtotal = subtotal + totalInlineDiscount;

                $('#tQty').text(totalQty.toFixed(2));
                $('#tSub').text(subtotal.toFixed(2));
                $('#subtotalInput').val(subtotal.toFixed(2));
                $('#totalAmount').text(subtotal.toFixed(2));

                let additionalDiscount = parseFloat($('#discountInput').val()) || 0;
                let billDiscVal = parseFloat($('#billDiscount').val());

                if ($(document.activeElement).is('#billDiscount') || $(document.activeElement).is('#billDiscountPct')) {
                    if ($(document.activeElement).is('#billDiscountPct')) {
                        const pct = parseFloat($('#billDiscountPct').val()) || 0;
                        billDiscVal = grossSubtotal * (pct / 100);
                        $('#billDiscount').val(billDiscVal.toFixed(2));
                    }
                    if (!isNaN(billDiscVal)) {
                        additionalDiscount = Math.max(0, billDiscVal - totalInlineDiscount);
                    } else {
                        additionalDiscount = 0;
                    }
                } else {
                    billDiscVal = totalInlineDiscount + additionalDiscount;
                    $('#billDiscount').val(billDiscVal.toFixed(2));
                }
                
                const pct = grossSubtotal > 0 ? (billDiscVal / grossSubtotal) * 100 : 0;
                $('#billDiscountPct').val(pct.toFixed(2));

                $('#discountInput').val(additionalDiscount.toFixed(2));

                const extraCost = parseFloat($('#extraCost').val()) || 0;

                const net = subtotal - additionalDiscount + extraCost;

                $('#tPayable').text(net.toFixed(2));
                $('#netAmountInput').val(net.toFixed(2));
            }

            function initProductSelect2($el) {
                if (!$el || !$el.length) return;
                if ($el.hasClass('select2-hidden-accessible')) {
                    try { $el.select2('destroy'); } catch(e) {}
                }
                $el.select2({
                    placeholder: 'Search Product (Name / SKU / Barcode / Variant)...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(document.body),
                    ajax: {
                        url: '{{ route('products.ajax.search') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                term: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results || [],
                                pagination: {
                                    more: (data.pagination && data.pagination.more) ? true : false
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0,
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                    templateResult: formatProduct,
                    templateSelection: formatSelection
                });

                $el.off('select2:open').on('select2:open', function() {
                    setTimeout(function() {
                        const searchInput = document.querySelector('.select2-container--open .select2-search__field');
                        if (searchInput) {
                            searchInput.focus();
                        }
                    }, 50);
                });

                $el.on('select2:select', function(e) {
                    const data = e.params.data;
                    const $row = $(this).closest('tr');
                    const pid = data.id;

                    $row.find('.product-id-hidden').val(pid);

                    let unitName = data.unit_name || 'Pcs';
                    const ppb = parseFloat(data.pieces_per_box || data.ppb) || 1;
                    const isCartonMode = (['by_cartons', 'by_bandal'].includes(data.size_mode) || unitName.toLowerCase() === 'carton' || unitName.toLowerCase() === 'ctn' || ppb > 1);

                    // Dynamic Unit & Style
                    if (isCartonMode) {
                        unitName = (data.size_mode === 'by_bandal') ? 'Bundal' : 'Carton';
                        $row.find('.unit-toggle-btn')
                            .removeClass('btn-outline-primary btn-outline-info')
                            .addClass('btn-outline-success')
                            .attr('data-unit', unitName)
                            .text(unitName);
                        $row.find('.unit-input-val').val(unitName);
                    } else if (data.size_mode === 'by_kg' || data.size_mode === 'by_gm') {
                        unitName = 'Kg';
                        $row.find('.unit-toggle-btn')
                            .removeClass('btn-outline-info btn-outline-success')
                            .addClass('btn-outline-primary')
                            .attr('data-unit', 'Kg')
                            .text('Kg');
                        $row.find('.unit-input-val').val('Kg');
                    } else {
                        $row.find('.unit-toggle-btn')
                            .removeClass('btn-outline-primary btn-outline-success')
                            .addClass('btn-outline-info')
                            .attr('data-unit', unitName)
                            .text(unitName);
                        $row.find('.unit-input-val').val(unitName);
                    }

                    // Variant Info Display (Size, Color)
                    let variantBadgeText = '';
                    if (data.variant_data) {
                        try {
                            const vObj = JSON.parse(atob(data.variant_data));
                            const parts = [];
                            if (vObj.size && vObj.size !== '-') parts.push('Size: ' + vObj.size);
                            if (vObj.color && vObj.color !== '-') parts.push('Color: ' + vObj.color);
                            if (parts.length > 0) {
                                variantBadgeText = parts.join(' | ');
                            }
                        } catch (err) {}
                    }
                    
                    if (variantBadgeText) {
                        $row.find('.variant-badge').text(variantBadgeText);
                        $row.find('.variant-badge-wrapper').removeClass('d-none');
                    } else {
                        $row.find('.variant-badge-wrapper').addClass('d-none');
                    }

                    // Prices
                    const pPiece = parseFloat(data.purchase_price_per_piece) || parseFloat(data.trade_price) || 0;
                    const pBox = parseFloat(data.purchase_price_per_box) || (pPiece * ppb);
                    const pM2 = parseFloat(data.purchase_price_per_m2) || 0;
                    const sizeMode = data.size_mode || 'std';

                    // Populate Snapshots
                    $row.find('.hidden-size-mode').val(data.size_mode || '');
                    $row.find('.hidden-pieces-per-box').val(ppb);
                    $row.find('.hidden-pieces-per-m2').val(data.pieces_per_m2 || 0);
                    $row.find('.hidden-price-per-carton').val(pBox);
                    $row.find('.hidden-length').val(data.length || '');
                    $row.find('.hidden-width').val(data.width || '');
                    $row.find('.hidden-variant-data').val(data.variant_data || '');

                    // Set default discount
                    $row.find('.item-disc-percent').val(data.purchase_discount_percent || 0);

                    // Set Price based on unit mode
                    let finalPrice = pPiece;
                    if (sizeMode === 'by_size') {
                        finalPrice = pM2;
                    } else if (isCartonMode) {
                        finalPrice = pBox > 0 ? pBox : (pPiece * ppb);
                    } else {
                        finalPrice = pPiece;
                    }

                    $row.find('.price').val(finalPrice % 1 === 0 ? finalPrice : finalPrice.toFixed(2));

                    // Data Attributes
                    $row.data('sizemode', sizeMode);
                    $row.data('pieces_per_m2', Number(data.pieces_per_m2) || 0);
                    $row.data('p_price_piece', pPiece);
                    $row.data('p_price_box', pBox);
                    $row.data('pieces_per_box', ppb);

                    // Qty default
                    let curQty = parseFloat($row.find('.main-qty-input').val()) || 0;
                    if (curQty <= 0) {
                        $row.find('.main-qty-input').val(1);
                    }

                    $row.find('.main-qty-input').focus().select();
                    recalcRow($row);
                    recalcAll();

                    // Auto-open Batch / Serial IMEI tracking modal if applicable
                    checkAndOpenProductTracking($row, pid);
                });
            }

            function formatProduct(repo) {
                if (repo.loading) return repo.text;
                let name = repo.name || repo.text || '';
                let sku = repo.sku || 'N/A';
                let unit = repo.unit_name || 'Pcs';
                let stock = repo.stock !== undefined ? repo.stock : 0;
                let stockVal = parseFloat(repo.stock_pieces !== undefined ? repo.stock_pieces : repo.stock) || 0;
                let buyPrice = parseFloat(repo.purchase_price_per_piece || repo.trade_price || 0);

                if (repo.element) {
                    const $el = $(repo.element);
                    if ($el.val() === '') return repo.text;
                    sku = $el.data('sku') || sku;
                    stock = $el.data('stock') !== undefined ? $el.data('stock') : stock;
                    stockVal = parseFloat(stock) || 0;
                    name = $el.data('name') || name || $el.text();
                }

                let badgeClass = stockVal > 0 ? 'bg-success' : 'bg-secondary';

                return '<div class="d-flex align-items-center justify-content-between w-100 py-1" style="color: #0f172a;">' +
                    '<div>' +
                        '<div class="fw-bold text-dark">' + name + '</div>' +
                        '<small class="text-muted" style="font-size: 11px;">SKU: ' + sku + ' | Unit: ' + unit + ' | Buy Price: Rs. ' + buyPrice.toFixed(2) + '</small>' +
                    '</div>' +
                    '<div>' +
                        '<span class="badge ' + badgeClass + ' rounded-pill px-2 py-1">Stock: ' + stock + '</span>' +
                    '</div>' +
                '</div>';
            }

            function formatSelection(repo) {
                if (repo.element) {
                    const $el = $(repo.element);
                    if (!$el.val()) return repo.text || '';
                    const name = $el.data('name') || repo.name || repo.text || '';
                    const sku = $el.data('sku') || repo.sku || '';
                    return sku ? (name + ' (SKU: ' + sku + ')') : name;
                }
                return repo.name || repo.text;
            }

            // --- Product Tracking for Purchase (New Batch / New Serial IMEI Entry Modals) ---
            window.checkAndOpenProductTracking = function($row, productId, warehouseId) {
                if (!productId) return;
                warehouseId = warehouseId || $('[name="warehouse_id"]').val() || 1;

                // Check Batches first
                $.get('{{ route("sale.get_batches") }}', { product_id: productId, warehouse_id: warehouseId }).done(function(res) {
                    if (res.success && (res.is_batch_product || (res.batches && res.batches.length > 0))) {
                        openBatchSelectModal($row);
                        return;
                    }

                    // If not batch product, check Serials / IMEIs
                    $.get('{{ route("sale.get_serials") }}', { product_id: productId, warehouse_id: warehouseId }).done(function(sRes) {
                        if (sRes.success && (sRes.is_serial_product || (sRes.serials && sRes.serials.length > 0))) {
                            openSerialSelectModal($row);
                        }
                    });
                });
            };

            // Open Purchase Batch Modal
            function openBatchSelectModal($row) {
                const rowIndex = $('#purchaseTableBody tr').index($row);
                $('#activeBatchRowIndex').val(rowIndex);

                const existingBatchNo = $row.find('.batch-no-hidden').val() || '';
                $('#inputBatchNo').val(existingBatchNo);

                $('#modalSelectBatch').modal('show');
                setTimeout(() => $('#inputBatchNo').focus(), 300);
            }

            // Save Batch Info Button
            $(document).on('click', '#btnSaveBatchInfo', function() {
                const batchNo = $('#inputBatchNo').val().trim();
                const rowIndex = $('#activeBatchRowIndex').val();
                const $row = $('#purchaseTableBody tr').eq(rowIndex);

                if (!batchNo) {
                    Swal.fire('Required', 'Please enter a Batch Number.', 'warning');
                    return;
                }

                if ($row.length) {
                    $row.find('.batch-no-hidden').val(batchNo);

                    // Update tracking badges display on row
                    let badgeHtml = `<span class="badge bg-info text-dark" style="font-size:0.7rem;"><i class="fas fa-layer-group me-1"></i>Batch: ${batchNo}</span>`;
                    $row.find('.row-tracking-badges').html(badgeHtml);

                    $('#modalSelectBatch').modal('hide');
                    $row.find('.main-qty-input').focus().select();
                }
            });

            // Open Purchase Serial / IMEI Modal
            let currentPurchaseSerials = [];
            function openSerialSelectModal($row) {
                const rowIndex = $('#purchaseTableBody tr').index($row);
                $('#activeSerialRowIndex').val(rowIndex);

                // Pre-selected/entered serials if any
                currentPurchaseSerials = [];
                try {
                    const raw = $row.find('.serials-hidden').val();
                    if (raw) {
                        currentPurchaseSerials = JSON.parse(raw);
                    }
                } catch(e) {}

                renderEnteredSerialsList();
                $('#modalSelectSerial').modal('show');
                setTimeout(() => $('#inputSingleSerial').focus(), 300);
            }

            function renderEnteredSerialsList() {
                let html = '';
                if (!currentPurchaseSerials || currentPurchaseSerials.length === 0) {
                    html = '<div class="text-muted text-center w-100 py-3 small">No IMEIs added yet. Scan barcode or paste IMEIs above.</div>';
                } else {
                    currentPurchaseSerials.forEach((sNum, idx) => {
                        html += `
                            <span class="badge bg-light text-dark border p-2 d-flex align-items-center gap-1 font-monospace" style="font-size:0.82rem;">
                                <i class="fas fa-barcode text-success"></i> ${sNum}
                                <i class="fas fa-times text-danger remove-serial-item ms-1" data-index="${idx}" style="cursor:pointer;" title="Remove IMEI"></i>
                            </span>`;
                    });
                }
                $('#enteredSerialsContainer').html(html);
                $('#purchaseSerialCount').text(currentPurchaseSerials.length);
            }

            // Single Serial Add / Scan (on Enter or Button click)
            function addSingleSerialFromInput() {
                const sNum = $('#inputSingleSerial').val().trim();
                if (!sNum) return;

                if (currentPurchaseSerials.includes(sNum)) {
                    Swal.fire('Duplicate', 'This IMEI / Serial number is already added.', 'warning');
                    $('#inputSingleSerial').val('').focus();
                    return;
                }

                currentPurchaseSerials.push(sNum);
                renderEnteredSerialsList();
                $('#inputSingleSerial').val('').focus();
            }

            $(document).on('keypress', '#inputSingleSerial', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    addSingleSerialFromInput();
                }
            });

            $(document).on('click', '#btnAddSingleSerial', function() {
                addSingleSerialFromInput();
            });

            // Bulk Serial Add
            $(document).on('click', '#btnApplyBulkSerials', function() {
                const text = $('#inputBulkSerials').val();
                if (!text || !text.trim()) return;

                // Split by newline or comma
                const items = text.split(/[\n,]+/).map(s => s.trim()).filter(s => s.length > 0);

                items.forEach(sNum => {
                    if (!currentPurchaseSerials.includes(sNum)) {
                        currentPurchaseSerials.push(sNum);
                    }
                });

                renderEnteredSerialsList();
                $('#inputBulkSerials').val('');
            });

            // Remove Single Serial Badge
            $(document).on('click', '.remove-serial-item', function() {
                const idx = $(this).data('index');
                if (idx !== undefined) {
                    currentPurchaseSerials.splice(idx, 1);
                    renderEnteredSerialsList();
                }
            });

            // Clear All Serials
            $(document).on('click', '#btnClearAllSerials', function() {
                currentPurchaseSerials = [];
                renderEnteredSerialsList();
            });

            // Apply Selected Serials Button
            $(document).on('click', '#btnApplySelectedSerials', function() {
                const rowIndex = $('#activeSerialRowIndex').val();
                const $row = $('#purchaseTableBody tr').eq(rowIndex);

                if ($row.length) {
                    $row.find('.serials-hidden').val(JSON.stringify(currentPurchaseSerials));

                    // Auto update qty in row based on entered IMEIs count
                    if (currentPurchaseSerials.length > 0) {
                        $row.find('.main-qty-input').val(currentPurchaseSerials.length);
                        recalcRow($row);
                        recalcAll();

                        let badgeHtml = `<span class="badge bg-secondary text-light" style="font-size:0.7rem;" title="${currentPurchaseSerials.join(', ')}"><i class="fas fa-barcode me-1"></i>${currentPurchaseSerials.length} IMEI(s)</span>`;
                        $row.find('.row-tracking-badges').html(badgeHtml);
                    } else {
                        $row.find('.row-tracking-badges').html('');
                    }

                    $('#modalSelectSerial').modal('hide');
                }
            });

            // Re-open Batch or Serial Modal on clicking tracking badge
            $(document).on('click', '.row-tracking-badges', function() {
                const $row = $(this).closest('tr');
                const pid = $row.find('.product-id-hidden').val() || $row.find('.product-select2').val();
                if (pid) {
                    checkAndOpenProductTracking($row, pid);
                }
            });
        });
    </script>
@endsection
