<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment {{ $payment->reference }}</title>
    @include('admin.css')
    <style>
        body {
            margin: 0;
            background: #f2f5f7;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
        }

        .payment-print-page {
            min-height: 100vh;
            padding: 28px;
            background: #f2f5f7;
        }

        .payment-sheet {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #dfe6ec;
            border-radius: 12px;
            box-shadow: 0 12px 34px rgba(15, 23, 42, 0.06);
            padding: 18mm 18mm 12mm;
        }

        .payment-header {
            text-align: center;
            border-bottom: 1px solid #dfe6ec;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .payment-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #e7f6f4;
            color: #009688;
            font-weight: 800;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .payment-brand {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #0f172a;
            text-transform: uppercase;
        }

        .payment-subtitle {
            margin-top: 4px;
            font-size: 11px;
            color: #4b5563;
            line-height: 1.5;
        }

        .payment-doc-title {
            text-align: center;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin: 14px 0 12px;
            color: #0f172a;
        }

        .payment-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 18px;
            margin-bottom: 12px;
            font-size: 11px;
            line-height: 1.6;
        }

        .payment-meta .label {
            display: block;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 9px;
            font-weight: 700;
        }

        .payment-meta .value {
            display: block;
            color: #111827;
            font-weight: 700;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 11px;
        }

        .payment-table th,
        .payment-table td {
            padding: 9px 8px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        .payment-table th {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
            background: #f9fafb;
        }

        .payment-table td.amount,
        .payment-table th.amount {
            text-align: right;
        }

        .payment-total {
            margin-top: 12px;
            margin-left: auto;
            width: 72%;
            font-size: 12px;
        }

        .payment-total-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            border-top: 1px solid #e5e7eb;
        }

        .payment-total-row.grand {
            font-weight: 800;
            font-size: 16px;
            border-top: 2px solid #0f172a;
            margin-top: 4px;
            padding-top: 10px;
        }

        .payment-signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-top: 22px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .signature-box {
            min-height: 64px;
            border-top: 1px solid #111827;
            padding-top: 8px;
            font-size: 10px;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .payment-footer {
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px dashed #d1d5db;
            text-align: center;
            color: #374151;
            font-size: 10px;
            line-height: 1.6;
        }

        .print-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        @media print {
            @page {
                size: A5 portrait;
                margin: 10mm;
            }

            body {
                background: #fff;
            }

            .print-actions {
                display: none !important;
            }

            .payment-print-page {
                padding: 0;
                background: #fff;
            }

            .payment-sheet {
                max-width: none;
                width: auto;
                margin: 0;
                border: 0;
                border-radius: 0;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="payment-print-page">
        <div class="print-actions">
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Back</a>
            <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fas fa-print mr-1"></i> Print receipt</button>
        </div>

        <article class="payment-sheet">
            <div class="payment-header">
                <div class="payment-logo">
                    {{ strtoupper(substr(trim($property?->receipt_name ?: $property?->name ?: 'H'), 0, 1)) }}
                </div>

                <div class="payment-brand">
                    {{ $property?->receipt_name ?: $property?->name ?: 'Hotel' }}
                </div>

                <div class="payment-subtitle">
                    {{ $property?->receipt_header ?: 'Hotel Management System' }}
                    @if($property?->address)
                        <br>{{ $property->address }}
                    @endif
                    @if($property?->phone || $property?->email)
                        <br>{{ trim(($property?->phone ?? '') . (empty($property?->phone) || empty($property?->email) ? '' : ' • ') . ($property?->email ?? '')) }}
                    @endif
                </div>
            </div>

            <div class="payment-doc-title">Invoice Payment Receipt</div>

            <div class="payment-meta">
                <div>
                    <span class="label">Receipt</span>
                    <span class="value">{{ $payment->reference }}</span>
                </div>
                <div>
                    <span class="label">Date</span>
                    <span class="value">{{ $payment->paid_at ? $payment->paid_at->format('d M Y H:i') : now()->format('d M Y H:i') }}</span>
                </div>
                <div>
                    <span class="label">Customer / Payer</span>
                    <span class="value">{{ $payment->payer_name }}</span>
                </div>
                @if($payment->payer_email)
                    <div>
                        <span class="label">Receipt email</span>
                        <span class="value">{{ $payment->payer_email }}</span>
                    </div>
                @endif
                <div>
                    <span class="label">Method</span>
                    <span class="value">{{ ucwords(str_replace('_', ' ', $payment->method)) }}</span>
                </div>
                <div>
                    <span class="label">Invoice / Bill</span>
                    <span class="value">{{ $payment->invoice?->invoice_number ?? 'Unlinked' }}</span>
                </div>
                <div>
                    <span class="label">Status</span>
                    <span class="value">{{ ucfirst($payment->status) }}</span>
                </div>
                <div>
                    <span class="label">Email status</span>
                    <span class="value">{{ ucfirst(str_replace('_', ' ', $payment->receipt_email_status ?? 'not sent')) }}</span>
                </div>
            </div>

            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="amount">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $payment->notes ?: 'Payment received against invoice ' . ($payment->invoice?->invoice_number ?? '') }}</td>
                        <td class="amount">UGX {{ number_format($payment->amount, 0) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="payment-total">
                <div class="payment-total-row">
                    <span>Subtotal</span>
                    <span>UGX {{ number_format($payment->amount, 0) }}</span>
                </div>
                <div class="payment-total-row grand">
                    <span>Total</span>
                    <span>UGX {{ number_format($payment->amount, 0) }}</span>
                </div>
            </div>

            <div class="payment-signatures">
                <div>
                    <div class="signature-box">Customer Signature</div>
                </div>
                <div>
                    <div class="signature-box">Authorized Signature</div>
                </div>
            </div>

            <div class="payment-footer">
                @if($property?->receipt_footer)
                    {{ $property->receipt_footer }}
                @else
                    Thank you for your payment. This receipt confirms payment received against invoice {{ $payment->invoice?->invoice_number ?? $payment->reference }}.
                @endif
            </div>
        </article>
    </div>
</body>
</html>
