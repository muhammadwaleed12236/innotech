@extends('admin_panel.layout.app')

@section('content')
<style>
    .report-container {
        padding: 12px 16px;
        background: #f1f5f9;
        min-height: calc(100vh - 75px);
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
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .stat-pill .stat-label {
        font-size: .65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .stat-pill .stat-val {
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.2;
    }
    .table-wrap {
        height: calc(100vh - 270px);
        max-height: calc(100vh - 270px);
        min-height: 400px;
        overflow-y: auto;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
    }
    .trial-table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #1e293b !important;
        color: #ffffff !important;
        font-size: .78rem;
        font-weight: 700;
        padding: 9px 10px;
        border-bottom: 2px solid #334155;
        white-space: nowrap;
    }
    .trial-table tfoot th {
        position: sticky;
        bottom: 0;
        z-index: 10;
        background-color: #f8fafc !important;
        color: #0f172a !important;
        font-size: .85rem;
        font-weight: 800;
        border-top: 2px solid #cbd5e1;
    }
    @media print {
        .no-print { display: none !important; }
        .table-wrap { height: auto !important; max-height: none !important; overflow: visible !important; border: none !important; }
        body { background: #ffffff !important; }
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="report-container">

            <!-- Title & Top Actions -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print gap-2">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-balance-scale text-primary me-2"></i>Trial Balance (Mizan-e-Azmaish)
                    </h5>
                    <p class="text-muted small mb-0">Complete summary of ledger balances proving double-entry equality (Debit = Credit)</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print Trial Balance
                    </button>
                    <a href="{{ route('report.balance_sheet') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Balance Sheet
                    </a>
                    <a href="{{ route('report.profit_loss') }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-chart-line me-1"></i> Profit &amp; Loss
                    </a>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white no-print">
                <div class="card-body p-3">
                    <form id="trialFilterForm" class="row g-2 align-items-end">
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">From Date</label>
                            <input type="date" name="start_date" id="tbStartDate" class="form-control form-control-sm" value="{{ date('Y-01-01') }}">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">To Date</label>
                            <input type="date" name="end_date" id="tbEndDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3 col-sm-6 d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-light border flex-fill quick-preset" data-preset="this_year">This Year</button>
                            <button type="button" class="btn btn-sm btn-light border flex-fill quick-preset" data-preset="this_month">This Month</button>
                            <button type="button" class="btn btn-sm btn-light border flex-fill quick-preset" data-preset="all">All Time</button>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">
                                <i class="fas fa-sync-alt me-1"></i> Generate Trial Balance
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary KPI Pill Bar -->
            <div class="card border-0 shadow-sm mb-3 bg-white rounded-3 no-print">
                <div class="card-body p-2">
                    <div class="summary-pill-bar">
                        <div class="stat-pill" style="background:#f8fafc; border-color:#cbd5e1;">
                            <div class="stat-label text-secondary">Opening Debit</div>
                            <div class="stat-val text-dark" id="pillOpenDr">Rs 0.00</div>
                        </div>
                        <div class="stat-pill" style="background:#f8fafc; border-color:#cbd5e1;">
                            <div class="stat-label text-secondary">Opening Credit</div>
                            <div class="stat-val text-dark" id="pillOpenCr">Rs 0.00</div>
                        </div>
                        <div class="stat-pill" style="background:#eff6ff; border-color:#bfdbfe;">
                            <div class="stat-label text-primary">Period Debit</div>
                            <div class="stat-val text-primary" id="pillPeriodDr">Rs 0.00</div>
                        </div>
                        <div class="stat-pill" style="background:#eff6ff; border-color:#bfdbfe;">
                            <div class="stat-label text-primary">Period Credit</div>
                            <div class="stat-val text-primary" id="pillPeriodCr">Rs 0.00</div>
                        </div>
                        <div class="stat-pill" style="background:#f0fdf4; border-color:#86efac;">
                            <div class="stat-label text-success">Closing Debit</div>
                            <div class="stat-val text-success" id="pillCloseDr">Rs 0.00</div>
                        </div>
                        <div class="stat-pill" style="background:#f0fdf4; border-color:#86efac;">
                            <div class="stat-label text-success">Closing Credit</div>
                            <div class="stat-val text-success" id="pillCloseCr">Rs 0.00</div>
                        </div>
                        <div class="stat-pill" id="pillBalanceBox" style="background:#ecfdf5; border-color:#6ee7b7;">
                            <div class="stat-label text-dark">Status</div>
                            <div class="stat-val text-success" id="pillBalanceStatus"><i class="fas fa-check-circle me-1"></i> Balanced</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loader -->
            <div id="loader" style="display:none; text-align:center; padding: 30px;">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="small text-muted mt-2">Computing Trial Balance...</div>
            </div>

            <!-- Main Table Container -->
            <div class="table-wrap shadow-sm">
                <table class="table table-hover table-striped align-middle trial-table mb-0" id="trialTable" style="font-size: 12.5px;">
                    <thead>
                        <tr>
                            <th rowspan="2" class="ps-3 align-middle">Code</th>
                            <th rowspan="2" class="align-middle">Account Title</th>
                            <th rowspan="2" class="align-middle">Head of Account</th>
                            <th colspan="2" class="text-center border-bottom border-light" style="background:#334155 !important;">Opening Balance</th>
                            <th colspan="2" class="text-center border-bottom border-light" style="background:#1e3a8a !important;">Period Transactions</th>
                            <th colspan="2" class="text-center pe-3" style="background:#065f46 !important;">Closing Balance</th>
                        </tr>
                        <tr>
                            <th class="text-end" style="background:#334155 !important; font-size:11px;">Debit (Dr)</th>
                            <th class="text-end" style="background:#334155 !important; font-size:11px;">Credit (Cr)</th>
                            <th class="text-end" style="background:#1e3a8a !important; font-size:11px;">Debit (Dr)</th>
                            <th class="text-end" style="background:#1e3a8a !important; font-size:11px;">Credit (Cr)</th>
                            <th class="text-end" style="background:#065f46 !important; font-size:11px;">Debit (Dr)</th>
                            <th class="text-end pe-3" style="background:#065f46 !important; font-size:11px;">Credit (Cr)</th>
                        </tr>
                    </thead>
                    <tbody id="trialTableBody">
                        <tr><td colspan="9" class="text-center text-muted py-5">Loading Trial Balance data...</td></tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="ps-3 text-uppercase">Grand Total</th>
                            <th class="text-end font-monospace" id="footOpenDr">0.00</th>
                            <th class="text-end font-monospace" id="footOpenCr">0.00</th>
                            <th class="text-end font-monospace text-primary" id="footPeriodDr">0.00</th>
                            <th class="text-end font-monospace text-primary" id="footPeriodCr">0.00</th>
                            <th class="text-end font-monospace text-success" id="footCloseDr">0.00</th>
                            <th class="text-end pe-3 font-monospace text-success" id="footCloseCr">0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {

    function fmt(n) {
        return parseFloat(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Quick Date Presets
    $('.quick-preset').on('click', function() {
        let preset = $(this).data('preset');
        let now = new Date();
        let y = now.getFullYear();
        let m = String(now.getMonth() + 1).padStart(2, '0');
        let d = String(now.getDate()).padStart(2, '0');

        if (preset === 'this_year') {
            $('#tbStartDate').val(`${y}-01-01`);
            $('#tbEndDate').val(`${y}-${m}-${d}`);
        } else if (preset === 'this_month') {
            $('#tbStartDate').val(`${y}-${m}-01`);
            $('#tbEndDate').val(`${y}-${m}-${d}`);
        } else if (preset === 'all') {
            $('#tbStartDate').val('2000-01-01');
            $('#tbEndDate').val(`${y}-${m}-${d}`);
        }
        fetchTrialBalance();
    });

    $('#trialFilterForm').on('submit', function(e) {
        e.preventDefault();
        fetchTrialBalance();
    });

    function fetchTrialBalance() {
        $('#loader').show();
        let startDate = $('#tbStartDate').val();
        let endDate   = $('#tbEndDate').val();

        $.ajax({
            url: "{{ route('report.trial_balance.fetch') }}",
            type: "GET",
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(res) {
                $('#loader').hide();
                renderTrialBalance(res);
            },
            error: function() {
                $('#loader').hide();
                alert('Could not fetch trial balance data');
            }
        });
    }

    function renderTrialBalance(data) {
        let rowsHtml = '';
        let rows = data.rows || [];

        if (rows.length === 0) {
            $('#trialTableBody').html('<tr><td colspan="9" class="text-center text-muted py-4">No account activity found for this period</td></tr>');
            return;
        }

        rows.forEach(function(r) {
            rowsHtml += `
                <tr>
                    <td class="ps-3 font-monospace fw-bold text-secondary small">${r.code}</td>
                    <td>
                        <strong class="text-dark">${r.title}</strong>
                    </td>
                    <td><span class="badge bg-light text-dark border px-2 py-1">${r.head}</span></td>
                    <td class="text-end font-monospace text-muted">${r.opening_dr > 0 ? fmt(r.opening_dr) : '—'}</td>
                    <td class="text-end font-monospace text-muted">${r.opening_cr > 0 ? fmt(r.opening_cr) : '—'}</td>
                    <td class="text-end font-monospace text-primary fw-bold">${r.period_dr > 0 ? fmt(r.period_dr) : '—'}</td>
                    <td class="text-end font-monospace text-primary fw-bold">${r.period_cr > 0 ? fmt(r.period_cr) : '—'}</td>
                    <td class="text-end font-monospace text-success fw-bold">${r.closing_dr > 0 ? fmt(r.closing_dr) : '—'}</td>
                    <td class="text-end pe-3 font-monospace text-success fw-bold">${r.closing_cr > 0 ? fmt(r.closing_cr) : '—'}</td>
                </tr>
            `;
        });

        $('#trialTableBody').html(rowsHtml);

        // Update Footers
        let t = data.totals || {};
        $('#footOpenDr').text('Rs ' + fmt(t.opening_dr));
        $('#footOpenCr').text('Rs ' + fmt(t.opening_cr));
        $('#footPeriodDr').text('Rs ' + fmt(t.period_dr));
        $('#footPeriodCr').text('Rs ' + fmt(t.period_cr));
        $('#footCloseDr').text('Rs ' + fmt(t.closing_dr));
        $('#footCloseCr').text('Rs ' + fmt(t.closing_cr));

        // Update Summary Pills
        $('#pillOpenDr').text('Rs ' + fmt(t.opening_dr));
        $('#pillOpenCr').text('Rs ' + fmt(t.opening_cr));
        $('#pillPeriodDr').text('Rs ' + fmt(t.period_dr));
        $('#pillPeriodCr').text('Rs ' + fmt(t.period_cr));
        $('#pillCloseDr').text('Rs ' + fmt(t.closing_dr));
        $('#pillCloseCr').text('Rs ' + fmt(t.closing_cr));

        // Balanced status
        let isBalanced = data.is_balanced;
        if (isBalanced) {
            $('#pillBalanceBox').css({ background: '#ecfdf5', 'border-color': '#6ee7b7' });
            $('#pillBalanceStatus').attr('class', 'stat-val text-success').html('<i class="fas fa-check-circle me-1"></i> Balanced');
        } else {
            let diff = Math.abs((t.closing_dr || 0) - (t.closing_cr || 0));
            $('#pillBalanceBox').css({ background: '#fef2f2', 'border-color': '#fca5a5' });
            $('#pillBalanceStatus').attr('class', 'stat-val text-danger').html('<i class="fas fa-exclamation-triangle me-1"></i> Diff: Rs ' + fmt(diff));
        }
    }

    // Initial Load
    fetchTrialBalance();

});
</script>
@endsection
