<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Expenses</title>
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
                    <h3 class="page-title mb-1">Expenses</h3>
                    <p class="text-muted mb-0">Track operating costs and supplier payments.</p>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-20 mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('expenses.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Category</label>
                                <input type="text" name="category" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Amount</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Payment method</label>
                                <input type="text" name="payment_method" class="form-control" value="cash">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Save expense</button>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-20">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->title }}</td>
                                        <td>{{ $expense->category }}</td>
                                        <td>${{ number_format($expense->amount, 2) }}</td>
                                        <td>{{ $expense->payment_method }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No expenses recorded.</td>
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
