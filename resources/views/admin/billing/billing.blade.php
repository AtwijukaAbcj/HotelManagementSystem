<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing</title>
    @include('admin.css')
    <link
        rel="stylesheet"
        href="{{ asset('admin/assets/css/custom.css') }}?v={{ time() }}"
    >
    <link rel="icon" type="image/png" sizes="32*32" href="images/billing.png">
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
                        <h3>Billing</h3>
                        <p>Record guest stay billing and payment details.</p>
                    </div>
                </div>

                <a href="{{ url('/billing_report') }}" class="property-create-back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Billing report
                </a>
            </div>

            @if(session()->has('message'))
                <div class="property-create-info">
                    <div class="property-create-info-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <strong>Success</strong>
                        <p>{{ session()->get('message') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ isset($data) ? url('/update_billdata_confirm/' . $data->id) : url('/savebill') }}" method="POST">
                @csrf
                @if(isset($data)) @method('PUT') @endif

                <div class="property-create-card">
                    <div class="property-create-card-header">
                        <div class="property-create-section-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h5>Guest bill details</h5>
                            <p>Enter the billing information for the guest stay.</p>
                        </div>
                    </div>

                    <div class="property-create-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="property-create-field">
                                    <label for="guest_id">Existing guest</label>
                                    <div class="property-create-select">
                                        <i class="fas fa-user-check"></i>
                                        <select id="guest_id" name="guest_id">
                                            <option value="">Select guest</option>
                                            @foreach($guests as $guest)
                                                <option value="{{ $guest->id }}"
                                                    data-name="{{ $guest->full_name }}"
                                                    data-email="{{ $guest->email }}"
                                                    data-room-number="{{ $guestProfiles[$guest->id]['room_number'] ?? '' }}"
                                                    data-room-type="{{ $guestProfiles[$guest->id]['room_type'] ?? '' }}"
                                                    data-price="{{ $guestProfiles[$guest->id]['price'] ?? '' }}"
                                                    data-stay-days="{{ $guestProfiles[$guest->id]['stay_days'] ?? '' }}"
                                                    @selected(old('guest_id', $data->guest_id ?? '') == $guest->id)>
                                                    {{ $guest->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="property-create-field">
                                    <label for="name">Guest name</label>
                                    <div class="property-create-input">
                                        <i class="fas fa-user"></i>
                                        <input type="text" id="name" name="name" value="{{ old('name', $data->name ?? $latestBooking?->name ?? '') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="property-create-field">
                                    <label for="email">Receipt email</label>
                                    <div class="property-create-input">
                                        <i class="fas fa-envelope"></i>
                                        <input type="email" id="email" name="email" value="{{ old('email', $data->email ?? '') }}" placeholder="guest@example.com">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="property-create-field">
                                    <label for="room_type">Room type</label>
                                    <div class="property-create-input">
                                        <i class="fas fa-bed"></i>
                                        <input type="text" id="room_type" name="room_type" value="{{ old('room_type', $data->room_type ?? $latestBooking?->room_type ?? '') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="property-create-field">
                                    <label for="room_number">Room number</label>
                                    <div class="property-create-select">
                                        <i class="fas fa-door-open"></i>
                                        <select id="room_number" name="room_number" required>
                                            <option value="">Select room</option>
                                            @foreach($rooms as $room)
                                                <option value="{{ $room->room_number }}" data-room-type="{{ $room->room_type }}" data-price="{{ $room->price }}" @selected(old('room_number', $data->room_number ?? $latestBooking?->room_number ?? '') == $room->room_number)>
                                                    Room {{ $room->room_number }} — {{ $room->room_type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="property-create-field">
                                    <label for="no_of_days_stay">No. of stay days</label>
                                    <div class="property-create-input">
                                        <i class="fas fa-calendar-check"></i>
                                        <input type="number" id="no_of_days_stay" name="no_of_days_stay" min="1" value="{{ old('no_of_days_stay', $data->no_of_days_stay ?? $stayDays ?? '') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="property-create-field">
                                    <label for="price">Price per room</label>
                                    <div class="property-create-input">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <input type="number" id="price" name="price" min="0" step="0.01" value="{{ old('price', $data->price ?? $defaultPrice ?? '') }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="property-create-field">
                                    <label for="total">Amount to be paid</label>
                                    <div class="property-create-input">
                                        <i class="fas fa-wallet"></i>
                                        <input type="number" id="total" name="total" value="{{ old('total', $data->total ?? '') }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="property-create-field">
                                    <label for="transaction_type">Payment method</label>
                                    <div class="property-create-select">
                                        <i class="fas fa-credit-card"></i>
                                        <select id="transaction_type" name="transaction_type" required>
                                            <option value="cash" @selected(old('transaction_type', $data->transaction_type ?? 'cash') === 'cash')>Cash</option>
                                            <option value="card" @selected(old('transaction_type', $data->transaction_type ?? '') === 'card')>Card</option>
                                            <option value="bank_transfer" @selected(old('transaction_type', $data->transaction_type ?? '') === 'bank_transfer')>Bank transfer</option>
                                            <option value="mobile_money" @selected(old('transaction_type', $data->transaction_type ?? '') === 'mobile_money')>Mobile money</option>
                                            <option value="online" @selected(old('transaction_type', $data->transaction_type ?? '') === 'online')>Online</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <button type="submit" class="property-create-save-btn" name="submit" value="record bill">
                                    <i class="fas fa-save"></i>
                                    {{ isset($data) ? 'Update billing' : 'Record bill' }}
                                </button>
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
    $(document).ready(function () {
        function calculateTotal() {
            var days = parseFloat($('#no_of_days_stay').val()) || 0;
            var price = parseFloat($('#price').val()) || 0;
            var total = days * price;
            $('#total').val(total.toFixed(2));
        }

        $('#guest_id').on('change', function () {
            var selected = $(this).find('option:selected');
            var guestName = selected.data('name');
            $('#email').val(selected.data('email') || '');
            var roomNumber = selected.data('room-number');
            var roomType = selected.data('room-type');
            var roomPrice = selected.data('price');
            var stayDays = selected.data('stay-days');

            if (guestName) {
                $('#name').val(guestName);
            }

            if (roomNumber) {
                $('#room_number').val(roomNumber).trigger('change');
            }

            if (roomType) {
                $('#room_type').val(roomType);
            }

            if (roomPrice !== undefined && roomPrice !== null && roomPrice !== '') {
                $('#price').val(roomPrice);
            }

            if (stayDays !== undefined && stayDays !== null && stayDays !== '') {
                $('#no_of_days_stay').val(stayDays);
            }

            calculateTotal();
        });

        $('#room_number').on('change', function () {
            var selected = $(this).find('option:selected');
            var roomType = selected.data('room-type');
            var roomPrice = selected.data('price');

            if (roomType) {
                $('#room_type').val(roomType);
            }

            if (roomPrice !== undefined && roomPrice !== null && roomPrice !== '') {
                $('#price').val(roomPrice);
            }

            calculateTotal();
        });

        $('#no_of_days_stay, #price').on('input', function () {
            calculateTotal();
        });

        function setCurrentDateTimeIfEmpty() {
            var now = new Date();
            var date = now.toISOString().split('T')[0];
            var time = now.toTimeString().split(' ')[0].slice(0, 5);

            if (!$('#billing_date').val()) {
                $('#billing_date').val(date);
            }

            if (!$('#datetimepicker3').val()) {
                $('#datetimepicker3').val(time);
            }
        }

        setCurrentDateTimeIfEmpty();
        calculateTotal();
    });
</script>
</body>
</html>
