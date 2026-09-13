<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room</title>
    @include('admin.css')
    <style>
        .room-create-page { background: #f6f8fb; min-height: 100vh; }
        .room-create-page .page-wrapper { padding-top: 80px; }
        .room-create-container { max-width: 980px; margin: 0 auto; padding: 30px 28px 56px; }
        .room-create-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        .room-intro { background: #eaf2ff; border-radius: 10px; color: #1d4ed8; padding: 14px 16px; }
        @media (max-width: 767px) { .room-create-page .page-wrapper { padding-top: 72px; } .room-create-container { padding: 24px 16px 40px; } }
    </style>
</head>
<body>
<div class="main-wrapper room-create-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="room-create-container">
        <div class="d-flex justify-content-between align-items-start mb-4"><div><h3 class="page-title mb-1">Add Room</h3><p class="text-muted mb-0">Create a room and assign its standard nightly rate.</p></div><a href="{{ url('/all_rooms') }}" class="btn btn-outline-secondary">Back to rooms</a></div>
        @if(session('message'))<div class="alert alert-success room-create-card">{{ session('message') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger room-create-card"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="card room-create-card"><div class="card-body"><div class="room-intro mb-4"><i class="fas fa-info-circle mr-1"></i> Rooms start as <strong>Available</strong>. You can update operational status from the room status board.</div><form method="POST" action="{{ url('/save_rooms') }}">@csrf<div class="row"><div class="col-md-6 mb-3"><label class="font-weight-bold">Property</label><select name="property_id" class="form-control" required><option value="">Select property</option>@foreach($properties as $property)<option value="{{ $property->id }}" @selected(old('property_id') == $property->id)>{{ $property->name }} ({{ $property->code }})</option>@endforeach</select></div><div class="col-md-6 mb-3"><label class="font-weight-bold">Room type</label><select name="room_type" class="form-control" required><option value="">Select room type</option><option value="Normal Room">Normal Room</option><option value="Video Room">Video Room</option><option value="Double Bed Room">Double Bed Room</option><option value="Special Room">Special Room</option><option value="AC Room">AC Room</option></select></div><div class="col-md-4 mb-3"><label class="font-weight-bold">Room number</label><input name="room_number" value="{{ old('room_number') }}" class="form-control" placeholder="101" required></div><div class="col-md-4 mb-3"><label class="font-weight-bold">Floor</label><input name="floor" type="number" min="0" value="{{ old('floor') }}" class="form-control" required></div><div class="col-md-4 mb-3"><label class="font-weight-bold">Nightly price (UGX)</label><input name="price" type="number" min="0" step=".01" value="{{ old('price') }}" class="form-control" required></div></div><div class="d-flex justify-content-end" style="gap:10px"><a href="{{ url('/all_rooms') }}" class="btn btn-light">Cancel</a><button class="btn btn-primary" type="submit">Save Room</button></div></form></div></div>
    </main></div>
</div>
@include('admin.script')
</body>
</html>
