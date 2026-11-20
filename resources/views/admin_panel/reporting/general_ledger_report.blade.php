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
    .gl-table thead th {
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
    .gl-table tfoot th {
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

            <!-- Title & Actions -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print gap-2">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-book text-primary me-2"></i>General Ledger (Account Statement)
                    </h5>
                    <p class="text-muted small mb-0">Detailed transaction statement and running balance for any Chart of Account</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print Statement
                    </button>
                    <a href="{{ route('report.trial_balance') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-balance-scale me-1"></i> Trial Balance
                    </a>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="card border-0 shadow-sm rounded-4 mb-3 bg-white no-print">
                <div class="card-body p-3">
                    <form id="glFilterForm" class="row g-2 align-items-end">
                        
                        <!-- Account Selector -->
                        <div class="col-md-4 col-sm-12">
                            <label class="form-label small fw-bold text-dark mb-1">Select Chart of Account <span class="text-danger">*</span></label>
                            <select name="account_id" id="glAccountId" class="form-select form-select-sm select2" required>
                                <option value="">-- Choose Account --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->title }} ({{ $acc->account_code ?: 'A-' . $acc->id }}) - {{ $acc->head_name ?: 'General' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="col-md-2 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">From Date</label>
                            <input type="date" name="start_date" id="glStartDate" class="form-control form-control-sm" value="{{ date('Y-01-01') }}">
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">To Date</label>
                            <input type="date" name="end_date" id="glEndDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Presets -->
                        <div class="col-md-2 col-sm-6 d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-light border flex-fill quick-preset" data-preset="this_month">Month</button>
                            <button type="button" class="btn btn-sm btn-light border flex-fill quick-preset" data-preset="this_year">Year</button>
                            <button type="button" class="btn btn-sm btn-light border flex-fill quick-preset" data-preset="all">All</button>
                        </div>

                        <!-- Submit -->
                        <div class="col-md-2 col-sm-6">
                            <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">
                                <i class="fas fa-search me-1"></i> View Statement
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
                            <div class="stat-label text-secondary">Account Title</div>
                            <div class="stat-val text-dark fs-6" id="pillAccountTitle">—</div>
                        </div>
                        <div class="stat-pill" style="background:#f8fafc; border-color:#cbd5e1;">
                            <div class="stat-label text-secondary">Opening Balance</div>
                            <div class="stat-val text-dark" id="pillOpenBal">Rs 0.00</div>
                        </div>
                        <div class="stat-pill" style="background:#eff6ff; border-color:#bfdbfe;">
                            <div class="stat-label text-primary">Total Debit (Dr)</div>
                            <div class="stat-val text-primary" id="pillTotalDr">Rs 0.00</div>
                        </div>
                        <div class="stat-pill" style="background:#eff6ff; border-color:#bfdbfe;">
                            <div class="stat-label text-primary">Total Credit (Cr)</div>
                            <div class="stat-val text-primary" id="pillTotalCr">Rs 0.00</div>
                        </div>
                        <div class="stat-pill" style="background:#f0fdf4; border-color:#86efac;">
                            <div class="stat-label text-success">Closing Balance</div>
                            <div class="stat-val text-success" id="pillCloseBal">Rs 0.00</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loader -->
            <div id="loader" style="display:none; text-align:center; padding: 30px;">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="small text-muted mt-2">Loading Account Statement...</div>
            </div>

            <!-- Transactions Table -->
            <div class="table-wrap shadow-sm">
                <table class="table table-hover table-striped align-middle gl-table mb-0" id="glTable" style="font-size: 12.5px;">
                    <thead>
                        <tr>
                            <th class="ps-3 py-2" style="width: 120px;">Date</th>
                            <th class="py-2" style="width: 120px;">Source</th>
                            <th class="py-2">Description / Narration</th>
                            <th class="py-2 text-end" style="width: 140px;">Debit (Dr)</th>
                            <th class="py-2 text-end" style="width: 140px;">Credit (Cr)</th>
                            <th class="pe-3 py-2 text-end" style="width: 160px;">Running Balance</th>
                        </tr>
                    </thead>
                    <tbody id="glTableBody">
                        <tr><td colspan="6" class="text-center text-muted py-5">Please select an account and click "View Statement"</td></tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="ps-3 text-uppercase">Totals</th>
                            <th class="text-end font-monospace text-primary" id="footTotalDr">0.00</th>
                            <th class="text-end font-monospace text-primary" id="footTotalCr">0.00</th>
                            <th class="pe-3 text-end font-monospace text-success" id="footCloseBal">0.00</th>
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

    if ($('.select2').length > 0 && $.fn.select2) {
        $('.select2').select2({ width: '100%' });
    }

    function fmt(n) {
        return parseFloat(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    $('.quick-preset').on('click', function() {
        let preset = $(this).data('preset');
        let now = new Date();
        let y = now.getFullYear();
        let m = String(now.getMonth() + 1).padStart(2, '0');
        let d = String(now.getDate()).padStart(2, '0');

        if (preset === 'this_year') {
            $('#glStartDate').val(`${y}-01-01`);
            $('#glEndDate').val(`${y}-${m}-${d}`);
        } else if (preset === 'this_month') {
            $('#glStartDate').val(`${y}-${m}-01`);
            $('#glEndDate').val(`${y}-${m}-${d}`);
        } else if (preset === 'all') {
            $('#glStartDate').val('2000-01-01');
            $('#glEndDate').val(`${y}-${m}-${d}`);
        }
        if ($('#glAccountId').val()) {
            fetchGeneralLedger();
        }
    });

    $('#glFilterForm').on('submit', function(e) {
        e.preventDefault();
        fetchGeneralLedger();
    });

    function fetchGeneralLedger() {
        let accId = $('#glAccountId').val();
        if (!accId) {
            alert('Please select an account');
            return;
        }

        $('#loader').show();
        let startDate = $('#glStartDate').val();
        let endDate   = $('#glEndDate').val();

        $.ajax({
            url: "{{ route('report.general_ledger.fetch') }}",
            type: "GET",
            data: {
                account_id: accId,
                start_date: startDate,
                end_date: endDate
            },
            success: function(res) {
                $('#loader').hide();
                renderGeneralLedger(res);
            },
            error: function(xhr) {
                $('#loader').hide();
                alert('Could not fetch account ledger');
            }
        });
    }

    function renderGeneralLedger(data) {
        let acc = data.account || {};
        let txs = data.transactions || [];

        $('#pillAccountTitle').text(acc.title + ' (' + acc.code + ')');
        $('#pillOpenBal').text('Rs ' + fmt(data.opening_balance));
        $('#pillTotalDr').text('Rs ' + fmt(data.total_debit));
        $('#pillTotalCr').text('Rs ' + fmt(data.total_credit));
        $('#pillCloseBal').text('Rs ' + fmt(data.closing_balance));

        $('#footTotalDr').text('Rs ' + fmt(data.total_debit));
        $('#footTotalCr').text('Rs ' + fmt(data.total_credit));
        $('#footCloseBal').text('Rs ' + fmt(data.closing_balance));

        let rowsHtml = '';
        
        // Opening balance row
        rowsHtml += `
            <tr style="background:#f8fafc;">
                <td class="ps-3 font-monospace text-muted">—</td>
                <td><span class="badge bg-secondary-subtle text-dark border">Opening</span></td>
                <td><strong>Opening Balance B/F</strong></td>
                <td class="text-end font-monospace text-muted">—</td>
                <td class="text-end font-monospace text-muted">—</td>
                <td class="pe-3 text-end font-monospace fw-bold text-dark">Rs ${fmt(data.opening_balance)}</td>
            </tr>
        `;

        if (txs.length === 0) {
            rowsHtml += `<tr><td colspan="6" class="text-center text-muted py-4">No transactions during this period</td></tr>`;
        } else {
            txs.forEach(function(t) {
                rowsHtml += `
                    <tr>
                        <td class="ps-3 font-monospace text-dark">${t.date}</td>
                        <td><span class="badge bg-light text-primary border">${t.source_type || 'GL'}</span></td>
                        <td>${t.description}</td>
                        <td class="text-end font-monospace text-primary fw-bold">${t.debit > 0 ? ('Rs ' + fmt(t.debit)) : '—'}</td>
                        <td class="text-end font-monospace text-danger fw-bold">${t.credit > 0 ? ('Rs ' + fmt(t.credit)) : '—'}</td>
                        <td class="pe-3 text-end font-monospace fw-bold text-success">Rs ${fmt(t.balance)}</td>
                    </tr>
                `;
            });
        }

        $('#glTableBody').html(rowsHtml);
    }

    // Auto-load first account if selected
    if ($('#glAccountId').val()) {
        fetchGeneralLedger();
    }

});
</script>
@endsection
