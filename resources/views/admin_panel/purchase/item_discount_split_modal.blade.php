{{-- Item Discount Split Modal & JavaScript Logic --}}
<div class="modal fade" id="itemDiscountSplitModal" tabindex="-1" aria-labelledby="itemDiscountSplitModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <h6 class="modal-title fw-bold" id="itemDiscountSplitModalLabel">
                    <i class="fas fa-sliders-h text-warning me-2"></i> Item Discount Split &amp; Heads Breakdown
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                {{-- Item Header Info Banner --}}
                <div class="p-2 px-3 rounded mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #2563eb;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="text-muted d-block" style="font-size: 11px;">Product:</span>
                            <strong class="text-dark fs-6" id="splitModalProductName">—</strong>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 11px;">Batch &amp; Unit:</span>
                            <span class="fw-semibold text-secondary" id="splitModalBatchUnit">—</span>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 11px;">Trade Value (Gross):</span>
                            <span class="fw-bold font-monospace text-dark fs-6" id="splitModalTradeVal">Rs 0.00</span>
                        </div>
                        <div>
                            <span class="text-muted d-block" style="font-size: 11px;">Total Item Discount:</span>
                            <span class="fw-bold font-monospace text-primary fs-6" id="splitModalTotalDisc">0% (Rs 0.00)</span>
                        </div>
                    </div>
                </div>

                {{-- Split Distribution Container --}}
                <div class="row g-3">
                    {{-- Section 1: Owner / Distributor Discount --}}
                    <div class="col-md-6">
                        <div class="card h-100 border-success-subtle shadow-sm" style="border-width: 1.5px;">
                            <div class="card-header bg-success-subtle text-success py-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold small">
                                    <i class="fas fa-user-shield me-1"></i> 1. Owner / Distributor Share (Humara)
                                </span>
                                <span class="badge bg-success text-white font-monospace" id="splitOwnerBadge">0%</span>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-2 align-items-center mb-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Percentage (%)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" min="0" max="100" class="form-control fw-bold text-success font-monospace" id="splitOwnerPct" placeholder="0">
                                            <span class="input-group-text fw-bold">%</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Amount (Rs)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rs</span>
                                            <input type="number" step="0.01" min="0" class="form-control fw-bold text-success font-monospace" id="splitOwnerAmt" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>

                                {{-- Optional Sub-heads under Owner Discount --}}
                                <div class="border rounded p-2 bg-light mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold text-secondary" style="font-size: 11px;">
                                            <i class="fas fa-tags me-1"></i> Sub-heads under Our Share (Optional)
                                        </span>
                                        <button type="button" class="btn btn-outline-success btn-xs py-0 px-2" id="btnAddOurSubhead" style="font-size: 10px;">
                                            <i class="fas fa-plus"></i> Add Sub-head
                                        </button>
                                    </div>
                                    <div id="splitOurSubheadsContainer" class="d-flex flex-column gap-1">
                                        {{-- Dynamic subheads rows (e.g. Commission, Target Scheme, Cash Disc) --}}
                                    </div>
                                    <div class="mt-2">
                                        <input type="text" class="form-control form-control-sm" id="splitOwnerNotes" placeholder="Notes / Maddain detail (e.g. 10% Commission, 5% Target Scheme)" style="font-size: 11px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Customer Discount --}}
                    <div class="col-md-6">
                        <div class="card h-100 border-primary-subtle shadow-sm" style="border-width: 1.5px;">
                            <div class="card-header bg-primary-subtle text-primary py-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold small">
                                    <i class="fas fa-users me-1"></i> 2. Customer Share (Market Pass-Through)
                                </span>
                                <span class="badge bg-primary text-white font-monospace" id="splitCustBadge">0%</span>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-2 align-items-center mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Percentage (%)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.01" min="0" max="100" class="form-control fw-bold text-primary font-monospace" id="splitCustPct" placeholder="0">
                                            <span class="input-group-text fw-bold">%</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Amount (Rs)</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rs</span>
                                            <input type="number" step="0.01" min="0" class="form-control fw-bold text-primary font-monospace" id="splitCustAmt" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2 rounded bg-light border text-muted" style="font-size: 11.5px;">
                                    <i class="fas fa-info-circle text-primary me-1"></i> 
                                    Yeh customer pass-through discount market mai sales invoices k waqt customers ko provide kia jata hai.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Custom Remaining Heads (Expiry, Return, Damage, Schemes etc.) --}}
                    <div class="col-12">
                        <div class="card border-warning-subtle shadow-sm" style="border-width: 1.5px;">
                            <div class="card-header bg-warning-subtle text-dark py-2 d-flex justify-content-between align-items-center flex-wrap gap-1">
                                <span class="fw-bold small">
                                    <i class="fas fa-layer-group me-1 text-warning-emphasis"></i> 3. Additional Custom Heads (Expiry, Return, Breakage, etc.)
                                </span>
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="badge bg-white text-dark border me-1 font-monospace" id="splitUnallocatedBadge">
                                        Unallocated: 0.00% (Rs 0.00)
                                    </span>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary btn-xs py-0 px-2 btn-add-preset-head" data-head="Expiry Allowance" style="font-size: 10.5px;">
                                            + Expiry
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-xs py-0 px-2 btn-add-preset-head" data-head="Return Allowance" style="font-size: 10.5px;">
                                            + Return
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-xs py-0 px-2 btn-add-preset-head" data-head="Breakage / Damage" style="font-size: 10.5px;">
                                            + Breakage
                                        </button>
                                        <button type="button" class="btn btn-primary btn-xs py-0 px-2" id="btnAddCustomHead" style="font-size: 10.5px;">
                                            <i class="fas fa-plus"></i> Custom Head
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle mb-0" id="splitCustomHeadsTable">
                                        <thead class="bg-light">
                                            <tr style="font-size: 11px;">
                                                <th>Head / Madd Name</th>
                                                <th style="width: 130px;" class="text-center">Rate (%)</th>
                                                <th style="width: 160px;" class="text-center">Amount (Rs)</th>
                                                <th style="width: 38px;" class="text-center"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="splitCustomHeadsBody">
                                            {{-- Dynamic custom head rows --}}
                                        </tbody>
                                    </table>
                                </div>
                                <div id="splitNoCustomHeadsMsg" class="text-center py-2 text-muted" style="font-size: 11px;">
                                    No custom heads added yet. Click "+ Expiry", "+ Return", or "+ Custom Head" to allocate remaining discount.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Live Summary and Validation Bar --}}
                <div class="p-2 px-3 rounded mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2" id="splitValidationBar" style="background: #f8fafc; border: 1.5px solid #cbd5e1;">
                    <div class="d-flex align-items-center gap-3 font-monospace small">
                        <span>Total Disc: <strong class="text-dark" id="barTotalDisc">0% (Rs 0.00)</strong></span>
                        <span>Allocated: <strong class="text-primary" id="barAllocatedDisc">0% (Rs 0.00)</strong></span>
                        <span>Difference: <strong id="barDiffDisc">0% (Rs 0.00)</strong></span>
                    </div>
                    <div id="splitStatusBadgeContainer">
                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Balanced 100%</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnResetSplit">
                    <i class="fas fa-undo me-1"></i> Reset to 100% Owner
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm fw-bold px-3" id="btnApplyItemSplit">
                        <i class="fas fa-check me-1"></i> Apply Split to Item
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function($) {
    let $activeRow = null;
    let modalTradeVal = 0;
    let modalTotalDiscPct = 0;
    let modalTotalDiscAmt = 0;

    function round2(num) {
        return Math.round((parseFloat(num) || 0) * 100) / 100;
    }

    function formatMoney(num) {
        num = round2(num);
        return 'Rs ' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Open Split Modal on Button Click
    $(document).on('click', '.btn-open-item-split', function(e) {
        e.preventDefault();
        $activeRow = $(this).closest('tr');
        if (!$activeRow.length) return;

        // Retrieve row info
        const productName = $activeRow.find('.product-search-input').val() || $activeRow.find('td:eq(1)').text().trim() || 'Product';
        const batchNo = $activeRow.find('.item-batch-no').val() || $activeRow.find('input[name="batch_no[]"]').val() || '—';
        const unit = $activeRow.find('.unit-toggle-btn').text().trim() || 'Carton';
        const qty = parseFloat($activeRow.find('.main-qty-input').val()) || 1;

        modalTradeVal = round2($activeRow.find('.item-trade-value').val());
        modalTotalDiscPct = round2($activeRow.find('.item-disc-percent').val());
        modalTotalDiscAmt = round2($activeRow.find('.item-disc-amt').val());

        // Fallback calculations if one is missing
        if (modalTotalDiscPct > 0 && modalTotalDiscAmt <= 0 && modalTradeVal > 0) {
            modalTotalDiscAmt = round2(modalTradeVal * (modalTotalDiscPct / 100));
        } else if (modalTotalDiscAmt > 0 && modalTotalDiscPct <= 0 && modalTradeVal > 0) {
            modalTotalDiscPct = round2((modalTotalDiscAmt / modalTradeVal) * 100);
        }

        $('#splitModalProductName').text(productName);
        $('#splitModalBatchUnit').text((batchNo !== '—' ? 'Batch: ' + batchNo + ' | ' : '') + qty + ' ' + unit);
        $('#splitModalTradeVal').text(formatMoney(modalTradeVal));
        $('#splitModalTotalDisc').text(modalTotalDiscPct.toFixed(2) + '% (' + formatMoney(modalTotalDiscAmt) + ')');

        // Load existing split values from row
        let ourPct = $activeRow.find('.item-our-disc-percent').val();
        let ourAmt = $activeRow.find('.item-our-disc-amount').val();
        let custPct = $activeRow.find('.item-customer-disc-percent').val();
        let custAmt = $activeRow.find('.item-customer-disc-amount').val();
        let customJson = $activeRow.find('.item-custom-disc-breakdown').val() || '[]';
        let ourSubJson = $activeRow.find('.item-our-disc-breakdown').val() || '[]';
        let notes = $activeRow.find('.item-our-disc-notes').val() || '';

        let customBreakdown = [];
        try { customBreakdown = typeof customJson === 'string' ? JSON.parse(customJson) : customJson; } catch(err) { customBreakdown = []; }

        let ourSubBreakdown = [];
        try { ourSubBreakdown = typeof ourSubJson === 'string' ? JSON.parse(ourSubJson) : ourSubJson; } catch(err) { ourSubBreakdown = []; }

        // If row has no split configured yet, default to 100% Owner
        const hasExistingSplit = (ourPct !== '' && parseFloat(ourPct) > 0) || 
                                (custPct !== '' && parseFloat(custPct) > 0) || 
                                (customBreakdown && customBreakdown.length > 0);

        if (!hasExistingSplit) {
            ourPct = modalTotalDiscPct;
            ourAmt = modalTotalDiscAmt;
            custPct = 0;
            custAmt = 0;
            customBreakdown = [];
            ourSubBreakdown = [];
        }

        $('#splitOwnerPct').val(parseFloat(ourPct) || 0);
        $('#splitOwnerAmt').val(parseFloat(ourAmt) || 0);
        $('#splitCustPct').val(parseFloat(custPct) || 0);
        $('#splitCustAmt').val(parseFloat(custAmt) || 0);
        $('#splitOwnerNotes').val(notes);

        // Render sub-heads
        renderOurSubheads(ourSubBreakdown);

        // Render custom heads
        renderCustomHeads(customBreakdown);

        // Run recalculation & validation
        recalcModalSplits();

        const modal = new bootstrap.Modal(document.getElementById('itemDiscountSplitModal'));
        modal.show();
    });

    // Render Our Sub-heads
    function renderOurSubheads(items) {
        const $cont = $('#splitOurSubheadsContainer');
        $cont.empty();
        if (!items || !items.length) return;

        items.forEach(item => {
            appendOurSubheadRow(item.head || '', item.percent || 0, item.amount || 0);
        });
    }

    function appendOurSubheadRow(headName, pct, amt) {
        const html = `
            <div class="d-flex align-items-center gap-1 our-subhead-row">
                <input type="text" class="form-control form-control-sm our-subhead-name" placeholder="Sub-head (e.g. Commission)" value="${headName}" style="font-size: 11px;">
                <div class="input-group input-group-sm" style="max-width: 95px;">
                    <input type="number" step="0.01" class="form-control text-end our-subhead-pct font-monospace" placeholder="%" value="${pct}" style="font-size: 11px;">
                    <span class="input-group-text p-1 small">%</span>
                </div>
                <div class="input-group input-group-sm" style="max-width: 110px;">
                    <span class="input-group-text p-1 small">Rs</span>
                    <input type="number" step="0.01" class="form-control text-end our-subhead-amt font-monospace" placeholder="Rs" value="${amt}" style="font-size: 11px;">
                </div>
                <button type="button" class="btn btn-outline-danger btn-xs py-0 px-1 btn-remove-subhead" title="Remove" style="font-size: 10px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        $('#splitOurSubheadsContainer').append(html);
    }

    $('#btnAddOurSubhead').on('click', function() {
        appendOurSubheadRow('Commission', 0, 0);
    });

    $(document).on('click', '.btn-remove-subhead', function() {
        $(this).closest('.our-subhead-row').remove();
    });

    // Render Custom Heads Table
    function renderCustomHeads(heads) {
        const $tbody = $('#splitCustomHeadsBody');
        $tbody.empty();

        if (!heads || !heads.length) {
            $('#splitNoCustomHeadsMsg').show();
            return;
        }

        $('#splitNoCustomHeadsMsg').hide();
        heads.forEach(h => {
            appendCustomHeadRow(h.head || '', h.percent || 0, h.amount || 0);
        });
    }

    function appendCustomHeadRow(headName, pct, amt) {
        $('#splitNoCustomHeadsMsg').hide();
        const html = `
            <tr class="custom-head-row">
                <td>
                    <input type="text" class="form-control form-control-sm custom-head-name fw-semibold" placeholder="Head Name (e.g. Expiry)" value="${headName}" style="font-size: 11.5px;">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" min="0" class="form-control text-end custom-head-pct font-monospace fw-bold text-dark" placeholder="0.00" value="${pct}">
                        <span class="input-group-text fw-bold">%</span>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rs</span>
                        <input type="number" step="0.01" min="0" class="form-control text-end custom-head-amt font-monospace fw-bold text-dark" placeholder="0.00" value="${amt}">
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-xs py-0 px-2 btn-remove-custom-head" title="Remove">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#splitCustomHeadsBody').append(html);
    }

    $('#btnAddCustomHead').on('click', function() {
        appendCustomHeadRow('Custom Allowance', 0, 0);
        recalcModalSplits();
    });

    $('.btn-add-preset-head').on('click', function() {
        const head = $(this).data('head');
        // If unallocated % exists, auto-fill it
        const currentAllocated = getAllocatedPct();
        const remainingPct = Math.max(0, round2(modalTotalDiscPct - currentAllocated));
        const remainingAmt = modalTradeVal > 0 ? round2(modalTradeVal * (remainingPct / 100)) : 0;
        appendCustomHeadRow(head, remainingPct, remainingAmt);
        recalcModalSplits();
    });

    $(document).on('click', '.btn-remove-custom-head', function() {
        $(this).closest('tr').remove();
        if ($('#splitCustomHeadsBody tr').length === 0) {
            $('#splitNoCustomHeadsMsg').show();
        }
        recalcModalSplits();
    });

    // Inputs Events in Modal
    $('#splitOwnerPct').on('input', function() {
        const p = parseFloat($(this).val()) || 0;
        const amt = modalTradeVal > 0 ? round2(modalTradeVal * (p / 100)) : 0;
        $('#splitOwnerAmt').val(amt);
        recalcModalSplits();
    });

    $('#splitOwnerAmt').on('input', function() {
        const a = parseFloat($(this).val()) || 0;
        const p = modalTradeVal > 0 ? round2((a / modalTradeVal) * 100) : 0;
        $('#splitOwnerPct').val(p);
        recalcModalSplits();
    });

    $('#splitCustPct').on('input', function() {
        const p = parseFloat($(this).val()) || 0;
        const amt = modalTradeVal > 0 ? round2(modalTradeVal * (p / 100)) : 0;
        $('#splitCustAmt').val(amt);
        recalcModalSplits();
    });

    $('#splitCustAmt').on('input', function() {
        const a = parseFloat($(this).val()) || 0;
        const p = modalTradeVal > 0 ? round2((a / modalTradeVal) * 100) : 0;
        $('#splitCustPct').val(p);
        recalcModalSplits();
    });

    $(document).on('input', '.custom-head-pct', function() {
        const p = parseFloat($(this).val()) || 0;
        const amt = modalTradeVal > 0 ? round2(modalTradeVal * (p / 100)) : 0;
        $(this).closest('tr').find('.custom-head-amt').val(amt);
        recalcModalSplits();
    });

    $(document).on('input', '.custom-head-amt', function() {
        const a = parseFloat($(this).val()) || 0;
        const p = modalTradeVal > 0 ? round2((a / modalTradeVal) * 100) : 0;
        $(this).closest('tr').find('.custom-head-pct').val(p);
        recalcModalSplits();
    });

    $(document).on('input', '.our-subhead-pct', function() {
        const p = parseFloat($(this).val()) || 0;
        const amt = modalTradeVal > 0 ? round2(modalTradeVal * (p / 100)) : 0;
        $(this).closest('.our-subhead-row').find('.our-subhead-amt').val(amt);
    });

    $(document).on('input', '.our-subhead-amt', function() {
        const a = parseFloat($(this).val()) || 0;
        const p = modalTradeVal > 0 ? round2((a / modalTradeVal) * 100) : 0;
        $(this).closest('.our-subhead-row').find('.our-subhead-pct').val(p);
    });

    function getAllocatedPct() {
        let total = (parseFloat($('#splitOwnerPct').val()) || 0) + (parseFloat($('#splitCustPct').val()) || 0);
        $('#splitCustomHeadsBody .custom-head-pct').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        return round2(total);
    }

    function getAllocatedAmt() {
        let total = (parseFloat($('#splitOwnerAmt').val()) || 0) + (parseFloat($('#splitCustAmt').val()) || 0);
        $('#splitCustomHeadsBody .custom-head-amt').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        return round2(total);
    }

    function recalcModalSplits() {
        const ownerPct = parseFloat($('#splitOwnerPct').val()) || 0;
        const custPct = parseFloat($('#splitCustPct').val()) || 0;

        $('#splitOwnerBadge').text(ownerPct.toFixed(2) + '%');
        $('#splitCustBadge').text(custPct.toFixed(2) + '%');

        const allocatedPct = getAllocatedPct();
        const allocatedAmt = getAllocatedAmt();

        const diffPct = round2(modalTotalDiscPct - allocatedPct);
        const diffAmt = round2(modalTotalDiscAmt - allocatedAmt);

        $('#splitUnallocatedBadge').text('Unallocated: ' + diffPct.toFixed(2) + '% (' + formatMoney(diffAmt) + ')');

        $('#barTotalDisc').text(modalTotalDiscPct.toFixed(2) + '% (' + formatMoney(modalTotalDiscAmt) + ')');
        $('#barAllocatedDisc').text(allocatedPct.toFixed(2) + '% (' + formatMoney(allocatedAmt) + ')');
        $('#barDiffDisc').text(diffPct.toFixed(2) + '% (' + formatMoney(diffAmt) + ')');

        const $statusCont = $('#splitStatusBadgeContainer');
        if (Math.abs(diffPct) < 0.01 && Math.abs(diffAmt) < 1.0) {
            $statusCont.html('<span class="badge bg-success py-1 px-2"><i class="fas fa-check-circle me-1"></i> Balanced 100%</span>');
            $('#barDiffDisc').removeClass('text-danger text-warning').addClass('text-success');
        } else if (diffPct > 0) {
            $statusCont.html('<span class="badge bg-warning text-dark py-1 px-2"><i class="fas fa-exclamation-triangle me-1"></i> ' + diffPct.toFixed(2) + '% Unallocated</span>');
            $('#barDiffDisc').removeClass('text-success text-danger').addClass('text-warning');
        } else {
            $statusCont.html('<span class="badge bg-danger py-1 px-2"><i class="fas fa-times-circle me-1"></i> ' + Math.abs(diffPct).toFixed(2) + '% Exceeds Total</span>');
            $('#barDiffDisc').removeClass('text-success text-warning').addClass('text-danger');
        }
    }

    // Reset Split to 100% Owner
    $('#btnResetSplit').on('click', function() {
        $('#splitOwnerPct').val(modalTotalDiscPct);
        $('#splitOwnerAmt').val(modalTotalDiscAmt);
        $('#splitCustPct').val(0);
        $('#splitCustAmt').val(0);
        $('#splitCustomHeadsBody').empty();
        $('#splitNoCustomHeadsMsg').show();
        $('#splitOurSubheadsContainer').empty();
        $('#splitOwnerNotes').val('');
        recalcModalSplits();
    });

    // Apply Split back to the Active Table Row or save to DB if in Reconciliation
    $('#btnApplyItemSplit').on('click', function() {
        const ownerPct = parseFloat($('#splitOwnerPct').val()) || 0;
        const ownerAmt = parseFloat($('#splitOwnerAmt').val()) || 0;
        const custPct = parseFloat($('#splitCustPct').val()) || 0;
        const custAmt = parseFloat($('#splitCustAmt').val()) || 0;
        const notes = $('#splitOwnerNotes').val() || '';

        // Collect custom heads
        const customHeads = [];
        $('#splitCustomHeadsBody tr.custom-head-row').each(function() {
            const hName = $(this).find('.custom-head-name').val().trim();
            const hPct = parseFloat($(this).find('.custom-head-pct').val()) || 0;
            const hAmt = parseFloat($(this).find('.custom-head-amt').val()) || 0;
            if (hName !== '' && (hPct > 0 || hAmt > 0)) {
                customHeads.push({ head: hName, percent: hPct, amount: hAmt });
            }
        });

        // Collect our sub-heads
        const ourSubheads = [];
        $('#splitOurSubheadsContainer .our-subhead-row').each(function() {
            const sName = $(this).find('.our-subhead-name').val().trim();
            const sPct = parseFloat($(this).find('.our-subhead-pct').val()) || 0;
            const sAmt = parseFloat($(this).find('.our-subhead-amt').val()) || 0;
            if (sName !== '' && (sPct > 0 || sAmt > 0)) {
                ourSubheads.push({ head: sName, percent: sPct, amount: sAmt });
            }
        });

        // If in Reconciliation Mode, save directly to server
        if (window.reconActiveItem && window.reconActiveItem.id) {
            const $btn = $('#btnApplyItemSplit');
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');
            $.ajax({
                url: "{{ route('report.discount_reconciliation.update_item_split') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    item_id: window.reconActiveItem.id,
                    our_disc_percent: ownerPct,
                    customer_disc_percent: custPct,
                    our_disc_notes: notes,
                    custom_disc_breakdown: customHeads,
                    our_disc_breakdown: ourSubheads
                },
                success: function(res) {
                    $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Apply Split to Item');
                    bootstrap.Modal.getInstance(document.getElementById('itemDiscountSplitModal')).hide();
                    window.reconActiveItem = null;
                    if (typeof loadReportData === 'function') {
                        loadReportData();
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Item Split Saved!',
                            text: res.msg,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Apply Split to Item');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error saving item split', 'error');
                    }
                }
            });
            return;
        }

        if (!$activeRow || !$activeRow.length) return;

        // Store into hidden inputs of row
        $activeRow.find('.item-our-disc-percent').val(ownerPct);
        $activeRow.find('.item-our-disc-amount').val(ownerAmt);
        $activeRow.find('.item-customer-disc-percent').val(custPct);
        $activeRow.find('.item-customer-disc-amount').val(custAmt);
        $activeRow.find('.item-custom-disc-breakdown').val(JSON.stringify(customHeads));
        $activeRow.find('.item-our-disc-breakdown').val(JSON.stringify(ourSubheads));
        $activeRow.find('.item-our-disc-notes').val(notes);

        // Render badge pill under discount input
        updateRowSplitBadge($activeRow, ownerPct, custPct, customHeads);

        bootstrap.Modal.getInstance(document.getElementById('itemDiscountSplitModal')).hide();
    });

    // Helper for Reconciliation Report to open modal
    window.openItemSplitModalForRecon = function(item, purchase) {
        $activeRow = null;
        window.reconActiveItem = item;

        modalTradeVal = round2(item.trade_value);
        modalTotalDiscPct = round2(item.company_disc_percent);
        modalTotalDiscAmt = round2(item.company_disc_amount);

        if (modalTotalDiscPct > 0 && modalTotalDiscAmt <= 0 && modalTradeVal > 0) {
            modalTotalDiscAmt = round2(modalTradeVal * (modalTotalDiscPct / 100));
        } else if (modalTotalDiscAmt > 0 && modalTotalDiscPct <= 0 && modalTradeVal > 0) {
            modalTotalDiscPct = round2((modalTotalDiscAmt / modalTradeVal) * 100);
        }

        $('#splitModalProductName').text(item.product_name);
        $('#splitModalBatchUnit').text((item.batch_no !== '—' ? 'Batch: ' + item.batch_no + ' | ' : '') + item.qty + ' ' + item.unit);
        $('#splitModalTradeVal').text(formatMoney(modalTradeVal));
        $('#splitModalTotalDisc').text(modalTotalDiscPct.toFixed(2) + '% (' + formatMoney(modalTotalDiscAmt) + ')');

        let ourPct = item.our_disc_percent;
        let ourAmt = item.our_disc_amount;
        let custPct = item.customer_disc_percent;
        let custAmt = item.customer_disc_amount;
        let customBreakdown = item.custom_disc_breakdown || [];
        let ourSubBreakdown = item.our_disc_breakdown || [];
        let notes = item.our_disc_notes || '';

        $('#splitOwnerPct').val(parseFloat(ourPct) || 0);
        $('#splitOwnerAmt').val(parseFloat(ourAmt) || 0);
        $('#splitCustPct').val(parseFloat(custPct) || 0);
        $('#splitCustAmt').val(parseFloat(custAmt) || 0);
        $('#splitOwnerNotes').val(notes);

        renderOurSubheads(ourSubBreakdown);
        renderCustomHeads(customBreakdown);
        recalcModalSplits();

        const modal = new bootstrap.Modal(document.getElementById('itemDiscountSplitModal'));
        modal.show();
    };

    // Helper to update split badge in row
    window.updateRowSplitBadge = function($row, ownerPct, custPct, customHeads) {
        const $cont = $row.find('.item-split-badge-container');
        if (!$cont.length) return;

        ownerPct = parseFloat(ownerPct) || 0;
        custPct = parseFloat(custPct) || 0;
        if (typeof customHeads === 'string') {
            try { customHeads = JSON.parse(customHeads); } catch(e) { customHeads = []; }
        }
        customHeads = customHeads || [];

        const hasSplit = (ownerPct > 0 || custPct > 0 || customHeads.length > 0);
        if (!hasSplit) {
            $cont.empty();
            $row.find('.btn-open-item-split').removeClass('btn-primary').addClass('btn-outline-primary');
            return;
        }

        let badgeParts = [];
        if (ownerPct > 0) badgeParts.push(`Own: ${ownerPct}%`);
        if (custPct > 0) badgeParts.push(`Cust: ${custPct}%`);
        customHeads.forEach(ch => {
            if (ch.percent > 0) badgeParts.push(`${ch.head}: ${ch.percent}%`);
        });

        const badgeHtml = `
            <div class="d-flex align-items-center gap-1" style="cursor: pointer;" title="Click to view/edit split">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 9px; line-height: 1.2; padding: 2px 4px; white-space: normal;">
                    ${badgeParts.join(' | ')}
                </span>
            </div>
        `;
        $cont.html(badgeHtml);
        $row.find('.btn-open-item-split').removeClass('btn-outline-primary').addClass('btn-primary');
    };

    // On clicking the badge pill, trigger split modal
    $(document).on('click', '.item-split-badge-container', function() {
        $(this).closest('tr').find('.btn-open-item-split').trigger('click');
    });

})(jQuery);
</script>
