<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Invoices</title>
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
                    <h3 class="page-title mb-1">Invoices</h3>
                    <p class="text-muted mb-0">Issue and track guest billing records.</p>
                </div>
                <a href="{{ route('invoices.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Add invoice</a>
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
                                    <th>Guest</th>
                                    <th>Invoice</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Method</th>
                                    <th>Recorded</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->guest_name }}</td>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>${{ number_format($invoice->amount, 2) }}</td>
                                        <td><span class="badge badge-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'warning') }}">{{ ucfirst($invoice->status) }}</span></td>
                                        <td><span class="badge badge-light text-capitalize">{{ str_replace('_', ' ', $invoice->payment_method) }}</span></td>
                                        <td class="text-muted">{{ $invoice->created_at->format('d M Y') }}</td>
                                        <td class="text-right"><a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Print invoice"><i class="fas fa-print mr-1"></i> Print</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5"><i class="fas fa-file-invoice-dollar fa-2x mb-3 text-primary"></i><br>No invoices recorded.</td>
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
