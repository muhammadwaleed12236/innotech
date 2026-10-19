@extends('admin_panel.layout.app')

@section('content')
<style>
    @import url('/assets/fonts/noto-nastaliq/noto-nastaliq.css');

    .customer-form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px 28px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        margin-bottom: 30px;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding-bottom: 20px;
        margin-bottom: 24px;
        border-bottom: 1px solid #f1f5f9;
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
    }

    .btn-header-secondary {
        background: #ffffff;
        border: 1px solid #d1d5db;
        color: #374151;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 8px 18px;
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
        font-size: 0.88rem;
        padding: 8px 22px;
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

<div class="main-content">
    <div class="main-content-inner">
        <div class="container-fluid px-4 py-3">

            <form action="{{ route('customers.update', $customer->id) }}" method="POST" class="needs-validation customer-form-card" novalidate>
                @csrf

                <!-- Form Header -->
                <div class="form-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon-box">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-dark" style="font-size: 1.45rem; letter-spacing: -0.01em;">
                                Edit Customer: <span class="text-primary">{{ $customer->customer_name }}</span>
                            </h3>
                            <small class="text-muted">Code: {{ $customer->customer_id }}</small>
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
                            <i class="fas fa-save"></i> Update Customer
                        </button>
                    </div>
                </div>

                <!-- Form Fields Grid (Matching Mockup) -->
                @php
                    $formFields = isset($fields) && count($fields) > 0 
                        ? $fields 
                        : (isset($groupedFields) ? collect($groupedFields)->flatten(1) : collect());
                @endphp
                <div class="row g-2">
                    @forelse($formFields as $field)
                        @include('admin_panel.customers.partials.field_input', [
                            'field' => $field,
                            'customer' => $customer,
                            'dropdownLookups' => $dropdownLookups,
                            'isEdit' => true
                        ])
                    @empty
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                            <p>No customer fields are currently configured for editing.</p>
                            <a href="{{ route('dynamic_fields.index', ['module' => 'customers']) }}" class="btn btn-sm btn-primary">
                                Configure Fields
                            </a>
                        </div>
                    @endforelse
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
                e.preventDefault();

                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    let invalid = form.querySelector(':invalid');
                    if (invalid) invalid.focus();
                    return;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Confirm Update',
                        text: "Are you sure you want to update this customer's details?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#1d4ed8',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Update!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    form.submit();
                }
            });
        }
    });
</script>
@endsection
