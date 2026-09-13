<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    @include('admin.css')
    <style>
        .finance-page { background: #f6f8fb; min-height: 100vh; }
        .finance-page .page-wrapper { padding-top: 80px; }
        .finance-container { max-width: 1320px; margin: 0 auto; padding: 30px 28px 56px; }
        .finance-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        .metric-label { color: #697586; font-size: .76rem; text-transform: uppercase; letter-spacing: .06em; }
        .metric-value { color: #172033; font-size: 1.55rem; font-weight: 700; }
        @media (max-width: 767px) { .finance-page .page-wrapper { padding-top: 72px; } .finance-container { padding: 24px 16px 40px; } }
    </style>
</head>
<body>
<div class="main-wrapper finance-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="finance-container">
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-4" style="gap:16px"><div><h3 class="page-title mb-1">Payments</h3><p class="text-muted mb-0">Record and reconcile cash, card, bank, mobile money, and online payments.</p></div></div>
        @if(session('message'))<div class="alert alert-success finance-card">{{ session('message') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger finance-card"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="row mb-4"><div class="col-md-4 mb-3 mb-md-0"><div class="card finance-card h-100"><div class="card-body"><div class="d-flex justify-content-between"><span class="metric-label">Completed value</span><i class="fas fa-coins text-success"></i></div><div class="metric-value mt-2">UGX {{ number_format($summary['total'], 0) }}</div></div></div></div><div class="col-md-4 mb-3 mb-md-0"><div class="card finance-card h-100"><div class="card-body"><div class="d-flex justify-content-between"><span class="metric-label">Received today</span><i class="fas fa-calendar-day text-primary"></i></div><div class="metric-value mt-2">UGX {{ number_format($summary['today'], 0) }}</div></div></div></div><div class="col-md-4"><div class="card finance-card h-100"><div class="card-body"><div class="d-flex justify-content-between"><span class="metric-label">Transactions</span><i class="fas fa-receipt text-info"></i></div><div class="metric-value mt-2">{{ $summary['count'] }}</div></div></div></div></div>
        <div class="card finance-card mb-4"><div class="card-body"><h5 class="mb-4">Record payment</h5><form method="POST" action="{{ route('payments.store') }}">@csrf<div class="row"><div class="col-md-3 mb-3"><label class="font-weight-bold">Invoice</label><select name="invoice_id" class="form-control"><option value="">Unlinked payment</option>@foreach($invoices as $invoice)<option value="{{ $invoice->id }}">{{ $invoice->invoice_number }} - {{ $invoice->guest_name }}</option>@endforeach</select></div><div class="col-md-3 mb-3"><label class="font-weight-bold">Payer name</label><input name="payer_name" class="form-control" required></div><div class="col-md-2 mb-3"><label class="font-weight-bold">Reference</label><input name="reference" class="form-control" placeholder="PMT-1001" required></div><div class="col-md-2 mb-3"><label class="font-weight-bold">Amount (UGX)</label><input name="amount" type="number" min=".01" step=".01" class="form-control" required></div><div class="col-md-2 mb-3"><label class="font-weight-bold">Method</label><select name="method" class="form-control" required><option value="cash">Cash</option><option value="card">Card</option><option value="bank_transfer">Bank transfer</option><option value="mobile_money">Mobile money</option><option value="online">Online</option></select></div><div class="col-md-3 mb-3"><label class="font-weight-bold">Paid at</label><input name="paid_at" type="datetime-local" value="{{ now()->format('Y-m-d\\TH:i') }}" class="form-control" required></div><div class="col-md-7 mb-3"><label class="font-weight-bold">Notes</label><input name="notes" class="form-control"></div><div class="col-md-2 mb-3 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit">Record payment</button></div></div></form></div></div>
        <div class="card finance-card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0 align-middle"><thead class="thead-light"><tr><th>Reference</th><th>Payer</th><th>Invoice</th><th>Amount</th><th>Method</th><th>Status</th><th>Paid at</th></tr></thead><tbody>@forelse($payments as $payment)<tr><td class="font-weight-bold">{{ $payment->reference }}</td><td>{{ $payment->payer_name }}</td><td>{{ $payment->invoice->invoice_number ?? 'Unlinked' }}</td><td>UGX {{ number_format($payment->amount, 0) }}</td><td>{{ ucwords(str_replace('_', ' ', $payment->method)) }}</td><td><span class="badge badge-success">{{ ucfirst($payment->status) }}</span></td><td>{{ $payment->paid_at->format('d M Y H:i') }}</td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-5"><i class="fas fa-money-bill-wave fa-2x mb-3 text-primary"></i><br>No payment transactions recorded yet.</td></tr>@endforelse</tbody></table></div>@if($payments->hasPages())<div class="p-3">{{ $payments->links() }}</div>@endif</div></div>
    </main></div>
</div>
@include('admin.script')
</body>
</html>
