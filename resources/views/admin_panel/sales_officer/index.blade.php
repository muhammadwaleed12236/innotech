@extends('admin_panel.layout.app')

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-3">

                <!-- Page Header -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div>
                        <h4 class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-users-cog text-primary me-2"></i>Order Bookers & Salesmen
                        </h4>
                        <p class="text-muted small mb-0">
                            Manage market order bookers (S.R), salesmen, their login portals & booking commissions.
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('booker.dashboard') }}" target="_blank" class="btn btn-outline-success shadow-sm">
                            <i class="fas fa-external-link-alt me-1"></i> Open Booker Portal
                        </a>
                        <button type="button" class="btn btn-primary shadow-sm" id="btnOpenCreateModal">
                            <i class="fas fa-plus-circle me-1"></i> Add Booker / Salesman
                        </button>
                    </div>
                </div>

                <!-- KPI Metric Cards -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Order Bookers (S.R)</span>
                                    <h3 class="fw-bold text-primary mb-0 mt-1">{{ $stats['total_bookers'] ?? 0 }}</h3>
                                </div>
                                <div class="bg-primary-subtle text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #e0f2fe; color: #0284c7;">
                                    <i class="fas fa-user-edit fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Salesmen (Delivery)</span>
                                    <h3 class="fw-bold text-info mb-0 mt-1">{{ $stats['total_salesmen'] ?? 0 }}</h3>
                                </div>
                                <div class="bg-info-subtle text-info rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #cffafe; color: #0891b2;">
                                    <i class="fas fa-truck fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Active Staff</span>
                                    <h3 class="fw-bold text-success mb-0 mt-1">{{ $stats['active_staff'] ?? 0 }}</h3>
                                </div>
                                <div class="bg-success-subtle text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #dcfce7; color: #16a34a;">
                                    <i class="fas fa-user-check fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Today's Bookings</span>
                                    <h4 class="fw-bold text-dark mb-0 mt-1 font-monospace">
                                        {{ $stats['today_bookings_count'] ?? 0 }}
                                        <small class="fs-6 text-muted fw-normal">({{ number_format($stats['today_bookings_val'] ?? 0, 0) }})</small>
                                    </h4>
                                </div>
                                <div class="bg-warning-subtle text-warning rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fef3c7; color: #d97706;">
                                    <i class="fas fa-clipboard-list fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Card -->
                <div class="card border-0 shadow-sm rounded-3 bg-white">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="row align-items-center gy-2">
                            <!-- Filter Tabs -->
                            <div class="col-md-7">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-link {{ $typeFilter === 'all' ? 'active' : '' }}" href="{{ route('sales.officer.index', ['type' => 'all']) }}">
                                            <i class="fas fa-list me-1"></i> All Staff
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $typeFilter === 'order_booker' ? 'active' : '' }}" href="{{ route('sales.officer.index', ['type' => 'order_booker']) }}">
                                            <i class="fas fa-user-edit me-1"></i> Order Bookers (S.R)
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $typeFilter === 'salesman' ? 'active' : '' }}" href="{{ route('sales.officer.index', ['type' => 'salesman']) }}">
                                            <i class="fas fa-truck me-1"></i> Salesmen
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Search -->
                            <div class="col-md-5">
                                <form action="{{ route('sales.officer.index') }}" method="GET" class="d-flex gap-2">
                                    <input type="hidden" name="type" value="{{ $typeFilter }}">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                        <input type="text" name="q" class="form-control border-start-0" placeholder="Search by name, ID, mobile, CNIC..." value="{{ $search ?? '' }}">
                                        @if(!empty($search))
                                            <a href="{{ route('sales.officer.index', ['type' => $typeFilter]) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-light border">Filter</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-center">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 110px;" class="text-start ps-3">ID / Code</th>
                                        <th class="text-start">Name</th>
                                        <th>Role / Type</th>
                                        <th>Reference</th>
                                        <th>Phone / Mobile</th>
                                        <th>CNIC</th>
                                        <th>Portal Access</th>
                                        <th style="width: 100px;">Status</th>
                                        <th style="width: 130px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($officers as $officer)
                                        <tr id="row-{{ $officer->id }}">
                                            <td class="text-start ps-3 font-monospace fw-bold text-primary">
                                                {{ $officer->code ?? ('SO-' . str_pad($officer->id, 4, '0', STR_PAD_LEFT)) }}
                                            </td>
                                            <td class="text-start">
                                                <div class="fw-bold text-dark">{{ $officer->name }}</div>
                                                @if($officer->name_urdu)
                                                    <div class="text-muted small" dir="rtl">{{ $officer->name_urdu }}</div>
                                                @endif
                                                @if($officer->commission_rate > 0)
                                                    <span class="badge bg-light text-muted border font-monospace mt-1" style="font-size: 10px;">
                                                        Comm: {{ number_format($officer->commission_rate, 1) }}%
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($officer->type === 'order_booker')
                                                    <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1">
                                                        <i class="fas fa-user-edit me-1"></i> Order Booker
                                                    </span>
                                                @elseif($officer->type === 'salesman')
                                                    <span class="badge bg-info-subtle text-info border border-info px-2 py-1">
                                                        <i class="fas fa-truck me-1"></i> Salesman
                                                    </span>
                                                @else
                                                    <span class="badge bg-purple-subtle text-dark border px-2 py-1" style="background-color: #ede9fe; color: #6d28d9;">
                                                        <i class="fas fa-users-cog me-1"></i> Both (Booker & Sales)
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-muted small">
                                                {{ $officer->reference ?: '—' }}
                                            </td>
                                            <td class="font-monospace">
                                                <a href="tel:{{ $officer->mobile }}" class="text-dark text-decoration-none">
                                                    <i class="fas fa-phone-alt text-success me-1 small"></i>{{ $officer->mobile }}
                                                </a>
                                            </td>
                                            <td class="font-monospace small text-muted">
                                                {{ $officer->cnic ?: '—' }}
                                            </td>
                                            <td>
                                                @if($officer->user)
                                                    <span class="badge bg-success-subtle text-success border border-success px-2 py-1" title="Login: {{ $officer->user->email }}">
                                                        <i class="fas fa-check-circle me-1"></i> Active
                                                        <br><small class="text-muted font-monospace">{{ $officer->user->email }}</small>
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-muted border px-2 py-1">
                                                        <i class="fas fa-times-circle me-1"></i> Disabled
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-check form-switch d-inline-block">
                                                    <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                                           data-id="{{ $officer->id }}" {{ $officer->status ? 'checked' : '' }}
                                                           style="cursor: pointer; width: 36px; height: 18px;">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary btn-edit" data-id="{{ $officer->id }}" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-delete" data-id="{{ $officer->id }}" data-name="{{ $officer->name }}" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5 text-muted">
                                                <i class="fas fa-user-slash fs-2 mb-2 d-block opacity-50"></i>
                                                No Order Bookers or Salesmen found. Click "+ Add Booker / Salesman" to create one.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($officers->hasPages())
                        <div class="card-footer bg-white border-top py-2 d-flex justify-content-end">
                            {{ $officers->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- CREATE / EDIT MODAL -->
    <!-- Form layout directly inspired by uploaded ERP screen: media_1795058404320_4a3c755e.png -->
    <div class="modal fade" id="officerModal" tabindex="-1" role="dialog" aria-labelledby="officerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content shadow border-0">
                <form id="officerForm" action="{{ route('sales-officer.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="edit_id" id="form_edit_id">

                    <div class="modal-header bg-light border-bottom py-3">
                        <h5 class="modal-title font-weight-bold text-dark" id="officerModalLabel">
                            <i class="fas fa-user-edit text-primary me-2"></i><span id="modalTitleText">Add Order Booker / S.R</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4" style="background-color: #fdfdfd;">
                        
                        <!-- Role Selection -->
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold text-secondary">Staff Role / Type <span class="text-danger">*</span> :</label>
                            <div class="col-sm-9">
                                <div class="d-flex gap-4 pt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" id="type_booker" value="order_booker" checked>
                                        <label class="form-check-label fw-semibold" for="type_booker">
                                            <i class="fas fa-user-edit text-primary me-1"></i> Order Booker (S.R)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" id="type_salesman" value="salesman">
                                        <label class="form-check-label fw-semibold" for="type_salesman">
                                            <i class="fas fa-truck text-info me-1"></i> Salesman (Delivery)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" id="type_both" value="both">
                                        <label class="form-check-label fw-semibold" for="type_both">
                                            Both Roles
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-2 border-secondary-subtle">

                        <!-- Booker / S.R ID -->
                        <div class="row mb-3 align-items-center">
                            <label for="form_code" class="col-sm-3 col-form-label fw-bold text-dark">
                                Booker / S.R ID :
                            </label>
                            <div class="col-sm-4">
                                <input type="text" name="code" id="form_code" class="form-control font-monospace fw-bold" 
                                       value="{{ $nextBookerCode }}" placeholder="e.g. B-0031" style="background-color: #f8fafc;">
                            </div>
                            <div class="col-sm-5">
                                <small class="text-muted">Auto-generated unique identifier</small>
                            </div>
                        </div>

                        <!-- Booker / S.R Name -->
                        <div class="row mb-3 align-items-center">
                            <label for="form_name" class="col-sm-3 col-form-label fw-bold text-dark">
                                Booker / S.R Name <span class="text-danger">*</span> :
                            </label>
                            <div class="col-sm-9">
                                <input type="text" name="name" id="form_name" class="form-control form-control-lg fw-semibold" 
                                       placeholder="Enter staff full name" required style="border: 2px solid #fdba74; background-color: #fffbeb;">
                            </div>
                        </div>

                        <!-- Urdu Name (Optional) -->
                        <div class="row mb-3 align-items-center">
                            <label for="form_name_urdu" class="col-sm-3 col-form-label fw-bold text-secondary">
                                نام (Urdu) :
                            </label>
                            <div class="col-sm-9">
                                <input type="text" name="name_urdu" id="form_name_urdu" class="form-control text-end" dir="rtl" placeholder="آرڈر بکر کا نام اردو میں">
                            </div>
                        </div>

                        <!-- Reference -->
                        <div class="row mb-3 align-items-center">
                            <label for="form_reference" class="col-sm-3 col-form-label fw-bold text-dark">
                                Reference :
                            </label>
                            <div class="col-sm-9">
                                <input type="text" name="reference" id="form_reference" class="form-control" placeholder="Guarantor / Reference Person / Agency">
                            </div>
                        </div>

                        <!-- Cell / Phone No. -->
                        <div class="row mb-3 align-items-center">
                            <label for="form_mobile" class="col-sm-3 col-form-label fw-bold text-dark">
                                Cell / Phone No. <span class="text-danger">*</span> :
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" name="mobile" id="form_mobile" class="form-control font-monospace" placeholder="0300-1234567" required>
                                </div>
                            </div>
                        </div>

                        <!-- CNIC No. -->
                        <div class="row mb-3 align-items-center">
                            <label for="form_cnic" class="col-sm-3 col-form-label fw-bold text-dark">
                                CNIC No. :
                            </label>
                            <div class="col-sm-5">
                                <input type="text" name="cnic" id="form_cnic" class="form-control font-monospace" placeholder="42101-1234567-1" maxlength="20">
                            </div>
                            <div class="col-sm-4">
                                <small class="text-muted">Format: 13 digits with dashes</small>
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="row mb-3 align-items-center">
                            <label for="form_email" class="col-sm-3 col-form-label fw-bold text-dark">
                                Email Address :
                            </label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" id="form_email" class="form-control" placeholder="booker@example.com">
                                </div>
                            </div>
                        </div>

                        <!-- Postal Address -->
                        <div class="row mb-3 align-items-start">
                            <label for="form_address" class="col-sm-3 col-form-label fw-bold text-dark">
                                Postal Address :
                            </label>
                            <div class="col-sm-9">
                                <textarea name="address" id="form_address" class="form-control" rows="2" placeholder="Full residential / postal address..."></textarea>
                            </div>
                        </div>

                        <!-- Commission Rate (%) -->
                        <div class="row mb-3 align-items-center">
                            <label for="form_commission_rate" class="col-sm-3 col-form-label fw-bold text-dark">
                                Commission Rate (%) :
                            </label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="commission_rate" id="form_commission_rate" class="form-control" value="0.00">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <small class="text-muted">Default booking commission</small>
                            </div>
                        </div>

                        <!-- PORTAL LOGIN ACCESS SECTION -->
                        <div class="card border rounded-3 p-3 mt-3" style="background-color: #f0fdf4; border-color: #86efac !important;">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="allow_portal_access" id="form_allow_portal" value="1" checked>
                                <label class="form-check-label fw-bold text-success" for="form_allow_portal">
                                    <i class="fas fa-key me-1"></i> Allow Booker Portal Access (Field Login)
                                </label>
                            </div>
                            <p class="small text-muted mb-2">
                                Order booker can login at <code class="text-primary">{{ url('login') }}</code> to book orders in the market from their phone/tablet.
                            </p>

                            <div id="portalCredentialsBox" class="row g-2 mt-1">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Portal Login Email / Username <span class="text-danger">*</span></label>
                                    <input type="text" name="portal_email" id="form_portal_email" class="form-control form-control-sm" placeholder="e.g. booker1@distributor.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">Password <span class="text-danger" id="passRequiredStar">*</span></label>
                                    <input type="text" name="portal_password" id="form_portal_password" class="form-control form-control-sm font-monospace" placeholder="Default: 123456" value="123456">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light border-top py-2 px-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold" id="btnSubmitForm">
                            <i class="fas fa-save me-1"></i> Save Staff Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            const nextBookerCode = "{{ $nextBookerCode }}";
            const nextSalesmanCode = "{{ $nextSalesmanCode }}";

            // Open Create Modal
            $('#btnOpenCreateModal').on('click', function(e) {
                e.preventDefault();
                $('#officerForm')[0].reset();
                $('#form_edit_id').val('');
                $('#modalTitleText').text('Add Order Booker / S.R');
                $('#type_booker').prop('checked', true);
                $('#form_code').val(nextBookerCode);
                $('#form_allow_portal').prop('checked', true);
                $('#portalCredentialsBox').show();
                $('#passRequiredStar').show();
                $('#form_portal_password').val('123456');

                var myModal = new bootstrap.Modal(document.getElementById('officerModal'));
                myModal.show();
            });

            // When Type changes, auto generate code
            $('input[name="type"]').on('change', function() {
                if ($('#form_edit_id').val()) return; // Don't overwrite if editing

                let selectedType = $(this).val();
                $.ajax({
                    url: "{{ route('sales-officer.next-code') }}",
                    type: "GET",
                    data: { type: selectedType },
                    success: function(res) {
                        if (res && res.code) {
                            $('#form_code').val(res.code);
                        }
                    }
                });
            });

            // Sync email to portal login username if empty
            $('#form_email').on('input', function() {
                if (!$('#form_edit_id').val()) {
                    let em = $(this).val().trim();
                    if (em) {
                        $('#form_portal_email').val(em);
                    }
                }
            });

            // Toggle portal box
            $('#form_allow_portal').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#portalCredentialsBox').slideDown();
                } else {
                    $('#portalCredentialsBox').slideUp();
                }
            });

            // Form Submit via AJAX
            $('#officerForm').on('submit', function(e) {
                e.preventDefault();
                let $form = $(this);
                let $btn = $('#btnSubmitForm');
                let originalText = $btn.html();

                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $('#officerModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message || 'Staff saved successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let msg = 'Failed to save staff member.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: msg
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Load Edit Data
            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                if (!id) return;

                $.ajax({
                    url: "{{ url('sales-officers/edit') }}/" + id,
                    type: 'GET',
                    success: function(data) {
                        $('#form_edit_id').val(data.id);
                        $('#modalTitleText').text('Edit ' + (data.type === 'order_booker' ? 'Order Booker' : (data.type === 'salesman' ? 'Salesman' : 'Staff Member')));
                        
                        // Select type radio
                        if (data.type === 'salesman') {
                            $('#type_salesman').prop('checked', true);
                        } else if (data.type === 'both') {
                            $('#type_both').prop('checked', true);
                        } else {
                            $('#type_booker').prop('checked', true);
                        }

                        $('#form_code').val(data.code || '');
                        $('#form_name').val(data.name || '');
                        $('#form_name_urdu').val(data.name_urdu || '');
                        $('#form_reference').val(data.reference || '');
                        $('#form_mobile').val(data.mobile || '');
                        $('#form_cnic').val(data.cnic || '');
                        $('#form_email').val(data.email || '');
                        $('#form_address').val(data.address || '');
                        $('#form_commission_rate').val(data.commission_rate ?? '0.00');

                        if (data.has_portal_access) {
                            $('#form_allow_portal').prop('checked', true);
                            $('#portalCredentialsBox').show();
                            $('#form_portal_email').val(data.portal_email || '');
                            $('#form_portal_password').val('');
                            $('#passRequiredStar').hide(); // optional when editing
                        } else {
                            $('#form_allow_portal').prop('checked', false);
                            $('#portalCredentialsBox').hide();
                            $('#form_portal_email').val('');
                            $('#form_portal_password').val('');
                        }

                        var myModal = new bootstrap.Modal(document.getElementById('officerModal'));
                        myModal.show();
                    },
                    error: function() {
                        Swal.fire('Error', 'Could not load staff details.', 'error');
                    }
                });
            });

            // Toggle Status Switch
            $(document).on('change', '.status-toggle', function() {
                let id = $(this).data('id');
                let checkbox = $(this);

                $.ajax({
                    url: "{{ url('sales-officers/toggle-status') }}/" + id,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        Toast.fire({
                            icon: 'success',
                            title: res.message || 'Status updated!'
                        });
                    },
                    error: function() {
                        checkbox.prop('checked', !checkbox.is(':checked'));
                        Swal.fire('Error', 'Could not update status.', 'error');
                    }
                });
            });

            // Delete staff
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let name = $(this).data('name') || 'this staff member';

                Swal.fire({
                    title: 'Delete ' + name + '?',
                    text: 'Are you sure? If this staff has active bookings or sales, delete will be prevented.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('sales-officers') }}/" + id,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                if (res.success) {
                                    $('#row-' + id).fadeOut(300, function() { $(this).remove(); });
                                    Swal.fire('Deleted!', res.message, 'success');
                                } else {
                                    Swal.fire('Cannot Delete', res.message, 'warning');
                                }
                            },
                            error: function(xhr) {
                                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error deleting staff.';
                                Swal.fire('Error', msg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
