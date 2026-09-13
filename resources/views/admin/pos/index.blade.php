<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Restaurant / Bar POS</title>
    @include('admin.css')
    <style>
        .pos-shell { background: #f8fafc; min-height: 100vh; }
        .pos-panel { background: white; border-radius: 20px; box-shadow: 0 12px 28px rgba(15, 23, 42, .06); }
        .menu-item { border: 1px solid #e2e8f0; border-radius: 16px; padding: 16px; margin-bottom: 12px; transition: .2s ease; }
        .menu-item:hover { border-color: #60a5fa; box-shadow: 0 10px 22px rgba(59, 130, 246, .12); }
        .bill-line { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #edf2f7; }
        .pos-empty { padding: 48px 24px; }
        .pos-empty-icon { width: 58px; height: 58px; border-radius: 16px; background: #eaf2ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; }
    </style>
</head>
<body>
<div class="main-wrapper pos-shell">
    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="page-title mb-1">Restaurant / Bar POS</h3>
                    <p class="text-muted mb-0">Fast sales for guest services and in-house beverage orders.</p>
                </div>
                <button form="pos-order-form" class="btn btn-primary" type="submit" {{ $items->isEmpty() ? 'disabled' : '' }}>Complete sale</button>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="pos-panel p-4">
                        <div class="row">
                            @forelse($items as $item)
                                <div class="col-md-4">
                                    <div class="menu-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $item->name }}</strong>
                                                <div class="text-muted small">{{ $item->category }}</div>
                                            </div>
                                            <span class="badge badge-success">{{ $item->current_stock }} left</span>
                                        </div>
                                        <div class="mt-3">
                                            <span class="text-primary font-weight-bold">UGX {{ number_format($item->unit_price, 0) }}</span>
                                            <label class="small text-muted d-block mt-2 mb-1">Quantity</label>
                                            <input form="pos-order-form" type="number" name="items[{{ $item->id }}][quantity]" class="form-control form-control-sm" min="0" max="{{ $item->current_stock }}" step="0.01" value="0">
                                            <input form="pos-order-form" type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center pos-empty">
                                    <div class="pos-empty-icon mb-3"><i class="fas fa-cash-register"></i></div>
                                    <h5>No active POS items</h5>
                                    <p class="text-muted mb-3">Add active inventory items before opening a sale.</p>
                                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-primary"><i class="fas fa-boxes mr-1"></i> Add inventory item</a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="pos-panel p-4">
                        <h5 class="mb-3">New order</h5>
                        <p class="text-muted small">{{ $items->isEmpty() ? 'Add inventory items to enable checkout.' : 'Enter a quantity above for each item, then complete the sale.' }}</p>
                        <form id="pos-order-form" method="POST" action="{{ route('pos.store') }}">
                            @csrf
                            @if($errors->any())<div class="alert alert-danger small">{{ $errors->first() }}</div>@endif
                            <button id="process-payment" class="btn btn-primary mt-4 w-100" type="submit" {{ $items->isEmpty() ? 'disabled' : '' }}>Process payment</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="pos-panel p-4 mt-4">
                <h5 class="mb-3">Recent sales</h5>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Order</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->items->sum('quantity') }}</td>
                                    <td>UGX {{ number_format($order->total, 0) }}</td>
                                    <td><span class="badge badge-success">{{ ucfirst($order->status) }}</span></td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No sales recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.script')
<script>
    const quantityInputs = document.querySelectorAll('#pos-order-form input[type="number"]');
    const processPayment = document.querySelector('#process-payment');
    function syncCheckoutState() {
        if (!processPayment) return;
        processPayment.disabled = !Array.from(quantityInputs).some(input => Number(input.value) > 0);
    }
    quantityInputs.forEach(input => input.addEventListener('input', syncCheckoutState));
    syncCheckoutState();
</script>
</body>
</html>
