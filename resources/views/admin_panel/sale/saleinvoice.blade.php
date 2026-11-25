<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ($isQuotation ?? ($sale->sale_status === "quotation")) ? "Quotation" : (($isEstimate ?? false) ? "Estimate" : "Cash Memo / Invoice") }} - {{ $sale->invoice_no }}</title>

    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/font-awesome/css/all.min.css') }}" rel="stylesheet">

    <style>
        :root {
            --text-color: #111827;
            --border-color: #333333;
            --light-border: #999999;
            --header-bg: #f8fafc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background-color: #f1f5f9;
            color: var(--text-color);
            font-family: Arial, "Helvetica Neue", Helvetica, "Segoe UI", sans-serif;
            font-size: 10px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Top Action Buttons (Toolbar) */
        .print-btn-container {
            position: fixed;
            top: 15px;
            right: 20px;
            z-index: 1050;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .print-btn-container .btn {
            font-size: 12px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
        }

        .print-btn-container .btn:hover {
            transform: translateY(-1px);
        }

        /* A4 Cash Memo Container */
        .invoice-container {
            max-width: 297mm;
            min-height: 210mm;
            margin: 20px auto 40px auto;
            background: #ffffff;
            padding: 16px 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            position: relative;
        }

        /* Company Header */
        .company-header-block {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 4px;
        }

        .company-name-title {
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            color: #000;
        }

        .company-sub-tax {
            font-size: 11px;
            font-weight: 800;
            color: #000;
            margin-bottom: 2px;
        }

        .company-address-phone {
            font-size: 10px;
            font-weight: 700;
            color: #111;
        }

        /* 2-Column Meta Info Grid */
        .meta-info-grid {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-top: 10px;
            margin-bottom: 8px;
            font-size: 10px;
        }

        .meta-col-left {
            flex: 1 1 54%;
        }

        .meta-col-right {
            flex: 1 1 46%;
        }

        .meta-data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-data-table td {
            padding: 1.5px 0;
            vertical-align: top;
            font-size: 10px;
            line-height: 1.35;
            color: #000000 !important;
        }

        .meta-data-table td.lbl {
            font-weight: 900;
            color: #000000 !important;
            white-space: nowrap;
            width: 110px;
        }

        .meta-data-table td.col {
            width: 10px;
            font-weight: 900;
            text-align: center;
            color: #000000 !important;
        }

        .meta-data-table td.val {
            font-weight: 700;
            color: #000000 !important;
        }

        .meta-data-table td.val.customer-title {
            font-weight: 900;
            font-size: 11px;
            color: #000000 !important;
        }

        .doc-memo-title {
            font-size: 13.5px;
            font-weight: 900;
            text-align: center;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 6px;
            padding: 2px 0;
            color: #000;
        }

        /* Cash Memo Items Table */
        .cashmemo-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 6px;
            color: #000000;
        }

        .cashmemo-table th, 
        .cashmemo-table td {
            border: 1px solid #000000;
            padding: 3.5px 4px;
            line-height: 1.25;
            vertical-align: middle;
            color: #000000 !important;
        }

        .cashmemo-table thead th {
            font-weight: 900;
            font-size: 9.5px;
            text-align: center;
            background-color: #ffffff;
            color: #000000 !important;
            text-transform: capitalize;
            letter-spacing: 0.1px;
            border-bottom: 1.5px solid #000000;
        }

        .cashmemo-table thead tr:first-child th {
            border-bottom: 1px solid #000000;
        }

        .cashmemo-table tbody td {
            font-size: 10px;
            font-weight: 700;
            color: #000000 !important;
        }

        .cashmemo-table tbody td strong {
            font-size: 10.5px;
            font-weight: 800;
            color: #000000 !important;
        }

        .cashmemo-table .category-header-row td {
            display: none !important;
        }

        .cashmemo-table tfoot td {
            font-weight: 900;
            font-size: 10px;
            border-top: 1.5px solid #000000;
            border-bottom: 2px solid #000000;
            background-color: #ffffff;
            color: #000000 !important;
        }

        .text-start { text-align: left !important; }
        .text-center { text-align: center !important; }
        .text-end { text-align: right !important; font-variant-numeric: tabular-nums; }

        /* Summary & Signatures Section */
        .memo-bottom-container {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
        }

        .summary-box-table {
            border-collapse: collapse;
            font-size: 10px;
            width: 320px;
            color: #000000;
        }

        .summary-box-table td {
            padding: 1.5px 4px;
            vertical-align: middle;
        }

        .summary-box-table td.sum-lbl {
            font-weight: 800;
            text-align: right;
            padding-right: 8px;
            color: #000000 !important;
            white-space: nowrap;
        }

        .summary-box-table td.sum-val {
            border: 1px solid #000000;
            text-align: right;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            width: 105px;
            padding: 2px 6px;
            background: #fff;
            color: #000000 !important;
        }

        .summary-box-table tr.net-invoice-row td.sum-val {
            font-size: 11.5px;
            font-weight: 900;
            border: 1.5px solid #000000;
            color: #000000 !important;
        }

        /* Signatures Section */
        .signatures-section {
            margin-top: 24px;
            margin-bottom: 10px;
        }

        .load-form-title {
            font-size: 9.5px;
            font-weight: 800;
            color: #000;
            margin-bottom: 12px;
        }

        .signatures-grid {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            gap: 20px;
            padding: 0 20px;
        }

        .sig-box-wrap {
            text-align: center;
            width: 200px;
        }

        .sig-person-name {
            font-size: 9.5px;
            font-weight: 800;
            color: #000;
            margin-bottom: 4px;
            min-height: 14px;
        }

        .sig-box {
            border: 1px solid #000;
            padding: 3px 8px;
            font-size: 9.5px;
            font-weight: 800;
            text-align: center;
            color: #000;
            background: #fff;
        }

        /* Thermal receipt container (hidden on normal screens & A4) */
        .receipt-container {
            display: none;
        }

        /* Print Styling */
        @media print {
            @page {
                size: A4 landscape !important;
                margin: 5mm 6mm !important;
            }

            body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 10px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .print-btn-container,
            .no-print {
                display: none !important;
            }

            .invoice-container {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }

            .receipt-container {
                display: none !important;
            }

            body.print-letterhead-mode .company-header-block {
                display: none !important;
            }

            /* Receipt Mode Overrides */
            body.print-receipt-mode .invoice-container {
                display: none !important;
            }

            body.print-receipt-mode .receipt-container {
                display: block !important;
                box-shadow: none !important;
                border: none !important;
                margin: 0 auto !important;
                padding: 3mm 2mm 5mm 2mm !important;
                width: 76mm !important;
                max-width: 76mm !important;
                background: #fff !important;
                color: #000 !important;
                font-size: 11px !important;
                line-height: 1.3 !important;
            }
        }
    </style>
</head>

<body>

@php
    $relCust = $sale->customer_relation;
    $customerName = $sale->walkin_name ?? ($relCust->customer_name ?? 'Walking Customer');
    $customerCode = $relCust->customer_id ?? 'WALK';
    $customerFilerStatus = $relCust ? ($relCust->is_filer ? 'Filer' : 'Non-Filer') : 'Non-Filer';
    $customerAddress = $relCust->address ?? '—';
    $channelType = $relCust->customer_type ?? 'Wholesale';
    $channelName = $relCust->party_type ? ucwords($relCust->party_type) : 'Wholesale';
    $subChannelName = $relCust->area ?? ($relCust->city ?? 'Scattered Market');
    $customerMobile = $sale->walkin_mobile ?? ($relCust->mobile ?? ($relCust->phone ?? '—'));
    $customerStrn = $relCust ? ($relCust->strn_number ?? $relCust->getCustomValue('strn_number')) : null;
    $customerNtn = $relCust ? ($relCust->ntn_number ?? $relCust->getCustomValue('ntn_number')) : null;
    $customerCnic = $relCust->cnic ?? null;
    $isCustomerRegistered = !empty(trim((string)$customerStrn)) || (bool)($relCust->is_registered ?? false);

    $companyName = \App\Models\Setting::get('company_name', config('app.name', 'Jamal & Brothers (Mardan)'));
    $compNtn = \App\Models\Setting::get('company_ntn', '50786976');
    $compStrn = \App\Models\Setting::get('company_strn', '3277876187095');
    $compAddress = \App\Models\Setting::get('company_address', 'Haji Muneem tobacco Mandi, Opp National Aitemad bank Par Hoti Mardan');
    $compPhone = \App\Models\Setting::get('company_phone', '0348-8147956');

    $orderBookerName = $sale->orderBooker->name ?? ($sale->salesOfficer->name ?? null);
    $orderBookerCode = $sale->orderBooker->code ?? ($sale->salesOfficer->code ?? ($sale->order_booker_id ?? 'OB1'));
    $salesmanName = $sale->salesman->name ?? null;
    $salesmanCode = $sale->salesman->code ?? ($sale->salesman_id ?? 'SM1');

    $bookingDate = $sale->created_at ? $sale->created_at->format('d F Y / l') : date('d F Y / l');
    $invoiceDate = $sale->created_at ? $sale->created_at->format('d F Y / l') : date('d F Y / l');
    $deliveryDate = $sale->due_date ? \Carbon\Carbon::parse($sale->due_date)->format('d F Y / l') : ($sale->created_at ? $sale->created_at->addDays(2)->format('d F Y / l') : date('d F Y / l'));
    $routeName = $relCust->zone ?? ($relCust->area ?? ($orderBookerName ? $orderBookerName . ' Route' : 'Main Market Route'));

    // Calculations
    $calcGross = 0;
    $calcSpDisc = 0;
    $calcRegDisc = 0;
    $calcTradeOffer = 0;
    $calcTradeActivity = 0;
    $calcSalesTax = 0;
    $calcMrpTax = 0;
    $calcFurtherTax = 0;
    $calcAdvTax = 0;
    $calcNetTotal = 0;
    $calcTotalPcs = 0;

    // Group items by Category
    $groupedItems = [];
    foreach ($saleItems as $it) {
        $cat = !empty($it['category_name']) ? trim($it['category_name']) : 'PRODUCTS';
        $groupedItems[$cat][] = $it;

        $itPcs = (float)($it['total_pieces'] ?? ($it['qty'] ?? 0));
        $itGross = (float)($it['gross_amount'] ?? 0);
        $itPrice = (float)($it['price'] ?? 0);
        if ($itGross <= 0 && $itPcs > 0 && $itPrice > 0) {
            $itGross = $itPcs * $itPrice;
        } elseif ($itPcs > 0 && $itGross > 0) {
            $itPrice = round($itGross / $itPcs, 2);
        }

        $calcTotalPcs += $itPcs;
        $calcGross += $itGross;
        $calcSpDisc += (float)($it['sp_disc_amount'] ?? 0);
        $calcRegDisc += (float)($it['reg_disc_amount'] ?? 0);
        $calcTradeOffer += (float)($it['trade_offer'] ?? 0);
        $calcTradeActivity += (float)($it['trade_activity'] ?? 0);
        $calcSalesTax += (float)($it['sales_tax_amount'] ?? 0);
        $calcMrpTax += (float)($it['mrp_amount'] ?? 0);
        $calcFurtherTax += $isCustomerRegistered ? 0.0 : (float)($it['further_tax_amount'] ?? 0);
        $calcAdvTax += (float)($it['adv_tax_amount'] ?? 0);
        $calcNetTotal += (float)($it['total'] ?? 0);
    }

    $calcTotalDisc = $calcSpDisc + $calcRegDisc + $calcTradeOffer + $calcTradeActivity;
@endphp

    <!-- Print / Action Toolbar -->
    <div class="print-btn-container no-print">
        <button onclick="printCashMemoA4()" class="btn btn-success" title="Print Cash Memo (A4 Landscape)">
            <i class="fas fa-print"></i> Print Cash Memo
        </button>
        <button onclick="printReceipt()" class="btn btn-primary" title="Print 80mm Thermal Receipt">
            <i class="fas fa-receipt"></i> Print Thermal
        </button>
        <a href="javascript:void(0)" onclick="handleGoBack()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <!-- Screen & A4 View -->
    <div class="invoice-container">

        <!-- 1. Header Section -->
        <div class="company-header-block">
            <div class="company-name-title">{{ $companyName }}</div>
            <div class="company-sub-tax">NTN # {{ $compNtn }}, STRN #, {{ $compStrn }}</div>
            <div class="company-address-phone">Address: {{ $compAddress }} Cell: {{ $compPhone }}</div>
        </div>

        <!-- 2. Metadata Info Grid -->
        <div class="meta-info-grid">
            <!-- Left: Customer Information -->
            <div class="meta-col-left">
                <table class="meta-data-table">
                    <tr>
                        <td class="lbl">M/S :</td>
                        <td class="col"></td>
                        <td class="val customer-title">{{ strtoupper($customerName) }} [ {{ $customerCode }} ]</td>
                    </tr>
                    <tr>
                        <td class="lbl">Income Tax Status :</td>
                        <td class="col"></td>
                        <td class="val">{{ $customerFilerStatus }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Address:</td>
                        <td class="col"></td>
                        <td class="val">{{ $customerAddress }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Channel Type:</td>
                        <td class="col"></td>
                        <td class="val">{{ $channelType }}</td>
                    </tr>

                    <tr>
                        <td class="lbl">Sub Channel Name:</td>
                        <td class="col"></td>
                        <td class="val">{{ $subChannelName }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Contact No:</td>
                        <td class="col"></td>
                        <td class="val">{{ $customerMobile }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Sales Tax No:</td>
                        <td class="col"></td>
                        <td class="val">{{ $customerStrn ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">NTN No:</td>
                        <td class="col"></td>
                        <td class="val">{{ $customerNtn ?: '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">CNIC No:</td>
                        <td class="col"></td>
                        <td class="val">{{ $customerCnic ?: '—' }}</td>
                    </tr>
                </table>
            </div>

            <!-- Right: Invoice & Delivery Details -->
            <div class="meta-col-right">
                <table class="meta-data-table">
                    <tr>
                        <td class="lbl">Invoice No # :</td>
                        <td class="col"></td>
                        <td class="val"><strong>{{ $sale->invoice_no }}</strong></td>
                    </tr>
                    <tr>
                        <td class="lbl">Order Booker :</td>
                        <td class="col"></td>
                        <td class="val">{{ $orderBookerName ? $orderBookerName . ' [ ' . $orderBookerCode . ' ]' : '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Delivery Man :</td>
                        <td class="col"></td>
                        <td class="val">{{ $salesmanName ? $salesmanName . ' [ ' . $salesmanCode . ' ]' : '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Booking Date/Day :</td>
                        <td class="col"></td>
                        <td class="val">{{ $bookingDate }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Invoice Date/Day :</td>
                        <td class="col"></td>
                        <td class="val">{{ $invoiceDate }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Delivery Date/Day :</td>
                        <td class="col"></td>
                        <td class="val">{{ $deliveryDate }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Route :</td>
                        <td class="col"></td>
                        <td class="val">{{ $routeName }}</td>
                    </tr>
                </table>

                <div class="doc-memo-title">
                    @if($sale->sale_status === 'quotation' || ($isQuotation ?? false))
                        QUOTATION
                    @elseif($isEstimate ?? false)
                        ESTIMATE
                    @else
                        CASH MEMO / INVOICE
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Items Table -->
        <table class="cashmemo-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 25%; text-align: left;">Item / Product Name</th>
                    <th rowspan="2" style="width: 7%; text-align: center;">HS Code</th>
                    <th rowspan="2" style="width: 4%; text-align: center;">PCS</th>
                    <th rowspan="2" style="width: 7%; text-align: right;">Trade Price</th>
                    <th rowspan="2" style="width: 8%; text-align: right;">Gross Amount</th>
                    <th rowspan="2" style="width: 7%; text-align: right;">Special Discount</th>
                    <th rowspan="2" style="width: 7%; text-align: right;">Regular Discount</th>
                    <th rowspan="2" style="width: 6%; text-align: right;">Trade Offer</th>
                    <th rowspan="2" style="width: 6%; text-align: right;">Trade Activity</th>
                    <th colspan="3" style="width: 16%; text-align: center;">Tax</th>
                    <th rowspan="2" style="width: 8%; text-align: right;">Net Amount</th>
                    <th rowspan="2" style="width: 5%; text-align: center;">Tax Type</th>
                </tr>
                <tr>
                    <th style="width: 5.3%; text-align: right;">S.Tax</th>
                    <th style="width: 5.3%; text-align: right;">MRP</th>
                    <th style="width: 5.4%; text-align: right;">4%</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupedItems as $categoryName => $catItems)
                    @foreach ($catItems as $item)
                        @php
                            $itPcs = (float)($item['total_pieces'] ?? ($item['qty'] ?? 0));
                            $itGross = (float)($item['gross_amount'] ?? 0);
                            $itPrice = (float)($item['price'] ?? 0);
                            if ($itGross <= 0 && $itPcs > 0 && $itPrice > 0) {
                                $itGross = $itPcs * $itPrice;
                            } elseif ($itPcs > 0 && $itGross > 0) {
                                $itPrice = round($itGross / $itPcs, 2);
                            }
                            $itSpDisc = (float)($item['sp_disc_amount'] ?? 0);
                            $itRegDisc = (float)($item['reg_disc_amount'] ?? 0);
                            $itTradeOffer = (float)($item['trade_offer'] ?? 0);
                            $itTradeAct = (float)($item['trade_activity'] ?? 0);
                            $itSalesTax = (float)($item['sales_tax_amount'] ?? 0);
                            $itMrpTax = (float)($item['mrp_amount'] ?? 0);
                            $itFurtherTax = $isCustomerRegistered ? 0.0 : (float)($item['further_tax_amount'] ?? 0);
                            $itNet = (float)($item['total'] ?? 0);
                            $rawTax = strtoupper(trim($item['tax_type'] ?? ''));
                            $isNonThird = str_contains($rawTax, 'NON THIRD') || str_contains($rawTax, 'NON-THIRD') || str_contains($rawTax, 'NON 3RD');
                            $isThird = !$isNonThird && (
                                str_contains($rawTax, 'THIRD SCHEDULE') 
                                || str_contains($rawTax, '3RD SCHEDULE') 
                                || $rawTax === 'MRP'
                                || $itMrpTax > 0
                            );
                            if ($isThird) {
                                $itTaxType = 'MRP';
                            } elseif (str_contains($rawTax, 'EXEMPT') || ($itSalesTax == 0 && $itMrpTax == 0)) {
                                $itTaxType = 'Exempt';
                            } else {
                                $itTaxType = 'Taxable';
                            }
                            $itHsCode = $item['hs_code'] ?? '—';
                            $dispItemName = !empty($item['item_name']) ? $item['item_name'] : ($item['product_name'] ?? 'Item');
                            $vName = trim($item['variant_name'] ?? '');
                            if (!empty($vName) && stripos($dispItemName, $vName) === false) {
                                $dispItemName .= ' - ' . $vName;
                            }
                        @endphp
                        <tr>
                            <td class="text-start">
                                <strong>{{ $dispItemName }}</strong>
                            </td>
                            <td class="text-center">{{ $itHsCode ?: '—' }}</td>
                            <td class="text-center">{{ (int)$itPcs }}</td>
                            <td class="text-end">{{ number_format($itPrice, 2) }}</td>
                            <td class="text-end">{{ number_format($itGross, 2) }}</td>
                            <td class="text-end">{{ number_format($itSpDisc, 2) }}</td>
                            <td class="text-end">{{ number_format($itRegDisc, 2) }}</td>
                            <td class="text-end">{{ number_format($itTradeOffer, 2) }}</td>
                            <td class="text-end">{{ number_format($itTradeAct, 2) }}</td>
                            <td class="text-end">{{ number_format($itSalesTax, 2) }}</td>
                            <td class="text-end">{{ number_format($itMrpTax, 2) }}</td>
                            <td class="text-end">{{ number_format($itFurtherTax, 2) }}</td>
                            <td class="text-end"><strong>{{ number_format($itNet, 2) }}</strong></td>
                            <td class="text-center">{{ $itTaxType }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-center fw-bold">Total</td>
                    <td></td>
                    <td class="text-center fw-bold">{{ (int)$calcTotalPcs }}</td>
                    <td></td>
                    <td class="text-end fw-bold">{{ number_format($calcGross, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($calcSpDisc, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($calcRegDisc, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($calcTradeOffer, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($calcTradeActivity, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($calcSalesTax, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($calcMrpTax, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($calcFurtherTax, 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($calcNetTotal, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- 4. Summary Box (Bottom Right) -->
        <div class="memo-bottom-container">
            <table class="summary-box-table">
                <tr>
                    <td class="sum-lbl">Gross Amount:</td>
                    <td class="sum-val">{{ number_format($calcGross, 2) }}</td>
                </tr>
                <tr>
                    <td class="sum-lbl">Total Discount:</td>
                    <td class="sum-val">{{ number_format($calcTotalDisc, 2) }}</td>
                </tr>
                <tr>
                    <td class="sum-lbl">Special Discount:</td>
                    <td class="sum-val">{{ number_format($calcSpDisc, 2) }}</td>
                </tr>
                <tr>
                    <td class="sum-lbl">Regular Discount:</td>
                    <td class="sum-val">{{ number_format($calcRegDisc, 2) }}</td>
                </tr>
                <tr>
                    <td class="sum-lbl">Trade Offer:</td>
                    <td class="sum-val">{{ number_format($calcTradeOffer, 2) }}</td>
                </tr>
                <tr>
                    <td class="sum-lbl">Trade Activity:</td>
                    <td class="sum-val">{{ number_format($calcTradeActivity, 2) }}</td>
                </tr>
                <tr>
                    <td class="sum-lbl">Add Sales Tax:</td>
                    <td class="sum-val">{{ number_format($calcSalesTax + $calcMrpTax, 2) }}</td>
                </tr>
                <tr>
                    <td class="sum-lbl">Additional Sales Tax On Un-Registered Shop:</td>
                    <td class="sum-val">{{ number_format($calcFurtherTax, 2) }}</td>
                </tr>
                <tr>
                    <td class="sum-lbl">Advance Income Tax 236H:</td>
                    <td class="sum-val">{{ number_format($calcAdvTax, 2) }}</td>
                </tr>
                <tr>
                    <td class="sum-lbl">Trading Terms:</td>
                    <td class="sum-val">{{ number_format((float)($sale->freight_charges ?? 0), 2) }}</td>
                </tr>
                <tr class="net-invoice-row">
                    <td class="sum-lbl"><strong>Net Invoice Amount:</strong></td>
                    <td class="sum-val"><strong>{{ number_format(round($sale->total_net)) }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- 5. Signatures Section -->
        <div class="signatures-section">
            <div class="signatures-grid">
                <div class="sig-box-wrap">
                    <div class="sig-person-name">{{ $orderBookerName ?: ' ' }}</div>
                    <div class="sig-box">Checked By Order Booker</div>
                </div>
                <div class="sig-box-wrap">
                    <div class="sig-person-name">{{ $salesmanName ?: ' ' }}</div>
                    <div class="sig-box">Delivered By</div>
                </div>
                <div class="sig-box-wrap">
                    <div class="sig-person-name">&nbsp;</div>
                    <div class="sig-box">Shop Keeper</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Thermal Receipt Mode (80mm) -->
    <div class="receipt-container">
        <div style="text-align: center; font-weight: bold; font-size: 14px; text-transform: uppercase;">
            {{ $companyName }}
        </div>
        <div style="text-align: center; font-size: 10px; margin-bottom: 6px;">
            NTN: {{ $compNtn }} | STRN: {{ $compStrn }}<br>
            {{ $compAddress }}<br>
            Phone: {{ $compPhone }}
        </div>
        <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 4px 0; margin-bottom: 6px; font-size: 10px;">
            <div><strong>Inv #:</strong> {{ $sale->invoice_no }}</div>
            <div><strong>Date:</strong> {{ $invoiceDate }}</div>
            <div><strong>Customer:</strong> {{ $customerName }}</div>
        </div>
        <table style="width: 100%; font-size: 9.5px; border-collapse: collapse; margin-bottom: 6px;">
            <thead>
                <tr style="border-bottom: 1px solid #000;">
                    <th style="text-align: left;">Item</th>
                    <th style="text-align: center;">PCS</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($saleItems as $it)
                    <tr>
                        <td style="text-align: left;">{{ $it['item_name'] }}</td>
                        <td style="text-align: center;">{{ (int)($it['total_pieces'] ?? $it['qty']) }}</td>
                        <td style="text-align: right;">{{ number_format($it['total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="border-top: 1px dashed #000; padding-top: 4px; font-size: 10px;">
            <div style="display: flex; justify-content: space-between;">
                <span>Gross:</span>
                <span>Rs. {{ number_format($calcGross, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Discount:</span>
                <span>Rs. {{ number_format($calcTotalDisc, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Sales Tax:</span>
                <span>Rs. {{ number_format($calcSalesTax + $calcMrpTax, 2) }}</span>
            </div>
            @if($calcFurtherTax > 0)
                <div style="display: flex; justify-content: space-between;">
                    <span>Further Tax (4%):</span>
                    <span>Rs. {{ number_format($calcFurtherTax, 2) }}</span>
                </div>
            @endif
            @if($calcAdvTax > 0)
                <div style="display: flex; justify-content: space-between;">
                    <span>Advance Tax:</span>
                    <span>Rs. {{ number_format($calcAdvTax, 2) }}</span>
                </div>
            @endif
            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 12px; border-top: 1px solid #000; margin-top: 4px; padding-top: 2px;">
                <span>Net Total:</span>
                <span>Rs. {{ number_format($sale->total_net, 2) }}</span>
            </div>
        </div>
    </div>

    <script>
        function printCashMemoA4() {
            document.body.classList.remove('print-receipt-mode');
            window.print();
        }

        function printReceipt() {
            document.body.classList.add('print-receipt-mode');
            window.print();
            setTimeout(() => {
                document.body.classList.remove('print-receipt-mode');
            }, 1000);
        }

        function handleGoBack() {
            if (window.opener && !window.opener.closed) {
                window.close();
                setTimeout(function() {
                    window.location.href = "{{ route('sale.index') }}";
                }, 150);
                return;
            }

            if (window.history.length > 1 && document.referrer && document.referrer.indexOf(window.location.host) !== -1) {
                window.history.back();
                return;
            }

            window.location.href = "{{ route('sale.index') }}";
        }
    </script>
</body>

</html>
