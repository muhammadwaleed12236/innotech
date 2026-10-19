@extends('admin_panel.layout.app')

@section('content')
<style>
    .config-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    
    .module-tab-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
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
        width: 34px;
        height: 18px;
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
        transition: .2s;
        border-radius: 18px;
    }
    .slider-sm:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: .2s;
        border-radius: 50%;
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
    .badge-system {
        background-color: #e0e7ff;
        color: #4338ca;
        font-weight: 600;
        font-size: 0.72rem;
    }
    .badge-custom {
        background-color: #fef3c7;
        color: #92400e;
        font-weight: 600;
        font-size: 0.72rem;
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
                            <i class="fas fa-sliders-h text-primary me-2"></i> Dynamic Modules &amp; Field Customizer
                        </h3>
                        <span class="badge bg-primary text-white" style="font-size: 0.75rem; border-radius: 6px;">ERP Core Engine</span>
                    </div>
                    <small class="text-muted">Centrally manage dynamic fields, visibility matrices, and custom data attributes across your ERP modules.</small>
                </div>

                <div class="d-flex gap-2">
                    @if($activeModule === 'customers')
                        <button type="button" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm" data-toggle="modal" data-target="#addFieldModal">
                            <i class="fas fa-plus me-1"></i> Add Custom Field
                        </button>
                        <form action="{{ route('dynamic_fields.resetDefaults') }}" method="POST" onsubmit="return confirm('Restore default customer fields? Custom fields will not be deleted.');">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm" title="Restore default system fields">
                                <i class="fas fa-undo me-1"></i> Reset Defaults
                            </button>
                        </form>
                        <a href="{{ route('customers.index') }}" class="btn btn-outline-dark btn-sm">
                            <i class="fas fa-users me-1"></i> View Customers
                        </a>
                    @endif
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Module Switcher Tabs -->
            <div class="row g-3 mb-4">
                @foreach($modules as $modKey => $mod)
                    <div class="col-md-2 col-sm-4 col-6">
                        <a href="{{ route('dynamic_fields.index', ['module' => $modKey]) }}" 
                           class="module-tab-card {{ $activeModule === $modKey ? 'active' : '' }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="rounded-circle p-2 {{ $activeModule === $modKey ? 'bg-primary text-white' : 'bg-light text-primary' }}" style="width: 36px; height: 36px; display:flex; align-items:center; justify-content:center;">
                                    <i class="{{ $mod['icon'] }} fs-6"></i>
                                </div>
                                <span class="badge {{ $mod['badge_class'] }}" style="font-size: 0.68rem;">
                                    {{ $mod['badge'] }}
                                </span>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.9rem;">{{ $mod['name'] }}</h6>
                            <small class="text-muted d-block" style="font-size: 0.74rem; line-height: 1.2;">
                                @if($mod['is_active'])
                                    <strong>{{ $mod['fields_count'] }}</strong> Fields Configured
                                @else
                                    Ready for next setup
                                @endif
                            </small>
                        </a>
                    </div>
                @endforeach
            </div>

            @if($activeModule === 'customers')
                <!-- KPI Stats Cards for Customer Module -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="config-card p-3 d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-list fs-5"></i>
                            </div>
                            <div>
                                <span class="text-muted small">Total Configured</span>
                                <h4 class="mb-0 fw-bold">{{ $fields->count() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="config-card p-3 d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-success bg-opacity-10 text-success p-3" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-check-double fs-5"></i>
                            </div>
                            <div>
                                <span class="text-muted small">Active / Enabled</span>
                                <h4 class="mb-0 fw-bold">{{ $fields->where('is_enabled', true)->count() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="config-card p-3 d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-puzzle-piece fs-5"></i>
                            </div>
                            <div>
                                <span class="text-muted small">Custom Attributes</span>
                                <h4 class="mb-0 fw-bold">{{ $fields->where('is_system', false)->count() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="config-card p-3 d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-info bg-opacity-10 text-info p-3" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-search fs-5"></i>
                            </div>
                            <div>
                                <span class="text-muted small">Active Search Filters</span>
                                <h4 class="mb-0 fw-bold">{{ $fields->where('visible_in_search', true)->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fields Configuration Matrix -->
                <div class="config-card mb-4" style="overflow: hidden;">
                    <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-table me-2 text-primary"></i> Customer Field Configuration Matrix
                            </h5>
                            <small class="text-muted">Use the toggles below to immediately control where each field appears across Customer screens.</small>
                        </div>
                        <span class="badge bg-light text-dark border p-2">
                            <i class="fas fa-info-circle text-primary me-1"></i> Toggles save automatically via AJAX
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="fieldsMatrixTable">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Field Label &amp; Key</th>
                                    <th>Type</th>
                                    <th>Section</th>
                                    <th class="text-center" title="Overall enable/disable">Enabled</th>
                                    <th class="text-center" title="Visible in Customer List Table">List</th>
                                    <th class="text-center" title="Visible in View Details Modal">View</th>
                                    <th class="text-center" title="Visible in Customer Create Form">Create</th>
                                    <th class="text-center" title="Visible in Customer Edit Form">Edit</th>
                                    <th class="text-center" title="Visible in Search &amp; Filters">Search</th>
                                    <th class="text-center" title="Included in CSV / Excel Export">Export</th>
                                    <th class="text-center" title="Mandatory Field">Required</th>
                                    <th class="text-center" title="Read Only in Forms">Readonly</th>
                                    <th class="text-center" style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fields as $index => $field)
                                    <tr data-id="{{ $field->id }}">
                                        <td><span class="text-muted small">{{ $index + 1 }}</span></td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $field->label }}</div>
                                            <code class="small text-muted">{{ $field->field_key }}</code>
                                            @if($field->is_system)
                                                <span class="badge badge-system ms-1">System</span>
                                            @else
                                                <span class="badge badge-custom ms-1">Custom</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.75rem;">
                                                {{ $field->field_type }}
                                            </span>
                                        </td>
                                        <td><small class="text-muted">{{ $field->section ?: 'General' }}</small></td>

                                        <!-- Toggle: Enabled -->
                                        <td class="text-center">
                                            <label class="switch-sm">
                                                <input type="checkbox" class="ajax-toggle" data-id="{{ $field->id }}" data-attr="is_enabled" {{ $field->is_enabled ? 'checked' : '' }}>
                                                <span class="slider-sm"></span>
                                            </label>
                                        </td>

                                        <!-- Toggle: List -->
                                        <td class="text-center">
                                            <label class="switch-sm">
                                                <input type="checkbox" class="ajax-toggle" data-id="{{ $field->id }}" data-attr="visible_in_list" {{ $field->visible_in_list ? 'checked' : '' }}>
                                                <span class="slider-sm"></span>
                                            </label>
                                        </td>

                                        <!-- Toggle: View -->
                                        <td class="text-center">
                                            <label class="switch-sm">
                                                <input type="checkbox" class="ajax-toggle" data-id="{{ $field->id }}" data-attr="visible_in_view" {{ $field->visible_in_view ? 'checked' : '' }}>
                                                <span class="slider-sm"></span>
                                            </label>
                                        </td>

                                        <!-- Toggle: Create -->
                                        <td class="text-center">
                                            <label class="switch-sm">
                                                <input type="checkbox" class="ajax-toggle" data-id="{{ $field->id }}" data-attr="visible_in_create" {{ $field->visible_in_create ? 'checked' : '' }}>
                                                <span class="slider-sm"></span>
                                            </label>
                                        </td>

                                        <!-- Toggle: Edit -->
                                        <td class="text-center">
                                            <label class="switch-sm">
                                                <input type="checkbox" class="ajax-toggle" data-id="{{ $field->id }}" data-attr="visible_in_edit" {{ $field->visible_in_edit ? 'checked' : '' }}>
                                                <span class="slider-sm"></span>
                                            </label>
                                        </td>

                                        <!-- Toggle: Search -->
                                        <td class="text-center">
                                            <label class="switch-sm">
                                                <input type="checkbox" class="ajax-toggle" data-id="{{ $field->id }}" data-attr="visible_in_search" {{ $field->visible_in_search ? 'checked' : '' }}>
                                                <span class="slider-sm"></span>
                                            </label>
                                        </td>

                                        <!-- Toggle: Export -->
                                        <td class="text-center">
                                            <label class="switch-sm">
                                                <input type="checkbox" class="ajax-toggle" data-id="{{ $field->id }}" data-attr="visible_in_export" {{ $field->visible_in_export ? 'checked' : '' }}>
                                                <span class="slider-sm"></span>
                                            </label>
                                        </td>

                                        <!-- Toggle: Required -->
                                        <td class="text-center">
                                            <label class="switch-sm">
                                                <input type="checkbox" class="ajax-toggle" data-id="{{ $field->id }}" data-attr="is_required" {{ $field->is_required ? 'checked' : '' }}>
                                                <span class="slider-sm"></span>
                                            </label>
                                        </td>

                                        <!-- Toggle: Readonly -->
                                        <td class="text-center">
                                            <label class="switch-sm">
                                                <input type="checkbox" class="ajax-toggle" data-id="{{ $field->id }}" data-attr="is_readonly" {{ $field->is_readonly ? 'checked' : '' }}>
                                                <span class="slider-sm"></span>
                                            </label>
                                        </td>

                                        <!-- Actions -->
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit-field" 
                                                    data-field='@json($field)'
                                                    title="Edit Field Properties">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                @if(!$field->is_system)
                                                    <form action="{{ route('dynamic_fields.destroy', $field->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this custom field? Existing values will be safely archived.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Custom Field">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-light text-muted border-0" title="System fields cannot be deleted" disabled>
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            @else
                <!-- Placeholder Screen for Next Modules -->
                <div class="config-card p-5 text-center my-4">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width: 80px; height: 80px;">
                        <i class="{{ $modules[$activeModule]['icon'] }} fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold text-dark">{{ $modules[$activeModule]['name'] }}</h4>
                    <p class="text-muted mx-auto" style="max-width: 600px;">
                        {{ $modules[$activeModule]['description'] }}
                    </p>
                    <div class="badge {{ $modules[$activeModule]['badge_class'] }} px-3 py-2 fs-6 mb-4">
                        {{ $modules[$activeModule]['badge'] }}
                    </div>

                    <div class="card p-4 mx-auto border-dashed text-start" style="max-width: 650px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px;">
                        <h6 class="fw-bold text-dark"><i class="fas fa-rocket text-primary me-2"></i> Architecture Ready</h6>
                        <p class="small text-muted mb-3">
                            The dynamic field engine is built so that this module can be converted to dynamic fields using the exact same database architecture and service layer established for the Customer Module.
                        </p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('dynamic_fields.index', ['module' => 'customers']) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Go to Customers Configuration
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     ADD CUSTOM FIELD MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="addFieldModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg,#4f46e5,#7c3aed); color:#fff; border-bottom: none;">
                <div>
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-plus-circle me-2"></i> Add Dynamic Custom Field
                    </h5>
                    <small style="color: rgba(255,255,255,0.85);">Define a new attribute for the Customer Module.</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:.8; text-shadow:none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('dynamic_fields.store') }}" method="POST">
                @csrf
                <input type="hidden" name="module" value="customers">

                <div class="modal-body p-4" style="background:#f8fafc;">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark">Field Label <span class="text-danger">*</span></label>
                            <input type="text" name="label" class="form-control" placeholder="e.g. WhatsApp Number, NTN Number" required>
                            <small class="text-muted">Display name shown on forms and tables.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark">Field Key (Unique Identifier)</label>
                            <input type="text" name="field_key" class="form-control" placeholder="Auto-generated if left blank (e.g. whatsapp_number)">
                            <small class="text-muted">Only letters, numbers, and underscores.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark">Field Type <span class="text-danger">*</span></label>
                            <select name="field_type" id="newFieldType" class="form-control" required>
                                <option value="text">Text (Single Line)</option>
                                <option value="textarea">Textarea (Multi Line)</option>
                                <option value="number">Numeric (Amount / Decimal / Quantity)</option>
                                <option value="phone">Phone / Mobile</option>
                                <option value="email">Email Address</option>
                                <option value="date">Date Picker</option>
                                <option value="dropdown">Dropdown Select</option>
                                <option value="checkbox">Checkbox (Yes / No)</option>
                                <option value="url">URL Link</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark">Section Grouping</label>
                            <select name="section" class="form-control">
                                @foreach($sections as $sec)
                                    <option value="{{ $sec }}">{{ $sec }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Groups fields logically in the create &amp; edit forms.</small>
                        </div>

                        <div class="col-12 mb-3" id="optionsGroup" style="display: none;">
                            <label class="form-label fw-bold text-dark">Dropdown Options (One per line)</label>
                            <textarea name="options" class="form-control" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                            <small class="text-muted">Enter selectable options for the dropdown list.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark">Placeholder</label>
                            <input type="text" name="placeholder" class="form-control" placeholder="Input placeholder text">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark">Default Value</label>
                            <input type="text" name="default_value" class="form-control" placeholder="Initial value if any">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold text-dark">Help Text / Tooltip</label>
                            <input type="text" name="help_text" class="form-control" placeholder="Short description explaining what to enter">
                        </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="fw-bold text-dark mb-3">Initial Visibility &amp; Validation Settings</h6>

                    <div class="row">
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_enabled" id="new_is_enabled" value="1" checked>
                                <label class="form-check-label fw-bold text-dark small" for="new_is_enabled">Enabled</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="visible_in_list" id="new_visible_in_list" value="1" checked>
                                <label class="form-check-label fw-bold text-dark small" for="new_visible_in_list">Show in Table</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="visible_in_view" id="new_visible_in_view" value="1" checked>
                                <label class="form-check-label fw-bold text-dark small" for="new_visible_in_view">Show in View</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="visible_in_create" id="new_visible_in_create" value="1" checked>
                                <label class="form-check-label fw-bold text-dark small" for="new_visible_in_create">Show in Create</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="visible_in_edit" id="new_visible_in_edit" value="1" checked>
                                <label class="form-check-label fw-bold text-dark small" for="new_visible_in_edit">Show in Edit</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="visible_in_search" id="new_visible_in_search" value="1">
                                <label class="form-check-label fw-bold text-dark small" for="new_visible_in_search">Filterable</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="visible_in_export" id="new_visible_in_export" value="1" checked>
                                <label class="form-check-label fw-bold text-dark small" for="new_visible_in_export">In CSV Export</label>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_required" id="new_is_required" value="1">
                                <label class="form-check-label fw-bold text-danger small" for="new_is_required">Required *</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Save Field</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     EDIT FIELD MODAL
══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editFieldModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #334155); color:#fff; border-bottom: none;">
                <div>
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-edit me-2 text-primary"></i> Edit Field: <span id="editFieldTitle">Field</span>
                    </h5>
                    <small style="color: rgba(255,255,255,0.75);" id="editFieldSubtitle">Key: -</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:.8; text-shadow:none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="editFieldForm" method="POST">
                @csrf
                <div class="modal-body p-4" style="background:#f8fafc;">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark">Field Label <span class="text-danger">*</span></label>
                            <input type="text" name="label" id="edit_label" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark">Section Grouping</label>
                            <select name="section" id="edit_section" class="form-control">
                                @foreach($sections as $sec)
                                    <option value="{{ $sec }}">{{ $sec }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark">Placeholder</label>
                            <input type="text" name="placeholder" id="edit_placeholder" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-dark">Default Value</label>
                            <input type="text" name="default_value" id="edit_default_value" class="form-control">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold text-dark">Help Text / Tooltip</label>
                            <input type="text" name="help_text" id="edit_help_text" class="form-control">
                        </div>

                        <div class="col-12 mb-3" id="editOptionsGroup" style="display: none;">
                            <label class="form-label fw-bold text-dark">Dropdown Options (One per line)</label>
                            <textarea name="options" id="edit_options" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Update Field</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Toggle options textarea if dropdown is selected
        $('#newFieldType').on('change', function() {
            if ($(this).val() === 'dropdown') {
                $('#optionsGroup').slideDown();
            } else {
                $('#optionsGroup').slideUp();
            }
        });

        // AJAX Toggle Switches
        $('.ajax-toggle').on('change', function() {
            const checkbox = $(this);
            const fieldId = checkbox.data('id');
            const attr = checkbox.data('attr');
            const isChecked = checkbox.is(':checked') ? 1 : 0;

            checkbox.prop('disabled', true);

            $.ajax({
                url: "{{ url('dynamic-fields/toggle') }}/" + fieldId,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    attribute: attr,
                    value: isChecked
                },
                success: function(res) {
                    checkbox.prop('disabled', false);
                },
                error: function(err) {
                    checkbox.prop('disabled', false);
                    checkbox.prop('checked', !isChecked);
                    alert('Failed to update setting. Please refresh and try again.');
                }
            });
        });

        // Edit Field Modal Setup
        $('.btn-edit-field').on('click', function() {
            const field = $(this).data('field');
            if (!field) return;

            $('#editFieldTitle').text(field.label);
            $('#editFieldSubtitle').text('Field Key: ' + field.field_key + (field.is_system ? ' (System Field)' : ' (Custom Field)'));
            $('#edit_label').val(field.label);
            $('#edit_section').val(field.section || 'General');
            $('#edit_placeholder').val(field.placeholder || '');
            $('#edit_default_value').val(field.default_value || '');
            $('#edit_help_text').val(field.help_text || '');

            if (field.field_type === 'dropdown') {
                let optsText = '';
                if (field.options) {
                    if (Array.isArray(field.options)) {
                        optsText = field.options.join("\n");
                    } else if (typeof field.options === 'object') {
                        optsText = Object.values(field.options).join("\n");
                    }
                }
                $('#edit_options').val(optsText);
                $('#editOptionsGroup').show();
            } else {
                $('#editOptionsGroup').hide();
            }

            $('#editFieldForm').attr('action', "{{ url('dynamic-fields/update') }}/" + field.id);
            $('#editFieldModal').modal('show');
        });
    });
</script>
@endsection
