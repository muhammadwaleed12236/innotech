@extends('booker_portal.layout')

@section('title', 'My Bookings')

@section('content')
<div class="row g-3">

    <!-- Header & Filters -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-list-alt text-primary me-2"></i>My Bookings History
                    </h5>
                    <p class="text-muted small mb-0">Track status of orders booked by you in the market</p>
                </div>
                <a href="{{ route('booker.orders.create') }}" class="btn btn-primary fw-bold shadow-sm">
                    <i class="fas fa-plus-circle me-1"></i> New Booking
                </a>
            </div>

            <!-- Filter Pills & Search -->
            <form action="{{ route('booker.orders.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('booker.orders.index', ['status' => 'all', 'time' => $time]) }}" 
                           class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            All
                        </a>
                        <a href="{{ route('booker.orders.index', ['status' => 'pending', 'time' => $time]) }}" 
                           class="btn btn-sm {{ $status === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' }}">
                            Booked (Pending)
                        </a>
                        <a href="{{ route('booker.orders.index', ['status' => 'confirmed', 'time' => $time]) }}" 
                           class="btn btn-sm {{ $status === 'confirmed' ? 'btn-success fw-bold' : 'btn-outline-secondary' }}">
                            Confirmed
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('booker.orders.index', ['status' => $status, 'time' => 'all']) }}" 
                           class="btn btn-sm {{ $time === 'all' ? 'btn-dark' : 'btn-outline-secondary' }}">
                            All Time
                        </a>
                        <a href="{{ route('booker.orders.index', ['status' => $status, 'time' => 'today']) }}" 
                           class="btn btn-sm {{ $time === 'today' ? 'btn-dark' : 'btn-outline-secondary' }}">
                            Today
                        </a>
                        <a href="{{ route('booker.orders.index', ['status' => $status, 'time' => 'this_month']) }}" 
                           class="btn btn-sm {{ $time === 'this_month' ? 'btn-dark' : 'btn-outline-secondary' }}">
                            This Month
                        </a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" class="form-control" placeholder="Search ref or shop..." value="{{ request('q') }}">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders List -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Ref #</th>
                                <th>Shop / Retailer</th>
                                <th>Date</th>
                                <th class="text-center">Items</th>
                                <th class="text-end">Total Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-center pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $o)
                                <tr>
                                    <td class="ps-3 font-monospace fw-bold text-primary">
                                        <a href="{{ route('booker.orders.show', $o->id) }}" class="text-decoration-none">
                                            {{ $o->invoice_no }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ optional($o->customer_relation)->customer_name ?? 'Walking' }}</div>
                                        <div class="text-muted small">
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ optional($o->customer_relation)->area ?? optional($o->customer_relation)->city ?? 'Karachi' }}
                                        </div>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $o->created_at ? $o->created_at->format('d/m/Y h:i A') : '-' }}
                                    </td>
                                    <td class="text-center font-monospace">
                                        {{ $o->items->count() }} items
                                    </td>
                                    <td class="text-end fw-bold font-monospace text-dark">
                                        Rs {{ number_format($o->total_net, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($o->sale_status === 'booked' || $o->is_booking)
                                            <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1">
                                                <i class="fas fa-bookmark me-1"></i> Booked
                                            </span>
                                        @elseif($o->sale_status === 'posted')
                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                <i class="fas fa-check-circle me-1"></i> Confirmed
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-1">
                                                {{ ucfirst($o->sale_status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <a href="{{ route('booker.orders.show', $o->id) }}" class="btn btn-sm btn-outline-primary" title="View Slip">
                                            <i class="fas fa-file-invoice me-1"></i> Slip
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-clipboard-list fs-2 mb-2 d-block opacity-50"></i>
                                        No bookings match your filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($orders->hasPages())
                <div class="card-footer bg-white border-top py-2 d-flex justify-content-end">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
