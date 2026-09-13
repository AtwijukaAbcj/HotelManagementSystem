<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Finance Overview</title>
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
                    <h3 class="page-title mb-1">Finance overview</h3>
                    <p class="text-muted mb-0">Monitor revenue, expenses, and outstanding invoices.</p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-20">
                        <div class="card-body">
                            <div class="text-muted">Revenue</div>
                            <h3 class="mt-2 mb-0">${{ number_format($revenue, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-20">
                        <div class="card-body">
                            <div class="text-muted">Expenses</div>
                            <h3 class="mt-2 mb-0">${{ number_format($costs, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 rounded-20">
                        <div class="card-body">
                            <div class="text-muted">Net</div>
                            <h3 class="mt-2 mb-0">${{ number_format($net, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 rounded-20">
                        <div class="card-header bg-white border-0 pb-0">
                            <h5 class="mb-0">Recent invoices</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Guest</th>
                                            <th>Invoice</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invoices as $invoice)
                                            <tr>
                                                <td>{{ $invoice->guest_name }}</td>
                                                <td>{{ $invoice->invoice_number }}</td>
                                                <td>${{ number_format($invoice->amount, 2) }}</td>
                                                <td><span class="badge badge-pill badge-success">{{ ucfirst($invoice->status) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">No invoices yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-20">
                        <div class="card-header bg-white border-0 pb-0">
                            <h5 class="mb-0">Recent expenses</h5>
                        </div>
                        <div class="card-body">
                            @forelse($expenses as $expense)
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <div class="font-weight-bold">{{ $expense->title }}</div>
                                        <small class="text-muted">{{ $expense->category }}</small>
                                    </div>
                                    <span class="text-danger font-weight-bold">-${{ number_format($expense->amount, 2) }}</span>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">No expenses recorded.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.script')
</body>
</html>
