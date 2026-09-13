<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Dashboard</title>
    @include('admin.css')
    <style>
        .property-dashboard-page { background: #f6f8fb; min-height: 100vh; }
        .property-dashboard-page .page-wrapper { padding-top: 80px; }
        .property-dashboard-container { max-width: 1320px; margin: 0 auto; padding: 30px 28px 56px; }
        .property-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        .property-metric { background: #f8fafc; border: 1px solid #e7ebf1; border-radius: 10px; padding: 16px; }
        @media (max-width: 767px) { .property-dashboard-page .page-wrapper { padding-top: 72px; } .property-dashboard-container { padding: 24px 16px 40px; } }
    </style>
</head>
<body>
<div class="main-wrapper property-dashboard-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="property-dashboard-container">
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-4" style="gap:16px"><div><h3 class="page-title mb-1">Property Dashboard</h3><p class="text-muted mb-0">Compare room inventory, occupancy, and pricing across properties.</p></div><a href="{{ route('properties.index') }}" class="btn btn-outline-primary">Manage properties</a></div>
        @forelse($propertySummaries as $summary)
            @php($property = $summary['property'])
            <div class="card property-card mb-4"><div class="card-body"><div class="d-flex justify-content-between align-items-center mb-4"><h5 class="mb-0">{{ $property->name }} <span class="text-muted font-weight-normal">({{ $property->code }})</span></h5><span class="badge badge-primary">{{ $summary['total_rooms'] ?? 0 }} rooms</span></div><div class="row mb-4"><div class="col-md-3 mb-3 mb-md-0"><div class="property-metric"><small class="text-muted d-block">Total rooms</small><strong class="h4">{{ $summary['total_rooms'] ?? 0 }}</strong></div></div><div class="col-md-3 mb-3 mb-md-0"><div class="property-metric"><small class="text-muted d-block">Occupied</small><strong class="h4 text-primary">{{ $summary['occupied_rooms'] ?? 0 }}</strong></div></div><div class="col-md-3 mb-3 mb-md-0"><div class="property-metric"><small class="text-muted d-block">Available</small><strong class="h4 text-success">{{ $summary['available_rooms'] ?? 0 }}</strong></div></div><div class="col-md-3"><div class="property-metric"><small class="text-muted d-block">Timezone</small><strong>{{ $property->timezone ?: 'UTC' }}</strong></div></div></div><div class="table-responsive"><table class="table table-hover mb-0"><thead class="thead-light"><tr><th>Room type</th><th>Rooms</th><th>Average price</th><th>Minimum</th><th>Maximum</th></tr></thead><tbody>@forelse($summary['rooms'] as $type)<tr><td>{{ $type->room_type }}</td><td>{{ $type->total_rooms }}</td><td>UGX {{ number_format($type->avg_price ?? 0, 0) }}</td><td>UGX {{ number_format($type->min_price ?? 0, 0) }}</td><td>UGX {{ number_format($type->max_price ?? 0, 0) }}</td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-4">No room pricing configured yet.</td></tr>@endforelse</tbody></table></div></div></div>
        @empty
            <div class="card property-card"><div class="card-body text-center py-5"><i class="fas fa-building fa-2x text-primary mb-3"></i><h5>No properties available</h5><p class="text-muted">Create a property to start comparing operations.</p><a href="{{ route('properties.index') }}" class="btn btn-primary">Create property</a></div></div>
        @endforelse
    </main></div>
</div>
@include('admin.script')
</body>
</html>
