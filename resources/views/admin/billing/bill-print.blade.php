<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $billing->receipt_number ?: 'BILL-' . str_pad((string) $billing->id, 6, '0', STR_PAD_LEFT) }} Receipt</title>
    @include('admin.css')
    <style>
        body { margin: 0; background: #f3f7f9; color: #12233f; font-family: Arial, Helvetica, sans-serif; }
        .bill-print-page { min-height: 100vh; padding: 28px; }
        .bill-sheet { max-width: 760px; margin: 0 auto; padding: 42px; background: #fff; border: 1px solid #dce5ec; border-radius: 8px; }
        .bill-header { display: flex; justify-content: space-between; gap: 20px; border-bottom: 1px solid #dce5ec; padding-bottom: 20px; }
        .bill-brand { color: #009688; font-size: 24px; font-weight: 800; }
        .bill-number { text-align: right; font-weight: 700; }
        .bill-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 24px; margin: 28px 0; }
        .bill-label { display: block; color: #667085; font-size: 11px; text-transform: uppercase; }
        .bill-value { display: block; margin-top: 4px; font-weight: 700; }
        .bill-total { display: flex; justify-content: space-between; margin-top: 24px; padding: 18px; background: #e5f4f1; color: #00796f; font-size: 20px; font-weight: 800; }
        .print-actions { display: flex; justify-content: space-between; max-width: 760px; margin: 0 auto 16px; }
        @media print { .bill-print-page { padding: 0; background: #fff; } .bill-sheet { border: 0; border-radius: 0; max-width: none; padding: 0; } .print-actions { display: none !important; } }
    </style>
</head>
<body>
    <div class="bill-print-page">
        <div class="print-actions">
            <a href="{{ url('/billing_report') }}" class="btn btn-outline-secondary">Back</a>
            <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fas fa-print mr-1"></i> Print receipt</button>
        </div>
        <article class="bill-sheet">
            <div class="bill-header">
                <div>
                    <div class="bill-brand">HOTEL</div>
                    <div>Hotel Management System</div>
                </div>
                <div class="bill-number">{{ $billing->receipt_number ?: 'BILL-' . str_pad((string) $billing->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="bill-meta">
                <div><span class="bill-label">Bill To</span><span class="bill-value">{{ $billing->name }}</span></div>
                <div><span class="bill-label">Email</span><span class="bill-value">{{ $billing->email ?: 'Not provided' }}</span></div>
                <div><span class="bill-label">Room</span><span class="bill-value">{{ $billing->room_number }} ({{ $billing->room_type }})</span></div>
                <div><span class="bill-label">Date</span><span class="bill-value">{{ $billing->billing_date }} {{ $billing->billing_time }}</span></div>
                <div><span class="bill-label">Stay</span><span class="bill-value">{{ $billing->no_of_days_stay }} day(s)</span></div>
                <div><span class="bill-label">Payment Method</span><span class="bill-value">{{ ucwords(str_replace('_', ' ', $billing->transaction_type)) }}</span></div>
                <div><span class="bill-label">Email Status</span><span class="bill-value">{{ ucfirst(str_replace('_', ' ', $billing->receipt_email_status ?? 'not sent')) }}</span></div>
            </div>
            <div class="bill-total"><span>Total</span><span>UGX {{ number_format((float) $billing->total, 2) }}</span></div>
            <p style="margin-top:28px;color:#667085;">Thank you for staying with us.</p>
        </article>
    </div>
</body>
</html>
