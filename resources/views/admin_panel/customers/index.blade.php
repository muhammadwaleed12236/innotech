@extends('admin_panel.layout.app')

@section('content')
    <style>
        .customer-header-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }

        .customer-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
        }

        .customer-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .btn-action-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 36px;
            padding: 0 13px;
            font-weight: 600;
            border-radius: 8px;
            font-size: 0.84rem;
            line-height: 1;
            white-space: nowrap;
            transition: all 0.15s ease;
        }

        .filter-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 14px 16px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }

        .filter-control {
            height: 38px !important;
            border-radius: 8px !important;
            border: 1px solid #cbd5e1 !important;
            font-size: 0.85rem !important;
            color: #1e293b !important;
            box-shadow: none !important;
        }

        .filter-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
        }

        .filter-btn {
            height: 38px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-weight: 600;
            font-size: 0.84rem;
            white-space: nowrap;
        }

        #customersTable {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        #customersTable thead th {
            background-color: #f8fafc;
            color: #334155;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
            vertical-align: middle;
            padding: 12px 14px;
            border-top: none;
            border-bottom: 2px solid #e2e8f0;
        }

        #customersTable tbody td {
            vertical-align: middle;
            padding: 10px 14px;
            font-size: 0.86rem;
            color: #1e293b;
            border-color: #eef2f6;
        }

        #customersTable tbody tr:hover {
            background-color: #f8fafc;
        }

        .cust-action-group {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            white-space: nowrap;
        }

        .cust-action-group .btn {
            height: 30px;
            min-width: 30px;
            padding: 0 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            line-height: 1;
        }

        .cust-action-group .btn i.fa-toggle-on {
            color: #22c55e;
            font-size: 16px;
        }

        .cust-action-group .btn i.fa-toggle-off {
            color: #cbd5e1;
            font-size: 16px;
        }

        .detail-item {
            background: #f8fafc;
            border-radius: 8px;
            padding: 10px 14px;
            border: 1px solid #eef2f6;
            height: 100%;
        }

        .detail-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 3px;
        }

        .detail-val {
            font-size: 0.92rem;
            font-weight: 600;
            color: #1e293b;
            word-break: break-word;
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid px-4 py-3">

                <!-- Header & Toolbar -->
                <div class="customer-header-card">
                    <div class="customer-toolbar">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="mb-0 fw-bold text-dark">Customer Management</h4>
                                <span class="badge bg-primary text-white px-2 py-1" style="font-size: 0.72rem; border-radius: 6px;">
                                    {{ $customers->count() }} Records
                                </span>
                            </div>
                            <small class="text-muted">Manage customer profiles, balances, locations, and CSV imports.</small>
                        </div>

                        <div class="customer-actions">
                            <a href="{{ route('dynamic_fields.index', ['module' => 'customers']) }}" class="btn btn-outline-dark btn-action-custom" title="Configure dynamic fields, visibility and permissions">
                                <i class="fas fa-sliders-h text-primary"></i> Field Config
                            </a>
                            <a href="{{ route('customers.template') }}" class="btn btn-outline-secondary btn-action-custom" title="Download blank CSV template">
                                <i class="fas fa-file-csv text-primary"></i> Template
                            </a>
                            <a href="{{ route('customers.export') }}" class="btn btn-outline-success btn-action-custom" title="Export configured customer columns to CSV">
                                <i class="fas fa-file-download"></i> Export CSV
                            </a>
                            <button type="button" class="btn btn-outline-primary btn-action-custom" data-toggle="modal" data-target="#customerImportModal" id="openCustomerImportModalBtn" title="Import customers from CSV">
                                <i class="fas fa-file-upload"></i> Import CSV
                            </button>

                            @can('customers.view')
                                <a href="{{ route('customers.ledger') }}" class="btn btn-info btn-action-custom text-white">
                                    <i class="fas fa-book"></i> Ledger
                                </a>
                                <a href="{{ route('customer.payments') }}" class="btn btn-secondary btn-action-custom">
                                    <i class="fas fa-money-bill-wave"></i> Payments
                                </a>
                            @endcan

                            <a href="{{ route('customers.inactive') }}" class="btn btn-outline-danger btn-action-custom">
                                <i class="fas fa-user-slash"></i> Inactive
                            </a>

                            @can('customers.create')
                                <a href="{{ route('customers.create') }}" class="btn btn-primary btn-action-custom">
                                    <i class="fas fa-user-plus"></i> Add Customer
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline: none;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="outline: none;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Responsive Aligned Filter Bar -->
                <div class="filter-card">
                    <form method="GET" action="{{ route('customers.index') }}" class="row g-2 align-items-center">
                        <!-- 1. Universal Search Input -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="position-relative">
                                <i class="fas fa-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 13px; z-index: 2;"></i>
                                <input type="text" name="search" class="form-control form-control-sm filter-control"
                                    placeholder="Search name, code, phone, address..."
                                    value="{{ request('search') }}"
                                    style="padding-left: 34px !important;">
                            </div>
                        </div>

                        <!-- 2. Province Filter -->
                        <div class="col-6 col-md-3 col-xl-2">
                            <select name="province" id="cust_search_province" class="form-select form-select-sm form-control form-control-sm filter-control">
                                <option value="">All Provinces</option>
                                @foreach(app(\App\Services\LocationService::class)->getProvinces() as $p)
                                    <option value="{{ $p }}" {{ request('province') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. City Filter -->
                        <div class="col-6 col-md-3 col-xl-2">
                            <select name="city" id="cust_search_city" class="form-select form-select-sm form-control form-control-sm filter-control">
                                <option value="">All Cities</option>
                                @foreach(app(\App\Services\LocationService::class)->getCities(request('province')) as $c)
                                    <option value="{{ $c }}" {{ request('city') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 4. Area Filter -->
                        <div class="col-6 col-md-4 col-xl-2">
                            <select name="area" id="cust_search_area" class="form-select form-select-sm form-control form-control-sm filter-control">
                                <option value="">All Areas</option>
                                @foreach(app(\App\Services\LocationService::class)->getAreas(request('city')) as $a)
                                    <option value="{{ $a }}" {{ request('area') == $a ? 'selected' : '' }}>{{ $a }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 5. Customer Type Dropdown -->
                        <div class="col-6 col-md-4 col-xl-1">
                            <select name="customer_type" class="form-select form-select-sm form-control form-control-sm filter-control">
                                <option value="">All Types</option>
                                @if(isset($dropdownLookups['customer_types']))
                                    @foreach($dropdownLookups['customer_types'] as $optVal => $optLabel)
                                        <option value="{{ $optVal }}" {{ request('customer_type') == $optVal ? 'selected' : '' }}>
                                            {{ $optLabel }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- 6. Action Buttons -->
                        <div class="col-12 col-md-4 col-xl-2 d-flex align-items-center justify-content-md-end gap-1">
                            <button type="submit" class="btn btn-primary btn-sm px-3 filter-btn flex-grow-1 flex-xl-grow-0">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn btn-light border btn-sm px-2 text-secondary filter-btn" title="Reset Filters" style="width: 38px;">
                                <i class="fas fa-undo"></i>
                            </a>
                            <button type="button" class="btn btn-outline-secondary btn-sm px-2 filter-btn" data-bs-toggle="collapse" data-bs-target="#moreCustomerFilters" data-toggle="collapse" data-target="#moreCustomerFilters" title="More Filters" style="width: 38px;">
                                <i class="fas fa-sliders-h"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Collapsible Extra Filters -->
                    <div class="collapse pt-3 mt-3 border-top {{ (request('party_type') || request('zone') || request('filer_type') || request('status') || request('ntn_number')) ? 'show' : '' }}" id="moreCustomerFilters">
                        <form method="GET" action="{{ route('customers.index') }}" class="row g-2 align-items-end">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="province" value="{{ request('province') }}">
                            <input type="hidden" name="city" value="{{ request('city') }}">
                            <input type="hidden" name="area" value="{{ request('area') }}">
                            <input type="hidden" name="customer_type" value="{{ request('customer_type') }}">

                            <div class="col-md-3 col-sm-6">
                                <label class="form-label small text-muted mb-1 fw-bold">Party Category (Role)</label>
                                <select name="party_type" class="form-select form-select-sm form-control form-control-sm filter-control">
                                    <option value="">All Categories</option>
                                    <option value="customer" {{ request('party_type') == 'customer' ? 'selected' : '' }}>Customer Only</option>
                                    <option value="both" {{ request('party_type') == 'both' ? 'selected' : '' }}>Both (Customer &amp; Vendor)</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label small text-muted mb-1 fw-bold">Region / Zone</label>
                                <select name="zone" class="form-select form-select-sm form-control form-control-sm filter-control">
                                    <option value="">All Zones</option>
                                    @if(isset($dropdownLookups['zones']))
                                        @foreach($dropdownLookups['zones'] as $optVal => $optLabel)
                                            <option value="{{ $optVal }}" {{ request('zone') == $optVal ? 'selected' : '' }}>
                                                {{ $optLabel }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label small text-muted mb-1 fw-bold">Tax Status (Filer)</label>
                                <select name="filer_type" class="form-select form-select-sm form-control form-control-sm filter-control">
                                    <option value="">All Tax Status</option>
                                    <option value="filer" {{ request('filer_type') == 'filer' ? 'selected' : '' }}>Filer</option>
                                    <option value="non_filer" {{ request('filer_type') == 'non_filer' ? 'selected' : '' }}>Non-Filer</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label small d-none d-sm-block mb-1">&nbsp;</label>
                                <button type="submit" class="btn btn-sm btn-dark px-3 w-100 filter-btn">
                                    <i class="fas fa-filter"></i> Apply Extra Filters
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Dynamic Customers Table -->
                <div class="card shadow-sm border" style="border-radius: 12px; border-color: #e2e8f0 !important; overflow: hidden;">
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-hover align-middle mb-0" id="customersTable">
                            <thead>
                                <tr>
                                    @foreach($listFields as $field)
                                        @php
                                            $thAlign = in_array($field->field_key, ['status', 'party_type']) ? 'text-center' : (in_array($field->field_key, ['closing_balance', 'opening_balance', 'previous_balance', 'credit_limit']) ? 'text-end' : 'text-start');
                                        @endphp
                                        <th class="{{ $thAlign }}">{{ $field->label }}</th>
                                    @endforeach
                                    <th style="width: 190px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customers as $customer)
                                    <tr>
                                        @foreach($listFields as $field)
                                            @php
                                                $key = $field->field_key;
                                                $formattedVal = app(\App\Services\CustomerFieldService::class)->getFieldValue($customer, $field, true);
                                                $tdAlign = in_array($key, ['status', 'party_type']) ? 'text-center' : (in_array($key, ['closing_balance', 'opening_balance', 'previous_balance', 'credit_limit']) ? 'text-end fw-semibold' : '');
                                            @endphp

                                            <td class="{{ $tdAlign }}">
                                                @if($key === 'customer_id')
                                                    <a href="javascript:void(0)" class="btn-view-customer fw-bold text-primary" data-id="{{ $customer->id }}" title="Click to view details">
                                                        {{ $formattedVal }}
                                                    </a>

                                                @elseif($key === 'party_type')
                                                    @if(($customer->party_type ?? '') === 'both' || !empty($customer->linked_vendor_id))
                                                        <span class="badge bg-primary text-white px-2 py-1" style="font-size: 11px;">
                                                            <i class="fas fa-sync-alt me-1"></i>Both (Cust &amp; Vend)
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 11px;">Customer</span>
                                                    @endif

                                                @elseif($key === 'customer_name')
                                                    <a href="javascript:void(0)" class="btn-view-customer fw-bold text-dark text-decoration-none" data-id="{{ $customer->id }}">
                                                        {{ $formattedVal }}
                                                    </a>
                                                    @if(($customer->party_type ?? '') === 'both' || !empty($customer->linked_vendor_id))
                                                        <span class="badge bg-primary-subtle text-primary border border-primary ms-1 px-1.5 py-0" style="font-size: 10px;" title="Party active as Customer &amp; Vendor">Both</span>
                                                    @endif
                                                    @if($customer->customer_name_ur)
                                                        <small class="text-muted d-block" dir="rtl" style="font-family: 'Noto Nastaliq Urdu', serif;">
                                                            {{ $customer->customer_name_ur }}
                                                        </small>
                                                    @endif
                                                    @if($customer->customer_type)
                                                        <span class="badge bg-light text-dark border mt-1">{{ $customer->customer_type }}</span>
                                                    @endif

                                                @elseif($key === 'status')
                                                    @if($customer->status === 'active')
                                                        <span class="badge bg-success text-white">active</span>
                                                    @else
                                                        <span class="badge bg-secondary text-white">inactive</span>
                                                    @endif

                                                @elseif($field->field_type === 'url' && !empty($formattedVal) && $formattedVal !== '-')
                                                    <a href="{{ $formattedVal }}" target="_blank" class="text-primary">
                                                        <i class="fas fa-external-link-alt me-1"></i> Link
                                                    </a>

                                                @else
                                                    {{ $formattedVal }}
                                                @endif
                                            </td>
                                        @endforeach

                                        <!-- Actions Column -->
                                        <td class="text-center">
                                            <div class="cust-action-group">
                                                <button type="button" class="btn btn-outline-primary btn-view-customer" data-id="{{ $customer->id }}" title="View Customer Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                @include('admin_panel.partials.action_buttons', [
                                                    'editRoute' => route('customers.edit', $customer->id),
                                                    'deleteRoute' => route('customers.destroy', $customer->id),
                                                    'editIsLink' => true,
                                                    'permissions' => [
                                                        'edit' => 'customers.edit',
                                                        'delete' => 'customers.delete',
                                                    ],
                                                    'dataId' => $customer->id,
                                                ])

                                                @can('customers.edit')
                                                    <a href="{{ route('customers.toggleStatus', $customer->id) }}"
                                                        class="btn {{ $customer->status === 'active' ? 'btn-dark' : 'btn-secondary' }}"
                                                        title="Toggle Status">
                                                        <i class="fa-solid {{ $customer->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                    </a>
                                                @endcan

                                                @can('customers.view')
                                                    <a href="{{ route('customer.payments') }}" class="btn btn-info text-white" title="Customer Payments">
                                                        <i class="fas fa-money-bill-wave me-1"></i> Pay
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($listFields) + 1 }}" class="text-center py-4 text-muted">
                                            No customers found matching the criteria. Click <strong>+ Add Customer</strong> or adjust your search filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         DYNAMIC CUSTOMER VIEW DETAILS MODAL
    ══════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="customerDetailsModal" tabindex="-1" role="dialog" aria-labelledby="customerDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #334155); color:#fff; border-bottom: none;">
                    <div>
                        <h5 class="modal-title fw-bold" id="customerDetailsModalLabel">
                            <i class="fas fa-user-circle me-2 text-primary"></i> <span id="modalCustomerName">Customer Details</span>
                        </h5>
                        <small style="color: rgba(255,255,255,0.75);" id="modalCustomerCode">Loading...</small>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:.8; text-shadow:none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4" style="background:#f8fafc; max-height: calc(85vh - 120px); overflow-y: auto;">
                    <!-- Loading Spinner -->
                    <div id="modalLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Loading customer information...</p>
                    </div>

                    <!-- Details Content -->
                    <div id="modalDetailsContent" style="display: none;">
                        <!-- Top Summary Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <div class="p-3 bg-white border rounded-3 shadow-xs">
                                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Status</small>
                                    <div class="h6 mb-0 mt-1" id="modalCustomerStatus">-</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 bg-white border rounded-3 shadow-xs">
                                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Current Balance</small>
                                    <div class="h6 mb-0 mt-1 text-danger fw-bold" id="modalCustomerBalance">-</div>
                                </div>
                            </div>
                            <div class="col-sm-4 text-end">
                                <div class="p-3 bg-white border rounded-3 shadow-xs d-flex justify-content-end align-items-center gap-2 h-100">
                                    <a href="#" id="modalEditLink" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit me-1"></i> Edit Customer
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Fields Grouped by Section -->
                        <div id="modalDynamicSections"></div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         CUSTOMER IMPORT MODAL
    ══════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="customerImportModal" tabindex="-1" role="dialog" aria-labelledby="customerImportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius:16px; overflow:hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg,#4f46e5,#7c3aed); color:#fff; border-bottom: none;">
                    <div>
                        <h5 class="modal-title fw-bold" id="customerImportModalLabel">
                            <i class="fas fa-file-upload me-2"></i> Import Customers from CSV
                        </h5>
                        <small style="color: rgba(255,255,255,0.88);">
                            Create new customers or update existing records directly from spreadsheet.
                        </small>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:.8; text-shadow:none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4" style="background:#f8fafc;">
                    <form action="{{ route('customers.import.validate') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="alert alert-info d-flex gap-2 align-items-start mb-4" style="font-size:.85rem; border-left: 4px solid #3b82f6;">
                            <i class="fas fa-info-circle fs-5 mt-1 flex-shrink-0 text-primary"></i>
                            <div>
                                <strong>Quick Guide:</strong><br>
                                &bull; <strong>Create New Customers:</strong> Download the <a href="{{ route('customers.template') }}" class="alert-link fw-bold">Blank Template</a>, enter customer info, and import.<br>
                                &bull; <strong>Edit Existing Customers:</strong> Download current data via <a href="{{ route('customers.export') }}" class="alert-link fw-bold">Export CSV</a>, modify prices/limits/phones in Excel, and upload here.<br>
                                &bull; Existing customers are matched accurately by <strong>Customer ID</strong> or <strong>System ID</strong>.<br>
                                &bull; You can review all rows using <strong>Validate &amp; Preview</strong> or import right away using <strong>Direct Import</strong>.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold text-dark">Import Mode</label>
                                <select name="import_mode" class="form-control" required style="border-radius: 8px;">
                                    <option value="upsert" selected>Create &amp; Update (Recommended)</option>
                                    <option value="create_only">Create Only (Skip existing)</option>
                                    <option value="update_only">Update Only (Ignore new)</option>
                                </select>
                                <small class="text-muted">Controls whether existing records are updated or skipped.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold text-dark">Auto-create Missing Data</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="autoCreateCustCheck" name="auto_create" value="1" checked>
                                    <label class="form-check-label fw-bold ms-1" for="autoCreateCustCheck">
                                        Auto-create missing Types &amp; Zones
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1">Automatically registers new Customer Types or Zones found in the CSV.</small>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="fw-bold text-dark">Upload CSV File <span class="text-danger">*</span></label>
                            <input type="file" name="csv_file" class="form-control p-1" accept=".csv,.txt" required style="border-radius: 8px;">
                            <small class="text-muted">Supported formats: .csv, .txt (UTF-8 encoded). Max size: 10 MB.</small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap" style="gap: 10px;">
                            <a href="{{ route('customers.template') }}" class="btn btn-outline-primary btn-sm" style="border-radius: 8px;">
                                <i class="fas fa-download me-1"></i> Download Template
                            </a>

                            <div class="d-flex flex-wrap" style="gap: 8px;">
                                <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                                <button type="submit" name="action_type" value="preview" class="btn btn-primary px-3 fw-bold" style="border-radius: 8px;">
                                    <i class="fas fa-eye me-1"></i> Validate &amp; Preview
                                </button>
                                <button type="submit" name="action_type" value="direct" class="btn btn-success px-3 fw-bold" style="border-radius: 8px;">
                                    <i class="fas fa-bolt me-1"></i> Direct Import
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@include('admin_panel.partials.location_selector_script')
<script>
$(document).ready(function() {
    setupLocationCascade({
        province: '#cust_search_province',
        city: '#cust_search_city',
        area: '#cust_search_area'
    });
});
</script>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle View Details Modal
        $(document).on('click', '.btn-view-customer', function(e) {
            e.preventDefault();
            const customerId = $(this).data('id');
            if (!customerId) return;

            // Reset modal states
            $('#modalCustomerName').text('Customer Details');
            $('#modalCustomerCode').text('Loading...');
            $('#modalLoading').show();
            $('#modalDetailsContent').hide();
            $('#modalDynamicSections').empty();

            // Set edit route dynamically
            const editUrl = "{{ url('customers') }}/" + customerId + "/edit";
            $('#modalEditLink').attr('href', editUrl);

            $('#customerDetailsModal').modal('show');

            // Fetch dynamic details
            $.ajax({
                url: "{{ url('customers') }}/" + customerId + "/details",
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#modalCustomerName').text(res.customer.customer_name);
                        $('#modalCustomerCode').text('Customer Code: ' + (res.customer.customer_id || 'N/A'));
                        
                        const statusBadge = res.customer.status === 'active' 
                            ? '<span class="badge bg-success text-white">active</span>'
                            : '<span class="badge bg-secondary text-white">inactive</span>';
                        $('#modalCustomerStatus').html(statusBadge);

                        const balance = parseFloat(res.customer.balance) || 0;
                        $('#modalCustomerBalance').text('Rs. ' + balance.toLocaleString('en-US', {minimumFractionDigits: 2}));

                        // Group fields by section
                        const sections = {};
                        (res.fields || []).forEach(function(f) {
                            const sec = f.section || 'General Information';
                            if (!sections[sec]) {
                                sections[sec] = [];
                            }
                            sections[sec].push(f);
                        });

                        // Render each section
                        let html = '';
                        for (const secName in sections) {
                            html += '<div class="mb-4">';
                            html += '<h6 class="fw-bold text-primary border-bottom pb-2 mb-3" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.04em;">';
                            html += '<i class="fas fa-folder-open me-2"></i>' + secName;
                            html += '</h6>';
                            html += '<div class="row g-2">';

                            sections[secName].forEach(function(item) {
                                html += '<div class="col-sm-6 col-md-4 mb-2">';
                                html += '<div class="detail-item">';
                                html += '<div class="detail-label">' + item.label + '</div>';
                                
                                let displayVal = item.formatted;
                                if (!displayVal || displayVal === '-') {
                                    displayVal = '<span class="text-muted fst-italic">Not specified</span>';
                                } else if (item.type === 'url' && item.value) {
                                    displayVal = '<a href="' + item.value + '" target="_blank" class="text-primary"><i class="fas fa-external-link-alt me-1"></i>' + item.value + '</a>';
                                }
                                
                                html += '<div class="detail-val">' + displayVal + '</div>';
                                html += '</div>';
                                html += '</div>';
                            });

                            html += '</div></div>';
                        }

                        $('#modalDynamicSections').html(html);
                        $('#modalLoading').hide();
                        $('#modalDetailsContent').fadeIn(200);
                    } else {
                        $('#modalLoading').html('<div class="alert alert-danger">Failed to load customer details.</div>');
                    }
                },
                error: function() {
                    $('#modalLoading').html('<div class="alert alert-danger">An error occurred while fetching customer details.</div>');
                }
            });
        });
    });
</script>
@include('admin_panel.partials.location_selector_script')
<script>
$(document).ready(function() {
    setupLocationCascade({
        province: '#cust_search_province',
        city: '#cust_search_city',
        area: '#cust_search_area'
    });
});
</script>
@endsection
