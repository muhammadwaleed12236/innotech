@extends('admin_panel.layout.app')

@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid py-4">

            <!-- Page Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">
                        <i class="fas fa-clipboard-check text-primary me-2"></i>Order Bookings &amp; Market Orders
                    </h4>
                    <p class="text-muted mb-0 small">
                        Live orders booked by Order Bookers from the market portal with instant confirmation &amp; itemized view.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('booker.dashboard') }}" target="_blank" class="btn btn-outline-primary px-3 shadow-sm fw-medium d-inline-flex align-items-center gap-2">
                        <i class="fas fa-mobile-alt"></i> Booker Portal (Field)
                    </a>
                    @can('bookings.create')
                        <a href="{{ route('sale.add') }}?type=booking" class="btn btn-primary px-3 shadow-sm fw-medium d-inline-flex align-items-center gap-2">
                            <i class="fas fa-plus"></i> + Add Manual Booking
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Alerts -->
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4 shadow-sm">
                    <i class="fas fa-check-circle fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4 shadow-sm">
                    <i class="fas fa-exclamation-circle fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- KPI Metric Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-left: 4px solid #0284c7 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Today's Bookings</div>
                                    <h4 class="fw-bold mb-0 text-dark mt-1">{{ $stats['today_count'] }}</h4>
                                    <div class="text-primary fw-bold small mt-1">Rs {{ number_format($stats['today_val'], 0) }}</div>
                                </div>
                                <div class="bg-white rounded-circle p-2 shadow-sm text-primary">
                                    <i class="fas fa-calendar-day fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 4px solid #f59e0b !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Pending Approval</div>
                                    <h4 class="fw-bold mb-0 text-dark mt-1">{{ $stats['pending_count'] }}</h4>
                                    <div class="text-warning-emphasis fw-bold small mt-1">Rs {{ number_format($stats['pending_val'], 0) }}</div>
                                </div>
                                <div class="bg-white rounded-circle p-2 shadow-sm text-warning">
                                    <i class="fas fa-hourglass-half fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 4px solid #16a34a !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Confirmed / Invoiced</div>
                                    <h4 class="fw-bold mb-0 text-dark mt-1">{{ $stats['confirmed_count'] }}</h4>
                                    <div class="text-success fw-bold small mt-1">Rs {{ number_format($stats['confirmed_val'], 0) }}</div>
                                </div>
                                <div class="bg-white rounded-circle p-2 shadow-sm text-success">
                                    <i class="fas fa-check-circle fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); border-left: 4px solid #9333ea !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted text-uppercase fw-semibold" style="font-size: 11px;">Filtered Total</div>
                                    <h4 class="fw-bold mb-0 text-dark mt-1">{{ $stats['filtered_count'] }}</h4>
                                    <div class="text-purple fw-bold small mt-1" style="color: #9333ea;">Rs {{ number_format($stats['filtered_val'], 0) }}</div>
                                </div>
                                <div class="bg-white rounded-circle p-2 shadow-sm" style="color: #9333ea;">
                                    <i class="fas fa-layer-group fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booker Performance Bar (Kis Kis Ne Kitna Book Kiya) -->
            @if(count($bookerBreakdown) > 0)
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold mb-0 text-dark small text-uppercase">
                                <i class="fas fa-user-tag text-primary me-1"></i> Order Booker Summary
                            </h6>
                            <span class="badge bg-light text-muted border small">{{ count($bookerBreakdown) }} Bookers with Orders</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($bookerBreakdown as $bk)
                                <a href="{{ route('bookings.index', array_merge(request()->except(['booker_id']), ['booker_id' => $bk['id']])) }}" 
                                   class="text-decoration-none badge p-2 d-flex align-items-center gap-2 border {{ ($filters['bookerId'] == $bk['id']) ? 'bg-primary text-white border-primary shadow-sm' : 'bg-light text-dark hover-shadow' }}"
                                   style="border-radius: 8px; font-weight: 500;">
                                    <span class="avatar-sm rounded-circle bg-white text-dark fw-bold d-inline-flex align-items-center justify-content-center px-1" style="width: 20px; height: 20px; font-size: 10px;">
                                        {{ strtoupper(substr($bk['name'], 0, 1)) }}
                                    </span>
                                    <span>
                                        <strong>{{ $bk['name'] }}</strong>
                                        @if($bk['code']) <small class="{{ ($filters['bookerId'] == $bk['id']) ? 'text-white-50' : 'text-muted' }}">({{ $bk['code'] }})</small> @endif
                                    </span>
                                    <span class="badge {{ ($filters['bookerId'] == $bk['id']) ? 'bg-white text-primary' : 'bg-primary text-white' }} px-2 py-0.5 rounded-pill" style="font-size: 10.5px;">
                                        {{ $bk['total_orders'] }} orders • Rs {{ number_format($bk['total_value'], 0) }}
                                    </span>
                                    @if($bk['pending_orders'] > 0)
                                        <span class="badge bg-warning text-dark px-1.5 py-0.5 rounded-pill" style="font-size: 10px;">
                                            {{ $bk['pending_orders'] }} pending
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                            @if($filters['bookerId'] && $filters['bookerId'] !== 'all')
                                <a href="{{ route('bookings.index', request()->except(['booker_id'])) }}" class="badge bg-danger text-white border-0 p-2 d-flex align-items-center gap-1 text-decoration-none">
                                    <i class="fas fa-times-circle"></i> Clear Booker Filter
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filter Controls Bar -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-body p-3">
                    <form action="{{ route('bookings.index') }}" method="GET" id="bookingFilterForm">
                        <div class="row g-2 align-items-end">

                            <!-- Order Booker Dropdown -->
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label small fw-bold text-dark mb-1">Filter by Order Booker</label>
                                <select name="booker_id" class="form-select form-select-sm select2">
                                    <option value="all" {{ $filters['bookerId'] === 'all' ? 'selected' : '' }}>-- All Order Bookers --</option>
                                    @foreach($allBookers as $ob)
                                        <option value="{{ $ob->id }}" {{ (string)$filters['bookerId'] === (string)$ob->id ? 'selected' : '' }}>
                                            {{ $ob->name }} ({{ $ob->code ?: 'ID: ' . $ob->id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Dropdown -->
                            <div class="col-md-2 col-sm-6">
                                <label class="form-label small fw-bold text-dark mb-1">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>All Statuses</option>
                                    <option value="booked" {{ $filters['status'] === 'booked' ? 'selected' : '' }}>Pending (Booked)</option>
                                    <option value="posted" {{ $filters['status'] === 'posted' ? 'selected' : '' }}>Confirmed (Posted)</option>
                                </select>
                            </div>

                            <!-- Date Filter Presets -->
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label small fw-bold text-dark mb-1">Time Period</label>
                                <select name="date_filter" id="dateFilterSelect" class="form-select form-select-sm">
                                    <option value="all" {{ $filters['dateFilter'] === 'all' ? 'selected' : '' }}>All Time</option>
                                    <option value="today" {{ $filters['dateFilter'] === 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="yesterday" {{ $filters['dateFilter'] === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                    <option value="this_week" {{ $filters['dateFilter'] === 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="this_month" {{ $filters['dateFilter'] === 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="custom" {{ $filters['dateFilter'] === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                                </select>
                            </div>

                            <!-- Custom Date Range (Conditional) -->
                            <div class="col-md-2 col-sm-6 custom-date-box" style="{{ $filters['dateFilter'] === 'custom' ? '' : 'display:none;' }}">
                                <label class="form-label small fw-bold text-dark mb-1">From</label>
                                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $filters['startDate'] }}">
                            </div>
                            <div class="col-md-2 col-sm-6 custom-date-box" style="{{ $filters['dateFilter'] === 'custom' ? '' : 'display:none;' }}">
                                <label class="form-label small fw-bold text-dark mb-1">To</label>
                                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $filters['endDate'] }}">
                            </div>

                            <!-- Search Box -->
                            <div class="col-md-2 col-sm-6">
                                <label class="form-label small fw-bold text-dark mb-1">Search</label>
                                <input type="text" name="q" class="form-control form-control-sm" placeholder="Ref / Shop / Area..." value="{{ $filters['search'] }}">
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-2 col-sm-6 d-flex gap-2">
                                <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">
                                    <i class="fas fa-filter me-1"></i> Apply
                                </button>
                                <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filters">
                                    <i class="fas fa-undo"></i>
                                </a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <!-- Bookings Main Table Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table id="bookings-table" class="table table-hover align-middle datanew" style="width:100%">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-3 text-secondary fw-semibold text-uppercase small">Booking Ref</th>
                                    <th class="py-3 text-secondary fw-semibold text-uppercase small">Order Booker (S.R)</th>
                                    <th class="py-3 text-secondary fw-semibold text-uppercase small">Retailer / Shop</th>
                                    <th class="py-3 text-secondary fw-semibold text-uppercase small">Booked Items Preview</th>
                                    <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Total Amount</th>
                                    <th class="py-3 text-secondary fw-semibold text-uppercase small text-center">Delivery Date</th>
                                    <th class="py-3 text-secondary fw-semibold text-uppercase small text-center">Status</th>
                                    <th class="py-3 pe-3 text-secondary fw-semibold text-uppercase small text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bookings as $booking)
                                    @php
                                        $booker = $booking->orderBooker ?? $booking->salesOfficer;
                                        $itemCount = $booking->items->count();
                                        $totalPieces = $booking->items->sum('total_pieces');
                                    @endphp
                                    <tr class="border-bottom">
                                        
                                        <!-- Booking Ref & Time -->
                                        <td class="ps-3">
                                            <div class="fw-bold font-monospace text-primary" style="font-size: 13.5px;">
                                                {{ $booking->invoice_no }}
                                            </div>
                                            <div class="text-muted small" style="font-size: 11px;">
                                                <i class="far fa-clock me-1 text-secondary"></i>{{ $booking->created_at ? $booking->created_at->format('d M, Y h:i A') : '—' }}
                                            </div>
                                            @if($booking->reference)
                                                <div class="text-secondary small mt-1 font-monospace" style="font-size: 10.5px;">
                                                    <i class="fas fa-sticky-note me-1 text-warning"></i>{{ Str::limit($booking->reference, 30) }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Order Booker (S.R) -->
                                        <td>
                                            @if ($booker)
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-circle rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center"
                                                         style="width: 32px; height: 32px; font-size: 12px;">
                                                        {{ strtoupper(substr($booker->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark" style="font-size: 13px;">{{ $booker->name }}</div>
                                                        <div class="text-muted small font-monospace" style="font-size: 11px;">
                                                            {{ $booker->code ?: 'ID: ' . $booker->id }}
                                                            @if($booker->mobile) • {{ $booker->mobile }} @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="badge bg-light text-muted border px-2 py-1">Direct / Admin</span>
                                            @endif
                                            @if ($booking->salesman)
                                                <div class="mt-1">
                                                    <span class="badge bg-secondary-subtle text-dark border px-2 py-0.5" style="font-size: 10px;">
                                                        <i class="fas fa-truck me-1 text-secondary"></i>{{ $booking->salesman->name }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Customer / Shop -->
                                        <td>
                                            <div class="fw-bold text-dark" style="font-size: 13.5px;">
                                                {{ optional($booking->customer_relation)->customer_name ?? 'Walking Retailer' }}
                                            </div>
                                            <div class="text-muted small" style="font-size: 11.5px;">
                                                @if(optional($booking->customer_relation)->mobile)
                                                    <i class="fas fa-phone-alt me-1 text-success small"></i>{{ $booking->customer_relation->mobile }}
                                                @endif
                                                @if(optional($booking->customer_relation)->area || optional($booking->customer_relation)->city)
                                                    <span class="badge bg-light text-secondary border ms-1" style="font-size: 10px;">
                                                        <i class="fas fa-map-marker-alt text-danger me-0.5"></i>{{ optional($booking->customer_relation)->area ?: optional($booking->customer_relation)->city }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Booked Items Preview -->
                                        <td>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold" style="font-size: 11px;">
                                                    <i class="fas fa-boxes me-1"></i>{{ $itemCount }} {{ Str::plural('Item', $itemCount) }}
                                                </span>
                                                @if($totalPieces > 0)
                                                    <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 11px;">
                                                        {{ $totalPieces }} Total Pcs
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-muted small" style="font-size: 11.5px; max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                @foreach ($booking->items->take(2) as $item)
                                                    {{ optional($item->product)->item_name ?? 'Product' }} ({{ $item->qty }}){{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                                @if($itemCount > 2)
                                                    <span class="text-primary fw-bold">+{{ $itemCount - 2 }} more</span>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 mt-1 btn-view-items" data-id="{{ $booking->id }}" style="font-size: 11px;">
                                                <i class="fas fa-list-ul me-1"></i>View Full Order Slip
                                            </button>
                                        </td>

                                        <!-- Total Net Amount -->
                                        <td class="text-end">
                                            <div class="fw-bold text-success font-monospace" style="font-size: 14.5px;">
                                                Rs {{ number_format($booking->total_net, 2) }}
                                            </div>
                                            @if($booking->total_reg_discount > 0 || $booking->total_extradiscount > 0)
                                                <div class="text-danger small" style="font-size: 11px;">
                                                    Disc: Rs {{ number_format(($booking->total_reg_discount + $booking->total_extradiscount), 2) }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Delivery Date -->
                                        <td class="text-center">
                                            @if($booking->due_date)
                                                <span class="badge bg-light text-dark border px-2 py-1 font-monospace" style="font-size: 11px;">
                                                    <i class="far fa-calendar-check me-1 text-primary"></i>{{ \Carbon\Carbon::parse($booking->due_date)->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="text-center">
                                            @if ($booking->sale_status === 'booked' || $booking->sale_status === 'quotation')
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2.5 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 600;">
                                                    <i class="fas fa-clock me-1"></i>Booked (Pending)
                                                </span>
                                            @elseif ($booking->sale_status === 'posted')
                                                <span class="badge bg-success-subtle text-success border border-success px-2.5 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 600;">
                                                    <i class="fas fa-check-circle me-1"></i>Confirmed / Sale
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary px-2.5 py-1.5 rounded-pill" style="font-size: 11px;">
                                                    {{ ucfirst($booking->sale_status) }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="pe-3 text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border dropdown-toggle px-2 py-1 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v text-secondary"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 py-1">
                                                    
                                                    <!-- View Details in Modal -->
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 btn-view-items" href="javascript:void(0)" data-id="{{ $booking->id }}">
                                                            <i class="fas fa-eye text-primary fa-fw"></i> View Full Slip / Details
                                                        </a>
                                                    </li>

                                                    <!-- Confirm Booking Action -->
                                                    @if ($booking->sale_status === 'booked' || $booking->sale_status === 'quotation')
                                                        @can('sales.create')
                                                            <li>
                                                                <form action="{{ route('sales.confirm', $booking->id) }}" method="POST" class="d-inline confirm-booking-form">
                                                                    @csrf
                                                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 text-success confirm-booking-btn">
                                                                        <i class="fas fa-check-circle text-success fa-fw"></i> Confirm &amp; Convert to Sale
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endcan
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.edit', $booking->id) }}">
                                                                <i class="fas fa-edit text-warning fa-fw"></i> Edit Booking
                                                            </a>
                                                        </li>
                                                    @endif

                                                    <li><hr class="dropdown-divider my-1"></li>

                                                    <!-- Printable Links -->
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('booker.orders.show', $booking->id) }}" target="_blank">
                                                            <i class="fas fa-file-invoice text-info fa-fw"></i> Printable Booking Slip
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.invoice', $booking->id) }}" target="_blank">
                                                            <i class="fas fa-receipt text-secondary fa-fw"></i> Standard Sale Invoice
                                                        </a>
                                                    </li>

                                                    @if($booking->sale_status === 'posted')
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('sales.dc', $booking->id) }}" target="_blank">
                                                                <i class="fas fa-shipping-fast text-warning fa-fw"></i> Delivery Challan (DC)
                                                            </a>
                                                        </li>
                                                    @endif

                                                </ul>
                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="fas fa-clipboard-list fs-1 text-muted mb-3 d-block opacity-50"></i>
                                            <h6 class="fw-bold">No Order Bookings Found</h6>
                                            <p class="small text-muted mb-0">Try adjusting your filters or book new orders from the Order Booker Portal.</p>
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

<!-- ========================================== -->
<!-- AJAX BOOKING DETAILS MODAL (Kya Kya Book Hua) -->
<!-- ========================================== -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            
            <!-- Modal Header -->
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white font-monospace fs-6 px-2.5 py-1" id="mBookingRef">BK-XXXXX</span>
                    <span class="badge" id="mBookingStatusBadge">Pending</span>
                </div>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.printModalSlip()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4" id="modalSlipContent">
                
                <!-- Loading State -->
                <div id="modalLoadingSpinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="text-muted small mt-2">Loading full order booking details...</div>
                </div>

                <!-- Content Container -->
                <div id="modalDetailContent" style="display: none;">
                    
                    <!-- Retailer & Booker Cards Row -->
                    <div class="row g-3 mb-3">
                        
                        <!-- Retailer Card -->
                        <div class="col-md-6">
                            <div class="card border border-primary-subtle rounded-3 bg-light h-100">
                                <div class="card-body p-3">
                                    <div class="text-muted small text-uppercase fw-bold mb-1">
                                        <i class="fas fa-store text-primary me-1"></i> Retailer / Shop Info
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1" id="mCustomerName">—</h5>
                                    <div class="small text-muted font-monospace" id="mCustomerCode">CUST-0000</div>
                                    <div class="small text-dark mt-1" id="mCustomerMobile">
                                        <i class="fas fa-phone-alt text-success me-1"></i> <span>—</span>
                                    </div>
                                    <div class="small text-muted" id="mCustomerAddress">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i> <span>—</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booker Card -->
                        <div class="col-md-6">
                            <div class="card border border-info-subtle rounded-3 bg-light h-100">
                                <div class="card-body p-3">
                                    <div class="text-muted small text-uppercase fw-bold mb-1">
                                        <i class="fas fa-user-edit text-info me-1"></i> Booked By (Order Booker)
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1" id="mBookerName">—</h5>
                                    <div class="small text-muted font-monospace" id="mBookerCode">ID: OB-000</div>
                                    <div class="small text-dark mt-1" id="mBookerMobile">
                                        <i class="fas fa-phone text-primary me-1"></i> <span>—</span>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <strong>Booking Date:</strong> <span id="mBookingDate">—</span><br>
                                        <strong>Delivery Date:</strong> <span id="mDeliveryDate" class="text-primary fw-bold">—</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Notes / Reference -->
                    <div class="alert alert-light border rounded-3 p-2 mb-3 small d-flex align-items-center gap-2" id="mReferenceBox" style="display: none !important;">
                        <i class="fas fa-info-circle text-warning fs-6"></i>
                        <div><strong>Order Remarks / Notes:</strong> <span id="mReferenceText">—</span></div>
                    </div>

                    <!-- Items Table -->
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="fas fa-boxes text-secondary me-1"></i> Booked Products List (<span id="mItemCount">0</span> items)
                    </h6>
                    <div class="table-responsive border rounded-3 mb-3">
                        <table class="table table-sm table-striped align-middle mb-0" style="font-size: 12.5px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-2">#</th>
                                    <th class="py-2">Item Description</th>
                                    <th class="py-2 text-center">Cartons</th>
                                    <th class="py-2 text-center">Loose Pcs</th>
                                    <th class="py-2 text-center">Total Pcs</th>
                                    <th class="py-2 text-end">Rate (TP)</th>
                                    <th class="py-2 text-center">Disc %</th>
                                    <th class="py-2 text-end">Tax</th>
                                    <th class="py-2 pe-3 text-end">Line Total</th>
                                </tr>
                            </thead>
                            <tbody id="mItemsTableBody">
                                <!-- Populated dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Financial Summary Footer -->
                    <div class="row justify-content-end">
                        <div class="col-md-5">
                            <div class="card border-0 bg-light rounded-3 p-3">
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">Gross Amount:</span>
                                    <strong id="mTotalGross">Rs 0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">Total Discount:</span>
                                    <strong class="text-danger" id="mTotalDiscount">- Rs 0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small border-bottom pb-1">
                                    <span class="text-muted">Sales Tax (GST):</span>
                                    <strong id="mTotalTax">Rs 0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between fs-6 fw-bold">
                                    <span class="text-dark">Net Order Value:</span>
                                    <span class="text-success" id="mTotalNet">Rs 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Modal Footer Actions -->
            <div class="modal-footer bg-light border-top py-2 px-4 d-flex justify-content-between">
                <a href="#" id="mBtnOpenFullSlip" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-external-link-alt me-1"></i> Open Field Slip
                </a>
                <div class="d-flex gap-2">
                    <div id="mConfirmFormWrapper">
                        <!-- Confirm button populated dynamically if status is booked -->
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {

    // Toggle custom date range fields
    $('#dateFilterSelect').on('change', function() {
        if ($(this).val() === 'custom') {
            $('.custom-date-box').show();
        } else {
            $('.custom-date-box').hide();
        }
    });

    // Initialize Select2 if available
    if ($('.select2').length > 0 && $.fn.select2) {
        $('.select2').select2({ width: '100%' });
    }

    // Initialize DataTable
    if ($.fn.DataTable.isDataTable('.datanew')) {
        $('.datanew').DataTable().destroy();
    }
    $('.datanew').DataTable({
        pageLength: 20,
        order: [],
        language: {
            search: "",
            searchPlaceholder: "Quick filter in table..."
        },
        dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });

    // Confirm Booking SweetAlert Action
    $(document).on('click', '.confirm-booking-btn', function(e) {
        e.preventDefault();
        let form = $(this).closest("form");

        Swal.fire({
            title: "Confirm Booking & Create Sale?",
            text: "This will convert the booking into a confirmed sale invoice, deduct warehouse stock, and post to customer ledger.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#16a34a",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, Confirm & Deduct Stock!"
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // View Items / Slip AJAX Modal Handler
    $(document).on('click', '.btn-view-items', function(e) {
        e.preventDefault();
        let bookingId = $(this).data('id');
        let modal = $('#bookingDetailsModal');

        $('#modalLoadingSpinner').show();
        $('#modalDetailContent').hide();
        modal.modal('show');

        $.ajax({
            url: "{{ url('bookings') }}/" + bookingId + "/details",
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (!res.success) {
                    Swal.fire('Error', 'Could not load booking details.', 'error');
                    modal.modal('hide');
                    return;
                }

                let b = res.booking;
                let items = res.items;

                // Set Header
                $('#mBookingRef').text(b.invoice_no);
                if (b.sale_status === 'booked' || b.sale_status === 'quotation') {
                    $('#mBookingStatusBadge').attr('class', 'badge bg-warning text-dark').html('<i class="fas fa-clock me-1"></i> Pending Approval');
                } else if (b.sale_status === 'posted') {
                    $('#mBookingStatusBadge').attr('class', 'badge bg-success text-white').html('<i class="fas fa-check-circle me-1"></i> Confirmed (Posted)');
                } else {
                    $('#mBookingStatusBadge').attr('class', 'badge bg-secondary text-white').text(b.sale_status);
                }

                // Retailer Details
                $('#mCustomerName').text(b.customer.name || 'Walking Retailer');
                $('#mCustomerCode').text(b.customer.code ? ('Code: ' + b.customer.code) : '');
                $('#mCustomerMobile span').text(b.customer.mobile || '—');
                $('#mCustomerAddress span').text((b.customer.area ? b.customer.area + ', ' : '') + (b.customer.city || '') + (b.customer.address ? ' (' + b.customer.address + ')' : ''));

                // Booker Details
                $('#mBookerName').text(b.booker.name || 'Direct / Admin');
                $('#mBookerCode').text(b.booker.code ? ('ID: ' + b.booker.code) : '');
                $('#mBookerMobile span').text(b.booker.mobile || '—');
                $('#mBookingDate').text(b.booking_date);
                $('#mDeliveryDate').text(b.delivery_date);

                // Reference / Notes
                if (b.reference) {
                    $('#mReferenceText').text(b.reference);
                    $('#mReferenceBox').removeAttr('style');
                } else {
                    $('#mReferenceBox').attr('style', 'display:none !important;');
                }

                // Items Count
                $('#mItemCount').text(items.length);

                // Items Table
                let rowsHtml = '';
                items.forEach(function(item, idx) {
                    rowsHtml += `
                        <tr>
                            <td class="ps-3 fw-bold text-muted">${idx + 1}</td>
                            <td>
                                <strong class="text-dark">${item.product_name}</strong>
                                <div class="text-muted font-monospace small" style="font-size:10.5px;">${item.item_code}</div>
                            </td>
                            <td class="text-center fw-bold text-primary">${item.cartons > 0 ? item.cartons : '—'}</td>
                            <td class="text-center fw-bold text-secondary">${item.loose > 0 ? item.loose : '—'}</td>
                            <td class="text-center font-monospace">${item.total_pieces}</td>
                            <td class="text-end font-monospace">Rs ${parseFloat(item.price).toFixed(2)}</td>
                            <td class="text-center text-danger font-monospace">${item.discount_percent > 0 ? item.discount_percent + '%' : '—'}</td>
                            <td class="text-end font-monospace text-muted">Rs ${parseFloat(item.sales_tax_amount).toFixed(2)}</td>
                            <td class="pe-3 text-end fw-bold text-dark font-monospace">Rs ${parseFloat(item.total).toFixed(2)}</td>
                        </tr>
                    `;
                });
                $('#mItemsTableBody').html(rowsHtml || '<tr><td colspan="9" class="text-center py-3 text-muted">No items recorded in this booking</td></tr>');

                // Financials
                $('#mTotalGross').text('Rs ' + parseFloat(b.total_gross).toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#mTotalDiscount').text('- Rs ' + parseFloat(b.total_discount).toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#mTotalTax').text('Rs ' + parseFloat(b.total_sales_tax).toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#mTotalNet').text('Rs ' + parseFloat(b.total_net).toLocaleString(undefined, {minimumFractionDigits: 2}));

                // Footer Links & Buttons
                $('#mBtnOpenFullSlip').attr('href', b.actions.slip_url);

                if (b.sale_status === 'booked' || b.sale_status === 'quotation') {
                    $('#mConfirmFormWrapper').html(`
                        <form action="${b.actions.confirm_url}" method="POST" class="d-inline confirm-booking-form">
                            @csrf
                            <button type="button" class="btn btn-success btn-sm fw-bold confirm-booking-btn">
                                <i class="fas fa-check-circle me-1"></i> Confirm &amp; Deduct Stock
                            </button>
                        </form>
                    `);
                } else {
                    $('#mConfirmFormWrapper').html(`
                        <a href="${b.actions.invoice_url}" target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-invoice me-1"></i> View Invoice
                        </a>
                    `);
                }

                $('#modalLoadingSpinner').hide();
                $('#modalDetailContent').show();
            },
            error: function() {
                modal.modal('hide');
                Swal.fire('Error', 'Unable to fetch booking details. Please try again.', 'error');
            }
        });
    });

    // Helper to print modal
    window.printModalSlip = function() {
        let slipUrl = $('#mBtnOpenFullSlip').attr('href');
        if (slipUrl && slipUrl !== '#') {
            window.open(slipUrl, '_blank');
        } else {
            window.print();
        }
    };

});
</script>
@endsection
