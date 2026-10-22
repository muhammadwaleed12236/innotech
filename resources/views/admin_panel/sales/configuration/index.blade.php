@extends('admin_panel.layout.app')

@section('content')
<style>
    .config-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    
    .tax-master-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border-radius: 16px;
        border: 1px solid #334155;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
    }

    .tax-stat-pill {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        padding: 12px 16px;
    }

    .module-tab-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        background: #ffffff;
        transition: all 0.25s ease;
        text-decoration: none !important;
        display: block;
        height: 100%;
    }
    .module-tab-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border-color: #3b82f6;
    }
    .module-tab-card.active {
        border-color: #4f46e5;
        background: linear-gradient(135deg, #f5f3ff, #ede9fe);
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.15);
    }
    
    .switch-sm {
        position: relative;
        display: inline-block;
        width: 36px;
        height: 20px;
        margin: 0;
    }
    .switch-sm input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider-sm {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .25s;
        border-radius: 20px;
    }
    .slider-sm:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .25s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    input:checked + .slider-sm {
        background-color: #10b981;
    }
    input:checked + .slider-sm:before {
        transform: translateX(16px);
    }
    
    .table th {
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #475569;
        vertical-align: middle;
        background: #f8fafc;
    }
    .table td {
        vertical-align: middle;
        font-size: 0.88rem;
    }
    .badge-section {
        background-color: #f1f5f9;
        color: #334155;
        font-weight: 600;
        font-size: 0.74rem;
        border: 1px solid #e2e8f0;
    }
    .badge-tax {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .badge-col {
        background-color: #e0e7ff;
        color: #3730a3;
        border: 1px solid #c7d2fe;
    }
    .badge-summary {
        background-color: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .badge-field {
        background-color: #ede9fe;
        color: #5b21b6;
        border: 1px solid #ddd6fe;
    }

    .nav-pills .nav-link {
        font-size: 0.88rem;
        font-weight: 600;
        color: #64748b;
        border-radius: 8px;
        padding: 8px 16px;
    }
    .nav-pills .nav-link.active {
        background-color: #4f46e5;
        color: #ffffff;
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid px-4 py-3">

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h3 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-sliders-h text-primary me-2"></i> Sale Screen &amp; Tax Configuration
                        </h3>
                        <span class="badge bg-success text-white" style="font-size: 0.75rem; border-radius: 6px;">
                            <i class="fas fa-bolt me-1"></i> Live Dynamic Controls
                        </span>
                    </div>
                    <small class="text-muted">
                        Fully customize display fields, GST, Further Tax, columns, and checkout elements for 
                        <a href="{{ route('sale.add') }}" target="_blank" class="fw-bold text-primary text-decoration-none">
                            <code>/sale/create</code> <i class="fas fa-external-link-alt small"></i>
                        </a>.
                    </small>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('sale.add') }}" target="_blank" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm">
                        <i class="fas fa-shopping-cart me-1"></i> Open Sale Screen
                    </a>
                    <a href="{{ route('dynamic_fields.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-cubes me-1"></i> All Modules Hub
                    </a>
                    <form action="{{ route('sales.configuration.resetDefaults') }}" method="POST" onsubmit="return confirm('Restore all sale fields, columns, and taxes back to system defaults?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Restore default settings">
                            <i class="fas fa-undo me-1"></i> Reset Defaults
                        </button>
                    </form>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- MASTER TAX CONTROL CARD -->
            <div class="tax-master-card p-4 mb-4">
                <form action="{{ route('sales.configuration.updateTaxDefaults') }}" method="POST" id="taxDefaultsForm">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-3 border-secondary border-opacity-25">
                        <div>
                            <h4 class="mb-1 fw-bold text-white d-flex align-items-center gap-2">
                                <span class="badge bg-warning text-dark p-2 rounded-circle" style="width: 36px; height: 36px; display:inline-flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-percentage"></i>
                                </span>
                                Dynamic Taxes &amp; GST Control Engine
                            </h4>
                            <p class="text-light text-opacity-75 small mb-0">
                                Toggle taxes on or off. When disabled, the field is completely hidden from the sale screen, line calculations become 0%, and nothing is charged.
                            </p>
                        </div>
                        <button type="submit" class="btn btn-success fw-bold px-4 py-2 shadow-sm" id="btnSaveTaxSettings">
                            <i class="fas fa-save me-1"></i> Save Tax Settings
                        </button>
                    </div>

                    <div class="row g-3">
                        <!-- SALES TAX (ST %) -->
                        <div class="col-lg-3 col-md-6">
                            <div class="tax-stat-pill h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-white" style="font-size: 14px;">
                                        <i class="fas fa-file-invoice text-success me-1"></i> Sales Tax (ST %)
                                    </span>
                                    <label class="switch-sm">
                                        <input type="checkbox" name="st_enabled" value="1" {{ ($salesTaxField->is_enabled ?? true) ? 'checked' : '' }}>
                                        <span class="slider-sm"></span>
                                    </label>
                                </div>
                                <small class="text-light text-opacity-75 d-block mb-2" style="font-size: 11px;">
                                    Item grid ST column, summary row, &amp; calculation.
                                </small>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-dark border-secondary text-white fw-bold">Default %</span>
                                    <input type="number" step="0.01" class="form-control text-end fw-bold" name="default_st_rate" 
                                           value="{{ $salesTaxField->default_value ?? '18.00' }}" placeholder="18.00" style="background: rgba(255,255,255,0.9);">
                                    <span class="input-group-text bg-dark border-secondary text-white">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- FURTHER TAX (FT %) -->
                        <div class="col-lg-3 col-md-6">
                            <div class="tax-stat-pill h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-white" style="font-size: 14px;">
                                        <i class="fas fa-plus-circle text-info me-1"></i> Further Tax (FT %)
                                    </span>
                                    <label class="switch-sm">
                                        <input type="checkbox" name="ft_enabled" value="1" {{ ($furtherTaxField->is_enabled ?? true) ? 'checked' : '' }}>
                                        <span class="slider-sm"></span>
                                    </label>
                                </div>
                                <small class="text-light text-opacity-75 d-block mb-2" style="font-size: 11px;">
                                    Applied on unregistered buyers (Sales Tax Act 1990).
                                </small>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-dark border-secondary text-white fw-bold">Default %</span>
                                    <input type="number" step="0.01" class="form-control text-end fw-bold" name="default_ft_rate" 
                                           value="{{ $furtherTaxField->default_value ?? '3.00' }}" placeholder="3.00" style="background: rgba(255,255,255,0.9);">
                                    <span class="input-group-text bg-dark border-secondary text-white">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- STRN AUTO-EXEMPTION -->
                        <div class="col-lg-3 col-md-6">
                            <div class="tax-stat-pill h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-white" style="font-size: 14px;">
                                        <i class="fas fa-shield-alt text-warning me-1"></i> STRN Auto-Exempt
                                    </span>
                                    <label class="switch-sm">
                                        <input type="checkbox" name="strn_exemption" value="1" {{ ($strnExemptionField->is_enabled ?? true) ? 'checked' : '' }}>
                                        <span class="slider-sm"></span>
                                    </label>
                                </div>
                                <small class="text-light text-opacity-75 d-block mb-0" style="font-size: 11.5px; line-height: 1.35;">
                                    Automatically sets Further Tax = <strong>0%</strong> when a registered customer with STRN is selected on the invoice.
                                </small>
                                <div class="mt-2">
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25" style="font-size: 10px;">
                                        STRN Buyer &rarr; 0% FT
                                    </span>
                                    <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25 ms-1" style="font-size: 10px;">
                                        Non-STRN &rarr; 3% FT
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- EXCLUSIVE GST & BADGES -->
                        <div class="col-lg-3 col-md-6">
                            <div class="tax-stat-pill h-100 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold text-white" style="font-size: 13px;">
                                        Exclusive GST Row
                                    </span>
                                    <label class="switch-sm">
                                        <input type="checkbox" name="exclusive_gst_enabled" value="1" {{ ($exclusiveGstField->is_enabled ?? true) ? 'checked' : '' }}>
                                        <span class="slider-sm"></span>
                                    </label>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold text-white" style="font-size: 13px;">
                                        Customer Tax Badges
                                    </span>
                                    <label class="switch-sm">
                                        <input type="checkbox" name="badges_enabled" value="1" {{ ($badgesField->is_enabled ?? true) ? 'checked' : '' }}>
                                        <span class="slider-sm"></span>
                                    </label>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold text-white" style="font-size: 13px;">
                                        Print Tax Breakdown
                                    </span>
                                    <label class="switch-sm">
                                        <input type="checkbox" name="print_tax_enabled" value="1" {{ ($fields->firstWhere('field_key', 'print_tax_breakdown')->is_enabled ?? true) ? 'checked' : '' }}>
                                        <span class="slider-sm"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- KPI Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="config-card p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-list fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Total Fields Configured</span>
                            <h4 class="mb-0 fw-bold">{{ $stats['total'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="config-card p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success p-3" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-eye fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Visible &amp; Active</span>
                            <h4 class="mb-0 fw-bold text-success">{{ $stats['enabled'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="config-card p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary p-3" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-eye-slash fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Fully Hidden</span>
                            <h4 class="mb-0 fw-bold text-muted">{{ $stats['disabled'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="config-card p-3 d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-percentage fs-5"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Tax &amp; Legal Controls</span>
                            <h4 class="mb-0 fw-bold text-warning">{{ $stats['taxes_count'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION TABS & CONFIGURATION TABLE -->
            <div class="config-card p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-pills gap-1" id="configSectionTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-all" type="button">
                                <i class="fas fa-th-list me-1"></i> All Elements ({{ $fields->count() }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-taxes" type="button">
                                <i class="fas fa-percentage me-1"></i> Taxes &amp; Legal ({{ $fields->where('section', 'Taxes & Legal Details')->count() }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-columns" type="button">
                                <i class="fas fa-table me-1"></i> Item Table Columns ({{ $fields->where('section', 'Item Table Columns')->count() }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-summary" type="button">
                                <i class="fas fa-calculator me-1"></i> Order Summary ({{ $fields->where('section', 'Order Summary & Totals')->count() }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-header" type="button">
                                <i class="fas fa-cog me-1"></i> Header &amp; Inputs ({{ $fields->where('section', 'Header & Checkout Options')->count() }})
                            </button>
                        </li>
                    </ul>

                    <div class="text-muted small">
                        <i class="fas fa-info-circle text-primary me-1"></i> Click any switch to immediately show/hide on the sale screen.
                    </div>
                </div>

                <!-- Tab Contents -->
                <div class="tab-content" id="configTabContent">
                    <!-- TAB 1: ALL -->
                    <div class="tab-pane fade show active" id="tab-all" role="tabpanel">
                        @include('admin_panel.sales.configuration.partials.table', ['tableFields' => $fields])
                    </div>

                    <!-- TAB 2: TAXES -->
                    <div class="tab-pane fade" id="tab-taxes" role="tabpanel">
                        @include('admin_panel.sales.configuration.partials.table', ['tableFields' => $fields->where('section', 'Taxes & Legal Details')])
                    </div>

                    <!-- TAB 3: COLUMNS -->
                    <div class="tab-pane fade" id="tab-columns" role="tabpanel">
                        @include('admin_panel.sales.configuration.partials.table', ['tableFields' => $fields->where('section', 'Item Table Columns')])
                    </div>

                    <!-- TAB 4: SUMMARY -->
                    <div class="tab-pane fade" id="tab-summary" role="tabpanel">
                        @include('admin_panel.sales.configuration.partials.table', ['tableFields' => $fields->where('section', 'Order Summary & Totals')])
                    </div>

                    <!-- TAB 5: HEADER & OPTIONS -->
                    <div class="tab-pane fade" id="tab-header" role="tabpanel">
                        @include('admin_panel.sales.configuration.partials.table', ['tableFields' => $fields->where('section', 'Header & Checkout Options')])
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- EDIT FIELD MODAL -->
<div class="modal fade" id="modalEditField" tabindex="-1" aria-labelledby="modalEditFieldLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <form id="editFieldForm" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark" id="modalEditFieldLabel">
                        <i class="fas fa-edit text-primary me-2"></i> Edit Screen Element
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="edit_field_id">

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Field Key</label>
                        <input type="text" class="form-control bg-light font-monospace" id="edit_field_key" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Display Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="label" id="edit_label" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Default Value / Rate</label>
                        <input type="text" class="form-control" name="default_value" id="edit_default_value" placeholder="e.g. 18.00">
                        <small class="text-muted">For tax fields, enter percentage (e.g. 18). For toggles, 1 is on.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Description &amp; Guide</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="2"></textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-dark">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" id="edit_sort_order" value="0">
                        </div>
                    </div>

                    <div class="p-3 rounded-3 bg-light border">
                        <span class="fw-bold d-block mb-2 small text-dark">Visibility Settings</span>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_enabled" id="edit_is_enabled" value="1">
                                <label class="form-check-label fw-semibold" for="edit_is_enabled">Master Enable / Active</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="visible_in_create" id="edit_visible_in_create" value="1">
                                <label class="form-check-label" for="edit_visible_in_create">Show on Create Sale Screen (<code>/sale/create</code>)</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="visible_in_edit" id="edit_visible_in_edit" value="1">
                                <label class="form-check-label" for="edit_visible_in_edit">Show on Edit Sale Screen</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="visible_in_print" id="edit_visible_in_print" value="1">
                                <label class="form-check-label" for="edit_visible_in_print">Show on A4 &amp; Thermal Prints</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fast AJAX Switch Toggle
    $(document).on('change', '.ajax-field-toggle', function() {
        const $switch = $(this);
        const fieldId = $switch.data('id');
        const attribute = $switch.data('attr');
        const isChecked = $switch.is(':checked');

        $.ajax({
            url: '{{ url("sales/configuration/toggle") }}/' + fieldId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                attribute: attribute,
                value: isChecked ? 1 : 0
            },
            success: function(res) {
                // Flash toast or badge
                const toast = document.createElement('div');
                toast.className = 'position-fixed bottom-0 end-0 p-3';
                toast.style.zIndex = '9999';
                toast.innerHTML = `
                    <div class="toast align-items-center text-white bg-success border-0 show" role="alert">
                        <div class="d-flex">
                            <div class="toast-body"><i class="fas fa-check-circle me-1"></i> ${res.message || 'Updated successfully'}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>`;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2500);
            },
            error: function() {
                alert('Failed to update field setting. Please try again.');
                $switch.prop('checked', !isChecked);
            }
        });
    });

    // Populate Edit Modal
    $(document).on('click', '.btn-edit-field', function() {
        const field = $(this).data('field');
        if (!field) return;

        $('#edit_field_id').val(field.id);
        $('#edit_field_key').val(field.field_key);
        $('#edit_label').val(field.label);
        $('#edit_default_value').val(field.default_value || '');
        $('#edit_description').val(field.description || '');
        $('#edit_sort_order').val(field.sort_order || 0);

        $('#edit_is_enabled').prop('checked', Boolean(field.is_enabled));
        $('#edit_visible_in_create').prop('checked', Boolean(field.visible_in_create));
        $('#edit_visible_in_edit').prop('checked', Boolean(field.visible_in_edit));
        $('#edit_visible_in_print').prop('checked', Boolean(field.visible_in_print));

        $('#editFieldForm').attr('action', '{{ url("sales/configuration/update") }}/' + field.id);
        
        const modal = new bootstrap.Modal(document.getElementById('modalEditField'));
        modal.show();
    });
});
</script>
@endsection
