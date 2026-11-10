@extends('admin_panel.layout.app')

@section('content')
<style>
    .branch-report-container {
        padding: 10px 14px;
        background: #f1f5f9;
        min-height: calc(100vh - 75px);
    }
    .filter-bar {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 10px 14px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .filter-label {
        font-weight: 700;
        font-size: .78rem;
        color: #475569;
        white-space: nowrap;
    }
    .summary-cards {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
    .summary-card {
        flex: 1 1 160px;
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 10px 14px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .summary-card .sc-label {
        font-size: .62rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #94a3b8;
        margin-bottom: 2px;
    }
    .summary-card .sc-value {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
    }
    .table-wrapper {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .branch-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .82rem;
    }
    .branch-table thead th {
        background: #1e293b;
        color: #fff;
        padding: 9px 12px;
        font-weight: 600;
        text-align: right;
        white-space: nowrap;
    }
    .branch-table thead th:first-child { text-align: left; }
    .branch-table tbody tr:nth-child(even) { background: #f8fafc; }
    .branch-table tbody td {
        padding: 8px 12px;
        border-bottom: 1px solid #f1f5f9;
        text-align: right;
        white-space: nowrap;
    }
    .branch-table tbody td:first-child { text-align: left; font-weight: 600; }
    .branch-table tfoot td {
        padding: 9px 12px;
        font-weight: 700;
        background: #f8fafc;
        border-top: 2px solid #cbd5e1;
        text-align: right;
    }
    .branch-table tfoot td:first-child { text-align: left; }
    .profit-pos { color: #16a34a; }
    .profit-neg { color: #dc2626; }
    #loadingSpinner { display: none; text-align: center; padding: 30px; }
</style>

<div class="branch-report-container">

    {{-- ── Title ─────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center mb-2">
        <i class="fa-solid fa-code-branch text-primary mr-2" style="font-size:1.3rem;"></i>
        <h5 class="mb-0 font-weight-bold" style="color:#1e293b;">Branch Summary Report</h5>
        <span class="badge badge-warning ml-2" style="font-size:.7rem;">Super Admin</span>
    </div>

    {{-- ── Filters ──────────────────────────────────────────────── --}}
    <div class="filter-bar">
        <span class="filter-label">From</span>
        <input type="date" id="startDate" class="form-control form-control-sm" style="width:140px;"
               value="{{ now()->startOfMonth()->format('Y-m-d') }}">
        <span class="filter-label">To</span>
        <input type="date" id="endDate" class="form-control form-control-sm" style="width:140px;"
               value="{{ now()->format('Y-m-d') }}">
        <span class="filter-label">Branch</span>
        <select id="branchFilter" class="form-control form-control-sm" style="width:170px;">
            <option value="">All Branches</option>
            @foreach($branches as $br)
                <option value="{{ $br->id }}">{{ $br->name }}</option>
            @endforeach
        </select>
        <button id="fetchBtn" class="btn btn-primary btn-sm px-3">
            <i class="fa fa-search mr-1"></i> Fetch
        </button>
        <button id="printBtn" class="btn btn-outline-secondary btn-sm px-3 ml-auto">
            <i class="fa fa-print mr-1"></i> Print
        </button>
    </div>

    {{-- ── Grand Total Summary Cards ──────────────────────────── --}}
    <div class="summary-cards" id="summaryCards" style="display:none!important;">
        <div class="summary-card">
            <div class="sc-label">Total Purchases</div>
            <div class="sc-value" id="gtPurchases">—</div>
        </div>
        <div class="summary-card">
            <div class="sc-label">Total Sales</div>
            <div class="sc-value" id="gtSales">—</div>
        </div>
        <div class="summary-card">
            <div class="sc-label">Total Expenses</div>
            <div class="sc-value" id="gtExpenses">—</div>
        </div>
        <div class="summary-card">
            <div class="sc-label">Net Profit</div>
            <div class="sc-value" id="gtProfit">—</div>
        </div>
    </div>

    {{-- ── Loading Spinner ─────────────────────────────────────── --}}
    <div id="loadingSpinner">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 text-muted">Loading branch data...</p>
    </div>

    {{-- ── Table ───────────────────────────────────────────────── --}}
    <div class="table-wrapper" id="tableWrapper" style="display:none;">
        <table class="branch-table" id="branchTable">
            <thead>
                <tr>
                    <th style="text-align:left;">Branch</th>
                    <th>Address</th>
                    <th>Purchases (#)</th>
                    <th>Purchase Amount</th>
                    <th>Sales (#)</th>
                    <th>Sales Amount</th>
                    <th>Expenses</th>
                    <th>Net Profit</th>
                </tr>
            </thead>
            <tbody id="branchTableBody"></tbody>
            <tfoot id="branchTableFoot"></tfoot>
        </table>
    </div>

    <div id="noDataMsg" class="text-center text-muted py-5" style="display:none;">
        No data found for the selected period.
    </div>

</div>

<script>
(function () {
    function fmt(n) {
        return parseFloat(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function fetchData() {
        const start  = document.getElementById('startDate').value;
        const end    = document.getElementById('endDate').value;
        const branch = document.getElementById('branchFilter').value;

        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('tableWrapper').style.display   = 'none';
        document.getElementById('noDataMsg').style.display      = 'none';
        document.getElementById('summaryCards').style.removeProperty('display');
        document.getElementById('summaryCards').style.display   = 'none';

        const params = new URLSearchParams({ start_date: start, end_date: end });
        if (branch) params.append('branch_id', branch);

        fetch('{{ route("report.branch_summary.fetch") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('loadingSpinner').style.display = 'none';

            if (!data.summary || data.summary.length === 0) {
                document.getElementById('noDataMsg').style.display = 'block';
                return;
            }

            // Grand totals
            const gt = data.grand_totals;
            document.getElementById('gtPurchases').textContent = 'Rs ' + fmt(gt.total_purchases);
            document.getElementById('gtSales').textContent     = 'Rs ' + fmt(gt.total_sales);
            document.getElementById('gtExpenses').textContent  = 'Rs ' + fmt(gt.total_expenses);
            const profitEl = document.getElementById('gtProfit');
            profitEl.textContent = 'Rs ' + fmt(gt.net_profit);
            profitEl.className   = 'sc-value ' + (gt.net_profit >= 0 ? 'profit-pos' : 'profit-neg');
            document.getElementById('summaryCards').style.display = 'flex';

            // Rows
            let rows = '';
            data.summary.forEach(r => {
                const profitClass = r.net_profit >= 0 ? 'profit-pos' : 'profit-neg';
                rows += `<tr>
                    <td>${r.branch_name}</td>
                    <td style="text-align:left;">${r.branch_address || '—'}</td>
                    <td>${r.purchase_count}</td>
                    <td>Rs ${fmt(r.total_purchases)}</td>
                    <td>${r.sale_count}</td>
                    <td>Rs ${fmt(r.total_sales)}</td>
                    <td>Rs ${fmt(r.total_expenses)}</td>
                    <td class="${profitClass}">Rs ${fmt(r.net_profit)}</td>
                </tr>`;
            });
            document.getElementById('branchTableBody').innerHTML = rows;

            // Footer
            const profitClass2 = gt.net_profit >= 0 ? 'profit-pos' : 'profit-neg';
            document.getElementById('branchTableFoot').innerHTML = `
                <tr>
                    <td colspan="3"><strong>Grand Total</strong></td>
                    <td>Rs ${fmt(gt.total_purchases)}</td>
                    <td></td>
                    <td>Rs ${fmt(gt.total_sales)}</td>
                    <td>Rs ${fmt(gt.total_expenses)}</td>
                    <td class="${profitClass2}">Rs ${fmt(gt.net_profit)}</td>
                </tr>`;

            document.getElementById('tableWrapper').style.display = 'block';
        })
        .catch(e => {
            document.getElementById('loadingSpinner').style.display = 'none';
            alert('Error fetching data. Please try again.');
            console.error(e);
        });
    }

    document.getElementById('fetchBtn').addEventListener('click', fetchData);
    document.getElementById('printBtn').addEventListener('click', () => window.print());

    // Auto-fetch on load
    fetchData();
})();
</script>
@endsection
