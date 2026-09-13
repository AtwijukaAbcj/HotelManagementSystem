<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>
    @include('admin.css')
    <style>
        .rooms-page { background: #f6f8fb; min-height: 100vh; }
        .rooms-page .page-wrapper { padding-top: 80px; }
        .rooms-container { max-width: 1320px; margin: 0 auto; padding: 30px 28px 56px; }
        .rooms-card, .room-stat { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        .room-stat { background: #fff; padding: 18px; }
        .room-stat-label { color: #667085; font-size: .74rem; letter-spacing: .05em; text-transform: uppercase; }
        .room-stat-value { color: #172033; font-size: 1.55rem; font-weight: 700; }
        .room-empty { padding: 58px 24px; }
        .room-empty-icon { width: 58px; height: 58px; border-radius: 16px; background: #eaf2ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .room-status { border-radius: 999px; font-size: .72rem; font-weight: 600; padding: .4rem .65rem; }
        .room-status-available { background: #ddf7ed; color: #087443; }
        .room-status-occupied { background: #e4efff; color: #1d4ed8; }
        .room-status-reserved { background: #fff4d6; color: #9a6700; }
        .room-status-dirty, .room-status-maintenance { background: #ffe5e5; color: #b42318; }
        @media (max-width: 767px) { .rooms-page .page-wrapper { padding-top: 72px; } .rooms-container { padding: 24px 16px 40px; } }
    </style>
</head>
<body>
<div class="main-wrapper rooms-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="rooms-container">
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-4" style="gap:16px"><div><h3 class="page-title mb-1">Rooms</h3><p class="text-muted mb-0">Manage room inventory, pricing, and operational availability.</p></div><a href="{{ url('/add_rooms') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Add Room</a></div>
        @if(session('message'))<div class="alert alert-success rooms-card">{{ session('message') }}</div>@endif
        @php($occupied = $addrooms->where('operational_status', 'occupied')->count())
        @php($available = $addrooms->where('operational_status', 'available')->count())
        @php($maintenance = $addrooms->whereIn('operational_status', ['maintenance', 'dirty', 'cleaning'])->count())
        <div class="row mb-4"><div class="col-md-3 mb-3 mb-md-0"><div class="room-stat"><span class="room-stat-label">Total rooms</span><div class="room-stat-value mt-2">{{ $addrooms->count() }}</div></div></div><div class="col-md-3 mb-3 mb-md-0"><div class="room-stat"><span class="room-stat-label">Available</span><div class="room-stat-value text-success mt-2">{{ $available }}</div></div></div><div class="col-md-3 mb-3 mb-md-0"><div class="room-stat"><span class="room-stat-label">Occupied</span><div class="room-stat-value text-primary mt-2">{{ $occupied }}</div></div></div><div class="col-md-3"><div class="room-stat"><span class="room-stat-label">Attention</span><div class="room-stat-value text-warning mt-2">{{ $maintenance }}</div></div></div></div>
        <div class="card rooms-card mb-4"><div class="card-body"><form method="GET" class="row align-items-end"><div class="col-md-4"><label class="font-weight-bold">Property</label><select name="property_id" class="form-control"><option value="">All properties</option>@foreach($properties as $property)<option value="{{ $property->id }}" @selected(request('property_id') == $property->id)>{{ $property->name }}</option>@endforeach</select></div><div class="col-md-2 mt-3 mt-md-0"><button class="btn btn-outline-primary" type="submit"><i class="fas fa-filter mr-1"></i> Filter</button></div></form></div></div>
        <div class="card rooms-card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0 align-middle"><thead class="thead-light"><tr><th>Room</th><th>Room type</th><th>Floor</th><th>Nightly price</th><th>Operational status</th><th>Actions</th></tr></thead><tbody>@forelse($addrooms as $addroom)@php($roomStatus = $addroom->operational_status ?: ($addroom->status ? 'available' : 'dirty'))<tr><td class="font-weight-bold">{{ $addroom->room_number }}</td><td>{{ $addroom->room_type }}</td><td>{{ $addroom->floor }}</td><td>UGX {{ number_format($addroom->price, 0) }}</td><td><span class="room-status room-status-{{ $roomStatus }}">{{ ucwords(str_replace('_', ' ', $roomStatus)) }}</span></td><td><a href="{{ url('/editrooms') }}" class="btn btn-sm btn-outline-primary">Edit</a> <a href="{{ url('delete_record1', $addroom->id) }}" class="btn btn-sm btn-outline-danger">Delete</a></td></tr>@empty<tr><td colspan="6"><div class="text-center room-empty"><div class="room-empty-icon mb-3"><i class="fas fa-bed"></i></div><h5>No rooms configured yet</h5><p class="text-muted">Add your first room to start managing availability and pricing.</p><a href="{{ url('/add_rooms') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Create First Room</a></div></td></tr>@endforelse</tbody></table></div></div></div>
    </main></div>
</div>
@include('admin.script')
</body>
</html>
