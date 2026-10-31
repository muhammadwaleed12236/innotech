@extends('admin_panel.layout.app')

@section('content')
<style>
    @import url('/assets/fonts/noto-nastaliq/noto-nastaliq.css');

    .customer-create-page {
        padding-bottom: 40px;
    }

    /* Page Header Card */
    .form-header-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
    }

    .header-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        border: 1px solid #dbeafe;
        flex-shrink: 0;
    }

    /* Section Cards */
    .form-section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 18px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }

    .section-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0;
    }

    .section-body {
        padding: 20px 22px;
    }

    /* Buttons */
    .btn-header-secondary {
        background: #ffffff;
        border: 1px solid #d1d5db;
        color: #374151;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.15s ease-in-out;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none !important;
    }

    .btn-header-secondary:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #111827;
    }

    .btn-header-primary {
        background: #1d4ed8;
        border: 1px solid #1d4ed8;
        color: #ffffff;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 8px 20px;
        border-radius: 8px;
        transition: all 0.15s ease-in-out;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .btn-header-primary:hover {
        background: #1e40af;
        border-color: #1e40af;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(29, 78, 216, 0.2);
    }

    /* Input Focus Styling */
    .input-group:focus-within {
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        border-radius: 8px;
    }

    .input-group:focus-within .input-group-text {
        border-color: #2563eb !important;
    }

    .input-group:focus-within .form-control,
    .input-group:focus-within .form-select {
        border-color: #2563eb !important;
        outline: none;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: none !important;
    }
</style>

<div class="main-content customer-create-page">
    <div class="main-content-inner">
        <div class="container-fluid px-3 px-md-4 py-3">

            <form action="{{ route('customers.store') }}" method="POST" class="needs-validation" novalidate id="customerForm">
                @csrf

                <!-- Form Header Card -->
                <div class="form-header-card d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon-box">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-dark" style="font-size: 1.35rem; letter-spacing: -0.01em;">New Customer</h3>
                            <small class="text-muted">Create customer account with complete contact, location and financial details</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('dynamic_fields.index', ['module' => 'customers']) }}" class="btn-header-secondary" title="Configure dynamic fields and visibility">
                            <i class="fas fa-cog text-secondary"></i> Configure Fields
                        </a>
                        <a href="{{ route('customers.index') }}" class="btn-header-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn-header-primary">
                            <i class="fas fa-save"></i> Save Customer
                        </button>
                    </div>
                </div>

                <!-- Grouped Form Sections for Perfect Visual Hierarchy & Alignment -->
                @php
                    $sectionsMap = [
                        'Basic Information' => [
                            'icon' => 'fas fa-id-card text-primary',
                            'title' => 'Basic Customer Information',
                            'badge' => 'Profile'
                        ],
                        'Contact & Location' => [
                            'icon' => 'fas fa-map-marked-alt text-success',
                            'title' => 'Contact & Address Information',
                            'badge' => 'Location'
                        ],
                        'Financial Details' => [
                            'icon' => 'fas fa-wallet text-info',
                            'title' => 'Financial & Credit Limits',
                            'badge' => 'Accounts'
                        ],
                        'Financial Information' => [
                            'icon' => 'fas fa-wallet text-info',
                            'title' => 'Financial & Credit Limits',
                            'badge' => 'Accounts'
                        ],
                        'Tax & Legal Details' => [
                            'icon' => 'fas fa-file-invoice text-warning',
                            'title' => 'Tax & Legal Compliance',
                            'badge' => 'Tax'
                        ],
                        'Additional Information' => [
                            'icon' => 'fas fa-comment-alt text-secondary',
                            'title' => 'Remarks & Additional Notes',
                            'badge' => 'Notes'
                        ],
                    ];
                @endphp

                @if(isset($groupedFields) && count($groupedFields) > 0)
                    @foreach($groupedFields as $sectionName => $sectionFields)
                        @if(count($sectionFields) > 0)
                            @php
                                $meta = $sectionsMap[$sectionName] ?? [
                                    'icon' => 'fas fa-folder-open text-primary',
                                    'title' => $sectionName,
                                    'badge' => 'Section'
                                ];
                            @endphp
                            <div class="form-section-card">
                                <div class="section-header">
                                    <h5 class="section-title">
                                        <i class="{{ $meta['icon'] }} me-1"></i> {{ $meta['title'] }}
                                    </h5>
                                    <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 11px;">
                                        {{ count($sectionFields) }} fields
                                    </span>
                                </div>
                                <div class="section-body">
                                    <div class="row g-3">
                                        @foreach($sectionFields as $field)
                                            @include('admin_panel.customers.partials.field_input', [
                                                'field' => $field,
                                                'customer' => null,
                                                'latestId' => $latestId,
                                                'dropdownLookups' => $dropdownLookups,
                                                'isEdit' => false
                                            ])
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="card border-0 shadow-sm p-4 text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                        <p class="text-muted">No fields configured for customer creation.</p>
                        <a href="{{ route('dynamic_fields.index', ['module' => 'customers']) }}" class="btn btn-sm btn-primary">
                            Configure Fields
                        </a>
                    </div>
                @endif

                <!-- Bottom Action Bar -->
                <div class="d-flex align-items-center justify-content-end gap-2 mt-4 p-3 bg-white border rounded-3 shadow-sm">
                    <a href="{{ route('customers.index') }}" class="btn-header-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn-header-primary">
                        <i class="fas fa-save"></i> Save Customer
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let nameField = document.querySelector('input[name="customer_name"]') || document.querySelector('input[type="text"]:not([readonly])');
        if (nameField) nameField.focus();

        const form = document.querySelector('form.needs-validation');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    form.classList.add('was-validated');
                    let invalid = form.querySelector(':invalid');
                    if (invalid) invalid.focus();
                }
            });
        }
    });
</script>
@include('admin_panel.partials.location_selector_script')
@endsection
