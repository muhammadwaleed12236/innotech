@extends('admin_panel.layout.app')

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="fas fa-undo-alt text-danger"></i> Sale Returns &amp; Credit Notes
                        </h4>
                        <p class="text-muted mb-0 small">View customer returns, credit notes, and restored stock</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a class="btn btn-outline-primary px-3 shadow-sm fw-medium d-inline-flex align-items-center gap-1" href="{{ route('sale.index') }}">
                            <i class="fas fa-shopping-cart"></i> All Sales Invoices
                        </a>
                        <a class="btn btn-danger px-3 shadow-sm fw-medium d-inline-flex align-items-center gap-1" href="{{ route('sale.return.create') }}">
                            <i class="fas fa-plus"></i> New Direct Return
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

                <!-- KPI Metric Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                            <span class="text-muted small fw-bold text-uppercase">Total Returns Count</span>
                            <h3 class="fw-bold text-danger mb-0 mt-1 font-monospace">{{ $returns->count() }}</h3>
                            <small class="text-muted">Return documents recorded</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                            <span class="text-muted small fw-bold text-uppercase">Total Refund Value</span>
                            <h3 class="fw-bold text-dark mb-0 mt-1 font-monospace">Rs {{ number_format($returns->sum('net_amount'), 2) }}</h3>
                            <small class="text-muted">Total amount credited/refunded to customers</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                            <span class="text-muted small fw-bold text-uppercase">Cash/Bank Paid Out</span>
                            <h3 class="fw-bold text-success mb-0 mt-1 font-monospace">Rs {{ number_format($returns->sum('paid'), 2) }}</h3>
                            <small class="text-muted">Immediate cash refunds paid back</small>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table id="return-table" class="table table-hover align-middle text-center mb-0" style="width: 100%;">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">ID</th>
                                        <th>Return Invoice</th>
                                        <th>Orig. Sale Inv</th>
                                        <th class="text-start">Customer</th>
                                        <th>Warehouse</th>
                                        <th>Date</th>
                                        <th class="text-end">Refund Value</th>
                                        <th class="text-end">Paid Cash</th>
                                        <th class="text-end">Ledger Credit</th>
                                        <th>Status</th>
                                        <th style="width: 90px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($returns as $return)
                                        @php
                                            $isPartialReturn = $return->sale && $return->total_returned < $return->original_net_amount;
                                            $isFullReturn = $return->sale && $return->total_returned >= $return->original_net_amount;
                                        @endphp
                                        <tr>
                                            <td class="font-monospace text-muted">{{ $return->id }}</td>
                                            <td class="font-monospace fw-bold text-danger">
                                                <a href="{{ route('sale.return.view', $return->id) }}" class="text-decoration-none text-danger">
                                                    {{ $return->return_invoice }}
                                                </a>
                                            </td>
                                            <td class="font-monospace">
                                                @if ($return->sale)
                                                    <span class="badge bg-light text-primary border">
                                                        {{ $return->sale->invoice_no }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-start">
                                                <div class="fw-bold text-dark">{{ $return->customer->customer_name ?? 'Walking Customer' }}</div>
                                                <small class="text-muted">{{ $return->customer->mobile ?? '' }}</small>
                                            </td>
                                            <td class="small">{{ $return->warehouse->warehouse_name ?? 'Default' }}</td>
                                            <td class="small text-muted">{{ \Carbon\Carbon::parse($return->return_date)->format('d/m/Y') }}</td>
                                            
                                            <td class="text-end font-monospace fw-bold text-danger">
                                                Rs {{ number_format($return->net_amount, 2) }}
                                            </td>
                                            
                                            <td class="text-end font-monospace text-success small">
                                                Rs {{ number_format($return->paid, 2) }}
                                            </td>

                                            <td class="text-end font-monospace text-primary fw-semibold small">
                                                Rs {{ number_format($return->balance, 2) }}
                                            </td>

                                            <td>
                                                @if ($isFullReturn)
                                                    <span class="badge bg-danger">Full Return</span>
                                                @elseif($isPartialReturn)
                                                    <span class="badge bg-warning text-dark">Partial Return</span>
                                                @else
                                                    <span class="badge bg-info text-white">Credit Note</span>
                                                @endif
                                            </td>

                                            <td>
                                                <a href="{{ route('sale.return.view', $return->id) }}" class="btn btn-sm btn-outline-primary px-2" title="View Return Slip">
                                                    <i class="fas fa-file-invoice me-1"></i> Slip
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center py-5 text-muted">
                                                <i class="fas fa-undo-alt fs-2 mb-2 d-block opacity-50"></i>
                                                No sale returns recorded yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            if ($('#return-table tbody tr').length > 0) {
                $('#return-table').DataTable({
                    "pageLength": 10,
                    "order": [[0, 'desc']]
                });
            }
        });
    </script>
@endsection
