<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Tax Invoice - {{ $purchase->invoice_no }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendors/font-awesome/css/all.min.css') }}" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background-color: #f1f5f9;
            color: #000000;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 9px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Action bar */
        .action-bar {
            background: #ffffff;
            border-bottom: 1px solid #cbd5e1;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .action-bar .btn {
            font-size: 12px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 6px;
        }

        /* Invoice Sheet (Landscape A4) */
        .invoice-sheet {
            max-width: 297mm;
            min-height: 210mm;
            margin: 15px auto 30px auto;
            background: #ffffff;
            padding: 18px 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            position: relative;
        }

        /* Header Layout */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .header-qr {
            width: 85px;
            text-align: left;
        }

        .header-center-info {
            flex: 1;
            text-align: center;
            padding: 0 10px;
        }

        .vendor-title {
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            color: #000;
        }

        .vendor-ntn {
            font-size: 9.5px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .vendor-address {
            font-size: 8.5px;
            line-height: 1.3;
            color: #111;
        }

        .header-right {
            width: 140px;
            text-align: right;
        }

        .page-inv-info {
            font-size: 8.5px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        /* FBR Digital Invoicing Badge */
        .fbr-badge-box {
            display: inline-block;
            border: 1.5px solid #005691;
            border-radius: 6px;
            overflow: hidden;
            text-align: center;
            width: 105px;
            background: #fff;
        }

        .fbr-top-text {
            color: #005691;
            font-weight: 900;
            font-size: 11px;
            letter-spacing: 1px;
            padding: 2px 0 0 0;
            line-height: 1;
        }

        .fbr-bottom-bar {
            background-color: #005691;
            color: #ffffff;
            font-size: 8px;
            font-weight: 900;
            padding: 3px 2px;
            text-transform: uppercase;
            line-height: 1.1;
        }

        /* Main Document Title */
        .doc-title-row {
            text-align: center;
            margin: 8px 0 2px 0;
            position: relative;
        }

        .sales-tax-title {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
            display: inline-block;
        }

        .copy-type-lbl {
            position: absolute;
            right: 0;
            bottom: 0;
            font-size: 9px;
            font-weight: 700;
        }

        /* 3-Column Metadata Table */
        .meta-section {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-top: 6px;
            margin-bottom: 8px;
        }

        .meta-section td {
            vertical-align: top;
            padding: 1.5px 6px;
            line-height: 1.35;
        }

        .meta-col-1 { width: 33%; }
        .meta-col-2 { width: 33%; }
        .meta-col-3 { width: 34%; }

        .meta-lbl {
            font-weight: 800;
            display: inline-block;
            min-width: 95px;
        }

        .meta-val {
            font-weight: 700;
        }

        /* FBR Barcode Simulation */
        .barcode-box {
            margin: 4px 0;
            font-family: 'Courier New', Courier, monospace;
            font-size: 9px;
            letter-spacing: -1px;
            height: 22px;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        /* Items Table */
        .table-items-wrapper {
            margin-top: 6px;
        }

        .schedule-header {
            font-weight: 900;
            font-size: 9px;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        .fbr-items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            color: #000;
        }

        .fbr-items-table th, 
        .fbr-items-table td {
            border-top: 1px solid #000000;
            border-bottom: 1px solid #000000;
            padding: 3px 3px;
            vertical-align: middle;
            line-height: 1.25;
        }

        .fbr-items-table thead th {
            font-weight: 800;
            text-align: center;
            background: #ffffff;
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
        }

        .fbr-items-table tbody td {
            border-top: none;
            border-bottom: none;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .fbr-items-table tbody tr:first-child td {
            padding-top: 4px;
        }

        .fbr-items-table tbody tr:last-child td {
            padding-bottom: 6px;
            border-bottom: 1.5px solid #000;
        }

        .fbr-items-table tfoot td {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            font-weight: 800;
            padding: 3px 3px;
        }

        .text-start { text-align: left !important; }
        .text-center { text-align: center !important; }
        .text-end { text-align: right !important; font-variant-numeric: tabular-nums; }

        /* Summary Calculations Rows */
        .summary-lines-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-top: 4px;
        }

        .summary-lines-table td {
            padding: 1.5px 4px;
            vertical-align: middle;
        }

        .summary-lines-table .sum-lbl-col {
            font-weight: 800;
            text-align: left;
        }

        .summary-lines-table .sum-val-col {
            width: 120px;
            text-align: right;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
        }

        .summary-lines-table .final-words-row td {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            font-weight: 900;
            font-size: 9px;
            padding: 3.5px 4px;
        }

        /* Footer Weight & Certificate Section */
        .footer-details-grid {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 10px;
            font-size: 8.5px;
        }

        .footer-weight-col {
            width: 160px;
        }

        .footer-weight-col table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-weight-col td {
            padding: 1px 0;
            font-weight: 800;
        }

        .footer-notes-col {
            flex: 1;
            text-align: center;
            padding: 0 20px;
            font-weight: 700;
            color: #111;
        }

        .footer-notes-col div {
            margin-bottom: 6px;
        }

        /* Print Media Overrides */
        @media print {
            @page {
                size: A4 landscape;
                margin: 5mm 6mm;
            }

            body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 8.5px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .action-bar, .no-print {
                display: none !important;
            }

            .invoice-sheet {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>

<body>

@php
    // Number to words helper for invoice
    if (!function_exists('purchaseNumberToWords')) {
        function purchaseNumberToWords($number) {
            $hyphen      = ' ';
            $conjunction = ' ';
            $separator   = ' ';
            $negative    = 'negative ';
            $decimal     = ' AND PAISAS ';
            $dictionary  = array(
                0                   => 'zero',
                1                   => 'one',
                2                   => 'two',
                3                   => 'three',
                4                   => 'four',
                5                   => 'five',
                6                   => 'six',
                7                   => 'seven',
                8                   => 'eight',
                9                   => 'nine',
                10                  => 'ten',
                11                  => 'eleven',
                12                  => 'twelve',
                13                  => 'thirteen',
                14                  => 'fourteen',
                15                  => 'fifteen',
                16                  => 'sixteen',
                17                  => 'seventeen',
                18                  => 'eighteen',
                19                  => 'nineteen',
                20                  => 'twenty',
                30                  => 'thirty',
                40                  => 'forty',
                50                  => 'fifty',
                60                  => 'sixty',
                70                  => 'seventy',
                80                  => 'eighty',
                90                  => 'ninety',
                100                 => 'hundred',
                1000                => 'thousand',
                1000000             => 'million',
                1000000000          => 'billion',
            );

            if (!is_numeric($number)) return '';
            if ($number < 0) return $negative . purchaseNumberToWords(abs($number));

            $string = $fraction = null;
            if (strpos((string)$number, '.') !== false) {
                list($number, $fraction) = explode('.', (string)$number);
            }

            switch (true) {
                case $number < 21:
                    $string = $dictionary[$number];
                    break;
                case $number < 100:
                    $tens   = ((int) ($number / 10)) * 10;
                    $units  = $number % 10;
                    $string = $dictionary[$tens];
                    if ($units) {
                        $string .= $hyphen . $dictionary[$units];
                    }
                    break;
                case $number < 1000:
                    $hundreds  = $number / 100;
                    $remainder = $number % 100;
                    $string = $dictionary[(int)$hundreds] . ' ' . $dictionary[100];
                    if ($remainder) {
                        $string .= $conjunction . purchaseNumberToWords($remainder);
                    }
                    break;
                default:
                    $baseUnit = pow(1000, floor(log($number, 1000)));
                    $numBaseUnits = (int) ($number / $baseUnit);
                    $remainder = $number % $baseUnit;
                    $string = purchaseNumberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                    if ($remainder) {
                        $string .= $remainder < 100 ? $conjunction : $separator;
                        $string .= purchaseNumberToWords($remainder);
                    }
                    break;
            }

            if (null !== $fraction && is_numeric($fraction) && (int)$fraction > 0) {
                $string .= $decimal;
                $string .= purchaseNumberToWords((int)substr($fraction, 0, 2));
            }

            return strtoupper($string . ' ONLY');
        }
    }

    $buyerName = \App\Models\Setting::get('company_name', 'Jamal & Brothers');
    $buyerAddress = \App\Models\Setting::get('company_address', 'Haji Muneeb Mandi Opp, NBP Bank - Mardan');
    $buyerCity = \App\Models\Setting::get('company_city', 'Mardan');
    $buyerPhone = \App\Models\Setting::get('company_phone', '021-0000000');
    $buyerCell = \App\Models\Setting::get('company_cell', '0315-4408440');
    $buyerCnic = \App\Models\Setting::get('company_cnic', '1610207207915');
    $buyerNtn = \App\Models\Setting::get('company_ntn', '50786976');
    $buyerStrn = \App\Models\Setting::get('company_strn', '3277876187095');

    $vendorName = $purchase->vendor->name ?? 'NATIONAL FOODS LIMITED';
    $vendorNtn = $purchase->vendor->ntn_number ?? '0816613-7';
    $vendorAddress = $purchase->vendor->address ?? 'Plot#346/347, Phase II, M-3, Industrial City, (M-3IC) Sahianwala Interchange, Faisalabad';
    $vendorPhone = $purchase->vendor->phone ?? '+92-21-38402022';
    $vendorCnic = $purchase->vendor->cnic ?? '—';
    $vendorStrn = $purchase->vendor->strn_number ?? '—';

    $purchaseDateFormatted = \Carbon\Carbon::parse($purchase->purchase_date)->format('d.m.Y');
    $invNumber = $purchase->invoice_no ?: ('PUR-' . str_pad($purchase->id, 3, '0', STR_PAD_LEFT));
    $deliveryNo = $purchase->purchase_order_no ?: ('80480' . str_pad($purchase->id, 3, '0', STR_PAD_LEFT));
    $shipmentDoc = $purchase->id + 80230;
    $plantWarehouse = $purchase->warehouse->warehouse_name ?? 'Islamabad TCS Warehouse';
    $poNumber = $purchase->purchase_order_no ?: ($purchase->note ?: ('1000203.D0043-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT)));
    $fbrInvNo = '0816613DID9MAK' . str_pad($purchase->id, 7, '0', STR_PAD_LEFT);

    // Sums
    $totQty = 0;
    $totCartons = 0;
    $totRetailVal = 0;
    $totTradeVal = 0;
    $totDiscVal = 0;
    $totValAfterDisc = 0;
    $totStRetail = 0;
    $totStTrade = 0;
    $totLineTotal = 0;
    $totNetWeight = 0;
    $totGrossWeight = 0;

    foreach ($purchase->items as $it) {
        $ppb = (float)($it->pieces_per_box > 0 ? $it->pieces_per_box : ($it->product->pieces_per_box ?? 1));
        if ($ppb <= 0) $ppb = 1;

        $rawU = strtolower(trim($it->unit ?? ''));
        $isPiece = in_array($rawU, ['pcs', 'pc', 'piece']);
        $isCtn = in_array($rawU, ['carton', 'ctn', 'box', 'bandal', 'bundal', 'bndl']) || (!$isPiece && in_array($it->size_mode, ['by_cartons', 'by_bandal']));

        if ($isCtn) {
            if ($it->boxes_qty > 0 || $it->loose_qty > 0) {
                $b = (int) $it->boxes_qty;
                $l = (int) $it->loose_qty;
            } else {
                [$b, $l] = \App\Http\Controllers\PurchaseController::parseCartonQty($it->qty);
            }
            $tPieces = ($b * $ppb) + $l;
            $cCount = $b + ($l / $ppb);
        } elseif ($isPiece) {
            $tPieces = (float)$it->qty;
            $cCount = $ppb > 1 ? ($tPieces / $ppb) : 0;
        } else {
            $tPieces = (float)$it->qty;
            $cCount = $ppb > 1 ? ($tPieces / $ppb) : 0;
        }

        $rV = (float)($it->retail_value ?: (($it->retail_price ?: ($it->product->retail_price ?? 0)) * $tPieces));
        $tV = (float)($it->trade_value ?: ($it->price * (float)$it->qty));
        $dV = (float)($it->item_discount ?? 0);
        $vAd = (float)($it->value_after_discount ?: max(0, $tV - $dV));
        $sTr = (float)($it->sales_tax_on_retail ?? 0);
        $sTt = (float)($it->sales_tax_on_trade ?? 0);
        $lTot = (float)$it->line_total;

        $totQty += $tPieces;
        $totCartons += $cCount;
        $totRetailVal += $rV;
        $totTradeVal += $tV;
        $totDiscVal += $dV;
        $totValAfterDisc += $vAd;
        $totStRetail += $sTr;
        $totStTrade += $sTt;
        $totLineTotal += $lTot;

        $wPerPiece = (float)($it->product->weight_per_piece ?? 0);
        if ($wPerPiece > 0) {
            $netW = ($tPieces * $wPerPiece) / 1000;
            $totNetWeight += $netW;
            $totGrossWeight += ($netW * 1.05);
        }
    }

    if ($totNetWeight <= 0) {
        $totNetWeight = $totQty * 0.2;
        $totGrossWeight = $totNetWeight * 1.95;
    }

    $wordsAmount = purchaseNumberToWords($purchase->net_amount);
@endphp

    <!-- Top Sticky Action Bar (Hidden in Print) -->
    <div class="action-bar no-print">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark text-white fw-bold px-2 py-1" style="font-size: 0.8rem;">Invoice #{{ $purchase->invoice_no }}</span>
                <span class="text-muted small">Sales Tax Invoice (FBR Format)</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('purchase.edit', $purchase->id) }}" class="btn btn-warning btn-sm text-dark">
                    <i class="fas fa-edit me-1"></i> Edit Purchase
                </a>
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="fas fa-print me-1"></i> Print Invoice
                </button>
                <a href="{{ route('Purchase.home') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Main Invoice Sheet -->
    <div class="invoice-sheet">

        <!-- 1. Header Section -->
        <div class="header-container">
            <!-- Left: QR Code -->
            <div class="header-qr">
                <svg width="78" height="78" viewBox="0 0 100 100" fill="#000" xmlns="http://www.w3.org/2000/svg">
                    <!-- Clean Vector QR Code Representation -->
                    <rect width="100" height="100" fill="#fff" />
                    <!-- Top-Left Target -->
                    <rect x="5" y="5" width="30" height="30" fill="#000"/>
                    <rect x="10" y="10" width="20" height="20" fill="#fff"/>
                    <rect x="15" y="15" width="10" height="10" fill="#000"/>
                    <!-- Top-Right Target -->
                    <rect x="65" y="5" width="30" height="30" fill="#000"/>
                    <rect x="70" y="10" width="20" height="20" fill="#fff"/>
                    <rect x="75" y="15" width="10" height="10" fill="#000"/>
                    <!-- Bottom-Left Target -->
                    <rect x="5" y="65" width="30" height="30" fill="#000"/>
                    <rect x="10" y="70" width="20" height="20" fill="#fff"/>
                    <rect x="15" y="75" width="10" height="10" fill="#000"/>
                    <!-- Random QR Pattern Matrix Blocks -->
                    <rect x="42" y="8" width="6" height="6" fill="#000"/>
                    <rect x="52" y="8" width="6" height="6" fill="#000"/>
                    <rect x="42" y="20" width="6" height="12" fill="#000"/>
                    <rect x="52" y="26" width="6" height="6" fill="#000"/>
                    <rect x="10" y="42" width="6" height="6" fill="#000"/>
                    <rect x="22" y="42" width="12" height="6" fill="#000"/>
                    <rect x="8" y="52" width="12" height="6" fill="#000"/>
                    <rect x="26" y="52" width="6" height="6" fill="#000"/>
                    <rect x="42" y="42" width="6" height="6" fill="#000"/>
                    <rect x="52" y="42" width="12" height="6" fill="#000"/>
                    <rect x="70" y="42" width="6" height="12" fill="#000"/>
                    <rect x="82" y="42" width="10" height="6" fill="#000"/>
                    <rect x="42" y="54" width="12" height="6" fill="#000"/>
                    <rect x="60" y="54" width="6" height="6" fill="#000"/>
                    <rect x="82" y="54" width="10" height="6" fill="#000"/>
                    <rect x="42" y="66" width="6" height="12" fill="#000"/>
                    <rect x="54" y="72" width="12" height="6" fill="#000"/>
                    <rect x="72" y="66" width="6" height="6" fill="#000"/>
                    <rect x="84" y="66" width="8" height="12" fill="#000"/>
                    <rect x="42" y="84" width="12" height="8" fill="#000"/>
                    <rect x="60" y="84" width="18" height="8" fill="#000"/>
                    <rect x="84" y="84" width="8" height="8" fill="#000"/>
                </svg>
            </div>

            <!-- Center: Vendor Header Info -->
            <div class="header-center-info">
                <div class="vendor-title">{{ $vendorName }}</div>
                <div class="vendor-ntn">National Tax No. {{ $vendorNtn }}</div>
                <div class="vendor-address">
                    {{ $vendorAddress }}<br>
                    H.O: 12/CL-6 Claremont Road, Civil Lines, Karachi-75530 Tel: {{ $vendorPhone }}
                </div>
            </div>

            <!-- Right: Page & FBR Digital Badge -->
            <div class="header-right">
                <div class="page-inv-info">
                    Page &nbsp; 1 &nbsp;&nbsp;&nbsp; Invoice: {{ $invNumber }}
                </div>
                <div class="fbr-badge-box">
                    <div class="fbr-top-text">FBR</div>
                    <div class="fbr-bottom-bar">DIGITAL<br><span style="font-size: 6.5px; font-weight: normal; letter-spacing: 0.2px;">INVOICING SYSTEM</span></div>
                </div>
            </div>
        </div>

        <!-- 2. Main Title -->
        <div class="doc-title-row">
            <span class="sales-tax-title">S A L E S &nbsp; T A X &nbsp; I N V O I C E</span>
            <span class="copy-type-lbl">Customer Copy</span>
        </div>

        <!-- 3. Metadata Section (3 Columns) -->
        <table class="meta-section">
            <tr>
                <!-- Column 1: Buyer Info -->
                <td class="meta-col-1">
                    <div><span class="meta-lbl">FBR Invoice Number:</span> <span class="meta-val">{{ $fbrInvNo }}</span></div>
                    <div><span class="meta-lbl">Sold-to party (Buyer):</span> <span class="meta-val">1000203 - {{ $buyerName }}</span></div>
                    <div style="padding-left: 95px;" class="meta-val">{{ $buyerAddress }}</div>
                    <div><span class="meta-lbl">City:</span> <span class="meta-val">{{ $buyerCity }}</span></div>
                    <div><span class="meta-lbl">Street:</span> <span class="meta-val">{{ $buyerAddress }}</span></div>

                    <!-- Barcode Box -->
                    <div class="barcode-box">
                        <svg width="130" height="18" viewBox="0 0 130 18" xmlns="http://www.w3.org/2000/svg">
                            <rect x="0" y="0" width="2" height="18" fill="#000"/>
                            <rect x="4" y="0" width="1" height="18" fill="#000"/>
                            <rect x="7" y="0" width="3" height="18" fill="#000"/>
                            <rect x="12" y="0" width="2" height="18" fill="#000"/>
                            <rect x="16" y="0" width="1" height="18" fill="#000"/>
                            <rect x="19" y="0" width="4" height="18" fill="#000"/>
                            <rect x="25" y="0" width="1" height="18" fill="#000"/>
                            <rect x="28" y="0" width="2" height="18" fill="#000"/>
                            <rect x="32" y="0" width="3" height="18" fill="#000"/>
                            <rect x="37" y="0" width="1" height="18" fill="#000"/>
                            <rect x="40" y="0" width="4" height="18" fill="#000"/>
                            <rect x="46" y="0" width="2" height="18" fill="#000"/>
                            <rect x="50" y="0" width="1" height="18" fill="#000"/>
                            <rect x="53" y="0" width="3" height="18" fill="#000"/>
                            <rect x="58" y="0" width="2" height="18" fill="#000"/>
                            <rect x="62" y="0" width="4" height="18" fill="#000"/>
                            <rect x="68" y="0" width="1" height="18" fill="#000"/>
                            <rect x="71" y="0" width="2" height="18" fill="#000"/>
                            <rect x="75" y="0" width="3" height="18" fill="#000"/>
                            <rect x="80" y="0" width="1" height="18" fill="#000"/>
                            <rect x="83" y="0" width="3" height="18" fill="#000"/>
                            <rect x="88" y="0" width="2" height="18" fill="#000"/>
                            <rect x="92" y="0" width="4" height="18" fill="#000"/>
                            <rect x="98" y="0" width="1" height="18" fill="#000"/>
                            <rect x="101" y="0" width="3" height="18" fill="#000"/>
                            <rect x="106" y="0" width="2" height="18" fill="#000"/>
                            <rect x="110" y="0" width="1" height="18" fill="#000"/>
                            <rect x="113" y="0" width="4" height="18" fill="#000"/>
                            <rect x="119" y="0" width="2" height="18" fill="#000"/>
                            <rect x="123" y="0" width="1" height="18" fill="#000"/>
                            <rect x="126" y="0" width="3" height="18" fill="#000"/>
                        </svg>
                    </div>

                    <div><span class="meta-lbl">Telephone No:</span> <span class="meta-val">{{ $buyerPhone }}</span></div>
                    <div><span class="meta-lbl">Cell No:</span> <span class="meta-val">{{ $buyerCell }}</span></div>
                </td>

                <!-- Column 2: Ship-to Info -->
                <td class="meta-col-2">
                    <div><span class="meta-lbl">Ship-to party:</span> <span class="meta-val">1000203 - {{ $buyerName }}</span></div>
                    <div style="padding-left: 95px;" class="meta-val">{{ $plantWarehouse }} - {{ $buyerAddress }}</div>
                    <div><span class="meta-lbl">City:</span> <span class="meta-val">{{ $buyerCity }}</span></div>
                    <div><span class="meta-lbl">Street:</span> <span class="meta-val">{{ $buyerAddress }}</span></div>
                    <div style="height: 26px;"></div>
                    <div><span class="meta-lbl">Telephone (Ship):</span> <span class="meta-val">{{ $buyerPhone }}</span></div>
                    <div><span class="meta-lbl">Cell No (Ship):</span> <span class="meta-val">{{ $buyerCell }}</span></div>
                </td>

                <!-- Column 3: Invoice & Document Details -->
                <td class="meta-col-3">
                    <div><span class="meta-lbl">Invoice No/ Invoice date:</span> <span class="meta-val">{{ $invNumber }}/ {{ $purchaseDateFormatted }}</span></div>
                    <div><span class="meta-lbl">Delivery No/ Delivery date:</span> <span class="meta-val">{{ $deliveryNo }}/ {{ $purchaseDateFormatted }}</span></div>
                    <div><span class="meta-lbl">Shipment Doc.</span> <span class="meta-val">{{ $shipmentDoc }}</span></div>
                    <div><span class="meta-lbl">Plant:</span> <span class="meta-val">{{ $plantWarehouse }}</span></div>
                    <div><span class="meta-lbl">Purchase Order:</span> <span class="meta-val">{{ $poNumber }}</span></div>
                    <div><span class="meta-lbl">Sales Tax Reg. #</span> <span class="meta-val">{{ $vendorStrn }}</span></div>
                    <div><span class="meta-lbl">CNIC:</span> <span class="meta-val">{{ $buyerCnic }}</span></div>
                    <div><span class="meta-lbl">NTN No:</span> <span class="meta-val">{{ $buyerNtn }}</span></div>
                </td>
            </tr>
        </table>

        <!-- 4. Items Table Section -->
        <div class="table-items-wrapper">
            <div class="schedule-header">Third Schedule Items:</div>

            <table class="fbr-items-table">
                <thead>
                    <tr>
                        <th style="width: 24%; text-align: left;">Item Description</th>
                        <th style="width: 6%; text-align: center;">HS Code</th>
                        <th style="width: 5%; text-align: right;">Qty.</th>
                        <th style="width: 5%; text-align: right;">Del. Cart</th>
                        <th style="width: 9%; text-align: right;">Retail Value Exc. All Taxes</th>
                        <th style="width: 9%; text-align: right;">Trade Value Exc. All Taxes</th>
                        <th style="width: 5%; text-align: right;">Disc Rate (%)</th>
                        <th style="width: 7%; text-align: right;">Discount Value</th>
                        <th style="width: 8%; text-align: right;">Value After Discount</th>
                        <th style="width: 6%; text-align: right;">Sales Tax Rate (%)</th>
                        <th style="width: 8%; text-align: right;">Sales Tax on Retail Value</th>
                        <th style="width: 8%; text-align: right;">Sales Tax on Disct. Trade</th>
                        <th style="width: 8%; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->items as $item)
                        @php
                            $ppb = (float)($item->pieces_per_box > 0 ? $item->pieces_per_box : ($item->product->pieces_per_box ?? 1));
                            if ($ppb <= 0) $ppb = 1;

                            $rawUnit = strtolower(trim($item->unit ?? ''));
                            $isPiece = in_array($rawUnit, ['pcs', 'pc', 'piece']);
                            $isCtn = in_array($rawUnit, ['carton', 'ctn', 'box', 'bandal', 'bundal', 'bndl']) || (!$isPiece && in_array($item->size_mode, ['by_cartons', 'by_bandal']));

                            if ($isCtn) {
                                if ($item->boxes_qty > 0 || $item->loose_qty > 0) {
                                    $boxes = (int) $item->boxes_qty;
                                    $loosePieces = (int) $item->loose_qty;
                                } else {
                                    [$boxes, $loosePieces] = \App\Http\Controllers\PurchaseController::parseCartonQty($item->qty);
                                }
                                $totalPieces = ($boxes * $ppb) + $loosePieces;
                                $numCartons = $ppb > 0 ? ($totalPieces / $ppb) : $boxes;
                            } elseif ($isPiece) {
                                $totalPieces = (float) $item->qty;
                                $numCartons = $ppb > 1 ? ($totalPieces / $ppb) : 0;
                            } else {
                                $totalPieces = (float) $item->qty;
                                $numCartons = $ppb > 1 ? ($totalPieces / $ppb) : 0;
                            }

                            $baseProductName = $item->product->item_name ?? 'Item';
                            $itemCode = $item->product->item_code ?? ($item->product_id ?? '');
                            $expDate = !empty($item->exp_date) ? \Carbon\Carbon::parse($item->exp_date)->format('d.m.Y') : null;
                            $batchNo = $item->batch_no ?? null;

                            $titleParts = [];
                            if ($itemCode) $titleParts[] = $itemCode;
                            $titleParts[] = strtoupper($baseProductName);

                            $fullDesc = implode('/', $titleParts);
                            if ($batchNo) $fullDesc .= ' LOC-' . $batchNo;
                            if ($expDate) $fullDesc .= ' EXP: ' . $expDate;

                            $rVal = (float)($item->retail_value ?: (($item->retail_price ?: ($item->product->retail_price ?? 0)) * $totalPieces));
                            $tVal = (float)($item->trade_value ?: ($item->price * (float)$item->qty));
                            $discAmt = (float)($item->item_discount ?? 0);
                            $discPct = $tVal > 0 ? ($discAmt / $tVal) * 100 : 0;
                            $vAftDisc = (float)($item->value_after_discount ?: max(0, $tV - $discAmt));
                            $stRet = (float)($item->sales_tax_on_retail ?? 0);
                            $stTrd = (float)($item->sales_tax_on_trade ?? 0);
                            $lineTotal = (float)$item->line_total;
                            $gstPct = (float)($item->gst_percent ?: ($item->product->taxType->rate ?? 18));
                        @endphp
                        <tr>
                            <td class="text-start">
                                <div>{{ $fullDesc }}</div>
                            </td>
                            <td class="text-center font-monospace">{{ $item->product->hs_code ?? '2007.9900' }}</td>
                            <td class="text-end font-monospace">{{ (int)$totalPieces }}</td>
                            <td class="text-end font-monospace">{{ number_format($numCartons, 2) }}</td>
                            <td class="text-end font-monospace">{{ number_format($rVal, 2) }}</td>
                            <td class="text-end font-monospace">{{ number_format($tVal, 2) }}</td>
                            <td class="text-end font-monospace">{{ number_format($discPct, 2) }} %</td>
                            <td class="text-end font-monospace">{{ number_format($discAmt, 2) }}</td>
                            <td class="text-end font-monospace">{{ number_format($vAftDisc, 2) }}</td>
                            <td class="text-end font-monospace">{{ number_format($gstPct, 2) }}%</td>
                            <td class="text-end font-monospace">{{ number_format($stRet, 2) }}</td>
                            <td class="text-end font-monospace">{{ number_format($stTrd, 2) }}</td>
                            <td class="text-end font-monospace"><strong>{{ number_format($lineTotal, 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-start fw-bold">Sub Total :</td>
                        <td></td>
                        <td class="text-end font-monospace fw-bold">{{ (int)$totQty }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totCartons, 2) }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totRetailVal, 2) }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totTradeVal, 2) }}</td>
                        <td></td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totDiscVal, 2) }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totValAfterDisc, 2) }}</td>
                        <td></td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totStRetail, 2) }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totStTrade, 2) }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totLineTotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-start fw-bold">Grand Total :</td>
                        <td></td>
                        <td class="text-end font-monospace fw-bold">{{ (int)$totQty }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totCartons, 2) }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totRetailVal, 2) }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totTradeVal, 2) }}</td>
                        <td></td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totDiscVal, 2) }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totValAfterDisc, 2) }}</td>
                        <td></td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totStRetail, 2) }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totStTrade, 2) }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($totLineTotal, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- 5. Summary Lines Section -->
        <table class="summary-lines-table">
            <tr>
                <td class="sum-lbl-col">Less Frieght:</td>
                <td class="sum-val-col font-monospace">{{ number_format((float)($purchase->freight_less ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td class="sum-lbl-col">Add 0.5% ( Withholding Income Tax collected U/S 153A ):</td>
                <td class="sum-val-col font-monospace">{{ number_format((float)($purchase->income_tax_153a ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td class="sum-lbl-col">Add 4% Sales Tax on Un-Registered Customer:</td>
                <td class="sum-val-col font-monospace">{{ number_format((float)($purchase->unregistered_sales_tax ?? 0), 2) }}</td>
            </tr>
            <tr>
                <td class="sum-lbl-col">WHT @ {{ (float)($purchase->wht_percent ?? 0) > 0 ? number_format($purchase->wht_percent, 2) : '0.10' }} % u/s 236G:</td>
                <td class="sum-val-col font-monospace">{{ number_format((float)($purchase->wht_amount ?? 0), 2) }}</td>
            </tr>
            <tr class="final-words-row">
                <td class="sum-lbl-col">
                    {{ $wordsAmount }}
                </td>
                <td class="sum-val-col font-monospace">
                    {{ number_format($purchase->net_amount, 2) }}
                </td>
            </tr>
        </table>

        <!-- 6. Footer Weight & Certificate Section -->
        <div class="footer-details-grid">
            <!-- Left: Weights -->
            <div class="footer-weight-col">
                <table>
                    <tr>
                        <td style="width: 80px;">Total Units</td>
                        <td class="font-monospace">{{ number_format($totQty, 3) }}</td>
                    </tr>
                    <tr>
                        <td>Total Cartons</td>
                        <td class="font-monospace">{{ number_format($totCartons, 3) }}</td>
                    </tr>
                    <tr>
                        <td>Net Weight</td>
                        <td class="font-monospace">{{ number_format($totNetWeight, 3) }}</td>
                    </tr>
                    <tr>
                        <td>Gross Weight</td>
                        <td class="font-monospace">{{ number_format($totGrossWeight, 3) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Center & Right: Compliance Statements -->
            <div class="footer-notes-col">
                <div>" This is a system generated Sales Tax Invoice and it does not require any signature "</div>
                <div>" This is to certify that goods supplied against sales tax invoice shall be exempted from WHT as per the exemption certificate "</div>
            </div>
        </div>

    </div>

</body>

</html>
