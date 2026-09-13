<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    @include('admin.css')
    <style>
        .users-page { background: #f6f8fb; min-height: 100vh; }
        .users-page .page-wrapper { padding-top: 80px; }
        .users-container { max-width: 1180px; margin: 0 auto; padding: 30px 28px 56px; }
        .users-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        .role-badge { background: #eaf2ff; color: #1d4ed8; border-radius: 999px; padding: .4rem .7rem; font-size: .75rem; font-weight: 600; }
        @media (max-width: 767px) { .users-page .page-wrapper { padding-top: 72px; } .users-container { padding: 24px 16px 40px; } }
    </style>
</head>
<body>
<div class="main-wrapper users-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="users-container">
        <div class="mb-4 d-flex align-items-start justify-content-between" style="gap:16px"><div><h3 class="page-title mb-1">Users</h3><p class="text-muted mb-0">Assign existing roles to users. Create and configure roles from the Roles page.</p></div><div><a href="{{ route('admin.roles') }}" class="btn btn-outline-secondary mr-2">Roles</a><a href="{{ route('admin.roles.permissions') }}" class="btn btn-outline-primary">Permissions</a></div></div>
        @if(session('message'))<div class="alert alert-success users-card">{{ session('message') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger users-card"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="card users-card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="thead-light"><tr><th>User</th><th>Email</th><th>Current role</th><th>Assign role</th></tr></thead><tbody>@forelse($users as $user)<tr><td class="font-weight-bold">{{ $user->name }}</td><td>{{ $user->email }}</td><td><span class="role-badge">{{ ucfirst(str_replace('_', ' ', $user->role ?: 'reception')) }}</span></td><td><form method="POST" action="{{ route('admin.roles.update', $user) }}" class="d-flex" style="gap:8px">@csrf @method('PUT')<select name="role" class="form-control form-control-sm" style="max-width:190px">@foreach($roles as $role)<option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>@endforeach</select><button class="btn btn-sm btn-outline-primary" type="submit">Save</button></form></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-5">No users found.</td></tr>@endforelse</tbody></table></div>@if($users->hasPages())<div class="p-3">{{ $users->links() }}</div>@endif</div></div>
    </main></div>
</div>
@include('admin.script')
</body>
</html>
