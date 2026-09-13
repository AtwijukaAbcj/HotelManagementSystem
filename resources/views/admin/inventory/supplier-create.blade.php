<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
    @include('admin.css')
    <style>
        .supplier-create-page { background: #f6f8fb; min-height: 100vh; }
        .supplier-create-page .page-wrapper { padding-top: 80px; }
        .supplier-create-container { max-width: 860px; margin: 0 auto; padding: 30px 28px 56px; }
        .supplier-create-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        @media (max-width: 767px) { .supplier-create-page .page-wrapper { padding-top: 72px; } .supplier-create-container { padding: 24px 16px 40px; } }
    </style>
</head>
<body>
<div class="main-wrapper supplier-create-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="supplier-create-container">
        <div class="d-flex justify-content-between align-items-start mb-4"><div><h3 class="page-title mb-1">Add Supplier</h3><p class="text-muted mb-0">Create a supplier profile for purchase orders and stock management.</p></div><a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
        @if($errors->any())<div class="alert alert-danger supplier-create-card"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="card supplier-create-card"><div class="card-body"><form method="POST" action="{{ route('suppliers.store') }}">@csrf<div class="row"><div class="col-md-7 mb-3"><label class="font-weight-bold">Supplier name</label><input name="name" value="{{ old('name') }}" class="form-control" required></div><div class="col-md-5 mb-3"><label class="font-weight-bold">Supplier code</label><input name="code" value="{{ old('code') }}" class="form-control" placeholder="SUP-001" required></div><div class="col-md-6 mb-3"><label class="font-weight-bold">Contact name</label><input name="contact_name" value="{{ old('contact_name') }}" class="form-control"></div><div class="col-md-3 mb-3"><label class="font-weight-bold">Phone</label><input name="phone" value="{{ old('phone') }}" class="form-control"></div><div class="col-md-3 mb-3"><label class="font-weight-bold">Email</label><input name="email" type="email" value="{{ old('email') }}" class="form-control"></div><div class="col-md-12 mb-3"><label class="font-weight-bold">Address</label><input name="address" value="{{ old('address') }}" class="form-control"></div><div class="col-md-12 mb-3"><label class="font-weight-bold">Notes</label><textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea></div></div><div class="d-flex justify-content-end" style="gap:10px"><a href="{{ route('suppliers.index') }}" class="btn btn-light">Cancel</a><button class="btn btn-primary" type="submit">Save Supplier</button></div></form></div></div>
    </main></div>
</div>
@include('admin.script')
</body>
</html>
