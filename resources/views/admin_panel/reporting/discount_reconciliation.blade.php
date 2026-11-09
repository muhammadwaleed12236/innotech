@extends('admin_panel.layout.app')

@section('content')
<style>
    .discount-recon-container {
        padding: 14px 18px;
        background: #f8fafc;
        min-height: calc(100vh - 75px);
        font-family: inherit;
    }
    .filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        margin-bottom: 14px;
    }
    .metric-pill-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }
    .metric-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        position: relative;
        overflow: hidden;
    }
    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }
    .metric-card.card-blue::before { background: #2563eb; }
    .metric-card.card-green::before { background: #10b981; }
    .metric-card.card-purple::before { background: #8b5cf6; }
    .metric-card.card-amber::before { background: #f59e0b; }
    .metric-card.card-rose::before { background: #f43f5e; }
    .metric-card.card-teal::before { background: #0d9488; }
    .metric-card.card-indigo::before { background: #4f46e5; }

    .metric-card .m-title {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #64748b;
        margin-bottom: 3px;
    }
    .metric-card .m-val {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .metric-card .m-sub {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 2px;
    }

    .report-table-wrap {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow-x: auto;
    }
    .report-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .report-table thead th {
        background: #f1f5f9;
        color: #334155;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        padding: 9px 8px;
        border-bottom: 1px solid #cbd5e1;
        white-space: nowrap;
    }
    .report-table tbody td {
        padding: 8px 8px;
        font-size: 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .report-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .report-table tfoot td {
        background: #f8fafc;
        font-weight: 800;
        font-size: 12.5px;
        border-top: 2px solid #cbd5e1;
        padding: 10px 8px;
    }
    .badge-status {
        font-size: 10px;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .badge-settled { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .badge-partial { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .badge-pending { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

    .nav-tabs .nav-link {
        font-weight: 700;
        font-size: 12.5px;
        color: #64748b;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 8px 16px;
    }
    .nav-tabs .nav-link.active {
        color: #2563eb;
        background: transparent;
        border-bottom: 3px solid #2563eb;
    }
    .breakdown-pill {
        display: inline-block;
        background: #f1f5f9;
        color: #475569;
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 10.5px;
        margin: 1px;
        border: 1px solid #e2e8f0;
    }
</style>

<div class="discount-recon-container">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="fas fa-tags text-primary me-2"></i> Discount &amp; Profit Reconciliation Report
            </h4>
            <small class="text-muted">Company Purchase Discounts vs Customer Advance Discounts &amp; Profit Tracking</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success btn-sm fw-bold" id="btnExportCsv">
                <i class="fas fa-file-excel me-1"></i> Export Excel/CSV
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print Statement
            </button>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="filter-card">
        <form id="filterForm" class="row g-2 align-items-end">
            <div class="col-md-2 col-6">
                <label class="form-label fw-bold text-muted small mb-1">Quick Month</label>
                <input type="month" class="form-control form-control-sm" id="monthPicker" value="{{ $currentMonth }}">
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label fw-bold text-muted small mb-1">From Date</label>
                <input type="date" class="form-control form-control-sm" id="dateFrom" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label fw-bold text-muted small mb-1">To Date</label>
                <input type="date" class="form-control form-control-sm" id="dateTo" value="{{ $dateTo }}">
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label fw-bold text-muted small mb-1">Company / Principal (Vendor)</label>
                <select class="form-select form-select-sm" id="vendorSelect">
                    <option value="">All Companies / Suppliers</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label fw-bold text-muted small mb-1">Company Settlement</label>
                <select class="form-select form-select-sm" id="settlementFilter">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending from Company</option>
                    <option value="partial">Partially Settled</option>
                    <option value="settled">Fully Settled</option>
                </select>
            </div>
            <div class="col-md-1 col-6 d-flex gap-1">
                <button type="button" class="btn btn-primary btn-sm w-100 fw-bold" id="btnFilter" title="Apply Filter">
                    <i class="fas fa-filter"></i> Apply
                </button>
            </div>
        </form>

        {{-- Quick Range Presets --}}
        <div class="d-flex flex-wrap align-items-center gap-1 mt-2 pt-2 border-top">
            <span class="text-muted small fw-bold me-1" style="font-size: 11px;"><i class="fas fa-bolt text-warning me-1"></i> Quick Presets:</span>
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 btn-preset" data-preset="all" style="font-size: 11px;">
                <i class="fas fa-globe me-1"></i> All Time (All Records)
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 btn-preset" data-preset="ytd" style="font-size: 11px;">
                This Year (YTD)
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 btn-preset" data-preset="this_month" style="font-size: 11px;">
                This Month
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 btn-preset" data-preset="last_month" style="font-size: 11px;">
                Last Month
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 ms-auto" id="btnResetFilter" style="font-size: 11px;">
                <i class="fas fa-undo me-1"></i> Reset
            </button>
        </div>
    </div>

    {{-- KPI Metric Cards --}}
    <div class="metric-pill-row">
        <div class="metric-card card-blue">
            <div class="m-title">Total Company Discount</div>
            <div class="m-val text-primary" id="cardCompanyDiscAmt">Rs 0.00</div>
            <div class="m-sub">Given by Principal on Purchases</div>
        </div>

        <div class="metric-card card-green">
            <div class="m-title">Our Margin Share</div>
            <div class="m-val text-success" id="cardOurDiscAmt">Rs 0.00</div>
            <div class="m-sub" id="cardOurDiscSub">Distributor Retained Income</div>
        </div>

        <div class="metric-card card-purple">
            <div class="m-title">Customer Share Provided</div>
            <div class="m-val text-purple" style="color: #8b5cf6;" id="cardCustomerPassThroughAmt">Rs 0.00</div>
            <div class="m-sub">Funded by Company for Customers</div>
        </div>

        <div class="metric-card card-amber">
            <div class="m-title">Actual Customer Discount Given</div>
            <div class="m-val text-warning-emphasis" id="cardCustomerDiscGiven">Rs 0.00</div>
            <div class="m-sub">Advance given on Sales Invoices</div>
        </div>

        <div class="metric-card card-rose">
            <div class="m-title">Advance Discount Variance</div>
            <div class="m-val" id="cardAdvanceVariance">Rs 0.00</div>
            <div class="m-sub" id="cardAdvanceVarianceSub">Actual Given vs Company Funded</div>
        </div>

        <div class="metric-card card-teal">
            <div class="m-title">Company Settlement</div>
            <div class="m-val text-teal" style="color: #0d9488;" id="cardCompanySettled">Rs 0.00</div>
            <div class="m-sub" id="cardCompanyPending">Pending: Rs 0.00</div>
        </div>

        <div class="metric-card card-indigo">
            <div class="m-title">Total Realized Profit</div>
            <div class="m-val text-indigo" style="color: #4338ca;" id="cardTotalProfit">Rs 0.00</div>
            <div class="m-sub">Trading Margin + Retained Discount</div>
        </div>
    </div>

    {{-- Tabbed Views --}}
    <ul class="nav nav-tabs mb-3" id="reconTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tab-invoices-link" data-bs-toggle="tab" href="#tab-invoices" role="tab">
                <i class="fas fa-file-invoice me-1"></i> Purchase Invoice Discount Split &amp; Maddain
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-summary-link" data-bs-toggle="tab" href="#tab-summary" role="tab">
                <i class="fas fa-balance-scale me-1"></i> Monthly Discount &amp; Advance Reconciliation
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-profit-link" data-bs-toggle="tab" href="#tab-profit" role="tab">
                <i class="fas fa-chart-pie me-1"></i> Profit Calculation Statement
            </a>
        </li>
    </ul>

    <div class="tab-content" id="reconTabsContent">
        
        {{-- Tab 1: Purchase Invoices List --}}
        <div class="tab-pane fade show active" id="tab-invoices" role="tabpanel">
            <div class="report-table-wrap">
                <table class="report-table" id="invoicesTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 38px;">#</th>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Company / Supplier</th>
                            <th class="text-end">Invoice Trade Value</th>
                            <th class="text-end">Company Disc.</th>
                            <th class="text-end text-success">Our Share</th>
                            <th class="text-end text-primary">Customer Share</th>
                            <th style="min-width: 180px;">Our Maddain (Breakdown Notes)</th>
                            <th class="text-center">Settlement</th>
                            <th class="text-center" style="width: 90px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="invoicesTableBody">
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                <i class="fas fa-spinner fa-spin me-1"></i> Loading Discount Data...
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end">Total Period Summary:</td>
                            <td class="text-end" id="footTradeVal">0.00</td>
                            <td class="text-end text-primary" id="footCompanyDisc">0.00</td>
                            <td class="text-end text-success" id="footOurDisc">0.00</td>
                            <td class="text-end text-purple" id="footCustDisc">0.00</td>
                            <td colspan="2" class="text-center" id="footSettledStatus">—</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Tab 2: Monthly Summary & Advance Breakdown --}}
        <div class="tab-pane fade" id="tab-summary" role="tabpanel">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border shadow-sm h-100">
                        <div class="card-header bg-white py-2 fw-bold text-dark border-bottom">
                            <i class="fas fa-arrow-circle-down text-success me-1"></i> Company Discount Received (Purchases)
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">Total Purchases Gross Value</td>
                                        <td class="text-end fw-bold font-monospace" id="summGrossPurchase">0.00</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td class="fw-bold text-primary">Total Company Discount Received</td>
                                        <td class="text-end fw-bold font-monospace text-primary" id="summCompanyDisc">0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 text-success fw-semibold">
                                            <i class="fas fa-check-circle me-1"></i> Distributor's Retained Share (Our Margin)
                                        </td>
                                        <td class="text-end fw-bold font-monospace text-success" id="summOurShare">0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 text-primary fw-semibold">
                                            <i class="fas fa-users me-1"></i> Customer Share Provided by Company
                                        </td>
                                        <td class="text-end fw-bold font-monospace text-primary" id="summCustShare">0.00</td>
                                    </tr>
                                    <tr class="table-light">
                                        <td class="fw-semibold">Company Already Settled / Credited</td>
                                        <td class="text-end fw-bold font-monospace text-teal" id="summSettled">0.00</td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td class="fw-bold text-warning-emphasis">Company Pending Settlement (Claim Receivable)</td>
                                        <td class="text-end fw-bold font-monospace text-warning-emphasis" id="summPending">0.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border shadow-sm h-100">
                        <div class="card-header bg-white py-2 fw-bold text-dark border-bottom">
                            <i class="fas fa-arrow-circle-up text-danger me-1"></i> Customer Discount Given (Sales Advance)
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-sm table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-semibold">Total Sales Gross Turnover</td>
                                        <td class="text-end fw-bold font-monospace" id="summGrossSales">0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 text-muted">1. Regular Scheme Discount Given</td>
                                        <td class="text-end font-monospace" id="summRegDisc">0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 text-muted">2. Special Promotion Discount Given</td>
                                        <td class="text-end font-monospace" id="summSpDisc">0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 text-muted">3. Trade Activity / Scheme Bonus Given</td>
                                        <td class="text-end font-monospace" id="summTradeActivity">0.00</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-3 text-muted">4. Order Level Extra Discounts Given</td>
                                        <td class="text-end font-monospace" id="summExtraDisc">0.00</td>
                                    </tr>
                                    <tr class="table-danger">
                                        <td class="fw-bold text-danger">Total Customer Advance Discount Given</td>
                                        <td class="text-end fw-bold font-monospace text-danger" id="summTotalCustDisc">0.00</td>
                                    </tr>
                                    <tr class="table-info">
                                        <td class="fw-bold text-dark">Advance Discount Variance (Given vs Company Provided)</td>
                                        <td class="text-end fw-bold font-monospace" id="summVariance">0.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab 3: Profit Calculation Statement --}}
        <div class="tab-pane fade" id="tab-profit" role="tabpanel">
            <div class="card border shadow-sm mx-auto" style="max-width: 820px;">
                <div class="card-header bg-dark text-white py-2 d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-calculator me-1"></i> Comprehensive Distribution Profit &amp; Loss Statement</span>
                    <span class="badge bg-primary" id="profitPeriodLabel">Period</span>
                </div>
                <div class="card-body p-4">
                    <table class="table table-bordered mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Revenue &amp; Cost Component</th>
                                <th class="text-end" style="width: 200px;">Amount (PKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Total Net Sales Revenue</td>
                                <td class="text-end fw-bold font-monospace text-dark" id="pNetSales">0.00</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-3">Less: Cost of Goods Sold (Net Purchase Value)</td>
                                <td class="text-end font-monospace text-danger" id="pCogs">(0.00)</td>
                            </tr>
                            <tr class="table-secondary">
                                <td class="fw-bold">A. Direct Trading Gross Margin</td>
                                <td class="text-end fw-bold font-monospace" id="pDirectMargin">0.00</td>
                            </tr>
                            <tr class="table-success">
                                <td class="fw-bold text-success">
                                    <i class="fas fa-plus-circle me-1"></i> B. Company Retained Discount Income (Our Share)
                                    <div class="small fw-normal text-muted">Direct margin credited/retained from manufacturer discount</div>
                                </td>
                                <td class="text-end fw-bold font-monospace text-success fs-6" id="pRetainedDisc">+ 0.00</td>
                            </tr>
                            <tr class="table-primary border-top border-2 border-primary">
                                <td class="fw-bold fs-6 text-primary">
                                    <i class="fas fa-trophy me-1"></i> Total Operating Distribution Profit (A + B)
                                </td>
                                <td class="text-end fw-bold font-monospace text-primary fs-5" id="pTotalProfit">Rs 0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- MODAL: EDIT DISCOUNT SPLIT & MADDAIN --}}
<div class="modal fade" id="modalEditSplit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title fw-bold" id="modalEditSplitTitle">
                    <i class="fas fa-edit me-1"></i> Edit Purchase Discount Split &amp; Settlement
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <input type="hidden" id="editPurchaseId">
                <div class="p-2 mb-3 bg-light border rounded">
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Invoice: <strong class="text-dark" id="modalInvoiceNo">—</strong></span>
                        <span>Supplier: <strong class="text-dark" id="modalSupplier">—</strong></span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mt-1">
                        <span>Invoice Value: <strong class="text-dark font-monospace" id="modalBaseValue">Rs 0.00</strong></span>
                        <span>Total Company Discount: <strong class="text-primary font-monospace" id="modalCompDisc">0% (Rs 0.00)</strong></span>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-success mb-1">Our Share % (Distributor Margin)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" class="form-control fw-bold text-success" id="editOurPct" placeholder="e.g. 7">
                            <span class="input-group-text fw-bold">%</span>
                        </div>
                        <small class="text-muted d-block mt-1 font-monospace" id="calcOurAmt">Rs 0.00</small>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-primary mb-1">Customer Share % (Pass-Through)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.01" class="form-control fw-bold text-primary" id="editCustPct" placeholder="e.g. 8">
                            <span class="input-group-text fw-bold">%</span>
                        </div>
                        <small class="text-muted d-block mt-1 font-monospace" id="calcCustAmt">Rs 0.00</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">
                        <i class="fas fa-list-check me-1 text-muted"></i> Our Share Maddain / Breakdown Notes
                    </label>
                    <textarea class="form-control form-control-sm" id="editOurNotes" rows="2" placeholder="e.g. 3% Commission, 2% Target Scheme, 2% Cash Discount"></textarea>
                    <small class="text-muted" style="font-size: 10.5px;">Detail of which specific heads this margin was earned under.</small>
                </div>

                <hr class="my-2 text-muted">

                <div class="row g-2">
                    <div class="col-7">
                        <label class="form-label small fw-bold text-dark mb-1">Company Settled / Paid Amount</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rs</span>
                            <input type="number" step="0.01" class="form-control font-monospace" id="editSettledAmt" placeholder="0.00">
                        </div>
                        <small class="text-muted" id="calcPendingAmt">Pending from Company: Rs 0.00</small>
                    </div>
                    <div class="col-5">
                        <label class="form-label small fw-bold text-dark mb-1">Settlement Status</label>
                        <select class="form-select form-select-sm" id="editSettlementStatus">
                            <option value="pending">Pending</option>
                            <option value="partial">Partial</option>
                            <option value="settled">Fully Settled</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm fw-bold px-3" id="btnSaveSplit">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let currentInvoices = [];
    let currentSummary = {};

    function formatRs(num) {
        num = parseFloat(num) || 0;
        return 'Rs ' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatNum(num) {
        num = parseFloat(num) || 0;
        return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Month picker change automatically updates from & to dates
    $('#monthPicker').on('change', function() {
        const val = $(this).val();
        if (val) {
            const parts = val.split('-');
            const year = parseInt(parts[0]);
            const month = parseInt(parts[1]);
            const firstDay = `${year}-${String(month).padStart(2, '0')}-01`;
            const lastDay = new Date(year, month, 0).getDate();
            const lastDate = `${year}-${String(month).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;
            $('#dateFrom').val(firstDay);
            $('#dateTo').val(lastDate);
            loadReportData();
        }
    });

    // Quick Presets
    $('.btn-preset').on('click', function() {
        const preset = $(this).data('preset');
        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth(); // 0-based

        if (preset === 'all') {
            $('#monthPicker').val('');
            $('#dateFrom').val('');
            $('#dateTo').val('');
        } else if (preset === 'ytd') {
            $('#monthPicker').val('');
            $('#dateFrom').val(`${year}-01-01`);
            $('#dateTo').val(now.toISOString().split('T')[0]);
        } else if (preset === 'this_month') {
            const mStr = String(month + 1).padStart(2, '0');
            const lastDay = new Date(year, month + 1, 0).getDate();
            $('#monthPicker').val(`${year}-${mStr}`);
            $('#dateFrom').val(`${year}-${mStr}-01`);
            $('#dateTo').val(`${year}-${mStr}-${String(lastDay).padStart(2, '0')}`);
        } else if (preset === 'last_month') {
            const prevMonthDate = new Date(year, month - 1, 1);
            const pYear = prevMonthDate.getFullYear();
            const pMonth = prevMonthDate.getMonth();
            const pmStr = String(pMonth + 1).padStart(2, '0');
            const lastDay = new Date(pYear, pMonth + 1, 0).getDate();
            $('#monthPicker').val(`${pYear}-${pmStr}`);
            $('#dateFrom').val(`${pYear}-${pmStr}-01`);
            $('#dateTo').val(`${pYear}-${pmStr}-${String(lastDay).padStart(2, '0')}`);
        }

        loadReportData();
    });

    $('#btnResetFilter').on('click', function() {
        $('#monthPicker').val('');
        $('#dateFrom').val('');
        $('#dateTo').val('');
        $('#vendorSelect').val('');
        $('#settlementFilter').val('all');
        loadReportData();
    });

    $('#btnFilter').on('click', function() {
        loadReportData();
    });

    $('#btnExportCsv').on('click', function() {
        const params = $.param({
            date_from: $('#dateFrom').val(),
            date_to: $('#dateTo').val(),
            vendor_id: $('#vendorSelect').val(),
            settlement_status: $('#settlementFilter').val()
        });
        window.location.href = "{{ route('report.discount_reconciliation.export') }}?" + params;
    });

    function loadReportData() {
        $('#invoicesTableBody').html(`
            <tr>
                <td colspan="11" class="text-center py-4 text-muted">
                    <i class="fas fa-spinner fa-spin me-1"></i> Loading Discount &amp; Profit Reconciliation Data...
                </td>
            </tr>
        `);

        $.ajax({
            url: "{{ route('report.discount_reconciliation.fetch') }}",
            type: "GET",
            data: {
                date_from: $('#dateFrom').val(),
                date_to: $('#dateTo').val(),
                vendor_id: $('#vendorSelect').val(),
                settlement_status: $('#settlementFilter').val()
            },
            success: function(res) {
                if (res && res.ok) {
                    currentSummary = res.summary || {};
                    currentInvoices = res.invoices || [];
                    renderSummary(currentSummary);
                    renderInvoicesTable(currentInvoices);
                } else {
                    $('#invoicesTableBody').html('<tr><td colspan="11" class="text-center py-3 text-danger">Failed to load report data</td></tr>');
                }
            },
            error: function() {
                $('#invoicesTableBody').html('<tr><td colspan="11" class="text-center py-3 text-danger">Error communicating with server</td></tr>');
            }
        });
    }

    function renderSummary(s) {
        // KPI Cards
        $('#cardCompanyDiscAmt').text(formatRs(s.total_company_disc_amt));
        $('#cardOurDiscAmt').text(formatRs(s.total_our_disc_amt));
        $('#cardCustomerPassThroughAmt').text(formatRs(s.total_customer_passthrough_amt));
        $('#cardCustomerDiscGiven').text(formatRs(s.total_customer_disc_given));
        
        // Variance
        const variance = parseFloat(s.advance_discount_variance) || 0;
        const $vCard = $('#cardAdvanceVariance');
        if (variance > 0) {
            $vCard.text(formatRs(variance)).removeClass('text-success text-muted').addClass('text-danger');
            $('#cardAdvanceVarianceSub').text('Extra discount given from distributor pocket in advance');
        } else if (variance < 0) {
            $vCard.text(formatRs(Math.abs(variance))).removeClass('text-danger text-muted').addClass('text-success');
            $('#cardAdvanceVarianceSub').text('Surplus margin (given less than company provided)');
        } else {
            $vCard.text(formatRs(0)).removeClass('text-danger text-success').addClass('text-muted');
            $('#cardAdvanceVarianceSub').text('Balanced exactly 100%');
        }

        // Settlement
        $('#cardCompanySettled').text(formatRs(s.total_company_settled_amt));
        $('#cardCompanyPending').text('Pending: ' + formatRs(s.total_company_pending_amt));

        // Profit
        $('#cardTotalProfit').text(formatRs(s.adjusted_net_profit));

        // Tab 2 Summary Tab
        $('#summGrossPurchase').text(formatNum(s.total_purchase_value));
        $('#summCompanyDisc').text(formatNum(s.total_company_disc_amt));
        $('#summOurShare').text(formatNum(s.total_our_disc_amt));
        $('#summCustShare').text(formatNum(s.total_customer_passthrough_amt));
        $('#summSettled').text(formatNum(s.total_company_settled_amt));
        $('#summPending').text(formatNum(s.total_company_pending_amt));

        $('#summGrossSales').text(formatNum(s.total_sales_gross));
        $('#summRegDisc').text(formatNum(s.total_reg_disc_given));
        $('#summSpDisc').text(formatNum(s.total_sp_disc_given));
        $('#summTradeActivity').text(formatNum(s.total_trade_activity_given));
        $('#summExtraDisc').text(formatNum(s.total_extra_disc_given));
        $('#summTotalCustDisc').text(formatNum(s.total_customer_disc_given));
        $('#summVariance').text(formatRs(variance));

        // Tab 3 Profit Tab
        $('#profitPeriodLabel').text($('#dateFrom').val() + ' to ' + $('#dateTo').val());
        $('#pNetSales').text(formatNum(s.total_sales_net));
        const cogs = Math.max(0, (s.total_purchase_value || 0) - (s.total_company_disc_amt || 0));
        $('#pCogs').text('(' + formatNum(cogs) + ')');
        $('#pDirectMargin').text(formatNum(s.direct_trading_margin));
        $('#pRetainedDisc').text('+ ' + formatNum(s.retained_discount_profit));
        $('#pTotalProfit').text(formatRs(s.adjusted_net_profit));

        // Table Footers
        $('#footTradeVal').text(formatNum(s.total_purchase_value));
        $('#footCompanyDisc').text(formatNum(s.total_company_disc_amt));
        $('#footOurDisc').text(formatNum(s.total_our_disc_amt));
        $('#footCustDisc').text(formatNum(s.total_customer_passthrough_amt));
        $('#footSettledStatus').html(`Settled: <strong>${formatNum(s.total_company_settled_amt)}</strong> | Pending: <strong>${formatNum(s.total_company_pending_amt)}</strong>`);
    }

    function renderInvoicesTable(list) {
        if (!list || list.length === 0) {
            $('#invoicesTableBody').html('<tr><td colspan="11" class="text-center py-4 text-muted">No purchase invoices found for this period.</td></tr>');
            return;
        }

        let html = '';
        list.forEach((inv, index) => {
            let statusBadge = '<span class="badge-status badge-pending">Pending</span>';
            if (inv.settlement_status === 'settled') {
                statusBadge = '<span class="badge-status badge-settled">Settled</span>';
            } else if (inv.settlement_status === 'partial') {
                statusBadge = '<span class="badge-status badge-partial">Partial</span>';
            }

            // Render Maddain tags
            let notesHtml = '<span class="text-muted small">—</span>';
            if (inv.our_disc_notes && inv.our_disc_notes.trim() !== '') {
                const parts = inv.our_disc_notes.split(/[,;\n]/).filter(p => p.trim() !== '');
                if (parts.length > 0) {
                    notesHtml = parts.map(p => `<span class="breakdown-pill"><i class="fas fa-check text-success me-1"></i>${p.trim()}</span>`).join('');
                } else {
                    notesHtml = `<small class="text-dark">${inv.our_disc_notes}</small>`;
                }
            }

            html += `
                <tr data-id="${inv.id}">
                    <td class="text-center font-monospace">${index + 1}</td>
                    <td class="fw-bold text-dark font-monospace">${inv.invoice_no}</td>
                    <td class="text-muted font-monospace" style="font-size: 11px;">${inv.purchase_date}</td>
                    <td class="fw-semibold text-dark">${inv.vendor_name}</td>
                    <td class="text-end font-monospace">${formatNum(inv.base_value)}</td>
                    <td class="text-end font-monospace">
                        <span class="d-block fw-bold text-primary">${formatNum(inv.company_disc_amount)}</span>
                        <small class="text-muted" style="font-size: 10px;">${inv.company_disc_percent}%</small>
                    </td>
                    <td class="text-end font-monospace">
                        <span class="d-block fw-bold text-success">${formatNum(inv.our_disc_amount)}</span>
                        <small class="text-muted" style="font-size: 10px;">${inv.our_disc_percent}%</small>
                    </td>
                    <td class="text-end font-monospace">
                        <span class="d-block fw-bold" style="color: #8b5cf6;">${formatNum(inv.customer_disc_amount)}</span>
                        <small class="text-muted" style="font-size: 10px;">${inv.customer_disc_percent}%</small>
                    </td>
                    <td>${notesHtml}</td>
                    <td class="text-center">
                        ${statusBadge}
                        <div class="small font-monospace text-muted mt-1" style="font-size: 10px;">
                            Pd: ${formatNum(inv.company_settled_amount)}
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2 btn-open-split" data-id="${inv.id}" title="Edit Discount Split & Maddain">
                            <i class="fas fa-edit"></i> Split
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#invoicesTableBody').html(html);
    }

    // Modal Interaction
    $(document).on('click', '.btn-open-split', function() {
        const id = $(this).data('id');
        const inv = currentInvoices.find(item => item.id == id);
        if (!inv) return;

        $('#editPurchaseId').val(inv.id);
        $('#modalInvoiceNo').text(inv.invoice_no);
        $('#modalSupplier').text(inv.vendor_name);
        $('#modalBaseValue').text(formatRs(inv.base_value));
        $('#modalCompDisc').text(inv.company_disc_percent + '% (' + formatRs(inv.company_disc_amount) + ')');

        $('#editOurPct').val(inv.our_disc_percent || '');
        $('#editCustPct').val(inv.customer_disc_percent || '');
        $('#editOurNotes').val(inv.our_disc_notes || '');
        $('#editSettledAmt').val(inv.company_settled_amount || 0);
        $('#editSettlementStatus').val(inv.settlement_status || 'pending');

        recalcModalAmounts(inv.base_value, inv.company_disc_percent, inv.company_disc_amount);

        $('#modalEditSplit').modal('show');
    });

    function recalcModalAmounts(baseValue, totalCompPct, totalCompAmt) {
        baseValue = parseFloat(baseValue) || 0;
        totalCompPct = parseFloat(totalCompPct) || 0;
        totalCompAmt = parseFloat(totalCompAmt) || 0;

        const ourPct = parseFloat($('#editOurPct').val()) || 0;
        const custPct = parseFloat($('#editCustPct').val()) || 0;
        const settled = parseFloat($('#editSettledAmt').val()) || 0;

        const ourAmt = (baseValue * ourPct) / 100;
        const custAmt = (baseValue * custPct) / 100;
        const pending = Math.max(0, totalCompAmt - settled);

        $('#calcOurAmt').text(formatRs(ourAmt));
        $('#calcCustAmt').text(formatRs(custAmt));
        $('#calcPendingAmt').text('Pending from Company: ' + formatRs(pending));
    }

    $('#editOurPct, #editCustPct, #editSettledAmt').on('input change', function() {
        const id = $('#editPurchaseId').val();
        const inv = currentInvoices.find(item => item.id == id);
        if (inv) {
            // Auto-balance if user changes Our % and total comp % exists
            if ($(this).attr('id') === 'editOurPct') {
                const totalCompPct = parseFloat(inv.company_disc_percent) || 0;
                const ourPct = parseFloat($(this).val()) || 0;
                if (totalCompPct > 0) {
                    $('#editCustPct').val(Math.max(0, totalCompPct - ourPct).toFixed(2));
                }
            } else if ($(this).attr('id') === 'editCustPct') {
                const totalCompPct = parseFloat(inv.company_disc_percent) || 0;
                const custPct = parseFloat($(this).val()) || 0;
                if (totalCompPct > 0) {
                    $('#editOurPct').val(Math.max(0, totalCompPct - custPct).toFixed(2));
                }
            }
            recalcModalAmounts(inv.base_value, inv.company_disc_percent, inv.company_disc_amount);
        }
    });

    $('#btnSaveSplit').on('click', function() {
        const id = $('#editPurchaseId').val();
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: "{{ route('report.discount_reconciliation.update_split') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                purchase_id: id,
                our_disc_percent: $('#editOurPct').val(),
                customer_disc_percent: $('#editCustPct').val(),
                our_disc_notes: $('#editOurNotes').val(),
                company_settled_amount: $('#editSettledAmt').val(),
                settlement_status: $('#editSettlementStatus').val()
            },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Changes');
                if (res && res.ok) {
                    $('#modalEditSplit').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: res.msg,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadReportData();
                } else {
                    Swal.fire('Error', res.msg || 'Save failed', 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Changes');
                Swal.fire('Error', xhr.responseJSON?.message || 'Server error', 'error');
            }
        });
    });

    // Initial Load
    loadReportData();
});
</script>
@endpush
@endsection
