<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Room Types & Pricing</title>
    @include('admin.css')
</head>
<body>
<div class="main-wrapper">
    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="page-title mb-1">Room types & pricing</h3>
                    <p class="text-muted mb-0">Manage room categories, inventory, and standard rates.</p>
                </div>
                <a href="{{ url('/add_rooms') }}" class="btn btn-primary">Add room</a>
            </div>

            <div class="card shadow-sm border-0 rounded-20">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Room type</th>
                                    <th>Rooms</th>
                                    <th>Average price</th>
                                    <th>Min price</th>
                                    <th>Max price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roomTypes as $type)
                                    <tr>
                                        <td>{{ $type->room_type }}</td>
                                        <td>{{ $type->total_rooms }}</td>
                                        <td>${{ number_format($type->avg_price ?? 0, 2) }}</td>
                                        <td>${{ number_format($type->min_price ?? 0, 2) }}</td>
                                        <td>${{ number_format($type->max_price ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No room types configured yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.script')
</body>
</html>
