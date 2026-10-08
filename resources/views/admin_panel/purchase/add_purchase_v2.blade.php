@extends('admin_panel.layout.app')

@section('content')
   <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}" rel="stylesheet">
    
    <style>
        /* ==================== NEW PURCHASE — PRO ERP & EXCEL GRID UI ==================== */
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

        /* ---------- VENDOR CARD PREVIEW ---------- */
        .vendor-bal-card {
            background: #FFFFFF;
            border: 1px solid var(--pos-border);
            border-radius: 8px;
            padding: 8px 12px;
            margin-top: 12px;
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

    <div class="container-fluid py-3 px-3 purchase-page">
        <div id="alertBox" class="alert d-none mb-3" role="alert"></div>

        <form id="purchaseForm" action="{{ route('store.Purchase') }}" method="POST" autocomplete="off">
            @csrf
            <input type="hidden" id="action" name="action" value="purchase">

            <!-- PAGE HEADER -->
            <div class="purchase-header">
                <div class="purchase-header-left">
                    <a href="{{ route('Purchase.home') }}" class="btn btn-outline-secondary btn-sm fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                    <div class="purchase-title-ic">
                        <i class="bi bi-bag-plus-fill"></i>
                    </div>
                    <div class="purchase-title-main">
                        <h5 class="mb-0">CREATE PURCHASE INVOICE</h5>
                        <div class="purchase-subtitle">Manage vendor purchases, stock entry & batch/serial tracking</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-white text-secondary border px-3 py-2 fs-6 fw-bold shadow-sm">
                        <i class="bi bi-calendar3 me-1 text-primary"></i> {{ date('d/m/Y') }}
                    </span>
                    <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" onclick="window.location.reload()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                    <button type="button" class="btn btn-info text-white btn-sm fw-bold shadow-sm px-3" id="btnSaveOnly">
                        <i class="bi bi-save me-1"></i> Save Draft
                    </button>
                    <button type="button" class="btn btn-success btn-sm fw-bold shadow-sm px-3" id="btnConfirm">
                        <i class="bi bi-check-circle me-1"></i> Confirm Purchase
                    </button>
                </div>
            </div>

            <!-- VENDOR & INVOICE DETAILS CARD -->
            <div class="purchase-card">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="field-label">System Inv #</label>
                        <input type="text" class="form-control input-readonly fw-bold" name="invoice_no" value="{{ $nextInvoice ?? 'NEW' }}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="field-label">Vendor Bill #</label>
                        <input type="text" class="form-control" name="purchase_order_no" placeholder="Manual Inv / Ref #">
                    </div>
                    <div class="col-md-3" id="vendorSelectWrapper">
                        <label class="field-label">Select Vendor <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-1">
                            <div class="flex-grow-1">
                                <select class="form-select select2" id="vendorSelect" name="vendor_id">
                                    <option value="" selected disabled>Select Vendor</option>
                                    @foreach ($Vendor as $v)
                                        <option value="{{ $v->id }}" data-phone="{{ $v->phone }}" data-address="{{ $v->address }}">{{ $v->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm px-2 py-1 shadow-sm" data-toggle="modal" data-target="#addVendorModal" title="Add New Vendor">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="field-label">Purchase Date</label>
                        <input type="text" name="purchase_date" class="form-control datepicker-custom fw-semibold" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="field-label">M.Bill / Remarks</label>
                        <input type="text" class="form-control" name="note" id="remarks" placeholder="Optional purchase notes...">
                    </div>
                </div>

                <!-- VENDOR DETAILS STRIP -->
                <div id="vendorInfoCard" class="vendor-bal-card d-none">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-telephone-fill text-primary"></i>
                            <span class="field-label mb-0">Mobile:</span>
                            <span class="fw-bold text-dark fs-6" id="vi_mobile">—</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-geo-alt-fill text-primary"></i>
                            <span class="field-label mb-0">Address:</span>
                            <span class="fw-bold text-dark fs-6" id="vi_address">—</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger-subtle text-danger border border-danger fs-6 px-3 py-1 fw-bold">
                                Previous Balance: Rs. <span id="vi_prev_bal">0.00</span>
                            </span>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="warehouse_id" value="{{ $Warehouse->first()->id ?? 1 }}">
            </div>

            <!-- PURCHASE ITEMS TABLE PANEL -->
            <div class="purchase-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-grid-3x3-gap-fill text-primary fs-5"></i>
                        <span>PURCHASE ITEMS (EXCEL GRID)</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success fw-bold px-3" data-toggle="modal" data-target="#quickAddProductModal">
                            <i class="bi bi-plus-circle me-1"></i> Quick Add Product
                        </button>
                        <button type="button" class="btn btn-sm btn-primary fw-bold px-3" id="btnAdd">
                            <i class="bi bi-plus-lg me-1"></i> Add Blank Row
                        </button>
                    </div>
                </div>

                <div class="pos-table-wrap">
                    <table class="sales-table" id="purchaseTable">
                        <thead>
                            <tr>
                                <th style="width: 38px;" class="text-center">#</th>
                                <th class="col-product">Product Description</th>
                                <th style="width: 90px;">Unit</th>
                                <th style="width: 100px;">Qty</th>
                                <th style="width: 130px;">Purchase Price</th>
                                <th style="width: 100px;">Disc %</th>
                                <th style="width: 110px;">Disc Amt</th>
                                <th style="width: 140px;">Subtotal</th>
                                <th style="width: 44px;" class="text-center"><i class="bi bi-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody id="purchaseTableBody">
                            <!-- Rows appended dynamically via JS -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7" class="text-end fw-bold text-muted py-2">Total Item Amount:</td>
                                <td class="text-end fw-bold fs-6 text-dark py-2"><span id="totalAmount">0.00</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- PAYMENT & SUMMARY ROW -->
            <div class="row g-3">
                <!-- PAYMENT VOUCHERS -->
                <div class="col-lg-7">
                    <div class="purchase-card h-100 mb-0">
                        <div class="card-title mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-wallet2 text-success fs-5"></i>
                            <span>PAYMENT / RECEIPT VOUCHER</span>
                        </div>
                        <div id="paymentWrapper" class="border rounded p-3 bg-light mb-3">
                            <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                                <select class="form-select rv-account" name="payment_account_id[]" style="max-width: 300px; flex-grow: 1;">
                                    @foreach ($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ (str_contains(strtolower($acc->title), 'cash') || $loop->first) ? 'selected' : '' }}>{{ $acc->title }}</option>
                                    @endforeach
                                </select>
                                <input type="number" class="form-control text-end payment-amount" name="payment_amount[]" placeholder="Amount" style="width:150px">
                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="btnAddPayment">
                                    <i class="bi bi-plus me-1"></i> Add Account
                                </button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 bg-white border rounded">
                            <span class="field-label mb-0 fs-6 text-muted">Total Paid Amount:</span>
                            <span class="fw-bold fs-5 text-success">Rs. <span id="totalPaid">0.00</span></span>
                        </div>
                    </div>
                </div>

                <!-- BILL SUMMARY -->
                <div class="col-lg-5">
                    <div class="purchase-card h-100 mb-0">
                        <div class="card-title mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-calculator text-primary fs-5"></i>
                            <span>INVOICE SUMMARY</span>
                        </div>
                        <div class="p-3 bg-light rounded border">
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-bold">Total Items Qty</div>
                                <div class="col-5 text-end"><span id="tQty" class="fw-bold fs-6">0</span></div>
                            </div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-bold">Sub-Total</div>
                                <div class="col-5 text-end fw-bold fs-6"><span id="tSub">0.00</span></div>
                            </div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-bold">Bill Discount</div>
                                <div class="col-5 text-end d-flex gap-1">
                                    <input type="number" class="form-control text-end form-control-sm" id="billDiscountPct" placeholder="%" style="width: 65px;" step="0.01">
                                    <input type="number" class="form-control text-end form-control-sm" id="billDiscount" value="0" step="0.01">
                                    <input type="hidden" name="discount" id="discountInput" value="0">
                                </div>
                            </div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-muted fw-bold">Freight / Extra Cost</div>
                                <div class="col-5 text-end">
                                    <input type="number" class="form-control text-end form-control-sm" name="extra_cost" id="extraCost" value="0">
                                </div>
                            </div>
                            <div class="row py-1 align-items-center">
                                <div class="col-7 text-danger fw-bold">Previous Balance</div>
                                <div class="col-5 text-end text-danger fw-bold fs-6"><span id="tPrev">0.00</span></div>
                            </div>
                            <hr class="my-2 border-secondary">
                            <div class="row py-2 align-items-center">
                                <div class="col-6 fw-bold fs-5 text-primary">Current Bill</div>
                                <div class="col-6 text-end fw-bold fs-5 text-primary">Rs. <span id="tPayable">0.00</span></div>
                            </div>
                            <div class="row py-2 bg-warning-subtle rounded border border-warning align-items-center">
                                <div class="col-6 fw-bold fs-5 text-dark">Total Payable</div>
                                <div class="col-6 text-end fw-bold fs-4 text-dark">Rs. <span id="tTotalPayable">0.00</span></div>
                            </div>
                            <input type="hidden" name="net_amount" id="netAmountInput" value="0">
                            <input type="hidden" name="subtotal" id="subtotalInput" value="0">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Add Vendor Modal -->
    <div class="modal fade" id="addVendorModal" tabindex="-1" aria-labelledby="addVendorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom-0 pb-2">
                    <h5 class="modal-title fw-bold" id="addVendorModalLabel">Add New Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="quickAddVendorForm">
                    @csrf
                    <div class="modal-body pt-2">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Vendor Name</label>
                            <input type="text" class="form-control" name="name" required placeholder="Enter vendor name">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Phone Number</label>
                                <input type="text" class="form-control" name="phone" placeholder="Optional">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-muted">Opening Balance</label>
                                <input type="number" step="0.01" class="form-control" name="opening_balance" value="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold small text-muted">Address</label>
                            <textarea class="form-control" name="address" rows="2" placeholder="Optional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnQuickSaveVendor">Save Vendor</button>
                    </div>
                </form>
            </div>
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

  <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/js/select2.min.js') }}"></script>

    {{-- Quick Add Product Modal --}}
    @include('admin_panel.partials.quick_add_product_modal')

    <script>
        $(document).ready(function() {
            // Init Select2
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

            // Vendor Select Logic
            $('#vendorSelect').on('change', function() {
                const vendorId = $(this).val();
                if (!vendorId) {
                    $('#vendorInfoCard').addClass('d-none');
                    return;
                }

                // Fetch Vendor Info & Ledger
                $.get(`/vendor/${vendorId}/ledger-json`, function(data) {
                    // Update Info Card
                    $('#vi_mobile').text(data.vendor.phone || '—');
                    $('#vi_address').text(data.vendor.address || '—');
                    $('#vi_prev_bal').text(parseFloat(data.current_balance).toFixed(2));
                    $('#vendorInfoCard').removeClass('d-none');

                    // Update Summary
                    $('#tPrev').text(parseFloat(data.current_balance).toFixed(2));
                    recalcAll();
                });
            });

            function updateRowIndexes() {
                $('#purchaseTableBody tr').each(function(index) {
                    $(this).find('.row-index-cell').text(index + 1);
                });
            }

            // Add First Row
            addBlankRow();

            // Add Row Button
            $('#btnAdd').click(function() {
                addBlankRow();
            });

            // Remove Row
            $(document).on('click', '.remove-row', function() {
                if ($('#purchaseTableBody tr').length > 1) {
                    $(this).closest('tr').remove();
                    recalcAll();
                    updateRowIndexes();
                }
            });

            // Inputs -> Calc
            $('#purchaseTableBody').on('input', '.main-qty-input, .price, .item-disc-percent', function() {
                recalcRow($(this).closest('tr'));
                recalcAll();
            });

            // Summary Inputs
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

            // Payment Row Add
            $('#btnAddPayment').click(function() {
                const html = `
                    <div class="d-flex gap-2 align-items-center mb-2 payment-row flex-wrap">
                        <select class="form-select rv-account" name="payment_account_id[]" style="max-width: 300px; flex-grow: 1;">
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ (str_contains(strtolower($acc->title), 'cash') || $loop->first) ? 'selected' : '' }}>{{ $acc->title }}</option>
                            @endforeach
                        </select>
                        <input type="number" class="form-control text-end payment-amount" name="payment_amount[]" placeholder="Amount" style="width:140px">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-payment">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>`;
                $('#paymentWrapper').append(html);
            });

            $(document).on('click', '.remove-payment', function() {
                $(this).closest('.payment-row').remove();
                calcTotalPaid();
            });

            $(document).on('input', '.payment-amount', function() {
                calcTotalPaid();
            });

            function calcTotalPaid() {
                let total = 0;
                $('.payment-amount').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#totalPaid').text(total.toFixed(2));
                recalcAll(); // Trigger summary update
            }


            // --- SAVE ONLY AJAX ---
            // --- Submit Logic (AJAX for both Save & Confirm) ---

            // 1. Save (Draft)
            $('#btnSaveOnly').click(function(e) {
                e.preventDefault();
                normalizeDiscountInput();
                let $btn = $(this);
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

                $('#action').val('save_only'); // Set action

                $.ajax({
                    url: "{{ route('store.Purchase') }}",
                    method: "POST",
                    data: $('#purchaseForm').serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Purchase saved as draft successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('Purchase.home') }}";
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(
                            '<i class="bi bi-save"></i> Save Purchase');
                        let msg = 'Something went wrong.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON
                            .message;
                        // Validation errors
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = Object.values(xhr.responseJSON.errors).flat().join(
                                '\n');
                            msg += '\n' + errors;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            // 2. Confirm (Approved)
            $('#btnConfirm').click(function(e) {
                e.preventDefault();
                normalizeDiscountInput();

                Swal.fire({
                    title: 'Confirm Purchase?',
                    text: "This will update stock and accounts. You cannot revert this directly.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Confirm it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let $btn = $('#btnConfirm');
                        $btn.prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2"></span>Processing...'
                        );

                        $('#action').val('approved'); // Set action

                        $.ajax({
                            url: "{{ route('store.Purchase') }}",
                            method: "POST",
                            data: $('#purchaseForm').serialize(),
                            success: function(response) {
                                // Open Invoice in New Tab
                                if (response.invoice_url) {
                                    window.open(response.invoice_url, '_blank');
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Confirmed!',
                                    text: 'Purchase confirmed and processed successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = response
                                        .redirect_url ||
                                        "{{ route('Purchase.home') }}";
                                });
                            },
                            error: function(xhr) {
                                $btn.prop('disabled', false).html(
                                    '<i class="bi bi-check-circle"></i> Confirm Purchase'
                                );
                                let msg = 'Something went wrong.';
                                if (xhr.responseJSON && xhr.responseJSON.message) msg =
                                    xhr.responseJSON.message;
                                if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    let errors = Object.values(xhr.responseJSON.errors)
                                        .flat().join('\n');
                                    msg += '\n' + errors;
                                }
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });

            // --- QUICK ADD VENDOR AJAX ---
            $('#quickAddVendorForm').on('submit', function(e) {
                e.preventDefault();
                let $btn = $('#btnQuickSaveVendor');
                let originalText = $btn.text();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

                $.ajax({
                    url: "{{ route('vendors.store.ajax') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        $btn.prop('disabled', false).text(originalText);
                        
                        let vendorId = null;
                        let vendorName = $('#quickAddVendorForm input[name="name"]').val();
                        
                        if (response.vendor && response.vendor.id) {
                            vendorId = response.vendor.id;
                            vendorName = response.vendor.name;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Vendor Added',
                            text: 'The vendor has been created successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#addVendorModal').modal('hide');
                            $('#quickAddVendorForm')[0].reset();
                            
                            if (vendorId) {
                                let newOption = new Option(vendorName, vendorId, false, true);
                                $('#vendorSelect').append(newOption).trigger('change');
                            } else {
                                window.location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).text(originalText);
                        let msg = 'Error adding vendor.';
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            function addBlankRow() {
                const html = `
                <tr>
                    <td class="row-index-cell text-center">1</td>
                    <td>
                        <select class="form-select product-select2" name="product_id[]"></select>
                        <!-- Hidden fields for product data snapshot -->
                        <input type="hidden" name="size_mode[]" class="hidden-size-mode" value="">
                        <input type="hidden" name="pieces_per_box[]" class="hidden-pieces-per-box" value="1">
                        <input type="hidden" name="pieces_per_m2[]" class="hidden-pieces-per-m2" value="0">
                        <input type="hidden" name="price_per_carton[]" class="hidden-price-per-carton" value="0">
                        <input type="hidden" name="boxes_qty[]" class="hidden-boxes-qty" value="0">
                        <input type="hidden" name="loose_qty[]" class="hidden-loose-qty" value="0">
                        <input type="hidden" name="length[]" class="hidden-length" value="">
                        <input type="hidden" name="width[]" class="hidden-width" value="">
                        <input type="hidden" name="color[]" class="hidden-variant-data" value="">
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
                        <input type="number" step="0.01" name="price[]" class="form-control text-end price" value="0">
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
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-x-lg"></i></button>
                    </td>
                </tr>
                `;
                const $row = $(html);
                $('#purchaseTableBody').append($row);
                initProductSelect2($row.find('.product-select2'));
                updateRowIndexes();
            }

            function initProductSelect2($el) {
                if (!$el || !$el.length) return;
                if ($el.hasClass('select2-hidden-accessible')) {
                    try { $el.select2('destroy'); } catch(e) {}
                }
                $el.select2({
                    placeholder: 'Search Product (Name / SKU / Barcode)',
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
                    const ppb = parseFloat(data.pieces_per_box) || 1;
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

                    // Prices
                    const pPiece = parseFloat(data.purchase_price_per_piece) || parseFloat(data.trade_price) || 0;
                    const pBox = parseFloat(data.purchase_price_per_box) || (pPiece * ppb);
                    const pM2 = parseFloat(data.purchase_price_per_m2) || 0;
                    const sizeMode = data.size_mode || 'std';

                    // Snapshot Data Population
                    $row.find('.hidden-size-mode').val(data.size_mode || '');
                    $row.find('.hidden-pieces-per-box').val(ppb);
                    $row.find('.hidden-pieces-per-m2').val(data.pieces_per_m2 || 0);
                    $row.find('.hidden-price-per-carton').val(pBox);
                    $row.find('.hidden-length').val(data.length || '');
                    $row.find('.hidden-width').val(data.width || '');
                    $row.find('.hidden-variant-data').val(data.variant_data || '');

                    $row.data('sizemode', data.size_mode);
                    $row.data('pieces_per_m2', Number(data.pieces_per_m2) || 0);
                    $row.data('p_price_piece', pPiece);
                    $row.data('p_price_box', pBox);
                    $row.data('pieces_per_box', ppb);

                    // Discount
                    $row.find('.item-disc-percent').val(data.purchase_discount_percent || 0);

                    // Determine initial price based on selected unit mode
                    let finalPrice = pPiece;
                    if (sizeMode === 'by_size') {
                        finalPrice = pM2;
                    } else if (isCartonMode) {
                        finalPrice = pBox > 0 ? pBox : (pPiece * ppb);
                    } else {
                        finalPrice = pPiece;
                    }

                    $row.find('.price').val(finalPrice % 1 === 0 ? finalPrice : finalPrice.toFixed(2));
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

                if (repo.element) {
                    const $el = $(repo.element);
                    if ($el.val() === '') return repo.text;
                    sku = $el.data('sku') || sku;
                    stock = $el.data('stock') !== undefined ? $el.data('stock') : stock;
                    stockVal = parseFloat(stock) || 0;
                    name = $el.data('name') || name || $el.text();
                }

                let badgeClass = stockVal > 0 ? 'bg-success' : 'bg-danger';

                return '<div class="d-flex align-items-center justify-content-between w-100 py-1" style="color: #0f172a;">' +
                    '<div>' +
                        '<div class="fw-bold" style="color: #0f172a;">' + name + '</div>' +
                        '<small class="text-muted" style="font-size: 11px;">SKU: ' + sku + ' | Unit: ' + unit + '</small>' +
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

            function recalcRow($row) {
                const qtyStr = ($row.find('.main-qty-input').val() || '').toString();
                const qty = parseFloat(qtyStr) || 0;
                const price = parseFloat($row.find('.price').val()) || 0;
                const discPct = parseFloat($row.find('.item-disc-percent').val()) || 0;
                const sizeMode = $row.data('sizemode') || $row.find('.hidden-size-mode').val();
                const unitVal = ($row.find('.unit-input-val').val() || '').toLowerCase();
                const pieces_per_m2 = parseFloat($row.data('pieces_per_m2')) || 0;
                const ppb = parseFloat($row.find('.hidden-pieces-per-box').val()) || parseFloat($row.data('pieces_per_box')) || 1;

                let gross = 0;
                const isPiece = (unitVal === 'pcs' || unitVal === 'pc' || unitVal === 'piece');
                const isCarton = (unitVal === 'carton' || unitVal === 'ctn' || unitVal === 'box' || (!isPiece && ['by_cartons', 'by_bandal'].includes(sizeMode)));

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
                $('#totalAmount').text(subtotal.toFixed(2));

                const prevBal = parseFloat($('#tPrev').text()) || 0;
                const totalPaid = parseFloat($('#totalPaid').text()) || 0;
                const totalPayable = (prevBal + net) - totalPaid;
                
                $('#tTotalPayable').text(totalPayable.toFixed(2));
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
