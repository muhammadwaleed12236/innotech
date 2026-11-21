@extends('admin_panel.layout.app')

@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid py-4">

            {{-- Page Header --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <div>
                    <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-warning"></i> Customer Claims &amp; Damages Management
                    </h4>
                    <p class="text-muted mb-0 small">
                        Track customer damages, expiries, breakages, and reconcile against Company 3-Tier Discount Allowance
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-primary px-3 shadow-sm fw-medium d-inline-flex align-items-center gap-1" href="{{ route('claims.report') }}" style="border-radius: 8px;">
                        <i class="fas fa-chart-pie"></i> Reconciliation Report
                    </a>
                    <a class="btn btn-danger px-3 shadow-sm fw-medium d-inline-flex align-items-center gap-1" href="{{ route('claims.create') }}" style="border-radius: 8px;">
                        <i class="fas fa-plus"></i> New Claim / Damage
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm d-flex align-items-center gap-2 mb-3" role="alert">
                    <i class="fas fa-check-circle fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- KPI Summary Metric Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted small fw-bold text-uppercase">Total Claims</span>
                            <span class="badge bg-danger-subtle text-danger p-2 rounded-circle"><i class="fas fa-boxes"></i></span>
                        </div>
                        <h3 class="fw-bold text-dark mb-0 mt-2 font-monospace">{{ $totalClaimsCount }}</h3>
                        <small class="text-muted">Recorded customer claims</small>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted small fw-bold text-uppercase">Total Damage Value</span>
                            <span class="badge bg-warning-subtle text-warning p-2 rounded-circle"><i class="fas fa-money-bill-wave"></i></span>
                        </div>
                        <h3 class="fw-bold text-danger mb-0 mt-2 font-monospace">Rs {{ number_format($totalClaimValue, 2) }}</h3>
                        <small class="text-muted">Customer claim value received</small>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted small fw-bold text-uppercase">Company Deducted</span>
                            <span class="badge bg-primary-subtle text-primary p-2 rounded-circle"><i class="fas fa-building"></i></span>
                        </div>
                        <h3 class="fw-bold text-primary mb-0 mt-2 font-monospace">Rs {{ number_format($totalAdjustedCompany, 2) }}</h3>
                        <small class="text-muted">Adjusted against company allowance</small>
                    </div>
                </div>

                <div class="col-md-3 col-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted small fw-bold text-uppercase">Cash Refunds Paid</span>
                            <span class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="fas fa-hand-holding-usd"></i></span>
                        </div>
                        <h3 class="fw-bold text-success mb-0 mt-2 font-monospace">Rs {{ number_format($totalRefundsPaid, 2) }}</h3>
                        <small class="text-muted">Immediate cash refunds issued</small>
                    </div>
                </div>
            </div>

            {{-- Filter Panel --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4">
                <form action="{{ route('claims.index') }}" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3 col-12">
                        <label class="form-label small fw-bold text-muted mb-1">Search Claim # / Remarks</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search Claim #, customer, etc." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2 col-6">
                        <label class="form-label small fw-bold text-muted mb-1">Customer</label>
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">All Customers</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 col-6">
                        <label class="form-label small fw-bold text-muted mb-1">Company / Vendor</label>
                        <select name="vendor_id" class="form-select form-select-sm">
                            <option value="">All Companies</option>
                            @foreach ($vendors as $v)
                                <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 col-6">
                        <label class="form-label small fw-bold text-muted mb-1">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2 col-6">
                        <label class="form-label small fw-bold text-muted mb-1">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-1 col-12 d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100" title="Apply Filter">
                            <i class="fas fa-filter"></i>
                        </button>
                        <a href="{{ route('claims.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Claims Table --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                        <thead class="bg-light text-secondary text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                            <tr>
                                <th class="text-center py-3">Claim #</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Company / Vendor</th>
                                <th>Items</th>
                                <th class="text-end">Damage Value</th>
                                <th class="text-end">Company Allowance</th>
                                <th class="text-end">Vendor Remaining</th>
                                <th>Settlement</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($claims as $claim)
                                <tr>
                                    <td class="text-center">
                                        <a href="{{ route('claims.show', $claim->id) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                            {{ $claim->claim_no }}
                                        </a>
                                    </td>
                                    <td>{{ $claim->claim_date->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $claim->customer->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $claim->customer->phone ?? '' }}</small>
                                    </td>
                                    <td>
                                        @if ($claim->vendor)
                                            <span class="badge bg-light text-dark border px-2 py-1">
                                                <i class="fas fa-building text-secondary me-1"></i> {{ $claim->vendor->name }}
                                            </span>
                                        @else
                                            <span class="text-muted small">Multi / Direct</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                            {{ $claim->items->count() }} Item(s) ({{ (int)$claim->items->sum('total_pieces') }} pcs)
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-danger font-monospace">
                                        Rs {{ number_format($claim->total_claim_amount, 2) }}
                                    </td>
                                    <td class="text-end font-monospace text-muted">
                                        Rs {{ number_format($claim->company_allowance_before, 2) }}
                                    </td>
                                    <td class="text-end fw-bold font-monospace text-success">
                                        Rs {{ number_format($claim->company_remaining_balance, 2) }}
                                    </td>
                                    <td>
                                        @if ($claim->settlement_type === 'cash_refund')
                                            <span class="badge bg-success-subtle text-success px-2 py-1">
                                                <i class="fas fa-money-bill me-1"></i> Cash Refund
                                            </span>
                                        @elseif ($claim->settlement_type === 'replacement')
                                            <span class="badge bg-warning-subtle text-warning px-2 py-1">
                                                <i class="fas fa-sync-alt me-1"></i> Replacement
                                            </span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary px-2 py-1">
                                                <i class="fas fa-book me-1"></i> Ledger Credit
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info px-2 py-1 text-capitalize">
                                            {{ $claim->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('claims.show', $claim->id) }}" class="btn btn-sm btn-outline-primary p-1 px-2" title="View / Print">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5 text-muted">
                                        <i class="fas fa-exclamation-triangle fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                        <span>No customer claims or damages recorded yet.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($claims->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $claims->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
