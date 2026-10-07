@extends('admin_panel.layout.app')
@section('content')

{{-- CDN Libraries --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
<link rel="stylesheet" href="{{ asset('assets/fonts/inter/inter.css') }}">

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --pw-primary: #2563eb;
    --pw-primary-dark: #1d4ed8;
    --pw-primary-light: #eff6ff;
    --pw-success: #10b981;
    --pw-success-light: #ecfdf5;
    --pw-warning: #f59e0b;
    --pw-danger: #ef4444;
    --pw-purple: #8b5cf6;
    --pw-slate-900: #0f172a;
    --pw-slate-800: #1e293b;
    --pw-slate-700: #334155;
    --pw-slate-600: #475569;
    --pw-slate-100: #f1f5f9;
    --pw-border: #cbd5e1;
    --pw-radius: 14px;
    --pw-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.05), 0 4px 6px -2px rgba(15, 23, 42, 0.02);
}

body {
    font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
    background-color: #f6f8fa;
    color: var(--pw-slate-800);
}

.pw-container {
    padding: 10px 20px 40px 20px;
    max-width: 1560px;
    margin: 0 auto;
}

/* --- Hero Banner Card --- */
.pw-header-card {
    background: #ffffff;
    border-radius: var(--pw-radius);
    border: 1.5px solid var(--pw-border);
    box-shadow: var(--pw-shadow);
    padding: 22px 28px;
    margin-bottom: 22px;
}

.pw-page-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--pw-slate-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

/* --- Voucher Type Selector Pills --- */
.voucher-type-pills {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 4px;
    margin-bottom: 22px;
}

.voucher-type-pill {
    background: #ffffff;
    border: 1.5px solid var(--pw-border);
    border-radius: 12px;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
    flex-shrink: 0;
}

.voucher-type-pill:hover {
    border-color: var(--pw-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.12);
}

.voucher-type-pill.active {
    background: var(--pw-primary);
    border-color: var(--pw-primary);
    color: #ffffff !important;
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.25);
}

.voucher-type-pill.active i,
.voucher-type-pill.active span {
    color: #ffffff !important;
}

.voucher-type-pill i {
    font-size: 1.1rem;
    color: var(--pw-primary);
}

.voucher-type-pill span {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--pw-slate-800);
}

/* --- Form Section Cards --- */
.pw-card {
    background: #ffffff;
    border-radius: var(--pw-radius);
    border: 1.5px solid var(--pw-border);
    box-shadow: var(--pw-shadow);
    padding: 24px;
    margin-bottom: 22px;
}

.pw-card-title {
    font-size: 1rem;
    font-weight: 800;
    color: var(--pw-slate-900);
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* --- Inputs & Selects --- */
.pw-label {
    font-size: 0.74rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--pw-slate-700);
    margin-bottom: 6px;
    display: block;
}

.pw-input {
    height: 42px;
    border-radius: 10px;
    border: 1.5px solid var(--pw-border);
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--pw-slate-900);
    padding: 0 12px;
    transition: all 0.15s ease;
    background-color: #ffffff;
}

.pw-input:focus {
    border-color: var(--pw-primary);
    box-shadow: 0 0 0 3.5px rgba(37, 99, 235, 0.12);
    outline: none;
}

/* --- Cash vs Bank Payment Head Buttons --- */
.payment-head-selector {
    display: flex;
    gap: 12px;
    margin-bottom: 18px;
}

.head-btn {
    flex: 1;
    background: #ffffff;
    border: 2px solid var(--pw-border);
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    cursor: pointer;
    font-weight: 800;
    font-size: 0.92rem;
    color: var(--pw-slate-700);
    transition: all 0.2s ease;
}

.head-btn:hover {
    border-color: var(--pw-primary);
    background: #f8fafc;
}

.head-btn.active-cash {
    border-color: var(--pw-success);
    background: var(--pw-success-light);
    color: var(--pw-success);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
}

.head-btn.active-bank {
    border-color: var(--pw-primary);
    background: var(--pw-primary-light);
    color: var(--pw-primary);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
}

/* --- Stat Summary Badges (Top Right) --- */
.pw-summary-stat {
    background: #f8fafc;
    border: 1.5px solid var(--pw-border);
    border-radius: 12px;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.pw-stat-value {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--pw-slate-900);
    line-height: 1.1;
}

.pw-stat-lbl {
    font-size: 0.7rem;
    font-weight: 800;
    color: var(--pw-slate-600);
    text-transform: uppercase;
}

/* --- Excel Spreadsheet Style Multi-Row Table --- */
.multi-row-table {
    border: 2px solid #cbd5e1 !important;
    border-collapse: collapse !important;
    width: 100%;
}

.multi-row-table th {
    background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%) !important;
    color: #0f172a !important;
    font-size: 0.74rem !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    padding: 10px 12px !important;
    border: 1px solid #cbd5e1 !important;
    border-bottom: 2.5px solid #64748b !important;
    white-space: nowrap;
}

.multi-row-table td {
    padding: 6px 8px !important;
    vertical-align: middle !important;
    border: 1px solid #cbd5e1 !important;
    background: #ffffff;
}

.multi-row-table tr:nth-child(even) td {
    background: #f8fafc;
}

.multi-row-table tr:hover td {
    background: #f1f5f9 !important;
}

/* --- Select2 Custom Styling --- */
.select2-container--default .select2-selection--single {
    height: 40px !important;
    border: 1.5px solid var(--pw-border) !important;
    border-radius: 8px !important;
    padding: 4px 8px !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--pw-slate-900) !important;
    font-weight: 700 !important;
    font-size: 0.86rem !important;
    line-height: 30px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
}

.select2-dropdown {
    border-radius: 10px !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    border: 1.5px solid var(--pw-border) !important;
}

/* --- Dynamic Sections Toggle --- */
.voucher-form-section {
    display: none;
}
.voucher-form-section.active {
    display: block;
}

/* --- Keyboard Shortcut Hint Badge --- */
.kb-hint-badge {
    background: #e2e8f0;
    color: #334155;
    font-size: 0.72rem;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    font-family: monospace;
}
</style>

<div class="pw-container">

    {{-- Header Banner Card --}}
    <div class="pw-header-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="pw-page-title">
                    <i class="fas fa-file-invoice-dollar text-primary"></i>
                    Voucher Entry & Accounting Terminal
                </h3>
                <p class="text-muted mb-0 mt-1" style="font-size: 0.88rem;">
                    Multi-Account transaction vouchers with <b>Cash & Bank</b> primary heads. Press <span class="kb-hint-badge">ENTER</span> key on table row to add new accounts instantly.
                </p>
            </div>
            <div>
                <a href="{{ route('voucher.history') }}" class="btn btn-outline-primary fw-bold px-4 py-2" style="border-radius: 10px; font-size: 0.88rem;">
                    <i class="fas fa-list-check me-1"></i> All Vouchers History
                </a>
            </div>
        </div>
    </div>

    {{-- Voucher Type Selector Pills (5 Voucher Types) --}}
    <div class="voucher-type-pills" id="voucherTypeSelector">
        <div class="voucher-type-pill active" data-type="expense">
            <i class="fas fa-receipt"></i>
            <span>Expense Voucher</span>
        </div>
        <div class="voucher-type-pill" data-type="payment_in">
            <i class="fas fa-arrow-down-left"></i>
            <span>Payment In (Receipt)</span>
        </div>
        <div class="voucher-type-pill" data-type="payment_out">
            <i class="fas fa-arrow-up-right"></i>
            <span>Payment Out (Paid)</span>
        </div>
        <div class="voucher-type-pill" data-type="party_transfer">
            <i class="fas fa-arrows-split-up-and-left"></i>
            <span>Party-To-Party Transfer</span>
        </div>
        <div class="voucher-type-pill" data-type="internal_transfer">
            <i class="fas fa-building-columns"></i>
            <span>Internal Transfer (Cash ↔ Bank)</span>
        </div>
    </div>

    {{-- MAIN VOUCHER FORM CONTAINER CARD --}}
    <div class="pw-card">
        
        {{-- =========================================================================
           1. EXPENSE VOUCHER (Multi-Account Category Table + Enter Key Navigation)
           ========================================================================= --}}
        <div class="voucher-form-section active" id="form-expense">
            <form class="voucher-form" data-action="{{ route('store_expense_vochers') }}" method="POST">
                @csrf
                <input type="hidden" name="vendor_type" value="account">

                {{-- Top Controls Row --}}
                <div class="row g-3 mb-4 align-items-end">
                    <div class="col-md-2 col-6">
                        <label class="pw-label">VOUCHER ID</label>
                        <input type="text" class="form-control pw-input fw-bold bg-light" value="{{ $nextEvid ?? 'EVID-Auto' }}" readonly>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="pw-label">ENTRY DATE <span class="text-danger">*</span></label>
                        <input type="date" name="entry_date" class="form-control pw-input fw-bold" value="{{ date('Y-m-d') }}" required>
                    </div>

                    {{-- 2 Primary Payment Heads: Cash vs Bank Filter --}}
                    <div class="col-md-5 col-12">
                        <label class="pw-label">PRIMARY SOURCE HEAD (CASH / BANK) <span class="text-danger">*</span></label>
                        <div class="payment-head-selector mb-2" id="expHeadTypeToggle">
                            <div class="head-btn active-cash" data-head="cash">
                                <i class="fas fa-money-bill-wave text-success"></i> 💵 Cash Accounts
                            </div>
                            <div class="head-btn" data-head="bank">
                                <i class="fas fa-university text-primary"></i> 🏦 Bank Accounts
                            </div>
                        </div>

                        <select name="vendor_id" id="expSourceAccountSelect" class="form-select select2-account" required>
                            <option value="">Select Primary Cash / Bank Account...</option>
                            @foreach($accounts as $acc)
                                @php
                                    $isBank = (str_contains(strtolower($acc->title), 'bank') || str_contains(strtolower($acc->account_code), 'bank') || str_contains(strtolower($acc->title), 'meezan') || str_contains(strtolower($acc->title), 'hbl') || str_contains(strtolower($acc->title), 'ubl') || str_contains(strtolower($acc->title), 'mcb'));
                                @endphp
                                <option value="{{ $acc->id }}" data-is-bank="{{ $isBank ? '1' : '0' }}" data-code="{{ $acc->account_code }}" data-balance="{{ $acc->current_balance ?? $acc->opening_balance ?? 0 }}">
                                    {{ $acc->title }} ({{ $acc->account_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Live Balance & Code Summary Cards --}}
                    <div class="col-md-3 col-12 text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <div class="pw-summary-stat flex-fill">
                                <div>
                                    <div class="pw-stat-lbl">Source Balance</div>
                                    <div class="pw-stat-value text-primary" id="expBalanceDisplay">0.00 PKR</div>
                                </div>
                            </div>
                            <div class="pw-summary-stat flex-fill">
                                <div>
                                    <div class="pw-stat-lbl">Account Code</div>
                                    <div class="pw-stat-value text-dark" id="expCodeDisplay">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Remarks Row --}}
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="pw-label">MEMO / GENERAL REMARKS</label>
                        <input type="text" name="remarks" class="form-control pw-input" placeholder="e.g. Office monthly operational expenses payment">
                    </div>
                </div>

                {{-- Multi-Account Entry Table Card --}}
                <div class="pw-card-title">
                    <i class="fas fa-layer-group text-primary"></i>
                    Multi-Account Expense Breakdown Grid
                    <span class="ms-auto text-muted small fw-normal">Press <span class="kb-hint-badge">ENTER</span> key to add row instantly</span>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table multi-row-table align-middle" id="expenseTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 35px;">#</th>
                                <th>EXPENSE CATEGORY / ACCOUNT HEAD <span class="text-danger">*</span></th>
                                <th style="width: 35%;">NARRATION / SPECIFIC NOTE</th>
                                <th class="text-end" style="width: 180px;">AMOUNT (PKR) <span class="text-danger">*</span></th>
                                <th class="text-center" style="width: 45px;">ACT</th>
                            </tr>
                        </thead>
                        <tbody id="expenseRows">
                            <tr>
                                <td class="text-center fw-bold text-muted item-row-idx">1</td>
                                <td>
                                    <select name="row_account_id[]" class="form-select rowAccountSub select2-cat" required>
                                        <option value="">Select Expense Category Head...</option>
                                        @foreach($expenseCategories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name ?? $cat->title }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="narration_id[]" value="">
                                </td>
                                <td>
                                    <input type="text" name="narration_text[]" class="form-control pw-input row-narration" placeholder="Details e.g. Tea for guests / Generator Fuel" value="Expense">
                                </td>
                                <td>
                                    <input type="number" step="any" min="0.01" name="amount[]" class="form-control pw-input text-end exp-amount fw-bold" placeholder="0.00" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle removeRow" title="Delete Row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Action Bar & Live Totals --}}
                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-4 border flex-wrap gap-3">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary fw-bold add-expense-row" style="border-radius: 10px;">
                            <i class="fas fa-plus me-1"></i> Add Account Row (Enter)
                        </button>
                        <button type="button" class="btn btn-outline-secondary fw-semibold btn-open-cat-modal" style="border-radius: 10px;">
                            <i class="fas fa-folder-plus me-1"></i> Add New Category
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-4">
                        <div class="text-end">
                            <span class="pw-stat-lbl">TOTAL NET VOUCHER VALUE:</span>
                            <div class="fw-extrabold text-primary fs-4" id="expenseTotal">0.00 PKR</div>
                            <input type="hidden" name="total_amount" id="expenseTotalInput" value="0">
                        </div>
                        <button type="submit" class="btn btn-success px-5 py-2 fw-bold text-white shadow" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; border-radius: 10px; font-size: 0.95rem;">
                            <i class="fas fa-check-circle me-1"></i> SAVE EXPENSE VOUCHER
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- =========================================================================
           2. PAYMENT IN (RECEIPT) VOUCHER
           ========================================================================= --}}
        <div class="voucher-form-section" id="form-payment_in">
            <form class="voucher-form" data-action="{{ route('store_rec_vochers') }}" method="POST">
                @csrf
                <input type="hidden" name="receipt_date" id="pi_receipt_date" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="entry_date" value="{{ date('Y-m-d') }}">

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="pw-label">PAYMENT DATE <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control pw-input fw-bold" value="{{ date('Y-m-d') }}" onchange="$('#pi_receipt_date').val(this.value)" required>
                    </div>
                    <div class="col-md-5">
                        <label class="pw-label">DEPOSIT TO (CASH / BANK HEAD) <span class="text-danger">*</span></label>
                        <select name="row_account_id[]" id="pi_deposit_account" class="form-select select2-account" required>
                            <option value="">Search cash / bank account...</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->title }} ({{ $acc->account_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="pw-summary-stat mt-4">
                            <div>
                                <div class="pw-stat-lbl">Account Balance</div>
                                <div class="pw-stat-value text-success" id="pi_deposit_balance_text">0.00 PKR</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Party Selection --}}
                <div class="bg-light p-3 rounded-4 border mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label class="pw-label">RECEIVED FROM PARTY TYPE <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <div class="form-check form-check-inline me-3 fw-bold">
                                    <input class="form-check-input pi-party-type" type="radio" name="vendor_type" id="pi_customer" value="customer" checked>
                                    <label class="form-check-label" for="pi_customer">Customer</label>
                                </div>
                                <div class="form-check form-check-inline fw-bold">
                                    <input class="form-check-input pi-party-type" type="radio" name="vendor_type" id="pi_vendor" value="vendor">
                                    <label class="form-check-label" for="pi_vendor">Vendor</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div id="pi_customer_wrapper">
                                <label class="pw-label">SELECT CUSTOMER <span class="text-danger">*</span></label>
                                <select name="vendor_id" id="pi_customer_select" class="form-select select2-customer" required>
                                    <option value="">Search customer by name or phone...</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->customer_name }} @if(!empty($c->mobile)) ({{ $c->mobile }}) @endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="pi_vendor_wrapper" style="display:none;">
                                <label class="pw-label">SELECT VENDOR <span class="text-danger">*</span></label>
                                <select name="vendor_id_vendor" id="pi_vendor_select" class="form-select select2-vendor" disabled>
                                    <option value="">Search vendor...</option>
                                    @foreach($vendors as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }} @if(!empty($v->phone)) ({{ $v->phone }}) @endif</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Amount & Remarks --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="pw-label">RECEIVED AMOUNT (PKR) <span class="text-danger">*</span></label>
                        <input type="number" step="any" min="0.01" name="amount[]" id="pi_amount" class="form-control pw-input fw-bold fs-5 text-success text-end" placeholder="0.00" required oninput="$('#pi_total_amount').val(this.value)">
                        <input type="hidden" name="total_amount" id="pi_total_amount" value="">
                        <input type="hidden" name="narration_id[]" value="">
                        <input type="hidden" name="narration_text[]" value="Payment Received">
                    </div>
                    <div class="col-md-8">
                        <label class="pw-label">REMARKS / REFERENCE</label>
                        <input type="text" name="remarks" class="form-control pw-input" placeholder="e.g. Received via cheque/online transfer">
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success px-5 py-2 fw-bold text-white shadow" style="border-radius: 10px;">
                        <i class="fas fa-check-circle me-1"></i> SAVE PAYMENT IN (RECEIPT)
                    </button>
                </div>
            </form>
        </div>

        {{-- =========================================================================
           3. PAYMENT OUT (PAID) VOUCHER
           ========================================================================= --}}
        <div class="voucher-form-section" id="form-payment_out">
            <form class="voucher-form" data-action="{{ route('store_Pay_vochers') }}" method="POST">
                @csrf
                <input type="hidden" name="receipt_date" id="po_receipt_date" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="entry_date" value="{{ date('Y-m-d') }}">

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="pw-label">PAYMENT DATE <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control pw-input fw-bold" value="{{ date('Y-m-d') }}" onchange="$('#po_receipt_date').val(this.value)" required>
                    </div>
                    <div class="col-md-5">
                        <label class="pw-label">PAY FROM (CASH / BANK HEAD) <span class="text-danger">*</span></label>
                        <select name="header_account_id" id="po_payfrom_account" class="form-select select2-account" required>
                            <option value="">Search cash / bank account...</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->title }} ({{ $acc->account_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="pw-summary-stat mt-4">
                            <div>
                                <div class="pw-stat-lbl">Account Balance</div>
                                <div class="pw-stat-value text-danger" id="po_payfrom_balance_text">0.00 PKR</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pay To Party --}}
                <div class="bg-light p-3 rounded-4 border mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label class="pw-label">PAY TO PARTY TYPE <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <div class="form-check form-check-inline me-3 fw-bold">
                                    <input class="form-check-input po-party-type" type="radio" name="vendor_type_choice" id="po_vendor" value="vendor" checked>
                                    <label class="form-check-label" for="po_vendor">Vendor</label>
                                </div>
                                <div class="form-check form-check-inline fw-bold">
                                    <input class="form-check-input po-party-type" type="radio" name="vendor_type_choice" id="po_customer" value="customer">
                                    <label class="form-check-label" for="po_customer">Customer</label>
                                </div>
                            </div>
                            <input type="hidden" name="vendor_type[]" id="po_vendor_type" value="vendor">
                        </div>

                        <div class="col-md-8">
                            <div id="po_vendor_wrapper">
                                <label class="pw-label">SELECT VENDOR <span class="text-danger">*</span></label>
                                <select name="vendor_id[]" id="po_vendor_select" class="form-select select2-vendor" required>
                                    <option value="">Search vendor...</option>
                                    @foreach($vendors as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }} @if(!empty($v->phone)) ({{ $v->phone }}) @endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="po_customer_wrapper" style="display:none;">
                                <label class="pw-label">SELECT CUSTOMER <span class="text-danger">*</span></label>
                                <select name="vendor_id_cust" id="po_customer_select" class="form-select select2-customer" disabled>
                                    <option value="">Search customer...</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->customer_name }} @if(!empty($c->mobile)) ({{ $c->mobile }}) @endif</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Amount & Remarks --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="pw-label">PAID AMOUNT (PKR) <span class="text-danger">*</span></label>
                        <input type="number" step="any" min="0.01" name="amount[]" id="po_amount" class="form-control pw-input fw-bold fs-5 text-danger text-end" placeholder="0.00" required oninput="$('#po_total_amount').val(this.value)">
                        <input type="hidden" name="total_amount" id="po_total_amount" value="">
                        <input type="hidden" name="narration_id[]" value="">
                        <input type="hidden" name="narration_text[]" value="Payment Made">
                    </div>
                    <div class="col-md-8">
                        <label class="pw-label">REMARKS / REFERENCE</label>
                        <input type="text" name="remarks" class="form-control pw-input" placeholder="e.g. Paid against bill invoice">
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-danger px-5 py-2 fw-bold text-white shadow" style="border-radius: 10px;">
                        <i class="fas fa-check-circle me-1"></i> SAVE PAYMENT OUT
                    </button>
                </div>
            </form>
        </div>

        {{-- =========================================================================
           4. PARTY TO PARTY TRANSFER
           ========================================================================= --}}
        <div class="voucher-form-section" id="form-party_transfer">
            <form class="voucher-form" data-action="{{ route('store_party_transfer') }}" method="POST">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="pw-label">TRANSFER DATE <span class="text-danger">*</span></label>
                        <input type="date" name="transfer_date" class="form-control pw-input fw-bold" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="pw-label">VOUCHER ID</label>
                        <input type="text" class="form-control pw-input fw-bold bg-light" value="{{ $nextTvid ?? 'TVID-001' }}" readonly>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    {{-- Source Party --}}
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-4 border">
                            <h6 class="fw-bold text-danger mb-3"><i class="fas fa-minus-circle me-1"></i> Source Party (Deduct From)</h6>
                            <div class="mb-3">
                                <label class="pw-label">PARTY TYPE</label>
                                <div class="d-flex gap-3">
                                    <label><input type="radio" name="source_party_type_choice" class="pt-src-party-type" value="customer" checked> Customer</label>
                                    <label><input type="radio" name="source_party_type_choice" class="pt-src-party-type" value="vendor"> Vendor</label>
                                </div>
                                <input type="hidden" name="source_party_type" id="pt_source_party_type" value="customer">
                            </div>
                            <div id="pt_src_customer_wrapper">
                                <select name="source_party_id" id="pt_src_customer_select" class="form-select select2-customer" required>
                                    <option value="">Select source customer...</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="pt_src_vendor_wrapper" style="display:none;">
                                <select name="source_party_id_vendor" id="pt_src_vendor_select" class="form-select select2-vendor" disabled>
                                    <option value="">Select source vendor...</option>
                                    @foreach($vendors as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Destination Party --}}
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-4 border">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-plus-circle me-1"></i> Destination Party (Transfer To)</h6>
                            <div class="mb-3">
                                <label class="pw-label">PARTY TYPE</label>
                                <div class="d-flex gap-3">
                                    <label><input type="radio" name="destination_party_type_choice" class="pt-dst-party-type" value="vendor" checked> Vendor</label>
                                    <label><input type="radio" name="destination_party_type_choice" class="pt-dst-party-type" value="customer"> Customer</label>
                                </div>
                                <input type="hidden" name="destination_party_type" id="pt_destination_party_type" value="vendor">
                            </div>
                            <div id="pt_dst_vendor_wrapper">
                                <select name="destination_party_id" id="pt_dst_vendor_select" class="form-select select2-vendor" required>
                                    <option value="">Select destination vendor...</option>
                                    @foreach($vendors as $v)
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="pt_dst_customer_wrapper" style="display:none;">
                                <select name="destination_party_id_customer" id="pt_dst_customer_select" class="form-select select2-customer" disabled>
                                    <option value="">Select destination customer...</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="pw-label">TRANSFER AMOUNT (PKR) <span class="text-danger">*</span></label>
                        <input type="number" step="any" min="0.01" name="amount" id="pt_amount" class="form-control pw-input fw-bold text-end" placeholder="0.00" required>
                    </div>
                    <div class="col-md-8">
                        <label class="pw-label">REMARKS</label>
                        <input type="text" name="remarks" class="form-control pw-input" placeholder="Transfer notes">
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold text-white shadow" style="border-radius: 10px;">
                        <i class="fas fa-check-circle me-1"></i> PROCESS PARTY TRANSFER
                    </button>
                </div>
            </form>
        </div>

        {{-- =========================================================================
           5. INTERNAL TRANSFER (CONTRA CASH ↔ BANK)
           ========================================================================= --}}
        <div class="voucher-form-section" id="form-internal_transfer">
            <form class="voucher-form" data-action="{{ route('store_internal_transfer') }}" method="POST">
                @csrf
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="pw-label">VOUCHER ID</label>
                        <input type="text" class="form-control pw-input fw-bold bg-light" value="{{ $nextItvid ?? 'ITV-Auto' }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="pw-label">TRANSFER DATE <span class="text-danger">*</span></label>
                        <input type="date" name="transfer_date" class="form-control pw-input fw-bold" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    {{-- From Account --}}
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-4 border">
                            <h6 class="fw-bold text-danger mb-3"><i class="fas fa-minus-circle me-1"></i> From Account (Source / Deduct From)</h6>
                            <select name="from_account_id" id="it_from_account" class="form-select select2-account" required>
                                <option value="">Select source cash/bank account...</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->title }} ({{ $acc->account_code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- To Account --}}
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-4 border">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-plus-circle me-1"></i> To Account (Destination / Deposit To)</h6>
                            <select name="to_account_id" id="it_to_account" class="form-select select2-account" required>
                                <option value="">Select destination cash/bank account...</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->title }} ({{ $acc->account_code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="pw-label">TRANSFER AMOUNT (PKR) <span class="text-danger">*</span></label>
                        <input type="number" step="any" min="0.01" name="amount" id="it_amount" class="form-control pw-input fw-bold text-end fs-5" placeholder="0.00" required>
                    </div>
                    <div class="col-md-8">
                        <label class="pw-label">REMARKS / DESCRIPTION</label>
                        <input type="text" name="remarks" class="form-control pw-input" placeholder="e.g. Cash deposit to bank account">
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold text-white shadow" style="border-radius: 10px;">
                        <i class="fas fa-check-circle me-1"></i> PROCESS INTERNAL TRANSFER
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

{{-- MODAL: ADD NEW EXPENSE CATEGORY --}}
<div class="modal fade" id="expenseCategoryModal" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered">
        <form id="addExpenseCategoryForm">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold mb-0"><i class="fas fa-folder-plus text-primary me-2"></i>New Expense Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <label class="pw-label mb-2">CATEGORY NAME <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control pw-input fw-bold" placeholder="e.g. Office Stationery, Fuel, Internet" required>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold"><i class="fas fa-check me-1"></i> Save Category</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Voucher Type Toggle
    $('#voucherTypeSelector .voucher-type-pill').on('click', function() {
        const type = $(this).attr('data-type');
        $('#voucherTypeSelector .voucher-type-pill').removeClass('active');
        $(this).addClass('active');

        $('.voucher-form-section').removeClass('active');
        $('#form-' + type).addClass('active');
        initSelect2();
    });

    function initSelect2() {
        $('.voucher-form-section.active .select2-account, .voucher-form-section.active .select2-vendor, .voucher-form-section.active .select2-customer, .voucher-form-section.active .select2-cat').select2({
            width: '100%'
        });
    }

    // 2. Primary Head Toggle: Cash vs Bank Filter
    $('#expHeadTypeToggle .head-btn').on('click', function() {
        const headType = $(this).attr('data-head');
        $('#expHeadTypeToggle .head-btn').removeClass('active-cash active-bank');
        if (headType === 'cash') {
            $(this).addClass('active-cash');
        } else {
            $(this).addClass('active-bank');
        }

        const select = $('#expSourceAccountSelect');
        select.find('option').each(function() {
            const isBank = $(this).attr('data-is-bank') === '1';
            if (!$(this).val()) return;

            if (headType === 'cash') {
                if (!isBank) $(this).prop('disabled', false).show();
                else $(this).prop('disabled', true).hide();
            } else {
                if (isBank) $(this).prop('disabled', false).show();
                else $(this).prop('disabled', true).hide();
            }
        });

        // Trigger change on first enabled option
        const firstVal = select.find('option:enabled:not([value=""])').first().val();
        if (firstVal) {
            select.val(firstVal).trigger('change');
        }
    });

    // 3. Source Account Details Auto-Populate
    $(document).on('change', '#expSourceAccountSelect', function() {
        const opt = $(this).find('option:selected');
        const code = opt.attr('data-code') || '-';
        const bal = parseFloat(opt.attr('data-balance')) || 0;
        $('#expCodeDisplay').text(code);
        $('#expBalanceDisplay').text(bal.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' PKR');
    });

    // 4. Excel Keyboard Navigation: Press ENTER anywhere on row input/select to add new row automatically
    $(document).on('keydown', '#expenseTable input, #expenseTable select', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const currentTr = $(this).closest('tr');
            const isLast = currentTr.is(':last-child');
            if (isLast) {
                $('.add-expense-row').trigger('click');
                setTimeout(() => {
                    const newTr = $('#expenseTable tbody tr').last();
                    newTr.find('.select2-cat').focus().select2('open');
                }, 60);
            } else {
                const nextTr = currentTr.next('tr');
                nextTr.find('select, input').first().focus();
            }
        }
    });

    // 5. Add Dynamic Expense Table Row
    $('.add-expense-row').on('click', function() {
        const idx = $('#expenseTable tbody tr').length + 1;
        const options = $('#expenseTable tbody tr:first select.rowAccountSub').html();

        const rowHtml = `
            <tr>
                <td class="text-center fw-bold text-muted item-row-idx">${idx}</td>
                <td>
                    <select name="row_account_id[]" class="form-select rowAccountSub select2-cat" required>
                        ${options}
                    </select>
                    <input type="hidden" name="narration_id[]" value="">
                </td>
                <td>
                    <input type="text" name="narration_text[]" class="form-control pw-input row-narration" placeholder="Details e.g. Tea for guests / Generator Fuel" value="Expense">
                </td>
                <td>
                    <input type="number" step="any" min="0.01" name="amount[]" class="form-control pw-input text-end exp-amount fw-bold" placeholder="0.00" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle removeRow" title="Delete Row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#expenseTable tbody').append(rowHtml);
        initSelect2();
        updateRowIndexes();
    });

    $(document).on('click', '.removeRow', function() {
        if ($('#expenseTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
            updateRowIndexes();
            calcExpenseTotal();
        } else {
            Swal.fire({icon: 'warning', title: 'Row Required', text: 'At least one account row is required.'});
        }
    });

    function updateRowIndexes() {
        $('#expenseTable tbody tr').each(function(i) {
            $(this).find('.item-row-idx').text(i + 1);
        });
    }

    function calcExpenseTotal() {
        let total = 0;
        $('#expenseTable .exp-amount').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#expenseTotal').text(total.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' PKR');
        $('#expenseTotalInput').val(total.toFixed(2));
    }

    $(document).on('input', '#expenseTable .exp-amount', calcExpenseTotal);

    // 6. Add Expense Category Modal Trigger
    $('.btn-open-cat-modal').on('click', function() {
        $('#expenseCategoryModal').modal('show');
    });

    $('#addExpenseCategoryForm').on('submit', function(e) {
        e.preventDefault();
        const name = $(this).find('input[name="name"]').val();

        $.ajax({
            url: "{{ route('expense_categories.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(resp) {
                const catId = resp.id || (resp.category ? resp.category.id : '');
                $('.rowAccountSub').append(`<option value="${catId}">${name}</option>`);
                $('#expenseCategoryModal').modal('hide');
                $('#addExpenseCategoryForm')[0].reset();
                Swal.fire({icon: 'success', title: 'Saved!', text: 'New category added!', timer: 1500, showConfirmButton: false});
            }
        });
    });

    // 7. Form Submission AJAX
    $('.voucher-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const action = form.attr('data-action');
        const formData = new FormData(this);

        Swal.fire({
            title: 'Saving Voucher...',
            text: 'Processing journal entry & updating balances',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: action,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(resp) {
                Swal.fire({
                    icon: 'success',
                    title: 'Voucher Saved Successfully!',
                    text: resp.message || 'Voucher posted successfully!',
                    showCancelButton: true,
                    confirmButtonText: 'Print Voucher',
                    cancelButtonText: 'Create New'
                }).then((res) => {
                    if (res.isConfirmed && (resp.print_url || resp.voucher_id)) {
                        window.open(resp.print_url || ('/print/' + resp.voucher_id), '_blank');
                    }
                    window.location.reload();
                });
            },
            error: function(xhr) {
                const msg = xhr.responseJSON ? (xhr.responseJSON.message || xhr.responseJSON.error || 'Failed to save voucher.') : 'Server error.';
                Swal.fire({icon: 'error', title: 'Save Failed', html: msg});
            }
        });
    });

    // Initialize Select2 & Defaults
    initSelect2();
    $('#expHeadTypeToggle .active-cash').trigger('click');
});
</script>

@endsection
