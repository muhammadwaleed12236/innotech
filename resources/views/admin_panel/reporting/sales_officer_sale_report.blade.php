@extends('admin_panel.layout.app')

@section('content')
<style>
    /* Executive Sales Officer Wise Sale Report */
    .so-report-container {
        padding: 12px 16px;
        background: #f8fafc;
        min-height: calc(100vh - 75px);
    }
    .so-filter-label {
        margin-right: 6px !important;
        margin-bottom: 0 !important;
        white-space: nowrap;
        font-weight: 700;
        font-size: .78rem;
        color: #475569;
    }
    .summary-pill-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        overflow-x: auto;
        white-space: nowrap;
    }
    .stat-pill {
        flex: 1 1 0px;
        padding: 8px 12px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .stat-pill .stat-label {
        font-size: .62rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .stat-pill .stat-val {
        font-size: .95rem;
        font-weight: 800;
        line-height: 1.2;
    }
    .report-table-wrap {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        overflow-x: auto;
    }
    .report-table {
        font-size: .78rem;
        margin-bottom: 0;
    }
    .report-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #1e293b !important;
        color: #ffffff !important;
        font-size: .75rem;
        font-weight: 700;
        padding: 9px 10px;
        border-bottom: 2px solid #334155;
        white-space: nowrap;
    }
    .officer-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
        transition: all 0.2s;
    }
    .officer-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
    }
    @media print {
        .no-print, .main-header, .sidebar, .footer { display: none !important; }
        .so-report-container { padding: 0 !important; background: #fff !important; }
        .report-table-wrap { border: none !important; }
    }
</style>

<div class="so-report-container">
    {{-- Header & Filters --}}
    <div class="card border-0 shadow-sm rounded-3 mb-3 p-3 bg-white no-print">
        <form id="soFilterForm" onsubmit="return false;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-primary rounded-3 text-white">
                        <i class="fas fa-user-tie fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Sales Officer Wise Sale Report</h5>
                        <small class="text-muted">Track sales performance, orders, discounts & recovery by sales officer</small>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="btnFilter" class="btn btn-primary btn-sm fw-bold px-3">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <button type="button" id="btnReset" class="btn btn-outline-secondary btn-sm fw-bold px-2">
                        <i class="fas fa-redo-alt"></i> Reset
                    </button>
                    <button type="button" onclick="window.print()" class="btn btn-outline-dark btn-sm fw-bold px-3">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>

            <hr class="my-2">

            {{-- Filter Row --}}
            <div class="row g-2 align-items-end mt-1">
                {{-- Location Hierarchy Filters --}}
                <div class="col-md-4 col-sm-6">
                    <label class="so-filter-label">Province:</label>
                    <select id="so_province" name="province" class="form-select form-select-sm">
                        <option value="">All Provinces</option>
                        @foreach(app(\App\Services\LocationService::class)->getProvinces() as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="so-filter-label">City:</label>
                    <select id="so_city" name="city" class="form-select form-select-sm">
                        <option value="">All Cities</option>
                        @foreach(app(\App\Services\LocationService::class)->getCities() as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="so-filter-label">Area / Sector:</label>
                    <select id="so_area" name="area" class="form-select form-select-sm">
                        <option value="">All Areas</option>
                        @foreach(app(\App\Services\LocationService::class)->getAreas() as $a)
                            <option value="{{ $a }}">{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Date Range --}}
                <div class="col-md-3 col-sm-6">
                    <label class="so-filter-label">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" value="{{ date('Y-m-01') }}" class="form-control form-control-sm fw-bold">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="so-filter-label">End Date:</label>
                    <input type="date" id="end_date" name="end_date" value="{{ date('Y-m-d') }}" class="form-control form-control-sm fw-bold">
                </div>

                {{-- Sales Officer Dropdown --}}
                <div class="col-md-3 col-sm-6">
                    <label class="so-filter-label">Sales Officer:</label>
                    <select id="sales_officer_id" name="sales_officer_id" class="form-select form-select-sm select2-officer">
                        <option value="all">-- All Sales Officers --</option>
                        @foreach($salesOfficers as $officer)
                            <option value="{{ $officer->id }}">{{ $officer->name }} {{ $officer->mobile ? '(' . $officer->mobile . ')' : '' }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Customer Filter --}}
                <div class="col-md-3 col-sm-6">
                    <label class="so-filter-label">Customer (Optional):</label>
                    <select id="customer_id" name="customer_id" class="form-select form-select-sm select2-customer">
                        <option value="all">-- All Customers --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" data-province="{{ $c->province }}" data-city="{{ $c->city }}" data-area="{{ $c->area }}">{{ $c->customer_id ? $c->customer_id . ' — ' : '' }}{{ $c->customer_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    {{-- Horizontal Metrics Summary Bar --}}
    <div class="summary-pill-bar mb-3">
        <div class="stat-pill">
            <div class="stat-label text-secondary">Total Invoices</div>
            <div class="stat-val text-dark" id="statInvoices">0</div>
        </div>
        <div class="stat-pill">
            <div class="stat-label text-secondary">Gross Revenue</div>
            <div class="stat-val text-primary" id="statGross">Rs 0</div>
        </div>
        <div class="stat-pill">
            <div class="stat-label text-secondary">Total Discounts</div>
            <div class="stat-val text-danger" id="statDiscounts">Rs 0</div>
        </div>
        <div class="stat-pill">
            <div class="stat-label text-secondary">Net Realized Sale</div>
            <div class="stat-val text-success" id="statNet">Rs 0</div>
        </div>
        <div class="stat-pill">
            <div class="stat-label text-secondary">Sales Returns</div>
            <div class="stat-val text-danger" id="statReturns">Rs 0</div>
        </div>
    </div>

    {{-- Tabs: Summary Breakdown by Officer VS Detailed Sale Log --}}
    <ul class="nav nav-pills mb-3 no-print" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold py-1 px-3 btn-sm" id="pills-summary-tab" data-bs-toggle="pill" data-bs-target="#pills-summary" type="button" role="tab">
                <i class="fas fa-users-cog me-1"></i> Officer Summary Breakdown
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold py-1 px-3 btn-sm" id="pills-detailed-tab" data-bs-toggle="pill" data-bs-target="#pills-detailed" type="button" role="tab">
                <i class="fas fa-list-alt me-1"></i> Detailed Sales Log
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        {{-- TAB 1: Officer Summary Table & Cards --}}
        <div class="tab-pane fade show active" id="pills-summary" role="tabpanel">
            <div class="report-table-wrap mb-4">
                <table class="table table-hover report-table align-middle" id="tableOfficerSummary">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Sales Officer Name</th>
                            <th class="text-center">Invoices</th>
                            <th class="text-center">Total Qty</th>
                            <th class="text-end">Gross Sale</th>
                            <th class="text-end">Discounts</th>
                            <th class="text-end">Net Sale</th>
                            <th class="text-end">Returns</th>
                            <th class="text-center">Contribution</th>
                        </tr>
                    </thead>
                    <tbody id="summaryBody">
                        <tr><td colspan="9" class="text-center py-4 text-muted">Loading officer summary...</td></tr>
                    </tbody>
                    <tfoot class="bg-light fw-bold" id="summaryFoot"></tfoot>
                </table>
            </div>
        </div>

        {{-- TAB 2: Detailed Sales Transactions --}}
        <div class="tab-pane fade" id="pills-detailed" role="tabpanel">
            <div class="report-table-wrap">
                <table class="table table-hover report-table align-middle" id="tableDetailedSales">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th>Date</th>
                            <th>Invoice #</th>
                            <th>Sales Officer</th>
                            <th>Customer</th>
                            <th>PO # (Mail)</th>
                            <th>Products</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Gross</th>
                            <th class="text-end">Disc</th>
                            <th class="text-end">Net Total</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="detailedBody">
                        <tr><td colspan="12" class="text-center py-4 text-muted">Click Filter to load data...</td></tr>
                    </tbody>
                    <tfoot class="bg-light fw-bold" id="detailedFoot"></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        if ($('.select2-officer').length > 0) $('.select2-officer').select2({ width: '100%' });
        if ($('.select2-customer').length > 0) $('.select2-customer').select2({ width: '100%' });

        function loadReport() {
            $('#summaryBody').html('<tr><td colspan="9" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Calculating officer metrics...</td></tr>');
            $('#detailedBody').html('<tr><td colspan="12" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Fetching sales log...</td></tr>');

            $.ajax({
                url: '{{ route('report.sales_officer_sale.fetch') }}',
                method: 'GET',
                data: $('#soFilterForm').serialize(),
                success: function(res) {
                    const sales = res.sales || [];
                    const summary = res.summary || [];
                    const totals = res.totals || {};

                    // 1. Update Metrics
                    $('#statInvoices').text(totals.total_sales || 0);
                    $('#statGross').text('Rs ' + Number(totals.total_gross || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#statDiscounts').text('Rs ' + Number(totals.total_discount || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#statNet').text('Rs ' + Number(totals.total_net || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#statReturns').text('Rs ' + Number(totals.total_returns || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

                    // 2. Render Officer Summary
                    let sumHtml = '';
                    let totalGrossSum = totals.total_gross || 0;

                    if (summary.length === 0) {
                        sumHtml = '<tr><td colspan="9" class="text-center py-4 text-muted">No sales found for the selected criteria.</td></tr>';
                    } else {
                        summary.forEach((item, idx) => {
                            let sharePct = totalGrossSum > 0 ? ((item.total_gross / totalGrossSum) * 100).toFixed(1) : 0;
                            sumHtml += `
                                <tr>
                                    <td class="text-center fw-bold text-muted">${idx + 1}</td>
                                    <td>
                                        <div class="fw-bold text-dark"><i class="fas fa-user-tie text-primary me-2"></i>${item.officer_name}</div>
                                    </td>
                                    <td class="text-center font-monospace">${item.total_invoices}</td>
                                    <td class="text-center font-monospace">${item.total_qty}</td>
                                    <td class="text-end font-monospace">Rs ${Number(item.total_gross).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="text-end font-monospace text-danger">Rs ${Number(item.total_discount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="text-end font-monospace fw-bold text-success">Rs ${Number(item.total_net).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="text-end font-monospace text-danger">Rs ${Number(item.total_returns).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <span class="badge bg-primary text-white" style="font-size:10px;">${sharePct}%</span>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                    }
                    $('#summaryBody').html(sumHtml);

                    // 3. Render Detailed Table
                    let detHtml = '';
                    if (sales.length === 0) {
                        detHtml = '<tr><td colspan="12" class="text-center py-4 text-muted">No transactions recorded.</td></tr>';
                    } else {
                        sales.forEach((s, idx) => {
                            let poText = s.po_no ? `<span class="badge bg-light text-dark border">${s.po_no}</span>` : '—';
                            if (s.po_date) poText += `<br><small class="text-muted" style="font-size:9.5px;">${s.po_date}</small>`;

                            let statusBadge = '<span class="badge bg-success">Posted</span>';
                            if (s.status === 'booked') statusBadge = '<span class="badge bg-info text-white">Booked</span>';
                            else if (s.status === 'quotation') statusBadge = '<span class="badge bg-primary">Quotation</span>';
                            else if (s.status === 'returned') statusBadge = '<span class="badge bg-danger">Returned</span>';

                            detHtml += `
                                <tr>
                                    <td class="text-center text-muted fw-bold">${idx + 1}</td>
                                    <td class="font-monospace" style="font-size:11px;">${s.created_at_formatted}</td>
                                    <td>
                                        <a href="/sales/${s.id}/invoice" target="_blank" class="fw-bold text-primary font-monospace">${s.invoice_no}</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><i class="fas fa-user-tie text-primary me-1"></i>${s.sales_officer_name}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">${s.customer_name}</div>
                                        ${s.customer_code ? `<small class="text-muted">(${s.customer_code})</small>` : ''}
                                    </td>
                                    <td>${poText}</td>
                                    <td style="max-width: 240px; font-size:11px;" class="text-truncate" title="${s.products}">${s.products}</td>
                                    <td class="text-center font-monospace">${s.total_qty}</td>
                                    <td class="text-end font-monospace">Rs ${Number(s.gross_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="text-end font-monospace text-danger">Rs ${Number(s.discount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="text-end font-monospace fw-bold text-success">Rs ${Number(s.net_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="text-center">${statusBadge}</td>
                                </tr>
                            `;
                        });
                    }
                    $('#detailedBody').html(detHtml);
                },
                error: function() {
                    $('#summaryBody').html('<tr><td colspan="9" class="text-center py-4 text-danger">Failed to fetch report data.</td></tr>');
                    $('#detailedBody').html('<tr><td colspan="12" class="text-center py-4 text-danger">Failed to fetch report data.</td></tr>');
                }
            });
        }

        // Trigger load
        loadReport();

        $('#btnFilter').on('click', function(e) {
            e.preventDefault();
            loadReport();
        });

        $('#btnReset').on('click', function(e) {
            e.preventDefault();
            $('#start_date').val('{{ date('Y-m-01') }}');
            $('#end_date').val('{{ date('Y-m-d') }}');
            $('#sales_officer_id').val('all').trigger('change');
            $('#customer_id').val('all').trigger('change');
            $('#so_province, #so_city, #so_area').val('');
            loadReport();
        });
    });
</script>
@include('admin_panel.partials.location_selector_script')
<script>
    $(document).ready(function() {
        setupLocationCascade({
            province: '#so_province',
            city: '#so_city',
            area: '#so_area',
            customer: '#customer_id'
        });
    });
</script>
@endsection
