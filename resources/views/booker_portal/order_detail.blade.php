@extends('booker_portal.layout')

@section('title', 'Booking Slip - ' . $order->invoice_no)

@section('css')
<style>
    @media print {
        body {
            background: #ffffff !important;
            padding-bottom: 0 !important;
        }
        .portal-navbar, .bottom-nav, .no-print {
            display: none !important;
        }
        .slip-card {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
        }
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

        <!-- Top Actions (No Print) -->
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
            <a href="{{ route('booker.orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Orders
            </a>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-success" id="btnShareWhatsApp">
                    <i class="fab fa-whatsapp me-1"></i> WhatsApp
                </button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print Slip
                </button>
                <a href="{{ route('booker.orders.create') }}" class="btn btn-warning fw-bold">
                    <i class="fas fa-plus me-1"></i> New Order
                </a>
            </div>
        </div>

        <!-- Printable Order Slip Card -->
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 slip-card">
            
            <!-- Slip Header -->
            <div class="text-center border-bottom pb-3 mb-3">
                <span class="badge bg-primary-subtle text-primary font-monospace px-3 py-1 mb-2">
                    MARKET ORDER BOOKING SLIP
                </span>
                <h4 class="fw-bold mb-0 text-dark">{{ config('app.name', 'Innotech ERP') }}</h4>
                <p class="text-muted small mb-0">Field Order Booking System</p>
            </div>

            <!-- Booking & Retailer Meta -->
            <div class="row g-2 mb-3 small">
                <div class="col-6">
                    <div class="text-muted">Booking Reference:</div>
                    <strong class="font-monospace text-primary fs-6">{{ $order->invoice_no }}</strong>
                </div>
                <div class="col-6 text-end">
                    <div class="text-muted">Date &amp; Time:</div>
                    <strong class="text-dark">{{ $order->created_at ? $order->created_at->format('d/m/Y h:i A') : '-' }}</strong>
                </div>

                <div class="col-12"><hr class="my-1 border-secondary-subtle"></div>

                <div class="col-6">
                    <div class="text-muted">Retailer / Shop:</div>
                    <strong class="text-dark fs-6">{{ optional($order->customer_relation)->customer_name ?? 'Walking Retailer' }}</strong>
                    <div class="text-muted font-monospace">
                        <i class="fas fa-phone text-success me-1"></i>{{ optional($order->customer_relation)->mobile ?: '—' }}
                    </div>
                    <div class="text-muted">
                        <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ optional($order->customer_relation)->area ?? optional($order->customer_relation)->city ?? '' }}
                    </div>
                </div>

                <div class="col-6 text-end">
                    <div class="text-muted">Booked By (S.R):</div>
                    <strong class="text-dark">{{ optional($order->orderBooker)->name ?? optional($order->salesOfficer)->name ?? optional($booker)->name ?? 'Order Booker' }}</strong>
                    <div class="text-muted font-monospace">
                        ID: {{ optional($order->orderBooker)->code ?? optional($order->salesOfficer)->code ?? optional($booker)->code ?? '—' }}
                    </div>
                    @if($order->salesman)
                        <div class="text-muted mt-1">
                            Salesman: <strong>{{ $order->salesman->name }}</strong>
                        </div>
                    @endif
                    <div class="mt-1">
                        Status: 
                        @if($order->sale_status === 'booked' || $order->is_booking)
                            <span class="badge bg-warning text-dark">Booked</span>
                        @elseif($order->sale_status === 'posted')
                            <span class="badge bg-success">Confirmed</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($order->sale_status) }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-sm align-middle text-center mb-0" style="font-size: 12px;">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th class="text-start">Item Description</th>
                            <th style="width: 70px;">Qty</th>
                            <th style="width: 50px;">Pcs</th>
                            <th style="width: 70px;">Rate</th>
                            <th style="width: 50px;">Disc</th>
                            <th style="width: 60px;">GST</th>
                            <th style="width: 85px;" class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sumPieces = 0;
                            $sumGst = 0;
                        @endphp
                        @foreach($order->items as $idx => $item)
                            @php
                                $sumPieces += $item->total_pieces;
                                $sumGst += $item->sales_tax_amount;
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td class="text-start">
                                    <div class="fw-bold">{{ optional($item->product)->item_name ?? $item->product_name }}</div>
                                </td>
                                <td class="font-monospace fw-bold">{{ $item->qty }}</td>
                                <td class="font-monospace text-muted">{{ $item->total_pieces }}</td>
                                <td class="font-monospace">{{ number_format($item->price, 2) }}</td>
                                <td class="font-monospace text-danger">{{ $item->discount_percent > 0 ? $item->discount_percent . '%' : '-' }}</td>
                                <td class="font-monospace small text-muted">{{ number_format($item->sales_tax_amount, 1) }}</td>
                                <td class="text-end font-monospace fw-bold text-dark">{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="3" class="text-start">Total Items: {{ $order->items->count() }}</td>
                            <td class="font-monospace">{{ $sumPieces }}</td>
                            <td colspan="2" class="text-end">Total GST:</td>
                            <td class="font-monospace">{{ number_format($order->total_sales_tax ?? $sumGst, 2) }}</td>
                            <td class="text-end font-monospace text-primary fs-6">
                                Rs {{ number_format($order->total_net, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notes -->
            @if($order->reference)
                <div class="p-2 rounded bg-light small mb-3">
                    <strong>Remarks:</strong> {{ $order->reference }}
                </div>
            @endif

            <div class="text-center text-muted small mt-2">
                <em>* This is a market booking order slip. Subject to dispatch and final invoice confirmation. *</em>
            </div>

        </div>

    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    $('#btnShareWhatsApp').on('click', function(e) {
        e.preventDefault();
        let shopName = "{{ optional($order->customer_relation)->customer_name ?? 'Retailer' }}";
        let refNo = "{{ $order->invoice_no }}";
        let totalNet = "Rs {{ number_format($order->total_net, 2) }}";
        let itemsCount = "{{ $order->items->count() }}";
        let bookerName = "{{ optional($order->orderBooker)->name ?? optional($order->salesOfficer)->name ?? optional($booker)->name ?? 'Order Booker' }}";
        let shopMobile = "{{ optional($order->customer_relation)->mobile ?? '' }}";

        let text = `*Order Booking Confirmation*\n` +
                   `Ref: ${refNo}\n` +
                   `Shop: ${shopName}\n` +
                   `Booked By: ${bookerName}\n` +
                   `Items: ${itemsCount}\n` +
                   `Total Amount: ${totalNet}\n` +
                   `Thank you for your order!`;

        let url = "https://wa.me/" + (shopMobile ? shopMobile.replace(/[^0-9]/g, '') : '') + "?text=" + encodeURIComponent(text);
        window.open(url, '_blank');
    });
});
</script>
@endsection
