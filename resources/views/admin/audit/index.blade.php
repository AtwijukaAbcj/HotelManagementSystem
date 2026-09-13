<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Audit Logs</title>
    @include('admin.css')
</head>
<body>
<div class="main-wrapper">
    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header mb-4">
                <h3 class="page-title mb-1">Audit logs</h3>
                <p class="text-muted mb-0">Review system changes and critical hotel actions.</p>
            </div>

            <div class="card shadow-sm border-0 rounded-20">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Event</th>
                                    <th>User</th>
                                    <th>IP</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->event }}</td>
                                        <td>{{ $log->user->name ?? 'System' }}</td>
                                        <td>{{ $log->ip_address ?? '-' }}</td>
                                        <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No audit entries yet.</td>
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
