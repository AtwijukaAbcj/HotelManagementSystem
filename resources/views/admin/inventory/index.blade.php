<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Inventory Management</title>
    @include('admin.css')
</head>
<body>
<div class="main-wrapper">
    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="page-title mb-1">Inventory</h3>
                    <p class="text-muted mb-0">Track stock, reorder levels, and item status.</p>
                </div>
                <div class="d-flex" style="gap: 8px;">
                    <a href="{{ route('inventory.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Add item</a>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-primary">Suppliers</a>
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary">Stock movements</a>
                </div>
            </div>

            @if($lowStockItems->isNotEmpty())
                <div class="alert alert-warning d-flex align-items-center mb-4"><i class="fas fa-exclamation-triangle mr-2"></i><strong>{{ $lowStockItems->count() }} item(s) need restocking.</strong><span class="ml-1">Review the highlighted inventory rows below.</span></div>
            @endif

            <div class="card shadow-sm border-0 rounded-20">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Reorder</th>
                                    <th>Unit price</th>
                                    <th>Supplier</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr class="{{ (float) $item->current_stock <= (float) $item->reorder_level ? 'table-warning' : '' }}">
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->category }}</td>
                                        <td>{{ $item->current_stock }} {{ $item->unit }} @if((float) $item->current_stock <= (float) $item->reorder_level)<span class="badge badge-warning ml-1">Low stock</span>@endif</td>
                                        <td>{{ $item->reorder_level }} {{ $item->unit }}</td>
                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                        <td>{{ $item->supplier ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-pill {{ $item->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('inventory.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit item"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No inventory items added yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.script')
</body>
</html>
