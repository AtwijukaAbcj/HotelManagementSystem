<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers</title>
    @include('admin.css')
    <style>
        .supplier-page { background: #f6f8fb; min-height: 100vh; }
        .supplier-page .page-wrapper { padding-top: 80px; }
        .supplier-container { max-width: 1180px; margin: 0 auto; padding: 30px 28px 56px; }
        .supplier-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        .supplier-empty { padding: 58px 24px; }
        .supplier-empty-icon { width: 58px; height: 58px; border-radius: 16px; background: #eaf2ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        @media (max-width: 767px) { .supplier-page .page-wrapper { padding-top: 72px; } .supplier-container { padding: 24px 16px 40px; } }
    </style>
</head>
<body>
<div class="main-wrapper supplier-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="supplier-container">
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-4" style="gap:16px"><div><h3 class="page-title mb-1">Suppliers</h3><p class="text-muted mb-0">Maintain supplier contacts for purchasing and inventory.</p></div><a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Add Supplier</a></div>
        @if(session('message'))<div class="alert alert-success supplier-card">{{ session('message') }}</div>@endif
        <div class="card supplier-card mb-4"><div class="card-body"><form method="GET" action="{{ route('suppliers.index') }}" class="row align-items-end"><div class="col-md-5"><label class="font-weight-bold">Search suppliers</label><input name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, code, contact, or email"></div><div class="col-md-2"><button class="btn btn-outline-primary" type="submit"><i class="fas fa-search mr-1"></i> Search</button></div></form></div></div>
        <div class="card supplier-card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="thead-light"><tr><th>Name</th><th>Code</th><th>Contact</th><th>Email</th><th>Phone</th><th>Status</th></tr></thead><tbody>@forelse($suppliers as $supplier)<tr><td class="font-weight-bold">{{ $supplier->name }}</td><td>{{ $supplier->code }}</td><td>{{ $supplier->contact_name ?: '-' }}</td><td>{{ $supplier->email ?: '-' }}</td><td>{{ $supplier->phone ?: '-' }}</td><td><span class="badge badge-success">{{ ucfirst($supplier->status) }}</span></td></tr>@empty<tr><td colspan="6"><div class="text-center supplier-empty"><div class="supplier-empty-icon mb-3"><i class="fas fa-address-book"></i></div><h5>No suppliers registered yet</h5><p class="text-muted">Create your first supplier to use it in purchase orders.</p><a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Create First Supplier</a></div></td></tr>@endforelse</tbody></table></div><div class="p-3">{{ $suppliers->links() }}</div></div></div>
    </main></div>
</div>
@include('admin.script')
</body>
</html>
