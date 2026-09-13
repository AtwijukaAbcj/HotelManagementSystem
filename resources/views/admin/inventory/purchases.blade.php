<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Orders</title>
    @include('admin.css')
    <style>
        .procurement-page { background: #f6f8fb; min-height: 100vh; }
        .procurement-page .page-wrapper { padding-top: 80px; }
        .procurement-container { max-width: 1440px; margin: 0 auto; padding: 28px 28px 56px; }
        .procurement-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15, 23, 42, .05); }
        .summary-value { font-size: 1.65rem; font-weight: 700; color: #172033; }
        .summary-label { color: #697586; font-size: .78rem; text-transform: uppercase; letter-spacing: .06em; }
        .status-badge { border-radius: 999px; padding: .42rem .7rem; font-size: .75rem; font-weight: 600; }
        .status-draft { background: #eef2f7; color: #536174; }
        .status-pending_approval { background: #fff4d6; color: #9a6700; }
        .status-approved, .status-ordered { background: #e4efff; color: #1d4ed8; }
        .status-partially_received { background: #e9e7ff; color: #5b47b7; }
        .status-received { background: #ddf7ed; color: #087443; }
        .status-cancelled { background: #ffe5e5; color: #b42318; }
        .empty-icon { width: 58px; height: 58px; border-radius: 16px; background: #eaf2ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .empty-state { padding: 38px 24px; }
        @media (max-width: 767px) { .procurement-container { padding: 24px 16px 40px; } .procurement-page .page-wrapper { padding-top: 72px; } }
    </style>
</head>
<body>
<div class="main-wrapper procurement-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper">
        <main class="procurement-container">
            <div class="d-flex justify-content-between align-items-start flex-wrap mb-4" style="gap: 18px;">
                <div>
                    <h3 class="page-title mb-1">Purchase Orders</h3>
                    <p class="text-muted mb-0">Manage procurement, approvals, deliveries, and stock receiving.</p>
                </div>
                <a href="{{ route('purchases.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> New Purchase Order</a>
            </div>
            @if(session('message'))<div class="alert alert-success procurement-card">{{ session('message') }}</div>@endif
            <div class="row mb-4">
                @foreach([
                    ['Total Orders', $summary['total'], 'fa-file-invoice', 'primary'],
                    ['Pending Approval', $summary['pending'], 'fa-hourglass-half', 'warning'],
                    ['Awaiting Delivery', $summary['awaiting'], 'fa-truck', 'info'],
                    ['Received', $summary['received'], 'fa-check-circle', 'success'],
                    ['Total Spend', 'UGX ' . number_format($summary['spend'], 0), 'fa-coins', 'dark'],
                ] as [$label, $value, $icon, $color])
                    <div class="col-xl col-md-4 col-6 mb-3 mb-xl-0"><div class="card procurement-card h-100"><div class="card-body"><div class="d-flex justify-content-between align-items-start"><span class="summary-label">{{ $label }}</span><i class="fas {{ $icon }} text-{{ $color }}"></i></div><div class="summary-value mt-2">{{ $value }}</div></div></div></div>
                @endforeach
            </div>
            <div class="card procurement-card mb-4"><div class="card-body"><form method="GET" action="{{ route('purchases.index') }}"><div class="row align-items-end">
                <div class="col-lg-3 mb-3 mb-lg-0"><label class="small font-weight-bold">Search</label><input name="search" value="{{ request('search') }}" class="form-control" placeholder="PO, supplier or invoice..."></div>
                <div class="col-lg-2 mb-3 mb-lg-0"><label class="small font-weight-bold">Supplier</label><select name="supplier" class="form-control"><option value="">All suppliers</option>@foreach($suppliers as $supplier)<option value="{{ $supplier }}" @selected(request('supplier') === $supplier)>{{ $supplier }}</option>@endforeach</select></div>
                <div class="col-lg-2 mb-3 mb-lg-0"><label class="small font-weight-bold">Status</label><select name="status" class="form-control"><option value="">All statuses</option>@foreach(['draft', 'pending_approval', 'approved', 'ordered', 'partially_received', 'received', 'cancelled'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>@endforeach</select></div>
                <div class="col-lg-2 mb-3 mb-lg-0"><label class="small font-weight-bold">From</label><input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control"></div>
                <div class="col-lg-2 mb-3 mb-lg-0"><label class="small font-weight-bold">To</label><input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control"></div>
                <div class="col-lg-1"><button class="btn btn-outline-primary w-100" type="submit"><i class="fas fa-search"></i></button></div>
            </div></form></div></div>
            <div class="card procurement-card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0 align-middle"><thead class="thead-light"><tr><th>PO Number</th><th>Supplier</th><th>Items</th><th>Total Amount</th><th>Order Status</th><th>Payment</th><th>Purchase Date</th><th>Expected Delivery</th><th>Actions</th></tr></thead><tbody>
                @forelse($purchases as $purchase)
                    <tr><td class="font-weight-bold">{{ $purchase->po_number ?: '#'.$purchase->id }}</td><td>{{ $purchase->supplier }}</td><td>{{ $purchase->lines->count() ?: 1 }}</td><td>UGX {{ number_format($purchase->display_total, 0) }}</td><td><span class="status-badge status-{{ $purchase->status }}">{{ ucwords(str_replace('_', ' ', $purchase->status)) }}</span></td><td>{{ ucfirst($purchase->payment_status ?: 'unpaid') }}</td><td>{{ optional($purchase->purchase_date)->format('d M Y') ?: $purchase->created_at->format('d M Y') }}</td><td>{{ optional($purchase->expected_delivery_date)->format('d M Y') ?: '-' }}</td><td class="text-nowrap"><a href="{{ route('purchases.history', $purchase) }}" class="btn btn-sm btn-link">History</a>@if($purchase->status === 'pending_approval')<form method="POST" action="{{ route('purchases.approve', $purchase) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-success" type="submit">Approve</button></form><button class="btn btn-sm btn-outline-danger" type="button" data-toggle="modal" data-target="#reject-{{ $purchase->id }}">Reject</button>@elseif(!in_array($purchase->status, ['received', 'cancelled']))<a href="{{ route('purchases.receive', $purchase) }}" class="btn btn-sm btn-outline-primary">Receive</a>@endif</td></tr>
                    @if($purchase->status === 'pending_approval')<div class="modal fade" id="reject-{{ $purchase->id }}" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST" action="{{ route('purchases.reject', $purchase) }}">@csrf<div class="modal-header"><h5 class="modal-title">Reject purchase order</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div><div class="modal-body"><label class="font-weight-bold">Reason</label><textarea name="comment" class="form-control" required></textarea></div><div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button><button class="btn btn-danger" type="submit">Reject order</button></div></form></div></div></div>@endif
                @empty
                    <tr><td colspan="9"><div class="text-center empty-state"><div class="empty-icon mb-3"><i class="fas fa-file-invoice"></i></div><h5>No purchase orders yet</h5><p class="text-muted">Create your first order to start tracking procurement.</p><a href="{{ route('purchases.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Create First Purchase Order</a></div></td></tr>
                @endforelse
            </tbody></table></div>@if($purchases->hasPages())<div class="p-3">{{ $purchases->links() }}</div>@endif</div></div>
        </main>
    </div>
</div>
@include('admin.script')
</body>
</html>
