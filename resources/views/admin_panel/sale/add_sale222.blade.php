@extends('admin_panel.layout.app')

@section('content')
    <!-- Loader Overlay -->
    <div id="pageLoader"
        class="{{ isset($sale) ? '' : 'd-none' }} position-fixed top-0 start-0 w-100 h-100 d-flex flex-column gap-3 justify-content-center align-items-center"
        style="background: rgba(255,255,255,0.9); z-index: 1055;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="fw-bold text-primary fs-5">Loading Sale Data...</div>
    </div>
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ==================== NEW SALE — CLEAN MODERN ERP/POS UI ==================== */
        :root {
            --pos-blue: #2563EB;
            --pos-blue-hover: #1D4ED8;
            --pos-blue-soft: #EFF6FF;
            --pos-green: #16A34A;
            --pos-green-soft: #F0FDF4;
            --pos-red: #DC2626;
            --pos-red-soft: #FEF2F2;
            --pos-orange: #F59E0B;
            --pos-orange-soft: #FFFBEB;
            --pos-text: #0F172A;
            --pos-muted: #64748B;
            --pos-border: #CBD5E1;
            --pos-border-strong: #94A3B8;
            --pos-bg: #F1F5F9;
            --pos-card: #FFFFFF;
            --pos-radius: 8px;
            --pos-radius-lg: 12px;
            --pos-shadow-sm: 0 1px 3px rgba(0,0,0,.05);
            --pos-shadow-md: 0 4px 14px rgba(15,23,42,.08);
            --pos-input-h: 38px;
        }

        body {
            background-color: var(--pos-bg) !important;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            color: var(--pos-text) !important;
            -webkit-font-smoothing: antialiased;
        }

        .sale-page {
            max-width: 1560px;
            margin: 0 auto;
        }

        /* ---------- CARDS & CONTAINERS ---------- */
        .sale-card {
            background: var(--pos-card);
            border: 1px solid var(--pos-border);
            border-radius: var(--pos-radius-lg);
            box-shadow: var(--pos-shadow-sm);
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .card-title {
            font-size: 14px;
            font-weight: 800;
            color: var(--pos-text);
            text-transform: uppercase;
            letter-spacing: .4px;
            line-height: 1.3;
        }

        /* ---------- LABELS ---------- */
        .field-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--pos-muted);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: .3px;
            line-height: 1.2;
        }

        /* ---------- INPUTS & FORM CONTROLS ---------- */
        .sale-page .form-control,
        .sale-page .form-select {
            height: var(--pos-input-h);
            border: 1px solid var(--pos-border);
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--pos-text);
            background-color: #ffffff;
            box-shadow: none;
            transition: border-color .12s ease, box-shadow .12s ease;
        }
        .sale-page .form-control::placeholder {
            color: #94A3B8;
            font-weight: 400;
        }
        .sale-page .form-control:focus,
        .sale-page .form-select:focus,
        .sale-page .form-control:focus-visible {
            border: 2px solid var(--pos-blue) !important;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, .15) !important;
            outline: none !important;
            background-color: #ffffff !important;
        }
        .sale-page .input-readonly,
        .sale-page input[readonly] {
            background-color: #F8FAFC !important;
            color: #475569 !important;
            border-color: #E2E8F0 !important;
            cursor: default;
            font-weight: 600;
        }

        /* Select2 (customer) */
        #customerInputWrapper .select2-container--default .select2-selection--single {
            height: var(--pos-input-h) !important;
            border: 1px solid var(--pos-border) !important;
            border-radius: 6px !important;
            background-color: #ffffff !important;
            padding: 0 !important;
        }
        #customerInputWrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 10px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: var(--pos-text) !important;
        }
        #customerInputWrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 6px !important;
        }
        #customerInputWrapper .select2-container--default.select2-container--focus .select2-selection--single,
        #customerInputWrapper .select2-container--default.select2-container--open .select2-selection--single {
            border: 2px solid var(--pos-blue) !important;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, .15) !important;
        }

        /* ---------- BUTTONS ---------- */
        .sale-page .btn-primary {
            background: var(--pos-blue);
            border-color: var(--pos-blue);
            color: #ffffff;
            border-radius: 6px;
            font-weight: 700;
            font-size: 13.5px;
            box-shadow: 0 1px 2px rgba(37, 99, 235, .2);
        }
        .sale-page .btn-primary:hover,
        .sale-page .btn-primary:focus {
            background: var(--pos-blue-hover);
            border-color: var(--pos-blue-hover);
            color: #ffffff;
        }
        .sale-page .btn-outline-primary {
            color: var(--pos-blue);
            border-color: #93C5FD;
            background: #ffffff;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13.5px;
        }
        .sale-page .btn-outline-primary:hover {
            background: var(--pos-blue-soft);
            color: var(--pos-blue-hover);
            border-color: var(--pos-blue);
        }
        .sale-page .btn-outline-secondary {
            color: var(--pos-muted);
            border-color: var(--pos-border);
            background: #ffffff;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13.5px;
        }
        .sale-page .btn-outline-secondary:hover {
            background: #F1F5F9;
            color: var(--pos-text);
            border-color: var(--pos-border-strong);
        }

        .btn-save-print {
            padding: 8px 18px !important;
            box-shadow: 0 3px 10px -2px rgba(37, 99, 235, .4);
        }

        .btn-icon-back {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--pos-border);
            background: #ffffff;
            color: var(--pos-muted);
            font-size: 14px;
            flex-shrink: 0;
            transition: all .15s ease;
        }
        .btn-icon-back:hover {
            background: #F1F5F9;
            color: var(--pos-text);
            border-color: var(--pos-border-strong);
        }

        /* ---------- PAGE HEADER ---------- */
        .sale-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 16px;
        }
        .sale-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sale-title-ic {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--pos-blue-soft);
            color: var(--pos-blue);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
            border: 1px solid #BFDBFE;
        }
        .sale-title-main h5 {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -.3px;
            color: var(--pos-text);
            margin-bottom: 2px;
        }
        .sale-subtitle {
            font-size: 12.5px;
            color: var(--pos-muted);
        }

        /* ---------- SALE TYPE SEGMENTED TOGGLE ---------- */
        .seg-toggle {
            display: flex;
            height: var(--pos-input-h);
            background: #F1F5F9;
            border: 1px solid var(--pos-border);
            border-radius: 6px;
            padding: 2px;
            width: 100%;
        }
        .seg-toggle .btn {
            flex: 1;
            border-radius: 4px;
            border: none;
            font-size: 12.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0 8px;
        }
        .seg-toggle .btn.btn-outline-primary {
            background: transparent;
            color: var(--pos-muted);
        }
        .seg-toggle .btn-outline-primary:hover {
            background: rgba(37, 99, 235, .08);
            color: var(--pos-blue);
        }

        /* ---------- INVOICE GROUP ---------- */
        .invoice-group {
            flex-wrap: nowrap;
        }
        .invoice-group .btn-prefix {
            height: var(--pos-input-h);
            border: 1px solid var(--pos-border);
            border-right: none;
            background: #F8FAFC;
            color: var(--pos-text);
            font-weight: 700;
            font-size: 13px;
            border-radius: 6px 0 0 6px;
            padding: 0 10px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .invoice-group .btn-prefix:hover {
            background: #F1F5F9;
        }
        .invoice-group #inputInvoiceNo {
            border-radius: 0;
            border-left: none;
            border-right: none;
            font-family: Consolas, 'JetBrains Mono', monospace;
            font-size: 13.5px;
            font-weight: 700 !important;
        }
        .invoice-group .btn-refresh {
            height: var(--pos-input-h);
            border: 1px solid var(--pos-border);
            border-left: none;
            background: #ffffff;
            color: var(--pos-muted);
            border-radius: 0 6px 6px 0;
            padding: 0 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .15s ease;
        }
        .invoice-group .btn-refresh:hover {
            background: #F1F5F9;
            color: var(--pos-blue);
        }

        /* ---------- CUSTOMER BALANCE CARD ---------- */
        .cust-bal-card {
            background: #FFFFFF;
            border: 1px solid var(--pos-border);
            border-radius: 8px;
            padding: 8px 10px;
            box-sizing: border-box;
            height: 100%;
            min-height: 104px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .cb-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 5px;
        }
        .cb-id {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }
        .cb-avatar {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: var(--pos-blue);
            color: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
        }
        .cb-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--pos-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cb-code {
            font-size: 11px;
            color: var(--pos-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cb-extras {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            margin-bottom: 5px;
        }
        .cb-ext {
            background: #F8FAFC;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 4px 7px;
        }
        .cb-ext-label {
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: .3px;
            font-weight: 700;
            color: #64748B;
            margin-bottom: 1px;
        }
        .cb-ext-val {
            font-size: 12px;
            font-weight: 700;
            color: var(--pos-text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .cb-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
        }
        .cb-cell {
            background: #F8FAFC !important;
            border: 1px solid #CBD5E1 !important;
            border-radius: 6px !important;
            padding: 6px 4px !important;
            text-align: center;
        }
        .cb-label {
            font-size: 9.5px !important;
            text-transform: uppercase !important;
            letter-spacing: .4px !important;
            font-weight: 800 !important;
            color: #64748B !important;
            margin-bottom: 2px !important;
            white-space: nowrap !important;
        }
        .cb-value {
            font-size: 13px !important;
            font-weight: 800 !important;
            color: #0F172A !important;
            white-space: nowrap !important;
        }
        .cust-bal-card .text-danger {
            color: var(--pos-red) !important;
        }
        .cust-bal-card .text-success {
            color: var(--pos-green) !important;
        }
        #cc_paid_now {
            color: var(--pos-green) !important;
        }

        /* ---------- ITEMS HEADER ---------- */
        .items-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--pos-text);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .items-count {
            background: var(--pos-blue-soft);
            color: var(--pos-blue);
            font-weight: 700;
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 12px;
        }

        /* ---------- PRODUCT CARDS ---------- */
        .pos-product-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            margin-bottom: 4px;
            transition: background .15s ease;
        }
        .pos-product-card:last-child {
            margin-bottom: 0;
        }
        .pos-product-card:hover {
            background: #F8FAFC;
        }
        .pos-product-img {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #F1F5F9;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .pos-product-info {
            flex: 1;
            min-width: 0;
        }
        .pos-product-name {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--pos-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .pos-product-sub {
            font-size: 12px;
            color: var(--pos-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .pos-product-price {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--pos-text);
            white-space: nowrap;
        }
        .pos-product-add-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--pos-blue);
            color: #ffffff;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
            cursor: pointer;
            transition: background .15s ease;
        }
        .pos-product-add-btn:hover {
            background: var(--pos-blue-hover);
        }
        .badge-stock-green {
            background-color: var(--pos-green-soft) !important;
            color: #15803D !important;
            font-weight: 700 !important;
            border: 1px solid #BBF7D0 !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
            font-size: 11.5px !important;
        }

        /* ---------- PRODUCT TABLE (EXCEL GRID STYLE) ---------- */
        .table-responsive {
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            background: #ffffff;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .pos-table-wrap {
            overflow-x: auto;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .sales-table {
            min-width: 850px;
            border-collapse: collapse !important;
            width: 100%;
            margin-bottom: 0;
            table-layout: fixed;
            background: #ffffff;
        }
        .sales-table thead th {
            background: #F1F5F9 !important;
            color: #334155 !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: .5px;
            padding: 9px 6px !important;
            border: 1px solid #CBD5E1 !important;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }
        .sales-table thead th.col-product {
            text-align: left;
            padding-left: 10px !important;
        }
        .sales-table tbody td {
            padding: 3px 5px !important;
            height: 42px !important;
            border: 1px solid #CBD5E1 !important;
            vertical-align: middle;
            background: #ffffff;
        }
        .sales-table tbody tr:nth-child(even) td {
            background: #FAFCFE;
        }
        .sales-table tbody tr:hover td {
            background: #F1F5F9;
        }
        .row-index-cell {
            font-size: 12px;
            font-weight: 700;
            color: #64748B;
            text-align: center;
            background: #F8FAFC !important;
        }

        /* Table inputs — clean Excel grid cells that highlight with sharp blue outline on focus */
        .sales-table tbody .form-control,
        .sales-table tbody .form-select {
            height: 34px !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 4px !important;
            padding: 3px 7px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            background: #ffffff !important;
            box-shadow: none !important;
            color: #0F172A !important;
            width: 100% !important;
            transition: border-color .12s ease, box-shadow .12s ease;
        }
        .sales-table tbody .form-control:hover,
        .sales-table tbody .form-select:hover {
            border-color: #94A3B8 !important;
        }
        .sales-table tbody .form-control:focus,
        .sales-table tbody .form-select:focus,
        .sales-table tbody .form-control:focus-visible {
            border: 2px solid #2563EB !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, .15) !important;
            outline: none !important;
        }
        .sales-table tbody input[readonly],
        .sales-table tbody .input-readonly {
            background: #F8FAFC !important;
            color: #475569 !important;
            cursor: default !important;
            font-weight: 600 !important;
            border-color: #E2E8F0 !important;
        }
        .sales-table tbody input[readonly]:focus {
            border-color: #CBD5E1 !important;
            box-shadow: none !important;
        }

        /* Stock badge style inside stock cell */
        .stock-badge {
            display: inline-block;
            background: #F1F5F9;
            color: #475569;
            border: 1px solid var(--pos-border);
            font-size: 12px;
            font-weight: 700;
            border-radius: 6px;
            padding: 4px 8px;
            line-height: 1.2;
        }
        .stock-badge.strong {
            background: var(--pos-green-soft);
            color: #15803D;
            border-color: #BBF7D0;
        }

        /* Product select2 inside table - Excel cell style */
        .sales-table tbody .select2-container {
            width: 100% !important;
        }
        .sales-table tbody .select2-container .select2-selection--single {
            height: 34px !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 4px !important;
            background: #ffffff !important;
            padding: 0 !important;
        }
        .sales-table tbody .select2-container:hover .select2-selection--single {
            border-color: #94A3B8 !important;
        }
        .sales-table tbody .select2-container--focus .select2-selection--single,
        .sales-table tbody .select2-container--open .select2-selection--single {
            border: 2px solid #2563EB !important;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, .15) !important;
            background: #ffffff !important;
        }
        .sales-table tbody .select2-container .select2-selection__rendered {
            line-height: 32px !important;
            padding-left: 7px !important;
            padding-right: 18px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #0F172A !important;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .sales-table tbody .select2-container .select2-selection__arrow {
            height: 32px !important;
            right: 4px !important;
        }

        /* Qty cell */
        .qty-cell-flex {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .qty-cell-flex .carton-qty {
            flex: 1;
            min-width: 0;
        }
        .qty-unit-toggle {
            height: 38px !important;
            min-width: 42px !important;
            border-radius: 6px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px !important;
        }

        /* Price cell */
        .price-cell-flex {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .price-cell-flex .visible-price {
            flex: 1;
            min-width: 0;
        }
        .price-mode-row-toggle {
            height: 38px !important;
            min-width: 32px !important;
            border-radius: 6px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
        }

        /* Discount cell */
        .discount-wrapper {
            display: flex;
            align-items: stretch;
            gap: 4px;
        }
        .discount-wrapper .discount-value {
            flex: 1;
            min-width: 0;
            text-align: right;
        }
        .discount-wrapper .discount-toggle {
            width: 32px;
            flex-shrink: 0;
            height: 38px !important;
            border: 1px solid var(--pos-border) !important;
            background: #F8FAFC !important;
            color: var(--pos-muted) !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            border-radius: 6px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            transition: all .15s ease;
        }
        .discount-wrapper .discount-toggle:hover {
            background: #EEF2F7 !important;
            color: var(--pos-blue) !important;
        }

        /* Amount cell */
        .sales-amount {
            font-weight: 800 !important;
            color: var(--pos-text) !important;
            font-size: 14px !important;
        }

        /* Row delete button */
        .sales-table .del-row {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #FECACA;
            background: #ffffff;
            color: var(--pos-red);
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            cursor: pointer;
            transition: all .15s ease;
        }
        .sales-table .del-row:hover {
            background: var(--pos-red);
            border-color: var(--pos-red);
            color: #ffffff;
        }

        /* Responsive: compress controls on smaller screens so the table always fits its container */
        @media (max-width: 1199.98px) {
            .sales-table tbody .form-control,
            .sales-table tbody .form-select {
                height: 34px !important;
                font-size: 12px !important;
                padding: 3px 6px !important;
            }
            .sales-table tbody .select2-container .select2-selection--single {
                height: 34px !important;
            }
            .sales-table tbody .select2-container .select2-selection__rendered {
                line-height: 32px !important;
                font-size: 12px !important;
                padding-left: 6px !important;
                padding-right: 16px !important;
            }
            .sales-table tbody td {
                height: 46px;
                padding: 5px;
            }
            .sales-table thead th {
                padding: 9px 5px;
                font-size: 10px;
            }
            .qty-unit-toggle,
            .price-mode-row-toggle,
            .discount-wrapper .discount-toggle {
                height: 34px !important;
            }
            .qty-unit-toggle {
                min-width: 34px !important;
            }
            .price-mode-row-toggle {
                min-width: 28px !important;
            }
            .sales-table .del-row {
                width: 30px;
                height: 30px;
            }
            .discount-wrapper .discount-toggle {
                width: 28px;
            }
            .stock-badge {
                font-size: 11px;
                padding: 3px 6px;
            }
        }

        /* Phones: drop non-essential columns (#, Stock, Pcs, Pcs/Ctn) instead of scrolling */
        @media (max-width: 767.98px) {
            .sales-table col:first-child,
            .sales-table col.c-stock,
            .sales-table col.c-pcs,
            .sales-table col.c-pc {
                width: 0 !important;
            }
            .sales-table col:last-child {
                width: 10%;
            }
            .sales-table thead th:first-child,
            .sales-table thead th.c-stock-th,
            .sales-table thead th.c-pcs-th,
            .sales-table thead th.col-pcs-ctn-th,
            .sales-table tbody td.row-index,
            .sales-table tbody td.col-stock,
            .sales-table tbody td.col-pieces,
            .sales-table tbody td.col-pcs-ctn {
                display: none !important;
            }
            .sales-table thead th {
                white-space: normal;
                line-height: 1.2;
            }
        }

        /* Grid total footer */
        .sales-table tfoot td {
            background: #F8FAFC;
            border-top: 1px solid var(--pos-border);
            padding: 13px 16px;
        }
        .grid-total-label {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--pos-muted);
            text-align: right;
        }
        .grid-total-val {
            font-size: 17px;
            font-weight: 800;
            color: var(--pos-text);
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        /* ---------- PAYMENT METHODS ---------- */
        .pay-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            padding-bottom: 4px;
            margin-bottom: 6px;
            border-bottom: 1px solid #E2E8F0;
        }
        .btn-pay-head {
            font-size: 11px !important;
            padding: 2px 6px !important;
            border-radius: 3px !important;
        }
        .rv-row {
            display: flex;
            gap: 6px;
            align-items: center;
            margin-bottom: 4px;
        }
        .rv-row .rv-account {
            flex: 1;
            min-width: 0;
            height: 28px !important;
            font-size: 11.5px !important;
            border-color: #CBD5E1;
            font-weight: 600;
        }
        .rv-row .rv-amount {
            width: 110px;
            flex-shrink: 0;
            height: 28px !important;
            font-size: 11.5px !important;
            text-align: right;
            font-weight: 700;
            border-color: #CBD5E1;
        }
        .btnRemRV {
            width: 28px;
            height: 28px !important;
            border-radius: 4px;
            border: 1px solid var(--pos-border);
            background: #ffffff;
            color: var(--pos-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 11px;
            transition: all .15s ease;
        }
        .btnRemRV:hover {
            background: var(--pos-red-soft);
            color: var(--pos-red);
            border-color: #FECACA;
        }
        .change-row {
            border-top: 1px dashed var(--pos-border);
            margin-top: 6px;
            padding-top: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .change-row .change-label {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--pos-muted);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .change-row .form-select {
            width: 130px;
            height: 28px !important;
            font-size: 11.5px !important;
        }

        /* ---------- ORDER SUMMARY ---------- */
        .s-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 6px;
            padding: 3px 0;
            font-size: 12px;
            border-bottom: 1px solid #F1F5F9;
        }
        .s-label {
            color: #64748B;
            font-weight: 600;
        }
        .s-val {
            font-weight: 700;
            color: #0F172A;
            font-variant-numeric: tabular-nums;
        }
        .s-row.net {
            padding: 4px 8px;
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            border-radius: 4px;
            margin: 3px 0;
        }
        .net-label {
            font-size: 12.5px;
            font-weight: 800;
            color: #1E3A8A;
            text-transform: uppercase;
        }
        .net-val {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -.3px;
            color: #2563EB;
            font-variant-numeric: tabular-nums;
        }
        .paid-val {
            font-weight: 800;
            font-size: 12.5px;
            color: #16A34A;
            font-variant-numeric: tabular-nums;
        }
        .change-val {
            font-weight: 700;
            font-size: 12.5px;
            font-variant-numeric: tabular-nums;
        }
        .change-val.text-success {
            color: var(--pos-green) !important;
        }
        .change-val.text-danger {
            color: var(--pos-red) !important;
        }
        .discount-input {
            width: 110px;
            flex-shrink: 0;
        }
        .discount-input input {
            height: 26px !important;
            font-size: 11.5px !important;
            padding: 1px 4px !important;
            font-weight: 700;
        }
        .discount-input .input-group-text {
            height: 26px !important;
            font-size: 10.5px !important;
            padding: 0 4px !important;
        }

        /* ---------- STICKY BOTTOM ACTION BAR ---------- */
        .sale-bottom-bar {
            position: sticky;
            bottom: 0;
            z-index: 40;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 6px 10px;
            background: #ffffff;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            box-shadow: 0 -3px 10px -4px rgba(15, 23, 42, .1);
            padding: 6px 12px;
            margin-top: 8px;
        }
        .bb-left {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 11.5px;
            color: #64748B;
            flex-wrap: wrap;
        }
        .bb-left b {
            color: #0F172A;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
        }
        .bb-left .text-success {
            color: #16A34A !important;
        }
        .btn-ghost {
            border: 1px solid #CBD5E1;
            background: #ffffff;
            color: #475569;
            border-radius: 4px;
            font-weight: 700;
            font-size: 11px;
            padding: 3px 7px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            transition: all .15s ease;
        }
        .btn-ghost:hover {
            background: #F1F5F9;
            color: #0F172A;
            border-color: #94A3B8;
        }
        .bb-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        /* ---------- OFF-CANVAS (Quick Products) ---------- */
        .offcanvas-header {
            border-bottom: 1px solid var(--pos-border);
        }

        /* ---------- VALIDATION STATES ---------- */
        .invalid-input,
        .invalid-select {
            border-color: var(--pos-red) !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, .12) !important;
        }
        .invalid-cell {
            background: #FFF7F7 !important;
            box-shadow: inset 0 0 0 1px rgba(220, 38, 38, .25) !important;
        }
        .invalid-input + .select2-container .select2-selection--single,
        .invalid-select + .select2-container .select2-selection--single {
            border-color: var(--pos-red) !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, .12) !important;
        }

        /* ---------- ALERT BOX ---------- */
        #alertBox {
            border-radius: 10px;
            font-size: 13.5px;
            padding: 12px 16px;
            margin-bottom: 18px;
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 1199.98px) {
            .cust-bal-card {
                height: auto;
                min-height: 0;
                max-height: none;
                overflow: visible;
            }
            .cb-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 575.98px) {
            .sale-subtitle {
                display: none;
            }
            .bb-left {
                gap: 12px;
                font-size: 12.5px;
            }
            .bb-actions .btn-outline-secondary,
            .bb-actions .btn-outline-primary {
                display: none;
            }
            .bb-actions .btn-primary {
                width: 100%;
            }
        }
    </style>

    <div class="container-fluid px-3 px-lg-4 pt-3 pb-4 sale-page">

        <div id="alertBox" class="alert d-none" role="alert"></div>

        <form id="saleForm" autocomplete="off">
            @csrf
            <input type="hidden" id="booking_id" name="booking_id" value="">
            <input type="hidden" id="action" name="action" value="sale">
            <input type="hidden" name="cash" value="0">
            <input type="hidden" id="totalBalance" value="0">
            <input type="hidden" name="total_extra_cost" id="discountAmount" value="0">

            {{-- ============================ PAGE HEADER ============================ --}}
            {{-- ============================ PAGE HEADER ============================ --}}
            <div class="sale-header">
                <div class="sale-header-left">
                    <a href="{{ route('sale.index') }}" class="btn-icon-back" title="Back to Sales List">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="sale-title-ic">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="sale-title-main">
                        <h5 class="header-text mb-0">New Sale</h5>
                        <div class="sale-subtitle">Create a new invoice &amp; manage checkout</div>
                    </div>
                </div>

                {{-- Header Right: Invoice No & Date --}}
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    {{-- Invoice No --}}
                    <div class="d-flex align-items-center gap-2">
                        <label class="field-label mb-0 fw-bold text-secondary" for="inputInvoiceNo">Inv #:</label>
                        <div class="input-group invoice-group" style="width: 200px;">
                            <button class="btn btn-prefix dropdown-toggle d-flex align-items-center gap-1"
                                    type="button"
                                    id="btnInvoicePrefix"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                <span id="activePrefixLabel">{{ $activePrefix ?? 'INV' }}</span>
                            </button>
                            <ul class="dropdown-menu shadow-lg p-1 border-0" id="dropdownInvoiceSeriesList" aria-labelledby="btnInvoicePrefix" style="min-width: 160px; font-size: 13px; z-index: 1050;">
                                @if(isset($allSeries) && count($allSeries) > 0)
                                    @foreach($allSeries as $s)
                                        <li>
                                            <a class="dropdown-item fw-bold {{ ($activePrefix ?? 'INV') == $s->prefix ? 'text-success active bg-light' : '' }}"
                                               href="#"
                                               data-prefix="{{ $s->prefix }}"
                                               data-next="{{ $s->next_number }}"
                                               data-padding="{{ $s->padding }}">
                                                @if(($activePrefix ?? 'INV') == $s->prefix) <i class="fas fa-check text-success me-1"></i> @endif
                                                {{ $s->prefix }} <span class="text-muted small font-monospace">({{ $s->padding }}d)</span>
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    <li><a class="dropdown-item fw-bold text-success active bg-light" href="#" data-prefix="INV"><i class="fas fa-check text-success me-1"></i> INV (4d)</a></li>
                                @endif
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item fw-bold text-success d-flex align-items-center gap-1" href="#" id="btnOpenAddSeriesModal">
                                        <i class="fas fa-plus-circle me-1"></i> Add Series
                                    </a>
                                </li>
                            </ul>

                            <input type="text" class="form-control text-center fw-bold input-readonly" name="Invoice_no" id="inputInvoiceNo" value="{{ $nextInvoiceNumber }}" readonly style="height: 38px;">

                            <button class="btn btn-refresh"
                                    type="button"
                                    id="btnRefreshInvoiceNo"
                                    title="Regenerate Invoice Number" style="height: 38px;">
                                <i class="fas fa-sync-alt" id="iconRefreshInvoice"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Date --}}
                    <div class="d-flex align-items-center gap-2">
                        <label class="field-label mb-0 fw-bold text-secondary" for="displayDateInput">Date:</label>
                        <input type="text" name="sale_date" class="form-control datepicker-custom text-center fw-bold" id="displayDateInput" value="{{ date('d/m/Y') }}" style="width: 130px; height: 38px;">
                    </div>
                </div>
            </div>

            {{-- ============================ SALE INFORMATION CARD ============================ --}}
            <div class="sale-card mb-3 p-2 px-3">
                <div class="row g-2 align-items-center">
                    {{-- Left: Customer Dropdown, Grouped Action Buttons, Sale Type --}}
                    <div class="col-xl-7 col-lg-6 col-md-12">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            {{-- Customer Input --}}
                            <div class="flex-grow-1" style="min-width: 220px;">
                                <label class="field-label mb-1">Customer</label>
                                <div id="customerInputWrapper" style="min-width: 0;">
                                    <input type="text" class="form-control d-none" name="walkin_name" id="walkinNameInput" value="Walk-in Customer" placeholder="Enter Walk-in Name...">
                                    <select class="form-select" id="customerSelect" name="customer" style="width:100%">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>

                            {{-- Action Buttons Group (+ and Eye/View icon) --}}
                            <div class="align-self-end mb-0">
                                <label class="field-label mb-1 opacity-0 d-block">&nbsp;</label>
                                <div class="btn-group" role="group">
                                    <button type="button" id="btnOpenAddCustomerModal"
                                            class="btn btn-outline-primary"
                                            style="height: var(--pos-input-h); padding: 0 10px; display: inline-flex; align-items: center; justify-content: center;"
                                            data-toggle="modal" data-target="#addCustomerModal"
                                            data-bs-toggle="modal" data-bs-target="#addCustomerModal"
                                            title="Quick Add Customer (Alt+C or F2)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button type="button" id="btnToggleCustomerInfo"
                                            class="btn btn-outline-secondary"
                                            style="height: var(--pos-input-h); padding: 0 10px; display: inline-flex; align-items: center; justify-content: center;"
                                            title="View Customer Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Sale Type --}}
                            <div class="align-self-end mb-0">
                                <label class="field-label mb-1">Sale Type</label>
                                <div class="seg-toggle" role="group" aria-label="Sale Type">
                                    <button type="button" class="btn btn-primary active text-white" id="btnTypeCustomer">
                                        <i class="fas fa-users me-1"></i> Customer
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="btnTypeWalkin">
                                        <i class="fas fa-walking me-1"></i> Walk-in
                                    </button>
                                </div>
                                <select class="d-none" id="partyTypeSelect" name="partyType">
                                    @foreach(\App\Models\CustomerType::orderBy('name')->get() as $type)
                                        <option value="{{ $type->name }}" {{ $type->name === 'Main Customer' ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Balances on the same line --}}
                    <div class="col-xl-5 col-lg-6 col-md-12 text-lg-end">
                        <div class="d-inline-flex align-items-center flex-wrap gap-2 fw-bold" style="font-size: 12.5px;">
                            <span class="text-danger">Prev. Due: <span id="cc_prev_bal_val">Rs 0</span> <span id="cc_prev_bal_suffix">Dr</span></span>
                            <span class="text-muted opacity-50">|</span>
                            <span class="text-primary">Current Due: <span id="cc_current_bill">Rs 0</span></span>
                            <span class="text-muted opacity-50">|</span>
                            <span class="text-success">Paid: <span id="cc_paid_now">Rs 0</span></span>
                            <span class="text-muted opacity-50">|</span>
                            <span style="color: #9333EA;">Closing: <span id="cc_closing_bal_val">Rs 0</span> <span id="cc_closing_bal_suffix">Dr</span></span>
                        </div>
                    </div>
                </div>

                <span id="cc_customer_name" class="d-none"></span>
                <span id="ci_code" class="d-none"></span>
                <span id="ci_name" class="d-none">—</span>
                <span id="ci_mobile" class="d-none">—</span>
                <span id="ci_address" class="d-none">—</span>
            </div>

            {{-- Hidden fields for backend --}}
            <input type="hidden" name="is_walkin" id="is_walkin" value="0">
            <input type="hidden" id="address" name="address">
            <input type="hidden" id="tel" name="tel">
            <input type="hidden" id="previousBalance" value="0">
            <input type="hidden" id="rangeBalance" value="0">

            {{-- ============================ ITEMS SECTION ============================ --}}
            <div class="sale-card mb-3 p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div class="items-title">
                        Items
                        <span class="items-count" id="itemsRowCount">0</span>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-primary px-3" id="btnNewProductHeader">
                            <i class="fas fa-box-open me-1"></i> New Product
                        </button>
                    </div>
                </div>

                <div class="pos-table-wrap">
                    <table class="table sales-table mb-0">
                        <colgroup>
                            <col style="width:3%;">
                            <col style="width:27%;">
                            <col class="c-stock" style="width:6%;">
                            <col style="width:8%;">
                            <col style="width:6%;">
                            <col class="c-pcs" style="width:6%;">
                            <col class="c-pc" style="width:7%;">
                            <col style="width:9%;">
                            <col style="width:8%;">
                            <col style="width:12%;">
                            <col style="width:5%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="col-product">Product</th>
                                <th class="c-stock-th">Stock</th>
                                <th>Qty</th>
                                <th>Size</th>
                                <th class="c-pcs-th">Pcs</th>
                                <th class="col-pcs-ctn-th">Pcs/Ctn</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="salesTableBody">
                            <tr>
                                <td class="row-index-cell row-index">1</td>

                                <!-- PRODUCT -->
                                <td class="col-product">
                                    <select class="form-select product" style="width:100%">
                                        <option value=""></option>
                                    </select>
                                    <input type="hidden" class="product-id-hidden" name="product_id[]">
                                    <input type="hidden" class="variant-data-hidden" name="color[]">
                                     <input type="hidden" class="batch-id-hidden" name="batch_id[]">
                                     <input type="hidden" class="batch-no-hidden" name="batch_no[]">
                                     <input type="hidden" class="serials-hidden" name="serials[]">
                                     <div class="row-tracking-badges mt-1 d-flex flex-wrap gap-1"></div>
                                    <input type="hidden" class="item-code-display">
                                    <input type="hidden" class="size-h">
                                    <input type="hidden" class="size-w">
                                    <input type="hidden" class="size-mode-text">
                                </td>

                                <!-- STOCK -->
                                <td class="col-stock text-center">
                                    <input type="text" class="form-control stock text-center input-readonly" readonly tabindex="-1">
                                    <input type="hidden" class="warehouse" name="warehouse_id[]" value="{{ auth()->user()->warehouse_id ?? 1 }}">
                                    <input type="hidden" class="variant-stock-value">
                                </td>

                                <!-- QTY -->
                                <td class="col-qty-wrapper">
                                    <div class="qty-cell-flex">
                                        <input type="number" step="any" class="form-control carton-qty text-start fw-bold" name="carton_qty[]" placeholder="0" min="0" value="">
                                        <button type="button" class="btn btn-sm qty-unit-toggle px-1 py-0 d-none"
                                                data-unit-mode="main" title="Toggle Unit"
                                                style="background: #fff; color: #2563EB; border: 1px solid #BFDBFE;">
                                            Kg
                                        </button>
                                    </div>
                                    <input type="hidden" class="hidden-sub-unit-mode" name="sub_unit_mode[]" value="main">
                                </td>

                                <!-- SIZE -->
                                <td class="col-size">
                                    <input type="text" class="form-control size-display text-center" name="size_display[]" placeholder="-">
                                    <input type="hidden" class="pack-qty" name="pack_qty[]" value="1">
                                </td>

                                <!-- PCS -->
                                <td class="col-pieces">
                                    <input type="text" class="form-control total-pieces text-end input-readonly fw-semibold" name="total_pieces[]" readonly placeholder="0" tabindex="-1">
                                    <input type="hidden" class="sales-qty" name="qty[]" value="0">
                                </td>

                                <!-- PCS/CTN (always visible; shows value when unit is Carton, "–" otherwise) -->
                                <td class="col-pcs-ctn text-center">
                                    <input type="text" class="form-control pcs-per-ctn text-center input-readonly fw-semibold" readonly tabindex="-1" placeholder="–">
                                </td>

                                <!-- PRICE -->
                                <td class="col-price-p">
                                    <div class="price-cell-flex">
                                        <input type="text" class="form-control visible-price text-end fw-semibold" name="visible_price[]" placeholder="0">
                                        <button type="button" class="btn btn-sm price-mode-row-toggle px-1 py-0"
                                                data-mode="retail" title="Retail Mode"
                                                style="background: #fff; border: 1px solid #BFDBFE; color: #2563EB;">
                                            R
                                        </button>
                                    </div>
                                    <input type="hidden" class="price-per-piece" name="price_per_piece[]">
                                    <input type="hidden" class="retail-price">
                                    <input type="hidden" class="wholesale-price">
                                    <input type="hidden" class="weight-per-piece">
                                </td>

                                <!-- DISCOUNT -->
                                <td class="col-disc">
                                    <div class="discount-wrapper">
                                        <input type="number" class="form-control discount-value text-end" name="item_disc[]" placeholder="0">
                                        <input type="hidden" class="discount-type-hidden" name="discount_type[]" value="percent">
                                        <button type="button" class="btn btn-outline-secondary discount-toggle" data-type="percent" tabindex="-1">%</button>
                                    </div>
                                    <input type="hidden" class="discount-amount" value="0">
                                </td>

                                <!-- AMOUNT -->
                                <td class="col-amount">
                                    <input type="text" class="form-control sales-amount text-end input-readonly" name="total[]" value="0" readonly tabindex="-1">
                                    <input type="hidden" class="gross-amount" name="gross_amount[]">
                                </td>

                                <!-- ACTION -->
                                <td class="col-action text-center">
                                    <button type="button" class="del-row" tabindex="-1" title="Delete Row">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="9" class="grid-total-label">Grid Total:</td>
                                <td class="grid-total-val">Rs <span id="totalAmount">0.00</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- ============================ PAYMENT METHODS & ORDER SUMMARY ============================ --}}
            <div class="row g-2 align-items-start mb-3">
                {{-- LEFT: Payment Methods --}}
                <div class="col-lg-6">
                    <div class="sale-card p-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="card-title d-flex align-items-center gap-2" style="font-size: 15px;">
                                <i class="fas fa-wallet text-primary"></i> Payment Methods
                            </span>
                        </div>

                        <div id="rvWrapper" class="row g-2">
                            @foreach ($accounts as $acc)
                                @php
                                    $accTitleLower = strtolower($acc->title);
                                @endphp
                                <div class="col-md-6 col-12">
                                    <div class="pay-account-card d-flex align-items-center justify-content-between p-2 rounded-3 border transition-all" style="background: #ffffff; border: 1px solid #E2E8F0;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="acc-icon-badge flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px; background: #F1F5F9;">
                                                @if(str_contains($accTitleLower, 'cash'))
                                                    <span class="fs-5">💵</span>
                                                @elseif(str_contains($accTitleLower, 'bank'))
                                                    <i class="fas fa-university text-primary fs-6"></i>
                                                @elseif(str_contains($accTitleLower, 'card'))
                                                    <i class="fas fa-credit-card text-info fs-6"></i>
                                                @elseif(str_contains($accTitleLower, 'easy') || str_contains($accTitleLower, 'paisa'))
                                                    <span class="fw-bold text-success fs-5" style="font-family: sans-serif;">e</span>
                                                @elseif(str_contains($accTitleLower, 'jazz'))
                                                    <span class="badge bg-danger rounded-circle p-1" style="font-size: 9px;">Jazz</span>
                                                @else
                                                    <i class="fas fa-ellipsis-h text-secondary fs-6"></i>
                                                @endif
                                            </div>
                                            <div class="lh-1">
                                                <span class="fw-bold fs-6 text-dark d-block">{{ $acc->title }}</span>
                                            </div>
                                        </div>
                                        <div class="rv-row m-0 p-0" style="width: 110px;">
                                            <input type="hidden" class="rv-account" name="receipt_account_id[]" value="{{ $acc->id }}">
                                            <input type="number" step="0.01" class="form-control text-end rv-amount fw-bold" name="receipt_amount[]" placeholder="0.00" style="height: 34px; font-size: 13.5px; border-radius: 6px;">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-2 border-top mt-3">
                            <div class="row g-2 mb-2">
                                <div class="col-8">
                                    <label class="field-label mb-1" for="remarks" style="font-size: 11px;">Reference / Remarks</label>
                                    <input type="text" class="form-control form-control-sm" name="reference" id="remarks" placeholder="Optional..." style="height: 34px;">
                                </div>
                                <div class="col-4">
                                    <label class="field-label mb-1" for="creditDaysInput" style="font-size: 11px;">Credit Days</label>
                                    <input type="number" class="form-control form-control-sm text-center" id="creditDaysInput" name="credit_days" placeholder="0" min="0" value="{{ $sale->credit_days ?? '0' }}" style="height: 34px;">
                                </div>
                            </div>
                            <div class="change-row mt-2" id="changeAccountRow" style="display:none;">
                                <span class="change-label me-2 fw-semibold text-muted small">
                                    <i class="fas fa-exchange-alt me-1"></i> Change Account
                                </span>
                                <select class="form-select form-select-sm d-inline-block" name="change_account_id" id="changeAccountId" style="width: auto;">
                                    @foreach ($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ str_contains(strtolower($acc->title), 'cash') ? 'selected' : '' }}>{{ $acc->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Order Summary --}}
                <div class="col-lg-6">
                    <div class="sale-card p-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="card-title d-flex align-items-center gap-2" style="font-size: 15px;">
                                <i class="fas fa-file-alt text-primary"></i> Order Summary
                            </span>
                        </div>

                        <div>
                            <div class="s-row">
                                <span class="s-label">Subtotal</span>
                                <span class="s-val" id="tGross">0.00</span>
                            </div>
                            <div class="s-row">
                                <span class="s-label">Line Discount</span>
                                <span class="s-val" id="tLineDisc">0.00</span>
                            </div>
                            <div class="s-row">
                                <span class="s-label">Discount (Rs)</span>
                                <div class="input-group input-group-sm discount-input">
                                    <input type="number" class="form-control text-end" id="walkinDiscountRs" value="0" placeholder="0">
                                    <span class="input-group-text">Rs</span>
                                </div>
                            </div>
                            <div class="s-row">
                                <span class="s-label">Freight</span>
                                <div class="input-group input-group-sm discount-input">
                                    <button type="button" class="btn btn-outline-secondary" id="freightTypeToggle" tabindex="-1" style="width: 34px; border-radius: 6px 0 0 6px; font-weight: bold; font-size: 13px; background: #F8FAFC; color: #64748b; border-color: var(--pos-border);">+</button>
                                    <input type="hidden" name="freight_type" id="freightType" value="add">
                                    <input type="number" class="form-control text-end" id="freightCharges" name="freight_charges" value="0" placeholder="0">
                                    <span class="input-group-text">Rs</span>
                                </div>
                            </div>
                            <div class="s-row net my-2 p-2 rounded-3" style="background: #EFF6FF; border: 1px solid #BFDBFE;">
                                <span class="net-label fw-bold text-primary" style="font-size: 14px;">Net Total</span>
                                <span class="net-val fw-extrabold text-primary" id="tSub" style="font-size: 18px;">0.00</span>
                                <span id="walkinNetTotal" class="d-none">0.00</span>
                            </div>
                            <div class="s-row">
                                <span class="s-label">Total Paid</span>
                                <span class="paid-val text-success fw-bold" id="receiptsTotal">0.00</span>
                                <span id="receiptsTotalBadge" style="display:none;">0.00</span>
                                <span id="bottomPaymentsTotal" class="d-none">0.00</span>
                            </div>
                            <div class="s-row">
                                <span class="s-label">Remaining</span>
                                <span class="s-val text-danger fw-bold" id="tPayable">0.00</span>
                            </div>
                            <div class="s-row">
                                <span class="s-label">Change</span>
                                <span class="change-val text-success fw-bold" id="walkinChange">-0.00</span>
                                <span id="bottomChangeVal" class="d-none">-0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================ STICKY BOTTOM ACTION BAR ============================ --}}
            <div class="sale-bottom-bar">
                <div class="bb-left">
                    <span>Items: <b id="footerItemCount">0</b></span>
                    <span>Total: <b>Rs <span id="footerTotal">0.00</span></b></span>
                    <span>Paid: <b class="text-success">Rs <span id="footerPaid">0.00</span></b></span>
                </div>

                <div class="bb-secondary">
                    <button type="button" class="btn-ghost" id="btnPrint"><i class="fas fa-print"></i> A4 Print</button>
                    <button type="button" class="btn-ghost" id="btnEstimate"><i class="fas fa-file-invoice"></i> Estimate</button>
                    <button type="button" class="btn-ghost" id="btnPrint2"><i class="fas fa-receipt"></i> Thermal</button>
                    <button type="button" class="btn-ghost" id="btnDcThermal"><i class="fas fa-truck"></i> DC</button>
                    <button type="button" class="d-none" id="btnPosted">Sale</button>
                </div>

                <div class="bb-actions">
                    <a href="{{ route('sale.index') }}" class="btn btn-outline-secondary px-3">Cancel</a>
                    <button type="button" class="btn btn-outline-warning px-3" id="btnQuotation">
                        <i class="fas fa-file-alt me-1"></i> Quotation
                    </button>
                    <button type="button" class="btn btn-outline-primary px-3" id="btnSave">
                        <i class="fas fa-save me-1"></i> Booking
                    </button>
                    <button type="button" class="btn btn-primary btn-save-print px-3" id="btnSaveAndComplete">
                        <i class="fas fa-print me-1"></i> Save &amp; Print Invoice
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Products Offcanvas Drawer -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="quickProductsOffcanvas" style="width: 360px;">
        <div class="offcanvas-header bg-light py-2 border-bottom">
            <h6 class="offcanvas-title fw-bold text-dark mb-0"><i class="fas fa-th text-primary me-2"></i>Quick Products Panel</h6>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-primary fw-bold" id="btnNewProductFromDrawer">
                    <i class="fas fa-plus me-1"></i> New Product
                </button>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
        </div>
        <div class="offcanvas-body p-2">
            <div class="input-group input-group-sm mb-2">
                <input type="text" class="form-control" id="sidebarProductSearch" placeholder="Search product by name, barcode or SKU...">
                <button class="btn btn-primary px-2" type="button"><i class="fas fa-search"></i></button>
            </div>
            <div class="overflow-auto pe-1" id="sidebarProductContainer" style="max-height: calc(100vh - 120px);">
                @if(isset($recentProducts) && count($recentProducts) > 0)
                    @foreach($recentProducts as $prod)
                        <div class="pos-product-card">
                            <div class="pos-product-img">
                                <i class="fas fa-box text-secondary fs-5"></i>
                            </div>
                            <div class="pos-product-info">
                                <div class="pos-product-name" title="{{ $prod->item_name }}">{{ $prod->item_name }}</div>
                                <div class="pos-product-sub">
                                    <span class="badge-stock-green">{{ $prod->total_pieces ?? 0 }} Pcs</span> Stock
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="pos-product-price">{{ number_format($prod->retail_price ?? 0, 2) }}</div>
                                <button type="button" class="pos-product-add-btn add-product-direct-btn" data-id="{{ $prod->id }}" title="Add to Grid"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header" style="background: #2563EB !important; padding: 14px 18px;">
                    <h5 class="modal-title font-weight-bold fw-bold text-white mb-0" id="addCustomerModalLabel" style="font-size: 1rem;">
                        <i class="fas fa-user-plus me-2 mr-2"></i>Quick Customer
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; line-height: 1; opacity: 0.9; cursor: pointer;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="ajaxAddCustomerForm" autocomplete="off">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label font-weight-bold fw-bold">Customer Type <span class="text-danger">*</span></label>
                                <select class="form-control form-select" name="customer_type" id="modalCustomerType" required>
                                    @foreach(\App\Models\CustomerType::orderBy('name')->get() as $type)
                                        <option value="{{ $type->name }}" {{ $type->name === 'Main Customer' ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label font-weight-bold fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="customer_name" id="modalCustomerName" required placeholder="Customer Name">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label font-weight-bold fw-bold">Mobile</label>
                                <input type="text" class="form-control" name="mobile" placeholder="0300-1234567">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label font-weight-bold fw-bold">Opening Balance</label>
                                <input type="number" step="0.01" class="form-control" name="opening_balance" value="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label font-weight-bold fw-bold">Address</label>
                                <input type="text" class="form-control" name="address" placeholder="Address">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm fw-bold" id="btnSaveAjaxCustomer">
                        <i class="fas fa-save me-1 mr-1"></i> Save Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Build Product Modal --}}
    @include('admin_panel.partials.quick_build_product_modal')
@endsection

@section('js')
    @include('admin_panel.sale.scripts.shared_logic')

    <script>
        $(document).ready(function() {
            // --- Initial Setup ---
            $('#salesTableBody tr').each(function() {
                initProductSelect2($(this).find('.product'));
            });
            if ($('#salesTableBody tr').length === 0) {
                addNewRow();
            }
            updateGrandTotals();
            refreshPostedState();

            // --- Check if URL is for Booking Flow ---
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('type') === 'booking') {
                $('.header-text').html('<i class="fas fa-bookmark text-primary me-2"></i>Add Booking');
                $('#action').val('booking');
                $('#btnPosted').addClass('d-none');
                $('#btnHeaderPosted').addClass('d-none');
            }
            if (urlParams.get('type') === 'quotation') {
                $('.header-text').html('<i class="fas fa-file-alt text-warning me-2"></i>Add Quotation');
                $('#action').val('quotation');
                $('#btnPosted').addClass('d-none');
                $('#btnHeaderPosted').addClass('d-none');
                $('#btnSave').addClass('d-none');
                $('#btnHeaderSaveDraft').addClass('d-none');
            }

            // ============================================================
            // CUSTOMER SELECT2 AJAX SEARCH (Name or Code)
            // ============================================================
            function getPartyType() {
                return $('#partyTypeSelect').val() || 'Main Customer';
            }

            $('#customerSelect').select2({
                placeholder: 'Search by Name or Code...',
                allowClear: true,
                width: '100%',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route('salecustomers.index') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            type: getPartyType(),
                            search: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(c) {
                                return {
                                    id: c.id,
                                    text: (c.customer_id || '') + ' — ' + c.customer_name,
                                    customer: c
                                };
                            })
                        };
                    },
                    cache: false
                },
                language: {
                    noResults: function() {
                        return $('<div>No customer found. <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary py-0 px-2 mt-1 btn-open-customer-modal" style="font-size:0.75rem;"><i class="fas fa-user-plus"></i> Quick Add Customer</a></div>');
                    }
                },
                escapeMarkup: function(markup) {
                    return markup;
                },
                templateResult: function(item) {
                    if (item.loading) return item.text;
                    if (!item.customer) return item.text;
                    const c = item.customer;
                    return $(`<div>
                        <strong>${c.customer_name}</strong>
                        <small class="text-muted ms-2">${c.customer_id || ''}</small>
                        ${c.mobile ? '<br><small class="text-muted">' + c.mobile + '</small>' : ''}
                    </div>`);
                },
                templateSelection: function(item) {
                    if (!item.customer) return item.text;
                    return item.customer.customer_id + ' — ' + item.customer.customer_name;
                }
            });

            // Set initial visibility state of Customer Select / Walk-in input
            $('#partyTypeSelect').trigger('change');

            // Party type change → reset customer
            $(document).on('change', '#partyTypeSelect', function() {
                $('#customerSelect').val(null).trigger('change');
                clearCustomerInfo();
            });

            // Customer selected → load details
            $('#customerSelect').on('select2:select', function(e) {
                const id = e.params.data.id;
                if (!id) return;

                $.get("{{ url('sale/customers') }}/" + id + "?t=" + new Date().getTime(), function(d) {
                    // Fill hidden fields
                    $('#address').val(d.address || '');
                    $('#tel').val(d.mobile || '');
                    const prev = parseFloat(d.previous_balance || 0);
                    const range = parseFloat(d.balance_range || 0);
                    $('#previousBalance').val(prev.toFixed(2));
                    $('#rangeBalance').val(range.toFixed(2));

                    // Fill info card
                    $('#ci_code').text(d.customer_id || '—');
                    $('#ci_name').text(d.customer_name || '—');
                    $('#ci_mobile').text(d.mobile || '—');
                    $('#ci_address').text(d.address || '—');
                    $('#ci_prev_bal').text(prev.toFixed(2));
                    $('#ci_range_bal').text(range.toFixed(2));
                    $('#customerInfoCard').removeClass('d-none');

                    // Auto-fill Sales Officer if customer has one
                    if (d.sales_officer_id) {
                        $('#salesOfficerSelect').val(d.sales_officer_id);
                    }

                    if (typeof updateGrandTotals === 'function') updateGrandTotals();
                }).fail(function() {
                    showAlert('error', 'Failed to load customer details');
                });
            });

            // Customer cleared
            $('#customerSelect').on('select2:clear', function() {
                clearCustomerInfo();
                if (typeof updateGrandTotals === 'function') updateGrandTotals();
            });

            function clearCustomerInfo() {
                $('#address, #tel').val('');
                $('#previousBalance, #rangeBalance').val('0');
                $('#ci_code, #ci_name, #ci_mobile, #ci_address').text('—');
                $('#ci_prev_bal, #ci_range_bal').text('0.00');
                $('#customerInfoCard').addClass('d-none');
                $('#salesOfficerSelect').val('');
            }

            $('#clearCustomerData').on('click', function() {
                $('#customerSelect').val(null).trigger('change');
                clearCustomerInfo();
                if (typeof updateGrandTotals === 'function') updateGrandTotals();
            });

            $('#btnPrint').on('click', function() {
                ensureSaved().then(id => window.open('{{ url('sales') }}/' + id + '/invoice', '_blank'));
            });
            $('#btnEstimate').on('click', function() {
                ensureSaved().then(id => window.open('{{ url('sales') }}/' + id + '/invoice?type=estimate', '_blank'));
            });
            $('#btnPrint2').on('click', function() {
                ensureSaved().then(id => window.open('{{ url('sales') }}/' + id + '/recepit', '_blank'));
            });
            $('#btnDcThermal').on('click', function() {
                ensureSaved().then(id => window.open('{{ url('sales') }}/' + id + '/dc-thermal', '_blank'));
            });

            // ══════════════════════════════════════════════════════════════
            // QUICK CUSTOMER MODAL LOGIC & EVENT HANDLERS
            // ══════════════════════════════════════════════════════════════
            window.openCustomerModal = function(initialName = '') {
                $('#ajaxAddCustomerForm')[0].reset();
                let currentParty = $('#partyTypeSelect').val() || 'Main Customer';
                $('#modalCustomerType').val(currentParty);
                if (initialName && typeof initialName === 'string') {
                    $('#modalCustomerName').val(initialName.trim());
                }
                
                if (typeof $('#addCustomerModal').modal === 'function') {
                    $('#addCustomerModal').modal('show');
                } else if (window.bootstrap && window.bootstrap.Modal) {
                    let m = bootstrap.Modal.getOrCreateInstance(document.getElementById('addCustomerModal'));
                    m.show();
                }

                setTimeout(function() {
                    $('#modalCustomerName').focus();
                }, 400);
            };

            window.closeCustomerModal = function() {
                try {
                    $('#addCustomerModal').modal('hide');
                } catch(e) {}
                if (window.bootstrap && window.bootstrap.Modal) {
                    let m = bootstrap.Modal.getInstance(document.getElementById('addCustomerModal'));
                    if (m) m.hide();
                }
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');
            };

            // Explicit click listener on any button with btn-open-customer-modal or #btnOpenAddCustomerModal
            $(document).on('click', '#btnOpenAddCustomerModal, .btn-open-customer-modal', function(e) {
                e.preventDefault();
                let term = '';
                // If clicked from select2 noResults or search box, grab search term
                if ($('.select2-search__field:visible').length) {
                    term = $('.select2-search__field:visible').val();
                    $('#customerSelect').select2('close');
                }
                openCustomerModal(term);
            });

            // Keyboard shortcut (F2 or Alt+C) to open Quick Customer modal
            $(document).on('keydown', function(e) {
                if ((e.key === 'F2' || (e.altKey && (e.key === 'c' || e.key === 'C'))) && !$('#addCustomerModal').is(':visible')) {
                    e.preventDefault();
                    openCustomerModal();
                }
            });

            // AJAX Customer Submit
            $('#btnSaveAjaxCustomer').on('click', function() {
                let form = $('#ajaxAddCustomerForm');
                if (!form[0].checkValidity()) {
                    form[0].reportValidity();
                    return;
                }
                
                let btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
                
                $.ajax({
                    url: '{{ route('customers.store') }}',
                    type: 'POST',
                    data: form.serialize(),
                    success: function(res) {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1 mr-1"></i> Save Customer');
                        if (res.success) {
                            closeCustomerModal();
                            form[0].reset();
                            
                            // Make sure partyTypeSelect matches the customer type
                            if (res.customer.customer_type) {
                                $('#partyTypeSelect').val(res.customer.customer_type);
                            }
                            
                            // Auto select new customer in Select2
                            let displayText = (res.customer.customer_id ? res.customer.customer_id + ' — ' : '') + res.customer.customer_name;
                            let newOption = new Option(displayText, res.customer.id, true, true);
                            $('#customerSelect').append(newOption).trigger('change');
                            
                            // Trigger select2 API selection to load customer details like Prev Bal
                            $('#customerSelect').trigger({
                                type: 'select2:select',
                                params: {
                                    data: {
                                        id: res.customer.id,
                                        text: displayText
                                    }
                                }
                            });
                            
                            showAlert('success', 'Customer added successfully!');
                        } else {
                            showAlert('error', res.message || 'Failed to save customer.');
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1 mr-1"></i> Save Customer');
                        let msg = 'Error adding customer. Check inputs.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showAlert('error', msg);
                    }
                });
            });

            // ══════════════════════════════════════════════════════════════
            // DYNAMIC INVOICE SERIES & PREFIX GENERATOR LOGIC (INSTANT 0ms)
            // ══════════════════════════════════════════════════════════════
            let currentInvoicePrefix = "{{ $activePrefix ?? 'INV' }}";

            function fetchNextInvoiceNo(prefix) {
                $('#iconRefreshInvoice').addClass('fa-spin');
                $.ajax({
                    url: "{{ route('invoice_series.generate_no') }}",
                    type: "GET",
                    data: { prefix: prefix },
                    success: function(res) {
                        $('#iconRefreshInvoice').removeClass('fa-spin');
                        if (res.invoice_no) {
                            $('#inputInvoiceNo').val(res.invoice_no);
                        }
                    },
                    error: function() {
                        $('#iconRefreshInvoice').removeClass('fa-spin');
                    }
                });
            }

            // Prefix Selection Handler
            $(document).on('click', '#dropdownInvoiceSeriesList a[data-prefix]', function(e) {
                e.preventDefault();
                let prefix = $(this).data('prefix');
                if (!prefix) return;

                currentInvoicePrefix = prefix;
                $('#activePrefixLabel').text(prefix);

                // Update active highlight in dropdown instantly
                $('#dropdownInvoiceSeriesList a[data-prefix]').removeClass('text-success active bg-light').find('i.fa-check').remove();
                $(this).addClass('text-success active bg-light').prepend('<i class="fas fa-check text-success me-1"></i>');

                fetchNextInvoiceNo(prefix);
            });

            // Refresh Invoice No Handler
            $(document).on('click', '#btnRefreshInvoiceNo', function() {
                fetchNextInvoiceNo(currentInvoicePrefix);
            });

            // Open Add Series Modal
            $(document).on('click', '#btnOpenAddSeriesModal', function(e) {
                e.preventDefault();
                $('#modalAddInvoiceSeries').modal('show');
            });

            // Submit Add Series Form via AJAX
            $('#formAddInvoiceSeries').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                let btn = $('#btnSaveSeries');

                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving…');

                $.ajax({
                    url: "{{ route('invoice_series.store') }}",
                    type: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save &amp; Select');
                        
                        if (res.success) {
                            $('#modalAddInvoiceSeries').modal('hide');
                            $('#formAddInvoiceSeries')[0].reset();

                            currentInvoicePrefix = res.prefix;
                            $('#activePrefixLabel').text(res.prefix);
                            $('#inputInvoiceNo').val(res.invoice_no);

                            // Update or insert item in dropdown list instantly
                            let existingItem = $(`#dropdownInvoiceSeriesList a[data-prefix="${res.prefix}"]`);
                            if (existingItem.length > 0) {
                                existingItem.data('next', res.series.next_number).data('padding', res.series.padding);
                            } else {
                                let newItemHtml = `<li>
                                    <a class="dropdown-item fw-bold text-success active bg-light" href="#" data-prefix="${res.prefix}" data-next="${res.series.next_number}" data-padding="${res.series.padding}">
                                        <i class="fas fa-check text-success me-1"></i> ${res.prefix} <span class="text-muted small font-monospace">(${res.series.padding}d)</span>
                                    </a>
                                </li>`;
                                $('#dropdownInvoiceSeriesList li:has(hr)').before(newItemHtml);
                            }

                            // Update active highlight state
                            $('#dropdownInvoiceSeriesList a[data-prefix]').removeClass('text-success active bg-light').find('i.fa-check').remove();
                            $(`#dropdownInvoiceSeriesList a[data-prefix="${res.prefix}"]`).addClass('text-success active bg-light').prepend('<i class="fas fa-check text-success me-1"></i>');

                            if (typeof showAlert === 'function') {
                                showAlert('success', res.message);
                            } else if (typeof Swal !== 'undefined') {
                                Swal.fire({ icon: 'success', title: 'Saved!', text: res.message, timer: 1500, showConfirmButton: false });
                            } else {
                                alert(res.message);
                            }
                        }
                    },
                    error: function(err) {
                        btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save &amp; Select');
                        let msg = 'Error saving series. Please check form inputs.';
                        if (err.responseJSON && err.responseJSON.errors) {
                            msg = Object.values(err.responseJSON.errors).flat().join('\n');
                        } else if (err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        
                        if (typeof showAlert === 'function') {
                            showAlert('error', msg);
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', msg, 'error');
                        } else {
                            alert(msg);
                        }
                    }
                });
            });
        });
    </script>

    {{-- New Sale UI additions (footer totals sync, header save-draft, global product search) --}}
    <script>
        $(function() {
            // Footer totals sync (display-only mirror of existing values)
            if (typeof window.updateGrandTotals === 'function') {
                var __baseUGT = window.updateGrandTotals;
                window.updateGrandTotals = function() {
                    __baseUGT();
                    if ($('#footerItemCount').length) $('#footerItemCount').text($('#itemsRowCount').text());
                    if ($('#footerTotal').length) $('#footerTotal').text($('#tSub').text());
                    if ($('#footerPaid').length) $('#footerPaid').text($('#receiptsTotal').text());
                };
                window.updateGrandTotals();
            }

            // Header Save Draft -> existing booking/save flow
            $('#btnHeaderSaveDraft').on('click', function() {
                $('#btnSave').trigger('click');
            });
        });
    </script>

    <!-- Modal: Add / Manage Invoice Series -->
    <div class="modal fade" id="modalAddInvoiceSeries" tabindex="-1" aria-labelledby="modalAddInvoiceSeriesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom bg-light px-3 py-2">
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalAddInvoiceSeriesLabel">
                        <i class="fas fa-barcode text-success me-1"></i> Add Invoice Series
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAddInvoiceSeries">
                    @csrf
                    <div class="modal-body p-3">
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-secondary mb-1">Prefix (e.g., SQ, POS, INV)</label>
                            <input type="text" name="prefix" id="seriesPrefixInput" class="form-control form-control-sm text-uppercase fw-bold" placeholder="e.g. SQ" required style="letter-spacing: 1px;">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-secondary mb-1">Starting Number (Counter)</label>
                            <input type="number" name="next_number" id="seriesNextNumInput" class="form-control form-control-sm fw-bold text-primary" placeholder="e.g. 50" min="1" value="50" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-secondary mb-1">Padding Length (Zero Digits)</label>
                            <select name="padding" id="seriesPaddingSelect" class="form-select form-select-sm fw-bold">
                                <option value="4">4 Digits (e.g., 0050)</option>
                                <option value="6" selected>6 Digits (e.g., 000050)</option>
                                <option value="8">8 Digits (e.g., 00000050)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-top p-2 px-3">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm fw-bold px-3" id="btnSaveSeries">
                            <i class="fas fa-save me-1"></i> Save &amp; Select
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Select Product Batch -->
    <div class="modal fade" id="modalSelectBatch" tabindex="-1" aria-labelledby="modalSelectBatchLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom bg-light px-3 py-2">
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalSelectBatchLabel">
                        <i class="fas fa-layer-group text-primary me-1"></i> Select Batch for Product
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <input type="hidden" id="activeBatchRowIndex" value="">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0 text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th>Batch #</th>
                                    <th>MFG Date</th>
                                    <th>Expiry Date</th>
                                    <th>Avail. Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="batchModalTableBody">
                                <tr>
                                    <td colspan="5" class="text-muted py-3">Loading available batches...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Select Product Serials / IMEIs -->
    <div class="modal fade" id="modalSelectSerial" tabindex="-1" aria-labelledby="modalSelectSerialLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom bg-light px-3 py-2">
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalSelectSerialLabel">
                        <i class="fas fa-barcode text-success me-1"></i> Select Serial / IMEI Numbers
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <input type="hidden" id="activeSerialRowIndex" value="">
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="serialSearchInput" placeholder="Scan IMEI barcode or search serial number...">
                    </div>
                    <div class="border rounded p-2 overflow-auto" style="max-height: 280px;" id="serialListContainer">
                        <div class="text-muted text-center py-3">Loading available serial numbers...</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                        <span class="small text-muted">Selected IMEIs: <strong id="selectedSerialCount" class="text-primary">0</strong></span>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-2 px-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success btn-sm fw-bold px-3" id="btnApplySelectedSerials">
                        <i class="fas fa-check me-1"></i> Apply Selected IMEIs
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Details Modal -->
    <div class="modal fade" id="modalCustomerDetails" tabindex="-1" aria-labelledby="modalCustomerDetailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-light border-bottom p-3">
                    <h5 class="modal-title fw-bold text-primary fs-6 d-flex align-items-center gap-2" id="modalCustomerDetailsLabel">
                        <i class="fas fa-user-circle fs-5"></i> Customer Information
                    </h5>
                    <button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; line-height: 1; cursor: pointer;"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-2 rounded bg-light border">
                                <label class="text-muted fw-bold d-block" style="font-size: 11px;">FULL NAME</label>
                                <span class="fw-bold text-dark fs-6" id="ci_modal_name">—</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-light border">
                                <label class="text-muted fw-bold d-block" style="font-size: 11px;">MOBILE</label>
                                <span class="fw-bold text-dark fs-6" id="ci_modal_mobile">—</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 rounded bg-light border">
                                <label class="text-muted fw-bold d-block" style="font-size: 11px;">ADDRESS</label>
                                <span class="fw-bold text-dark" id="ci_modal_address" style="font-size: 13.5px;">—</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded border" style="background: #FEF2F2; border-color: #FCA5A5 !important;">
                                <label class="text-danger fw-bold d-block" style="font-size: 11px;">PREV. DUE</label>
                                <span class="fw-bold text-danger fs-6" id="ci_modal_prev">—</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded border" style="background: #F3E8FF; border-color: #D8B4FE !important;">
                                <label class="fw-bold d-block" style="font-size: 11px; color: #9333EA;">CLOSING BALANCE</label>
                                <span class="fw-bold fs-6" id="ci_modal_closing" style="color: #9333EA;">—</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-2 px-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection