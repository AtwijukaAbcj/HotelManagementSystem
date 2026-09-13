<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Receive Purchase Order</title>@include('admin.css')</head>
<body>
<style>.purchase-receive-page .page-wrapper { padding-top: 80px; } @media (max-width: 767px) { .purchase-receive-page .page-wrapper { padding-top: 72px; } }</style>
<div class="main-wrapper purchase-receive-page" style="background:#f6f8fb;min-height:100vh">@include('admin.header')@include('admin.sidebar')<div class="page-wrapper"><main class="container-fluid" style="max-width:1180px;padding:34px 28px 56px">
    <div class="d-flex justify-content-between align-items-start mb-4"><div><h3 class="page-title mb-1">Receive {{ $purchase->po_number ?: '#'.$purchase->id }}</h3><p class="text-muted mb-0">Record only quantities physically delivered. Stock updates after saving.</p></div><a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('purchases.receive.store', $purchase) }}">@csrf<div class="card border-0 shadow-sm" style="border-radius:12px"><div class="card-body"><div class="table-responsive"><table class="table align-middle"><thead class="thead-light"><tr><th>Product</th><th>Ordered</th><th>Already received</th><th>Receive now</th></tr></thead><tbody>@foreach($purchase->lines as $line)<tr><td>{{ $line->item->name }}</td><td>{{ $line->quantity }} {{ $line->unit }}</td><td>{{ $line->received_quantity }} {{ $line->unit }}</td><td><input type="number" name="received[{{ $line->id }}]" class="form-control" min="0" max="{{ $line->quantity - $line->received_quantity }}" step=".01" value="0"></td></tr>@endforeach</tbody></table></div><div class="d-flex justify-content-end mt-3"><button class="btn btn-primary" type="submit">Save Received Items</button></div></div></div></form>
</main></div></div>
@include('admin.script')
</body>
</html>
