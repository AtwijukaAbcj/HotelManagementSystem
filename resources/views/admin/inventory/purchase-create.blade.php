<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Purchase Order</title>
    @include('admin.css')
    <style>
        .purchase-form-page { background: #f6f8fb; min-height: 100vh; }
        .purchase-form-page .page-wrapper { padding-top: 80px; }
        .purchase-container { max-width: 1180px; margin: 0 auto; padding: 34px 28px 56px; }
        .purchase-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        .line-total { font-weight: 600; white-space: nowrap; }
        @media (max-width: 767px) { .purchase-form-page .page-wrapper { padding-top: 72px; } .purchase-container { padding: 24px 16px 40px; } }
    </style>
</head>
<body>
<div class="main-wrapper purchase-form-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="purchase-container">
        <div class="d-flex justify-content-between align-items-start mb-4"><div><h3 class="page-title mb-1">New Purchase Order</h3><p class="text-muted mb-0">Create a draft or submit procurement for approval.</p></div><a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
        @if($errors->any())<div class="alert alert-danger purchase-card"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('purchases.store') }}" id="purchase-form">@csrf
            <div class="card purchase-card mb-4"><div class="card-body"><h5 class="mb-4">Purchase information</h5><div class="row">
                <div class="col-md-6 mb-3"><label class="font-weight-bold">Supplier</label><input list="supplier-options" name="supplier" value="{{ old('supplier') }}" class="form-control" required placeholder="Search or enter supplier"><datalist id="supplier-options">@foreach($suppliers as $supplier)<option value="{{ $supplier }}">@endforeach</datalist></div>
                <div class="col-md-3 mb-3"><label class="font-weight-bold">Purchase date</label><input type="date" name="purchase_date" value="{{ old('purchase_date', now()->format('Y-m-d')) }}" class="form-control" required></div>
                <div class="col-md-3 mb-3"><label class="font-weight-bold">Expected delivery</label><input type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}" class="form-control"></div>
                <div class="col-md-3 mb-3"><label class="font-weight-bold">Warehouse / store</label><input name="warehouse" value="{{ old('warehouse', 'Main Store') }}" class="form-control" required></div>
                <div class="col-md-3 mb-3"><label class="font-weight-bold">Invoice reference</label><input name="invoice_reference" value="{{ old('invoice_reference') }}" class="form-control"></div>
                <div class="col-md-3 mb-3"><label class="font-weight-bold">Payment terms</label><select name="payment_terms" class="form-control"><option value="Cash">Cash</option><option value="Net 15">Net 15</option><option value="Net 30">Net 30</option><option value="Net 60">Net 60</option></select></div>
                <div class="col-md-3 mb-3"><label class="font-weight-bold">Notes</label><input name="notes" value="{{ old('notes') }}" class="form-control"></div>
            </div></div></div>
            <div class="card purchase-card mb-4"><div class="card-body"><div class="d-flex justify-content-between align-items-center mb-3"><h5 class="mb-0">Line items</h5><button type="button" class="btn btn-sm btn-outline-primary" id="add-line"><i class="fas fa-plus mr-1"></i> Add item</button></div><div class="table-responsive"><table class="table align-middle" id="line-items"><thead class="thead-light"><tr><th style="min-width:230px">Product</th><th>Qty</th><th>Unit</th><th>Unit cost (UGX)</th><th>Tax %</th><th>Discount (UGX)</th><th>Line total</th><th></th></tr></thead><tbody></tbody></table></div><div id="empty-lines" class="text-center text-muted py-4">Add products to this purchase order.</div></div></div>
            <div class="card purchase-card"><div class="card-body"><div class="row justify-content-end"><div class="col-md-5"><div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong id="subtotal">UGX 0</strong></div><div class="d-flex justify-content-between mb-2"><span>Tax</span><strong id="tax-total">UGX 0</strong></div><div class="d-flex justify-content-between mb-2"><span>Discount</span><strong id="discount-total">UGX 0</strong></div><hr><div class="d-flex justify-content-between"><strong>Grand Total</strong><strong id="grand-total" class="text-primary">UGX 0</strong></div></div></div><div class="d-flex justify-content-end mt-4" style="gap:10px"><a href="{{ route('purchases.index') }}" class="btn btn-light">Cancel</a><button name="action" value="draft" class="btn btn-outline-primary">Save as Draft</button><button name="action" value="submit" class="btn btn-primary">Submit for Approval</button></div></div></div>
        </form>
    </main></div>
</div>
@include('admin.script')
<script>
const products = @json($items);
let lineIndex = 0;
const money = value => 'UGX ' + Math.round(value).toLocaleString();
function addLine() {
    const tbody = document.querySelector('#line-items tbody');
    const row = document.createElement('tr');
    row.dataset.index = lineIndex;
    row.innerHTML = `<td><select name="items[${lineIndex}][inventory_item_id]" class="form-control product" required><option value="">Select product</option>${products.map(p => `<option value="${p.id}" data-unit="${p.unit}" data-cost="${p.unit_price}">${p.name}</option>`).join('')}</select></td><td><input name="items[${lineIndex}][quantity]" type="number" min=".01" step=".01" value="1" class="form-control qty" required></td><td><input name="items[${lineIndex}][unit]" class="form-control unit" readonly required></td><td><input name="items[${lineIndex}][unit_cost]" type="number" min="0" step=".01" class="form-control cost" required></td><td><input name="items[${lineIndex}][tax_rate]" type="number" min="0" step=".01" value="0" class="form-control tax"></td><td><input name="items[${lineIndex}][discount]" type="number" min="0" step=".01" value="0" class="form-control discount"></td><td class="line-total">UGX 0</td><td><button type="button" class="btn btn-sm btn-link text-danger remove-line"><i class="fas fa-times"></i></button></td>`;
    tbody.appendChild(row);
    lineIndex++;
    document.querySelector('#empty-lines').style.display = 'none';
    bindRow(row);
}
function bindRow(row) {
    row.querySelector('.product').addEventListener('change', event => { const option = event.target.selectedOptions[0]; row.querySelector('.unit').value = option.dataset.unit || ''; row.querySelector('.cost').value = option.dataset.cost || 0; calculate(); });
    row.querySelectorAll('input').forEach(input => input.addEventListener('input', calculate));
    row.querySelector('.remove-line').addEventListener('click', () => { row.remove(); calculate(); if (!document.querySelector('#line-items tbody').children.length) document.querySelector('#empty-lines').style.display = 'block'; });
}
function calculate() { let subtotal = 0, tax = 0, discount = 0; document.querySelectorAll('#line-items tbody tr').forEach(row => { const quantity = +(row.querySelector('.qty').value || 0), cost = +(row.querySelector('.cost').value || 0), taxRate = +(row.querySelector('.tax').value || 0), requestedDiscount = +(row.querySelector('.discount').value || 0), base = quantity * cost, lineDiscount = Math.min(requestedDiscount, base), lineTax = (base - lineDiscount) * taxRate / 100; subtotal += base; discount += lineDiscount; tax += lineTax; row.querySelector('.line-total').textContent = money(base - lineDiscount + lineTax); }); document.querySelector('#subtotal').textContent = money(subtotal); document.querySelector('#tax-total').textContent = money(tax); document.querySelector('#discount-total').textContent = money(discount); document.querySelector('#grand-total').textContent = money(subtotal - discount + tax); }
document.querySelector('#add-line').addEventListener('click', addLine);
addLine();
</script>
</body>
</html>
