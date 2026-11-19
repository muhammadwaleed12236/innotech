@extends('booker_portal.layout')

@section('title', 'Retail Shops')

@section('content')
<div class="row g-3">

    <!-- Header & Search -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-store text-primary me-2"></i>Retail Shops &amp; Customers
                    </h5>
                    <p class="text-muted small mb-0">Browse market retail shops, call retailers or book orders directly</p>
                </div>
                <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#newShopModal">
                    <i class="fas fa-plus-circle me-1"></i>+ New Retailer
                </button>
            </div>

            <!-- Search Form -->
            <form action="{{ route('booker.shops.index') }}" method="GET">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0" placeholder="Search by shop name, owner, area, city or phone..." value="{{ request('q') }}">
                    @if(request('q'))
                        <a href="{{ route('booker.shops.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                    <button class="btn btn-primary px-3" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Shops Grid -->
    <div class="col-12">
        <div class="row g-3">
            @forelse($shops as $shop)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">{{ $shop->customer_name }}</h6>
                                    @if($shop->contact_person)
                                        <div class="text-muted small">Prop: {{ $shop->contact_person }}</div>
                                    @endif
                                </div>
                                <span class="badge bg-light text-muted border font-monospace" style="font-size: 10px;">
                                    {{ $shop->customer_id ?? ('CUST-' . $shop->id) }}
                                </span>
                            </div>

                            <div class="small text-muted mb-3">
                                <div>
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                    {{ $shop->area ? $shop->area . ', ' : '' }}{{ $shop->city ?: 'Karachi' }}
                                </div>
                                @if($shop->address)
                                    <div class="text-truncate text-secondary" style="font-size: 11px;">
                                        {{ $shop->address }}
                                    </div>
                                @endif
                                <div class="font-monospace mt-1">
                                    <i class="fas fa-phone text-success me-1"></i>
                                    <a href="tel:{{ $shop->mobile }}" class="text-dark text-decoration-none fw-bold">{{ $shop->mobile }}</a>
                                </div>
                            </div>
                        </div>

                        <!-- Actions for this shop -->
                        <div class="border-top pt-2 d-flex justify-content-between align-items-center gap-1">
                            <div class="d-flex gap-1">
                                <a href="tel:{{ $shop->mobile }}" class="btn btn-sm btn-outline-success" title="Call">
                                    <i class="fas fa-phone"></i>
                                </a>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->mobile) }}" target="_blank" class="btn btn-sm btn-outline-success" title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>

                            <a href="{{ route('booker.orders.create') }}?customer_id={{ $shop->id }}" class="btn btn-sm btn-primary fw-bold">
                                <i class="fas fa-cart-plus me-1"></i> Book Order
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center text-muted">
                        <i class="fas fa-store-slash fs-2 mb-2 d-block opacity-50"></i>
                        No retail shops found. Tap "+ New Retailer" to register shops on your route.
                    </div>
                </div>
            @endforelse
        </div>

        @if($shops->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $shops->links() }}
            </div>
        @endif
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
                    <button type="submit" class="btn btn-primary fw-bold" id="btnSaveQuickShop">Save Retailer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
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
                if (res.success) {
                    $('#newShopModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Shop Added!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
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
