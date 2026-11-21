<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Claim Voucher - {{ $claim->claim_no }}</title>
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ea580c;
            --dark-color: #0f172a;
            --border-color: #cbd5e1;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12.5px;
        }

        .invoice-container {
            max-width: 210mm;
            margin: 15px auto;
            background: #fff;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            position: relative;
        }

        .company-header {
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .company-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--dark-color);
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .document-title {
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .info-box {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 10px 12px;
            background-color: #fafafa;
            height: 100%;
        }
        .info-box-title {
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .claim-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .claim-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 11px;
            text-transform: uppercase;
            padding: 8px 6px;
            border: 1px solid var(--border-color);
            font-weight: 700;
        }
        .claim-table td {
            border: 1px solid var(--border-color);
            padding: 6px 8px;
            vertical-align: middle;
        }

        .summary-box {
            border: 1.5px solid var(--primary-color);
            border-radius: 8px;
            padding: 12px 16px;
            background: #fff7ed;
        }

        .print-btn-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        @media print {
            body { background: #fff; }
            .invoice-container { box-shadow: none; border: none; padding: 0; width: 100%; max-width: 100%; }
            .print-btn-container { display: none; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>

    <div class="print-btn-container d-flex gap-2">
        <a href="{{ route('claims.index') }}" class="btn btn-secondary btn-sm shadow">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <button onclick="window.print()" class="btn btn-danger btn-sm shadow fw-bold">
            <i class="fas fa-print me-1"></i> Print Voucher
        </button>
    </div>

    <div class="invoice-container">
        {{-- Company Header --}}
        <div class="company-header">
            <div class="company-title">INNOTECH ERP DISTRIBUTION</div>
            <div class="text-muted small">Claims, Expiries &amp; Damage Management Division</div>
        </div>

        <div class="document-title">
            <i class="fas fa-exclamation-triangle me-1"></i> Customer Claim &amp; Damage Voucher
        </div>

        {{-- Info Boxes Grid --}}
        <div class="row g-2 mb-3">
            <div class="col-4">
                <div class="info-box">
                    <div class="info-box-title">Customer Information</div>
                    <div class="fw-bold fs-6 text-dark">{{ $claim->customer->name ?? 'N/A' }}</div>
                    <div>Phone: {{ $claim->customer->phone ?? 'N/A' }}</div>
                    <div>Address: {{ $claim->customer->address ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="col-4">
                <div class="info-box">
                    <div class="info-box-title">Company / Vendor Pool</div>
                    <div class="fw-bold fs-6 text-dark">{{ $claim->vendor->name ?? 'N/A' }}</div>
                    <div>Phone: {{ $claim->vendor->phone ?? 'N/A' }}</div>
                    <div>Warehouse: {{ $claim->warehouse->warehouse_name ?? 'Damage Warehouse' }}</div>
                </div>
            </div>

            <div class="col-4">
                <div class="info-box">
                    <div class="info-box-title">Document Reference</div>
                    <div>Claim #: <strong class="font-monospace">{{ $claim->claim_no }}</strong></div>
                    <div>Date: <strong>{{ $claim->claim_date->format('d/m/Y') }}</strong></div>
                    <div>Type: <strong class="text-capitalize">{{ $claim->claim_type }}</strong></div>
                    <div>Settlement: <strong class="text-capitalize">{{ str_replace('_', ' ', $claim->settlement_type) }}</strong></div>
                </div>
            </div>
        </div>

        @if ($claim->remarks)
            <div class="alert alert-light border py-1 px-2 mb-3 small">
                <strong>Remarks / Reason:</strong> {{ $claim->remarks }}
            </div>
        @endif

        {{-- Damaged Items Table --}}
        <table class="claim-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 35%;">Item Description</th>
                    <th style="width: 15%;" class="text-center">Reason</th>
                    <th style="width: 10%;" class="text-center">Packing</th>
                    <th style="width: 10%;" class="text-center">Total Pcs</th>
                    <th style="width: 12%;" class="text-end">Claim Rate</th>
                    <th style="width: 13%;" class="text-end">Total (Rs)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($claim->items as $idx => $item)
                    <tr>
                        <td class="text-center font-monospace">{{ $idx + 1 }}</td>
                        <td>
                            <div class="fw-bold">{{ $item->product->item_name ?? 'Product' }}</div>
                            <small class="text-muted">SKU: {{ $item->product->item_code ?? 'N/A' }}</small>
                        </td>
                        <td class="text-center text-capitalize">
                            <span class="badge bg-light text-dark border">{{ $item->damage_reason }}</span>
                        </td>
                        <td class="text-center">
                            {{ $item->pieces_per_box > 1 ? '1 Ctn = ' . (int)$item->pieces_per_box . ' P' : $item->unit }}
                        </td>
                        <td class="text-center fw-bold text-danger">
                            {{ number_format($item->total_pieces, 0) }}
                        </td>
                        <td class="text-end font-monospace">
                            {{ number_format($item->rate, 2) }}
                        </td>
                        <td class="text-end fw-bold font-monospace">
                            {{ number_format($item->line_total, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Reconciliation & Totals Summary --}}
        <div class="row g-3 mt-3 align-items-center">
            <div class="col-7">
                <div class="summary-box">
                    <div class="fw-bold text-dark mb-1 d-flex align-items-center gap-1">
                        <i class="fas fa-building text-warning"></i> Company 3-Tier Discount Allowance Reconciliation:
                    </div>
                    <div class="small text-muted mb-2">
                        Vendor se milne wala 5% Damage/Claim Allowance vs Actual Claim:
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-1 small">
                        <span>Company Damage Allowance Before:</span>
                        <strong class="font-monospace">Rs {{ number_format($claim->company_allowance_before, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-1 small text-danger">
                        <span>Minus Customer Claim Adjusted:</span>
                        <strong class="font-monospace">- Rs {{ number_format($claim->claim_adjusted, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pt-1 small text-success fw-bold">
                        <span>Remaining Company Balance (Payable/Pool):</span>
                        <strong class="font-monospace">Rs {{ number_format($claim->company_remaining_balance, 2) }}</strong>
                    </div>
                </div>
            </div>

            <div class="col-5">
                <table class="table table-sm table-bordered mb-0">
                    <tr>
                        <td class="text-muted">Total Damaged Pcs:</td>
                        <td class="text-end fw-bold font-monospace">{{ (int)$claim->items->sum('total_pieces') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Customer Settlement:</td>
                        <td class="text-end fw-bold text-capitalize">{{ str_replace('_', ' ', $claim->settlement_type) }}</td>
                    </tr>
                    @if ($claim->refund_amount > 0)
                        <tr>
                            <td class="text-muted">Cash Refund Paid:</td>
                            <td class="text-end fw-bold font-monospace text-success">Rs {{ number_format($claim->refund_amount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="table-danger">
                        <td class="fw-bold text-danger">Total Claim Value:</td>
                        <td class="text-end fw-bold font-monospace text-danger fs-6">Rs {{ number_format($claim->total_claim_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Signatures --}}
        <div class="row text-center mt-5 pt-4">
            <div class="col-4">
                <div style="border-top: 1px solid #94a3b8; width: 140px; margin: 0 auto; padding-top: 4px;" class="small text-muted">
                    Prepared By
                </div>
            </div>
            <div class="col-4">
                <div style="border-top: 1px solid #94a3b8; width: 140px; margin: 0 auto; padding-top: 4px;" class="small text-muted">
                    Customer Signature
                </div>
            </div>
            <div class="col-4">
                <div style="border-top: 1px solid #94a3b8; width: 140px; margin: 0 auto; padding-top: 4px;" class="small text-muted">
                    Authorized Manager
                </div>
            </div>
        </div>
    </div>

</body>
</html>
