<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Add Payment</title>
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
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div>
                        <h3>Add Payment</h3>
                        <p>Create a payment against an existing invoice.</p>
                    </div>
                </div>

                <a href="{{ route('payments.index') }}" class="property-create-back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to payments
                </a>
            </div>

            @if ($errors->any())
                <div class="property-create-error">
                    <div class="property-create-error-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Please review the form.
                    </div>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('payments.store') }}">
                @csrf

                <div class="property-create-card">
                    <div class="property-create-card-header">
                        <div class="property-create-section-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div>
                            <h5>Invoice payment details</h5>
                            <p>Record payment received for a guest invoice.</p>
                        </div>
                    </div>

                    <div class="property-create-card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="property-create-field">
                                    <label for="invoice_id">Invoice / Bill</label>
                                    <div class="property-create-select">
                                        <i class="fas fa-file-invoice"></i>
                                        <select id="invoice_id" name="invoice_id">
                                            <option value="">Unlinked payment</option>
                                            @foreach($invoices as $invoice)
                                                <option value="{{ $invoice->id }}" data-name="{{ $invoice->guest_name }}" data-email="{{ $invoice->receipt_email ?: $invoice->guest?->email }}">{{ $invoice->invoice_number }} - {{ $invoice->guest_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="property-create-field">
                                    <label for="payer_name">Customer / Payer <span class="required">*</span></label>
                                    <div class="property-create-input">
                                        <i class="fas fa-user"></i>
                                        <input id="payer_name" name="payer_name" type="text" value="{{ old('payer_name') }}" placeholder="Customer or payer name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="property-create-field">
                                    <label for="payer_email">Receipt email</label>
                                    <div class="property-create-input">
                                        <i class="fas fa-envelope"></i>
                                        <input id="payer_email" name="payer_email" type="email" value="{{ old('payer_email') }}" placeholder="payer@example.com">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="property-create-field">
                                    <label>Reference</label>
                                    <div class="property-create-input">
                                        <i class="fas fa-hashtag"></i>
                                        <input type="text" value="Auto-generated" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="property-create-field">
                                    <label for="amount">Amount (UGX) <span class="required">*</span></label>
                                    <div class="property-create-input">
                                        <i class="fas fa-money-bill"></i>
                                        <input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="property-create-field">
                                    <label for="method">Method <span class="required">*</span></label>
                                    <div class="property-create-select">
                                        <i class="fas fa-credit-card"></i>
                                        <select id="method" name="method" required>
                                            @foreach (['cash' => 'Cash', 'card' => 'Card', 'bank_transfer' => 'Bank transfer', 'mobile_money' => 'Mobile money', 'online' => 'Online'] as $value => $label)
                                                <option value="{{ $value }}" @selected(old('method', 'cash') === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="property-create-field">
                                    <label for="paid_at">Paid at <span class="required">*</span></label>
                                    <div class="property-create-input">
                                        <i class="fas fa-calendar"></i>
                                        <input id="paid_at" name="paid_at" type="datetime-local" value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="property-create-field">
                                    <label for="notes">Notes</label>
                                    <div class="property-create-input">
                                        <i class="fas fa-sticky-note"></i>
                                        <input id="notes" name="notes" type="text" value="{{ old('notes') }}" placeholder="Optional payment notes">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="property-create-field">
                                    <label class="invisible">Action</label>
                                    <button class="property-create-save-btn w-100" type="submit">
                                        <i class="fas fa-save"></i>
                                        Save payment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

@include('admin.script')
<script>
    document.getElementById('invoice_id')?.addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        if (!option?.value) return;
        document.getElementById('payer_name').value = option.dataset.name || '';
        document.getElementById('payer_email').value = option.dataset.email || '';
    });
</script>
</body>
</html>
