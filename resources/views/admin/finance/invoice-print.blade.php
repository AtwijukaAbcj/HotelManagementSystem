<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    @include('admin.css')
    <style>
        .invoice-print-page { background: #f3f7f9; min-height: 100vh; padding: 32px; }
        .invoice-sheet { background: #fff; border: 1px solid #dce5ec; border-radius: 8px; margin: 0 auto; max-width: 820px; padding: 48px; }
        .invoice-brand { color: #009688; font-size: 1.5rem; font-weight: 700; letter-spacing: .04em; }
        .invoice-meta { border-bottom: 1px solid #dce5ec; border-top: 1px solid #dce5ec; margin: 32px 0; padding: 20px 0; }
        .invoice-total { background: #e5f4f1; color: #00796f; font-size: 1.3rem; font-weight: 700; padding: 18px 20px; }
        @media print {
            @page { margin: 14mm; }
            body { background: #fff; }
            .invoice-print-page { padding: 0; }
            .invoice-sheet { border: 0; border-radius: 0; box-shadow: none; max-width: none; padding: 0; }
            .print-actions { display: none !important; }
        }
        @media (max-width: 767px) {
            .invoice-print-page { padding: 16px; }
            .invoice-sheet { padding: 24px; }
        }
    </style>
</head>
<body>
    <div class="invoice-print-page">
        <div class="print-actions d-flex justify-content-between mb-3">
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Back</a>
            <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fas fa-print mr-1"></i> Print invoice</button>
        </div>
        <article class="invoice-sheet">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="invoice-brand">HOTEL</div>
                    <div class="text-muted">Hotel Management System</div>
                </div>
                <div class="text-right">
                    <h1 class="mb-1">INVOICE</h1>
                    <div class="text-muted">{{ $invoice->invoice_number }}</div>
                </div>
            </div>

            <div class="invoice-meta row">
                <div class="col-sm-6">
                    <small class="text-muted d-block">BILL TO</small>
                    <strong>{{ $invoice->guest_name }}</strong>
                </div>
                <div class="col-sm-3">
                    <small class="text-muted d-block">DATE</small>
                    <strong>{{ $invoice->created_at->format('d M Y') }}</strong>
                </div>
                <div class="col-sm-3">
                    <small class="text-muted d-block">STATUS</small>
                    <strong>{{ ucfirst($invoice->status) }}</strong>
                </div>
            </div>

            <table class="table mb-4 no-datatable">
                <thead>
                    <tr><th>Description</th><th class="text-right">Amount</th></tr>
                </thead>
                <tbody>
                    <tr><td>Hotel services</td><td class="text-right">${{ number_format($invoice->amount, 2) }}</td></tr>
                </tbody>
            </table>

            <div class="invoice-total d-flex justify-content-between">
                <span>Total due</span>
                <span>${{ number_format($invoice->amount, 2) }}</span>
            </div>
            <div class="mt-4"><strong>Payment method:</strong> {{ str_replace('_', ' ', $invoice->payment_method) }}</div>
            @if ($invoice->notes)
                <div class="mt-3 text-muted"><strong>Notes:</strong> {{ $invoice->notes }}</div>
            @endif
            <p class="text-muted mt-5 mb-0">Thank you for staying with us.</p>
        </article>
    </div>
</body>
</html>