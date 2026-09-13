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
                <a href="{{ route('expenses.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Add expense</a>
            </div>

            @if (session('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Recorded</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->title }}</td>
                                        <td>{{ $expense->category }}</td>
                                        <td>${{ number_format($expense->amount, 2) }}</td>
                                        <td><span class="badge badge-light text-capitalize">{{ str_replace('_', ' ', $expense->payment_method) }}</span></td>
                                        <td class="text-muted">{{ $expense->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5"><i class="fas fa-receipt fa-2x mb-3 text-primary"></i><br>No expenses recorded.</td>
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
