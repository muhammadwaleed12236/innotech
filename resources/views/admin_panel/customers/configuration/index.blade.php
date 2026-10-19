@extends('admin_panel.layout.app')

@section('content')
<style>
    .config-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid #e2e8f0;
    }
    .badge-vis {
        font-size: 0.72rem;
        padding: 3px 6px;
        border-radius: 4px;
        font-weight: 600;
        display: inline-block;
        margin: 1px;
    }
    .badge-vis-on {
        background-color: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .badge-vis-off {
        background-color: #f1f5f9;
        color: #94a3b8;
        border: 1px solid #e2e8f0;
    }
    .drag-handle {
        cursor: grab;
        color: #94a3b8;
    }
    .drag-handle:hover {
        color: #4f46e5;
    }
    .switch {
        position: relative;
        display: inline-block;
        width: 36px;
        height: 20px;
    }
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 20px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #10b981;
    }
    input:checked + .slider:before {
        transform: translateX(16px);
    }
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid px-4 py-3">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h3 class="fw-bold text-dark mb-0">
                        <i class="fas fa-sliders-h text-primary me-2"></i> Customer Field Configuration
                    </h3>
                    <small class="text-muted">
                        Configure all customer fields, labels, visibility across screens, validation, and custom attributes dynamically.
                    </small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
                        <i class="fas fa-arrow-left me-1"></i> Back to Customers
                    </a>

                    <form action="{{ route('customers.configuration.resetDefaults') }}" method="POST" class="d-inline" onsubmit="return confirm('Restore system default customer fields? Custom fields will be preserved.');">
                        @csrf
                        <button type="submit" class="btn btn-outline-warning btn-sm" style="border-radius: 8px;" title="Reset factory default fields">
                            <i class="fas fa-sync-alt me-1"></i> Restore Defaults
                        </button>
                    </form>

                    <button type="button" class="btn btn-primary btn-sm px-3" data-toggle="modal" data-target="#fieldModal" id="btnAddNewField" style="border-radius: 8px;">
                        <i class="fas fa-plus me-1"></i> Add Custom Field
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Summary KPI Badges -->
            <div class="row g-2 mb-3">
                <div class="col-md-3 col-sm-6">
                    <div class="p-3 config-card d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary-subtle text-primary p-3" style="background:#e0e7ff;">
                            <i class="fas fa-list fs-5 text-primary"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Total Fields</span>
                            <h4 class="mb-0 fw-bold">{{ $fields->count() }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="p-3 config-card d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success-subtle text-success p-3" style="background:#d1fae5;">
                            <i class="fas fa-check-circle fs-5 text-success"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Enabled Fields</span>
                            <h4 class="mb-0 fw-bold">{{ $fields->where('is_enabled', true)->count() }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="p-3 config-card d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-info-subtle text-info p-3" style="background:#cffafe;">
                            <i class="fas fa-table fs-5 text-info"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Visible in List</span>
                            <h4 class="mb-0 fw-bold">{{ $fields->where('is_enabled', true)->where('visible_in_list', true)->count() }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="p-3 config-card d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning-subtle text-warning p-3" style="background:#fef3c7;">
                            <i class="fas fa-star fs-5 text-warning"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Custom Fields</span>
                            <h4 class="mb-0 fw-bold">{{ $fields->where('is_system', false)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fields Configuration Table -->
            <div class="config-card overflow-hidden">
                <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="fw-bold text-dark">
                        <i class="fas fa-table-columns text-primary me-2"></i> Field List &amp; Visibility Matrix
                    </div>
                    <small class="text-muted">
                        Toggle the switch to enable/disable. Click <strong>Edit</strong> to configure visibility, labels, dropdown options &amp; rules.
                    </small>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="fieldConfigTable" style="font-size: 0.88rem;">
                        <thead class="table-light text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <tr>
                                <th style="width: 40px;" class="text-center">#</th>
                                <th>Field Label &amp; Key</th>
                                <th>Type</th>
                                <th>Section</th>
                                <th>Rules</th>
                                <th>Visibility Matrix</th>
                                <th class="text-center" style="width: 90px;">Active</th>
                                <th class="text-center" style="width: 110px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortableFields">
                            @foreach ($fields as $field)
                                <tr id="field-row-{{ $field->id }}" data-id="{{ $field->id }}" class="{{ !$field->is_enabled ? 'table-light text-muted' : '' }}">
                                    <td class="text-center">
                                        <i class="fas fa-grip-vertical drag-handle" title="Sort Order: {{ $field->sort_order }}"></i>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $field->label }}</div>
                                        <div class="small font-monospace text-muted">
                                            <code>{{ $field->field_key }}</code>
                                            @if($field->is_system)
                                                <span class="badge bg-secondary-subtle text-secondary border px-1" style="font-size: 0.65rem;">System</span>
                                            @else
                                                <span class="badge bg-primary-subtle text-primary border px-1" style="font-size: 0.65rem;">Custom</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.72rem;">
                                            {{ $field->field_type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-dark small">{{ $field->section ?: 'General' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if($field->is_required)
                                                <span class="badge bg-danger" style="font-size: 0.68rem;">Required</span>
                                            @else
                                                <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">Optional</span>
                                            @endif

                                            @if($field->is_unique)
                                                <span class="badge bg-info text-dark" style="font-size: 0.68rem;">Unique</span>
                                            @endif

                                            @if($field->is_readonly)
                                                <span class="badge bg-warning text-dark" style="font-size: 0.68rem;">Readonly</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div style="line-height: 1.5;">
                                            <span class="badge-vis {{ $field->visible_in_list ? 'badge-vis-on' : 'badge-vis-off' }}" title="Visible in Customer List">List: {{ $field->visible_in_list ? '✓' : '✗' }}</span>
                                            <span class="badge-vis {{ $field->visible_in_view ? 'badge-vis-on' : 'badge-vis-off' }}" title="Visible in View Details">View: {{ $field->visible_in_view ? '✓' : '✗' }}</span>
                                            <span class="badge-vis {{ $field->visible_in_create ? 'badge-vis-on' : 'badge-vis-off' }}" title="Visible in Create Form">Create: {{ $field->visible_in_create ? '✓' : '✗' }}</span>
                                            <span class="badge-vis {{ $field->visible_in_edit ? 'badge-vis-on' : 'badge-vis-off' }}" title="Visible in Edit Form">Edit: {{ $field->visible_in_edit ? '✓' : '✗' }}</span>
                                            <span class="badge-vis {{ $field->visible_in_search ? 'badge-vis-on' : 'badge-vis-off' }}" title="Search / Filter">Search: {{ $field->visible_in_search ? '✓' : '✗' }}</span>
                                            <span class="badge-vis {{ $field->visible_in_export ? 'badge-vis-on' : 'badge-vis-off' }}" title="CSV Export & Print">Export: {{ $field->visible_in_export ? '✓' : '✗' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <label class="switch mb-0">
                                            <input type="checkbox" class="toggle-field-status" data-id="{{ $field->id }}" {{ $field->is_enabled ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-edit-field" data-field='@json($field)' title="Edit Configuration">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if(!$field->is_system)
                                                <button type="button" class="btn btn-outline-danger btn-delete-field" data-id="{{ $field->id }}" data-label="{{ $field->label }}" title="Delete Custom Field">
                                                    <i class="fas fa-trash"></i>
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

        </div>
    </div>
</div>

<!-- Modal: Add / Edit Field Definition -->
<div class="modal fade" id="fieldModal" tabindex="-1" role="dialog" aria-labelledby="fieldModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header bg-primary text-white border-bottom-0">
                <h5 class="modal-title fw-bold" id="fieldModalLabel">
                    <i class="fas fa-plus-circle me-1"></i> Customer Field Configuration
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="fieldForm" method="POST" action="{{ route('customers.configuration.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" id="fieldId" value="">

                <div class="modal-body p-4" style="background:#f8fafc; max-height: calc(85vh - 120px); overflow-y:auto;">
                    
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-dark small">Display Label <span class="text-danger">*</span></label>
                            <input type="text" name="label" id="fieldLabel" class="form-control" required placeholder="e.g. NTN Number, WhatsApp" style="border-radius:8px;">
                            <small class="text-muted">Label shown on forms, tables, and views.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-dark small">Field Key (Slug)</label>
                            <input type="text" name="field_key" id="fieldKey" class="form-control font-monospace" placeholder="e.g. ntn_number" style="border-radius:8px;">
                            <small class="text-muted" id="fieldKeyHelp">Unique internal identifier (alphanumeric &amp; underscores only).</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4 mb-3">
                            <label class="fw-bold text-dark small">Field Type <span class="text-danger">*</span></label>
                            <select name="field_type" id="fieldType" class="form-control" required style="border-radius:8px;">
                                <option value="text">Text (Single Line)</option>
                                <option value="textarea">Textarea (Multi-Line)</option>
                                <option value="number">Numeric (Amount / Integer)</option>
                                <option value="phone">Phone / Mobile</option>
                                <option value="email">Email Address</option>
                                <option value="date">Date Picker</option>
                                <option value="dropdown">Dropdown (Select Box)</option>
                                <option value="checkbox">Checkbox (Yes/No)</option>
                                <option value="url">Web URL</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="fw-bold text-dark small">Section / Group</label>
                            <input type="text" name="section" id="fieldSection" class="form-control" list="sectionList" placeholder="e.g. Tax Details" style="border-radius:8px;">
                            <datalist id="sectionList">
                                @foreach($sections as $sec)
                                    <option value="{{ $sec }}">
                                @endforeach
                            </datalist>
                            <small class="text-muted">Groups fields under clean section headers.</small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="fw-bold text-dark small">Sort Order</label>
                            <input type="number" name="sort_order" id="fieldSortOrder" class="form-control" value="100" style="border-radius:8px;">
                            <small class="text-muted">Display sequence on forms and tables.</small>
                        </div>
                    </div>

                    <!-- Dropdown Options Box (Conditional) -->
                    <div class="mb-3" id="optionsBox" style="display:none;">
                        <label class="fw-bold text-dark small">Dropdown Options (One per line)</label>
                        <textarea name="options" id="fieldOptions" class="form-control" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3" style="border-radius:8px;"></textarea>
                        <small class="text-muted">Enter selectable options for this dropdown list.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-dark small">Placeholder Text</label>
                            <input type="text" name="placeholder" id="fieldPlaceholder" class="form-control" placeholder="Optional input hint" style="border-radius:8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold text-dark small">Default Value</label>
                            <input type="text" name="default_value" id="fieldDefault" class="form-control" placeholder="Default prefill value" style="border-radius:8px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-dark small">Help / Guidance Note</label>
                        <input type="text" name="help_text" id="fieldHelpText" class="form-control" placeholder="Small helper text shown under input" style="border-radius:8px;">
                    </div>

                    <!-- Validation & Rules -->
                    <div class="card border-0 shadow-sm rounded-3 mb-3 p-3 bg-white">
                        <label class="fw-bold text-dark small mb-2 border-bottom pb-1">
                            <i class="fas fa-shield-alt text-primary me-1"></i> Validation &amp; Behavior
                        </label>
                        <div class="row g-2">
                            <div class="col-md-3 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_enabled" id="chkEnabled" value="1" checked>
                                    <label class="form-check-label fw-bold small" for="chkEnabled">Enabled (Active)</label>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_required" id="chkRequired" value="1">
                                    <label class="form-check-label fw-bold small text-danger" for="chkRequired">Required Field</label>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_unique" id="chkUnique" value="1">
                                    <label class="form-check-label fw-bold small text-info" for="chkUnique">Unique Value</label>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_readonly" id="chkReadonly" value="1">
                                    <label class="form-check-label fw-bold small text-warning" for="chkReadonly">Read-Only</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Screen Visibility Matrix -->
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                        <label class="fw-bold text-dark small mb-2 border-bottom pb-1">
                            <i class="fas fa-eye text-success me-1"></i> Screen Visibility Matrix
                        </label>
                        <div class="row g-2">
                            <div class="col-md-4 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="visible_in_list" id="chkList" value="1" checked>
                                    <label class="form-check-label small" for="chkList">Visible in <strong>Customer Table</strong></label>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="visible_in_view" id="chkView" value="1" checked>
                                    <label class="form-check-label small" for="chkView">Visible in <strong>View Details</strong></label>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="visible_in_create" id="chkCreate" value="1" checked>
                                    <label class="form-check-label small" for="chkCreate">Visible in <strong>Create Form</strong></label>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="visible_in_edit" id="chkEdit" value="1" checked>
                                    <label class="form-check-label small" for="chkEdit">Visible in <strong>Edit Form</strong></label>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="visible_in_search" id="chkSearch" value="1">
                                    <label class="form-check-label small" for="chkSearch">Visible in <strong>Search Filters</strong></label>
                                </div>
                            </div>
                            <div class="col-md-4 col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="visible_in_export" id="chkExport" value="1" checked>
                                    <label class="form-check-label small" for="chkExport">Visible in <strong>Export &amp; Print</strong></label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" style="border-radius:8px;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" style="border-radius:8px;" id="btnSaveField">
                        <i class="fas fa-save me-1"></i> Save Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    $(document).ready(function () {

        // Show/hide dropdown options
        $('#fieldType').on('change', function () {
            if ($(this).val() === 'dropdown') {
                $('#optionsBox').slideDown();
            } else {
                $('#optionsBox').slideUp();
            }
        });

        // Add new field modal reset
        $('#btnAddNewField').on('click', function () {
            $('#fieldModalLabel').html('<i class="fas fa-plus-circle me-1"></i> Add Custom Customer Field');
            $('#fieldForm').attr('action', "{{ route('customers.configuration.store') }}");
            $('#formMethod').val('POST');
            $('#fieldId').val('');

            $('#fieldLabel').val('');
            $('#fieldKey').val('').prop('readonly', false);
            $('#fieldKeyHelp').text('Unique internal identifier (alphanumeric & underscores only).');
            $('#fieldType').val('text').trigger('change');
            $('#fieldSection').val('General Information');
            $('#fieldSortOrder').val(100);
            $('#fieldOptions').val('');
            $('#fieldPlaceholder').val('');
            $('#fieldDefault').val('');
            $('#fieldHelpText').val('');

            $('#chkEnabled').prop('checked', true);
            $('#chkRequired').prop('checked', false);
            $('#chkUnique').prop('checked', false);
            $('#chkReadonly').prop('checked', false);

            $('#chkList').prop('checked', false);
            $('#chkView').prop('checked', true);
            $('#chkCreate').prop('checked', true);
            $('#chkEdit').prop('checked', true);
            $('#chkSearch').prop('checked', false);
            $('#chkExport').prop('checked', true);
        });

        // Edit field modal populate
        $('.btn-edit-field').on('click', function () {
            let field = $(this).data('field');
            if (typeof field === 'string') {
                field = JSON.parse(field);
            }

            $('#fieldModalLabel').html('<i class="fas fa-edit me-1"></i> Edit Field: ' + field.label);
            $('#fieldForm').attr('action', '/customers/configuration/update/' + field.id);
            $('#formMethod').val('POST');
            $('#fieldId').val(field.id);

            $('#fieldLabel').val(field.label);
            $('#fieldKey').val(field.field_key);

            if (field.is_system) {
                $('#fieldKey').prop('readonly', true);
                $('#fieldKeyHelp').text('System field key cannot be changed to preserve database integrity.');
            } else {
                $('#fieldKey').prop('readonly', false);
                $('#fieldKeyHelp').text('Unique internal identifier (alphanumeric & underscores only).');
            }

            $('#fieldType').val(field.field_type).trigger('change');
            $('#fieldSection').val(field.section || 'General Information');
            $('#fieldSortOrder').val(field.sort_order || 10);

            // Options parsing
            if (field.options) {
                if (Array.isArray(field.options)) {
                    $('#fieldOptions').val(field.options.join('\n'));
                } else if (typeof field.options === 'object') {
                    $('#fieldOptions').val(Object.values(field.options).join('\n'));
                } else {
                    $('#fieldOptions').val(field.options);
                }
            } else {
                $('#fieldOptions').val('');
            }

            $('#fieldPlaceholder').val(field.placeholder || '');
            $('#fieldDefault').val(field.default_value || '');
            $('#fieldHelpText').val(field.help_text || '');

            $('#chkEnabled').prop('checked', !!field.is_enabled);
            $('#chkRequired').prop('checked', !!field.is_required);
            $('#chkUnique').prop('checked', !!field.is_unique);
            $('#chkReadonly').prop('checked', !!field.is_readonly);

            $('#chkList').prop('checked', !!field.visible_in_list);
            $('#chkView').prop('checked', !!field.visible_in_view);
            $('#chkCreate').prop('checked', !!field.visible_in_create);
            $('#chkEdit').prop('checked', !!field.visible_in_edit);
            $('#chkSearch').prop('checked', !!field.visible_in_search);
            $('#chkExport').prop('checked', !!field.visible_in_export);

            $('#fieldModal').modal('show');
        });

        // Quick toggle field active/disabled status
        $('.toggle-field-status').on('change', function () {
            let fieldId = $(this).data('id');
            let isChecked = $(this).is(':checked');
            let $row = $('#field-row-' + fieldId);

            $.ajax({
                url: '/customers/configuration/toggle/' + fieldId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    if (res.success) {
                        if (res.is_enabled) {
                            $row.removeClass('table-light text-muted');
                        } else {
                            $row.addClass('table-light text-muted');
                        }
                    } else {
                        alert(res.message || 'Error toggling field status.');
                    }
                },
                error: function () {
                    alert('Server error occurred while toggling field.');
                }
            });
        });

        // Delete Custom Field
        $('.btn-delete-field').on('click', function () {
            let fieldId = $(this).data('id');
            let label = $(this).data('label');

            if (!confirm(`Are you sure you want to delete custom field "${label}"?\nExisting values will be retained safely in audit logs.`)) {
                return;
            }

            $.ajax({
                url: '/customers/configuration/' + fieldId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    if (res.success) {
                        $('#field-row-' + fieldId).fadeOut(300, function () { $(this).remove(); });
                    } else {
                        alert(res.message || 'Failed to delete field.');
                    }
                },
                error: function (xhr) {
                    let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Server error occurred.';
                    alert(msg);
                }
            });
        });

    });
</script>
@endsection
