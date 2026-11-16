@extends('admin_panel.layout.app')

@section('title', ($account->title ?? 'Account') . ' - Ledger')

@section('content')
    <style>
        .ledger-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px 8px 0 0;
        }

        .balance-positive {
            color: #198754;
            font-weight: bold;
        }

        .balance-negative {
            color: #dc3545;
            font-weight: bold;
        }

        .table-ledger th {
            background-color: #212529 !important;
            color: #fff;
            text-align: center;
        }

        .table-ledger td {
            vertical-align: middle;
        }

        @media print {
            body { background: #fff !important; }
            .no-print, header, .sidebar, .navbar, .rt_nav_header, footer, form, .btn, .card-footer {
                display: none !important;
            }
            .card { border: none !important; box-shadow: none !important; }
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid mt-4">

                <div class="card shadow-sm">
                    <!-- Ledger Header -->
                    <div class="ledger-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-primary"><i class="bi bi-book"></i> General Ledger</h4>
                            <h5 class="text-dark">{{ $account->title }} <span
                                    class="text-muted fs-6">({{ $account->account_code }})</span></h5>
                            <p class="mb-0 text-muted">Head: {{ $account->head->name ?? 'N/A' }} | Type: {{ $account->type }}
                            </p>
                        </div>
                        <div class="text-end">
                            <h3 class="{{ $account->current_balance >= 0 ? 'balance-positive' : 'balance-negative' }}">
                                {{ number_format(abs($account->current_balance), 2) }}
                                <small class="fs-6 text-muted">{{ $account->current_balance >= 0 ? 'Dr' : 'Cr' }}</small>
                            </h3>
                            <div class="d-flex align-items-center justify-content-end gap-2 mt-1">
                                <span class="badge bg-secondary">Current Balance</span>
                                <button type="button" onclick="printAccountLedger()" class="btn btn-sm btn-outline-primary"><i class="bi bi-printer"></i> Print</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if(!empty($isDiscountAccount) && $isDiscountAccount)
                            {{-- DISCOUNTS & CLAIMS MADD-WISE SUMMARY BANNER --}}
                            <div class="p-3 mb-4 rounded-3 border shadow-xs" style="background: linear-gradient(135deg, #f8fafc 0%, #ede9fe 100%); border-color: #c4b5fd !important;">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                    <div>
                                        <h5 class="fw-bold mb-1" style="color: #4c1d95;">
                                            <i class="fas fa-tags me-2" style="color: #7c3aed;"></i> ڈسکاؤنٹ اور کمپنی کلیمز مَد وائز تفصیل (Discounts & Claims Breakdown)
                                        </h5>
                                        <p class="small text-muted mb-0">
                                            بیلنس شیٹ پر شو ہونے والے ڈسکاؤنٹ کلیمز (Rs {{ number_format($totalDiscountReceived ?? 0, 2) }}) کی مکمل مَد وائز تفصیل اور اندراج
                                        </p>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge px-3 py-2 fs-6 shadow-xs" style="background: #e11d48; color: #fff; border-radius: 8px;" title="Total Discount minus Owner Share = Balance Sheet Liabilities">
                                            لائیبلٹیز کلیمز (بیلنس شیٹ): Rs {{ number_format(max(0, ($totalDiscountReceived ?? 0) - ($maddSummary['ہمارا شیئر']['amount'] ?? 0)), 2) }}
                                        </span>
                                        <span class="badge px-3 py-2 fs-6 shadow-xs" style="background: #7c3aed; color: #fff; border-radius: 8px;" title="Total Discount Received from Suppliers">
                                            کُل ڈسکاؤنٹ موصولہ: Rs {{ number_format($totalDiscountReceived ?? 0, 2) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Madd Cards Grid --}}
                                @if(!empty($maddSummary) && count($maddSummary) > 0)
                                    <div class="row g-2">
                                        @foreach($maddSummary as $mName => $mData)
                                            @php
                                                $isCust = str_contains($mName, 'کسٹمر') || str_contains($mName, 'Customer');
                                                $isOur = str_contains($mName, 'ہمارا') || str_contains($mName, 'Owner');
                                                $bgCard = $isCust ? '#e0f2fe' : ($isOur ? '#f3e8ff' : '#fef3c7');
                                                $borderCard = $isCust ? '#bae6fd' : ($isOur ? '#e9d5ff' : '#fde68a');
                                                $textCard = $isCust ? '#0369a1' : ($isOur ? '#6b21a8' : '#92400e');
                                                $iconCard = $isCust ? 'fa-users' : ($isOur ? 'fa-user-shield' : 'fa-tag');
                                            @endphp
                                            <div class="col-6 col-md-3">
                                                <div class="p-2 rounded-2 border d-flex align-items-center justify-content-between h-100" style="background: {{ $bgCard }}; border-color: {{ $borderCard }} !important;">
                                                    <div>
                                                        <span class="d-block fw-bold" style="font-size: 11px; color: {{ $textCard }};">
                                                            <i class="fas {{ $iconCard }} me-1"></i> {{ $mName }}
                                                        </span>
                                                        <span class="fw-bold fs-6 font-monospace" style="color: #0f172a;">
                                                            Rs {{ number_format($mData['amount'], 2) }}
                                                        </span>
                                                    </div>
                                                    <span class="badge rounded-pill bg-white text-dark border px-2 py-1 small">
                                                        {{ $mData['count'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if(!empty($vendorSummary) && count($vendorSummary) > 0)
                                    <div class="mt-3 pt-2 border-top d-flex flex-wrap align-items-center gap-2" style="font-size: 11px;">
                                        <span class="fw-bold text-muted"><i class="fas fa-truck-field me-1"></i> وینڈرز وائز سمری:</span>
                                        @foreach($vendorSummary as $vName => $vData)
                                            <span class="badge bg-white text-dark border px-2 py-1 shadow-xs">
                                                <strong>{{ $vName }}:</strong> Rs {{ number_format($vData['amount'], 2) }} ({{ $vData['count'] }})
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Filters -->
                        <form method="GET" class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">From Date</label>
                                <input type="date" name="from_date" value="{{ request('from_date') }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">To Date</label>
                                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                            </div>
                            @if(!empty($isDiscountAccount) && $isDiscountAccount && !empty($maddSummary))
                                <div class="col-md-2">
                                    <label class="form-label">مَد (Head)</label>
                                    <select name="madd" class="form-select">
                                        <option value="all">تمام مَدات (All)</option>
                                        @foreach($maddSummary as $mName => $mData)
                                            <option value="{{ $mName }}" {{ request('madd') == $mName ? 'selected' : '' }}>{{ $mName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter"></i>
                                    Filter</button>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <a href="{{ route('accounts.ledger', $account->id) }}"
                                    class="btn btn-outline-secondary w-100">Reset</a>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <a href="{{ route('view_all') }}" class="btn btn-secondary w-100"><i
                                        class="bi bi-arrow-left"></i> Back</a>
                            </div>
                        </form>

                        <!-- Ledger Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-ledger">
                                <thead>
                                    <tr>
                                        <th width="10%">Date</th>
                                        <th width="12%">Voucher No</th>
                                        <th width="14%">مَد (Head)</th>
                                        <th width="24%" class="text-start">Description</th>
                                        <th width="16%">Party</th>
                                        <th width="8%">Debit</th>
                                        <th width="8%">Credit</th>
                                        <th width="8%">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $runningBalance = $account->opening_balance ?? 0;
                                    @endphp

                                    {{-- Opening Balance Row --}}
                                    <tr class="bg-light">
                                        <td colspan="7" class="text-end fw-bold">Opening Balance</td>
                                        <td class="text-end fw-bold">{{ number_format($runningBalance, 2) }}</td>
                                    </tr>

                                    @foreach ($entries as $entry)
                                        @php
                                            $debit = $entry->debit ?? 0;
                                            $credit = $entry->credit ?? 0;
                                            $runningBalance = $runningBalance + $debit - $credit;

                                            $voucherNo = '-';
                                            $invoiceUrl = null;
                                            $editUrl = null;

                                            if ($entry->source && $entry->source->voucher_no) {
                                                $voucherNo = $entry->source->voucher_no;
                                            } elseif ($entry->source && $entry->source->invoice_no) {
                                                $voucherNo = $entry->source->invoice_no;
                                            }

                                            // Fallback from description
                                            if ($voucherNo === '-' && preg_match('/(?:Purchase|PUR)[\s#\-]*([A-Za-z0-9\-_]+)/i', $entry->description, $matches)) {
                                                $voucherNo = trim($matches[1]);
                                            }

                                            // Check Purchase link
                                            if ($entry->source_type === 'App\\Models\\Purchase' || ($entry->source && $entry->source instanceof \App\Models\Purchase) || $entry->source_id) {
                                                $pId = $entry->source_id ?? ($entry->source ? $entry->source->id : null);
                                                if (!$pId && $voucherNo !== '-') {
                                                    $pur = \App\Models\Purchase::where('invoice_no', $voucherNo)->orWhere('invoice_no', 'like', "%{$voucherNo}%")->first();
                                                    if ($pur) $pId = $pur->id;
                                                }
                                                if ($pId) {
                                                    $invoiceUrl = route('purchase.invoice', $pId);
                                                    $editUrl = route('purchase.edit', $pId);
                                                }
                                            } elseif ($entry->source_type === 'App\\Models\\Sale' || ($entry->source && $entry->source instanceof \App\Models\Sale)) {
                                                $sId = $entry->source_id ?? ($entry->source ? $entry->source->id : null);
                                                if ($sId) {
                                                    $invoiceUrl = url('/sale/invoice/' . $sId);
                                                    $editUrl = url('/sale/' . $sId . '/edit');
                                                }
                                            }

                                            // Resolve Party name and type
                                            $partyName = '';
                                            $partyType = '';
                                            $partyBadgeClass = 'bg-secondary';
                                            if ($entry->party) {
                                                if ($entry->party_type === 'App\\Models\\Customer') {
                                                    $partyName = $entry->party->customer_name ?? 'Unknown';
                                                    $partyType = 'Customer';
                                                    $partyBadgeClass = 'bg-primary';
                                                } elseif ($entry->party_type === 'App\\Models\\Vendor') {
                                                    $partyName = $entry->party->name ?? 'Unknown';
                                                    $partyType = 'Vendor';
                                                    $partyBadgeClass = 'bg-warning text-dark';
                                                } else {
                                                    $partyName = $entry->party->title ?? $entry->party->name ?? 'Account';
                                                    $partyType = 'Account';
                                                    $partyBadgeClass = 'bg-info text-dark';
                                                }
                                            }

                                            // Parse Head (Madd) from description
                                            $rowMadd = 'جنرل ڈسکاؤنٹ';
                                            $cleanDesc = $entry->description;
                                            if (preg_match('/\[مَد:\s*([^\]]+)\]\s*(.*)/u', $entry->description, $m)) {
                                                $rowMadd = trim($m[1]);
                                                $cleanDesc = trim($m[2]);
                                            } elseif (stripos($entry->description, 'Customer') !== false) {
                                                $rowMadd = 'کسٹمر شیئر';
                                            } elseif (stripos($entry->description, 'Owner') !== false || stripos($entry->description, 'Humara') !== false) {
                                                $rowMadd = 'ہمارا شیئر';
                                            }

                                            $isCustM = str_contains($rowMadd, 'کسٹمر') || str_contains($rowMadd, 'Customer');
                                            $isOurM = str_contains($rowMadd, 'ہمارا') || str_contains($rowMadd, 'Owner');
                                            $maddBg = $isCustM ? '#e0f2fe' : ($isOurM ? '#f3e8ff' : '#fef3c7');
                                            $maddColor = $isCustM ? '#0369a1' : ($isOurM ? '#6b21a8' : '#92400e');
                                            $maddBorder = $isCustM ? '#bae6fd' : ($isOurM ? '#e9d5ff' : '#fde68a');
                                            $maddIcon = $isCustM ? 'fa-users' : ($isOurM ? 'fa-user-shield' : 'fa-tag');
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $entry->entry_date->format('d/m/Y') }}</td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                @if ($invoiceUrl)
                                                    <div>
                                                        <a href="{{ $invoiceUrl }}" target="_blank" class="badge bg-primary-subtle text-primary border border-primary-subtle text-decoration-none py-1 px-2 fw-bold d-inline-flex align-items-center gap-1 shadow-xs" title="Click to View & Print Invoice">
                                                            <i class="fas fa-file-invoice"></i> {{ $voucherNo }} <i class="fas fa-arrow-up-right-from-square" style="font-size: 8px;"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <span class="badge bg-light text-dark border">{{ $voucherNo }}</span>
                                                @endif

                                                @if ($editUrl)
                                                    <div class="mt-1">
                                                        <a href="{{ $editUrl }}" target="_blank" class="btn btn-outline-warning btn-xs py-0 px-2 fw-semibold text-dark d-inline-flex align-items-center gap-1 shadow-xs" style="font-size: 10px; border-radius: 4px;" title="Click to Edit Invoice">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center" style="white-space: nowrap;">
                                                <span class="badge px-2 py-1 fw-bold" style="background: {{ $maddBg }}; color: {{ $maddColor }}; border: 1px solid {{ $maddBorder }}; font-size: 11px;">
                                                    <i class="fas {{ $maddIcon }} me-1"></i> {{ $rowMadd }}
                                                </span>
                                            </td>
                                            <td>{{ $cleanDesc }}</td>
                                            <td>
                                                @if ($partyName)
                                                    <span class="badge {{ $partyBadgeClass }}" style="font-size: 0.7rem;">{{ $partyType }}</span>
                                                    <span class="fw-bold d-block" style="font-size: 0.85rem;">{{ $partyName }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end text-success">
                                                {{ $debit > 0 ? number_format($debit, 2) : '-' }}</td>
                                            <td class="text-end text-danger">
                                                {{ $credit > 0 ? number_format($credit, 2) : '-' }}</td>
                                            <td class="text-end fw-bold">
                                                {{ number_format(abs($runningBalance), 2) }}
                                                <small class="text-muted">{{ $runningBalance >= 0 ? 'Dr' : 'Cr' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-dark">
                                        <td colspan="5" class="text-end fw-bold">Total Period</td>
                                        <td class="text-end fw-bold">{{ number_format($entries->sum('debit'), 2) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($entries->sum('credit'), 2) }}</td>
                                        <td class="text-end fw-bold">{{ number_format(abs($runningBalance), 2) }} <small>{{ $runningBalance >= 0 ? 'Dr' : 'Cr' }}</small></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-muted text-center">
                        End of Report
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function printAccountLedger() {
        let name = "{{ addslashes($account->title ?? 'Account') }}";
        let cleanName = name.replace(/[\/\\:*?"<>|]/g, ' ').replace(/\s+/g, ' ').trim();
        document.title = (cleanName || 'Account') + ' - Ledger';
        window.print();
    }

    window.addEventListener('beforeprint', function() {
        let name = "{{ addslashes($account->title ?? 'Account') }}";
        let cleanName = name.replace(/[\/\\:*?"<>|]/g, ' ').replace(/\s+/g, ' ').trim();
        document.title = (cleanName || 'Account') + ' - Ledger';
    });
</script>
@endpush
