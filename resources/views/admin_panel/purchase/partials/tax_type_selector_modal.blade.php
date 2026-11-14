<!-- Tax Type Selector Modal -->
<div class="modal fade" id="modalSelectTaxType" tabindex="-1" aria-labelledby="modalSelectTaxTypeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="modalSelectTaxTypeLabel">
                    <i class="fas fa-file-invoice-dollar"></i> Select Tax Schedule / Tax Type
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light bg-opacity-25">
                <p class="text-muted small mb-3">
                    Choose the tax schedule to apply to this purchase item. GST calculation base (Trade Value vs Retail MRP) and percentages will update automatically.
                </p>

                <div class="tax-type-options-list d-flex flex-column gap-2" id="taxTypeOptionsContainer">
                    @forelse($taxTypes ?? [] as $tt)
                        <label class="tax-type-card-label p-3 rounded-3 border bg-white d-flex align-items-start gap-3 position-relative shadow-xs cursor-pointer mb-0" for="tax_opt_{{ $tt->id }}" style="cursor: pointer;">
                            <input class="form-check-input mt-1 tax-type-radio" type="radio" name="selected_modal_tax_type" id="tax_opt_{{ $tt->id }}" value="{{ $tt->id }}"
                                   data-id="{{ $tt->id }}"
                                   data-code="{{ $tt->code }}"
                                   data-title="{{ $tt->title }}"
                                   data-is-third="{{ $tt->isThirdSchedule() ? '1' : '0' }}"
                                   data-gst="{{ $tt->getPurchaseGstRate() }}"
                                   data-wht="{{ $tt->getPurchaseAdvTaxRate() }}"
                                   data-applied="{{ $tt->tax_applied_on }}"
                                   {{ ($tt->is_default ?? false) ? 'checked' : '' }}>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold py-1 px-2">
                                            {{ $tt->code }}
                                        </span>
                                        <span class="fw-bold text-dark fs-6">{{ $tt->title }}</span>
                                    </div>
                                    @if($tt->isThirdSchedule())
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle py-1 px-2">
                                            <i class="fas fa-tag me-1"></i> 3rd Schedule (Retail MRP)
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info border border-info-subtle py-1 px-2">
                                            <i class="fas fa-boxes me-1"></i> Trade Value
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-3 text-secondary small pt-1">
                                    <span><strong>Purchase GST:</strong> {{ $tt->st_purchase_active ? number_format($tt->st_purchase_rate, 2) . '%' : '0% (Exempt)' }}</span>
                                    <span><strong>Adv. Tax (WHT):</strong> {{ $tt->adv_tax_purchase_active ? number_format($tt->adv_tax_purchase_rate, 2) . '%' : '0%' }}</span>
                                    @if($tt->notes)
                                        <span class="text-muted"><i class="fas fa-info-circle me-1"></i>{{ $tt->notes }}</span>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <p class="mb-2">No tax types configured.</p>
                            <a href="{{ route('tax-types.create') }}" target="_blank" class="btn btn-sm btn-outline-primary">Create Tax Type</a>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer bg-white py-3 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" id="btnApplyTaxToAllRows">
                        <i class="fas fa-check-double me-1"></i> Apply to All Rows
                    </button>
                    <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" id="btnApplyTaxToRow">
                        <i class="fas fa-check me-1"></i> Apply to Selected Row
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
