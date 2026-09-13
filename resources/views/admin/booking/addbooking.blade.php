<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking</title>
    @include('admin.css')
    <style>
        .booking-create-page { background: #f6f8fb; min-height: 100vh; }
        .booking-create-page .page-wrapper { padding-top: 80px; }
        .booking-container { max-width: 1180px; margin: 0 auto; padding: 30px 28px 56px; }
        .booking-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        .booking-section-title { color: #172033; font-size: 1.05rem; font-weight: 700; margin-bottom: 18px; }
        @media (max-width: 767px) { .booking-create-page .page-wrapper { padding-top: 72px; } .booking-container { padding: 24px 16px 40px; } }
    </style>
</head>
<body>
<div class="main-wrapper booking-create-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="booking-container">
        <div class="d-flex justify-content-between align-items-start mb-4"><div><h3 class="page-title mb-1">New Booking</h3><p class="text-muted mb-0">Create a reservation and assign a room for the guest stay.</p></div><a href="{{ url('form/allbooking') }}" class="btn btn-outline-secondary">Back to bookings</a></div>
        @if(session('message'))<div class="alert alert-success booking-card">{{ session('message') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger booking-card"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form action="{{ url('form/savebooking') }}" method="POST">@csrf
            <div class="card booking-card mb-4"><div class="card-body"><div class="booking-section-title">Guest and property</div><div class="row"><div class="col-md-6 mb-3"><label class="font-weight-bold">Guest name</label><input name="name" value="{{ old('name') }}" class="form-control" required></div><div class="col-md-6 mb-3"><label class="font-weight-bold">Property</label><select name="property_id" class="form-control" required><option value="">Select property</option>@foreach($properties as $property)<option value="{{ $property->id }}" @selected(old('property_id') == $property->id)>{{ $property->name }} ({{ $property->code }})</option>@endforeach</select></div><div class="col-md-4 mb-3"><label class="font-weight-bold">Email</label><input type="email" name="email_id" value="{{ old('email_id') }}" class="form-control" required></div><div class="col-md-4 mb-3"><label class="font-weight-bold">Phone</label><input name="ph_number" value="{{ old('ph_number') }}" class="form-control" required></div><div class="col-md-4 mb-3"><label class="font-weight-bold">Message</label><input name="message" value="{{ old('message') }}" class="form-control"></div></div></div></div>
            <div class="card booking-card mb-4"><div class="card-body"><div class="booking-section-title">Room and stay</div><div class="row"><div class="col-md-4 mb-3"><label class="font-weight-bold">Room type</label><select name="room_type" class="form-control" required><option value="">Select room type</option>@forelse($roomTypes as $roomType)<option value="{{ $roomType }}" @selected(old('room_type') === $roomType)>{{ $roomType }}</option>@empty<option>Normal Room</option>@endforelse</select></div><div class="col-md-4 mb-3"><label class="font-weight-bold">Room number</label><input name="room_number" value="{{ old('room_number') }}" class="form-control" placeholder="101" required></div><div class="col-md-4 mb-3"><label class="font-weight-bold">Booking date</label><input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="form-control" required></div><div class="col-md-4 mb-3"><label class="font-weight-bold">Arrival date</label><input type="date" name="arrival_date" value="{{ old('arrival_date') }}" class="form-control" required></div><div class="col-md-4 mb-3"><label class="font-weight-bold">Departure date</label><input type="date" name="departure_date" value="{{ old('departure_date') }}" class="form-control" required></div><div class="col-md-4 mb-3"><label class="font-weight-bold">Arrival time</label><input type="time" name="time" value="{{ old('time') }}" class="form-control" required></div></div></div></div>
            <div class="d-flex justify-content-end" style="gap:10px"><a href="{{ url('form/allbooking') }}" class="btn btn-light">Cancel</a><button type="submit" class="btn btn-primary">Create Booking</button></div>
        </form>
    </main></div>
</div>
@include('admin.script')
</body>
</html>
