<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $order->reference_number }} Receipt</title>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        body {
            background: #f5f7fb;
        }

        /* ==========================================
           RECEIPT
           Designed for 80mm thermal printer
           ========================================== */

        .receipt {
            width: 80mm;
            max-width: 80mm;
            min-height: 0;
            margin: 20px auto;
            padding: 4mm;
            background: #fff;
            color: #000;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 3mm;
        }

        .receipt-logo {
            width: 8mm;
            height: 8mm;
            margin: 0 auto 2mm;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
        }

        .receipt-title {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .receipt-subtitle {
            margin-top: 1mm;
            font-size: 10px;
        }

        /* META INFORMATION */

        .receipt-meta {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 1.2mm 3mm;

            margin: 3mm 0;
            padding: 2.5mm 0;

            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;

            font-size: 10px;
            line-height: 1.35;
        }

        .receipt-meta span {
            font-weight: 500;
        }

        .receipt-meta strong {
            font-weight: 700;
            text-align: right;
        }

        /* ITEMS */

        .receipt-items {
            width: 100%;
            padding: 1mm 0;
            font-size: 10px;
        }

        .receipt-item {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 3mm;

            padding: 1.2mm 0;
            line-height: 1.35;
        }

        .receipt-item span:first-child {
            overflow-wrap: anywhere;
        }

        .receipt-item span:last-child {
            white-space: nowrap;
            text-align: right;
        }

        /* TOTALS */

        .receipt-total {
            margin-top: 2mm;
            padding-top: 2.5mm;
            border-top: 1px dashed #000;
        }

        .receipt-line {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 3mm;

            padding: .7mm 0;

            font-size: 10px;
            line-height: 1.35;
        }

        .receipt-line span:last-child {
            text-align: right;
            white-space: nowrap;
        }

        .receipt-line.grand {
            margin-top: 2mm;
            padding-top: 2mm;

            border-top: 1px dashed #000;

            font-size: 13px;
            font-weight: 800;
        }

        /* BUTTONS - SCREEN ONLY */

        .actions {
            width: 80mm;
            margin: 12px auto 30px;

            display: flex;
            gap: 8px;
        }

        .btn {
            flex: 1;

            border: 0;
            border-radius: 12px;

            padding: 14px 16px;
            min-height: 48px;

            font-size: 15px;
            font-weight: 800;
            letter-spacing: .01em;
            cursor: pointer;

            text-decoration: none;
            text-align: center;
            transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #0f766e 0%, #10b981 100%);
            color: #fff;
            box-shadow: 0 10px 20px rgba(15, 118, 110, 0.22);
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.08);
        }

        /* ==========================================
           XPRINTER 80MM PRINT SETTINGS
           ========================================== */

        @media print {

            @page {
                size: 80mm auto;
                margin: 0;
            }

            html {
                width: 80mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            body {
                width: 80mm !important;
                margin: 0 !important;
                padding: 0 !important;

                background: #fff !important;
            }

            .receipt {
                width: 80mm !important;
                max-width: 80mm !important;

                margin: 0 !important;

                /*
                 * 4mm left + 4mm right gives
                 * approximately 72mm usable area.
                 */
                padding:
                    3mm
                    4mm
                    4mm
                    4mm !important;

                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .actions,
            .print-hidden {
                display: none !important;
            }
        }

        /* =========================================================
   80MM XPRINTER / THERMAL RECEIPT PRINT
   ========================================================= */

@media print {

    @page {
        size: 80mm 200mm;
        margin: 0;
    }

    html,
    body {
        width: 80mm !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
    }

    body {
        display: block !important;
    }

    .receipt {
        width: 80mm !important;
        max-width: 80mm !important;
        min-width: 80mm !important;

        margin: 0 !important;

        padding: 3mm 4mm 4mm 4mm !important;

        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;

        background: #ffffff !important;
    }

    .actions,
    .print-hidden {
        display: none !important;
    }

    .receipt-header,
    .receipt-meta,
    .receipt-items,
    .receipt-total {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}
    </style>
</head>

<body>

<div class="receipt">

    <div class="receipt-header">
        <div class="receipt-logo">
            {{ strtoupper(substr(trim($property?->receipt_name ?: $property?->name ?: 'H'), 0, 1)) }}
        </div>

        <div class="receipt-title">
            {{ $property?->receipt_name ?: $property?->name ?: 'Hotel' }}
        </div>

        <div class="receipt-subtitle">
            {{ $property?->receipt_header ?: 'Restaurant / Bar' }}
        </div>

        @if($property?->address)
            <div class="receipt-subtitle">
                {{ $property->address }}
            </div>
        @endif

        @if($property?->phone || $property?->email)
            <div class="receipt-subtitle">
                {{ trim(($property?->phone ?? '') . (empty($property?->phone) || empty($property?->email) ? '' : ' • ') . ($property?->email ?? '')) }}
            </div>
        @endif
    </div>


    <div class="receipt-meta">

        <span>Receipt #</span>
        <strong>{{ $order->reference_number }}</strong>

        <span>Date</span>
        <strong>
            {{ $order->completed_at?->format('M d, Y H:i')
                ?? $order->created_at?->format('M d, Y H:i') }}
        </strong>

        <span>Cashier</span>
        <strong>
            {{ $order->cashier?->name ?? 'System' }}
        </strong>

        <span>Guest</span>
        <strong>
            {{ $order->guest?->full_name ?? 'Walk-in' }}
        </strong>

        <span>Room / Table</span>
        <strong>
            {{ $order->table_reference ?? ($order->room?->room_number ? 'Room ' . $order->room->room_number : '-') }}
        </strong>

        @if($order->receipt_email)
            <span>Email</span>
            <strong>{{ $order->receipt_email }}</strong>
        @endif

    </div>


    <div class="receipt-items">

        @forelse($order->items as $item)

            <div class="receipt-item">

                <span>
                    {{ $item->quantity }} x
                    {{ $item->product_name_snapshot
                        ?? ($item->item?->name ?? 'Item') }}
                </span>

                <span>
                    UGX {{ number_format((float) $item->line_total, 0) }}
                </span>

            </div>

        @empty

            <div class="receipt-item">

                <span>No items</span>

                <span>UGX 0</span>

            </div>

        @endforelse

    </div>


    <div class="receipt-total">

        <div class="receipt-line">
            <span>Subtotal</span>

            <span>
                UGX {{ number_format((float) $order->subtotal, 0) }}
            </span>
        </div>


        <div class="receipt-line">

            <span>Discount</span>

            <span>
                UGX {{ number_format((float) $order->discount, 0) }}
            </span>

        </div>


        <div class="receipt-line">

            <span>Tax</span>

            <span>
                UGX {{ number_format((float) $order->tax, 0) }}
            </span>

        </div>


        @if(($order->service_charge ?? 0) > 0)

            <div class="receipt-line">

                <span>Service Charge</span>

                <span>
                    UGX {{ number_format((float) $order->service_charge, 0) }}
                </span>

            </div>

        @endif


        <div class="receipt-line grand">

            <span>Total</span>

            <span>
                UGX {{ number_format((float) $order->total, 0) }}
            </span>

        </div>

        @if($property?->receipt_footer)
            <div class="receipt-line" style="display:block; padding-top:2mm; text-align:center; border-top:1px dashed #000; margin-top:2mm; font-size:9px; line-height:1.5;">
                {{ $property->receipt_footer }}
            </div>
        @endif

    </div>

</div>


<div class="actions">

    <a
        href="{{ route('pos.purchases') }}"
        class="btn btn-secondary"
    >
        Purchases
    </a>

    <button
        type="button"
        class="btn btn-primary"
        onclick="window.print()"
    >
        Print Receipt
    </button>

</div>


<script>

    const params = new URLSearchParams(window.location.search);

    if (params.get('print') === '1') {

        setTimeout(() => {
            window.print();
        }, 300);

    }

</script>

</body>
</html>