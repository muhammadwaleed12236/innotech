@extends('admin_panel.layout.app')

@section('content')
<style>
    .sap-report-container {
        padding: 14px 18px;
        background: #f8fafc;
        min-height: calc(100vh - 75px);
        font-family: inherit;
    }
    .filter-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 12px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        margin-bottom: 14px;
    }
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }
    .kpi-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 14px;
        position: relative;
        overflow: hidden;
    }
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }
    .kpi-blue::before { background: #2563eb; }
    .kpi-green::before { background: #10b981; }
    .kpi-purple::before { background: #8b5cf6; }
    .kpi-amber::before { background: #f59e0b; }
    .kpi-indigo::before { background: #4f46e5; }
    .kpi-rose::before { background: #f43f5e; }

    .kpi-card .k-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #64748b;
        margin-bottom: 3px;
    }
    .kpi-card .k-val {
        font-size: 1.2rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .kpi-card .k-sub {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 2px;
    }

    .table-container {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow-x: auto;
        max-height: 72vh;
    }
    .sap-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 11px;
    }
    .sap-table thead th {
        background: #0f172a;
        color: #f8fafc;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
        padding: 8px 10px;
        border-bottom: 1px solid #334155;
        border-right: 1px solid #334155;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .sap-table thead th.th-tax { background: #1e293b; color: #38bdf8; }
    .sap-table thead th.th-discount { background: #1e293b; color: #fbbf24; }
    .sap-table thead th.th-net { background: #14532d; color: #86efac; }

    .sap-table tbody td {
        padding: 6px 10px;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #f1f5f9;
        white-space: nowrap;
        vertical-align: middle;
        font-family: inherit;
    }
    .sap-table tbody tr:hover {
        background-color: #f1f5f9;
    }
    .sap-table tfoot td {
        background: #0f172a;
        color: #ffffff;
        font-weight: 800;
        font-size: 11.5px;
        border-top: 2px solid #0284c7;
        border-right: 1px solid #334155;
        padding: 9px 10px;
        white-space: nowrap;
        position: sticky;
        bottom: 0;
        z-index: 10;
    }
    .num-col {
        text-align: right;
        font-family: var(--bs-font-monospace, monospace);
    }
    .badge-soft-primary { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-weight: 600; padding: 2px 6px; border-radius: 4px; }
</style>

<div class="sap-report-container">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="fas fa-file-invoice text-primary me-2"></i> Principal Delivery &amp; Discount Statement
            </h4>
            <small class="text-muted">SAP Invoicing Format &bull; Complete Delivery &amp; Discount Head Matrix</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('report.discount_reconciliation') }}" class="btn btn-outline-primary btn-sm fw-bold">
                <i class="fas fa-balance-scale me-1"></i> Reconciliation &amp; Profit Report
            </a>
            <button type="button" class="btn btn-success btn-sm fw-bold" id="btnExportCsv">
                <i class="fas fa-file-excel me-1"></i> Export Excel (CSV)
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
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
                    <option value="">All Principals / Suppliers</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label fw-bold text-muted small mb-1">Keyword Search</label>
                <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Invoice #, Material, etc.">
            </div>
            <div class="col-md-1 col-12 d-flex gap-1">
                <button type="button" class="btn btn-primary btn-sm w-100 fw-bold" id="btnFilter" title="Apply Filter">
                    <i class="fas fa-filter"></i> Apply
                </button>
            </div>
        </form>

        <div class="d-flex flex-wrap align-items-center gap-1 mt-2 pt-2 border-top">
            <span class="text-muted small fw-bold me-1" style="font-size: 11px;"><i class="fas fa-bolt text-warning me-1"></i> Presets:</span>
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 btn-preset" data-preset="all" style="font-size: 11px;">All Time</button>
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 btn-preset" data-preset="this_month" style="font-size: 11px;">This Month</button>
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 btn-preset" data-preset="last_month" style="font-size: 11px;">Last Month</button>
            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 btn-preset" data-preset="ytd" style="font-size: 11px;">This Year (YTD)</button>
            <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2 ms-auto" id="btnResetFilter" style="font-size: 11px;">
                <i class="fas fa-undo me-1"></i> Reset Filters
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="metric-grid">
        <div class="kpi-card kpi-blue">
            <div class="k-title">Total Delivered Qty</div>
            <div class="k-val font-monospace text-primary" id="kpiDelQty">0.00</div>
            <div class="k-sub" id="kpiLinesCount">0 Lines Recorded</div>
        </div>

        <div class="kpi-card kpi-indigo">
            <div class="k-title">Del Gross Amount</div>
            <div class="k-val font-monospace text-indigo" id="kpiDelGross">Rs 0.00</div>
            <div class="k-sub">Total Trade Value</div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="k-title">Total Discounts Given</div>
            <div class="k-val font-monospace text-warning-emphasis" id="kpiTotalDiscounts">Rs 0.00</div>
            <div class="k-sub">Across All Discount Heads</div>
        </div>

        <div class="kpi-card kpi-purple">
            <div class="k-title">Total Sales Tax / GST</div>
            <div class="k-val font-monospace text-purple" style="color: #7c3aed;" id="kpiDelSalesTax">Rs 0.00</div>
            <div class="k-sub">Sales Tax on Retail / Trade</div>
        </div>

        <div class="kpi-card kpi-green">
            <div class="k-title">Del Net Amount</div>
            <div class="k-val font-monospace text-success" id="kpiDelNet">Rs 0.00</div>
            <div class="k-sub">Net Payable Amount</div>
        </div>
    </div>

    {{-- Full SAP Delivery Table --}}
    <div class="table-container">
        <table class="sap-table" id="statementTable">
            <thead>
                <tr>
                    <th style="width: 35px;" class="text-center">#</th>
                    <th>Invoice Date</th>
                    <th>Sold to party Customer</th>
                    <th>Sold to party Customer Code</th>
                    <th>Distributor Code (SAP)</th>
                    <th>Distributor Name</th>
                    <th>Ship to party Customer Code</th>
                    <th>Ship to party Customer</th>
                    <th>Delivery No</th>
                    <th>Invoice No. (SAP)</th>
                    <th>Order No</th>
                    <th>Distribution Channel Description</th>
                    <th>Business Unit Name</th>
                    <th>Material</th>
                    <th>Material Desc</th>
                    <th>Material Tax Type</th>
                    <th>Class</th>
                    <th class="num-col">Del Qty</th>
                    <th class="num-col">Del Gross Amount</th>
                    <th class="num-col th-discount">Del Norm Discount</th>
                    <th class="num-col th-tax">Del Sales Tax</th>
                    <th class="num-col th-tax">Del Sales Tax Un-Registered</th>
                    <th class="num-col th-discount">Del Discount on Return</th>
                    <th class="num-col th-discount">Del Display Discount</th>
                    <th class="num-col th-discount">Del Extra Discount</th>
                    <th class="num-col th-discount">Del Freight Discount</th>
                    <th class="num-col th-discount">Del Market Price Discount</th>
                    <th class="num-col th-discount">Del Trade Offer</th>
                    <th class="num-col th-tax">Del Income Tax</th>
                    <th class="num-col th-discount">Del Trade Activity Discount</th>
                    <th class="num-col th-discount">Del Direct Discount</th>
                    <th class="num-col th-discount">Del Distributor Comp</th>
                    <th class="num-col th-discount">Del DSF Income</th>
                    <th class="num-col th-discount">Del DSF Salary</th>
                    <th class="num-col th-discount">Del Freight Charges Claim</th>
                    <th class="num-col th-discount">Del JBP Claim</th>
                    <th class="num-col th-discount">Del Perf. Store Claims</th>
                    <th class="num-col th-discount">Del Price Adjustment Discount</th>
                    <th class="num-col th-discount">Del Progressive Rebate and ROI</th>
                    <th class="num-col th-discount">Del TCI MFG Claim</th>
                    <th class="num-col th-discount">Del VB Payout</th>
                    <th class="num-col th-net">Del Net Amount</th>
                    <th class="text-center" style="width: 60px;">Action</th>
                </tr>
            </thead>
            <tbody id="statementTableBody">
                <tr>
                    <td colspan="43" class="text-center py-4 text-muted">
                        <i class="fas fa-spinner fa-spin me-1"></i> Loading Principal Statement Data...
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="17" class="fw-bold text-end">Total:</td>
                    <td class="num-col text-white" id="footQty">0.00</td>
                    <td class="num-col text-info" id="footGross">0.00</td>
                    <td class="num-col text-warning" id="footNormDisc">0.00</td>
                    <td class="num-col text-info" id="footSalesTax">0.00</td>
                    <td class="num-col text-muted" id="footSalesTaxUnreg">0.00</td>
                    <td class="num-col text-warning" id="footDiscReturn">0.00</td>
                    <td class="num-col text-warning" id="footDisplayDisc">0.00</td>
                    <td class="num-col text-warning" id="footExtraDisc">0.00</td>
                    <td class="num-col text-warning" id="footFreightDisc">0.00</td>
                    <td class="num-col text-warning" id="footMarketPriceDisc">0.00</td>
                    <td class="num-col text-warning" id="footTradeOffer">0.00</td>
                    <td class="num-col text-info" id="footIncomeTax">0.00</td>
                    <td class="num-col text-warning" id="footTradeActivityDisc">0.00</td>
                    <td class="num-col text-warning" id="footDirectDisc">0.00</td>
                    <td class="num-col text-warning" id="footDistComp">0.00</td>
                    <td class="num-col text-warning" id="footDsfIncome">0.00</td>
                    <td class="num-col text-warning" id="footDsfSalary">0.00</td>
                    <td class="num-col text-warning" id="footFreightChargesClaim">0.00</td>
                    <td class="num-col text-warning" id="footJbpClaim">0.00</td>
                    <td class="num-col text-warning" id="footPerfStoreClaims">0.00</td>
                    <td class="num-col text-warning" id="footPriceAdjDisc">0.00</td>
                    <td class="num-col text-warning" id="footProgRebate">0.00</td>
                    <td class="num-col text-warning" id="footTciMfgClaim">0.00</td>
                    <td class="num-col text-warning" id="footVbPayout">0.00</td>
                    <td class="num-col text-success fs-6" id="footNet">0.00</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

@push('scripts')
<script>
$(document).ready(function() {
    let currentRows = [];
    let currentTotals = {};

    function formatNum(num) {
        num = parseFloat(num) || 0;
        return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatRs(num) {
        num = parseFloat(num) || 0;
        return 'Rs ' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Month picker change
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
            loadData();
        }
    });

    // Presets
    $('.btn-preset').on('click', function() {
        const preset = $(this).data('preset');
        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth();

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

        loadData();
    });

    $('#btnResetFilter').on('click', function() {
        $('#monthPicker').val('');
        $('#dateFrom').val('');
        $('#dateTo').val('');
        $('#vendorSelect').val('');
        $('#searchInput').val('');
        loadData();
    });

    $('#btnFilter').on('click', function() {
        loadData();
    });

    $('#searchInput').on('input', function() {
        renderFilteredRows();
    });

    $('#btnExportCsv').on('click', function() {
        const params = $.param({
            date_from: $('#dateFrom').val(),
            date_to: $('#dateTo').val(),
            vendor_id: $('#vendorSelect').val()
        });
        window.location.href = "{{ route('report.principal_delivery_statement.export') }}?" + params;
    });

    function loadData() {
        $('#statementTableBody').html(`
            <tr>
                <td colspan="43" class="text-center py-4 text-muted">
                    <i class="fas fa-spinner fa-spin me-1"></i> Fetching Principal Statement &amp; Discount Breakdown Data...
                </td>
            </tr>
        `);

        $.ajax({
            url: "{{ route('report.principal_delivery_statement.fetch') }}",
            type: "GET",
            data: {
                date_from: $('#dateFrom').val(),
                date_to: $('#dateTo').val(),
                vendor_id: $('#vendorSelect').val()
            },
            success: function(res) {
                if (res && res.ok) {
                    currentRows = res.rows || [];
                    currentTotals = res.totals || {};
                    renderKPIs(currentTotals, currentRows.length);
                    renderFilteredRows();
                } else {
                    $('#statementTableBody').html('<tr><td colspan="43" class="text-center py-3 text-danger">Failed to load statement data</td></tr>');
                }
            },
            error: function() {
                $('#statementTableBody').html('<tr><td colspan="43" class="text-center py-3 text-danger">Error communicating with server</td></tr>');
            }
        });
    }

    function renderKPIs(t, count) {
        $('#kpiDelQty').text(formatNum(t.del_qty));
        $('#kpiLinesCount').text(count + ' Lines Recorded');
        $('#kpiDelGross').text(formatRs(t.del_gross_amount));

        const totalDiscounts = (t.del_norm_discount || 0) + (t.del_disc_on_return || 0) + (t.del_display_discount || 0) + (t.del_extra_discount || 0) + (t.del_freight_discount || 0) + (t.del_market_price_disc || 0) + (t.del_trade_offer || 0) + (t.del_trade_activity_disc || 0) + (t.del_direct_discount || 0) + (t.del_distributor_comp || 0) + (t.del_dsf_income || 0) + (t.del_dsf_salary || 0) + (t.del_freight_charges_claim || 0) + (t.del_jbp_claim || 0) + (t.del_perf_store_claims || 0) + (t.del_price_adj_discount || 0) + (t.del_progressive_rebate || 0) + (t.del_tci_mfg_claim || 0) + (t.del_vb_payout || 0);

        const totalTaxes = (t.del_sales_tax || 0) + (t.del_sales_tax_unreg || 0) + (t.del_income_tax || 0);

        $('#kpiTotalDiscounts').text(formatRs(totalDiscounts));
        $('#kpiDelSalesTax').text(formatRs(totalTaxes));
        $('#kpiDelNet').text(formatRs(t.del_net_amount));
    }

    function renderFilteredRows() {
        const search = ($('#searchInput').val() || '').toLowerCase().trim();
        let rows = currentRows;

        if (search) {
            rows = currentRows.filter(r => {
                const combined = `${r.invoice_no} ${r.order_no} ${r.delivery_no} ${r.sold_to_customer} ${r.material_code} ${r.material_desc} ${r.class_name}`.toLowerCase();
                return combined.includes(search);
            });
        }

        if (!rows || rows.length === 0) {
            $('#statementTableBody').html('<tr><td colspan="43" class="text-center py-4 text-muted">No records match the filter criteria.</td></tr>');
            updateFooter({});
            return;
        }

        let html = '';
        let sums = {
            del_qty: 0,
            del_gross_amount: 0,
            del_norm_discount: 0,
            del_sales_tax: 0,
            del_sales_tax_unreg: 0,
            del_disc_on_return: 0,
            del_display_discount: 0,
            del_extra_discount: 0,
            del_freight_discount: 0,
            del_market_price_disc: 0,
            del_trade_offer: 0,
            del_income_tax: 0,
            del_trade_activity_disc: 0,
            del_direct_discount: 0,
            del_distributor_comp: 0,
            del_dsf_income: 0,
            del_dsf_salary: 0,
            del_freight_charges_claim: 0,
            del_jbp_claim: 0,
            del_perf_store_claims: 0,
            del_price_adj_discount: 0,
            del_progressive_rebate: 0,
            del_tci_mfg_claim: 0,
            del_vb_payout: 0,
            del_net_amount: 0
        };

        rows.forEach((r, idx) => {
            sums.del_qty += r.del_qty;
            sums.del_gross_amount += r.del_gross_amount;
            sums.del_norm_discount += r.del_norm_discount;
            sums.del_sales_tax += r.del_sales_tax;
            sums.del_sales_tax_unreg += r.del_sales_tax_unreg;
            sums.del_disc_on_return += r.del_disc_on_return;
            sums.del_display_discount += r.del_display_discount;
            sums.del_extra_discount += r.del_extra_discount;
            sums.del_freight_discount += r.del_freight_discount;
            sums.del_market_price_disc += r.del_market_price_disc;
            sums.del_trade_offer += r.del_trade_offer;
            sums.del_income_tax += r.del_income_tax;
            sums.del_trade_activity_disc += r.del_trade_activity_disc;
            sums.del_direct_discount += r.del_direct_discount;
            sums.del_distributor_comp += r.del_distributor_comp;
            sums.del_dsf_income += r.del_dsf_income;
            sums.del_dsf_salary += r.del_dsf_salary;
            sums.del_freight_charges_claim += r.del_freight_charges_claim;
            sums.del_jbp_claim += r.del_jbp_claim;
            sums.del_perf_store_claims += r.del_perf_store_claims;
            sums.del_price_adj_discount += r.del_price_adj_discount;
            sums.del_progressive_rebate += r.del_progressive_rebate;
            sums.del_tci_mfg_claim += r.del_tci_mfg_claim;
            sums.del_vb_payout += r.del_vb_payout;
            sums.del_net_amount += r.del_net_amount;

            html += `
                <tr>
                    <td class="text-center font-monospace text-muted">${idx + 1}</td>
                    <td class="font-monospace text-muted">${r.invoice_date}</td>
                    <td class="fw-semibold text-dark">${r.sold_to_customer}</td>
                    <td class="font-monospace text-muted">${r.sold_to_code}</td>
                    <td class="font-monospace text-muted">${r.distributor_code}</td>
                    <td>${r.distributor_name}</td>
                    <td class="font-monospace text-muted">${r.ship_to_code}</td>
                    <td>${r.ship_to_customer}</td>
                    <td class="font-monospace">${r.delivery_no}</td>
                    <td class="font-monospace fw-bold text-dark">${r.invoice_no}</td>
                    <td class="font-monospace text-muted">${r.order_no}</td>
                    <td>${r.dist_channel}</td>
                    <td>${r.business_unit}</td>
                    <td class="font-monospace fw-bold text-primary">${r.material_code}</td>
                    <td class="fw-semibold text-dark">${r.material_desc}</td>
                    <td><span class="badge-soft-primary">${r.tax_type}</span></td>
                    <td>${r.class_name}</td>
                    <td class="num-col fw-bold">${formatNum(r.del_qty)}</td>
                    <td class="num-col fw-bold text-dark">${formatNum(r.del_gross_amount)}</td>
                    <td class="num-col ${r.del_norm_discount > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_norm_discount)}</td>
                    <td class="num-col ${r.del_sales_tax > 0 ? 'text-info fw-bold' : 'text-muted'}">${formatNum(r.del_sales_tax)}</td>
                    <td class="num-col ${r.del_sales_tax_unreg > 0 ? 'text-muted fw-bold' : 'text-muted'}">${formatNum(r.del_sales_tax_unreg)}</td>
                    <td class="num-col ${r.del_disc_on_return > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_disc_on_return)}</td>
                    <td class="num-col ${r.del_display_discount > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_display_discount)}</td>
                    <td class="num-col ${r.del_extra_discount > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_extra_discount)}</td>
                    <td class="num-col ${r.del_freight_discount > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_freight_discount)}</td>
                    <td class="num-col ${r.del_market_price_disc > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_market_price_disc)}</td>
                    <td class="num-col ${r.del_trade_offer > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_trade_offer)}</td>
                    <td class="num-col ${r.del_income_tax > 0 ? 'text-info fw-bold' : 'text-muted'}">${formatNum(r.del_income_tax)}</td>
                    <td class="num-col ${r.del_trade_activity_disc > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_trade_activity_disc)}</td>
                    <td class="num-col ${r.del_direct_discount > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_direct_discount)}</td>
                    <td class="num-col ${r.del_distributor_comp > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_distributor_comp)}</td>
                    <td class="num-col ${r.del_dsf_income > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_dsf_income)}</td>
                    <td class="num-col ${r.del_dsf_salary > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_dsf_salary)}</td>
                    <td class="num-col ${r.del_freight_charges_claim > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_freight_charges_claim)}</td>
                    <td class="num-col ${r.del_jbp_claim > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_jbp_claim)}</td>
                    <td class="num-col ${r.del_perf_store_claims > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_perf_store_claims)}</td>
                    <td class="num-col ${r.del_price_adj_discount > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_price_adj_discount)}</td>
                    <td class="num-col ${r.del_progressive_rebate > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_progressive_rebate)}</td>
                    <td class="num-col ${r.del_tci_mfg_claim > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_tci_mfg_claim)}</td>
                    <td class="num-col ${r.del_vb_payout > 0 ? 'text-warning-emphasis fw-bold' : 'text-muted'}">${formatNum(r.del_vb_payout)}</td>
                    <td class="num-col fw-bold text-success">${formatNum(r.del_net_amount)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 btn-open-item-split-recon" data-item-id="${r.item_id}" data-inv-id="${r.purchase_id}" title="Split / Edit Discounts" style="font-size: 10px;">
                            <i class="fas fa-sliders-h"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#statementTableBody').html(html);
        updateFooter(sums);
    }

    function updateFooter(s) {
        $('#footQty').text(formatNum(s.del_qty || 0));
        $('#footGross').text(formatNum(s.del_gross_amount || 0));
        $('#footNormDisc').text(formatNum(s.del_norm_discount || 0));
        $('#footSalesTax').text(formatNum(s.del_sales_tax || 0));
        $('#footSalesTaxUnreg').text(formatNum(s.del_sales_tax_unreg || 0));
        $('#footDiscReturn').text(formatNum(s.del_disc_on_return || 0));
        $('#footDisplayDisc').text(formatNum(s.del_display_discount || 0));
        $('#footExtraDisc').text(formatNum(s.del_extra_discount || 0));
        $('#footFreightDisc').text(formatNum(s.del_freight_discount || 0));
        $('#footMarketPriceDisc').text(formatNum(s.del_market_price_disc || 0));
        $('#footTradeOffer').text(formatNum(s.del_trade_offer || 0));
        $('#footIncomeTax').text(formatNum(s.del_income_tax || 0));
        $('#footTradeActivityDisc').text(formatNum(s.del_trade_activity_disc || 0));
        $('#footDirectDisc').text(formatNum(s.del_direct_discount || 0));
        $('#footDistComp').text(formatNum(s.del_distributor_comp || 0));
        $('#footDsfIncome').text(formatNum(s.del_dsf_income || 0));
        $('#footDsfSalary').text(formatNum(s.del_dsf_salary || 0));
        $('#footFreightChargesClaim').text(formatNum(s.del_freight_charges_claim || 0));
        $('#footJbpClaim').text(formatNum(s.del_jbp_claim || 0));
        $('#footPerfStoreClaims').text(formatNum(s.del_perf_store_claims || 0));
        $('#footPriceAdjDisc').text(formatNum(s.del_price_adj_discount || 0));
        $('#footProgRebate').text(formatNum(s.del_progressive_rebate || 0));
        $('#footTciMfgClaim').text(formatNum(s.del_tci_mfg_claim || 0));
        $('#footVbPayout').text(formatNum(s.del_vb_payout || 0));
        $('#footNet').text(formatNum(s.del_net_amount || 0));
    }

    // Item Split Modal click
    $(document).on('click', '.btn-open-item-split-recon', function() {
        const itemId = $(this).data('item-id');
        const invId = $(this).data('inv-id');
        
        // Fetch purchase item details and open modal
        $.ajax({
            url: "{{ route('report.discount_reconciliation.fetch') }}",
            type: "GET",
            data: { date_from: $('#dateFrom').val(), date_to: $('#dateTo').val() },
            success: function(res) {
                if (res && res.invoices) {
                    const inv = res.invoices.find(i => i.id == invId);
                    if (inv) {
                        const item = (inv.items || []).find(it => it.id == itemId);
                        if (item && typeof window.openItemSplitModalForRecon === 'function') {
                            window.openItemSplitModalForRecon(item, inv);
                        }
                    }
                }
            }
        });
    });

    // Listen to modal save to reload data
    $(document).on('itemDiscountSplitSaved', function() {
        loadData();
    });

    // Initial load
    loadData();
});
</script>
@include('admin_panel.purchase.item_discount_split_modal')
@endpush
@endsection
