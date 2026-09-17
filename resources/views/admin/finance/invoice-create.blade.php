<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Add Invoice</title>
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
                    <h3 class="page-title mb-1">Add invoice</h3>
                    <p class="text-muted mb-0">Create a guest billing record.</p>
                </div>
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to invoices</a>
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
                    <form method="POST" action="{{ route('invoices.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="guest_id">Guest profile</label>
                                <select id="guest_id" name="guest_id" class="form-control">
                                    <option value="">Manual / walk-in payer</option>
                                    @foreach($guests as $guest)
                                        <option value="{{ $guest->id }}" data-name="{{ $guest->full_name }}" data-email="{{ $guest->email }}" @selected(old('guest_id') == $guest->id)>{{ $guest->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="guest_name">Guest name</label>
                                <input id="guest_name" type="text" name="guest_name" class="form-control" value="{{ old('guest_name') }}" required autofocus>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="receipt_email">Receipt email</label>
                                <input id="receipt_email" type="email" name="receipt_email" class="form-control" value="{{ old('receipt_email') }}" placeholder="guest@example.com">
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="invoice_number">Invoice number</label>
                                <input id="invoice_number" type="text" class="form-control" value="Auto-generated" disabled>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="amount">Amount</label>
                                <input id="amount" type="number" step="0.01" min="0" name="amount" class="form-control" value="{{ old('amount') }}" required>
                            </div>
                            <div class="col-md-6 col-lg-4 form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="paid" @selected(old('status', 'pending') === 'paid')>Paid</option>
                                    <option value="pending" @selected(old('status', 'pending') === 'pending')>Pending</option>
                                    <option value="overdue" @selected(old('status') === 'overdue')>Overdue</option>
                                </select>
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
                            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save invoice</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.script')
<script>
    document.getElementById('guest_id')?.addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        document.getElementById('guest_name').value = option?.dataset?.name || '';
        document.getElementById('receipt_email').value = option?.dataset?.email || '';
    });
</script>
</body>
</html>