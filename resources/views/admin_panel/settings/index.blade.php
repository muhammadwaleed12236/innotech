@extends('admin_panel.layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">ERP Settings</h3>
                    </div>
                    <div class="card-body">
                        <!-- Advanced Settings & Navigation -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-muted mb-3 font-weight-bold text-uppercase"
                                style="font-size: 0.8rem; letter-spacing: 1px;">Advanced Actions</h6>
                            <div class="d-flex flex-wrap">
                                <a href="{{ route('settings.return-policy') }}"
                                    class="btn btn-outline-primary mr-2 mb-2 shadow-sm">
                                    <i class="fas fa-undo-alt mr-2"></i> Return Policy
                                </a>
                                <a href="{{ route('settings.return-approvers') }}"
                                    class="btn btn-outline-info mr-2 mb-2 shadow-sm">
                                    <i class="fas fa-user-shield mr-2"></i> Return Approvers
                                </a>
                                <a href="#" class="btn btn-outline-dark mr-2 mb-2 shadow-sm">
                                    <i class="fas fa-exchange-alt mr-2"></i> Switch Account
                                </a>
                            </div>
                        </div>

                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="company-tab" data-toggle="tab" href="#company"
                                    role="tab">
                                    <i class="fas fa-building"></i> Company
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="sales-tab" data-toggle="tab" href="#sales" role="tab">
                                    <i class="fas fa-shopping-cart"></i> Sales
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="inventory-tab" data-toggle="tab" href="#inventory" role="tab">
                                    <i class="fas fa-boxes"></i> Inventory
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="accounting-tab" data-toggle="tab" href="#accounting" role="tab">
                                    <i class="fas fa-calculator"></i> Accounting
                                </a>
                            </li>
                        </ul>

                        @php
                            $canEditSettings = auth()->user()->hasAnyPermission(['settings.edit', 'settings.update']);
                        @endphp
                        <form id="settingsForm" class="mt-4">
                            @csrf
                            <div class="tab-content" id="settingsTabContent">
                                <!-- Company Tab -->
                                <div class="tab-pane fade show active" id="company" role="tabpanel">
                                    @if (isset($settings['company']))
                                        @php
                                            $compByKey = [];
                                            foreach ($settings['company'] as $s) {
                                                $compByKey[$s['key']] = $s;
                                            }
                                        @endphp

                                        <!-- Section 1: Business Identity & Tax Details -->
                                        <div class="card mb-3 border shadow-sm" style="border-radius: 8px;">
                                            <div class="card-header bg-light py-2 px-3">
                                                <strong class="text-primary font-weight-bold" style="font-size: 13.5px;">
                                                    <i class="fas fa-building mr-1"></i> Business Identity & Tax Information
                                                </strong>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="row">
                                                    <!-- Company Name (4 cols) -->
                                                    @if(isset($compByKey['company_name']))
                                                        <div class="col-md-4 col-sm-6 mb-2">
                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold mb-1" style="font-size: 12.5px;">{{ $compByKey['company_name']['label'] }} <span class="text-danger">*</span></label>
                                                                <input type="text" name="settings[company_name]" class="form-control form-control-sm" value="{{ $compByKey['company_name']['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }} placeholder="e.g. Innot Tech" style="height: 35px;">
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Phone Number (3 cols) -->
                                                    @if(isset($compByKey['company_phone']))
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold mb-1" style="font-size: 12.5px;"><i class="fas fa-phone-alt text-secondary mr-1"></i> {{ $compByKey['company_phone']['label'] }}</label>
                                                                <input type="text" name="settings[company_phone]" class="form-control form-control-sm" value="{{ $compByKey['company_phone']['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }} placeholder="e.g. 0333-6722263" style="height: 35px;">
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- NTN Number (3 cols) -->
                                                    @if(isset($compByKey['company_ntn']))
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold mb-1" style="font-size: 12.5px;"><i class="fas fa-id-card text-info mr-1"></i> {{ $compByKey['company_ntn']['label'] }}</label>
                                                                <input type="text" name="settings[company_ntn]" class="form-control form-control-sm font-weight-bold" value="{{ $compByKey['company_ntn']['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }} placeholder="e.g. H126850-4" style="height: 35px; letter-spacing: 0.5px;">
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Currency Symbol (2 cols) -->
                                                    @if(isset($compByKey['currency_symbol']))
                                                        <div class="col-md-2 col-sm-6 mb-2">
                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold mb-1" style="font-size: 12.5px;"><i class="fas fa-money-bill-wave text-success mr-1"></i> {{ $compByKey['currency_symbol']['label'] }}</label>
                                                                <input type="text" name="settings[currency_symbol]" class="form-control form-control-sm text-center font-weight-bold" value="{{ $compByKey['currency_symbol']['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }} placeholder="PKR" style="height: 35px;">
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- STRN Number (4 cols) -->
                                                    @if(isset($compByKey['company_strn']))
                                                        <div class="col-md-4 col-sm-6 mb-2">
                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold mb-1" style="font-size: 12.5px;"><i class="fas fa-file-invoice text-warning mr-1"></i> {{ $compByKey['company_strn']['label'] }}</label>
                                                                <input type="text" name="settings[company_strn]" class="form-control form-control-sm font-weight-bold" value="{{ $compByKey['company_strn']['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }} placeholder="e.g. 32-77-8763-950-94" style="height: 35px; letter-spacing: 0.5px;">
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Company Address (8 cols) -->
                                                    @if(isset($compByKey['company_address']))
                                                        <div class="col-md-8 col-sm-12 mb-2">
                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold mb-1" style="font-size: 12.5px;"><i class="fas fa-map-marker-alt text-danger mr-1"></i> {{ $compByKey['company_address']['label'] }}</label>
                                                                <input type="text" name="settings[company_address]" class="form-control form-control-sm" value="{{ $compByKey['company_address']['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }} placeholder="e.g. 7/A, Block-B, Unit No 10, Latifabad, Hyderabad" style="height: 35px;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Section 2: Social Media & Website Profiles -->
                                        <div class="card mb-3 border shadow-sm" style="border-radius: 8px;">
                                            <div class="card-header bg-light py-2 px-3">
                                                <strong class="text-secondary font-weight-bold" style="font-size: 13.5px;">
                                                    <i class="fas fa-share-alt mr-1"></i> Online & Social Profiles (Shown on Receipts/Website)
                                                </strong>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="row">
                                                    <!-- Website Link (3 cols) -->
                                                    @if(isset($compByKey['website_link']))
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold mb-1" style="font-size: 12px;"><i class="fas fa-globe text-primary mr-1"></i> Website URL</label>
                                                                <input type="text" name="settings[website_link]" class="form-control form-control-sm" value="{{ $compByKey['website_link']['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }} placeholder="https://yourwebsite.com" style="height: 35px;">
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Facebook Link (3 cols) -->
                                                    @if(isset($compByKey['facebook_link']))
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold mb-1" style="font-size: 12px;"><i class="fab fa-facebook text-primary mr-1"></i> Facebook</label>
                                                                <input type="text" name="settings[facebook_link]" class="form-control form-control-sm" value="{{ $compByKey['facebook_link']['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }} placeholder="https://facebook.com/page" style="height: 35px;">
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Instagram Link (3 cols) -->
                                                    @if(isset($compByKey['instagram_link']))
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold mb-1" style="font-size: 12px;"><i class="fab fa-instagram text-danger mr-1"></i> Instagram</label>
                                                                <input type="text" name="settings[instagram_link]" class="form-control form-control-sm" value="{{ $compByKey['instagram_link']['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }} placeholder="https://instagram.com/page" style="height: 35px;">
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- TikTok Link (3 cols) -->
                                                    @if(isset($compByKey['tiktok_link']))
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div class="form-group mb-2">
                                                                <label class="font-weight-bold mb-1" style="font-size: 12px;"><i class="fab fa-tiktok text-dark mr-1"></i> TikTok</label>
                                                                <input type="text" name="settings[tiktok_link]" class="form-control form-control-sm" value="{{ $compByKey['tiktok_link']['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }} placeholder="https://tiktok.com/@page" style="height: 35px;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fallback for any other custom company settings -->
                                        @php
                                            $handledKeys = ['company_name', 'company_address', 'company_phone', 'currency_symbol', 'company_ntn', 'company_strn', 'website_link', 'facebook_link', 'instagram_link', 'tiktok_link'];
                                            $unhandled = array_filter($settings['company'], function($item) use ($handledKeys) {
                                                return !in_array($item['key'], $handledKeys);
                                            });
                                        @endphp
                                        @if(count($unhandled) > 0)
                                            <div class="row">
                                                @foreach ($unhandled as $setting)
                                                    <div class="col-md-4 col-sm-6 mb-2">
                                                        <div class="form-group mb-2">
                                                            <label class="font-weight-bold mb-1" style="font-size: 12px;">{{ $setting['label'] }}</label>
                                                            @if ($setting['type'] === 'text')
                                                                <textarea name="settings[{{ $setting['key'] }}]" class="form-control form-control-sm" rows="2" {{ !$canEditSettings ? 'disabled' : '' }}>{{ $setting['value'] }}</textarea>
                                                            @else
                                                                <input type="text" name="settings[{{ $setting['key'] }}]" class="form-control form-control-sm" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }} style="height: 35px;">
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                <!-- Sales Tab -->
                                <div class="tab-pane fade" id="sales" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                        <div>
                                            <h5 class="mb-0 text-dark font-weight-bold"><i class="fas fa-file-contract text-primary mr-2"></i>Invoice Terms & Conditions</h5>
                                            <small class="text-muted">Manage multiple headings and paragraphs displayed on invoices.</small>
                                        </div>
                                        @if($canEditSettings)
                                            <button type="button" class="btn btn-sm btn-primary" id="erpAddTermBtn">
                                                <i class="fas fa-plus mr-1"></i> Add New Term
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Add/Edit Term Box -->
                                    <div id="erpTermFormWrapper" class="card shadow-none border mb-4" style="display: none; background: #fdfdfe; border-radius: 8px;">
                                        <div class="card-body p-3">
                                            <h6 class="font-weight-bold text-primary mb-3" id="erpTermFormTitle">
                                                <i class="fas fa-plus-circle mr-1"></i> Add New Term
                                            </h6>
                                            <input type="hidden" id="erpTermEditId" value="">
                                            <div class="form-group mb-2">
                                                <label class="font-weight-bold text-dark" style="font-size: 13px;">Heading <span class="text-danger">*</span></label>
                                                <input type="text" id="erpTermHeading" class="form-control" placeholder="e.g. Return Policy, Exchange Conditions, Payment Terms...">
                                            </div>
                                            <div class="form-group mb-2">
                                                <label class="font-weight-bold text-dark" style="font-size: 13px;">Paragraph / Details <span class="text-danger">*</span></label>
                                                <textarea id="erpTermParagraph" class="form-control" rows="4" placeholder="Write the detailed conditions or paragraph here..."></textarea>
                                            </div>
                                            <div class="form-group mb-3" style="max-width: 180px;">
                                                <label class="font-weight-bold text-dark" style="font-size: 13px;">Sort Order</label>
                                                <input type="number" id="erpTermSortOrder" class="form-control" value="0" min="0">
                                            </div>
                                            <div class="d-flex">
                                                <button type="button" class="btn btn-sm btn-primary mr-2" id="erpSaveTermBtn">
                                                    <i class="fas fa-save mr-1"></i> Save Term
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="erpCancelTermBtn">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Terms List Container -->
                                    <div id="erpTermsListContainer">
                                        @if(isset($termsAndConditions) && $termsAndConditions->count() > 0)
                                            @foreach($termsAndConditions as $term)
                                                <div class="card mb-3 border shadow-sm erp-term-card" id="erp-term-item-{{ $term->id }}" style="border-radius: 8px;">
                                                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                                                        <div>
                                                            <span class="badge {{ $term->is_active ? 'badge-success' : 'badge-secondary' }} mr-2">
                                                                {{ $term->is_active ? 'Active' : 'Inactive' }}
                                                            </span>
                                                            <strong class="text-dark" style="font-size: 14px;">{{ $term->heading }}</strong>
                                                            <small class="text-muted ml-2">(Order: {{ $term->sort_order }})</small>
                                                        </div>
                                                        @if($canEditSettings)
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="button" class="btn btn-outline-primary erp-edit-term-btn" 
                                                                    data-id="{{ $term->id }}"
                                                                    data-heading="{{ $term->heading }}"
                                                                    data-paragraph="{{ $term->paragraph }}"
                                                                    data-sort-order="{{ $term->sort_order }}"
                                                                    title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-warning erp-toggle-term-btn" 
                                                                    data-id="{{ $term->id }}"
                                                                    title="{{ $term->is_active ? 'Deactivate' : 'Activate' }}">
                                                                    <i class="fas {{ $term->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-danger erp-delete-term-btn" 
                                                                    data-id="{{ $term->id }}"
                                                                    data-heading="{{ $term->heading }}"
                                                                    title="Delete">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card-body py-2 px-3">
                                                        <p class="mb-0 text-secondary" style="font-size: 13px; line-height: 1.6; white-space: pre-line;">{{ $term->paragraph }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div id="erpNoTermsMsg" class="text-center py-4 border rounded bg-light">
                                                <i class="fas fa-file-contract text-muted mb-2" style="font-size: 2rem; opacity: 0.4;"></i>
                                                <p class="text-muted mb-0 font-weight-bold">No terms and conditions added yet.</p>
                                                <small class="text-muted">Click "Add New Term" to add headings and paragraphs.</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <!-- Inventory Tab -->
                                <div class="tab-pane fade" id="inventory" role="tabpanel">
                                    @if (isset($settings['inventory']))
                                        @foreach ($settings['inventory'] as $setting)
                                            <div class="form-group">
                                                <label>{{ $setting['label'] }}</label>
                                                <input type="number" name="settings[{{ $setting['key'] }}]"
                                                    class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <!-- Accounting Tab -->
                                <div class="tab-pane fade" id="accounting" role="tabpanel">
                                    @if (isset($settings['accounting']))
                                        @foreach ($settings['accounting'] as $setting)
                                            <div class="form-group">
                                                <label>{{ $setting['label'] }}</label>
                                                <input type="text" name="settings[{{ $setting['key'] }}]"
                                                    class="form-control" value="{{ $setting['value'] }}" {{ !$canEditSettings ? 'disabled' : '' }}>
                                                @if ($setting['description'])
                                                    <small
                                                        class="form-text text-muted">{{ $setting['description'] }}</small>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4">
                                @if($canEditSettings)
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Settings
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary" disabled style="opacity: 0.6; cursor: not-allowed;">
                                        <i class="fas fa-lock"></i> Save Settings (Read Only)
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // ERP Settings: Terms & Conditions Manager
                var csrfToken = $('input[name="_token"]').val() || $('meta[name="csrf-token"]').attr('content');

                // Open Add Form
                $(document).on('click', '#erpAddTermBtn', function(e) {
                    e.preventDefault();
                    $('#erpTermEditId').val('');
                    $('#erpTermHeading').val('');
                    $('#erpTermParagraph').val('');
                    $('#erpTermSortOrder').val('0');
                    $('#erpTermFormTitle').html('<i class="fas fa-plus-circle mr-1"></i> Add New Term');
                    $('#erpTermFormWrapper').show();
                    $('#erpTermHeading').focus();
                });

                // Cancel Form
                $(document).on('click', '#erpCancelTermBtn', function(e) {
                    e.preventDefault();
                    $('#erpTermFormWrapper').hide();
                    $('#erpTermEditId').val('');
                    $('#erpTermHeading').val('');
                    $('#erpTermParagraph').val('');
                    $('#erpTermSortOrder').val('0');
                });

                // Save Term (AJAX POST/PUT)
                $(document).on('click', '#erpSaveTermBtn', function(e) {
                    e.preventDefault();
                    var heading = $('#erpTermHeading').val().trim();
                    var paragraph = $('#erpTermParagraph').val().trim();
                    var sortOrder = $('#erpTermSortOrder').val() || 0;
                    var editId = $('#erpTermEditId').val();

                    if (!heading) {
                        Swal.fire({ icon: 'warning', title: 'Required', text: 'Please enter a heading.', timer: 2000 });
                        $('#erpTermHeading').focus();
                        return;
                    }
                    if (!paragraph) {
                        Swal.fire({ icon: 'warning', title: 'Required', text: 'Please enter paragraph details.', timer: 2000 });
                        $('#erpTermParagraph').focus();
                        return;
                    }

                    var isEdit = editId !== '';
                    var url = isEdit ? '/settings/terms/' + editId : '/settings/terms';
                    var method = isEdit ? 'PUT' : 'POST';

                    var $btn = $(this);
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

                    $.ajax({
                        url: url,
                        method: method,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: {
                            _token: csrfToken,
                            heading: heading,
                            paragraph: paragraph,
                            sort_order: sortOrder
                        },
                        success: function(res) {
                            $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Term');
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: res.message,
                                    timer: 1500
                                });
                                setTimeout(function() {
                                    location.reload();
                                }, 1200);
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error!', text: res.message || 'Operation failed' });
                            }
                        },
                        error: function(xhr) {
                            $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Term');
                            var msg = 'Failed to save term.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire({ icon: 'error', title: 'Error!', text: msg });
                        }
                    });
                });

                // Edit Term
                $(document).on('click', '.erp-edit-term-btn', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    var heading = $(this).data('heading');
                    var paragraph = $(this).data('paragraph');
                    var sortOrder = $(this).data('sort-order');

                    $('#erpTermEditId').val(id);
                    $('#erpTermHeading').val(heading);
                    $('#erpTermParagraph').val(paragraph);
                    $('#erpTermSortOrder').val(sortOrder);
                    $('#erpTermFormTitle').html('<i class="fas fa-edit mr-1"></i> Edit Term');
                    $('#erpTermFormWrapper').show();
                    $('#erpTermHeading').focus();
                });

                // Toggle Active Status
                $(document).on('click', '.erp-toggle-term-btn', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    $.ajax({
                        url: '/settings/terms/' + id + '/toggle',
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        data: { _token: csrfToken },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({ icon: 'success', title: 'Updated!', text: res.message, timer: 1200 });
                                setTimeout(function() { location.reload(); }, 1000);
                            }
                        },
                        error: function() {
                            Swal.fire({ icon: 'error', title: 'Error!', text: 'Could not update status.' });
                        }
                    });
                });

                // Delete Term
                $(document).on('click', '.erp-delete-term-btn', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');
                    var heading = $(this).data('heading');

                    Swal.fire({
                        title: 'Delete Term?',
                        html: 'Are you sure you want to delete <b>"' + heading + '"</b>?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Yes, Delete',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/settings/terms/' + id,
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': csrfToken },
                                data: { _token: csrfToken },
                                success: function(res) {
                                    if (res.success) {
                                        Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1200 });
                                        $('#erp-term-item-' + id).fadeOut(300, function() { $(this).remove(); });
                                    }
                                },
                                error: function() {
                                    Swal.fire({ icon: 'error', title: 'Error!', text: 'Failed to delete term.' });
                                }
                            });
                        }
                    });
                });
                $('#settingsForm').on('submit', function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: '{{ route('settings.update') }}',
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to update settings',
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
