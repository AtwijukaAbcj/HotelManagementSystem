<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Invoices</title>
    @include('admin.css')
    <link
        rel="stylesheet"
        href="{{ asset('admin/assets/css/custom.css') }}?v={{ time() }}"
    >
</head>
<body>
<div class="main-wrapper property-create-page">
    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-wrapper">
        <main class="property-create-container">
            <div class="property-create-header">
                <div class="property-create-heading">
                    <div class="property-create-heading-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h3>Invoices</h3>
                        <p>Issue and track guest billing records.</p>
                    </div>
                </div>

                <a href="{{ route('invoices.create') }}" class="property-create-save-btn">
                    <i class="fas fa-plus"></i>
                    Add invoice
                </a>
            </div>

            @if (session('message'))
                <div class="property-create-info">
                    <div class="property-create-info-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <strong>Success</strong>
                        <p>{{ session('message') }}</p>
                    </div>
                </div>
            @endif

            <div class="property-create-card">
                <div class="property-create-card-header">
                    <div class="property-create-section-icon location">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <h5>Invoice records</h5>
                        <p>Payment, status, and billing detail overview.</p>
                    </div>
                </div>

                <div class="property-create-card-body table-card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 modern-table">
                            <thead>
                                <tr>
                                    <th>Guest</th>
                                    <th>Invoice</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Method</th>
                                    <th>Email</th>
                                    <th>Recorded</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->guest_name }}</td>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>UGX {{ number_format($invoice->amount, 2) }}</td>
                                        <td><span class="badge badge-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'warning') }}">{{ ucfirst($invoice->status) }}</span></td>
                                        <td><span class="badge badge-light text-capitalize">{{ str_replace('_', ' ', $invoice->payment_method) }}</span></td>
                                        <td><span class="badge badge-{{ ($invoice->receipt_email_status ?? 'not_sent') === 'sent' ? 'success' : 'light' }}">{{ ucfirst(str_replace('_', ' ', $invoice->receipt_email_status ?? 'not sent')) }}</span></td>
                                        <td class="text-muted">{{ $invoice->created_at->format('d M Y') }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Print invoice">
                                                <i class="fas fa-print mr-1"></i> Print
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5"><i class="fas fa-file-invoice-dollar fa-2x mb-3 text-primary"></i><br>No invoices recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@include('admin.script')
</body>
</html>
