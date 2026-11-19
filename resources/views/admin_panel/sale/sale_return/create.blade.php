@extends('admin_panel.layout.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
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
        --pos-bg: #F1F5F9;
        --pos-card: #FFFFFF;
        --pos-radius: 8px;
        --pos-radius-lg: 12px;
        --pos-input-h: 36px;
    }

    body {
        background-color: var(--pos-bg) !important;
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        color: var(--pos-text) !important;
    }

    .sale-page {
        max-width: 100%;
        margin: 0 auto;
    }

    .sale-card {
        background: var(--pos-card);
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.05);
    }

    .field-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: var(--pos-muted);
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .sale-page .form-control,
    .sale-page .form-select {
        height: var(--pos-input-h);
        border: 1px solid var(--pos-border);
        border-radius: 6px;
        padding: 5px 9px;
        font-size: 13px;
        font-weight: 500;
        color: var(--pos-text);
        background-color: #ffffff;
    }

    .sale-page .form-control:focus,
    .sale-page .form-select:focus {
        border-color: var(--pos-red);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15);
    }

    /* Table Styling matching Sale Table */
    .sales-table {
        width: 100% !important;
        border-collapse: collapse !important;
        background: #ffffff;
        font-size: 12px;
    }

    .sales-table thead th {
        background: #0f172a;
        color: #ffffff;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 10.5px;
        letter-spacing: .3px;
        padding: 8px 5px !important;
        border: 1px solid #1e293b;
        vertical-align: middle;
        white-space: nowrap;
    }

    .sales-table thead th.th-return {
        background: #991b1b !important; /* Distinct Crimson Red for Return Columns */
        border-color: #b91c1c !important;
    }

    .sales-table tbody td {
        padding: 6px 5px !important;
        border: 1px solid #e2e8f0;
        vertical-align: middle;
    }

    .sales-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .sales-table .td-return {
        background-color: #fef2f2 !important; /* Soft light red highlight */
        border-left: 2px solid #ef4444 !important;
        border-right: 2px solid #ef4444 !important;
    }

    .ret-input {
        height: 32px !important;
        text-align: center;
        font-weight: 700 !important;
        font-family: monospace;
        font-size: 13.5px !important;
        border: 1.5px solid #f87171 !important;
        background-color: #ffffff !important;
        color: #b91c1c !important;
    }

    .ret-input:focus {
        border-color: #dc2626 !important;
        box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.2) !important;
    }

    /* Floating / Sticky Footer Summary */
    .return-summary-card {
        background: #1e293b;
        color: #ffffff;
        border-radius: var(--pos-radius-lg);
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
</style>

<div class="container-fluid px-3 px-lg-4 pt-3 pb-5 sale-page">

    <!-- Top Alert / Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="fas fa-check-circle fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="fas fa-exclamation-triangle fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Page Header & Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger text-white px-2 py-1 font-monospace text-uppercase" style="font-size: 11px;">
                    <i class="fas fa-undo-alt me-1"></i> SALE RETURN / CREDIT NOTE
                </span>
                <span class="badge bg-light text-dark border font-monospace" style="font-size: 12px;">
                    Orig Inv: {{ $sale->invoice_no }}
                </span>
            </div>
            <h4 class="fw-bold text-dark mb-0 mt-1">
                Process Sale Return &amp; Customer Credit
            </h4>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-danger fw-bold shadow-sm" id="btnReturnAllItems">
                <i class="fas fa-check-double me-1"></i> Return All Items
            </button>
            <button type="button" class="btn btn-outline-secondary shadow-sm" id="btnResetAllItems">
                <i class="fas fa-redo me-1"></i> Reset
            </button>
            <a href="{{ route('sale.index') }}" class="btn btn-light border shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Sales
            </a>
        </div>
    </div>

    <form id="saleReturnForm" action="{{ route('sale.return.store') }}" method="POST">
        @csrf
        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
        <input type="hidden" name="customer_id" value="{{ $sale->customer_id }}">

        <!-- Top Context Panels: Customer, Original Sale, Return Settings -->
        <div class="row g-3 mb-3">
            
            <!-- 1. Customer Information Card -->
            <div class="col-lg-4 col-md-6">
                <div class="sale-card p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="field-label mb-0 text-primary"><i class="fas fa-store me-1"></i> Customer / Retailer</span>
                        <span class="badge bg-primary-subtle text-primary font-monospace" style="font-size: 10px;">
                            {{ optional($sale->customer_relation)->customer_id ?? ('CUST-' . $sale->customer_id) }}
                        </span>
                    </div>

                    <h5 class="fw-bold text-dark mb-1">
                        {{ optional($sale->customer_relation)->customer_name ?? ($sale->walkin_name ?? 'Walking Customer') }}
                    </h5>
                    <div class="small text-muted font-monospace">
                        <i class="fas fa-phone text-success me-1"></i> {{ optional($sale->customer_relation)->mobile ?: '—' }}
                    </div>
                    <div class="small text-muted mt-1 text-truncate">
                        <i class="fas fa-map-marker-alt text-danger me-1"></i> 
                        {{ optional($sale->customer_relation)->area ? optional($sale->customer_relation)->area . ', ' : '' }}{{ optional($sale->customer_relation)->city ?: 'Karachi' }}
                    </div>

                    <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Customer Balance:</span>
                        @php
                            $custBal = optional($sale->customer_relation)->balance ?? 0;
                        @endphp
                        <span class="fw-bold font-monospace {{ $custBal > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 13px;">
                            Rs {{ number_format(abs($custBal), 2) }} {{ $custBal > 0 ? 'Dr' : 'Cr' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Original Sale Details Card -->
            <div class="col-lg-4 col-md-6">
                <div class="sale-card p-3 h-100">
                    <span class="field-label text-info"><i class="fas fa-file-invoice me-1"></i> Original Invoice Details</span>
                    
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Invoice No:</span>
                        <strong class="font-monospace text-primary">{{ $sale->invoice_no }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Sale Date:</span>
                        <span class="small text-dark fw-semibold">{{ $sale->created_at ? $sale->created_at->format('d/m/Y h:i A') : '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Original Total Net:</span>
                        <strong class="font-monospace text-dark">Rs {{ number_format($sale->total_net, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="small text-muted">Booker / S.R:</span>
                        <span class="badge bg-light text-dark border">
                            {{ optional($sale->orderBooker)->name ?? optional($sale->salesOfficer)->name ?? 'Direct / Admin' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. Return Parameters Card -->
            <div class="col-lg-4 col-md-12">
                <div class="sale-card p-3 h-100">
                    <span class="field-label text-danger"><i class="fas fa-sliders-h me-1"></i> Return Parameters</span>

                    <div class="row g-2">
                        <div class="col-sm-6">
                            <label class="field-label mb-1">Return Date <span class="text-danger">*</span></label>
                            <input type="date" name="return_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="field-label mb-1">Receiving Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ ($sale->warehouse_id == $wh->id) ? 'selected' : '' }}>
                                        {{ $wh->warehouse_name ?? $wh->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <label class="field-label mb-1">Return Reason / Reference</label>
                            <input type="text" name="return_reason" class="form-control" placeholder="e.g. Defective items, Market return, Excess supply...">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Items Table Card (Exact same format as Sales Table with prominent Return Column on the side) -->
        <div class="sale-card mb-4 overflow-hidden">
            <div class="p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-boxes text-danger fs-5"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Original Invoiced Items &amp; Return Quantity</h6>
                        <small class="text-muted">Enter return quantity in the highlighted red columns. System calculates refund value, taxes &amp; discounts automatically.</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-muted border px-2 py-1 font-monospace">
                        {{ $sale->items->count() }} Invoice Items
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table sales-table align-middle text-center mb-0">
                    <thead>
                        <tr>
                            <th style="width: 28px;">#</th>
                            <th class="text-start" style="min-width: 170px;">Item Title / Packaging</th>
                            <th style="width: 65px;">Batch</th>
                            <th style="width: 75px;" class="text-end">Trade Price<br>(TP)</th>
                            
                            {{-- Sold Quantities (Read-only reference) --}}
                            <th style="width: 52px;" title="Original Sold Cartons">Sold<br>CTN</th>
                            <th style="width: 52px;" title="Original Sold Loose Pieces">Sold<br>Loose</th>
                            <th style="width: 65px;" title="Total Sold Pieces">Total<br>Sold Pcs</th>
                            <th style="width: 65px;" title="Already Returned Pieces">Prev.<br>Ret. Pcs</th>
                            
                            {{-- DEDICATED RETURN SECTION (HIGHLIGHTED IN RED/BURGUNDY) --}}
                            <th class="th-return" style="width: 78px;" title="Enter Cartons to Return">
                                Return<br>CTNs
                            </th>
                            <th class="th-return" style="width: 78px;" title="Enter Loose Pieces to Return">
                                Return<br>Loose Pcs
                            </th>
                            <th class="th-return" style="width: 68px;" title="Total Pieces being returned">
                                Tot Ret<br>Pieces
                            </th>
                            <th class="th-return" style="width: 48px;" title="Quick Fill Max">
                                Quick
                            </th>

                            {{-- Financial Return Calculations --}}
                            <th style="width: 80px;" class="text-end" title="Return Gross TP">Return<br>Gross</th>
                            <th style="width: 65px;" class="text-center" title="Discount Reversal">Disc<br>Adj</th>
                            <th style="width: 75px;" class="text-end" title="Sales Tax Refund">Tax<br>Refund</th>
                            <th style="width: 95px;" class="text-end" title="Net Customer Refund">Net<br>Refund</th>
                        </tr>
                    </thead>
                    <tbody id="returnTableBody">
                        @php
                            $allFullyReturned = true;
                        @endphp
                        @forelse($sale->items as $idx => $item)
                            @php
                                if ($item->max_returnable_pieces > 0) {
                                    $allFullyReturned = false;
                                }
                            @endphp
                            <tr class="return-row" 
                                data-row-id="{{ $item->id }}"
                                data-ppb="{{ $item->ppb }}"
                                data-size-mode="{{ $item->size_mode }}"
                                data-trade-rate="{{ $item->trade_rate }}"
                                data-piece-rate="{{ $item->piece_rate }}"
                                data-disc-pct="{{ $item->total_disc_pct }}"
                                data-gst-rate="{{ $item->gst_rate }}"
                                data-is-3rd="{{ $item->is_3rd ? 1 : 0 }}"
                                data-mrp="{{ $item->mrp_rate }}"
                                data-max-pieces="{{ $item->max_returnable_pieces }}"
                                data-max-cartons="{{ $item->max_returnable_cartons }}"
                                data-max-loose="{{ $item->max_returnable_loose }}">

                                <input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
                                <input type="hidden" name="color[]" value="{{ $item->color ?? '' }}">
                                <input type="hidden" name="qty[]" class="input-total-return-pieces" value="0">
                                <input type="hidden" name="price[]" class="input-row-price" value="{{ $item->trade_rate }}">
                                <input type="hidden" name="item_disc[]" class="input-row-disc" value="0">
                                <input type="hidden" name="tax_amount[]" class="input-row-tax" value="0">
                                <input type="hidden" name="line_gross[]" class="input-row-gross" value="0">
                                <input type="hidden" name="line_total[]" class="input-row-net" value="0">

                                <!-- Row # -->
                                <td class="text-center text-muted font-monospace">{{ $idx + 1 }}</td>

                                <!-- Product Title & Pack Info -->
                                <td class="text-start">
                                    <div class="fw-bold text-dark" style="font-size: 13px;">{{ $item->item_title }}</div>
                                    <div class="text-muted small d-flex gap-2 font-monospace" style="font-size: 11px;">
                                        @if($item->brand_title)
                                            <span><i class="fas fa-tag text-secondary me-1"></i>{{ $item->brand_title }}</span>
                                        @endif
                                        <span class="badge bg-light text-dark border">
                                            1 CTN = {{ $item->ppb }} {{ $item->unit_title }}
                                        </span>
                                        @if($item->is_3rd)
                                            <span class="badge bg-warning-subtle text-dark border border-warning" style="font-size: 9px;">3rd Sched MRP</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Batch No -->
                                <td class="text-center font-monospace small">
                                    <span class="badge bg-light text-muted border">{{ $item->batch_title }}</span>
                                </td>

                                <!-- Trade Price -->
                                <td class="text-end font-monospace fw-bold text-dark">
                                    {{ number_format($item->trade_rate, 2) }}
                                    <div class="text-muted small" style="font-size: 10px;">/ ctn</div>
                                </td>

                                <!-- Sold CTNs -->
                                <td class="text-center font-monospace">
                                    <span class="badge bg-light text-dark border">{{ $item->sold_cartons }}</span>
                                </td>

                                <!-- Sold Loose Pcs -->
                                <td class="text-center font-monospace">
                                    <span class="badge bg-light text-dark border">{{ $item->sold_loose }}</span>
                                </td>

                                <!-- Total Sold Pieces -->
                                <td class="text-center font-monospace fw-bold text-dark">
                                    {{ (int)$item->sold_total_pieces }}
                                </td>

                                <!-- Already Returned Pieces -->
                                <td class="text-center font-monospace">
                                    @if($item->already_returned_pieces > 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger">
                                            {{ (int)$item->already_returned_pieces }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- DEDICATED RETURN SECTION (HIGHLIGHTED IN SOFT RED) --}}
                                <!-- Return Cartons Input -->
                                <td class="td-return text-center">
                                    @if($item->max_returnable_pieces > 0)
                                        <input type="number" 
                                               name="return_carton[]" 
                                               class="form-control ret-input input-return-carton" 
                                               min="0" 
                                               max="{{ $item->max_returnable_cartons }}" 
                                               step="1" 
                                               placeholder="0" 
                                               value="0"
                                               title="Max Cartons Returnable: {{ $item->max_returnable_cartons }}">
                                    @else
                                        <span class="badge bg-secondary text-white small">Fully Ret.</span>
                                    @endif
                                </td>

                                <!-- Return Loose Input -->
                                <td class="td-return text-center">
                                    @if($item->max_returnable_pieces > 0)
                                        <input type="number" 
                                               name="return_loose[]" 
                                               class="form-control ret-input input-return-loose" 
                                               min="0" 
                                               max="{{ $item->ppb > 1 ? ($item->ppb - 1) : $item->max_returnable_pieces }}" 
                                               step="1" 
                                               placeholder="0" 
                                               value="0"
                                               title="Max Loose Returnable: {{ $item->ppb > 1 ? ($item->ppb - 1) : $item->max_returnable_pieces }}">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <!-- Total Return Pieces (Auto-computed) -->
                                <td class="td-return text-center font-monospace">
                                    <span class="badge bg-white text-danger border border-danger fw-bold display-ret-pieces" style="font-size: 11px;">
                                        0
                                    </span>
                                </td>

                                <!-- Quick Max Button -->
                                <td class="td-return text-center">
                                    @if($item->max_returnable_pieces > 0)
                                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 btn-quick-max" title="Return Max remaining">
                                            All
                                        </button>
                                    @endif
                                </td>

                                <!-- Return Gross -->
                                <td class="text-end font-monospace text-muted display-ret-gross">
                                    0.00
                                </td>

                                <!-- Discount Adjust -->
                                <td class="text-center font-monospace text-danger small display-ret-disc">
                                    -0.00
                                </td>

                                <!-- Tax Refund -->
                                <td class="text-end font-monospace text-success small display-ret-tax">
                                    +0.00
                                </td>

                                <!-- Net Refund Amount -->
                                <td class="text-end font-monospace fw-bold text-danger fs-6 display-ret-net">
                                    Rs 0.00
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="text-center py-5 text-muted">
                                    No items found for this sale invoice.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($allFullyReturned)
                <div class="alert alert-warning m-3 d-flex align-items-center gap-2">
                    <i class="fas fa-exclamation-circle fs-4"></i>
                    <div>
                        <strong>Notice:</strong> All items in this invoice have already been 100% returned. No further returns can be accepted for this document.
                    </div>
                </div>
            @endif
        </div>

        <!-- Return Summary & Settlement Card -->
        <div class="return-summary-card p-4">
            <div class="row g-4 align-items-center">
                
                <!-- Left: Quantities Breakdown & Notes -->
                <div class="col-lg-6 border-end border-secondary">
                    <h6 class="fw-bold text-white-50 text-uppercase mb-3" style="font-size: 11.5px; letter-spacing: 0.5px;">
                        <i class="fas fa-clipboard-list text-warning me-1"></i> Return Summary Breakdown
                    </h6>

                    <div class="row g-2 mb-3">
                        <div class="col-3">
                            <div class="bg-dark rounded p-2 text-center">
                                <span class="text-white-50 small d-block" style="font-size: 10px;">Items Ret.</span>
                                <strong class="fs-6 font-monospace text-white" id="statReturnItems">0</strong>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-dark rounded p-2 text-center">
                                <span class="text-white-50 small d-block" style="font-size: 10px;">Cartons Ret.</span>
                                <strong class="fs-6 font-monospace text-warning" id="statReturnCartons">0</strong>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-dark rounded p-2 text-center">
                                <span class="text-white-50 small d-block" style="font-size: 10px;">Loose Pcs</span>
                                <strong class="fs-6 font-monospace text-info" id="statReturnLoose">0</strong>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-dark rounded p-2 text-center">
                                <span class="text-white-50 small d-block" style="font-size: 10px;">Tot Pieces</span>
                                <strong class="fs-6 font-monospace text-danger" id="statReturnTotalPieces">0</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Settlement Method -->
                    <div>
                        <label class="text-white-50 small fw-bold text-uppercase mb-2" style="font-size: 10.5px;">
                            Refund Settlement Method:
                        </label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="settlement_mode" id="mode_credit" value="credit" checked>
                                <label class="form-check-label text-white fw-semibold" for="mode_credit">
                                    <i class="fas fa-credit-card text-success me-1"></i> Credit Note (Customer Ledger)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="settlement_mode" id="mode_cash" value="cash">
                                <label class="form-check-label text-white fw-semibold" for="mode_cash">
                                    <i class="fas fa-money-bill-wave text-warning me-1"></i> Cash / Bank Refund
                                </label>
                            </div>
                        </div>

                        <!-- Cash/Bank Account Selector (Hidden unless Cash selected) -->
                        <div id="cashRefundBox" class="mt-2 row g-2" style="display: none;">
                            <div class="col-sm-6">
                                <select name="payment_account_id[]" class="form-select form-select-sm bg-dark text-white border-secondary">
                                    <option value="">-- Select Cash/Bank Account --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <input type="number" step="0.01" min="0" name="payment_amount[]" id="inputCashRefundAmount" class="form-control form-control-sm font-monospace fw-bold" placeholder="Refund Amount">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Financial Refund Calculations & Submit -->
                <div class="col-lg-6 ps-lg-4">
                    <div class="d-flex justify-content-between align-items-center mb-1 small">
                        <span class="text-white-50">Return Gross TP:</span>
                        <span class="font-monospace text-white" id="summaryReturnGross">Rs 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1 small">
                        <span class="text-white-50">Discount Reversal:</span>
                        <span class="font-monospace text-danger" id="summaryReturnDisc">-Rs 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                        <span class="text-white-50">Sales Tax Refund:</span>
                        <span class="font-monospace text-success" id="summaryReturnTax">+Rs 0.00</span>
                    </div>

                    <div class="border-top border-secondary pt-2 mb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-white-50 small text-uppercase fw-bold d-block">Total Refund / Credit Note</span>
                            <small class="text-muted" style="font-size: 10px;">Amount credited to customer's account</small>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold font-monospace fs-4 text-warning" id="summaryTotalRefund">Rs 0.00</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('sale.index') }}" class="btn btn-outline-light px-3">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-danger btn-lg px-4 fw-bold shadow" id="btnSubmitReturn" {{ $allFullyReturned ? 'disabled' : '' }}>
                            <i class="fas fa-undo-alt me-2"></i> Confirm &amp; Post Sale Return
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </form>

</div>
@endsection

@section('js')
<script>
$(document).ready(function() {

    // Helper: calculate a single row
    function calculateRow($row) {
        let ppb = parseInt($row.data('ppb')) || 1;
        let sizeMode = $row.data('size-mode') || 'by_pieces';
        let tradeRate = parseFloat($row.data('trade-rate')) || 0;
        let pieceRate = parseFloat($row.data('piece-rate')) || 0;
        let discPct = parseFloat($row.data('disc-pct')) || 0;
        let gstRate = parseFloat($row.data('gst-rate')) || 0;
        let is3rd = parseInt($row.data('is-3rd')) || 0;
        let mrp = parseFloat($row.data('mrp')) || 0;

        let maxPieces = parseFloat($row.data('max-pieces')) || 0;
        let maxCartons = parseFloat($row.data('max-cartons')) || 0;

        let retCtn = parseInt($row.find('.input-return-carton').val()) || 0;
        let retLoose = parseInt($row.find('.input-return-loose').val()) || 0;

        if (retCtn < 0) retCtn = 0;
        if (retLoose < 0) retLoose = 0;

        // Total return pieces
        let totPieces = (retCtn * ppb) + retLoose;

        // Validation against remaining max pieces
        if (totPieces > maxPieces) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'Cannot exceed max remaining returnable pieces (' + maxPieces + ' pcs)',
                showConfirmButton: false,
                timer: 2500
            });

            // Auto-cap to max pieces
            totPieces = maxPieces;
            retCtn = ppb > 1 ? Math.floor(totPieces / ppb) : 0;
            retLoose = ppb > 1 ? (totPieces % ppb) : totPieces;

            $row.find('.input-return-carton').val(retCtn);
            $row.find('.input-return-loose').val(retLoose);
        }

        // Calculate Gross
        let lineGross = 0;
        if (sizeMode === 'by_cartons' || sizeMode === 'by_bandal') {
            lineGross = (retCtn * tradeRate) + (retLoose * pieceRate);
        } else {
            lineGross = totPieces * (ppb > 1 ? pieceRate : tradeRate);
        }

        // Calculate Discount
        let lineDisc = (lineGross * discPct) / 100;
        let exclGst = Math.max(0, lineGross - lineDisc);

        // Calculate Tax Refund
        let lineTax = 0;
        if (gstRate > 0) {
            if (is3rd && mrp > 0) {
                lineTax = (mrp * totPieces) * (gstRate / 100);
            } else {
                lineTax = exclGst * (gstRate / 100);
            }
        }

        // Net Refund Total
        let lineNet = exclGst + lineTax;

        // Update Row Displays & Hidden Form Inputs
        $row.find('.display-ret-pieces').text(totPieces);
        $row.find('.display-ret-gross').text(lineGross.toFixed(2));
        $row.find('.display-ret-disc').text('-' + lineDisc.toFixed(2));
        $row.find('.display-ret-tax').text('+' + lineTax.toFixed(2));
        $row.find('.display-ret-net').text('Rs ' + lineNet.toFixed(2));

        $row.find('.input-total-return-pieces').val(totPieces);
        $row.find('.input-row-disc').val(lineDisc.toFixed(2));
        $row.find('.input-row-tax').val(lineTax.toFixed(2));
        $row.find('.input-row-gross').val(lineGross.toFixed(2));
        $row.find('.input-row-net').val(lineNet.toFixed(2));

        $row.data('calc-pieces', totPieces);
        $row.data('calc-cartons', retCtn);
        $row.data('calc-loose', retLoose);
        $row.data('calc-gross', lineGross);
        $row.data('calc-disc', lineDisc);
        $row.data('calc-tax', lineTax);
        $row.data('calc-net', lineNet);

        calculateOverallSummary();
    }

    // Overall Summary Calculation
    function calculateOverallSummary() {
        let totalItems = 0;
        let totalCartons = 0;
        let totalLoose = 0;
        let totalPieces = 0;
        let totalGross = 0;
        let totalDisc = 0;
        let totalTax = 0;
        let totalNet = 0;

        $('.return-row').each(function() {
            let pcs = $(this).data('calc-pieces') || 0;
            if (pcs > 0) {
                totalItems++;
                totalCartons += ($(this).data('calc-cartons') || 0);
                totalLoose += ($(this).data('calc-loose') || 0);
                totalPieces += pcs;
                totalGross += ($(this).data('calc-gross') || 0);
                totalDisc += ($(this).data('calc-disc') || 0);
                totalTax += ($(this).data('calc-tax') || 0);
                totalNet += ($(this).data('calc-net') || 0);
            }
        });

        $('#statReturnItems').text(totalItems);
        $('#statReturnCartons').text(totalCartons);
        $('#statReturnLoose').text(totalLoose);
        $('#statReturnTotalPieces').text(totalPieces);

        $('#summaryReturnGross').text('Rs ' + totalGross.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#summaryReturnDisc').text('-Rs ' + totalDisc.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#summaryReturnTax').text('+Rs ' + totalTax.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#summaryTotalRefund').text('Rs ' + totalNet.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        // If Cash refund is selected, prefill refund amount
        if ($('#mode_cash').is(':checked')) {
            $('#inputCashRefundAmount').val(totalNet.toFixed(2));
        }
    }

    // Listen on return inputs change
    $(document).on('input change', '.input-return-carton, .input-return-loose', function() {
        let $row = $(this).closest('.return-row');
        calculateRow($row);
    });

    // Quick Max Button on row
    $(document).on('click', '.btn-quick-max', function(e) {
        e.preventDefault();
        let $row = $(this).closest('.return-row');
        let maxPieces = parseFloat($row.data('max-pieces')) || 0;
        let ppb = parseInt($row.data('ppb')) || 1;

        let maxCtn = ppb > 1 ? Math.floor(maxPieces / ppb) : 0;
        let maxLse = ppb > 1 ? (maxPieces % ppb) : maxPieces;

        $row.find('.input-return-carton').val(maxCtn);
        $row.find('.input-return-loose').val(maxLse);
        calculateRow($row);
    });

    // Top Button: Return All Items
    $('#btnReturnAllItems').on('click', function(e) {
        e.preventDefault();
        $('.return-row').each(function() {
            let maxPieces = parseFloat($(this).data('max-pieces')) || 0;
            let ppb = parseInt($(this).data('ppb')) || 1;

            if (maxPieces > 0) {
                let maxCtn = ppb > 1 ? Math.floor(maxPieces / ppb) : 0;
                let maxLse = ppb > 1 ? (maxPieces % ppb) : maxPieces;

                $(this).find('.input-return-carton').val(maxCtn);
                $(this).find('.input-return-loose').val(maxLse);
            } else {
                $(this).find('.input-return-carton').val(0);
                $(this).find('.input-return-loose').val(0);
            }
            calculateRow($(this));
        });

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'All available items set to maximum returnable quantity.',
            showConfirmButton: false,
            timer: 2000
        });
    });

    // Top Button: Reset All Items
    $('#btnResetAllItems').on('click', function(e) {
        e.preventDefault();
        $('.return-row').each(function() {
            $(this).find('.input-return-carton').val(0);
            $(this).find('.input-return-loose').val(0);
            calculateRow($(this));
        });
    });

    // Settlement mode toggle
    $('input[name="settlement_mode"]').on('change', function() {
        if ($(this).val() === 'cash') {
            $('#cashRefundBox').slideDown();
            let net = parseFloat($('#summaryTotalRefund').text().replace(/[^0-9.]/g, '')) || 0;
            $('#inputCashRefundAmount').val(net.toFixed(2));
        } else {
            $('#cashRefundBox').slideUp();
            $('#inputCashRefundAmount').val('');
        }
    });

    // Form Submission with SweetAlert confirmation
    $('#saleReturnForm').on('submit', function(e) {
        e.preventDefault();
        let $form = $(this);
        let $btn = $('#btnSubmitReturn');

        let validCount = 0;
        $('.return-row').each(function() {
            let pcs = $(this).data('calc-pieces') || 0;
            if (pcs > 0) validCount++;
        });

        if (validCount === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Items to Return',
                text: 'Please enter a return quantity in the red column for at least one item.'
            });
            return;
        }

        let totalRefund = $('#summaryTotalRefund').text();

        Swal.fire({
            title: 'Confirm Sale Return?',
            html: 'Total Refund Value: <strong class="text-danger">' + totalRefund + '</strong><br>Stock will be restored and customer ledger will be adjusted.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Post Return'
        }).then((result) => {
            if (result.isConfirmed) {
                let origText = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Processing Return...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message || 'Sale return processed successfully.',
                            confirmButtonText: 'View Return Details'
                        }).then(() => {
                            if (res.redirect_url) {
                                window.location.href = res.redirect_url;
                            } else {
                                window.location.href = "{{ route('sale.return.index') }}";
                            }
                        });
                    },
                    error: function(xhr) {
                        let msg = 'Failed to process return.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                        $btn.prop('disabled', false).html(origText);
                    }
                });
            }
        });
    });

});
</script>
@endsection
