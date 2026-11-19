@extends('booker_portal.layout')

@section('title', 'Booker Dashboard')

@section('content')
<div class="row g-3">

    <!-- Welcome & Booker Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 text-white p-3 p-md-4" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-primary p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 54px; height: 54px;">
                        <i class="fas fa-user-edit fs-4"></i>
                    </div>
                    <div>
                        <span class="badge bg-white-50 text-white font-monospace text-uppercase mb-1" style="font-size: 10px; background-color: rgba(255,255,255,0.25);">
                            Order Booker (S.R)
                        </span>
                        <h4 class="fw-bold mb-0 text-white">{{ $booker->name }}</h4>
                        <div class="small text-white-50 mt-1">
                            <span class="font-monospace fw-bold me-2"><i class="fas fa-id-badge me-1"></i>{{ $booker->code }}</span>
                            <span><i class="fas fa-phone me-1"></i>{{ $booker->mobile }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('booker.orders.create') }}" class="btn btn-warning fw-bold px-3 py-2 shadow d-flex align-items-center gap-2">
                        <i class="fas fa-plus-circle fs-5"></i>
                        <span>Book New Order</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="col-6 col-md-3">
        <div class="card card-stat border-0 shadow-sm bg-white p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Today's Orders</span>
                <span class="badge bg-primary-subtle text-primary rounded-pill"><i class="fas fa-calendar-day"></i></span>
            </div>
            <h3 class="fw-bold text-primary mb-0 font-monospace">{{ $stats['today_orders_count'] ?? 0 }}</h3>
            <div class="text-muted small mt-1">
                Value: <strong class="text-dark">Rs {{ number_format($stats['today_orders_val'] ?? 0, 0) }}</strong>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-stat border-0 shadow-sm bg-white p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Month Value</span>
                <span class="badge bg-success-subtle text-success rounded-pill"><i class="fas fa-chart-line"></i></span>
            </div>
            <h3 class="fw-bold text-success mb-0 font-monospace">Rs {{ number_format($stats['month_orders_val'] ?? 0, 0) }}</h3>
            <div class="text-muted small mt-1">
                <span class="fw-bold text-dark">{{ $stats['month_orders_count'] ?? 0 }}</span> orders this month
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-stat border-0 shadow-sm bg-white p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">Pending Bookings</span>
                <span class="badge bg-warning-subtle text-warning rounded-pill"><i class="fas fa-clock"></i></span>
            </div>
            <h3 class="fw-bold text-warning mb-0 font-monospace">{{ $stats['pending_count'] ?? 0 }}</h3>
            <div class="text-muted small mt-1">
                Awaiting admin confirmation
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card card-stat border-0 shadow-sm bg-white p-3 h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase">My Shops</span>
                <span class="badge bg-info-subtle text-info rounded-pill"><i class="fas fa-store"></i></span>
            </div>
            <h3 class="fw-bold text-info mb-0 font-monospace">{{ $stats['total_shops'] ?? 0 }}</h3>
            <div class="text-muted small mt-1">
                <a href="{{ route('booker.shops.index') }}" class="text-decoration-none">View all retail shops &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Quick Action Banner -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary-subtle text-primary rounded-3 p-3">
                        <i class="fas fa-cart-plus fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Ready to book an order for a retailer?</h6>
                        <p class="text-muted small mb-0">Select a retail shop, add carton/loose quantities with auto GST &amp; scheme calculation.</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('booker.orders.create') }}" class="btn btn-primary px-4 fw-bold">
                        <i class="fas fa-plus me-1"></i> Start Booking
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-history text-primary me-2"></i>Recent Bookings
                </h6>
                <a href="{{ route('booker.orders.index') }}" class="btn btn-sm btn-outline-secondary">
                    View All Orders &rarr;
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Booking #</th>
                                <th>Shop / Retailer</th>
                                <th>Date</th>
                                <th class="text-center">Items</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-center pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $order)
                                <tr>
                                    <td class="ps-3 font-monospace fw-bold text-primary">
                                        <a href="{{ route('booker.orders.show', $order->id) }}" class="text-decoration-none">
                                            {{ $order->invoice_no }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ optional($order->customer_relation)->customer_name ?? 'Walking Retailer' }}</div>
                                        <small class="text-muted">{{ optional($order->customer_relation)->area ?? optional($order->customer_relation)->city ?? '' }}</small>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $order->created_at ? $order->created_at->format('d M, h:i A') : '-' }}
                                    </td>
                                    <td class="text-center font-monospace">
                                        {{ $order->items->count() }}
                                    </td>
                                    <td class="text-end fw-bold font-monospace text-dark">
                                        Rs {{ number_format($order->total_net, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($order->sale_status === 'booked' || $order->is_booking)
                                            <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1">
                                                <i class="fas fa-bookmark me-1"></i> Booked
                                            </span>
                                        @elseif($order->sale_status === 'posted')
                                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                <i class="fas fa-check-circle me-1"></i> Confirmed
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-1">
                                                {{ ucfirst($order->sale_status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <a href="{{ route('booker.orders.show', $order->id) }}" class="btn btn-sm btn-light border" title="View Slip">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No bookings yet today. Tap "Book New Order" to start taking orders in the field!
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
@endsection
