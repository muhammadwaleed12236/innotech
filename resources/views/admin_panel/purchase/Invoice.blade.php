<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Invoice - {{ $purchase->invoice_no }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0f172a;
            --accent-color: #2563eb;
            --border-color: #000000;
            --text-color: #0f172a;
        }

        body {
            background-color: #f1f5f9;
            color: var(--text-color);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            max-width: 215mm;
            margin: 20px auto;
            background: #ffffff;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            position: relative;
        }

        .company-info {
            text-align: center;
            margin-bottom: 16px;
        }

        .company-name {
            font-size: 22px;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 2px;
            letter-spacing: -0.02em;
        }

        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            color: #1e293b;
            margin: 12px 0 16px 0;
            letter-spacing: 2px;
        }

        .info-box {
            border: 1px solid var(--border-color);
            padding: 10px 12px;
            height: 100%;
            border-radius: 8px;
            background-color: #ffffff;
        }

        .info-box-header {
            font-weight: 800;
            border-bottom: 1.5px solid var(--border-color);
            margin-bottom: 6px;
            padding-bottom: 4px;
            color: var(--primary-color);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-label {
            font-weight: 700;
            color: #334155;
            min-width: 70px;
            display: inline-block;
        }

        /* Desktop Invoice Table */
        .invoice-table-wrap {
            overflow-x: auto;
            margin-top: 16px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-table th {
            background-color: #ffffff;
            color: #000000;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 800;
            padding: 8px 6px;
            border: 1px solid var(--border-color);
        }

        .invoice-table td {
            border: 1px solid var(--border-color);
            padding: 8px 6px;
            vertical-align: middle;
            font-size: 12px;
        }

        .invoice-table tbody tr:nth-of-type(even) {
            background-color: #f8fafc;
        }

        /* Mobile Item Cards View (< 768px) */
        .mobile-invoice-items {
            display: none;
            flex-direction: column;
            gap: 10px;
            margin-top: 16px;
        }

        .mob-item-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .mob-item-hdr {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 6px;
        }

        .mob-item-title {
            font-weight: 700;
            font-size: 0.92rem;
            color: #0f172a;
        }

        .mob-item-code {
            font-family: monospace;
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .mob-item-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.84rem;
            padding-top: 8px;
            border-top: 1px solid #f1f5f9;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .totals-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .totals-table .total-row td {
            border-top: 2px solid var(--primary-color);
            font-weight: 800;
            font-size: 14px;
            color: var(--primary-color);
        }

        .signature-area {
            margin-top: 40px;
            border-top: 1px solid #000000;
            width: 180px;
            text-align: center;
            padding-top: 6px;
            font-weight: 600;
        }

        /* Action bar */
        .action-bar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        /* Print Media Queries */
        @media print {
            body {
                background: #ffffff !important;
                margin: 0;
                padding: 0;
            }

            .action-bar {
                display: none !important;
            }

            .invoice-container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 10px !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }

            .invoice-table-wrap {
                display: block !important;
            }

            .mobile-invoice-items {
                display: none !important;
            }

            @page {
                margin: 5mm;
            }
        }

        /* Mobile Breakpoints (< 768px) */
        @media (max-width: 768px) {
            body {
                background-color: #ffffff;
            }
            .invoice-container {
                margin: 0;
                border-radius: 0;
                box-shadow: none;
                padding: 14px;
            }
            .invoice-table-wrap {
                display: none !important;
            }
            .mobile-invoice-items {
                display: flex !important;
            }
            .info-box {
                margin-bottom: 8px;
            }
        }
    </style>
</head>

<body>

    <!-- Sticky Responsive Action Bar -->
    <div class="action-bar no-print">
        <div class="container-fluid d-flex align-items-center justify-content-between gap-2 px-2 px-md-3">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white fw-bold px-2 py-1" style="font-size: 0.75rem; white-space: nowrap;">#{{ $purchase->invoice_no }}</span>
                @if(!empty($purchase->purchase_order_no))
                    <span class="badge bg-secondary text-white fw-medium px-2 py-1" style="font-size: 0.75rem; white-space: nowrap;">Ref: {{ $purchase->purchase_order_no }}</span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('purchase.edit', $purchase->id) }}" class="btn btn-warning btn-sm px-3 fw-bold shadow-sm d-flex align-items-center gap-1 text-dark" style="border-radius: 8px; font-size: 0.8rem; white-space: nowrap;">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button onclick="window.print()" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm d-flex align-items-center gap-1" style="border-radius: 8px; font-size: 0.8rem; white-space: nowrap;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-printer-fill" viewBox="0 0 16 16">
                        <path d="M0 9a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V9zm4-6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2H4V3z" />
                        <path d="M2.5 14.5A1.5 1.5 0 0 1 1 13V9a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v4a1.5 1.5 0 0 1-1.5 1.5h-13z" />
                    </svg>
                    Print
                </button>
                <a href="{{ route('Purchase.home') }}" class="btn btn-outline-secondary btn-sm px-2 px-md-3 fw-semibold text-nowrap" style="border-radius: 8px; font-size: 0.8rem;">Back</a>
            </div>
        </div>
    </div>

    <div class="invoice-container">
        <!-- Company Header -->
        <div class="company-info">
            <div class="company-name">{{ \App\Models\Setting::get('company_name', 'prowave technogies') }} - {{ date('Y') }}</div>
            <div style="font-size: 12px; color: #475569;">{{ \App\Models\Setting::get('company_address', 'Hyderabad') }}</div>
        </div>

        <div class="invoice-title">Purchase Invoice</div>

        <!-- Info Grid -->
        <div class="row g-2 mb-3">
            <!-- Left Box: Vendor Info -->
            <div class="col-12 col-md-4">
                <div class="info-box">
                    <div class="info-box-header">Vendor Details</div>
                    <div style="font-size: 13px; font-weight: 800; color: #0f172a;">
                        {{ $purchase->vendor->name ?? 'N/A' }}
                    </div>
                    @if(!empty($purchase->vendor->address))
                        <div style="font-size: 11px; color: #475569;">{{ $purchase->vendor->address }}</div>
                    @endif
                    @if(!empty($purchase->vendor->phone))
                        <div class="text-dark small" style="font-size: 11px;">Mob: {{ $purchase->vendor->phone }}</div>
                    @endif
                </div>
            </div>

            <!-- Middle Box: Details -->
            <div class="col-12 col-md-4">
                <div class="info-box">
                    <div class="info-box-header">Details</div>
                    <div><span class="info-label">Type:</span> {{ $purchase->status_purchase ?? 'Confirmed' }}</div>
                    <div><span class="info-label">Warehouse:</span> {{ $purchase->warehouse->warehouse_name ?? 'Main' }}</div>
                </div>
            </div>

            <!-- Right Box: Invoice Specifics -->
            <div class="col-12 col-md-4">
                <div class="info-box">
                    <div class="info-box-header">Reference</div>
                    <div><span class="info-label">Inv #:</span> <strong>{{ $purchase->invoice_no ?: ('PUR-' . str_pad($purchase->id, 3, '0', STR_PAD_LEFT)) }}</strong></div>
                    <div><span class="info-label">Date:</span> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</div>
                    <div><span class="info-label">Vendor Ref #:</span> <strong>{{ $purchase->purchase_order_no ?: ($purchase->note ?: '—') }}</strong></div>
                </div>
            </div>
        </div>

        <!-- Remarks -->
        @if ($purchase->note)
            <div class="row mb-3">
                <div class="col-12">
                    <div class="info-box" style="min-height: auto; padding: 6px 10px; background-color: #f8fafc; font-style: italic; border-color: #cbd5e1;">
                        <strong>Note:</strong> {{ $purchase->note }}
                    </div>
                </div>
            </div>
        @endif

        @php
            $sumCartons = 0;
            $sumLoosePieces = 0;
            $sumTotalPieces = 0;
            $hasCartonMode = false;

            foreach ($purchase->items as $it) {
                $rawU = strtolower(trim($it->unit ?? ''));
                $itPPB = (float) ($it->pieces_per_box > 0 ? $it->pieces_per_box : ($it->product->pieces_per_box ?? 1));

                if (!empty($it->color)) {
                    $dec = base64_decode($it->color, true);
                    $vD = ($dec !== false) ? json_decode($dec, true) : json_decode($it->color, true);
                    if (is_array($vD)) {
                        if (!empty($vD['conv_factor']) && (float)$vD['conv_factor'] > 0) {
                            $itPPB = (float)$vD['conv_factor'];
                        }
                        if (empty($rawU) && !empty($vD['unit'])) {
                            $rawU = strtolower(trim($vD['unit']));
                        }
                    }
                }
                if ($itPPB <= 0) $itPPB = 1;

                $rawQtyStr = (string) ($it->qty ?? '0');
                $isPiece = in_array($rawU, ['pcs', 'pc', 'piece']);
                $isCtn = in_array($rawU, ['carton', 'ctn', 'box', 'bandal', 'bundal', 'bndl']) || (!$isPiece && in_array($it->size_mode, ['by_cartons', 'by_bandal']));

                if ($isCtn) {
                    $hasCartonMode = true;
                    if ($it->boxes_qty > 0 || $it->loose_qty > 0) {
                        $b = (int) $it->boxes_qty;
                        $l = (int) $it->loose_qty;
                    } else {
                        [$b, $l] = \App\Http\Controllers\PurchaseController::parseCartonQty($it->qty);
                    }
                    $sumCartons += $b;
                    $sumLoosePieces += $l;
                    $sumTotalPieces += (($b * $itPPB) + $l);
                } elseif ($itPPB > 1) {
                    $hasCartonMode = true;
                    $itQty = (float) $it->qty;
                    $b = floor($itQty / $itPPB);
                    $l = $itQty - ($b * $itPPB);
                    $sumCartons += $b;
                    $sumLoosePieces += $l;
                    $sumTotalPieces += $itQty;
                } else {
                    $itQty = (float) $it->qty;
                    $sumLoosePieces += $itQty;
                    $sumTotalPieces += $itQty;
                }
            }

            if ($hasCartonMode && $sumCartons > 0) {
                if ($sumLoosePieces > 0) {
                    $cartonPcsDisplay = "{$sumCartons} Ctn + {$sumLoosePieces} Pcs (" . number_format($sumTotalPieces) . " Total Pcs)";
                    $cartonPcsShort = "{$sumCartons} Ctn + {$sumLoosePieces} Pcs";
                } else {
                    $cartonPcsDisplay = "{$sumCartons} Ctn (" . number_format($sumTotalPieces) . " Total Pcs)";
                    $cartonPcsShort = "{$sumCartons} Ctn";
                }
            } elseif ($hasCartonMode && $sumLoosePieces > 0) {
                $cartonPcsDisplay = "{$sumLoosePieces} Pcs";
                $cartonPcsShort = "{$sumLoosePieces} Pcs";
            } else {
                $cartonPcsDisplay = number_format($sumTotalPieces) . " Pcs";
                $cartonPcsShort = number_format($sumTotalPieces) . " Pcs";
            }
        @endphp

        <!-- Desktop & Print Table View -->
        <div class="invoice-table-wrap">
            <table class="invoice-table">
                <thead>
                    <tr style="background-color: #f8fafc;">
                        <th class="text-center" style="width: 3%">#</th>
                        <th class="text-start" style="width: 24%">Item Description</th>
                        <th class="text-center" style="width: 11%">Qty / Packaging</th>
                        <th class="text-end" style="width: 12%">Trade Rate</th>
                        <th class="text-end" style="width: 9%">Gross Value</th>
                        <th class="text-end" style="width: 8%">Disc.</th>
                        <th class="text-end" style="width: 9%">After Disc</th>
                        <th class="text-end" style="width: 9%">Sales Tax</th>
                        <th class="text-end" style="width: 15%; background-color: #eff6ff; color: #1e40af; border-left: 2px solid #3b82f6;">
                            Hamari Parhi<br><span style="font-size:9.5px; font-weight:600; text-transform:none;">(Cost / Parhi per Unit)</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->items as $item)
                        @php
                            $height = $item->length ?? 0;
                            $width = $item->width ?? 0;

                            $piecesPerBox = (float) ($item->pieces_per_box > 0 ? $item->pieces_per_box : ($item->product->pieces_per_box ?? 1));
                            if (!empty($item->color)) {
                                $decColor = base64_decode($item->color, true);
                                $vColorData = ($decColor !== false) ? json_decode($decColor, true) : json_decode($item->color, true);
                                if (is_array($vColorData) && !empty($vColorData['conv_factor']) && (float)$vColorData['conv_factor'] > 0) {
                                    $piecesPerBox = (float)$vColorData['conv_factor'];
                                }
                            }
                            if ($piecesPerBox <= 0) $piecesPerBox = 1;

                            $rawUnit = strtolower(trim($item->unit ?? ''));
                            $isPiece = in_array($rawUnit, ['pcs', 'pc', 'piece']);
                            $isCarton = in_array($rawUnit, ['carton', 'ctn', 'box', 'bandal', 'bundal', 'bndl']) || (!$isPiece && (in_array($item->size_mode, ['by_cartons', 'by_bandal'])));
                            $isWeight = in_array($rawUnit, ['kg', 'gm', 'g']);

                            if ($isCarton) {
                                if ($item->boxes_qty > 0 || $item->loose_qty > 0) {
                                    $boxes = (int) $item->boxes_qty;
                                    $loosePieces = (int) $item->loose_qty;
                                } else {
                                    [$boxes, $loosePieces] = \App\Http\Controllers\PurchaseController::parseCartonQty($item->qty);
                                }
                                $totalPieces = ($boxes * $piecesPerBox) + $loosePieces;
                                $numCartons = $piecesPerBox > 0 ? ($totalPieces / $piecesPerBox) : $boxes;
                            } elseif ($isPiece) {
                                $totalPieces = (float) $item->qty;
                                $boxes = $piecesPerBox > 1 ? floor($totalPieces / $piecesPerBox) : 0;
                                $loosePieces = $piecesPerBox > 1 ? ($totalPieces % $piecesPerBox) : $totalPieces;
                                $numCartons = $piecesPerBox > 1 ? ($totalPieces / $piecesPerBox) : 0;
                            } else {
                                $totalPieces = (float) $item->qty;
                                $boxes = $piecesPerBox > 1 ? floor($totalPieces / $piecesPerBox) : 0;
                                $loosePieces = $piecesPerBox > 1 ? ($totalPieces % $piecesPerBox) : $totalPieces;
                                $numCartons = $piecesPerBox > 1 ? ($totalPieces / $piecesPerBox) : 0;
                            }

                            $baseProductName = $item->product->item_name ?? 'Item';
                            $variantNameDisplay = '';
                            $variantDetails = [];

                            if (!empty($item->color)) {
                                $decodedColor = base64_decode($item->color, true);
                                $vData = ($decodedColor !== false) ? json_decode($decodedColor, true) : null;
                                if (empty($vData) || !is_array($vData)) {
                                    $vData = json_decode($item->color, true);
                                }
                                if (!empty($vData) && is_array($vData)) {
                                    $vName = trim($vData['name'] ?? ($vData['variant_name'] ?? ''));
                                    $vColorName = trim($vData['color'] ?? '');
                                    $vSizeName = trim($vData['size'] ?? '');

                                    if ($vName !== '' && strcasecmp($vName, $baseProductName) !== 0) {
                                        $variantNameDisplay = $vName;
                                    }

                                    if ($vSizeName !== '' && $vSizeName !== '-') {
                                        $variantDetails[] = 'Size: ' . $vSizeName;
                                    }
                                    if ($vColorName !== '' && $vColorName !== '-') {
                                        $variantDetails[] = 'Color: ' . $vColorName;
                                    }
                                } elseif (is_string($item->color) && trim($item->color) !== '' && trim($item->color) !== '-') {
                                    $variantDetails[] = trim($item->color);
                                }
                            }

                            if ($variantNameDisplay !== '') {
                                if (stripos($variantNameDisplay, $baseProductName) !== false) {
                                    $fullItemTitle = $variantNameDisplay;
                                } else {
                                    $fullItemTitle = $baseProductName . ' — ' . $variantNameDisplay;
                                }
                            } else {
                                $fullItemTitle = $baseProductName;
                            }

                            if (!empty($variantDetails)) {
                                $fullItemTitle .= ' (' . implode(' | ', $variantDetails) . ')';
                            }

                            $rVal = (float)($item->retail_value ?? 0);
                            $tVal = (float)($item->trade_value ?: ($item->price * (float)$item->qty));
                            $discAmt = (float)($item->item_discount ?? 0);
                            $discPct = $tVal > 0 ? ($discAmt / $tVal) * 100 : 0;
                            $vAftDisc = (float)($item->value_after_discount ?: max(0, $tVal - $discAmt));
                            $stRet = (float)($item->sales_tax_on_retail ?? 0);
                            $stTrd = (float)($item->sales_tax_on_trade ?? 0);
                            $taxAmt = $stRet > 0 ? $stRet : $stTrd;
                            $lineTotal = (float) $item->line_total;

                            // Rates: Trade & Retail
                            $tradeRatePerCtn = 0;
                            $tradeRatePerPc = 0;
                            $retailRatePerCtn = 0;
                            $retailRatePerPc = 0;

                            if ($isCarton) {
                                $tradeRatePerCtn = (float) $item->price;
                                $tradeRatePerPc = $piecesPerBox > 0 ? ($tradeRatePerCtn / $piecesPerBox) : $tradeRatePerCtn;
                                $retailRatePerCtn = (float) ($item->retail_price ?: ($item->product->sale_price_per_box ?? (($item->product->retail_price ?? 0) * $piecesPerBox)));
                                $retailRatePerPc = $piecesPerBox > 0 ? ($retailRatePerCtn / $piecesPerBox) : $retailRatePerCtn;
                            } elseif ($piecesPerBox > 1) {
                                $tradeRatePerPc = (float) $item->price;
                                $tradeRatePerCtn = $tradeRatePerPc * $piecesPerBox;
                                $retailRatePerPc = (float) ($item->retail_price ?: ($item->product->retail_price ?? 0));
                                $retailRatePerCtn = $retailRatePerPc * $piecesPerBox;
                            } else {
                                $tradeRatePerPc = (float) $item->price;
                                $retailRatePerPc = (float) ($item->retail_price ?: ($item->product->retail_price ?? 0));
                            }

                            // Actual Final Cost (Hamari Parhi: Trade Value - Humara Discount + GST + WHT)
                            $ourDiscAmt = (float)($item->our_disc_amount ?? 0);
                            $custDiscAmt = (float)($item->customer_disc_amount ?? 0);
                            if ($ourDiscAmt <= 0 && $discAmt > 0) {
                                if ($custDiscAmt > 0) {
                                    $ourDiscAmt = max(0, $discAmt - $custDiscAmt);
                                } else {
                                    $ourDiscAmt = $discAmt;
                                }
                            }
                            $companyDiscAmt = max(0, $discAmt - $ourDiscAmt);

                            $totalBeforeWht = (float)($purchase->total_value_after_discount ?? 0) + (float)($purchase->total_gst ?? 0);
                            $itemWht = 0;
                            if ((float)($purchase->wht_amount ?? 0) > 0 && $totalBeforeWht > 0) {
                                $itemWht = ($lineTotal / $totalBeforeWht) * (float)$purchase->wht_amount;
                            }

                            $hamariParhiTotal = max(0, $tVal - $ourDiscAmt) + $taxAmt + $itemWht;
                            $finalCostPerCtn = $numCartons > 0 ? ($hamariParhiTotal / $numCartons) : 0;
                            $finalCostPerPc = $totalPieces > 0 ? ($hamariParhiTotal / $totalPieces) : ($hamariParhiTotal / max(1, (float)$item->qty));
                        @endphp
                        <tr>
                            <td class="text-center font-monospace" style="vertical-align: middle;">
                                {{ $loop->iteration }}
                            </td>
                            <td class="text-start" style="vertical-align: middle;">
                                <div style="font-weight: 700; font-size: 12.5px; color: #0f172a; margin-bottom: 2px;">
                                    {{ $fullItemTitle }}
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-1" style="font-size: 10.5px;">
                                    @if($piecesPerBox > 1)
                                        <span class="badge bg-light text-secondary border font-monospace">1 Ctn = {{ number_format($piecesPerBox, 0) }} Pcs</span>
                                    @endif
                                    @if(!empty($item->product->hs_code))
                                        <span class="badge bg-light text-secondary border font-monospace">HS: {{ $item->product->hs_code }}</span>
                                    @endif
                                    @php
                                        $rowTax = $item->taxType ?? ($item->product->taxType ?? null);
                                    @endphp
                                    @if($rowTax)
                                        <span class="badge {{ $item->is_third_schedule ? 'bg-warning text-dark' : 'bg-primary-subtle text-primary' }} border" style="font-size: 9.5px;">[{{ $rowTax->code }}] {{ $rowTax->title }}</span>
                                    @elseif($item->is_third_schedule)
                                        <span class="badge bg-warning text-dark border" style="font-size: 9.5px;">3rd Schedule</span>
                                    @endif
                                    @if($retailRatePerPc > 0)
                                        <span class="text-muted">Retail: Rs. {{ number_format($retailRatePerPc, 2) }}/pc</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Qty / Packaging --}}
                            <td class="text-center font-monospace" style="vertical-align: middle;">
                                @if($isCarton || $piecesPerBox > 1)
                                    @if($boxes > 0 && $loosePieces > 0)
                                        <div class="fw-bold text-dark">{{ $boxes }} Ctn + {{ $loosePieces }} Pcs</div>
                                    @elseif($boxes > 0)
                                        <div class="fw-bold text-dark">{{ $boxes }} {{ $boxes == 1 ? 'Carton' : 'Cartons' }}</div>
                                    @elseif($loosePieces > 0)
                                        <div class="fw-bold text-dark">{{ $loosePieces }} Pcs</div>
                                    @endif
                                    <div class="text-muted" style="font-size: 11px;">({{ number_format($totalPieces, 0) }} Total Pcs)</div>
                                @else
                                    <div class="fw-bold text-dark">{{ number_format($totalPieces, 2) }} {{ $item->unit ?? 'Pcs' }}</div>
                                @endif
                            </td>

                            {{-- Trade Rate --}}
                            <td class="text-end font-monospace" style="vertical-align: middle;">
                                @if($tradeRatePerCtn > 0)
                                    <div class="fw-bold text-dark">Rs. {{ number_format($tradeRatePerCtn, 2) }}<span class="text-muted fw-normal" style="font-size: 10px;">/ctn</span></div>
                                    <div class="text-muted" style="font-size: 10.5px;">Rs. {{ number_format($tradeRatePerPc, 2) }}/pc</div>
                                @else
                                    <div class="fw-bold text-dark">Rs. {{ number_format($tradeRatePerPc, 2) }}<span class="text-muted fw-normal" style="font-size: 10px;">/pc</span></div>
                                @endif
                            </td>

                            {{-- Gross Trade Value --}}
                            <td class="text-end font-monospace fw-semibold" style="vertical-align: middle;">
                                <div>Rs. {{ number_format($tVal, 2) }}</div>
                                @if($rVal > 0 && $item->is_third_schedule)
                                    <div class="text-muted" style="font-size: 10px;" title="Retail Value">Ret: {{ number_format($rVal, 2) }}</div>
                                @endif
                            </td>

                            {{-- Disc - Trader --}}
                            <td class="text-end font-monospace" style="vertical-align: middle;">
                                @if($discAmt > 0)
                                    <div class="text-danger fw-semibold">-Rs. {{ number_format($discAmt, 2) }}</div>
                                    <div class="text-muted" style="font-size: 10px;">({{ number_format($discPct, 2) }}%)</div>
                                    @if($companyDiscAmt > 0)
                                        <div class="badge bg-light text-secondary border mt-1" style="font-size: 9px;" title="Company Share Discount (Company Ledger)">Comp: -Rs. {{ number_format($companyDiscAmt, 2) }}</div>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Value After Disc --}}
                            <td class="text-end font-monospace fw-bold text-dark" style="vertical-align: middle;">
                                Rs. {{ number_format($vAftDisc, 2) }}
                            </td>

                            {{-- Sales Tax (GST) --}}
                            <td class="text-end font-monospace" style="vertical-align: middle;">
                                @if($taxAmt > 0)
                                    <div class="text-success fw-semibold">+Rs. {{ number_format($taxAmt, 2) }}</div>
                                    <div class="text-muted" style="font-size: 10px;">
                                        {{ number_format($item->gst_percent ?? 0, 1) }}% {{ $stRet > 0 ? '(Retail)' : '(Trade)' }}
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Hamari Parhi (Trade Value - Humara Disc + GST + WHT) --}}
                            <td class="text-end font-monospace" style="vertical-align: middle; background-color: #f8fafc; border-left: 2px solid #3b82f6;">
                                <div class="fw-bold text-primary" style="font-size: 13.5px;" title="Hamari Parhi: Trade - Our Disc + GST + WHT">
                                    Rs. {{ number_format($hamariParhiTotal, 2) }}
                                </div>
                                <div class="mt-1 d-flex flex-column align-items-end gap-1">
                                    @if($numCartons > 0)
                                        <span class="badge bg-primary text-white font-monospace px-2 py-1" style="font-size: 11px;" title="Hamari Parhi Per Carton">
                                            Rs. {{ number_format($finalCostPerCtn, 2) }} / Ctn
                                        </span>
                                    @endif
                                    <span class="badge bg-dark text-white font-monospace px-2 py-1" style="font-size: 11px;" title="Hamari Parhi Per Piece">
                                        Rs. {{ number_format($finalCostPerPc, 2) }} / Pc
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @php
                        $sumRetailVal = $purchase->items->sum('retail_value');
                        $sumTradeVal = (float)$purchase->items->sum(function($it) { return (float)($it->trade_value ?: ($it->price * (float)$it->qty)); }) ?: $purchase->subtotal;
                        $sumInlineDisc = $purchase->items->sum('item_discount');
                        $sumValAfterDisc = $purchase->items->sum('value_after_discount') ?: max(0, $sumTradeVal - $sumInlineDisc);
                        $sumSt = ($purchase->items->sum('sales_tax_on_retail') ?: 0) + ($purchase->items->sum('sales_tax_on_trade') ?: 0);
                        if ($sumSt <= 0) $sumSt = (float)($purchase->total_gst ?? 0);

                        $sumHamariParhi = 0;
                        $sumCompDiscAmt = 0;
                        foreach ($purchase->items as $it) {
                            $itTVal = (float)($it->trade_value ?: ($it->price * (float)$it->qty));
                            $itDisc = (float)($it->item_discount ?? 0);
                            $itOur = (float)($it->our_disc_amount ?? 0);
                            $itCust = (float)($it->customer_disc_amount ?? 0);
                            if ($itOur <= 0 && $itDisc > 0) {
                                $itOur = $itCust > 0 ? max(0, $itDisc - $itCust) : $itDisc;
                            }
                            $itComp = max(0, $itDisc - $itOur);
                            $sumCompDiscAmt += $itComp;
                            $itTax = (float)($it->sales_tax_on_retail > 0 ? $it->sales_tax_on_retail : ($it->sales_tax_on_trade ?: ($it->gst_amount ?? 0)));
                            $itWht = 0;
                            if ((float)($purchase->wht_amount ?? 0) > 0 && ((float)($purchase->total_value_after_discount ?? 0) + (float)($purchase->total_gst ?? 0)) > 0) {
                                $itWht = ((float)$it->line_total / ((float)$purchase->total_value_after_discount + (float)$purchase->total_gst)) * (float)$purchase->wht_amount;
                            }
                            $sumHamariParhi += max(0, $itTVal - $itOur) + $itTax + $itWht;
                        }
                        $avgFinalCostPerCtn = $sumCartons > 0 ? ($sumHamariParhi / $sumCartons) : 0;
                        $avgFinalCostPerPc = $sumTotalPieces > 0 ? ($sumHamariParhi / $sumTotalPieces) : 0;
                    @endphp
                    <tr style="background-color: #f1f5f9; font-weight: 800; border-top: 2px solid var(--border-color); font-size:11.5px;">
                        <td colspan="2" class="text-end text-uppercase" style="padding: 10px 8px;">Total:</td>
                        <td class="text-center text-primary font-monospace">{{ $cartonPcsShort }}</td>
                        <td></td>
                        <td class="text-end font-monospace">Rs. {{ number_format($sumTradeVal, 2) }}</td>
                        <td class="text-end text-danger font-monospace">{{ $sumInlineDisc > 0 ? '-Rs. ' . number_format($sumInlineDisc, 2) : '—' }}</td>
                        <td class="text-end font-monospace">Rs. {{ number_format($sumValAfterDisc, 2) }}</td>
                        <td class="text-end font-monospace text-success">+Rs. {{ number_format($sumSt, 2) }}</td>
                        <td class="text-end text-primary font-monospace" style="background-color: #eff6ff; border-left: 2px solid #3b82f6;">
                            <div style="font-size: 13.5px;" title="Hamari Parhi: Trade - Our Disc + GST + WHT">Rs. {{ number_format($sumHamariParhi, 2) }}</div>
                            @if($sumCartons > 0)
                                <div class="text-muted fw-normal" style="font-size: 10.5px;">Avg: Rs. {{ number_format($avgFinalCostPerCtn, 2) }}/Ctn</div>
                            @endif
                            @if($sumTotalPieces > 0)
                                <div class="text-muted fw-normal" style="font-size: 10.5px;">Avg: Rs. {{ number_format($avgFinalCostPerPc, 2) }}/Pc</div>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Mobile Items View (< 768px) -->
        <div class="mobile-invoice-items">
            @foreach ($purchase->items as $item)
                @php
                    $piecesPerBox = (float) ($item->pieces_per_box > 0 ? $item->pieces_per_box : ($item->product->pieces_per_box ?? 1));
                    $rawUnit = strtolower(trim($item->unit ?? ''));
                    $isPiece = in_array($rawUnit, ['pcs', 'pc', 'piece']);
                    $isCarton = in_array($rawUnit, ['carton', 'ctn', 'box', 'bandal', 'bundal', 'bndl']) || (!$isPiece && (in_array($item->size_mode, ['by_cartons', 'by_bandal'])));

                    if ($isCarton) {
                        if ($item->boxes_qty > 0 || $item->loose_qty > 0) {
                            $boxes = (int) $item->boxes_qty;
                            $loosePieces = (int) $item->loose_qty;
                        } else {
                            [$boxes, $loosePieces] = \App\Http\Controllers\PurchaseController::parseCartonQty($item->qty);
                        }
                        $totalPieces = ($boxes * $piecesPerBox) + $loosePieces;
                        $numCartons = $piecesPerBox > 0 ? ($totalPieces / $piecesPerBox) : $boxes;
                        $ctnLbl = ($item->size_mode === 'by_bandal') ? 'Bndl' : 'Ctn';
                        $cartonFull = ($item->size_mode === 'by_bandal') ? 'Bundal' : 'Carton';
                        $cartonFulls = ($item->size_mode === 'by_bandal') ? 'Bundals' : 'Cartons';
                        if ($boxes > 0 && $loosePieces > 0) {
                            $qtyDisplay = "{$boxes} {$ctnLbl} + {$loosePieces} Pcs";
                        } elseif ($boxes > 0) {
                            $qtyDisplay = ($boxes == 1 ? "1 {$cartonFull}" : ($boxes . " {$cartonFulls}"));
                        } elseif ($loosePieces > 0) {
                            $qtyDisplay = "{$loosePieces} Pcs";
                        } else {
                            $qtyDisplay = "0 {$cartonFulls}";
                        }
                    } elseif ($isPiece) {
                        $totalPieces = (float) $item->qty;
                        $numCartons = $piecesPerBox > 1 ? ($totalPieces / $piecesPerBox) : 0;
                        $qtyDisplay = (float) $item->qty . ' Pcs';
                    } else {
                        $totalPieces = (float) $item->qty;
                        $numCartons = $piecesPerBox > 1 ? ($totalPieces / $piecesPerBox) : 0;
                        $qtyDisplay = (float) $item->qty . ' ' . ($item->unit ?? 'Pcs');
                    }

                    $baseProductName = $item->product->item_name ?? 'Item';
                    $variantNameDisplay = '';
                    $variantDetails = [];

                    if (!empty($item->color)) {
                        $decodedColor = base64_decode($item->color, true);
                        $vData = ($decodedColor !== false) ? json_decode($decodedColor, true) : null;
                        if (empty($vData) || !is_array($vData)) {
                            $vData = json_decode($item->color, true);
                        }
                        if (!empty($vData) && is_array($vData)) {
                            $vName = trim($vData['name'] ?? ($vData['variant_name'] ?? ''));
                            $vColorName = trim($vData['color'] ?? '');
                            $vSizeName = trim($vData['size'] ?? '');

                            if ($vName !== '' && strcasecmp($vName, $baseProductName) !== 0) {
                                $variantNameDisplay = $vName;
                            }

                            if ($vSizeName !== '' && $vSizeName !== '-') {
                                $variantDetails[] = 'Size: ' . $vSizeName;
                            }
                            if ($vColorName !== '' && $vColorName !== '-') {
                                $variantDetails[] = 'Color: ' . $vColorName;
                            }
                        } elseif (is_string($item->color) && trim($item->color) !== '' && trim($item->color) !== '-') {
                            $variantDetails[] = trim($item->color);
                        }
                    }

                    if ($variantNameDisplay !== '') {
                        if (stripos($variantNameDisplay, $baseProductName) !== false) {
                            $fullItemTitle = $variantNameDisplay;
                        } else {
                            $fullItemTitle = $baseProductName . ' — ' . $variantNameDisplay;
                        }
                    } else {
                        $fullItemTitle = $baseProductName;
                    }

                    if (!empty($variantDetails)) {
                        $fullItemTitle .= ' (' . implode(' | ', $variantDetails) . ')';
                    }

                    $lineTotal = (float) $item->line_total;
                    $ourDiscAmt = (float)($item->our_disc_amount ?? 0);
                    $custDiscAmt = (float)($item->customer_disc_amount ?? 0);
                    $discAmt = (float)($item->item_discount ?? 0);
                    if ($ourDiscAmt <= 0 && $discAmt > 0) {
                        $ourDiscAmt = $custDiscAmt > 0 ? max(0, $discAmt - $custDiscAmt) : $discAmt;
                    }
                    $companyDiscAmt = max(0, $discAmt - $ourDiscAmt);
                    $tVal = (float)($item->trade_value ?: ($item->price * (float)$item->qty));
                    $taxAmt = (float)($item->sales_tax_on_retail > 0 ? $item->sales_tax_on_retail : ($item->sales_tax_on_trade ?: ($item->gst_amount ?? 0)));
                    $totalBeforeWht = (float)($purchase->total_value_after_discount ?? 0) + (float)($purchase->total_gst ?? 0);
                    $itemWht = 0;
                    if ((float)($purchase->wht_amount ?? 0) > 0 && $totalBeforeWht > 0) {
                        $itemWht = ($lineTotal / $totalBeforeWht) * (float)$purchase->wht_amount;
                    }
                    $hamariParhiTotal = max(0, $tVal - $ourDiscAmt) + $taxAmt + $itemWht;
                    $finalCostPerCtn = $numCartons > 0 ? ($hamariParhiTotal / $numCartons) : 0;
                    $finalCostPerPc = $totalPieces > 0 ? ($hamariParhiTotal / $totalPieces) : ($hamariParhiTotal / max(1, (float)$item->qty));
                @endphp
                <div class="mob-item-card">
                    <div class="mob-item-hdr">
                        <div class="mob-item-title">{{ $fullItemTitle }}</div>
                        <span class="mob-item-code">#{{ $item->product->item_code ?? '—' }}</span>
                    </div>

                    <div class="mob-item-details">
                        <div>
                            <span class="fw-bold text-dark">{{ $qtyDisplay }}</span>
                            <span class="text-muted ms-1">({{ number_format($totalPieces, 0) }} Pcs)</span>
                            <div class="text-muted" style="font-size: 0.78rem;">Rate: Rs. {{ number_format($item->price, 2) }}</div>
                        </div>
                        <div class="text-end">
                            @if ($item->item_discount > 0)
                                <div class="text-danger small" style="font-size: 0.72rem;">Disc: -Rs. {{ number_format($item->item_discount, 2) }}</div>
                                @if($companyDiscAmt > 0)
                                    <div class="text-muted small" style="font-size: 0.68rem;">Comp Disc: -Rs. {{ number_format($companyDiscAmt, 2) }}</div>
                                @endif
                            @endif
                            <div class="fw-bold text-primary" style="font-size: 0.95rem;">Hamari Parhi: Rs. {{ number_format($hamariParhiTotal, 2) }}</div>
                        </div>
                    </div>

                    {{-- Hamari Parhi Badges for Mobile --}}
                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                        <span class="text-primary fw-bold" style="font-size: 0.8rem;">Hamari Parhi:</span>
                        <div class="d-flex gap-1">
                            @if($numCartons > 0)
                                <span class="badge bg-primary text-white font-monospace" style="font-size: 0.75rem;">
                                    Rs. {{ number_format($finalCostPerCtn, 2) }} / Ctn
                                </span>
                            @endif
                            <span class="badge bg-dark text-white font-monospace" style="font-size: 0.75rem;">
                                Rs. {{ number_format($finalCostPerPc, 2) }} / Pc
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="mob-item-card bg-light" style="border: 1.5px solid var(--primary-color);">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark">Total Quantity:</span>
                    <span class="fw-bold text-primary font-monospace">{{ $cartonPcsDisplay }}</span>
                </div>
            </div>
        </div>

        <!-- Footer / Totals Section -->
        <div class="row mt-3">
            <div class="col-12 col-md-7 mb-3 mb-md-0">
                <div class="mt-md-4 pt-2">
                    <div class="signature-area">
                        Authorized Signature
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-5">
                <div class="info-box" style="border: 1px solid #cbd5e1; padding: 10px; border-radius: 8px;">
                    <table class="totals-table">
                        <tr>
                            <td class="text-muted fw-bold">Total Cartons / Pcs</td>
                            <td class="text-end fw-bold text-primary font-monospace" style="font-size: 12px;">{{ $cartonPcsDisplay }}</td>
                        </tr>
                        <tr>
                            <td class="text-dark">Total Trade Value</td>
                            <td class="text-end font-monospace">Rs. {{ number_format($purchase->total_trade_value ?: $purchase->subtotal, 2) }}</td>
                        </tr>
                        @if(($purchase->total_retail_value ?? 0) > 0)
                        <tr>
                            <td class="text-dark">Total Retail Value</td>
                            <td class="text-end font-monospace">Rs. {{ number_format($purchase->total_retail_value, 2) }}</td>
                        </tr>
                        @endif
                        @if($purchase->items->sum('item_discount') > 0)
                        <tr>
                            <td class="text-danger">Trade Discount</td>
                            <td class="text-end text-danger font-monospace">-Rs. {{ number_format($purchase->items->sum('item_discount'), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-dark fw-bold">Value After Discount</td>
                            <td class="text-end font-monospace fw-bold">Rs. {{ number_format($purchase->total_value_after_discount ?: ($purchase->total_trade_value - $purchase->items->sum('item_discount')), 2) }}</td>
                        </tr>
                        @endif
                        @if(($purchase->total_sales_tax_on_retail ?? 0) > 0)
                        <tr>
                            <td class="text-success fw-bold">ST on Retail (3rd Schedule)</td>
                            <td class="text-end text-success font-monospace">+Rs. {{ number_format($purchase->total_sales_tax_on_retail, 2) }}</td>
                        </tr>
                        @endif
                        @if(($purchase->total_sales_tax_on_trade ?? 0) > 0)
                        <tr>
                            <td class="text-info fw-bold">ST on Trade (Standard)</td>
                            <td class="text-end text-info font-monospace">+Rs. {{ number_format($purchase->total_sales_tax_on_trade, 2) }}</td>
                        </tr>
                        @endif
                        @if (($purchase->total_gst ?? 0) > 0)
                        <tr>
                            <td class="text-muted fw-bold">Excl. GST Subtotal</td>
                            <td class="text-end font-monospace">Rs. {{ number_format($purchase->total_exclusive_gst ?: ($purchase->subtotal - $purchase->total_gst), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-success fw-bold">GST Amount</td>
                            <td class="text-end text-success fw-bold font-monospace">+Rs. {{ number_format($purchase->total_gst, 2) }}</td>
                        </tr>
                        @endif
                        @if ($purchase->additional_discount > 0)
                            <tr>
                                <td>Additional Discount</td>
                                <td class="text-end text-danger font-monospace">
                                    @php
                                        $billDiscPercent = $purchase->subtotal > 0 ? ($purchase->additional_discount / $purchase->subtotal) * 100 : 0;
                                    @endphp
                                    <span style="font-size: 10px;" class="me-1">({{ number_format($billDiscPercent, 1) }}%)</span>
                                    -{{ number_format($purchase->additional_discount, 2) }}
                                </td>
                            </tr>
                        @endif
                        @php
                            $extraCostAmt = (float)($purchase->extra_cost ?? 0);
                            $whtAmt = (float)($purchase->wht_amount ?? 0);
                            $whtPct = (float)($purchase->wht_percent ?? 0);
                        @endphp
                        @if ($extraCostAmt > 0)
                            <tr>
                                <td class="text-dark fw-semibold">Freight / Extra Cost</td>
                                <td class="text-end font-monospace text-dark fw-bold">+Rs. {{ number_format($extraCostAmt, 2) }}</td>
                            </tr>
                        @endif
                        @if ($whtAmt > 0)
                            <tr>
                                <td class="text-dark fw-semibold">Withholding Tax (WHT {{ $whtPct > 0 ? number_format($whtPct, 1).'%' : '' }})</td>
                                <td class="text-end font-monospace text-primary fw-bold">+Rs. {{ number_format($whtAmt, 2) }}</td>
                            </tr>
                        @endif
                        @php
                            $sumHamariParhiTotal = $sumHamariParhi ?? 0;
                            if ($sumHamariParhiTotal <= 0) {
                                $sumHamariParhiTotal = (float)$purchase->net_amount + ($sumCompDiscAmt ?? 0);
                            }
                        @endphp
                        @if(($sumCompDiscAmt ?? 0) > 0)
                        <tr>
                            <td class="text-secondary fw-semibold">Company Discount (Claim Ledger)</td>
                            <td class="text-end text-secondary font-monospace">-Rs. {{ number_format($sumCompDiscAmt, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="total-row" style="background-color: #f8fafc;">
                            <td>Payable to Vendor (Net Bill)</td>
                            <td class="text-end font-monospace">Rs. {{ number_format($purchase->net_amount, 2) }}</td>
                        </tr>

                        {{-- Final Landed Parhi (Actual Final Cost per Unit) --}}
                        @if($sumCartons > 0 || $sumTotalPieces > 0)
                        <tr style="background-color: #eff6ff; border-top: 1.5px dashed #3b82f6;">
                            <td class="text-primary fw-bold" style="font-size: 11.5px;">Hamari Parhi (Landed Cost)</td>
                            <td class="text-end font-monospace fw-bold text-primary" style="font-size: 12.5px;">
                                Rs. {{ number_format($sumHamariParhiTotal, 2) }}
                            </td>
                        </tr>
                        <tr style="background-color: #eff6ff;">
                            <td class="text-primary fw-bold" style="font-size: 11.5px;">Hamari Parhi / Carton</td>
                            <td class="text-end font-monospace fw-bold text-primary" style="font-size: 12px;">
                                Rs. {{ number_format($sumCartons > 0 ? ($sumHamariParhiTotal / $sumCartons) : 0, 2) }} / Ctn
                            </td>
                        </tr>
                        <tr style="background-color: #eff6ff;">
                            <td class="text-dark fw-bold" style="font-size: 11.5px;">Hamari Parhi / Piece</td>
                            <td class="text-end font-monospace fw-bold text-dark" style="font-size: 12px;">
                                Rs. {{ number_format($sumTotalPieces > 0 ? ($sumHamariParhiTotal / $sumTotalPieces) : 0, 2) }} / Pc
                            </td>
                        </tr>
                        @endif

                        <tr>
                            <td>Paid Amount</td>
                            <td class="text-end text-success fw-bold font-monospace">Rs. {{ number_format($purchase->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Bill Due</td>
                            <td class="text-end fw-bold font-monospace text-danger">
                                Rs. {{ number_format($purchase->net_amount - $purchase->paid_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-dark">Previous Balance</td>
                            <td class="text-end text-dark font-monospace">Rs. {{ number_format($previousBalance, 2) }}</td>
                        </tr>
                        <tr style="border-top: 2px solid #0f172a;">
                            <td class="fw-bold text-danger">Total Closing Balance</td>
                            <td class="text-end fw-bold text-danger font-monospace" style="font-size: 1.05rem;">
                                Rs. {{ number_format($currentBalance, 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Bottom Actions (no-print) --}}
        <div class="mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2 no-print">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('purchase.edit', $purchase->id) }}" class="btn btn-warning px-4 py-2 fw-bold shadow-sm text-dark d-inline-flex align-items-center gap-2" style="border-radius: 8px; font-size: 0.92rem;">
                    <i class="fas fa-edit"></i> Edit Purchase Invoice
                </a>
                <button onclick="window.print()" class="btn btn-primary px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 8px; font-size: 0.92rem;">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
            </div>
            <div>
                <a href="{{ route('Purchase.home') }}" class="btn btn-outline-secondary px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2" style="border-radius: 8px; font-size: 0.92rem;">
                    <i class="fas fa-arrow-left"></i> Back to Purchases List
                </a>
            </div>
        </div>

    </div>
</body>

</html>
