@extends('booker_portal.layout')

@section('title', 'New Order Booking')

@section('css')
<style>
    .product-row {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: border-color 0.15s ease;
    }
    .product-row:hover {
        border-color: #93c5fd;
    }
    .qty-input {
        text-align: center;
        font-weight: 700;
        font-family: monospace;
        font-size: 15px;
    }
    .floating-summary-bar {
        position: fixed;
        bottom: 62px; /* right above bottom nav */
        left: 0;
        right: 0;
        background: #1e293b;
        color: #ffffff;
        padding: 10px 16px;
        z-index: 1030;
        box-shadow: 0 -4px 15px rgba(0,0,0,0.15);
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">

        <form id="orderBookingForm" action="{{ route('booker.orders.store') }}" method="POST">
            @csrf

            <!-- Header Card: Shop Details -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-3">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-cart-plus text-primary me-2"></i>New Market Order Booking
                    </h5>
                    <span class="badge bg-primary-subtle text-primary font-monospace px-2 py-1">
                        Ref: {{ $nextBookingNo }}
                    </span>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <!-- Shop Selection -->
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                                <span>Select Retailer / Shop <span class="text-danger">*</span></span>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#newShopModal">
                                    <i class="fas fa-plus-circle me-1"></i>+ New Shop
                                </button>
                            </label>
                            <select name="customer_id" id="customer_id" class="form-select select2-shop" required>
                                <option value="">-- Choose Retailer / Shop --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" data-mobile="{{ $c->mobile }}" data-area="{{ $c->area }}" data-city="{{ $c->city }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->customer_name }} ({{ $c->customer_id ?? 'CUST-' . $c->id }}) - {{ $c->area ?: $c->city }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="shopInfoText" class="text-muted small mt-1 font-monospace" style="display:none;"></div>
                        </div>

                        <!-- Order Date -->
                        <div class="col-6 col-md-2">
                            <label class="form-label fw-bold text-dark small">Booking Date</label>
                            <input type="date" name="booking_date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Delivery Due Date -->
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-bold text-dark small">Delivery Due Date</label>
                            <input type="date" name="delivery_date" class="form-control" value="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        </div>

                        <!-- Notes / Reference -->
                        <div class="col-12">
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Order instructions / remarks / delivery note (optional)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Rows Container -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-5">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-boxes text-primary me-2"></i>Order Items
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary shadow-sm" id="btnAddRow">
                        <i class="fas fa-plus me-1"></i> Add Product
                    </button>
                </div>
                <div class="card-body p-3 p-md-4" id="itemsContainer">
                    <!-- Dynamic Product Rows will appear here -->
                </div>
                <div class="card-footer bg-light border-top py-3 text-center">
                    <button type="button" class="btn btn-outline-primary px-4 fw-bold" id="btnAddRowBottom">
                        <i class="fas fa-plus me-1"></i> Add Another Product
                    </button>
                </div>
            </div>

            <!-- Spacer for fixed bar -->
            <div style="height: 70px;"></div>

            <!-- Fixed Bottom Order Summary & Submit Bar -->
            <div class="floating-summary-bar">
                <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <div class="text-white-50" style="font-size: 10px; text-transform: uppercase;">Items / Qty</div>
                            <div class="fw-bold font-monospace" style="font-size: 13px;">
                                <span id="summaryTotalItems">0</span> items | <span id="summaryTotalCartons">0</span> ctn + <span id="summaryTotalLoose">0</span> pcs
                            </div>
                        </div>
                        <div class="border-start border-secondary ps-3">
                            <div class="text-white-50" style="font-size: 10px; text-transform: uppercase;">Net Amount</div>
                            <div class="fw-bold text-warning font-monospace" style="font-size: 18px;">
                                Rs <span id="summaryTotalNet">0.00</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-success btn-lg px-4 fw-bold shadow" id="btnSubmitOrder">
                            <i class="fas fa-check-circle me-1"></i> Confirm &amp; Book Order
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>

<!-- QUICK ADD SHOP MODAL -->
<div class="modal fade" id="newShopModal" tabindex="-1" aria-labelledby="newShopModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="quickShopForm" action="{{ route('booker.shops.quick-store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light border-bottom py-3">
                    <h6 class="modal-title fw-bold text-dark" id="newShopModalLabel">
                        <i class="fas fa-store text-primary me-2"></i>Quick Register New Retailer
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Shop / Store Name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" class="form-control" placeholder="e.g. Madina General Store" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Contact Person / Owner</label>
                        <input type="text" name="contact_person" class="form-control" placeholder="e.g. Haji Muhammad">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Mobile / Phone <span class="text-danger">*</span></label>
                        <input type="text" name="mobile" class="form-control font-monospace" placeholder="0300-1234567" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Area / Market</label>
                            <input type="text" name="area" class="form-control" placeholder="e.g. Saddar Bazaar">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">City</label>
                            <input type="text" name="city" class="form-control" placeholder="e.g. Karachi" value="Karachi">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Full Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Street / landmark..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="btnSaveQuickShop">Save &amp; Select Shop</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Product Row Template (Hidden) -->
<template id="productRowTemplate">
    <div class="product-row" data-row-idx="{INDEX}">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-secondary-subtle text-dark border font-monospace row-badge">Item #{ROW_NUM}</span>
            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-remove-row" title="Remove item">
                <i class="fas fa-trash-alt me-1"></i> Delete
            </button>
        </div>

        <div class="row g-2">
            <!-- Product Dropdown -->
            <div class="col-md-5">
                <label class="form-label small fw-bold text-dark mb-1">Product <span class="text-danger">*</span></label>
                <select name="product_id[]" class="form-select product-select" required>
                    <option value="">-- Choose Product --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" 
                                data-ppb="{{ $p->ppb }}" 
                                data-trade-price="{{ $p->trade_rate }}" 
                                data-retail-price="{{ $p->retail_rate }}" 
                                data-gst-rate="{{ $p->gst_rate }}" 
                                data-is-3rd="{{ $p->is_3rd }}"
                                data-size-mode="{{ $p->size_mode }}">
                            {{ $p->item_name }} (TP: {{ number_format($p->trade_rate, 2) }} | 1x{{ $p->ppb }})
                        </option>
                    @endforeach
                </select>
                <div class="pack-info text-muted small mt-1 font-monospace" style="font-size: 11px;"></div>
            </div>

            <!-- Carton Qty -->
            <div class="col-4 col-md-2">
                <label class="form-label small fw-bold text-dark mb-1">Cartons</label>
                <div class="input-group input-group-sm">
                    <input type="number" step="1" min="0" name="qty_carton[]" class="form-control qty-input input-carton" placeholder="0" value="0">
                </div>
            </div>

            <!-- Loose Qty -->
            <div class="col-4 col-md-2">
                <label class="form-label small fw-bold text-dark mb-1">Loose Pcs</label>
                <div class="input-group input-group-sm">
                    <input type="number" step="1" min="0" name="qty_loose[]" class="form-control qty-input input-loose" placeholder="0" value="0">
                </div>
            </div>

            <!-- Rate / Price -->
            <div class="col-4 col-md-3">
                <label class="form-label small fw-bold text-dark mb-1">Rate (TP)</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rs</span>
                    <input type="number" step="0.01" min="0" name="price[]" class="form-control font-monospace fw-bold input-price" placeholder="0.00" value="0.00">
                </div>
            </div>

            <!-- Discount % -->
            <div class="col-6 col-md-3">
                <label class="form-label small fw-bold text-secondary mb-1">Disc % / Scheme</label>
                <div class="input-group input-group-sm">
                    <input type="number" step="0.1" min="0" max="100" name="discount_percent[]" class="form-control input-disc" placeholder="0" value="0">
                    <span class="input-group-text">%</span>
                </div>
            </div>

            <!-- Line Total Display -->
            <div class="col-6 col-md-9 text-end pt-3">
                <span class="text-muted small me-2 font-monospace line-calc-detail">0 pcs &times; 0</span>
                <span class="fw-bold text-dark font-monospace fs-6">
                    Line Total: <span class="text-primary line-total-val">Rs 0.00</span>
                </span>
            </div>
        </div>
    </div>
</template>
@endsection

@section('js')
<script>
$(document).ready(function() {
    let rowIndex = 0;

    // Init Select2 for shop
    $('.select2-shop').select2({
        width: '100%',
        placeholder: 'Search shop name or area...'
    });

    $('#customer_id').on('change', function() {
        let opt = $(this).find('option:selected');
        let mobile = opt.data('mobile') || '';
        let area = opt.data('area') || '';
        let city = opt.data('city') || '';
        if (mobile || area) {
            $('#shopInfoText').html('<i class="fas fa-map-marker-alt text-danger me-1"></i> ' + (area ? area + ', ' : '') + city + ' | <i class="fas fa-phone text-success me-1"></i> ' + mobile).show();
        } else {
            $('#shopInfoText').hide();
        }
    });

    // Add Product Row Function
    function addProductRow() {
        rowIndex++;
        let template = $('#productRowTemplate').html();
        template = template.replace(/{INDEX}/g, rowIndex).replace(/{ROW_NUM}/g, rowIndex);
        let $row = $(template);
        $('#itemsContainer').append($row);

        // Init select2 on the new product select
        $row.find('.product-select').select2({
            width: '100%',
            placeholder: 'Search product...'
        });

        calculateSummary();
    }

    // Add initial 2 rows
    addProductRow();
    addProductRow();

    $('#btnAddRow, #btnAddRowBottom').on('click', function(e) {
        e.preventDefault();
        addProductRow();
    });

    // Remove row
    $(document).on('click', '.btn-remove-row', function(e) {
        e.preventDefault();
        let rowsCount = $('.product-row').length;
        if (rowsCount <= 1) {
            Swal.fire('Notice', 'At least one product item is required in the order.', 'info');
            return;
        }
        $(this).closest('.product-row').slideUp(200, function() {
            $(this).remove();
            calculateSummary();
        });
    });

    // On Product Change
    $(document).on('change', '.product-select', function() {
        let $row = $(this).closest('.product-row');
        let $opt = $(this).find('option:selected');

        let tradePrice = parseFloat($opt.data('trade-price')) || 0;
        let ppb = parseInt($opt.data('ppb')) || 1;
        let gstRate = parseFloat($opt.data('gst-rate')) || 18.0;
        let is3rd = parseInt($opt.data('is-3rd')) || 0;

        $row.find('.input-price').val(tradePrice.toFixed(2));
        $row.find('.pack-info').text('1 Carton = ' + ppb + ' pcs | GST: ' + gstRate + '% ' + (is3rd ? '(3rd Sched MRP)' : ''));

        calculateRow($row);
    });

    // On Qty, Price, Disc Change
    $(document).on('input change', '.input-carton, .input-loose, .input-price, .input-disc', function() {
        let $row = $(this).closest('.product-row');
        calculateRow($row);
    });

    function calculateRow($row) {
        let $opt = $row.find('.product-select option:selected');
        let ppb = parseInt($opt.data('ppb')) || 1;
        let sizeMode = $opt.data('size-mode') || 'by_pieces';
        let gstRate = parseFloat($opt.data('gst-rate')) || 18.0;
        let is3rd = parseInt($opt.data('is-3rd')) || 0;
        let retailPrice = parseFloat($opt.data('retail-price')) || 0;

        let cartons = parseFloat($row.find('.input-carton').val()) || 0;
        let loose = parseFloat($row.find('.input-loose').val()) || 0;
        let unitPrice = parseFloat($row.find('.input-price').val()) || 0;
        let discPct = parseFloat($row.find('.input-disc').val()) || 0;

        let totalPieces = (cartons * ppb) + loose;

        let lineGross = 0;
        if ((sizeMode === 'by_cartons' || sizeMode === 'by_bandal') && ppb > 1) {
            let piecePrice = unitPrice / ppb;
            lineGross = (cartons * unitPrice) + (loose * piecePrice);
        } else {
            lineGross = totalPieces * unitPrice;
        }

        let discAmt = (lineGross * discPct) / 100;
        let exclGst = Math.max(0, lineGross - discAmt);

        let mrpAmt = retailPrice * totalPieces;
        let gstBase = (is3rd && mrpAmt > 0) ? mrpAmt : exclGst;
        let gstAmt = gstBase * (gstRate / 100);

        let lineTotal = exclGst + gstAmt;

        $row.find('.line-total-val').text('Rs ' + lineTotal.toFixed(2));
        $row.find('.line-calc-detail').text(totalPieces + ' pcs (Net TP: ' + exclGst.toFixed(0) + ' + GST: ' + gstAmt.toFixed(0) + ')');

        $row.data('total-pieces', totalPieces);
        $row.data('cartons', cartons);
        $row.data('loose', loose);
        $row.data('line-total', lineTotal);

        calculateSummary();
    }

    function calculateSummary() {
        let sumPieces = 0;
        let sumCartons = 0;
        let sumLoose = 0;
        let sumNet = 0;
        let validItems = 0;

        $('.product-row').each(function() {
            let pid = $(this).find('.product-select').val();
            let ctn = parseFloat($(this).find('.input-carton').val()) || 0;
            let lse = parseFloat($(this).find('.input-loose').val()) || 0;
            let lineTot = $(this).data('line-total') || 0;

            if (pid && (ctn > 0 || lse > 0)) {
                validItems++;
                sumCartons += ctn;
                sumLoose += lse;
                sumNet += lineTot;
            }
        });

        $('#summaryTotalItems').text(validItems);
        $('#summaryTotalCartons').text(sumCartons);
        $('#summaryTotalLoose').text(sumLoose);
        $('#summaryTotalNet').text(sumNet.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }

    // Submit Order Form
    $('#orderBookingForm').on('submit', function(e) {
        e.preventDefault();
        let $form = $(this);
        let $btn = $('#btnSubmitOrder');

        let validCount = 0;
        $('.product-row').each(function() {
            let pid = $(this).find('.product-select').val();
            let ctn = parseFloat($(this).find('.input-carton').val()) || 0;
            let lse = parseFloat($(this).find('.input-loose').val()) || 0;
            if (pid && (ctn > 0 || lse > 0)) {
                validCount++;
            }
        });

        if (validCount === 0) {
            Swal.fire('Empty Order', 'Please add at least one product with Carton or Loose quantity greater than zero.', 'warning');
            return;
        }

        let origText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Booking...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Booked!',
                        text: res.message,
                        confirmButtonText: 'View Booking Receipt'
                    }).then(() => {
                        window.location.href = res.redirect_url;
                    });
                } else {
                    Swal.fire('Error', res.message || 'Could not book order.', 'error');
                }
            },
            error: function(xhr) {
                let msg = 'Failed to book order.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(origText);
            }
        });
    });

    // Quick Store Shop AJAX
    $('#quickShopForm').on('submit', function(e) {
        e.preventDefault();
        let $form = $(this);
        let $btn = $('#btnSaveQuickShop');
        let orig = $btn.text();

        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.success && res.customer) {
                    let c = res.customer;
                    let newOption = new Option(c.customer_name + ' (' + c.customer_id + ') - ' + (c.area || c.city), c.id, true, true);
                    $('#customer_id').append(newOption).trigger('change');

                    $('#newShopModal').modal('hide');
                    $form[0].reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Shop Added!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error registering shop.';
                Swal.fire('Error', msg, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).text(orig);
            }
        });
    });
});
</script>
@endsection
