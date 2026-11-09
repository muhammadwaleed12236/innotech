@foreach ($Purchase as $purchase)
    {{-- Table Row --}}
    <tr class="border-bottom-0">
        <td class="text-center" style="width: 35px; vertical-align: middle;">
            <input type="checkbox" class="select-purchase-row" value="{{ $purchase->id }}" style="cursor: pointer; width: 16px; height: 16px; display: inline-block; vertical-align: middle; margin: 0 auto !important;">
        </td>
        <td class="text-center text-nowrap" style="vertical-align: middle;">
            <span class="fw-bold text-dark">#{{ $purchase->id }}</span>
            <div class="text-muted small" style="font-size: 11px;">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</div>
        </td>
        <td style="vertical-align: middle;">
            <div class="font-monospace fw-bold text-dark">{{ $purchase->invoice_no }}</div>
            @if ($purchase->purchase_order_no)
                <div class="text-primary font-monospace small" style="font-size: 11px;"><i class="fas fa-file-alt me-1"></i>Ref: {{ $purchase->purchase_order_no }}</div>
            @endif
            @if ($purchase->note)
                <div class="text-muted small text-truncate" style="max-width: 140px; font-size: 10.5px;" title="{{ $purchase->note }}">
                    <i class="fas fa-comment-dots me-1"></i>{{ $purchase->note }}
                </div>
            @endif
        </td>
        <td style="vertical-align: middle;">
            <div class="d-flex align-items-center">
                <div class="avatar-circle bg-info-subtle text-info me-2 fw-bold d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                    style="width: 30px; height: 30px; font-size: 13px;">
                    {{ strtoupper(substr($purchase->vendor->name ?? 'V', 0, 1)) }}
                </div>
                <div>
                    <span class="fw-semibold text-dark d-block">{{ $purchase->vendor->name ?? 'N/A' }}</span>
                    @if ($purchase->warehouse)
                        <span class="text-muted small" style="font-size: 11px;">
                            <i class="fas fa-warehouse me-1 text-secondary"></i>{{ $purchase->warehouse->warehouse_name }}
                        </span>
                    @endif
                </div>
            </div>
        </td>
        <td class="text-center" style="vertical-align: middle;">
            @if ($purchase->status_purchase == 'draft')
                <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1">Draft</span>
            @elseif ($purchase->status_purchase == 'Returned')
                <span class="badge bg-danger text-white border border-danger px-2 py-1">Returned</span>
            @else
                <span class="badge bg-success-subtle text-success border border-success px-2 py-1">Approved</span>
            @endif
        </td>

        {{-- Trade Value --}}
        <td class="text-end" style="vertical-align: middle;">
            <div class="fw-bold text-dark font-monospace">Rs. {{ number_format($purchase->total_trade_value ?: $purchase->subtotal, 2) }}</div>
            @if (($purchase->total_retail_value ?? 0) > 0)
                <div class="text-muted small" style="font-size: 10px;" title="Retail Value">Ret: {{ number_format($purchase->total_retail_value, 2) }}</div>
            @endif
        </td>

        {{-- Trade Disc. --}}
        <td class="text-end" style="vertical-align: middle;">
            @php
                $inline_val = $purchase->items ? $purchase->items->sum('item_discount') : 0;
                $order_val = ($purchase->discount ?? 0) + ($purchase->additional_discount ?? 0);
                $tot_disc = $inline_val + $order_val;
            @endphp
            @if ($tot_disc > 0)
                <div class="fw-semibold text-danger font-monospace">Rs. {{ number_format($tot_disc, 2) }}</div>
                @if ($order_val > 0 && $inline_val > 0)
                    <div class="text-muted small" style="font-size: 10px;">Item: {{ number_format($inline_val, 0) }} + Ord: {{ number_format($order_val, 0) }}</div>
                @elseif ($order_val > 0)
                    <div class="text-muted small" style="font-size: 10px;">(Order Disc)</div>
                @endif
            @else
                <span class="text-muted font-monospace">0.00</span>
            @endif
        </td>

        {{-- Sales Tax (GST) --}}
        <td class="text-end" style="vertical-align: middle;">
            @if (($purchase->total_gst ?? 0) > 0)
                <div class="fw-bold text-info font-monospace">Rs. {{ number_format($purchase->total_gst, 2) }}</div>
                @if (($purchase->total_sales_tax_on_retail ?? 0) > 0 || ($purchase->total_sales_tax_on_trade ?? 0) > 0)
                    <div class="text-muted small" style="font-size: 9.5px;">
                        @if ($purchase->total_sales_tax_on_retail > 0)
                            <span>Ret: {{ number_format($purchase->total_sales_tax_on_retail, 0) }}</span>
                        @endif
                        @if ($purchase->total_sales_tax_on_trade > 0)
                            <span class="ms-1">Trd: {{ number_format($purchase->total_sales_tax_on_trade, 0) }}</span>
                        @endif
                    </div>
                @endif
            @else
                <span class="text-muted font-monospace">0.00</span>
            @endif
        </td>

        {{-- WHT Tax --}}
        <td class="text-end" style="vertical-align: middle;">
            @if (($purchase->wht_amount ?? 0) > 0)
                <div class="fw-bold text-dark font-monospace">Rs. {{ number_format($purchase->wht_amount, 2) }}</div>
                <span class="badge bg-warning-subtle text-dark border border-warning" style="font-size: 9.5px;">{{ number_format($purchase->wht_percent, 1) }}%</span>
            @else
                <span class="text-muted font-monospace">0.00</span>
            @endif
        </td>

        {{-- Freight / Shipping --}}
        <td class="text-end" style="vertical-align: middle;">
            @if (($purchase->extra_cost ?? 0) > 0)
                <span class="fw-semibold text-dark font-monospace">Rs. {{ number_format($purchase->extra_cost, 2) }}</span>
            @else
                <span class="text-muted font-monospace">0.00</span>
            @endif
        </td>

        {{-- Total Net Amount --}}
        <td class="text-end font-monospace" style="vertical-align: middle;">
            @if ($purchase->total_returned > 0)
                <div>
                    <small class="text-muted text-decoration-line-through">Rs. {{ number_format($purchase->net_amount, 2) }}</small>
                </div>
                <div class="text-primary fw-bold" style="font-size: 13.5px;">
                    Rs. {{ number_format($purchase->updated_net_amount, 2) }}
                </div>
                <small class="text-danger">(-{{ number_format($purchase->total_returned, 2) }})</small>
            @else
                <span class="fw-bold text-primary" style="font-size: 13.5px;">Rs. {{ number_format($purchase->net_amount, 2) }}</span>
            @endif
        </td>

        {{-- Paid Amount --}}
        <td class="text-end text-success fw-bold font-monospace" style="vertical-align: middle;">
            Rs. {{ number_format($purchase->paid_amount, 2) }}
        </td>

        {{-- Balance Due --}}
        <td class="text-end" style="vertical-align: middle;">
            @php
                $displayDue = $purchase->total_returned > 0 ? $purchase->updated_due_amount : $purchase->due_amount;
            @endphp
            @if ($displayDue > 0)
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill fw-bold font-monospace px-2 py-1">Rs. {{ number_format($displayDue, 2) }}</span>
            @else
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-bold px-2 py-1">Paid</span>
            @endif

            @if ($purchase->has_partial_return)
                <div class="mt-1"><span class="badge bg-warning text-dark" style="font-size: 9.5px;"><i class="fas fa-undo-alt me-1"></i> Partial Return</span></div>
            @elseif($purchase->is_fully_returned)
                <div class="mt-1"><span class="badge bg-danger text-white" style="font-size: 9.5px;">Fully Returned</span></div>
            @endif
        </td>

        {{-- Action --}}
        <td class="pe-3 text-center" style="vertical-align: middle;">
            <div class="dropdown">
                <button class="btn btn-premium-action dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v small me-1"></i> Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg rounded-3">
                    @can('purchases.edit')
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.edit', $purchase->id) }}">
                                <i class="fas fa-edit text-primary fa-fw"></i> Edit (Simple)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase-pos.index') }}?edit_id={{ $purchase->id }}">
                                <i class="fas fa-cash-register text-success fa-fw"></i> Edit (POS Purchase)
                            </a>
                        </li>
                    @endcan

                    @if ($purchase->status_purchase == 'draft')
                        @can('purchases.create')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-success confirm-purchase-btn" href="{{ route('purchase.confirm', $purchase->id) }}">
                                    <i class="fas fa-check-circle fa-fw"></i> Confirm Purchase
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                        @endcan
                    @endif

                    @if ($purchase->status_purchase != 'draft')
                        @can('purchases.view')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.invoice', $purchase->id) }}" target="_blank">
                                    <i class="fas fa-file-invoice text-info fa-fw"></i> View Invoice
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.receipt', $purchase->id) }}" target="_blank">
                                    <i class="fas fa-receipt text-secondary fa-fw"></i> View Receipt
                                </a>
                            </li>
                        @endcan
                        @can('purchases.create')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('purchase.return.show', $purchase->id) }}">
                                    <i class="fas fa-undo text-warning fa-fw"></i> Return
                                </a>
                            </li>
                        @endcan
                    @endif

                    @if ($purchase->status_purchase == 'draft')
                        @can('purchases.delete')
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('purchase.destroy', $purchase->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 delete-btn text-danger">
                                        <i class="fas fa-trash-alt fa-fw"></i> Delete
                                    </button>
                                </form>
                            </li>
                        @endcan
                    @endif
                </ul>
            </div>
        </td>
    </tr>
@endforeach
