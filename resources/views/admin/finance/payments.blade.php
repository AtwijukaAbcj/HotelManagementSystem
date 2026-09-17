<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    @include('admin.css')
    <link
        rel="stylesheet"
        href="{{ asset('admin/assets/css/custom.css') }}?v={{ time() }}"
    >
    <style>
        .payment-page .metric-value {
            color: #102a43;
            font-size: 1.7rem;
            font-weight: 800;
            letter-spacing: -.02em;
        }

        .payment-page .table-card-body {
            padding: 0;
        }

        .payment-page .modern-table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .payment-page .modern-table thead th {
            padding: 12px 16px;
            background: #f7f9fb;
            border-bottom: 1px solid #e8edf3;
            color: #667085;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .payment-page .modern-table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #edf1f5;
            color: #344054;
            font-size: 10.5px;
            background: #fff;
        }

        .payment-page .modern-table tbody tr:hover td {
            background: #fafcfd;
        }

        .payment-page .badge-success {
            background: rgba(0, 150, 136, 0.12);
            color: #007f74;
            border: 1px solid rgba(0, 150, 136, 0.18);
            font-size: 8px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: 6px 8px;
        }

        .payment-page .pagination-row {
            padding: 14px 16px 18px;
            border-top: 1px solid #edf1f5;
            background: #fff;
        }

        @media (max-width: 767px) {
            .payment-page .metric-value {
                font-size: 1.35rem;
            }
        }
    </style>
</head>
<body>
<div class="main-wrapper payment-page property-create-page">
    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-wrapper">
        <main class="property-create-container">
            <div class="property-create-header">
                <div class="property-create-heading">
                    <div class="property-create-heading-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <h3>Payments</h3>
                        <p>Track incoming payments received against guest invoices.</p>
                    </div>
                </div>

                <a href="{{ route('payments.create') }}" class="property-create-save-btn">
                    <i class="fas fa-plus"></i>
                    Add payment
                </a>
            </div>

            @if(session('message'))
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

            <div class="row mb-4">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="property-create-card h-100">
                        <div class="property-create-card-header">
                            <div class="property-create-section-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div>
                                <h5>Completed value</h5>
                                <p>Overall payments received</p>
                            </div>
                        </div>
                        <div class="property-create-card-body">
                            <div class="metric-value">UGX {{ number_format($summary['total'], 0) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="property-create-card h-100">
                        <div class="property-create-card-header">
                            <div class="property-create-section-icon location">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div>
                                <h5>Received today</h5>
                                <p>Today's collection total</p>
                            </div>
                        </div>
                        <div class="property-create-card-body">
                            <div class="metric-value">UGX {{ number_format($summary['today'], 0) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="property-create-card h-100">
                        <div class="property-create-card-header">
                            <div class="property-create-section-icon">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <h5>Transactions</h5>
                                <p>Payments recorded</p>
                            </div>
                        </div>
                        <div class="property-create-card-body">
                            <div class="metric-value">{{ $summary['count'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="property-create-card">
                <div class="property-create-card-header">
                    <div class="property-create-section-icon location">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <h5>Recent payments</h5>
                        <p>Latest payment activity and invoice collections.</p>
                    </div>
                </div>

                <div class="property-create-card-body table-card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle modern-table payment-modern-table">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Customer / Payer</th>
                                    <th>Invoice / Bill</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Email</th>
                                    <th>Paid at</th>
                                    <th class="text-right">Print</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td class="font-weight-bold">{{ $payment->reference }}</td>
                                        <td>{{ $payment->payer_name }}</td>
                                        <td>{{ $payment->invoice->invoice_number ?? 'Unlinked' }}</td>
                                        <td>UGX {{ number_format($payment->amount, 0) }}</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $payment->method)) }}</td>
                                        <td>
                                            <span class="badge badge-success">{{ ucfirst($payment->status) }}</span>
                                        </td>
                                        <td><span class="badge badge-{{ ($payment->receipt_email_status ?? 'not_sent') === 'sent' ? 'success' : 'light' }}">{{ ucfirst(str_replace('_', ' ', $payment->receipt_email_status ?? 'not sent')) }}</span></td>
                                        <td>{{ $payment->paid_at->format('d M Y H:i') }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('payments.print', $payment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-print mr-1"></i> Print
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5">
                                            <i class="fas fa-money-bill-wave fa-2x mb-3 text-primary"></i><br>
                                            No payment transactions recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($payments->hasPages())
                        <div class="pagination-row">
                            {{ $payments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@include('admin.script')
</body>
</html>
