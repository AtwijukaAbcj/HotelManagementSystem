<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Hotel Management Dashboard</title>
    @include('admin.css')
    <style>
        .hotel-dashboard-shell { background: #f4f7fb; min-height: 100vh; }
        .dashboard-hero { background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%); border-radius: 24px; padding: 28px 30px; color: white; margin-bottom: 28px; box-shadow: 0 15px 30px rgba(15, 23, 42, .18); }
        .dashboard-hero h2 { margin: 0; font-size: 2rem; font-weight: 700; }
        .dashboard-hero small { opacity: .8; }
        .stat-card { background: white; border: 0; border-radius: 18px; box-shadow: 0 12px 28px rgba(15, 23, 42, .06); transition: transform .2s ease; }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card .card-body { padding: 24px 22px; }
        .stat-label { color: #64748b; font-size: .78rem; text-transform: uppercase; letter-spacing: .08em; }
        .stat-value { font-size: 2rem; font-weight: 700; color: #0f172a; margin-top: 12px; }
        .stat-icon { width: 50px; height: 50px; display: inline-flex; align-items: center; justify-content: center; border-radius: 14px; font-size: 1.2rem; }
        .section-card { background: white; border: 0; border-radius: 18px; box-shadow: 0 12px 28px rgba(15, 23, 42, .06); }
        .section-header { padding: 20px 24px 0; }
        .section-title { font-size: 1.05rem; font-weight: 700; color: #0f172a; }
        .table thead th { font-size: .78rem; text-transform: uppercase; letter-spacing: .06em; color: #64748b; background: #f8fafc; }
        .badge-pill { border-radius: 999px; padding: .45rem .8rem; }
        .status-available { background: rgba(16, 185, 129, .12); color: #047857; }
        .status-occupied { background: rgba(59, 130, 246, .12); color: #1d4ed8; }
        .status-reserved { background: rgba(245, 158, 11, .12); color: #b45309; }
        .status-dirty { background: rgba(239, 68, 68, .12); color: #b91c1c; }
        .status-maintenance { background: rgba(107, 114, 128, .12); color: #374151; }
        .chart-card { min-height: 360px; }
        .chart-wrap { position: relative; height: 270px; }
    </style>
</head>
<body>
<div class="main-wrapper hotel-dashboard-shell">
    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="dashboard-hero">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <small>Operations overview</small>
                        <h2>{{ \App\Greeting\Greetings::getNepaliGreeting(Auth::user()->name) }}!</h2>
                    </div>
                    <a href="{{ url('form/addbooking') }}" class="btn btn-light btn-lg font-weight-bold">New booking</a>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Bookings</div>
                                <div class="stat-value">{{ $lastBookingId ?? 0 }}</div>
                            </div>
                            <span class="stat-icon bg-primary text-white"><i class="fas fa-calendar-check"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Rooms</div>
                                <div class="stat-value">{{ $stats['total_rooms'] ?? 0 }}</div>
                            </div>
                            <span class="stat-icon bg-success text-white"><i class="fas fa-bed"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Occupied</div>
                                <div class="stat-value">{{ $stats['occupied'] ?? 0 }}</div>
                            </div>
                            <span class="stat-icon bg-warning text-white"><i class="fas fa-door-open"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="stat-label">Guests</div>
                                <div class="stat-value">{{ $recentGuests->count() ?? 0 }}</div>
                            </div>
                            <span class="stat-icon bg-info text-white"><i class="fas fa-user-friends"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <div class="card section-card chart-card">
                        <div class="section-header">
                            <div class="section-title">Room status distribution</div>
                            <small class="text-muted">Current operational state</small>
                        </div>
                        <div class="card-body">
                            <div class="chart-wrap">
                                <canvas id="roomStatusChart" aria-label="Room status distribution"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card section-card chart-card">
                        <div class="section-header">
                            <div class="section-title">Room type pricing and inventory</div>
                            <small class="text-muted">Average nightly rate and room count</small>
                        </div>
                        <div class="card-body">
                            <div class="chart-wrap">
                                <canvas id="roomTypeChart" aria-label="Room type pricing and inventory"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-lg-7">
                    <div class="card section-card">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <div class="section-title">Recent bookings</div>
                            <a href="{{ url('form/allbooking') }}" class="btn btn-sm btn-outline-primary">View all</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Guest</th>
                                            <th>Room</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($data as $booking)
                                            <tr>
                                                <td>{{ $booking->name }}</td>
                                                <td>{{ $booking->room_number }}</td>
                                                <td>{{ $booking->room_type }}</td>
                                                <td>
                                                    <span class="badge badge-pill {{ $booking->status ? 'status-available' : 'status-dirty' }}">
                                                        {{ $booking->status ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted py-4">No recent bookings yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card section-card">
                        <div class="section-header">
                            <div class="section-title">Room status overview</div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Available</span>
                                    <span class="badge badge-pill status-available">{{ $stats['available'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Reserved</span>
                                    <span class="badge badge-pill status-reserved">{{ $stats['reserved'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Occupied</span>
                                    <span class="badge badge-pill status-occupied">{{ $stats['occupied'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Dirty</span>
                                    <span class="badge badge-pill status-dirty">{{ $stats['dirty'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Maintenance</span>
                                    <span class="badge badge-pill status-maintenance">{{ $stats['maintenance'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-7">
                    <div class="card section-card">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <div class="section-title">Room type pricing</div>
                            <a href="{{ route('room-types') }}" class="btn btn-sm btn-outline-primary">Manage rates</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Room type</th>
                                            <th>Rooms</th>
                                            <th>Avg. rate</th>
                                            <th>Range</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($roomTypeSummary as $type)
                                            <tr>
                                                <td>{{ $type->room_type }}</td>
                                                <td>{{ $type->total_rooms }}</td>
                                                <td>${{ number_format($type->avg_price ?? 0, 2) }}</td>
                                                <td>${{ number_format($type->min_price ?? 0, 2) }} - ${{ number_format($type->max_price ?? 0, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted py-4">No room types configured.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card section-card">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <div class="section-title">Recent guests</div>
                            <a href="{{ route('guests.index') }}" class="btn btn-sm btn-outline-primary">Open guest list</a>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-column gap-3">
                                @forelse($recentGuests as $guest)
                                    <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                        <div>
                                            <div class="font-weight-bold">{{ $guest->first_name }} {{ $guest->last_name }}</div>
                                            <small class="text-muted">{{ $guest->email ?: $guest->phone ?: 'No contact info' }}</small>
                                        </div>
                                        <span class="badge badge-pill status-available">Guest</span>
                                    </div>
                                @empty
                                    <div class="text-muted text-center py-3">No guest profiles yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('roomStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Available', 'Reserved', 'Occupied', 'Dirty', 'Maintenance'],
            datasets: [{
                data: [
                    {{ $stats['available'] ?? 0 }},
                    {{ $stats['reserved'] ?? 0 }},
                    {{ $stats['occupied'] ?? 0 }},
                    {{ $stats['dirty'] ?? 0 }},
                    {{ $stats['maintenance'] ?? 0 }}
                ],
                backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#6b7280'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    new Chart(document.getElementById('roomTypeChart'), {
        type: 'bar',
        data: {
            labels: @json($roomTypeSummary->pluck('room_type')->values()),
            datasets: [
                {
                    label: 'Average rate',
                    data: @json($roomTypeSummary->pluck('avg_price')->map(fn ($price) => (float) $price)->values()),
                    backgroundColor: '#2563eb',
                    borderRadius: 6,
                    yAxisID: 'price'
                },
                {
                    label: 'Rooms',
                    data: @json($roomTypeSummary->pluck('total_rooms')->values()),
                    backgroundColor: '#14b8a6',
                    borderRadius: 6,
                    yAxisID: 'rooms'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                price: { beginAtZero: true, position: 'left', title: { display: true, text: 'Rate ($)' } },
                rooms: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Rooms' } }
            },
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
</body>
</html>