@extends('admin_panel.layout.app')

@section('content')
<style>
    .tax-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
    }
    .tax-box-title {
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        margin-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 4px;
    }
    .tax-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 1px dashed #f1f5f9;
        gap: 8px;
    }
    .tax-row:last-child {
        border-bottom: none;
    }
    .tax-row label {
        margin: 0;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e293b;
        cursor: pointer;
    }
    .tax-input-pct {
        width: 82px !important;
        height: 30px !important;
        font-size: 0.82rem !important;
        padding: 2px 6px !important;
    }
    .tax-radio-group .form-check {
        margin-bottom: 0;
        padding-left: 1.4rem;
    }
    .tax-radio-group .form-check-label {
        font-size: 0.8rem;
        color: #334155;
    }
    .form-control-xs {
        height: 30px !important;
        font-size: 0.82rem !important;
        padding: 2px 8px !important;
    }
</style>

<div class="container-fluid px-3 py-3">

    <!-- Compact Header -->
    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tax-types.index') }}" class="btn btn-outline-secondary btn-sm py-1 px-2" title="Back to Tax Types">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-edit text-warning"></i> Edit Tax Type: <span class="text-primary font-monospace">[{{ $taxType->code }}]</span> {{ $taxType->title }}
            </h5>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small me-2 d-none d-lg-inline"><kbd class="bg-light text-dark border">Tab</kbd> to move &bull; <kbd class="bg-light text-dark border">Enter</kbd> to save</span>
            <a href="{{ route('tax-types.index') }}" class="btn btn-outline-secondary btn-sm py-1 px-3">Cancel</a>
            <button type="submit" form="taxTypeForm" class="btn btn-warning text-dark btn-sm px-4 fw-bold shadow-xs">
                <i class="fas fa-save me-1"></i> Update Tax Type
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-2 small shadow-xs" role="alert">
            <div class="fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Please check errors:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('tax-types.update', $taxType->id) }}" method="POST" id="taxTypeForm">
        @csrf
        @method('PUT')

        <!-- Top Section: Code, Title & Base in 1 Compact Card -->
        <div class="tax-box mb-2.5 p-2 px-3 shadow-xs">
            <div class="row g-2 align-items-center">
                <!-- Tax Code -->
                <div class="col-md-2 col-sm-3 col-4">
                    <label class="form-label text-dark fw-bold small mb-1">Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" id="tax_code" class="form-control form-control-xs font-monospace fw-bold text-primary" value="{{ old('code', $taxType->code) }}" required>
                </div>

                <!-- Tax Title -->
                <div class="col-md-5 col-sm-9 col-8">
                    <label class="form-label text-dark fw-bold small mb-1">Tax Type Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="tax_title" class="form-control form-control-xs fw-bold text-dark" value="{{ old('title', $taxType->title) }}" required>
                </div>

                <!-- Primary GST Base (Trade vs Retail) -->
                <div class="col-md-3 col-sm-8 col-12">
                    <label class="form-label text-dark fw-bold small mb-1">Tax Applied On (Base):</label>
                    <div class="d-flex align-items-center gap-3 p-1 px-2 bg-light rounded border" style="height: 30px;">
                        <div class="form-check form-check-inline m-0">
                            <input class="form-check-input" type="radio" name="tax_applied_on" id="tax_on_trade" value="trade_value" {{ old('tax_applied_on', $taxType->tax_applied_on) == 'trade_value' ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold text-dark" for="tax_on_trade">Trade Value</label>
                        </div>
                        <div class="form-check form-check-inline m-0">
                            <input class="form-check-input" type="radio" name="tax_applied_on" id="tax_on_retail" value="retail_value" {{ old('tax_applied_on', $taxType->tax_applied_on) == 'retail_value' ? 'checked' : '' }}>
                            <label class="form-check-label small fw-bold text-warning-emphasis" for="tax_on_retail">Retail (3rd Sched)</label>
                        </div>
                    </div>
                </div>

                <!-- Status & Default Toggles -->
                <div class="col-md-2 col-sm-4 col-12 d-flex align-items-center gap-3 pt-md-3">
                    <div class="form-check form-switch m-0" title="Set as default for new products & purchases">
                        <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $taxType->is_default ? '1' : '0') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label small fw-semibold" for="is_default">Default</label>
                    </div>
                    <div class="form-check form-switch m-0" title="Active in dropdowns">
                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', $taxType->status ? '1' : '0') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label small fw-semibold" for="status">Active</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 Columns Grid: Compact & Balanced -->
        <div class="row g-2 mb-2.5">
            <!-- Left Column: Sales Tax (GST) & Further Tax -->
            <div class="col-lg-6">
                <!-- Sales Tax Box -->
                <div class="tax-box mb-2 shadow-xs">
                    <div class="tax-box-title d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-percent text-success me-1"></i> Sales Tax & Duties</span>
                        <span class="badge bg-light text-muted border" style="font-size: 10px;">GST Rules</span>
                    </div>

                    <!-- Purchase Sales Tax -->
                    <div class="tax-row">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="st_purchase_active" id="st_purchase_active" value="1" {{ old('st_purchase_active', $taxType->st_purchase_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="st_purchase_active">Sales Tax (Purchase Invoice)</label>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="small text-muted">Tax %:</span>
                            <input type="number" step="0.01" min="0" max="100" name="st_purchase_rate" id="st_purchase_rate" class="form-control form-control-xs text-end fw-bold tax-input-pct" value="{{ old('st_purchase_rate', $taxType->st_purchase_rate) }}">
                        </div>
                    </div>

                    <!-- Sales Invoice Tax -->
                    <div class="tax-row">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="st_sale_active" id="st_sale_active" value="1" {{ old('st_sale_active', $taxType->st_sale_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="st_sale_active">Sales Tax (Sales Invoice)</label>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="small text-muted">Tax %:</span>
                            <input type="number" step="0.01" min="0" max="100" name="st_sale_rate" id="st_sale_rate" class="form-control form-control-xs text-end fw-bold tax-input-pct" value="{{ old('st_sale_rate', $taxType->st_sale_rate) }}">
                        </div>
                    </div>

                    <!-- MRP Tax (3rd Schedule) -->
                    <div class="tax-row">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="mrp_tax_active" id="mrp_tax_active" value="1" {{ old('mrp_tax_active', $taxType->mrp_tax_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="mrp_tax_active">MRP Tax Applied (3rd Sched)</label>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="small text-muted">Tax %:</span>
                            <input type="number" step="0.01" min="0" max="100" name="mrp_tax_rate" id="mrp_tax_rate" class="form-control form-control-xs text-end fw-bold tax-input-pct" value="{{ old('mrp_tax_rate', $taxType->mrp_tax_rate) }}">
                        </div>
                    </div>

                    <!-- Excise Duty -->
                    <div class="tax-row">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="excise_duty_active" id="excise_duty_active" value="1" {{ old('excise_duty_active', $taxType->excise_duty_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="excise_duty_active">Excise Duty (Purchase / Sale)</label>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="small text-muted">Tax %:</span>
                            <input type="number" step="0.01" min="0" max="100" name="excise_duty_rate" id="excise_duty_rate" class="form-control form-control-xs text-end fw-bold tax-input-pct" value="{{ old('excise_duty_rate', $taxType->excise_duty_rate) }}">
                        </div>
                    </div>
                </div>

                <!-- Un-registered S. Tax Box -->
                <div class="tax-box shadow-xs">
                    <div class="tax-box-title d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-user-tag text-warning me-1"></i> Un-Registered S. Tax (Further Tax)</span>
                    </div>

                    <div class="tax-row">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="unreg_st_active" id="unreg_st_active" value="1" {{ old('unreg_st_active', $taxType->unreg_st_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="unreg_st_active">Un-Registered S. Tax Applied</label>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="small text-muted">Tax %:</span>
                            <input type="number" step="0.01" min="0" max="100" name="unreg_st_rate" id="unreg_st_rate" class="form-control form-control-xs text-end fw-bold tax-input-pct" value="{{ old('unreg_st_rate', $taxType->unreg_st_rate) }}">
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 mt-1 small bg-light p-2 rounded">
                        <div class="d-flex align-items-center gap-2 tax-radio-group">
                            <span class="text-muted fw-bold">Applied On:</span>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="unreg_st_applied_on" id="unreg_purch" value="purchases" {{ old('unreg_st_applied_on', $taxType->unreg_st_applied_on) == 'purchases' ? 'checked' : '' }}>
                                <label class="form-check-label" for="unreg_purch">Purch</label>
                            </div>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="unreg_st_applied_on" id="unreg_sales" value="sales" {{ old('unreg_st_applied_on', $taxType->unreg_st_applied_on) == 'sales' ? 'checked' : '' }}>
                                <label class="form-check-label" for="unreg_sales">Sales</label>
                            </div>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="unreg_st_applied_on" id="unreg_both" value="both" {{ old('unreg_st_applied_on', $taxType->unreg_st_applied_on) == 'both' ? 'checked' : '' }}>
                                <label class="form-check-label" for="unreg_both">Both</label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 tax-radio-group">
                            <span class="text-muted fw-bold">Client:</span>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="unreg_client_mode" id="cli_add_unreg" value="add_unreg" {{ old('unreg_client_mode', $taxType->unreg_client_mode) == 'add_unreg' ? 'checked' : '' }}>
                                <label class="form-check-label" for="cli_add_unreg">Add UnReg</label>
                            </div>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="unreg_client_mode" id="cli_less_reg" value="less_reg" {{ old('unreg_client_mode', $taxType->unreg_client_mode) == 'less_reg' ? 'checked' : '' }}>
                                <label class="form-check-label" for="cli_less_reg">Less Reg</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Advance Tax (WHT) & Valuation Base -->
            <div class="col-lg-6">
                <!-- Advance Tax Box -->
                <div class="tax-box mb-2 shadow-xs">
                    <div class="tax-box-title d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-hand-holding-usd text-danger me-1"></i> Advance Tax / WHT</span>
                        <span class="badge bg-light text-muted border" style="font-size: 10px;">Income Tax</span>
                    </div>

                    <!-- Adv Tax (Purchase) -->
                    <div class="tax-row">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="adv_tax_purchase_active" id="adv_tax_purchase_active" value="1" {{ old('adv_tax_purchase_active', $taxType->adv_tax_purchase_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="adv_tax_purchase_active">Adv. Tax (Purchase Inv)</label>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="small text-muted">Tax %:</span>
                            <input type="number" step="0.01" min="0" max="100" name="adv_tax_purchase_rate" id="adv_tax_purchase_rate" class="form-control form-control-xs text-end fw-bold tax-input-pct" value="{{ old('adv_tax_purchase_rate', $taxType->adv_tax_purchase_rate) }}">
                        </div>
                    </div>

                    <!-- Adv Tax (Sale) -->
                    <div class="tax-row">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="adv_tax_sale_active" id="adv_tax_sale_active" value="1" {{ old('adv_tax_sale_active', $taxType->adv_tax_sale_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="adv_tax_sale_active">Adv. Tax (Sale Inv)</label>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-success small fw-semibold">Filer:</span>
                                <input type="number" step="0.01" min="0" max="100" name="adv_tax_sale_filer_rate" id="adv_tax_sale_filer_rate" class="form-control form-control-xs text-end fw-bold text-success" style="width: 70px;" value="{{ old('adv_tax_sale_filer_rate', $taxType->adv_tax_sale_filer_rate) }}">
                                <span class="small text-muted">%</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-danger small fw-semibold">Non-Filer:</span>
                                <input type="number" step="0.01" min="0" max="100" name="adv_tax_sale_non_filer_rate" id="adv_tax_sale_non_filer_rate" class="form-control form-control-xs text-end fw-bold text-danger" style="width: 70px;" value="{{ old('adv_tax_sale_non_filer_rate', $taxType->adv_tax_sale_non_filer_rate) }}">
                                <span class="small text-muted">%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Adv Tax Applied On Base Radios -->
                    <div class="pt-2">
                        <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.78rem;">Adv. Tax Applied On Base:</label>
                        <div class="d-flex flex-wrap gap-2 tax-radio-group p-1.5 bg-light rounded border">
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="adv_tax_applied_on" id="adv_net_tp" value="net_tp" {{ old('adv_tax_applied_on', $taxType->adv_tax_applied_on) == 'net_tp' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-primary" for="adv_net_tp">Net TP (Disc & Taxes)</label>
                            </div>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="adv_tax_applied_on" id="adv_gross" value="gross_amount" {{ old('adv_tax_applied_on', $taxType->adv_tax_applied_on) == 'gross_amount' ? 'checked' : '' }}>
                                <label class="form-check-label" for="adv_gross">Gross Amt</label>
                            </div>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="adv_tax_applied_on" id="adv_gross_tp" value="gross_tp" {{ old('adv_tax_applied_on', $taxType->adv_tax_applied_on) == 'gross_tp' ? 'checked' : '' }}>
                                <label class="form-check-label" for="adv_gross_tp">Gross TP (Disc Excl)</label>
                            </div>
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="radio" name="adv_tax_applied_on" id="adv_net_retail" value="net_retail" {{ old('adv_tax_applied_on', $taxType->adv_tax_applied_on) == 'net_retail' ? 'checked' : '' }}>
                                <label class="form-check-label" for="adv_net_retail">Net Retail</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date & Policy Box -->
                <div class="tax-box shadow-xs">
                    <div class="row g-2 align-items-center">
                        <div class="col-sm-5 col-6">
                            <label class="form-label text-dark fw-bold small mb-1">Applied From Date:</label>
                            <input type="date" name="applied_from" id="applied_from" class="form-control form-control-xs" value="{{ old('applied_from', $taxType->applied_from ? $taxType->applied_from->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-sm-7 col-6">
                            <label class="form-label text-dark fw-bold small mb-1">Include Special Disc:</label>
                            <div class="form-check form-switch pt-1 m-0">
                                <input class="form-check-input" type="checkbox" name="include_special_disc" id="include_special_disc" value="1" {{ old('include_special_disc', $taxType->include_special_disc ? '1' : '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label small" for="include_special_disc">Yes, include in calc</label>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <input type="text" name="notes" class="form-control form-control-xs" placeholder="Notes / Description (Optional)" value="{{ old('notes', $taxType->notes) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Compact Live Simulator & Actions Bar -->
        <div class="tax-box p-2.5 px-3 bg-white shadow-xs d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap small">
                <span class="fw-bold text-dark"><i class="fas fa-calculator text-primary me-1"></i> Live Example (Rs 1000 Trade, Rs 1200 Retail):</span>
                <span>GST: <strong id="simGst">Rs 171.00</strong></span>
                <span>WHT: <strong id="simWht">Rs 1.12</strong></span>
                <span class="badge bg-primary-subtle text-primary border fs-7 font-monospace py-1 px-2" id="simCost">Cost: Rs 1,122.12</span>
            </div>

            <div class="d-flex align-items-center gap-2 ms-auto">
                <a href="{{ route('tax-types.index') }}" class="btn btn-outline-secondary btn-sm px-3">Cancel</a>
                <button type="submit" class="btn btn-warning text-dark btn-sm px-4 fw-bold shadow-xs">
                    <i class="fas fa-save me-1"></i> Update Tax Type
                </button>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
    function updateCompactSimulator() {
        const isTrade = $('#tax_on_trade').is(':checked');
        const stPurchaseActive = $('#st_purchase_active').is(':checked');
        const stPurchaseRate = stPurchaseActive ? (parseFloat($('#st_purchase_rate').val()) || 0) : 0;
        const mrpTaxActive = $('#mrp_tax_active').is(':checked');
        const mrpTaxRate = mrpTaxActive ? (parseFloat($('#mrp_tax_rate').val()) || 0) : 0;
        const advTaxActive = $('#adv_tax_purchase_active').is(':checked');
        const advTaxRate = advTaxActive ? (parseFloat($('#adv_tax_purchase_rate').val()) || 0) : 0;

        const tradeVal = 1000.00;
        const retailVal = 1200.00;
        const discVal = 50.00;
        const valAfterDisc = tradeVal - discVal; // 950

        let gstAmount = 0;
        if (!isTrade || mrpTaxActive) {
            const rate = mrpTaxActive ? mrpTaxRate : stPurchaseRate;
            gstAmount = (retailVal * rate) / 100;
            $('#simGst').html(`Rs ${gstAmount.toFixed(2)} <span class="text-warning-emphasis" style="font-size:10px;">(${rate}% on Retail)</span>`);
        } else {
            gstAmount = (valAfterDisc * stPurchaseRate) / 100;
            $('#simGst').html(`Rs ${gstAmount.toFixed(2)} <span class="text-muted" style="font-size:10px;">(${stPurchaseRate}% on Trade)</span>`);
        }

        const totalBeforeWht = valAfterDisc + gstAmount;
        const whtAmount = advTaxActive ? ((totalBeforeWht * advTaxRate) / 100) : 0;
        $('#simWht').text(`Rs ${whtAmount.toFixed(2)} (${advTaxRate}%)`);

        const cost = valAfterDisc + gstAmount + whtAmount;
        $('#simCost').text(`Cost: Rs ${cost.toFixed(2)}`);
    }

    $('#mrp_tax_active').on('change', function() {
        if ($(this).is(':checked')) {
            $('#tax_on_retail').prop('checked', true);
            if (parseFloat($('#mrp_tax_rate').val()) === 0) {
                $('#mrp_tax_rate').val($('#st_purchase_rate').val());
            }
        }
        updateCompactSimulator();
    });

    $('#tax_on_retail').on('change', function() {
        if ($(this).is(':checked') && !$('#mrp_tax_active').is(':checked')) {
            $('#mrp_tax_active').prop('checked', true);
            if (parseFloat($('#mrp_tax_rate').val()) === 0) {
                $('#mrp_tax_rate').val($('#st_purchase_rate').val());
            }
        }
        updateCompactSimulator();
    });

    $('#tax_on_trade').on('change', function() {
        if ($(this).is(':checked')) {
            $('#mrp_tax_active').prop('checked', false);
        }
        updateCompactSimulator();
    });

    $(document).on('keydown', '#taxTypeForm input:not([type="submit"]):not([type="button"])', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const $inputs = $('#taxTypeForm').find('input:visible:not([disabled])');
            const nextIdx = $inputs.index(this) + 1;
            if (nextIdx < $inputs.length) {
                $inputs.eq(nextIdx).focus().select();
            } else {
                $('#taxTypeForm').submit();
            }
        }
    });

    $(document).on('input change', '#taxTypeForm input', function() {
        updateCompactSimulator();
    });

    $(document).ready(function() {
        updateCompactSimulator();
        $('#tax_title').focus();
    });
</script>
@endpush
@endsection
