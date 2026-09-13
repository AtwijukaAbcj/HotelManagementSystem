<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Room Status Board</title>
    @include('admin.css')
    <style>
        .status-board { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
        .room-tile { border-left: 5px solid #6c757d; }
        .room-tile.status-available { border-color: #28a745; }
        .room-tile.status-reserved { border-color: #007bff; }
        .room-tile.status-occupied { border-color: #6f42c1; }
        .room-tile.status-dirty { border-color: #fd7e14; }
        .room-tile.status-cleaning { border-color: #17a2b8; }
        .room-tile.status-maintenance { border-color: #dc3545; }
        .room-status { text-transform: capitalize; }
    </style>
</head>
<body>
<div class="main-wrapper">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        @if(session()->has('message'))
                            <div class="alert alert-success">{{ session('message') }}</div>
                        @endif
                        <h4 class="card-title float-left mt-2">Room Status Board</h4>
                        <a href="{{ url('/all_rooms') }}" class="btn btn-primary float-right">Manage Rooms</a>
                    </div>
                </div>
            </div>
            <div class="status-board">
                @forelse($rooms as $room)
                    @php($stay = $room->stays->first())
                    <div class="card room-tile status-{{ $room->operational_status }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="mb-1">Room {{ $room->room_number }}</h5>
                                <span class="badge badge-light room-status">{{ $room->operational_status }}</span>
                            </div>
                            <p class="mb-3">{{ $room->room_type }} · {{ number_format($room->price, 2) }}</p>
                            @if($stay)
                                <p class="small mb-2">{{ $stay->guest->full_name ?? 'Guest' }}<br>{{ $stay->arrival_date->format('d M Y') }} - {{ $stay->departure_date->format('d M Y') }}</p>
                                @if($stay->status === 'reserved')
                                    <form method="POST" action="{{ route('stays.check-in', $stay->id) }}">@csrf<button class="btn btn-sm btn-success btn-block">Check in</button></form>
                                @elseif($stay->status === 'checked_in')
                                    <form method="POST" action="{{ route('stays.check-out', $stay->id) }}">@csrf<button class="btn btn-sm btn-warning btn-block">Check out</button></form>
                                @endif
                            @endif
                            <form method="POST" action="{{ route('rooms.operational-status', $room->id) }}" class="mt-2">@csrf
                                <select name="operational_status" class="form-control form-control-sm" onchange="this.form.submit()">
                                    @foreach(['available', 'reserved', 'occupied', 'dirty', 'cleaning', 'maintenance'] as $status)
                                        <option value="{{ $status }}" @selected($room->operational_status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">No rooms have been added yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@include('admin.script')
</body>
</html>
