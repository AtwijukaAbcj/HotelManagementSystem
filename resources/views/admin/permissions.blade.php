<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Permissions</title>
    @include('admin.css')
    <style>
        .permissions-page { background: #f6f8fb; min-height: 100vh; }
        .permissions-page .page-wrapper { padding-top: 80px; }
        .permissions-container { max-width: 1320px; margin: 0 auto; padding: 30px 28px 56px; }
        .permissions-card { border: 1px solid #e7ebf1; border-radius: 12px; box-shadow: 0 8px 22px rgba(15,23,42,.05); }
        .permissions-table th { white-space: nowrap; font-size: .78rem; }
        .permissions-table td { min-width: 130px; }
        .permissions-table .module-name { min-width: 190px; font-weight: 600; }
        @media (max-width: 767px) { .permissions-page .page-wrapper { padding-top: 72px; } .permissions-container { padding: 24px 16px 40px; } }
    </style>
</head>
<body>
<div class="main-wrapper permissions-page">
    @include('admin.header')
    @include('admin.sidebar')
    <div class="page-wrapper"><main class="permissions-container">
        <div class="mb-4 d-flex align-items-start justify-content-between" style="gap:16px">
            <div><h3 class="page-title mb-1">Module Permissions</h3><p class="text-muted mb-0">Choose what each role can view, create, edit, delete, or manage.</p></div>
            <a href="{{ route('admin.roles') }}" class="btn btn-outline-secondary">Back to roles</a>
        </div>
        @if(session('message'))<div class="alert alert-success permissions-card">{{ session('message') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger permissions-card"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @foreach($roles as $role)
            <section id="{{ $role }}" class="card permissions-card mb-4">
                <div class="card-header bg-white d-flex align-items-center justify-content-between" style="gap:16px">
                    <div><h5 class="mb-1">{{ ucfirst(str_replace('_', ' ', $role)) }}</h5><small class="text-muted">{{ $role === 'admin' ? 'Administrators always have full access.' : 'Changes apply to every user with this role.' }}</small></div>
                    @if($role !== 'admin')<button form="permissions-{{ $role }}" class="btn btn-primary btn-sm" type="submit">Save permissions</button>@endif
                </div>
                <form id="permissions-{{ $role }}" method="POST" action="{{ route('admin.roles.permissions.update', $role) }}">
                    @csrf @method('PUT')
                    <div class="table-responsive"><table class="table table-hover align-middle mb-0 permissions-table">
                        <thead class="thead-light"><tr><th class="module-name">Module</th>@foreach($actions as $action)<th>{{ ucfirst(str_replace('-', ' ', $action)) }}</th>@endforeach</tr></thead>
                        <tbody>@foreach($modules as $module => $label)<tr><td class="module-name">{{ $label }}</td>@foreach($actions as $action)<td><div class="custom-control custom-checkbox"><input class="custom-control-input" type="checkbox" id="{{ $role }}-{{ $module }}-{{ $action }}" name="permissions[]" value="{{ $module }}.{{ $action }}" @checked($permissions[$role][$module][$action]) @disabled($role === 'admin')><label class="custom-control-label" for="{{ $role }}-{{ $module }}-{{ $action }}"></label></div></td>@endforeach</tr>@endforeach</tbody>
                    </table></div>
                </form>
            </section>
        @endforeach
    </main></div>
</div>
@include('admin.script')
</body>
</html>
