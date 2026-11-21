@extends('admin_panel.layout.app')

@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid py-4">

            {{-- Header --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <div>
                    <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-chart-pie text-primary"></i> Company Claim &amp; Damage Reconciliation Report
                    </h4>
                    <p class="text-muted mb-0 small">
                        Reconcile Company 3-Tier Damage Allowances against actual customer damages, expiries, and vendor payables
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('claims.index') }}" class="btn btn-outline-secondary px-3 shadow-sm fw-medium d-inline-flex align-items-center gap-1" style="border-radius: 8px;">
                        <i class="fas fa-list"></i> Claims List
                    </a>
                    <a href="{{ route('claims.create') }}" class="btn btn-danger px-3 shadow-sm fw-medium d-inline-flex align-items-center gap-1" style="border-radius: 8px;">
                        <i class="fas fa-plus"></i> New Claim
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-dark px-3 shadow-sm fw-medium d-inline-flex align-items-center gap-1" style="border-radius: 8px;">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>

            {{-- Filters Card --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4">
                <form action="{{ route('claims.report') }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4 col-12">
                        <label class="form-label small fw-bold text-muted mb-1">Company / Vendor</label>
                        <select name="vendor_id" class="form-select form-select-sm">
                            <option value="">All Companies / Vendors</option>
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id }}" {{ $selectedVendorId == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-bold text-muted mb-1">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                    </div>

                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-bold text-muted mb-1">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                    </div>

                    <div class="col-md-2 col-12 d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                            <i class="fas fa-filter me-1"></i> Filter Report
                        </button>
                        <a href="{{ route('claims.report') }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- KPI Metrics Grid --}}
            @php
                $grandPurchases = collect($vendorSummaries)->sum('total_purchases');
                $grandAllowance = collect($vendorSummaries)->sum('company_allowance');
                $grandClaims = collect($vendorSummaries)->sum('claims_in_period');
                $grandRemaining = collect($vendorSummaries)->sum('remaining_allowance');
                $grandPayable = collect($vendorSummaries)->sum('vendor_payable');
            @endphp
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                        <span class="text-muted small fw-bold text-uppercase">Purchases in Period</span>
                        <h4 class="fw-bold text-dark mb-0 mt-2 font-monospace">Rs {{ number_format($grandPurchases, 2) }}</h4>
                        <small class="text-muted">Total purchases from companies</small>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                        <span class="text-muted small fw-bold text-uppercase">Company Allowance (5%)</span>
                        <h4 class="fw-bold text-warning mb-0 mt-2 font-monospace">Rs {{ number_format($grandAllowance, 2) }}</h4>
                        <small class="text-muted">Total damage pool granted</small>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                        <span class="text-muted small fw-bold text-uppercase">Market Claims Received</span>
                        <h4 class="fw-bold text-danger mb-0 mt-2 font-monospace">Rs {{ number_format($grandClaims, 2) }}</h4>
                        <small class="text-muted">Customer claims in period</small>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                        <span class="text-muted small fw-bold text-uppercase">Remaining Pool / Surplus</span>
                        <h4 class="fw-bold text-success mb-0 mt-2 font-monospace">Rs {{ number_format($grandRemaining, 2) }}</h4>
                        <small class="text-muted">Unutilized damage allowance</small>
                    </div>
                </div>
            </div>

            {{-- Company-wise Reconciliation Summary Table --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-building text-secondary"></i> Company-wise Damage Allowance &amp; Payable Reconciliation
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                        <thead class="bg-light text-secondary text-uppercase" style="font-size: 11px;">
                            <tr>
                                <th class="py-3">Company / Vendor</th>
                                <th class="text-end">Purchases in Period</th>
                                <th class="text-end">Damage Allowance (5%)</th>
                                <th class="text-end">Claims in Period</th>
                                <th class="text-end">Total All-time Claims</th>
                                <th class="text-end">Remaining Allowance</th>
                                <th class="text-end">Current Payable</th>
                                <th class="text-end">Net Payable after Claims</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vendorSummaries as $vs)
                                @php
                                    $isExceeded = $vs['claims_in_period'] > $vs['company_allowance'] && $vs['company_allowance'] > 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $vs['vendor']->name }}</div>
                                        <small class="text-muted">{{ $vs['vendor']->phone ?? 'No Phone' }}</small>
                                    </td>
                                    <td class="text-end font-monospace">Rs {{ number_format($vs['total_purchases'], 2) }}</td>
                                    <td class="text-end font-monospace fw-bold text-warning">Rs {{ number_format($vs['company_allowance'], 2) }}</td>
                                    <td class="text-end font-monospace fw-bold text-danger">Rs {{ number_format($vs['claims_in_period'], 2) }}</td>
                                    <td class="text-end font-monospace text-muted">Rs {{ number_format($vs['total_claims_all_time'], 2) }}</td>
                                    <td class="text-end font-monospace fw-bold text-success">Rs {{ number_format($vs['remaining_allowance'], 2) }}</td>
                                    <td class="text-end font-monospace text-dark">Rs {{ number_format($vs['vendor_payable'], 2) }}</td>
                                    <td class="text-end font-monospace fw-bold text-primary">Rs {{ number_format($vs['net_payable_after_claims'], 2) }}</td>
                                    <td class="text-center">
                                        @if ($isExceeded)
                                            <span class="badge bg-danger-subtle text-danger px-2 py-1">Allowance Exceeded</span>
                                        @elseif ($vs['remaining_allowance'] > 0)
                                            <span class="badge bg-success-subtle text-success px-2 py-1">Surplus Covered</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1">Balanced</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">No company data found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light fw-bold" style="font-size: 13px;">
                            <tr>
                                <td>TOTAL SUMMARY:</td>
                                <td class="text-end font-monospace">Rs {{ number_format($grandPurchases, 2) }}</td>
                                <td class="text-end font-monospace text-warning">Rs {{ number_format($grandAllowance, 2) }}</td>
                                <td class="text-end font-monospace text-danger">Rs {{ number_format($grandClaims, 2) }}</td>
                                <td class="text-end font-monospace text-muted">-</td>
                                <td class="text-end font-monospace text-success">Rs {{ number_format($grandRemaining, 2) }}</td>
                                <td class="text-end font-monospace">Rs {{ number_format($grandPayable, 2) }}</td>
                                <td class="text-end font-monospace text-primary">Rs {{ number_format(max(0, $grandPayable - $grandClaims), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Top Damaged Products Table --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-boxes text-danger"></i> Top Damaged &amp; Expired Products (Period Loss)
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                        <thead class="bg-light text-secondary text-uppercase" style="font-size: 11px;">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th>Product Name</th>
                                <th>Item Code / SKU</th>
                                <th class="text-center">Total Damaged Pcs</th>
                                <th class="text-end">Total Loss Value (Rs)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topDamagedProducts as $idx => $tdp)
                                <tr>
                                    <td class="text-center font-monospace">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $tdp->product->item_name ?? 'Product' }}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $tdp->product->item_code ?? 'N/A' }}</span></td>
                                    <td class="text-center fw-bold font-monospace text-danger">{{ (int)$tdp->total_qty }} pcs</td>
                                    <td class="text-end fw-bold font-monospace text-danger fs-6">Rs {{ number_format($tdp->total_loss, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No product damage records found in this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
