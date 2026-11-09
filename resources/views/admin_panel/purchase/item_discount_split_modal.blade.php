@php
    $dbCustomDiscountHeads = \App\Models\Setting::get('custom_discount_heads', []);
    if (is_string($dbCustomDiscountHeads)) {
        $dbCustomDiscountHeads = json_decode($dbCustomDiscountHeads, true) ?: [];
    }
    if (!is_array($dbCustomDiscountHeads)) {
        $dbCustomDiscountHeads = [];
    }
@endphp
{{-- Item Discount Split Modal & JavaScript Logic --}}
<div class="modal fade" id="itemDiscountSplitModal" tabindex="-1" aria-labelledby="itemDiscountSplitModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <h6 class="modal-title fw-bold" id="itemDiscountSplitModalLabel">
                    <i class="fas fa-sliders-h text-warning me-2"></i> Item Discount Split &amp; Heads Breakdown
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
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

                    {{-- Section 3: Standard & Custom Discount Heads Breakdown --}}
                    <div class="col-12">
                        <div class="card border-warning-subtle shadow-sm" style="border-width: 1.5px;">
                            <div class="card-header bg-warning-subtle text-dark py-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold small">
                                        <i class="fas fa-layer-group me-1 text-warning-emphasis"></i> 3. Standard Discount Heads Breakdown
                                    </span>
                                    <span class="badge bg-dark text-white font-monospace" id="splitActiveHeadsBadge" style="font-size: 10px;">0 Active</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="input-group input-group-sm" style="max-width: 210px;">
                                        <span class="input-group-text bg-white py-0 px-2 text-muted"><i class="fas fa-search" style="font-size: 10px;"></i></span>
                                        <input type="text" id="splitCustomHeadSearch" class="form-control form-control-sm py-0" placeholder="Filter heads..." style="font-size: 11px;">
                                    </div>
                                    <span class="badge bg-white text-dark border font-monospace" id="splitUnallocatedBadge">
                                        Unallocated: 0.00% (Rs 0.00)
                                    </span>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary btn-xs py-0 px-2" id="btnToggleActiveOnly" style="font-size: 10.5px;">
                                            <i class="fas fa-filter"></i> Active Only
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-xs py-0 px-2" id="btnClearCustomHeads" style="font-size: 10.5px;" title="Reset all head rates to 0">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                        <button type="button" class="btn btn-primary btn-xs py-0 px-2" id="btnAddCustomHead" style="font-size: 10.5px;">
                                            <i class="fas fa-plus"></i> Custom Head
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                                    <table class="table table-sm table-hover table-bordered align-middle mb-0" id="splitCustomHeadsTable">
                                        <thead class="bg-light sticky-top" style="z-index: 2;">
                                            <tr style="font-size: 11px;">
                                                <th style="width: 34px;" class="text-center bg-light">#</th>
                                                <th class="bg-light">Standard Head / Madd Name</th>
                                                <th style="width: 125px;" class="text-center bg-light">Rate (%)</th>
                                                <th style="width: 155px;" class="text-center bg-light">Amount (Rs)</th>
                                                <th style="width: 70px;" class="text-center bg-light">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="splitCustomHeadsBody">
                                            {{-- Pre-populated static head rows + any extra custom rows --}}
                                        </tbody>
                                    </table>
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
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal" id="btnCancelItemSplit">Cancel</button>
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

    function safeOpenSplitModal() {
        const el = document.getElementById('itemDiscountSplitModal');
        if (!el) return;

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                const inst = bootstrap.Modal.getOrCreateInstance ? bootstrap.Modal.getOrCreateInstance(el) : (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el));
                if (inst) {
                    inst.show();
                    return;
                }
            } catch (e) {
                console.warn('Bootstrap 5 modal show error:', e);
            }
        }
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            try {
                $('#itemDiscountSplitModal').modal('show');
                return;
            } catch (e) {
                console.warn('jQuery modal show error:', e);
            }
        }
        $('#itemDiscountSplitModal').addClass('show').css('display', 'block').attr('aria-hidden', 'false');
        if (!$('.modal-backdrop').length) {
            $('body').append('<div class="modal-backdrop fade show"></div>');
        }
        $('body').addClass('modal-open');
    }

    function safeCloseSplitModal() {
        const el = document.getElementById('itemDiscountSplitModal');
        if (!el) return;

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                const inst = bootstrap.Modal.getInstance ? bootstrap.Modal.getInstance(el) : null;
                if (inst) inst.hide();
            } catch (e) {
                console.warn('Bootstrap 5 modal hide error:', e);
            }
        }
        if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            try {
                $('#itemDiscountSplitModal').modal('hide');
            } catch (e) {
                console.warn('jQuery modal hide error:', e);
            }
        }
        setTimeout(function() {
            const $m = $('#itemDiscountSplitModal');
            $m.removeClass('show').css('display', 'none').attr('aria-hidden', 'true');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css({ 'overflow': '', 'padding-right': '' });
        }, 120);
    }

    // Dismiss buttons listener
    $(document).on('click', '#btnCancelItemSplit, #itemDiscountSplitModal [data-dismiss="modal"], #itemDiscountSplitModal [data-bs-dismiss="modal"]', function(e) {
        safeCloseSplitModal();
    });

    // Open Split Modal on Button Click
    $(document).on('click', '.btn-open-item-split', function(e) {
        e.preventDefault();
        $('#purchaseTableBody tr').removeClass('split-active-row');
        $activeRow = $(this).closest('tr');
        if (!$activeRow.length) return;
        $activeRow.addClass('split-active-row');
        $('#itemDiscountSplitModal').data('active-row-index', $activeRow.index());

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

        safeOpenSplitModal();
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

    // Pre-defined Base Static Standard Discount Heads
    const BASE_STATIC_DISCOUNT_HEADS = [
        "Del Norm Discount",
        "Del Discount on Return expiry",
        "Del Display Discount",
        "Del Extra Discount",
        "Del Freight Discount",
        "Del Market Price Discount",
        "Del Trade Offer",
        "Del Trade Activity Discount",
        "Del Direct Discount",
        "Del Distributor Comp",
        "Del DSF Income",
        "Del DSF Salary",
        "Del Freight Charges Claim",
        "Del JBP Claim",
        "Del Perf. Store Claims",
        "Del Price Adjustment Discount",
        "Del Progressive Rebate and ROI"
    ];

    // Load saved custom heads from DB and localStorage
    let savedCustomHeadsList = @json($dbCustomDiscountHeads ?? []);
    try {
        const localList = JSON.parse(localStorage.getItem('innotech_custom_discount_heads') || '[]');
        if (Array.isArray(localList)) {
            localList.forEach(lh => {
                if (lh && typeof lh === 'string' && !savedCustomHeadsList.some(sh => sh.toLowerCase() === lh.toLowerCase())) {
                    savedCustomHeadsList.push(lh.trim());
                }
            });
        }
    } catch(e) {}

    function getAllAvailableHeads() {
        let heads = [...BASE_STATIC_DISCOUNT_HEADS];
        savedCustomHeadsList.forEach(ch => {
            if (ch && !heads.some(h => h.toLowerCase() === ch.toLowerCase())) {
                heads.push(ch);
            }
        });
        return heads;
    }

    function persistNewCustomHead(headName) {
        headName = (headName || '').trim();
        if (!headName) return;

        const alreadyInBase = BASE_STATIC_DISCOUNT_HEADS.some(h => h.toLowerCase() === headName.toLowerCase());
        const alreadyInSaved = savedCustomHeadsList.some(h => h.toLowerCase() === headName.toLowerCase());

        if (!alreadyInBase && !alreadyInSaved) {
            savedCustomHeadsList.push(headName);
            try {
                localStorage.setItem('innotech_custom_discount_heads', JSON.stringify(savedCustomHeadsList));
            } catch(e) {}

            $.ajax({
                url: "{{ route('report.discount_reconciliation.save_custom_head') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    head: headName
                },
                success: function(res) {
                    if (res && res.heads) {
                        savedCustomHeadsList = res.heads;
                    }
                }
            });
        }
    }

    function deletePersistedCustomHead(headName) {
        headName = (headName || '').trim();
        savedCustomHeadsList = savedCustomHeadsList.filter(h => h.toLowerCase() !== headName.toLowerCase());
        try {
            localStorage.setItem('innotech_custom_discount_heads', JSON.stringify(savedCustomHeadsList));
        } catch(e) {}

        $.ajax({
            url: "{{ route('report.discount_reconciliation.delete_custom_head') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                head: headName
            }
        });
    }

    // Render Custom Heads Table (Populates all 17 Static Heads + saved custom heads + any item-specific heads)
    function renderCustomHeads(heads) {
        heads = heads || [];
        const existingMap = {};
        heads.forEach(h => {
            if (h && h.head) {
                existingMap[h.head.trim().toLowerCase()] = h;
            }
        });

        const $tbody = $('#splitCustomHeadsBody');
        $tbody.empty();

        const allAvailable = getAllAvailableHeads();

        allAvailable.forEach((headName, idx) => {
            const key = headName.trim().toLowerCase();
            const existing = existingMap[key];
            const pct = existing ? (parseFloat(existing.percent) || 0) : 0;
            const amt = existing ? (parseFloat(existing.amount) || 0) : 0;
            const isBase = BASE_STATIC_DISCOUNT_HEADS.some(bh => bh.toLowerCase() === key);

            appendHeadRow(headName, pct, amt, idx + 1, isBase);
            if (existing) {
                delete existingMap[key];
            }
        });

        // Any heads present in this item's data that weren't in allAvailable yet
        Object.values(existingMap).forEach(h => {
            if (h && h.head) {
                persistNewCustomHead(h.head);
                appendHeadRow(h.head, h.percent || 0, h.amount || 0, $('#splitCustomHeadsBody tr').length + 1, false);
            }
        });

        updateActiveHeadsCount();
    }

    function appendHeadRow(headName, pct, amt, rowNum, isBase) {
        const isActive = (pct > 0 || amt > 0);
        const rowBg = isActive ? 'table-warning bg-opacity-25' : '';
        const customBadge = !isBase ? '<span class="badge bg-info-subtle text-info border border-info-subtle px-1 py-0 ms-1" style="font-size: 8.5px;">Custom</span>' : '';
        const deleteBtn = !isBase ? `
            <button type="button" class="btn btn-outline-danger btn-xs py-0 px-1 btn-delete-custom-head-permanent" data-head="${headName}" title="Delete this custom head permanently" style="font-size: 9.5px;">
                <i class="fas fa-trash-alt"></i>
            </button>
        ` : '';

        const html = `
            <tr class="custom-head-row ${isBase ? 'static-head-row' : 'persisted-custom-row'} ${rowBg}" data-head="${headName.toLowerCase()}">
                <td class="text-center text-muted small fw-semibold py-1 row-idx">${rowNum}</td>
                <td class="py-1">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fw-semibold text-dark head-label" style="font-size: 11.5px;">${headName}</span>
                            ${customBadge}
                        </div>
                        ${isActive ? '<span class="badge bg-warning text-dark px-1 py-0 active-mark" style="font-size: 9px;"><i class="fas fa-check"></i></span>' : ''}
                    </div>
                    <input type="hidden" class="custom-head-name" value="${headName}">
                </td>
                <td class="py-1">
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" min="0" class="form-control text-end custom-head-pct font-monospace fw-bold text-dark py-0" placeholder="0.00" value="${pct > 0 ? pct : ''}" style="font-size: 11.5px;">
                        <span class="input-group-text py-0 px-1 fw-bold small">%</span>
                    </div>
                </td>
                <td class="py-1">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text py-0 px-1 small">Rs</span>
                        <input type="number" step="0.01" min="0" class="form-control text-end custom-head-amt font-monospace fw-bold text-dark py-0" placeholder="0.00" value="${amt > 0 ? amt : ''}" style="font-size: 11.5px;">
                    </div>
                </td>
                <td class="text-center py-1">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary btn-xs py-0 px-1 btn-fill-head-remaining" title="Fill unallocated discount into this head" style="font-size: 9.5px;">
                            Fill
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-xs py-0 px-1 btn-clear-head-row" title="Clear this head" style="font-size: 9.5px;">
                            <i class="fas fa-times"></i>
                        </button>
                        ${deleteBtn}
                    </div>
                </td>
            </tr>
        `;
        $('#splitCustomHeadsBody').append(html);
    }

    function updateActiveHeadsCount() {
        let count = 0;
        $('#splitCustomHeadsBody tr.custom-head-row').each(function() {
            const pct = parseFloat($(this).find('.custom-head-pct').val()) || 0;
            const amt = parseFloat($(this).find('.custom-head-amt').val()) || 0;
            if (pct > 0 || amt > 0) {
                count++;
                $(this).addClass('table-warning bg-opacity-25');
                if (!$(this).find('.active-mark').length) {
                    $(this).find('.head-label').after('<span class="badge bg-warning text-dark px-1 py-0 active-mark" style="font-size: 9px;"><i class="fas fa-check"></i></span>');
                }
            } else {
                $(this).removeClass('table-warning bg-opacity-25');
                $(this).find('.active-mark').remove();
            }
        });
        $('#splitActiveHeadsBadge').text(count + ' Active');
    }

    // Add extra custom head with prompt & permanent persistence
    $('#btnAddCustomHead').on('click', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Add New Custom Discount Head',
                input: 'text',
                inputLabel: 'Enter Head Name (e.g. Special Festival Rebate)',
                inputPlaceholder: 'Head name...',
                showCancelButton: true,
                confirmButtonText: 'Add & Save Head',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value || !value.trim()) {
                        return 'Please enter a head name!';
                    }
                    const val = value.trim().toLowerCase();
                    const allExisting = getAllAvailableHeads().map(h => h.toLowerCase());
                    if (allExisting.includes(val)) {
                        return 'This head name already exists!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const newHeadName = result.value.trim();
                    persistNewCustomHead(newHeadName);
                    const rowIdx = $('#splitCustomHeadsBody tr').length + 1;
                    appendHeadRow(newHeadName, 0, 0, rowIdx, false);
                    recalcModalSplits();
                    updateActiveHeadsCount();
                    // Scroll to newly added row and focus its rate input
                    setTimeout(function() {
                        const $newRow = $('#splitCustomHeadsBody tr').last();
                        if ($newRow.length) {
                            $newRow[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                            $newRow.find('.custom-head-pct').focus();
                        }
                    }, 100);
                }
            });
        } else {
            const newHeadName = prompt('Enter new discount head name:');
            if (newHeadName && newHeadName.trim()) {
                persistNewCustomHead(newHeadName.trim());
                const rowIdx = $('#splitCustomHeadsBody tr').length + 1;
                appendHeadRow(newHeadName.trim(), 0, 0, rowIdx, false);
                recalcModalSplits();
                updateActiveHeadsCount();
            }
        }
    });

    // Delete custom head permanently
    $(document).on('click', '.btn-delete-custom-head-permanent', function() {
        const headName = $(this).data('head');
        const $row = $(this).closest('tr');
        if (confirm('Are you sure you want to delete "' + headName + '" from permanently saved heads list?')) {
            deletePersistedCustomHead(headName);
            $row.remove();
            recalcModalSplits();
            updateActiveHeadsCount();
        }
    });

    // Fill remaining unallocated discount into this specific head
    $(document).on('click', '.btn-fill-head-remaining', function() {
        const $row = $(this).closest('tr');
        const currentPct = parseFloat($row.find('.custom-head-pct').val()) || 0;
        const totalAllocated = getAllocatedPct();
        const otherAllocated = totalAllocated - currentPct;
        const remainingPct = Math.max(0, round2(modalTotalDiscPct - otherAllocated));
        const remainingAmt = modalTradeVal > 0 ? round2(modalTradeVal * (remainingPct / 100)) : 0;

        $row.find('.custom-head-pct').val(remainingPct > 0 ? remainingPct : '');
        $row.find('.custom-head-amt').val(remainingAmt > 0 ? remainingAmt : '');
        recalcModalSplits();
        updateActiveHeadsCount();
    });

    // Clear single head row
    $(document).on('click', '.btn-clear-head-row', function() {
        const $row = $(this).closest('tr');
        $row.find('.custom-head-pct').val('');
        $row.find('.custom-head-amt').val('');
        $row.removeClass('table-warning bg-opacity-25');
        recalcModalSplits();
        updateActiveHeadsCount();
    });

    // Remove extra custom head row
    $(document).on('click', '.btn-remove-custom-head', function() {
        $(this).closest('tr').remove();
        recalcModalSplits();
        updateActiveHeadsCount();
    });

    // Reset all custom head inputs to 0
    $('#btnClearCustomHeads').on('click', function() {
        $('#splitCustomHeadsBody .custom-head-pct').val('');
        $('#splitCustomHeadsBody .custom-head-amt').val('');
        $('#splitCustomHeadsBody tr.extra-custom-head-row').remove();
        $('#splitCustomHeadsBody tr').removeClass('table-warning bg-opacity-25');
        recalcModalSplits();
        updateActiveHeadsCount();
    });

    // Search / Filter heads in table
    $('#splitCustomHeadSearch').on('input', function() {
        const query = $(this).val().toLowerCase().trim();
        $('#splitCustomHeadsBody tr').each(function() {
            const head = $(this).attr('data-head') || $(this).find('.custom-head-name').val() || '';
            if (!query || head.toLowerCase().includes(query)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Toggle Active Only rows
    let showActiveOnly = false;
    $('#btnToggleActiveOnly').on('click', function() {
        showActiveOnly = !showActiveOnly;
        $(this).toggleClass('btn-dark btn-outline-secondary');
        $('#splitCustomHeadsBody tr').each(function() {
            const pct = parseFloat($(this).find('.custom-head-pct').val()) || 0;
            const amt = parseFloat($(this).find('.custom-head-amt').val()) || 0;
            if (showActiveOnly) {
                if (pct > 0 || amt > 0) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            } else {
                $(this).show();
            }
        });
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
        updateActiveHeadsCount();
    }

    // Reset Split to 100% Owner
    $('#btnResetSplit').on('click', function() {
        $('#splitOwnerPct').val(modalTotalDiscPct);
        $('#splitOwnerAmt').val(modalTotalDiscAmt);
        $('#splitCustPct').val(0);
        $('#splitCustAmt').val(0);
        $('#splitCustomHeadsBody .custom-head-pct').val('');
        $('#splitCustomHeadsBody .custom-head-amt').val('');
        $('#splitCustomHeadsBody tr.extra-custom-head-row').remove();
        $('#splitCustomHeadsBody tr').removeClass('table-warning bg-opacity-25');
        $('#splitOurSubheadsContainer').empty();
        $('#splitOwnerNotes').val('');
        recalcModalSplits();
        updateActiveHeadsCount();
    });

    // Apply Split back to the Active Table Row or save to DB if in Reconciliation
    $('#btnApplyItemSplit').on('click', function(e) {
        e.preventDefault();
        try {
            const ownerPct = parseFloat($('#splitOwnerPct').val()) || 0;
            const ownerAmt = parseFloat($('#splitOwnerAmt').val()) || 0;
            const custPct = parseFloat($('#splitCustPct').val()) || 0;
            const custAmt = parseFloat($('#splitCustAmt').val()) || 0;
            const notes = $('#splitOwnerNotes').val() || '';

            // Collect custom heads
            const customHeads = [];
            $('#splitCustomHeadsBody tr.custom-head-row').each(function() {
                const hName = $(this).find('.custom-head-name').val() ? $(this).find('.custom-head-name').val().trim() : '';
                const hPct = parseFloat($(this).find('.custom-head-pct').val()) || 0;
                const hAmt = parseFloat($(this).find('.custom-head-amt').val()) || 0;
                if (hName !== '' && (hPct > 0 || hAmt > 0)) {
                    customHeads.push({ head: hName, percent: hPct, amount: hAmt });
                    persistNewCustomHead(hName);
                }
            });

            // Collect our sub-heads
            const ourSubheads = [];
            $('#splitOurSubheadsContainer .our-subhead-row').each(function() {
                const sName = $(this).find('.our-subhead-name').val() ? $(this).find('.our-subhead-name').val().trim() : '';
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
                        safeCloseSplitModal();
                        window.reconActiveItem = null;
                        if (typeof loadReportData === 'function') {
                            loadReportData();
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Item Split Saved!',
                                text: res.msg || 'Split saved successfully.',
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

            // Normal Purchase Form Mode (/add/Purchase or /edit/Purchase)
            let $targetRow = ($activeRow && $activeRow.length) ? $activeRow : $('#purchaseTableBody tr.split-active-row');
            if (!$targetRow.length) {
                const savedIndex = $('#itemDiscountSplitModal').data('active-row-index');
                if (savedIndex !== undefined && savedIndex >= 0) {
                    $targetRow = $('#purchaseTableBody tr').eq(savedIndex);
                }
            }
            if (!$targetRow.length) {
                $targetRow = $('#purchaseTableBody tr').first();
            }

            if ($targetRow && $targetRow.length) {
                const totalAllocatedPct = getAllocatedPct();
                const tradeVal = round2($targetRow.find('.item-trade-value').val());
                const totalAllocatedAmt = tradeVal > 0 ? round2(tradeVal * (totalAllocatedPct / 100)) : getAllocatedAmt();

                // TIGHT COUPLING: Update visible table inputs so table matches the split
                $targetRow.find('.item-disc-percent').val(totalAllocatedPct.toFixed(2));
                $targetRow.find('.item-disc-amt').val(totalAllocatedAmt.toFixed(2));

                // Store into hidden inputs of row
                $targetRow.find('.item-our-disc-percent').val(ownerPct);
                $targetRow.find('.item-our-disc-amount').val(ownerAmt);
                $targetRow.find('.item-customer-disc-percent').val(custPct);
                $targetRow.find('.item-customer-disc-amount').val(custAmt);
                $targetRow.find('.item-custom-disc-breakdown').val(JSON.stringify(customHeads));
                $targetRow.find('.item-our-disc-breakdown').val(JSON.stringify(ourSubheads));
                $targetRow.find('.item-our-disc-notes').val(notes);

                // Render badge pill under discount input
                updateRowSplitBadge($targetRow, ownerPct, custPct, customHeads);

                // Trigger row recalculation and footer totals
                if (typeof recalcRow === 'function') {
                    $targetRow.data('last_disc_input', 'pct');
                    recalcRow($targetRow, 'disc_pct');
                }
                if (typeof recalcAll === 'function') {
                    recalcAll();
                }

                // Flash row briefly to give visual feedback
                $targetRow.addClass('table-success');
                setTimeout(function() {
                    $targetRow.removeClass('table-success');
                }, 1200);
            }

            safeCloseSplitModal();

            if (typeof Swal !== 'undefined') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Item Split Applied!'
                });
            }
        } catch (err) {
            console.error('Error applying item split:', err);
            safeCloseSplitModal();
        }
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

        safeOpenSplitModal();
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

    // Helper to keep row discount input and split breakdown tightly coupled
    window.syncRowSplitWithDiscInput = function($row) {
        if (!$row || !$row.length) return;

        let totalPct = parseFloat($row.find('.item-disc-percent').val()) || 0;
        if (totalPct > 100) {
            totalPct = 100;
            $row.find('.item-disc-percent').val('100.00');
        }
        if (totalPct < 0) {
            totalPct = 0;
            $row.find('.item-disc-percent').val('0.00');
        }

        const tradeVal = round2($row.find('.item-trade-value').val());
        const totalAmt = tradeVal > 0 ? round2(tradeVal * (totalPct / 100)) : 0;
        $row.find('.item-disc-amt').val(totalAmt.toFixed(2));

        let ourPct = parseFloat($row.find('.item-our-disc-percent').val()) || 0;
        let custPct = parseFloat($row.find('.item-customer-disc-percent').val()) || 0;
        let customJson = $row.find('.item-custom-disc-breakdown').val() || '[]';
        let customHeads = [];
        try { customHeads = typeof customJson === 'string' ? JSON.parse(customJson) : customJson; } catch(e) { customHeads = []; }
        if (!Array.isArray(customHeads)) customHeads = [];

        let customPctSum = 0;
        customHeads.forEach(ch => { customPctSum += (parseFloat(ch.percent) || 0); });
        customPctSum = round2(customPctSum);

        const hasSplitConfigured = (ourPct > 0 || custPct > 0 || customHeads.length > 0);

        if (!hasSplitConfigured || totalPct === 0) {
            ourPct = totalPct;
            custPct = 0;
            customHeads = [];
        } else {
            const othersPct = round2(custPct + customPctSum);
            if (othersPct <= totalPct) {
                ourPct = round2(totalPct - othersPct);
            } else {
                const ratio = othersPct > 0 ? (totalPct / othersPct) : 1;
                custPct = round2(custPct * ratio);
                customHeads.forEach(ch => {
                    ch.percent = round2((parseFloat(ch.percent) || 0) * ratio);
                    ch.amount = tradeVal > 0 ? round2(tradeVal * (ch.percent / 100)) : 0;
                });
                ourPct = 0;
            }
        }

        const ourAmt = tradeVal > 0 ? round2(tradeVal * (ourPct / 100)) : 0;
        const custAmt = tradeVal > 0 ? round2(tradeVal * (custPct / 100)) : 0;
        customHeads.forEach(ch => {
            ch.amount = tradeVal > 0 ? round2(tradeVal * ((parseFloat(ch.percent) || 0) / 100)) : 0;
        });

        $row.find('.item-our-disc-percent').val(ourPct);
        $row.find('.item-our-disc-amount').val(ourAmt);
        $row.find('.item-customer-disc-percent').val(custPct);
        $row.find('.item-customer-disc-amount').val(custAmt);
        $row.find('.item-custom-disc-breakdown').val(JSON.stringify(customHeads));

        updateRowSplitBadge($row, ourPct, custPct, customHeads);
    };

    // On clicking the badge pill, trigger split modal
    $(document).on('click', '.item-split-badge-container', function() {
        $(this).closest('tr').find('.btn-open-item-split').trigger('click');
    });

})(jQuery);
</script>
