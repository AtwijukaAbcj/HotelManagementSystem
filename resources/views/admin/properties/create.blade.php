<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property</title>
    @include('admin.css')
    <style>
        .property-create-page { background: #f6f8fb; min-height: 100vh; }
        .property-create-page .page-wrapper { padding-top: 80px; }
        .property-create-container { max-width: 760px; margin: 0 auto; padding: 30px 28px 56px; }
        .property-create-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        @media (max-width: 767px) { .property-create-page .page-wrapper { padding-top: 72px; } .property-create-container { padding: 24px 16px 40px; } }
    </style>
</head>
<body>
<div class="main-wrapper property-create-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="property-create-container">
        <div class="d-flex justify-content-between align-items-start mb-4"><div><h3 class="page-title mb-1">Add Property</h3><p class="text-muted mb-0">Create a hotel, branch, or operating location.</p></div><a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
        @if($errors->any())<div class="alert alert-danger property-create-card"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="card property-create-card"><div class="card-body"><form method="POST" action="{{ route('properties.store') }}">@csrf<div class="form-group"><label>Property name</label><input name="name" value="{{ old('name') }}" class="form-control" required></div><div class="form-group"><label>Property code</label><input name="code" value="{{ old('code') }}" class="form-control" placeholder="HBR-001" required></div><div class="form-group"><label>Address</label><input name="address" value="{{ old('address') }}" class="form-control"></div><div class="form-group"><label>Timezone</label><input name="timezone" value="{{ old('timezone', 'UTC') }}" class="form-control" required></div><div class="d-flex justify-content-end" style="gap:10px"><a href="{{ route('properties.index') }}" class="btn btn-light">Cancel</a><button class="btn btn-primary" type="submit">Save Property</button></div></form></div></div>
    </main></div>
</div>
@include('admin.script')
</body>
</html>
