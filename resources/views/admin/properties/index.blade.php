<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties</title>
    @include('admin.css')
    <style>
        .properties-page { background: #f6f8fb; min-height: 100vh; }
        .properties-page .page-wrapper { padding-top: 80px; }
        .properties-container { max-width: 1240px; margin: 0 auto; padding: 30px 28px 56px; }
        .properties-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        .properties-empty { padding: 64px 24px; }
        .properties-empty-icon { width: 58px; height: 58px; border-radius: 16px; background: #eaf2ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        @media (max-width: 767px) { .properties-page .page-wrapper { padding-top: 72px; } .properties-container { padding: 24px 16px 40px; } }
    </style>
</head>
<body>
<div class="main-wrapper properties-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="properties-container">
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-4" style="gap:16px"><div><h3 class="page-title mb-1">Properties</h3><p class="text-muted mb-0">Manage hotels, branches, and property-level operations.</p></div><div class="d-flex" style="gap:8px"><a href="{{ route('properties.dashboard') }}" class="btn btn-outline-primary"><i class="fas fa-chart-line mr-1"></i> Property dashboard</a><a href="{{ route('properties.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Add Property</a></div></div>
        @if(session('message'))<div class="alert alert-success properties-card">{{ session('message') }}</div>@endif
        <div class="card properties-card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="thead-light"><tr><th>Property</th><th>Code</th><th>Address</th><th>Timezone</th><th>Status</th></tr></thead><tbody>@forelse($properties as $property)<tr><td class="font-weight-bold">{{ $property->name }}</td><td>{{ $property->code }}</td><td>{{ $property->address ?: '-' }}</td><td>{{ $property->timezone ?: 'UTC' }}</td><td><span class="badge badge-success">{{ $property->is_active ? 'Active' : 'Inactive' }}</span></td></tr>@empty<tr><td colspan="5"><div class="text-center properties-empty"><div class="properties-empty-icon mb-3"><i class="fas fa-building"></i></div><h5>No properties created yet</h5><p class="text-muted">Create your first property to start managing locations.</p><a href="{{ route('properties.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Create First Property</a></div></td></tr>@endforelse</tbody></table></div></div></div>
    </main></div>
</div>
@include('admin.script')
</body>
</html>
