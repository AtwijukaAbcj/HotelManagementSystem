<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Edit Inventory Item</title>
    @include('admin.css')
</head>
<body>
<div class="main-wrapper">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap">
                <div>
                    <h3 class="page-title mb-1">Edit inventory item</h3>
                    <p class="text-muted mb-0">Update item details and reorder settings.</p>
                </div>
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to inventory</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please check the form.</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('inventory.update', $item) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="name">Item name</label>
                                <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required autofocus>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="category">Category</label>
                                <input id="category" type="text" name="category" class="form-control" value="{{ old('category', $item->category) }}" required>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="unit">Unit</label>
                                <input id="unit" type="text" name="unit" class="form-control" value="{{ old('unit', $item->unit) }}" required>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label>Current stock</label>
                                <input type="text" class="form-control" value="{{ $item->current_stock }} {{ $item->unit }}" readonly>
                                <small class="form-text text-muted">Use Stock movements to change the balance.</small>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="reorder_level">Reorder level</label>
                                <input id="reorder_level" type="number" step="0.01" min="0" name="reorder_level" class="form-control" value="{{ old('reorder_level', $item->reorder_level) }}" required>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="unit_price">Unit price</label>
                                <input id="unit_price" type="number" step="0.01" min="0" name="unit_price" class="form-control" value="{{ old('unit_price', $item->unit_price) }}" required>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="supplier">Supplier</label>
                                <input id="supplier" type="text" name="supplier" class="form-control" value="{{ old('supplier', $item->supplier) }}">
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="active" @selected(old('status', $item->status) === 'active')>Active</option>
                                    <option value="inactive" @selected(old('status', $item->status) === 'inactive')>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3" style="gap: 8px;">
                            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.script')
</body>
</html>
