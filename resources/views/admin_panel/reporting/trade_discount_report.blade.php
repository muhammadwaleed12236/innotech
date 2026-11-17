@extends('admin_panel.layout.app')

@section('content')
<style>
    .trade-discount-container {
        padding: 16px 20px;
        background: #f8fafc;
        min-height: calc(100vh - 75px);
        font-family: inherit;
    }

    /* Top Action Bar */
    .report-top-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        position: relative;
    }

    .btn-toggle-filters {
        background: #ffffff;
        border: 1.5px solid #94a3b8;
        border-radius: 20px;
        padding: 5px 14px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .5px;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-toggle-filters:hover {
        background: #f1f5f9;
        border-color: #64748b;
        color: #1e293b;
    }
    .btn-toggle-filters .toggle-dot {
        width: 10px;
        height: 10px;
        border: 2px solid #64748b;
        border-radius: 50%;
        display: inline-block;
    }
    .btn-toggle-filters.active .toggle-dot {
        background: #0284c7;
        border-color: #0284c7;
    }

    .report-title-main {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0;
        letter-spacing: -0.3px;
        text-align: center;
    }

    .print-date-line {
        font-size: 11.5px;
        color: #334155;
        font-weight: 600;
        padding-bottom: 8px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 14px;
    }

    /* Collapsible Filter Panel */
    .filter-panel {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: all 0.25s ease;
    }

    /* KPI Summary Cards */
    .kpi-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }
    .kpi-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .kpi-stat-card .lbl {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: .4px;
    }
    .kpi-stat-card .val {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        margin-top: 2px;
    }

    /* Main Data Table */
    .report-table-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        overflow-x: auto;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    .trade-discount-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
        font-size: 11.5px;
    }

    /* Vibrant Blue Table Header matching screenshot */
    .trade-discount-table thead tr:first-child th {
        background: #0284c7 !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 11.5px;
        padding: 8px 10px;
        border: 1px solid #0369a1;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .trade-discount-table thead tr:nth-child(2) th {
        background: #0284c7 !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 11px;
        padding: 6px 8px;
        border: 1px solid #0369a1;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    /* Table Body */
    .trade-discount-table tbody td {
        padding: 7px 10px;
        border: 1px solid #e2e8f0;
        font-size: 11.5px;
        color: #1e293b;
        white-space: nowrap;
        vertical-align: middle;
    }

    .trade-discount-table tbody tr:nth-child(even) {
        background-color: #f8fafc;
    }

    .trade-discount-table tbody tr:hover {
        background-color: #f1f5f9;
    }

    /* Numbers alignment */
    .col-num {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }
    .col-center {
        text-align: center;
    }
    .col-text {
        text-align: left;
    }

    /* Table Footer Total Row matching screenshot */
    .trade-discount-table tfoot tr td {
        background: #e2e8f0 !important;
        color: #0f172a !important;
        font-weight: 800;
        font-size: 11.5px;
        padding: 9px 10px;
        border: 1px solid #cbd5e1;
        white-space: nowrap;
        vertical-align: middle;
    }

    /* Print Styles */
    @media print {
        .no-print, .main-header, .sidebar, .footer, .btn, .filter-panel, .kpi-row {
            display: none !important;
        }
        body, .trade-discount-container {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .report-top-bar {
            margin-bottom: 6px !important;
            justify-content: center !important;
        }
        .report-title-main {
            font-size: 16pt !important;
        }
        .print-date-line {
            display: block !important;
            font-size: 9pt !important;
            margin-bottom: 8px !important;
        }
        .report-table-card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
            overflow: visible !important;
        }
        .trade-discount-table thead tr th {
            background: #0284c7 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            color: #ffffff !important;
            font-size: 8.5pt !important;
            padding: 4px 6px !important;
        }
        .trade-discount-table tbody td {
            font-size: 8.5pt !important;
            padding: 4px 6px !important;
            border: 1px solid #94a3b8 !important;
        }
        .trade-discount-table tfoot tr td {
            background: #e2e8f0 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-size: 8.5pt !important;
            padding: 4px 6px !important;
            border: 1px solid #64748b !important;
        }
    }
</style>

<div class="trade-discount-container">

    {{-- Top Action Bar --}}
    <div class="report-top-bar">
        <div>
            <button type="button" class="btn-toggle-filters active no-print" id="btnToggleFilters" onclick="toggleFilterPanel()">
                <span class="toggle-dot"></span>
                <span>SHOW FILTERS</span>
            </button>
        </div>

        <div class="text-center">
            <h4 class="report-title-main">Trade Discount Report</h4>
        </div>

        <div class="d-flex align-items-center gap-2 no-print">
            <button type="button" class="btn btn-outline-success btn-sm fw-bold px-3" id="btnExportCsv" onclick="exportCsvReport()">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm fw-bold px-3" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    {{-- Print Date Line (Matching Screenshot) --}}
    <div class="print-date-line">
        <span>Print Date &nbsp; <span id="lblPrintDate">{{ date('d F , Y h:i a') }}</span></span>
    </div>

    {{-- Filter Panel (Collapsible) --}}
    <div class="filter-panel no-print" id="filterPanel">
        <form id="tradeDiscountFilterForm" onsubmit="event.preventDefault(); loadReportData();">
            <div class="row g-2 align-items-end">
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-bold text-muted mb-1">From Date</label>
                    <input type="date" id="filterFromDate" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-bold text-muted mb-1">To Date</label>
                    <input type="date" id="filterToDate" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold text-muted mb-1">Order Booker (Sales Officer)</label>
                    <select id="filterOfficer" name="sales_officer_id" class="form-select form-select-sm">
                        <option value="all">All Order Bookers</option>
                        @foreach($salesOfficers as $officer)
                            <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold text-muted mb-1">Customer</label>
                    <select id="filterCustomer" name="customer_id" class="form-select form-select-sm">
                        <option value="all">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-bold text-muted mb-1">Warehouse</label>
                    <select id="filterWarehouse" name="warehouse_id" class="form-select form-select-sm">
                        <option value="all">All Warehouses</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->warehouse_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary" onclick="setDatePreset('today')">Today</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setDatePreset('this_week')">This Week</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setDatePreset('this_month')">This Month</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="setDatePreset('all_time')">All Time</button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light btn-sm fw-bold text-muted px-3" onclick="resetFilters()">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-4">
                        <i class="fas fa-filter me-1"></i> Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI Stat Bar (Visible on screen) --}}
    <div class="kpi-row no-print" id="kpiBar">
        <div class="kpi-stat-card">
            <div class="lbl">Gross Exc GST</div>
            <div class="val text-primary" id="kpiGrossExc">0.00</div>
        </div>
        <div class="kpi-stat-card">
            <div class="lbl">Gross Inc GST</div>
            <div class="val text-info" id="kpiGrossInc">0.00</div>
        </div>
        <div class="kpi-stat-card">
            <div class="lbl">Net Amount</div>
            <div class="val text-success" id="kpiNetAmount">0.00</div>
        </div>
        <div class="kpi-stat-card">
            <div class="lbl">Regular Disc</div>
            <div class="val text-danger" id="kpiRegDisc">0.00</div>
        </div>
        <div class="kpi-stat-card">
            <div class="lbl">Special Disc</div>
            <div class="val text-danger" id="kpiSpDisc">0.00</div>
        </div>
        <div class="kpi-stat-card">
            <div class="lbl">Trade Offer (T.O)</div>
            <div class="val text-warning" id="kpiToVal">0.00</div>
        </div>
        <div class="kpi-stat-card">
            <div class="lbl">Trade Activity</div>
            <div class="val text-dark" id="kpiTradeAct">0.00</div>
        </div>
    </div>

    {{-- Main Report Table --}}
    <div class="report-table-card">
        <div id="tableLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted small mt-2 mb-0">Calculating Trade Discount Report...</p>
        </div>

        <table class="table trade-discount-table" id="reportTable" style="display: none;">
            <thead>
                {{-- Group Header Row --}}
                <tr>
                    <th rowspan="2" class="col-text" style="width: 120px;">Order Booker Code</th>
                    <th rowspan="2" class="col-text" style="width: 160px;">Order Booker Name</th>
                    <th rowspan="2" class="col-num" style="width: 110px;">Gross Sales Exc GST</th>
                    <th rowspan="2" class="col-num" style="width: 110px;">Gross Sales Inc GST</th>
                    <th rowspan="2" class="col-num" style="width: 110px;">Net Amount</th>
                    <th colspan="6" class="col-center">Discount</th>
                    <th colspan="5" class="col-center">To</th>
                    <th colspan="1" class="col-center">Trade Activity</th>
                </tr>
                {{-- Sub Header Row --}}
                <tr>
                    {{-- Discount Sub-columns --}}
                    <th class="col-num" style="width: 85px;">Regular</th>
                    <th class="col-center" style="width: 60px;">%</th>
                    <th class="col-num" style="width: 85px;">Special</th>
                    <th class="col-center" style="width: 60px;">%</th>
                    <th class="col-num" style="width: 90px;">Total</th>
                    <th class="col-center" style="width: 60px;">%</th>
                    {{-- To (Trade Offer) Sub-columns --}}
                    <th class="col-center" style="width: 50px;">Unit</th>
                    <th class="col-num" style="width: 80px;">Unit Value</th>
                    <th class="col-num" style="width: 90px;">In Value</th>
                    <th class="col-num" style="width: 95px;">Total</th>
                    <th class="col-center" style="width: 70px;">Total T.O %</th>
                    {{-- Trade Activity Sub-columns --}}
                    <th class="col-num" style="width: 95px;">Value</th>
                </tr>
            </thead>
            <tbody id="reportTableBody">
                {{-- Populated dynamically via AJAX --}}
            </tbody>
            <tfoot id="reportTableFoot">
                {{-- Total row populated dynamically --}}
            </tfoot>
        </table>

        {{-- Empty State --}}
        <div id="tableEmpty" class="text-center py-5" style="display: none;">
            <i class="fas fa-inbox fs-2 text-muted mb-2"></i>
            <h6 class="text-dark fw-bold mb-1">No sales data found</h6>
            <p class="text-muted small mb-0">No sales transactions found for the selected date range and filter criteria.</p>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        loadReportData();
    });

    function toggleFilterPanel() {
        const panel = document.getElementById('filterPanel');
        const btn = document.getElementById('btnToggleFilters');
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
            btn.classList.add('active');
        } else {
            panel.style.display = 'none';
            btn.classList.remove('active');
        }
    }

    function formatNumber(num, decimals = 2) {
        if (num === null || num === undefined || isNaN(num)) return '0.00';
        return Number(num).toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }

    function setDatePreset(preset) {
        const fromInput = document.getElementById('filterFromDate');
        const toInput = document.getElementById('filterToDate');
        const now = new Date();

        if (preset === 'today') {
            const todayStr = now.toISOString().slice(0, 10);
            fromInput.value = todayStr;
            toInput.value = todayStr;
        } else if (preset === 'this_week') {
            const firstDay = new Date(now.setDate(now.getDate() - now.getDay() + 1));
            fromInput.value = firstDay.toISOString().slice(0, 10);
            toInput.value = new Date().toISOString().slice(0, 10);
        } else if (preset === 'this_month') {
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            fromInput.value = firstDay.toISOString().slice(0, 10);
            toInput.value = new Date().toISOString().slice(0, 10);
        } else if (preset === 'all_time') {
            fromInput.value = '';
            toInput.value = '';
        }

        loadReportData();
    }

    function resetFilters() {
        document.getElementById('filterFromDate').value = '{{ $fromDate }}';
        document.getElementById('filterToDate').value = '{{ $toDate }}';
        document.getElementById('filterOfficer').value = 'all';
        document.getElementById('filterCustomer').value = 'all';
        document.getElementById('filterWarehouse').value = 'all';
        loadReportData();
    }

    function loadReportData() {
        const loading = document.getElementById('tableLoading');
        const table = document.getElementById('reportTable');
        const empty = document.getElementById('tableEmpty');
        const tbody = document.getElementById('reportTableBody');
        const tfoot = document.getElementById('reportTableFoot');

        loading.style.display = 'block';
        table.style.display = 'none';
        empty.style.display = 'none';

        const params = new URLSearchParams({
            from_date: document.getElementById('filterFromDate').value,
            to_date: document.getElementById('filterToDate').value,
            sales_officer_id: document.getElementById('filterOfficer').value,
            customer_id: document.getElementById('filterCustomer').value,
            warehouse_id: document.getElementById('filterWarehouse').value,
        });

        fetch('{{ route("report.trade_discount.fetch") }}?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(res => {
            loading.style.display = 'none';

            if (!res.ok || !res.rows || res.rows.length === 0) {
                empty.style.display = 'block';
                updateKpis({
                    gross_exc_gst: 0,
                    gross_inc_gst: 0,
                    net_amount: 0,
                    reg_discount: 0,
                    sp_discount: 0,
                    to_total: 0,
                    trade_activity_val: 0
                });
                return;
            }

            if (res.print_date) {
                document.getElementById('lblPrintDate').innerText = res.print_date;
            }

            // Update KPI cards
            updateKpis(res.totals);

            // Populate rows
            let rowsHtml = '';
            res.rows.forEach(r => {
                rowsHtml += `
                    <tr>
                        <td class="col-text fw-bold text-secondary">${r.order_booker_code}</td>
                        <td class="col-text fw-semibold text-dark">${r.order_booker_name}</td>
                        <td class="col-num">${formatNumber(r.gross_exc_gst)}</td>
                        <td class="col-num">${formatNumber(r.gross_inc_gst)}</td>
                        <td class="col-num fw-bold text-dark">${formatNumber(r.net_amount)}</td>

                        <!-- Discount -->
                        <td class="col-num">${formatNumber(r.reg_discount)}</td>
                        <td class="col-center text-muted">${formatNumber(r.reg_discount_pct)} %</td>
                        <td class="col-num">${formatNumber(r.sp_discount)}</td>
                        <td class="col-center text-muted">${formatNumber(r.sp_discount_pct)} %</td>
                        <td class="col-num fw-semibold">${formatNumber(r.total_discount)}</td>
                        <td class="col-center text-muted">${formatNumber(r.total_discount_pct)} %</td>

                        <!-- To (Trade Offer) -->
                        <td class="col-center">${Math.round(r.to_unit)}</td>
                        <td class="col-num">${formatNumber(r.to_unit_value)}</td>
                        <td class="col-num">${formatNumber(r.to_in_value)}</td>
                        <td class="col-num fw-semibold">${formatNumber(r.to_total)}</td>
                        <td class="col-center fw-semibold text-primary">${formatNumber(r.to_total_pct)} %</td>

                        <!-- Trade Activity -->
                        <td class="col-num fw-semibold">${formatNumber(r.trade_activity_val)}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = rowsHtml;

            // Populate Totals row
            const tot = res.totals;
            const footHtml = `
                <tr>
                    <td colspan="2" class="text-end fw-bold">Total:</td>
                    <td class="col-num fw-bold">${formatNumber(tot.gross_exc_gst)}</td>
                    <td class="col-num fw-bold">${formatNumber(tot.gross_inc_gst)}</td>
                    <td class="col-num fw-bold">${formatNumber(tot.net_amount)}</td>

                    <!-- Discount Totals -->
                    <td class="col-num fw-bold">${formatNumber(tot.reg_discount)}</td>
                    <td class="col-center fw-bold">${formatNumber(tot.reg_discount_pct)} %</td>
                    <td class="col-num fw-bold">${formatNumber(tot.sp_discount)}</td>
                    <td class="col-center fw-bold">${formatNumber(tot.sp_discount_pct)} %</td>
                    <td class="col-num fw-bold">${formatNumber(tot.total_discount)}</td>
                    <td class="col-center fw-bold">${formatNumber(tot.total_discount_pct)} %</td>

                    <!-- To Totals -->
                    <td class="col-center fw-bold">${Math.round(tot.to_unit)}</td>
                    <td class="col-num fw-bold">${formatNumber(tot.to_unit_value)}</td>
                    <td class="col-num fw-bold">${formatNumber(tot.to_in_value)}</td>
                    <td class="col-num fw-bold">${formatNumber(tot.to_total)}</td>
                    <td class="col-center fw-bold">${formatNumber(tot.to_total_pct)} %</td>

                    <!-- Trade Activity Total -->
                    <td class="col-num fw-bold">${formatNumber(tot.trade_activity_val)}</td>
                </tr>
            `;
            tfoot.innerHTML = footHtml;

            table.style.display = 'table';
        })
        .catch(err => {
            console.error('Error loading trade discount report:', err);
            loading.style.display = 'none';
            empty.style.display = 'block';
        });
    }

    function updateKpis(tot) {
        document.getElementById('kpiGrossExc').innerText = formatNumber(tot.gross_exc_gst);
        document.getElementById('kpiGrossInc').innerText = formatNumber(tot.gross_inc_gst);
        document.getElementById('kpiNetAmount').innerText = formatNumber(tot.net_amount);
        document.getElementById('kpiRegDisc').innerText = formatNumber(tot.reg_discount);
        document.getElementById('kpiSpDisc').innerText = formatNumber(tot.sp_discount);
        document.getElementById('kpiToVal').innerText = formatNumber(tot.to_total);
        document.getElementById('kpiTradeAct').innerText = formatNumber(tot.trade_activity_val);
    }

    function exportCsvReport() {
        const params = new URLSearchParams({
            from_date: document.getElementById('filterFromDate').value,
            to_date: document.getElementById('filterToDate').value,
            sales_officer_id: document.getElementById('filterOfficer').value,
            customer_id: document.getElementById('filterCustomer').value,
            warehouse_id: document.getElementById('filterWarehouse').value,
        });
        window.location.href = '{{ route("report.trade_discount.export") }}?' + params.toString();
    }
</script>
@endsection
