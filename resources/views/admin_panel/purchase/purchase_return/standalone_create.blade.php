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
        --pos-bg: #F8FAFC;
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

    .purchase-return-page {
        max-width: 100%;
        margin: 0 auto;
    }

    .pr-card {
        background: var(--pos-card);
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius-lg);
        box-shadow: var(--pos-shadow-sm);
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    /* ---------- ERP HEADER & VENDOR KPI PILLS ---------- */
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
        color: #9333EA;
        border-color: #E9D5FF;
    }

    /* ---------- INPUT CONTROLS ---------- */
    .form-control, .form-select {
        height: var(--pos-input-h);
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius);
        font-size: 13px;
        color: var(--pos-text);
        background-color: #FFFFFF;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--pos-red);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, .12);
        outline: 0;
    }
    .form-control-sm, .form-select-sm {
        height: 32px;
        font-size: 12px;
        padding: 4px 8px;
    }

    /* ---------- SELECT2 CUSTOMIZATION ---------- */
    .select2-container .select2-selection--single {
        height: var(--pos-input-h) !important;
        border: 1px solid var(--pos-border) !important;
        border-radius: var(--pos-radius) !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--pos-text) !important;
        font-size: 13px !important;
        line-height: var(--pos-input-h) !important;
        padding-left: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: var(--pos-input-h) !important;
        right: 8px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--pos-red) !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, .12) !important;
    }
    .select2-dropdown {
        border: 1px solid var(--pos-border) !important;
        border-radius: var(--pos-radius) !important;
        box-shadow: var(--pos-shadow-md) !important;
        font-size: 13px !important;
        z-index: 9999 !important;
    }

    /* ---------- PRODUCTS TABLE ---------- */
    .table-container {
        border: 1px solid var(--pos-border);
        border-radius: var(--pos-radius);
        overflow-x: auto;
        background: #FFFFFF;
    }
    .pr-table {
        margin-bottom: 0;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12.5px;
    }
    .pr-table thead th {
        background: #F8FAFC;
        color: #475569;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
        padding: 10px 8px;
        border-bottom: 1px solid var(--pos-border);
        border-right: 1px solid #EDF2F7;
        white-space: nowrap;
        vertical-align: middle;
        text-align: center;
    }
    .pr-table thead th:last-child {
        border-right: none;
    }
    .pr-table tbody td {
        padding: 6px 8px;
        vertical-align: middle;
        border-bottom: 1px solid #F1F5F9;
        border-right: 1px solid #F1F5F9;
        background: #FFFFFF;
    }
    .pr-table tbody td:last-child {
        border-right: none;
    }
    .pr-table tbody tr:hover td {
        background: #FEF2F2 !important;
    }
    .pr-table tbody tr.row-selected td {
        background: #FEE2E2 !important;
    }

    .tbl-input {
        height: 32px;
        padding: 4px 6px;
        font-size: 12.5px;
        border: 1px solid var(--pos-border);
        border-radius: 6px;
        text-align: right;
        font-weight: 600;
        background: #FFFFFF;
        width: 100%;
    }
    .tbl-input:focus {
        border-color: var(--pos-red);
        background: #FFF;
        box-shadow: 0 0 0 2px rgba(220, 38, 38, .15);
        outline: 0;
    }
    .tbl-readonly {
        background: #F8FAFC !important;
        color: #475569;
        font-weight: 600;
        border: 1px solid #E2E8F0;
        cursor: default;
    }

    /* ---------- SEARCH BAR & QUICK DRAWER ---------- */
    .barcode-search-wrap {
        position: relative;
    }
    .barcode-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--pos-muted);
        font-size: 16px;
        pointer-events: none;
        z-index: 2;
    }
    .barcode-input {
        padding-left: 38px !important;
        font-weight: 500;
    }

    /* ---------- SUMMARY CARD ---------- */
    .summary-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 13px;
        color: #475569;
    }
    .summary-row.total-row {
        border-top: 2px dashed #CBD5E1;
        margin-top: 8px;
        padding-top: 10px;
        font-size: 16px;
        font-weight: 800;
        color: var(--pos-red);
    }

    /* ---------- BUTTONS ---------- */
    .btn-pos-primary {
        background: var(--pos-red);
        color: #FFF;
        border: none;
        font-weight: 600;
        border-radius: var(--pos-radius);
        padding: 8px 18px;
        transition: all .15s ease;
    }
    .btn-pos-primary:hover {
        background: var(--pos-red-hover);
        color: #FFF;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(220, 38, 38, .25);
    }
</style>

<div class="purchase-return-page container-fluid py-3">
    <!-- Header Section -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="fas fa-undo-alt text-danger"></i> Direct Purchase Return / Debit Note
            </h4>
            <p class="text-muted mb-0 small">
                Return goods directly to any vendor without selecting an existing purchase invoice. Stock is automatically deducted and vendor liability reduced.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('purchase.return.index') }}" class="btn btn-outline-secondary px-3 shadow-sm fw-medium d-inline-flex align-items-center gap-1" style="border-radius: 8px;">
                <i class="fas fa-list"></i> Returns List
            </a>
            <a href="{{ route('Purchase.home') }}" class="btn btn-outline-primary px-3 shadow-sm fw-medium d-inline-flex align-items-center gap-1" style="border-radius: 8px;">
                <i class="fas fa-shopping-cart"></i> All Purchases
            </a>
        </div>
    </div>

    <form id="purchaseReturnForm" action="{{ route('purchase.return.store') }}" method="POST">
        @csrf

        <!-- 2-Tier ERP Vendor & Header Card -->
        <div class="erp-header-card p-3 mb-3">
            <!-- Row 1: Vendor Selection & Balance Ribbon -->
            <div class="row g-2 align-items-center mb-2">
                <div class="col-lg-5 col-md-6">
                    <label class="erp-field-label">
                        <i class="fas fa-truck text-danger me-1"></i> Vendor <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <select name="vendor_id" id="vendorSelect" class="form-select select2-vendor" required>
                            <option value="">Select Vendor (Search by Name, Phone)...</option>
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id }}" 
                                    data-balance="{{ $v->ledger->closing_balance ?? 0 }}"
                                    data-phone="{{ $v->phone ?? '' }}"
                                    data-address="{{ $v->address ?? '' }}">
                                    {{ $v->name }} {{ $v->phone ? '('.$v->phone.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-danger px-2" title="Quick Add Vendor" data-bs-toggle="modal" data-bs-target="#quickAddVendorModal">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="col-lg-7 col-md-6">
                    <label class="erp-field-label text-muted">
                        <i class="fas fa-wallet text-secondary me-1"></i> Vendor Ledger Status
                    </label>
                    <div class="erp-status-strip d-flex flex-wrap align-items-center gap-2 justify-content-between">
                        <div class="erp-kpi-pill erp-kpi-danger" title="Previous Outstanding Balance">
                            <span class="text-muted small">Prev Payable:</span>
                            <strong id="kpiPrevPayable">Rs 0.00</strong>
                        </div>
                        <div class="erp-kpi-pill erp-kpi-primary" title="Return Bill Value">
                            <span class="text-muted small">Return Value:</span>
                            <strong id="kpiReturnValue">Rs 0.00</strong>
                        </div>
                        <div class="erp-kpi-pill erp-kpi-success" title="Cash/Bank Refund Received">
                            <span class="text-muted small">Refund Recv:</span>
                            <strong id="kpiRefundReceived">Rs 0.00</strong>
                        </div>
                        <div class="erp-kpi-pill erp-kpi-purple" title="Net Balance After Return">
                            <span class="text-muted small">New Payable:</span>
                            <strong id="kpiNewPayable">Rs 0.00</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Document Details -->
            <div class="row g-2 align-items-center pt-2 border-top">
                <div class="col-md-3 col-sm-6">
                    <label class="erp-field-label">
                        <i class="fas fa-file-invoice text-muted me-1"></i> Return Invoice #
                    </label>
                    <input type="text" class="form-control form-control-sm font-monospace fw-bold tbl-readonly" value="{{ $nextInvoice }}" readonly>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="erp-field-label">
                        <i class="fas fa-calendar-alt text-muted me-1"></i> Return Date <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="return_date" class="form-control form-control-sm fw-semibold" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="erp-field-label">
                        <i class="fas fa-warehouse text-muted me-1"></i> Source Warehouse <span class="text-danger">*</span>
                    </label>
                    <select name="warehouse_id" id="warehouseSelect" class="form-select form-select-sm fw-semibold" required>
                        @foreach ($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->warehouse_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="erp-field-label">
                        <i class="fas fa-comment-dots text-muted me-1"></i> Return Reason / Note
                    </label>
                    <input type="text" name="return_reason" class="form-control form-control-sm" placeholder="e.g. Damaged Goods, Expired, Wrong Batch">
                </div>
            </div>
        </div>

        <!-- Product Search & Quick Add Section -->
        <div class="pr-card p-3 mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-7 col-12">
                    <div class="barcode-search-wrap">
                        <i class="fas fa-barcode barcode-icon"></i>
                        <select id="productSearchSelect" class="form-control barcode-input" style="width: 100%;">
                            <option value="">Scan Barcode or Search Product by Name, Code, SKU (F2)...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-5 col-12 text-md-end text-start">
                    <span class="text-muted small me-2"><i class="fas fa-keyboard text-secondary me-1"></i> Press <strong>F2</strong> to search product</span>
                    <button type="button" class="btn btn-outline-danger btn-sm px-3" id="btnAddNewRow">
                        <i class="fas fa-plus me-1"></i> Add Manual Row
                    </button>
                </div>
            </div>
        </div>

        <!-- Return Products Grid -->
        <div class="pr-card p-0 mb-3 overflow-hidden">
            <div class="table-container border-0">
                <table class="table pr-table align-middle" id="returnItemsTable">
                    <thead>
                        <tr>
                            <th style="width: 4%;">#</th>
                            <th style="width: 28%; text-align: left;">Product / Variant</th>
                            <th style="width: 11%;">Wh Stock</th>
                            <th style="width: 8%;">Packing</th>
                            <th style="width: 9%;">Return Ctn</th>
                            <th style="width: 8%;">Return Loose</th>
                            <th style="width: 9%;">Total Pcs</th>
                            <th style="width: 10%;">Price (Rs)</th>
                            <th style="width: 8%;">Disc (Rs)</th>
                            <th style="width: 11%;">Net Total (Rs)</th>
                            <th style="width: 4%;">Act</th>
                        </tr>
                    </thead>
                    <tbody id="returnItemsBody">
                        <tr id="emptyRowPlaceholder">
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                <span>No items added to return yet. Scan a barcode or search a product above to add items.</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Financial Summary & Immediate Refund Section -->
        <div class="row g-3 mb-4">
            <!-- Left: Immediate Refund from Vendor & Remarks -->
            <div class="col-lg-7 col-12">
                <div class="summary-card h-100">
                    <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-hand-holding-usd text-success"></i> Immediate Refund from Vendor (Optional)
                    </h6>
                    <p class="text-muted small mb-3">
                        Agar vendor ne foran naqd (Cash) ya bank ke zariye refund ada kiya hai, to neeche checkbox check karke account aur amount enter karein. System foran Receipt Voucher create karke cash/bank account mein amount barha dega.
                    </p>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="immediateRefundToggle">
                        <label class="form-check-label fw-bold text-dark" for="immediateRefundToggle">
                            Vendor issued immediate Cash / Bank Refund
                        </label>
                    </div>

                    <div id="refundDetailsSection" class="p-3 bg-light rounded-3 border" style="display: none;">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="erp-field-label">Deposit Account</label>
                                <select name="payment_account_id[]" id="refundAccountSelect" class="form-select form-select-sm">
                                    @foreach ($payment_accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->title }} ({{ $acc->head->name ?? 'Account' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="erp-field-label">Refund Amount (Rs)</label>
                                <input type="number" step="any" name="payment_amount[]" id="refundAmountInput" class="form-control form-control-sm fw-bold text-success" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="erp-field-label">General Remarks / Memo</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="Additional notes or transport details..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Right: Bill Totals & Submit -->
            <div class="col-lg-5 col-12">
                <div class="summary-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-calculator text-danger"></i> Return Financial Summary
                        </h6>

                        <div class="summary-row">
                            <span>Gross Bill Amount:</span>
                            <span class="fw-bold font-monospace" id="summaryGross">Rs 0.00</span>
                        </div>
                        <div class="summary-row">
                            <span>Total Item Discounts:</span>
                            <span class="fw-bold text-success font-monospace" id="summaryItemDiscount">- Rs 0.00</span>
                        </div>
                        <div class="summary-row align-items-center">
                            <span>Extra Bill Discount (Rs):</span>
                            <input type="number" step="any" name="extra_discount" id="extraDiscountInput" class="form-control form-control-sm text-end fw-bold" style="width: 130px;" value="0">
                        </div>

                        <div class="summary-row total-row">
                            <span>Net Return (Debit Note):</span>
                            <span class="font-monospace" id="summaryNetReturn">Rs 0.00</span>
                        </div>
                        <small class="text-muted d-block mt-1">This total amount will be debited to vendor account, reducing payable liability.</small>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-pos-primary w-100 py-2 fs-6 shadow-sm d-flex align-items-center justify-content-center gap-2" id="btnSubmitReturn">
                            <i class="fas fa-check-circle"></i> Submit Purchase Return
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Quick Add Vendor Modal -->
<div class="modal fade" id="quickAddVendorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white py-2 px-3">
                <h6 class="modal-title fw-bold"><i class="fas fa-user-plus me-1"></i> Quick Add Vendor</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickVendorForm">
                @csrf
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Vendor Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" required placeholder="e.g. Al-Madina Traders">
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Phone / Mobile</label>
                            <input type="text" name="phone" class="form-control form-control-sm" placeholder="0300-1234567">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Opening Balance (Payable)</label>
                            <input type="number" step="any" name="opening_balance" class="form-control form-control-sm" value="0">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Address / City</label>
                        <input type="text" name="address" class="form-control form-control-sm" placeholder="Shop #, Market, City">
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-3" id="btnSaveQuickVendor">Save Vendor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let rowIndex = 0;

    // Initialize Vendor Select2
    $('.select2-vendor').select2({
        placeholder: "Select Vendor...",
        allowClear: true,
        width: '100%'
    });

    // Update Vendor Balance Ribbon when vendor changes
    $('#vendorSelect').on('change', function() {
        const vendorId = $(this).val();
        if (!vendorId) {
            updateRibbon(0);
            return;
        }

        const selectedOption = $(this).find('option:selected');
        let balance = parseFloat(selectedOption.data('balance')) || 0;

        // Fetch latest balance via AJAX
        $.ajax({
            url: `/vendor/${vendorId}/balance`,
            method: 'GET',
            success: function(res) {
                if (res && res.closing_balance !== undefined) {
                    balance = parseFloat(res.closing_balance) || 0;
                }
                updateRibbon(balance);
            },
            error: function() {
                updateRibbon(balance);
            }
        });
    });

    function updateRibbon(prevPayable) {
        const netReturn = getNetReturnAmount();
        const refundRecv = parseFloat($('#refundAmountInput').val()) || 0;
        const newPayable = prevPayable - netReturn + refundRecv;

        $('#kpiPrevPayable').text('Rs ' + prevPayable.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#kpiReturnValue').text('Rs ' + netReturn.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#kpiRefundReceived').text('Rs ' + refundRecv.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#kpiNewPayable').text('Rs ' + newPayable.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        // Styling based on positive/negative
        if (newPayable > 0) {
            $('#kpiNewPayable').parent().removeClass('erp-kpi-success erp-kpi-purple').addClass('erp-kpi-danger');
        } else if (newPayable < 0) {
            $('#kpiNewPayable').parent().removeClass('erp-kpi-danger erp-kpi-purple').addClass('erp-kpi-success');
        } else {
            $('#kpiNewPayable').parent().removeClass('erp-kpi-danger erp-kpi-success').addClass('erp-kpi-purple');
        }
    }

    // Toggle Immediate Refund Details
    $('#immediateRefundToggle').on('change', function() {
        if ($(this).is(':checked')) {
            $('#refundDetailsSection').slideDown(200);
            const netReturn = getNetReturnAmount();
            $('#refundAmountInput').val(netReturn > 0 ? netReturn : '');
        } else {
            $('#refundDetailsSection').slideUp(200);
            $('#refundAmountInput').val('');
        }
        recalculateTotals();
    });

    $('#refundAmountInput').on('input', function() {
        const prevBal = parseFloat($('#vendorSelect').find('option:selected').data('balance')) || 0;
        updateRibbon(prevBal);
    });

    // Product Search Select2 with AJAX
    $('#productSearchSelect').select2({
        placeholder: "Scan Barcode or Search Product by Name, Code, SKU (F2)...",
        minimumInputLength: 1,
        width: '100%',
        ajax: {
            url: "{{ route('products.ajax.search') }}",
            dataType: 'json',
            delay: 150,
            data: function(params) {
                return {
                    q: params.term || '',
                    warehouse_id: $('#warehouseSelect').val(),
                    is_return: 1,
                    allow_zero_stock: 1
                };
            },
            processResults: function(data) {
                return {
                    results: (data.results || data || []).map(function(item) {
                        return {
                            id: item.id,
                            text: item.text || item.item_name,
                            data: item
                        };
                    })
                };
            },
            cache: true
        }
    });

    // When Product is Selected from Dropdown
    $('#productSearchSelect').on('select2:select', function(e) {
        const item = e.params.data.data || e.params.data;
        addProductRow(item);
        $(this).val(null).trigger('change');
    });

    // Shortcut F2 to search product
    $(document).on('keydown', function(e) {
        if (e.key === 'F2') {
            e.preventDefault();
            $('#productSearchSelect').select2('open');
        }
        if (e.altKey && (e.key === 'v' || e.key === 'V')) {
            e.preventDefault();
            $('#vendorSelect').select2('open');
        }
    });

    // Manual Row Button
    $('#btnAddNewRow').on('click', function() {
        $('#productSearchSelect').select2('open');
    });

    // Add Product Row to Table
    function addProductRow(item) {
        $('#emptyRowPlaceholder').hide();
        rowIndex++;

        const ppb = parseFloat(item.pieces_per_box || item.ppb || 1) || 1;
        const currentStockPieces = parseFloat(item.stock_pieces || item.warehouse_stocks_sum_total_pieces || 0);
        
        let stockDisplay = '';
        if (ppb > 1) {
            const ctn = Math.floor(currentStockPieces / ppb);
            const loose = Math.round(currentStockPieces % ppb);
            stockDisplay = `${ctn} Ctn ${loose > 0 ? loose + ' Pcs' : ''} (${Math.round(currentStockPieces)} P)`;
        } else {
            stockDisplay = `${Math.round(currentStockPieces)} Pcs`;
        }

        // Price fallback
        let price = parseFloat(item.purchase_price_per_piece || item.trade_price || item.purchase_price_per_box || 0);
        if (ppb > 1 && item.purchase_price_per_box && !item.purchase_price_per_piece) {
            price = parseFloat(item.purchase_price_per_box) / ppb;
        }

        const unitName = item.unit_name || (ppb > 1 ? 'Carton' : 'Pcs');

        const rowHtml = `
            <tr data-row-id="${rowIndex}" class="item-row">
                <td class="text-center text-muted fw-bold">${rowIndex}</td>
                <td>
                    <div class="fw-bold text-dark">${item.name || item.text}</div>
                    <small class="text-muted">SKU: ${item.sku || item.item_code || 'N/A'}</small>
                    <input type="hidden" name="product_id[]" value="${item.id}">
                    <input type="hidden" name="unit[]" value="${unitName}">
                    <input type="hidden" name="color[]" value="${item.variant_data || item.color || ''}">
                    <input type="hidden" class="row-ppb" value="${ppb}">
                    <input type="hidden" class="row-stock-pieces" value="${currentStockPieces}">
                </td>
                <td class="text-center">
                    <span class="badge ${currentStockPieces > 0 ? 'bg-light text-dark border' : 'bg-danger text-white'} px-2 py-1" style="font-size: 11px;">
                        ${stockDisplay}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge bg-secondary px-2 py-1" style="font-size: 11px;">${ppb > 1 ? '1 Ctn = ' + ppb + ' P' : unitName}</span>
                </td>
                <td>
                    <input type="number" step="1" min="0" name="return_carton[]" class="tbl-input input-carton" placeholder="0" value="1">
                </td>
                <td>
                    <input type="number" step="1" min="0" name="return_loose[]" class="tbl-input input-loose" placeholder="0" value="0">
                </td>
                <td>
                    <input type="number" step="any" min="0" name="qty[]" class="tbl-input tbl-readonly input-total-pcs text-center fw-bold text-danger" value="${ppb}" readonly>
                </td>
                <td>
                    <input type="number" step="any" min="0" name="price[]" class="tbl-input input-price text-end" value="${price.toFixed(2)}">
                </td>
                <td>
                    <input type="number" step="any" min="0" name="item_discount[]" class="tbl-input input-discount text-end" value="0">
                </td>
                <td>
                    <input type="number" step="any" min="0" name="line_total[]" class="tbl-input tbl-readonly input-line-total text-end fw-bold text-dark" value="${(ppb * price).toFixed(2)}" readonly>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm p-1 px-2 btn-remove-row" title="Remove">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#returnItemsBody').append(rowHtml);
        const $newRow = $('#returnItemsBody tr:last-child');
        calculateRow($newRow);
        recalculateTotals();

        // Focus carton input of new row
        $newRow.find('.input-carton').focus().select();
    }

    // Row Calculation Logic
    function calculateRow($row) {
        const ppb = parseFloat($row.find('.row-ppb').val()) || 1;
        const carton = parseFloat($row.find('.input-carton').val()) || 0;
        const loose = parseFloat($row.find('.input-loose').val()) || 0;
        const price = parseFloat($row.find('.input-price').val()) || 0;
        const disc = parseFloat($row.find('.input-discount').val()) || 0;
        const whStock = parseFloat($row.find('.row-stock-pieces').val()) || 0;

        let totalPieces = (carton * ppb) + loose;
        $row.find('.input-total-pcs').val(totalPieces);

        // Warning if returning more than current warehouse stock
        if (totalPieces > whStock && whStock >= 0) {
            $row.find('.input-total-pcs').addClass('bg-warning-subtle text-danger').attr('title', `Warning: Return qty (${totalPieces}) exceeds warehouse stock (${whStock})`);
        } else {
            $row.find('.input-total-pcs').removeClass('bg-warning-subtle text-danger').removeAttr('title');
        }

        const gross = totalPieces * price;
        const net = Math.max(0, gross - disc);
        $row.find('.input-line-total').val(net.toFixed(2));
    }

    // Events on Row Input changes
    $('#returnItemsBody').on('input', '.input-carton, .input-loose, .input-price, .input-discount', function() {
        const $row = $(this).closest('tr');
        calculateRow($row);
        recalculateTotals();
    });

    // Remove Row
    $('#returnItemsBody').on('click', '.btn-remove-row', function() {
        $(this).closest('tr').remove();
        if ($('#returnItemsBody tr.item-row').length === 0) {
            $('#emptyRowPlaceholder').show();
        }
        recalculateTotals();
    });

    // Extra Bill Discount change
    $('#extraDiscountInput').on('input', function() {
        recalculateTotals();
    });

    function getNetReturnAmount() {
        let gross = 0;
        let itemDisc = 0;
        $('#returnItemsBody tr.item-row').each(function() {
            const lineTotal = parseFloat($(this).find('.input-line-total').val()) || 0;
            const lineDisc = parseFloat($(this).find('.input-discount').val()) || 0;
            gross += (lineTotal + lineDisc);
            itemDisc += lineDisc;
        });

        const extraDisc = parseFloat($('#extraDiscountInput').val()) || 0;
        return Math.max(0, (gross - itemDisc) - extraDisc);
    }

    // Recalculate Grand Totals
    function recalculateTotals() {
        let gross = 0;
        let itemDisc = 0;

        $('#returnItemsBody tr.item-row').each(function() {
            const lineTotal = parseFloat($(this).find('.input-line-total').val()) || 0;
            const lineDisc = parseFloat($(this).find('.input-discount').val()) || 0;
            gross += (lineTotal + lineDisc);
            itemDisc += lineDisc;
        });

        const extraDisc = parseFloat($('#extraDiscountInput').val()) || 0;
        const netReturn = Math.max(0, (gross - itemDisc) - extraDisc);

        $('#summaryGross').text('Rs ' + gross.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#summaryItemDiscount').text('- Rs ' + itemDisc.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#summaryNetReturn').text('Rs ' + netReturn.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        // Update refund field if immediate refund toggle is checked
        if ($('#immediateRefundToggle').is(':checked')) {
            const currentRefundVal = parseFloat($('#refundAmountInput').val()) || 0;
            if (currentRefundVal <= 0 || currentRefundVal > netReturn) {
                $('#refundAmountInput').val(netReturn > 0 ? netReturn : '');
            }
        }

        const prevBal = parseFloat($('#vendorSelect').find('option:selected').data('balance')) || 0;
        updateRibbon(prevBal);
    }

    // Quick Add Vendor Submission
    $('#quickVendorForm').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#btnSaveQuickVendor');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('vendors.store.ajax') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(res) {
                $btn.prop('disabled', false).text('Save Vendor');
                if (res.success || res.vendor) {
                    const vendor = res.vendor || res;
                    const newOption = new Option(vendor.name + (vendor.phone ? ' (' + vendor.phone + ')' : ''), vendor.id, true, true);
                    $(newOption).attr('data-balance', vendor.opening_balance || 0);
                    $('#vendorSelect').append(newOption).trigger('change');

                    $('#quickAddVendorModal').modal('hide');
                    $('#quickVendorForm')[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Vendor Created',
                        text: 'New vendor added and selected.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', res.message || 'Could not save vendor.', 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Save Vendor');
                Swal.fire('Error', xhr.responseJSON?.message || 'Server error occurred.', 'error');
            }
        });
    });

    // Form Submit with Validation & SweetAlert
    $('#purchaseReturnForm').on('submit', function(e) {
        e.preventDefault();

        if (!$('#vendorSelect').val()) {
            Swal.fire('Vendor Required', 'Please select a vendor for this return.', 'warning');
            $('#vendorSelect').select2('open');
            return false;
        }

        if ($('#returnItemsBody tr.item-row').length === 0) {
            Swal.fire('No Items', 'Please add at least one product to return.', 'warning');
            return false;
        }

        const netAmount = getNetReturnAmount();
        const vendorName = $('#vendorSelect option:selected').text();

        Swal.fire({
            title: 'Confirm Purchase Return?',
            html: `You are returning goods worth <strong>Rs ${netAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong> to <strong>${vendorName}</strong>.<br><br>Stock will be deducted from warehouse and vendor payable balance will be reduced.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#64748B',
            confirmButtonText: '<i class="fas fa-check me-1"></i> Yes, Process Return',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const $btn = $('#btnSubmitReturn');
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Processing Return...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Return Completed!',
                            text: response.message || 'Purchase return processed successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect || "{{ route('purchase.return.index') }}";
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Submit Purchase Return');
                        const msg = xhr.responseJSON?.message || 'Failed to process return. Please check items and try again.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
