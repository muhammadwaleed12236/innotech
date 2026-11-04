@extends('admin_panel.layout.app')

@section('content')
    <style>
        /* Modern Sales Management Styles */
        .sale-stat-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
            height: 100%;
        }
        .sale-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .sale-stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        /* Responsive Status Filter Pills */
        .sales-status-pills {
            display: flex !important;
            align-items: center;
            gap: 8px;
            overflow-x: auto !important;
            white-space: nowrap !important;
            padding-bottom: 6px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }
        .sales-status-pills::-webkit-scrollbar {
            height: 4px;
        }
        .sales-status-pills::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .sales-status-pills .btn {
            flex-shrink: 0 !important;
        }

        /* Clean & Bold Filter Panel */
        .filter-panel {
            background-color: #f8fafc !important;
            border: 2px dashed #cbd5e1 !important;
            border-radius: 12px !important;
            padding: 16px !important;
        }
        
        .filter-panel label {
            font-size: 11px;
            font-weight: 700 !important;
            color: #475569 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .filter-panel .form-control,
        .filter-panel .form-select {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            transition: all 0.2s ease-in-out;
            height: 38px !important;
            font-size: 13px !important;
        }
        
        .filter-panel .form-control:focus,
        .filter-panel .form-select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        }

        /* Select2 In Filter Panel */
        .filter-panel .select2-container {
            width: 100% !important;
        }
        .filter-panel .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 5px 8px !important;
            background-color: #ffffff !important;
        }
        .filter-panel .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            color: #1e293b !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            padding-left: 0 !important;
        }
        .filter-panel .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 6px !important;
        }
        .filter-panel .select2-container--default.select2-container--focus .select2-selection--single,
        .filter-panel .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        }
        .select2-dropdown {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.1) !important;
            z-index: 9999 !important;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 6px !important;
            padding: 6px 10px !important;
            font-size: 13px !important;
        }

        /* Premium Buttons */
        .btn-premium-primary {
            background-color: #2563eb !important;
            border: 1.5px solid #1d4ed8 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            height: 38px !important;
            padding: 0 16px !important;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-premium-primary:hover {
            background-color: #1d4ed8 !important;
            transform: translateY(-1px);
        }
        
        .btn-premium-secondary {
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            height: 38px !important;
            padding: 0 16px !important;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-premium-secondary:hover {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #1e293b !important;
        }

        /* Premium Table Styling */
        .premium-card {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
            background-color: #ffffff;
        }

        .premium-table {
            border: 2px solid #475569 !important;
            border-radius: 8px !important;
            overflow: visible !important;
        }

        .card-body > .table-responsive {
            border-radius: 8px !important;
            overflow-x: auto !important;
            min-height: 380px;
        }

        #viewSaleModal .modal-dialog {
            max-width: 940px;
            margin: 1.75rem auto;
        }

        #viewSaleModal .table-responsive {
            min-height: auto !important;
            max-height: 280px !important;
            border: 1px solid #e2e8f0;
            border-radius: 6px !important;
        }

        #viewSaleModal .modal-body {
            max-height: calc(100vh - 180px);
            overflow-y: auto;
            background-color: #f8fafc !important;
        }

        /* Dropdown Menu Customizations */
        .dropdown-menu {
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.15), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            padding: 6px 0 !important;
            z-index: 1060 !important;
        }
        .dropdown-item {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            padding: 8px 16px !important;
            transition: all 0.15s ease-in-out !important;
        }
        .dropdown-item:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
        .dropdown-divider {
            border-top: 1.5px solid #e2e8f0 !important;
            margin: 6px 0 !important;
        }
        
        .premium-table thead th {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            border-bottom: 3px solid #475569 !important;
            border-right: 1.5px solid #cbd5e1 !important;
            padding: 12px 10px !important;
            white-space: nowrap;
        }
        
        .premium-table tbody td {
            border: 1.5px solid #e2e8f0 !important;
            padding: 10px 10px !important;
            font-size: 13px !important;
            color: #334155 !important;
            background-color: #ffffff;
        }
        
        .premium-table tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        /* Dropdown Action Button */
        .btn-premium-action {
            background-color: #f8fafc !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 700 !important;
            border-radius: 6px !important;
            height: 32px !important;
            padding: 0 12px !important;
            font-size: 11px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease-in-out !important;
        }

        /* Mobile Optimization Rules (< 768px) */
        @media (max-width: 767.98px) {
            .sale-stat-card {
                padding: 10px 12px !important;
            }
            .sale-stat-card h4 {
                font-size: 1.05rem !important;
                word-break: break-word;
            }
            .sale-stat-icon {
                width: 36px !important;
                height: 36px !important;
                font-size: 16px !important;
            }
            .sales-hdr-actions {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                width: 100%;
            }
            .sales-hdr-actions a[href*="sale/create"] {
                grid-column: span 2;
            }
            .filter-actions-row {
                display: flex !important;
                width: 100%;
                gap: 8px;
                margin-top: 10px;
            }
            .filter-actions-row .btn {
                flex: 1;
            }
            #viewSaleModal .modal-footer {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                justify-content: space-between;
            }
            #viewSaleModal .modal-footer .btn {
                flex: 1 1 45%;
                font-size: 11px !important;
                padding: 6px 8px !important;
            }
            #viewSaleModal .modal-footer .btn-secondary {
                flex: 1 1 100%;
                margin-top: 4px;
            }
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                float: none !important;
                text-align: left !important;
                margin-bottom: 10px;
            }
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
            }
        }
        @media (min-width: 768px) {
            .sales-hdr-actions {
                display: flex;
                gap: 8px;
            }
        }
    </style>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="container-fluid py-4">

                {{-- Page Header --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="fas fa-shopping-cart text-primary"></i> Sales Management
                        </h4>
                        <p class="text-muted mb-0 small">View, search, filter and edit your sales invoices & bookings</p>
                    </div>
                    <div class="sales-hdr-actions">
                        <a class="btn btn-outline-danger px-3 shadow-sm fw-medium d-inline-flex align-items-center justify-content-center gap-1"
                            href="{{ route('sale.return.index') }}" style="border-radius: 8px;">
                            <i class="fas fa-undo"></i> Returns
                        </a>
                        <a class="btn btn-outline-primary px-3 shadow-sm fw-medium d-inline-flex align-items-center justify-content-center gap-1"
                            href="{{ url('bookings') }}" style="border-radius: 8px;">
                            <i class="fas fa-bookmark"></i> Bookings
                        </a>
                        <a class="btn btn-outline-warning px-3 shadow-sm fw-medium d-inline-flex align-items-center justify-content-center gap-1 text-dark"
                            href="{{ route('sale.add', ['type' => 'quotation']) }}" style="border-radius: 8px;">
                            <i class="fas fa-file-alt"></i> Quotation
                        </a>
                        @can('sales.create')
                            <a class="btn btn-primary px-3 shadow-sm fw-medium d-inline-flex align-items-center justify-content-center gap-1"
                                href="{{ route('sale.add') }}" style="border-radius: 8px;">
                                <i class="fas fa-plus"></i> Add Sale
                            </a>
                        @endcan
                    </div>
                </div>

                {{-- KPI Stat Cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="sale-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Total Invoices</div>
                                    <h4 class="fw-bold text-dark mb-0 mt-1" id="statTotalCount">{{ number_format($stats['total_count'] ?? 0) }}</h4>
                                </div>
                                <div class="sale-stat-icon bg-primary-subtle text-primary" style="background-color: #eff6ff; color: #2563eb;">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sale-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Total Net Revenue</div>
                                    <h4 class="fw-bold text-success mb-0 mt-1" id="statTotalNet">Rs. {{ number_format($stats['total_net'] ?? 0, 2) }}</h4>
                                </div>
                                <div class="sale-stat-icon bg-success-subtle text-success" style="background-color: #ecfdf5; color: #059669;">
                                    <i class="fas fa-coins"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sale-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Discounts Given</div>
                                    <h4 class="fw-bold text-warning mb-0 mt-1" id="statTotalDiscount" style="color: #d97706 !important;">Rs. {{ number_format($stats['total_discount'] ?? 0, 2) }}</h4>
                                </div>
                                <div class="sale-stat-icon bg-warning-subtle text-warning" style="background-color: #fffbeb; color: #d97706;">
                                    <i class="fas fa-tags"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sale-stat-card">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px;">Posted / Booked</div>
                                    <h4 class="fw-bold text-info mb-0 mt-1" id="statStatusCounts" style="color: #0284c7 !important;">
                                        {{ $stats['posted_count'] ?? 0 }} <span class="fs-6 fw-normal text-muted">/ {{ $stats['booked_count'] ?? 0 }}</span>
                                    </h4>
                                </div>
                                <div class="sale-stat-icon bg-info-subtle text-info" style="background-color: #f0f9ff; color: #0284c7;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Filter Pills --}}
                <div class="mb-4 sales-status-pills">
                    <a href="{{ route('sale.index', ['status' => 'all']) }}"
                        class="btn btn-sm {{ request('status') == 'all' || !request('status') ? 'btn-secondary' : 'btn-outline-secondary' }} rounded-3 shadow-sm px-3 fw-bold">
                        All <span class="badge bg-white text-dark ms-1">{{ $stats['total_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('sale.index', ['status' => 'posted']) }}"
                        class="btn btn-sm {{ request('status') == 'posted' ? 'btn-success' : 'btn-outline-success' }} rounded-3 shadow-sm px-3 fw-bold">
                        Posted <span class="badge bg-white text-success ms-1">{{ $stats['posted_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('sale.index', ['status' => 'draft']) }}"
                        class="btn btn-sm {{ request('status') == 'draft' ? 'btn-warning text-dark' : 'btn-outline-warning' }} rounded-3 shadow-sm px-3 fw-bold">
                        Draft <span class="badge bg-white text-dark ms-1">{{ $stats['draft_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('sale.index', ['status' => 'booked']) }}"
                        class="btn btn-sm {{ request('status') == 'booked' ? 'btn-info text-white' : 'btn-outline-info' }} rounded-3 shadow-sm px-3 fw-bold">
                        Booked <span class="badge bg-white text-info ms-1">{{ $stats['booked_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('sale.index', ['status' => 'quotation']) }}"
                        class="btn btn-sm {{ request('status') == 'quotation' ? 'btn-primary' : 'btn-outline-primary' }} rounded-3 shadow-sm px-3 fw-bold">
                        Quotation <span class="badge bg-white text-primary ms-1">{{ $stats['quotation_count'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('sale.index', ['status' => 'returned']) }}"
                        class="btn btn-sm {{ request('status') == 'returned' ? 'btn-danger' : 'btn-outline-danger' }} rounded-3 shadow-sm px-3 fw-bold">
                        Returned <span class="badge bg-white text-danger ms-1">{{ $stats['returned_count'] ?? 0 }}</span>
                    </a>
                </div>

                <div class="card premium-card">
                    <div class="card-body p-3 p-md-4">
                        @if (session('success'))
                            <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ session('success') }}</span>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ session('error') }}</span>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Mobile Filter Panel Toggle Button --}}
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 d-md-none mb-3 fw-bold d-flex align-items-center justify-content-center gap-2" id="toggleFilterPanel">
                            <i class="fas fa-filter"></i> Search & Filters Toggle
                        </button>

                        {{-- AJAX Filter Panel --}}
                        <div class="card filter-panel mb-4" id="filterPanelContainer">
                            <div class="card-body p-0">
                                <form id="filterForm" class="row g-2 g-md-3 align-items-end" autocomplete="off" onsubmit="return false;">
                                    <div class="col-6 col-md-2">
                                        <label class="form-label mb-1">Quick Filter</label>
                                        <select id="quick_filter" class="form-select">
                                            <option value="custom">Custom Range</option>
                                            <option value="daily">Daily (Today)</option>
                                            <option value="weekly">Weekly (This Week)</option>
                                            <option value="monthly">Monthly (This Month)</option>
                                            <option value="yearly">Yearly (This Year)</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label mb-1">From Date</label>
                                        <input type="text" class="form-control datepicker-custom bg-white" name="from_date" id="filter_from_date" placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label mb-1">To Date</label>
                                        <input type="text" class="form-control datepicker-custom bg-white" name="to_date" id="filter_to_date" placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="col-6 col-md-2">
                                        <label class="form-label mb-1">Invoice / Bill#</label>
                                        <input type="text" class="form-control" name="bill_no" id="filter_bill_no" value="{{ request('bill_no') ?? request('invoice_no') }}" placeholder="Inv / Bill#...">
                                    </div>
                                    <div class="col-6 col-md-1">
                                        <label class="form-label mb-1">M.Bill / Ref</label>
                                        <input type="text" class="form-control" name="reference" id="filter_reference" placeholder="M.Bill...">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label mb-1">Customer</label>
                                        <select class="form-select select2-customer" name="customer_id" id="filter_customer_id" style="width: 100%;">
                                            <option value="">All Customers</option>
                                            @foreach ($customers as $c)
                                                <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                                    {{ $c->customer_name }} {{ $c->mobile ? '('.$c->mobile.')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2 mt-2 filter-actions-row">
                                        <button type="button" class="btn btn-premium-secondary px-3" id="btnReset">
                                            <i class="fas fa-undo me-1"></i>Reset
                                        </button>
                                        <button type="button" class="btn btn-premium-primary px-4" id="btnSearch">
                                            <i class="fas fa-search me-1"></i>Search
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Table Container --}}
                        <div class="table-responsive">
                            <table id="sales-table" class="table table-hover align-middle datanew premium-table" style="width:100%">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 ps-3 rounded-start text-secondary fw-semibold text-uppercase small">Invoice / Bill#</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Customer</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">M.Bill</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Products</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-center">Qty</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Gross</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Inline Disc</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Add. Disc</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small text-end">Net Total</th>
                                        <th class="py-3 text-secondary fw-semibold text-uppercase small">Date</th>
                                        <th class="py-3 pe-3 rounded-end text-secondary fw-semibold text-uppercase small text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="salesTableBody">
                                    @include('admin_panel.sale.partials.sales_table_body')
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize Select2 with search for customer dropdown
            if ($('.select2-customer').length > 0) {
                $('.select2-customer').select2({
                    placeholder: "All Customers",
                    allowClear: true,
                    width: '100%'
                });
            }

            // Function to initialize DataTable safely
            function initDataTable() {
                try {
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#sales-table')) {
                        $('#sales-table').DataTable().destroy();
                    }
                    if ($.fn.DataTable) {
                        $('#sales-table').DataTable({
                            "pageLength": 10,
                            "order": [],
                            "language": {
                                "search": "",
                                "searchPlaceholder": "Search sales..."
                            },
                            "dom": "<'row mb-3 align-items-center'<'col-12 col-md-6 mb-2 mb-md-0'l><'col-12 col-md-6'f>>" +
                                "<'row'<'col-12'tr>>" +
                                "<'row mt-3 align-items-center'<'col-12 col-md-5 mb-2 mb-md-0'i><'col-12 col-md-7'p>>",
                        });
                    }
                } catch(e) {
                    console.error("DataTable initialization error: ", e);
                }
            }

            // Initial call
            initDataTable();

            // Mobile Filter Panel Toggle
            $('#toggleFilterPanel').on('click', function() {
                $('#filterPanelContainer').slideToggle(200);
            });

            // Core AJAX Filter Function
            function applySalesFilter() {
                const $btn = $('#btnSearch');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Searching...');

                let formData = $('#filterForm').serialize();
                let urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('status')) {
                    formData += '&status=' + encodeURIComponent(urlParams.get('status'));
                }

                $.ajax({
                    url: '{{ route("sale.index") }}',
                    method: 'GET',
                    data: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        $btn.prop('disabled', false).html(origHtml);
                        
                        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#sales-table')) {
                            $('#sales-table').DataTable().destroy();
                        }
                        
                        $('#salesTableBody').html(response.html);
                        
                        // Update Stat Cards dynamically if present
                        if (response.stats) {
                            $('#statTotalCount').text(Number(response.stats.total_count || 0).toLocaleString());
                            $('#statTotalNet').text('Rs. ' + Number(response.stats.total_net || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#statTotalDiscount').text('Rs. ' + Number(response.stats.total_discount || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#statStatusCounts').html((response.stats.posted_count || 0) + ' <span class="fs-6 fw-normal text-muted">/ ' + (response.stats.booked_count || 0) + '</span>');
                        }

                        initDataTable();
                    },
                    error: function(err) {
                        $btn.prop('disabled', false).html(origHtml);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'Failed to retrieve filtered list.', 'error');
                        } else {
                            alert('Failed to retrieve filtered list.');
                        }
                    }
                });
            }

            // Quick Filter Logic
            $(document).on('change', '#quick_filter', function() {
                let val = $(this).val();
                if (val === 'custom') return;

                let today = new Date();
                let start = new Date();
                let end = new Date();

                if (val === 'daily') {
                    start = new Date();
                    end = new Date();
                } else if (val === 'weekly') {
                    let day = today.getDay();
                    let diff = today.getDate() - day + (day === 0 ? -6 : 1);
                    start = new Date(today.setDate(diff));
                    end = new Date();
                } else if (val === 'monthly') {
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    end = new Date();
                } else if (val === 'yearly') {
                    start = new Date(today.getFullYear(), 0, 1);
                    end = new Date();
                }

                let formatDate = function(d) {
                    let year = d.getFullYear();
                    let month = String(d.getMonth() + 1).padStart(2, '0');
                    let day = String(d.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };

                let startStr = formatDate(start);
                let endStr = formatDate(end);

                let pickerFrom = document.getElementById('filter_from_date') ? document.getElementById('filter_from_date')._flatpickr : null;
                let pickerTo = document.getElementById('filter_to_date') ? document.getElementById('filter_to_date')._flatpickr : null;

                if (pickerFrom) pickerFrom.setDate(startStr, true);
                else $("#filter_from_date").val(startStr);

                if (pickerTo) pickerTo.setDate(endStr, true);
                else $("#filter_to_date").val(endStr);

                applySalesFilter();
            });

            // Trigger search on button click & enter key
            $('#btnSearch').on('click', function(e) {
                e.preventDefault();
                applySalesFilter();
            });

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                applySalesFilter();
                return false;
            });

            $(document).on('keypress', '#filterForm input', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    applySalesFilter();
                    return false;
                }
            });

            // Reset form completely and fetch unfiltered list via AJAX
            $('#btnReset').on('click', function(e) {
                e.preventDefault();

                // 1. Explicitly clear all filter inputs
                $('#filter_from_date').val('');
                $('#filter_to_date').val('');
                $('#filter_bill_no').val('');
                $('#filter_reference').val('');
                $('#quick_filter').val('custom');
                
                // 2. Clear Select2 Customer Dropdown properly
                if ($('.select2-customer').length > 0) {
                    $('.select2-customer').val('').trigger('change');
                }
                
                // 3. Clear Flatpickr instances
                let fromElem = document.getElementById('filter_from_date');
                let toElem = document.getElementById('filter_to_date');
                if (fromElem && fromElem._flatpickr) {
                    fromElem._flatpickr.clear();
                }
                if (toElem && toElem._flatpickr) {
                    toElem._flatpickr.clear();
                }

                // Clear any Flatpickr visible alt-inputs inside filter container
                $('#filterPanelContainer .datepicker-custom').val('');
                $('#filterPanelContainer input.input').val('');

                // 4. Clear DataTables client search if any
                if ($.fn.DataTable && $.fn.DataTable.isDataTable('#sales-table')) {
                    $('#sales-table').DataTable().search('');
                }

                // 5. Clean browser URL query parameters (revert back to clean /sale or preserve status tab)
                let urlParams = new URLSearchParams(window.location.search);
                let newUrl = window.location.pathname;
                if (urlParams.has('status')) {
                    newUrl += '?status=' + encodeURIComponent(urlParams.get('status'));
                }
                if (window.history.replaceState) {
                    window.history.replaceState({}, '', newUrl);
                }

                // 6. Trigger AJAX to fetch complete unfiltered data
                applySalesFilter();
            });

            // Confirm Booking Action
            $(document).on('click', '.confirm-booking-btn', function(e) {
                e.preventDefault();
                let form = $(this).closest("form");

                Swal.fire({
                    title: "Confirm to Post?",
                    text: "This will convert the sale to Posted status. Stock will be deducted and ledger will be updated.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, Confirm it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // View Sale Modal AJAX Handler
            $(document).on('click', '.btn-view-sale-modal', function(e) {
                e.preventDefault();
                const saleId = $(this).data('id');
                const $modal = $('#viewSaleModal');
                const $modalBody = $('#viewSaleModalBody');
                
                $('#viewSaleModalTitle').text('Loading Details...');
                $('#viewSaleModalSubtitle').text('Sale ID #' + saleId);
                $('#viewSaleStatusBadge').html('');
                $modalBody.html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>
                        <p class="text-muted mt-2 small">Fetching sale details & taxes...</p>
                    </div>
                `);

                $modal.modal('show');

                $.ajax({
                    url: '/sales/' + saleId + '/details-modal',
                    method: 'GET',
                    success: function(res) {
                        if (!res.status) {
                            $modalBody.html('<div class="alert alert-danger">Failed to load details.</div>');
                            return;
                        }

                        const sale = res.sale;
                        const items = res.items || [];
                        const stats = res.stats || {};

                        const isQuotation = sale.sale_status === 'quotation';
                        const isBooked = sale.sale_status === 'booked';
                        const isReturned = sale.sale_status === 'returned';
                        
                        let docTitle = isQuotation ? 'Quotation #' + (sale.invoice_no || sale.id) : (isBooked ? 'Booking #' + (sale.invoice_no || sale.id) : 'Sale Invoice #' + sale.invoice_no);
                        $('#viewSaleModalTitle').text(docTitle);
                        $('#viewSaleModalSubtitle').text('Date: ' + sale.created_at_formatted + (sale.reference ? ' | Ref: ' + sale.reference : ''));

                        // Status Badge
                        let statusHtml = '<span class="badge badge-secondary px-2 py-1">Draft</span>';
                        if (sale.sale_status === 'posted') statusHtml = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Posted</span>';
                        else if (isBooked) statusHtml = '<span class="badge bg-info text-white"><i class="fas fa-bookmark me-1"></i>Booked</span>';
                        else if (isQuotation) statusHtml = '<span class="badge badge-primary text-white px-2 py-1"><i class="fas fa-file-alt mr-1"></i>Quotation</span>';
                        else if (isReturned) statusHtml = '<span class="badge bg-danger"><i class="fas fa-undo me-1"></i>Returned</span>';
                        $('#viewSaleStatusBadge').html(statusHtml);

                        // Set Links & Button Texts based on status
                        if (isQuotation) {
                            $('#viewSaleModalIcon').attr('class', 'fas fa-file-alt text-white');
                            $('#modalConfirmSaleForm').show().attr('action', '/sales/' + sale.id + '/confirm');
                            $('#modalBtnConvertToSale').html('<i class="fas fa-check-circle mr-1"></i> Convert to Sale');
                            $('#modalBtnPrintInvoice').show().attr('href', '/sales/' + sale.id + '/invoice').html('<i class="fas fa-print mr-1"></i> Print (Simple)');
                            $('#modalBtnPrintLetterhead').show().attr('href', '/sales/' + sale.id + '/invoice?print=letterhead').html('<i class="fas fa-file-alt mr-1"></i> Print (Letterhead)');
                            $('#modalBtnPrintDC').hide();
                            $('#modalBtnPrintReceipt').hide();
                            $('#modalBtnEditSale').attr('href', '/sales/' + sale.id + '/edit?type=quotation');
                        } else if (isBooked) {
                            $('#viewSaleModalIcon').attr('class', 'fas fa-bookmark text-white');
                            $('#modalConfirmSaleForm').show().attr('action', '/sales/' + sale.id + '/confirm');
                            $('#modalBtnConvertToSale').html('<i class="fas fa-check-circle mr-1"></i> Confirm Booking');
                            $('#modalBtnPrintInvoice').show().attr('href', '/sales/' + sale.id + '/invoice').html('<i class="fas fa-print me-1"></i> Print Booking');
                            $('#modalBtnPrintLetterhead').show().attr('href', '/sales/' + sale.id + '/invoice?print=letterhead').html('<i class="fas fa-file-alt me-1"></i> Letterhead');
                            $('#modalBtnPrintDC').hide();
                            $('#modalBtnPrintReceipt').show().attr('href', '/sales/' + sale.id + '/recepit');
                            $('#modalBtnEditSale').attr('href', '/sales/' + sale.id + '/edit');
                        } else {
                            $('#viewSaleModalIcon').attr('class', 'fas fa-file-invoice text-white');
                            $('#modalConfirmSaleForm').hide();
                            $('#modalBtnPrintInvoice').show().attr('href', '/sales/' + sale.id + '/invoice').html('<i class="fas fa-print me-1"></i> Print (Simple)');
                            $('#modalBtnPrintLetterhead').show().attr('href', '/sales/' + sale.id + '/invoice?print=letterhead').html('<i class="fas fa-file-alt me-1"></i> Print (Letterhead)');
                            $('#modalBtnPrintDC').show().attr('href', '/sales/' + sale.id + '/dc');
                            $('#modalBtnPrintReceipt').show().attr('href', '/sales/' + sale.id + '/recepit');
                            $('#modalBtnEditSale').attr('href', '/sales/' + sale.id + '/edit');
                        }

                        // Construct Modal Content
                        let itemsRowsHtml = '';
                        items.forEach((item, idx) => {
                            let title = item.item_name || 'Item';
                            if (item.variant_name && item.variant_name.toLowerCase() !== title.toLowerCase()) {
                                title += ' — ' + item.variant_name;
                            }

                            // Serials & Subdescriptions
                            let extraDetails = '';
                            if (item.serials) {
                                let serialsArr = Array.isArray(item.serials) ? item.serials : (typeof item.serials === 'string' ? item.serials.split(',') : []);
                                if (serialsArr.length > 0) {
                                    extraDetails += `<div class="fw-bold text-dark mt-1" style="font-size:11px;">SN #: ${serialsArr.join(', ')}</div>`;
                                }
                            }

                            let modelVal = item.model || '';
                            let subDescs = [];
                            if (modelVal) {
                                try {
                                    subDescs = isNaN(modelVal) && modelVal.startsWith('[') ? JSON.parse(modelVal) : modelVal.split('\n');
                                } catch(e) {
                                    subDescs = modelVal.split('\n');
                                }
                            }
                            if (subDescs && subDescs.length > 0) {
                                extraDetails += '<div class="text-muted mt-1" style="font-size:10.5px;">';
                                subDescs.forEach(sd => {
                                    if (sd.trim()) extraDetails += `<div>• ${sd.trim()}</div>`;
                                });
                                extraDetails += '</div>';
                            }

                            let qty = item.qty_box || item.qty || item.total_pieces || 0;
                            let price = item.price || 0;
                            let gross = item.gross_amount > 0 ? item.gross_amount : (qty * price);
                            let discAmt = item.discount_amount || 0;
                            let exclGst = item.exclusive_gst_amount || 0;
                            let salesTax = item.sales_tax_amount || 0;
                            let furtherTax = item.further_tax_amount || 0;
                            let net = item.total || 0;

                            itemsRowsHtml += `
                                <tr>
                                    <td class="text-center fw-bold">${idx + 1}</td>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size:12px;">${title}</div>
                                        ${extraDetails}
                                    </td>
                                    <td class="text-center font-monospace fw-bold">${qty} ${item.variant_unit || 'Pcs'}</td>
                                    <td class="text-end font-monospace">Rs. ${Number(price).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="text-end font-monospace">Rs. ${Number(gross).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="text-end font-monospace text-danger">Rs. ${Number(discAmt).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="text-end font-monospace">Rs. ${Number(exclGst).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="text-end font-monospace text-primary">Rs. ${Number(salesTax).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="text-end font-monospace text-warning">Rs. ${Number(furtherTax).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    <td class="text-end font-monospace fw-bold text-success">Rs. ${Number(net).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                </tr>
                            `;
                        });

                                                const summaryHeading = isQuotation ? 'Quotation Summary' : 'Invoice Summary';
                        const itemsHeading = isQuotation ? 'Quotation Items & Pricing' : 'Sale Items & Tax Breakdown';
                        const docNumberLabel = isQuotation ? 'Quotation No:' : 'Invoice No:';

                        const hasMobile = sale.customer_mobile && sale.customer_mobile !== '—' && sale.customer_mobile !== '-';
                        const hasAddress = sale.customer_address && sale.customer_address !== '—' && sale.customer_address !== '-';

                        const bodyHtml = `
                            {{-- Header: Customer info & Document summary side-by-side --}}
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <div class="card border rounded-3 p-3 bg-white h-100 shadow-sm">
                                        <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                            <span class="text-uppercase text-muted font-weight-bold" style="font-size:10.5px; letter-spacing:0.5px;">
                                                <i class="fas fa-user-circle text-primary mr-1"></i> Customer Information
                                            </span>
                                            ${sale.customer_code ? `<span class="badge badge-light border text-dark px-2 py-0" style="font-size:10px;">${sale.customer_code}</span>` : ''}
                                        </div>
                                        <div class="font-weight-bold text-dark mb-1" style="font-size:14px;">${sale.customer_name}</div>
                                        ${hasMobile ? `
                                            <div class="text-muted small mb-1" style="font-size:11.5px;">
                                                <i class="fas fa-phone-alt mr-1 text-primary"></i> ${sale.customer_mobile}
                                            </div>
                                        ` : ''}
                                        ${hasAddress ? `
                                            <div class="text-muted small text-truncate" style="font-size:11.5px;" title="${sale.customer_address}">
                                                <i class="fas fa-map-marker-alt mr-1 text-danger"></i> ${sale.customer_address}
                                            </div>
                                        ` : ''}
                                        ${!hasMobile && !hasAddress ? `
                                            <div class="text-muted small font-italic" style="font-size:11px;">
                                                Walk-in / No contact details specified
                                            </div>
                                        ` : ''}
                                        ${sale.sales_officer_name ? `
                                            <div class="text-primary small mt-2 pt-1 border-top" style="font-size:11px;">
                                                <i class="fas fa-user-tie mr-1"></i> Sales Officer: <strong>${sale.sales_officer_name}</strong>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border rounded-3 p-3 bg-white h-100 shadow-sm">
                                        <div class="text-uppercase text-muted font-weight-bold border-bottom pb-2 mb-2" style="font-size:10.5px; letter-spacing:0.5px;">
                                            <i class="fas fa-file-invoice text-info mr-1"></i> ${summaryHeading}
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:12px;">
                                            <span class="text-muted">${docNumberLabel}</span>
                                            <strong class="font-monospace text-primary" style="font-size:13px;">${sale.invoice_no}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:12px;">
                                            <span class="text-muted">Date:</span>
                                            <span class="text-dark font-weight-medium">${sale.created_at_formatted}</span>
                                        </div>
                                        ${sale.po_no ? `
                                            <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:12px;">
                                                <span class="text-muted">PO # (Mail):</span>
                                                <strong class="text-success">${sale.po_no} ${sale.po_date ? `(${sale.po_date})` : ''}</strong>
                                            </div>
                                        ` : ''}
                                        ${sale.reference ? `
                                            <div class="d-flex justify-content-between align-items-center" style="font-size:12px;">
                                                <span class="text-muted">Reference:</span>
                                                <strong class="text-dark">${sale.reference}</strong>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>

                            {{-- Items Table --}}
                            <div class="card border rounded-3 mb-3 bg-white shadow-sm overflow-hidden">
                                <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                                    <h6 class="font-weight-bold text-dark mb-0" style="font-size:12px;">
                                        <i class="fas fa-list mr-1 text-primary"></i> ${itemsHeading}
                                    </h6>
                                    <span class="badge badge-light border text-dark px-2 py-1" style="font-size:10px;">${items.length} Item(s)</span>
                                </div>
                                <div class="table-responsive mb-0" style="min-height: auto !important; max-height: 240px; overflow-y: auto;">
                                    <table class="table table-sm table-hover align-middle mb-0" style="font-size:11px;">
                                        <thead class="bg-light text-uppercase font-weight-bold sticky-top" style="font-size:10px; z-index:1; background-color: #f8fafc;">
                                            <tr>
                                                <th class="text-center py-2" style="width:35px;">#</th>
                                                <th class="py-2">Description</th>
                                                <th class="text-center py-2" style="width:75px;">Qty</th>
                                                <th class="text-right text-end py-2" style="width:85px;">Rate</th>
                                                <th class="text-right text-end py-2" style="width:90px;">Gross</th>
                                                <th class="text-right text-end py-2" style="width:75px;">Disc</th>
                                                <th class="text-right text-end py-2" style="width:85px;">Excl GST</th>
                                                <th class="text-right text-end py-2" style="width:85px;">Sales Tax</th>
                                                <th class="text-right text-end py-2" style="width:80px;">Further Tax</th>
                                                <th class="text-right text-end py-2" style="width:105px;">Net Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${itemsRowsHtml || '<tr><td colspan="10" class="text-center text-muted py-4">No items found</td></tr>'}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Bottom Row: Document Info / Notes (Left) & Financial Summary (Right) --}}
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <div class="card border rounded-3 p-3 bg-white h-100 shadow-sm d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="text-uppercase text-muted font-weight-bold border-bottom pb-1 mb-2" style="font-size:10.5px; letter-spacing:0.5px;">
                                                <i class="fas ${isQuotation ? 'fa-file-alt text-primary' : 'fa-clipboard-check text-success'} mr-1"></i> ${isQuotation ? 'Quotation Information' : 'Terms & Remarks'}
                                            </div>
                                            ${isQuotation ? `
                                                <div class="alert alert-light border p-2 mb-2 small text-muted" style="font-size: 11px; line-height: 1.5; background: #f8fafc;">
                                                    <i class="fas fa-info-circle text-primary mr-1"></i> <strong>Non-Posting Document:</strong><br>
                                                    This quotation does not impact stock inventory or customer financial ledger. Click <strong>"Convert to Sale"</strong> below when ready to finalize into a confirmed invoice.
                                                </div>
                                            ` : `
                                                <div class="text-muted small" style="font-size: 11.5px;">
                                                    ${sale.return_note ? `<p class="mb-1"><strong>Note:</strong> ${sale.return_note}</p>` : ''}
                                                    ${sale.reference ? `<p class="mb-1"><strong>Reference:</strong> ${sale.reference}</p>` : ''}
                                                    <p class="mb-0 text-muted">Official system sales transaction document.</p>
                                                </div>
                                            `}
                                        </div>
                                        <div class="text-muted small font-italic mt-2 pt-2 border-top" style="font-size: 10.5px;">
                                            <i class="fas fa-clock mr-1"></i> Record Date: ${sale.created_at_formatted}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border rounded-3 p-3 bg-white h-100 shadow-sm">
                                        <div class="text-uppercase text-muted font-weight-bold border-bottom pb-1 mb-2" style="font-size:10.5px; letter-spacing:0.5px;">
                                            <i class="fas fa-calculator mr-1 text-success"></i> Financial Summary
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:11.5px;">
                                            <span class="text-muted">Gross Total:</span>
                                            <span class="font-weight-bold font-monospace">Rs. ${Number(stats.sum_gross || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                        </div>
                                        ${stats.sum_disc > 0 ? `
                                            <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:11.5px;">
                                                <span class="text-muted">Item Discount:</span>
                                                <span class="text-danger font-monospace">Rs. ${Number(stats.sum_disc || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                            </div>
                                        ` : ''}
                                        ${sale.total_extradiscount > 0 ? `
                                            <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:11.5px;">
                                                <span class="text-muted">Extra Discount:</span>
                                                <span class="text-danger font-monospace">Rs. ${Number(sale.total_extradiscount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                            </div>
                                        ` : ''}
                                        <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:11.5px;">
                                            <span class="text-muted">Excl. GST Amount:</span>
                                            <span class="font-monospace">Rs. ${Number(stats.sum_excl_gst || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                        </div>
                                        ${stats.sum_sales_tax > 0 ? `
                                            <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:11.5px;">
                                                <span class="text-muted">Sales Tax @18%:</span>
                                                <span class="text-primary font-monospace">Rs. ${Number(stats.sum_sales_tax || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                            </div>
                                        ` : ''}
                                        ${stats.sum_further_tax > 0 ? `
                                            <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:11.5px;">
                                                <span class="text-muted">Further Tax @3%:</span>
                                                <span class="text-warning font-monospace">Rs. ${Number(stats.sum_further_tax || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                            </div>
                                        ` : ''}
                                        <div class="d-flex justify-content-between align-items-center py-2 mt-2 rounded px-2" style="background-color: #ecfdf5; border: 1px solid #a7f3d0;">
                                            <strong class="text-success" style="font-size:12.5px;">${isQuotation ? 'Quotation Net Total:' : 'Grand Net Total:'}</strong>
                                            <strong class="text-success font-monospace" style="font-size:15px;">Rs. ${Number(sale.total_net || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        $modalBody.html(bodyHtml);
                    },
                    error: function() {
                        $modalBody.html('<div class="alert alert-danger text-center my-4">Failed to load sale details. Please try again.</div>');
                    }
                });
            });
        });
    </script>

    <!-- Executive Sale Details Modal -->
    <div class="modal fade" id="viewSaleModal" tabindex="-1" aria-labelledby="viewSaleModalLabel" aria-hidden="true" style="z-index: 1080;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header text-white py-2 px-3 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0f172a, #1e293b); border-bottom: 1px solid #334155;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-primary rounded-3 text-white">
                            <i class="fas fa-file-alt" id="viewSaleModalIcon"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-white" id="viewSaleModalTitle">Sale Invoice Details</h5>
                            <small class="text-white-50" id="viewSaleModalSubtitle">Invoice No: -</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span id="viewSaleStatusBadge"></span>
                        <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.6rem; line-height: 1; opacity: 0.85; cursor: pointer;"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
                <div class="modal-body p-3 bg-light" id="viewSaleModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 small">Loading sale details...</p>
                    </div>
                </div>
                <div class="modal-footer bg-white py-2 px-3 border-top d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                        <form id="modalConfirmSaleForm" method="POST" action="" class="d-inline mb-0" style="display: none;">
                            @csrf
                            <button type="submit" class="btn btn-success fw-bold px-3 btn-sm" id="modalBtnConvertToSale">
                                <i class="fas fa-check-circle mr-1"></i> Convert to Sale
                            </button>
                        </form>
                    <a href="#" id="modalBtnPrintInvoice" target="_blank" class="btn btn-primary fw-bold px-3 btn-sm">
                        <i class="fas fa-print me-1"></i> Print (Simple)
                    </a>
                    <a href="#" id="modalBtnPrintLetterhead" target="_blank" class="btn btn-outline-primary fw-bold px-3 btn-sm" title="Print on Pre-Printed Letterhead Stationary">
                        <i class="fas fa-file-alt me-1"></i> Print (Letterhead)
                    </a>
                    <a href="#" id="modalBtnPrintDC" target="_blank" class="btn btn-warning fw-bold px-3 btn-sm text-dark">
                        <i class="fas fa-shipping-fast me-1"></i> Delivery Challan (DC)
                    </a>
                    <a href="#" id="modalBtnPrintReceipt" target="_blank" class="btn btn-success fw-bold px-3 btn-sm">
                        <i class="fas fa-receipt me-1"></i> Receipt
                    </a>
                    <a href="#" id="modalBtnEditSale" class="btn btn-outline-secondary fw-bold px-3 btn-sm">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    </div>
                    <button type="button" class="btn btn-secondary px-4 fw-bold btn-sm ml-auto" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
