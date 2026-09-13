<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles</title>
    @include('admin.css')
    <style>
        .roles-page { background: #f6f8fb; min-height: 100vh; }
        .roles-page .page-wrapper { padding-top: 80px; }
        .roles-container { max-width: 1180px; margin: 0 auto; padding: 30px 28px 56px; }
        .roles-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        .role-badge { background: #eaf2ff; color: #1d4ed8; border-radius: 999px; padding: .4rem .7rem; font-size: .75rem; font-weight: 600; }
        @media (max-width: 767px) { .roles-page .page-wrapper { padding-top: 72px; } .roles-container { padding: 24px 16px 40px; } }
    </style>
</head>
<body>
<div class="main-wrapper roles-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="roles-container">
        <div class="mb-4 d-flex align-items-start justify-content-between" style="gap:16px"><div><h3 class="page-title mb-1">Roles</h3><p class="text-muted mb-0">Create roles and configure their access to system modules.</p></div><div><a href="{{ route('admin.users') }}" class="btn btn-outline-secondary mr-2">Users</a><a href="{{ route('admin.roles.permissions') }}" class="btn btn-outline-primary">Module permissions</a></div></div>
        @if(session('message'))<div class="alert alert-success roles-card">{{ session('message') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger roles-card"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="card roles-card mb-4"><div class="card-body"><h5 class="mb-3">Create a role</h5><form method="POST" action="{{ route('admin.roles.store') }}" class="form-row align-items-end">@csrf<div class="col-md-5"><label for="role-name">Role name</label><input id="role-name" name="name" class="form-control" placeholder="spa_manager" required maxlength="50"></div><div class="col-md-auto"><button class="btn btn-primary" type="submit">Create role</button></div></form></div></div>
        <div class="card roles-card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="thead-light"><tr><th>Role</th><th>Users assigned</th><th>Access configuration</th></tr></thead><tbody>@foreach($roles as $role)<tr><td><span class="role-badge">{{ ucfirst(str_replace('_', ' ', $role)) }}</span></td><td>{{ $roleCounts[$role] ?? 0 }}</td><td><a href="{{ route('admin.roles.permissions') }}#{{ $role }}" class="btn btn-sm btn-outline-primary">Configure permissions</a></td></tr>@endforeach</tbody></table></div></div></div>
    </main></div>
</div>
@include('admin.script')
</body>
</html>
