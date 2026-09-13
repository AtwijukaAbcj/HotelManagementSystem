<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Maintenance Requests</title>
    @include('admin.css')
</head>
<body>
<div class="main-wrapper">
    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="page-title mb-1">Maintenance requests</h3>
                    <p class="text-muted mb-0">Track room defects, repairs, and vendor requests.</p>
                </div>
            </div>

            @if(session()->has('message'))
                <div class="alert alert-success">{{ session()->get('message') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 rounded-20">
                        <div class="card-body">
                            <h5 class="mb-3">Submit maintenance ticket</h5>
                            <form action="{{ route('maintenance.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Room</label>
                                    <select name="room_id" class="form-control" required>
                                        <option value="">Select room</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}">Room {{ $room->room_number }} - {{ $room->room_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Issue title</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" rows="4" class="form-control" required></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Priority</label>
                                            <select name="priority" class="form-control" required>
                                                <option value="low">Low</option>
                                                <option value="normal" selected>Normal</option>
                                                <option value="high">High</option>
                                                <option value="urgent">Urgent</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" class="form-control" required>
                                                <option value="open" selected>Open</option>
                                                <option value="in_progress">In progress</option>
                                                <option value="resolved">Resolved</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Requested by</label>
                                            <input type="text" name="requested_by" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Assigned to</label>
                                            <select name="assigned_to" class="form-control">
                                                <option value="">Unassigned</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->name }}">{{ $employee->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit request</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card shadow-sm border-0 rounded-20">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Room</th>
                                            <th>Title</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Assigned</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($requests as $request)
                                            <tr>
                                                <td>Room {{ $request->room?->room_number ?? 'N/A' }}</td>
                                                <td>{{ $request->title }}</td>
                                                <td><span class="badge badge-pill badge-light">{{ ucfirst($request->priority) }}</span></td>
                                                <td><span class="badge badge-pill badge-warning">{{ str_replace('_', ' ', ucfirst($request->status)) }}</span></td>
                                                <td>{{ $request->assigned_to ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">No maintenance requests logged.</td>
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
    </div>
</div>

@include('admin.script')
</body>
</html>
