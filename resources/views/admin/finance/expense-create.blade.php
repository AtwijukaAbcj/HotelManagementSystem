<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Add Expense</title>
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
                    <h3 class="page-title mb-1">Add expense</h3>
                    <p class="text-muted mb-0">Record an operating cost or supplier payment.</p>
                </div>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to expenses</a>
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
                    <form method="POST" action="{{ route('expenses.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="title">Title</label>
                                <input id="title" type="text" name="title" class="form-control" value="{{ old('title') }}" required autofocus>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="category">Category</label>
                                <input id="category" type="text" name="category" class="form-control" value="{{ old('category') }}" required>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="amount">Amount</label>
                                <input id="amount" type="number" step="0.01" min="0" name="amount" class="form-control" value="{{ old('amount') }}" required>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="payment_method">Payment method</label>
                                <select id="payment_method" name="payment_method" class="form-control" required>
                                    @foreach (['cash' => 'Cash', 'card' => 'Card', 'bank_transfer' => 'Bank transfer', 'mobile_money' => 'Mobile money', 'online' => 'Online'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('payment_method', 'cash') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 form-group">
                                <label for="notes">Notes</label>
                                <textarea id="notes" name="notes" rows="4" class="form-control">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3" style="gap: 8px;">
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save expense</button>
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