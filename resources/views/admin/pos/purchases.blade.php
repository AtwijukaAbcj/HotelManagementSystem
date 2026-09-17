<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipts</title>
    @include('admin.css')
    <style>
        body {
            margin: 0;
            background: #f4f7fb;
            font-family: Inter, sans-serif;
            font-size: 13px;
            font-style: normal;
            font-weight: 400;
            line-height: 20px;
            color: #12233f;
        }
        .pos-page {
            min-height: 100vh;
            padding: 28px 20px 40px;
        }
        .pos-shell {
            max-width: 1200px;
            margin: 0 auto;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            padding: 18px 22px;
            background: #fff;
            border: 1px solid #e3e9f1;
            border-radius: 16px;
            box-shadow: 0 8px 28px rgba(16, 35, 63, .06);
        }
        .title-wrap h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
        }
        .title-wrap p {
            margin: 6px 0 0;
            color: #718096;
            font-size: 14px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            padding: 12px 18px;
            min-height: 42px;
            text-decoration: none;
            font-weight: 800;
            font-size: 13px;
            letter-spacing: .01em;
            cursor: pointer;
            border: 1px solid transparent;
            transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
        }
        .btn-primary {
            background: linear-gradient(135deg, #009c8c 0%, #10b981 100%);
            border-color: #009c8c;
            color: #fff;
            box-shadow: 0 10px 18px rgba(0, 156, 140, 0.22);
        }
        .btn-outline {
            background: #fff;
            border-color: #dfe7f1;
            color: #123a63;
        }
        .btn-print {
            background: linear-gradient(135deg, #0f766e 0%, #10b981 100%);
            border-color: #0f766e;
            color: #fff;
            box-shadow: 0 10px 18px rgba(15, 118, 110, 0.2);
        }
        .card {
            background: #fff;
            border: 1px solid #e3e9f1;
            border-radius: 16px;
            box-shadow: 0 8px 28px rgba(16, 35, 63, .06);
            overflow: hidden;
        }
        .table-wrap {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid #edf1f5;
            text-align: left;
            font-size: 14px;
        }
        thead th {
            background: #f7fafc;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-size: 11px;
            font-weight: 800;
        }
        tbody tr:last-child td {
            border-bottom: none;
        }
        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: #e7f8f2;
            color: #087443;
            font-size: 11px;
            font-weight: 700;
        }
        .muted {
            color: #718096;
        }
        .pagination {
            display: flex;
            gap: 8px;
            list-style: none;
            padding: 0;
            margin: 18px 0 0;
            justify-content: flex-end;
        }
        .pagination li a,
        .pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            border-radius: 9px;
            border: 1px solid #dfe7f1;
            background: #fff;
            color: #123a63;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
        }
        .pagination .active span {
            background: #009c8c;
            border-color: #009c8c;
            color: #fff;
        }
        @media (max-width: 700px) {
            .topbar {
                flex-direction: column;
                align-items: stretch;
            }
            .title-wrap h1 {
                font-size: 24px;
            }
            th, td {
                padding: 12px 10px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
<div class="pos-page">
    <div class="pos-shell">
        <div class="topbar">
            <div class="title-wrap">
                <h1>Receipts</h1>
                <p>Every completed restaurant and bar sale</p>
            </div>
            <a href="{{ route('pos.index') }}" class="btn btn-primary">Back to POS</a>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Receipt</th>
                            <th>Customer</th>
                            <th>Room / Table</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><strong>{{ $order->reference_number }}</strong></td>
                                <td>{{ $order->guest?->full_name ?? 'Walk-in' }}</td>
                                <td>{{ $order->table_reference ?? ($order->room?->room_number ? 'Room ' . $order->room->room_number : '-') }}</td>
                                <td>UGX {{ number_format((float) $order->total, 0) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'cash')) }}</td>
                                <td>{{ $order->completed_at?->format('M d, Y H:i') ?? $order->created_at?->format('M d, Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('pos.receipt', $order) }}" class="btn btn-print">View Receipt</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="muted" style="text-align: center; padding: 24px 14px;">No receipts recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($orders->hasPages())
            <div class="mt-3">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
</body>
</html>
