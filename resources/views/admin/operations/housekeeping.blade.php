<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Housekeeping Board</title>
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
                    <h3 class="page-title mb-1">Housekeeping board</h3>
                    <p class="text-muted mb-0">Track room cleaning, inspections, and staff assignments.</p>
                </div>
            </div>

            @if(session()->has('message'))
                <div class="alert alert-success">{{ session()->get('message') }}</div>
            @endif

            <div class="row">
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 rounded-20">
                        <div class="card-body">
                            <h5 class="mb-3">Assign housekeeping task</h5>
                            <form action="{{ route('housekeeping.store') }}" method="POST">
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
                                    <label>Task type</label>
                                    <input type="text" name="task_type" class="form-control" placeholder="Vacuum, restock, deep clean..." required>
                                </div>
                                <div class="form-group">
                                    <label>Assigned to</label>
                                    <input type="text" name="assignee" class="form-control" required>
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
                                                <option value="pending" selected>Pending</option>
                                                <option value="in_progress">In progress</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Notes</label>
                                    <textarea name="notes" class="form-control" rows="4"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Save task</button>
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
                                            <th>Task</th>
                                            <th>Assignee</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tasks as $task)
                                            <tr>
                                                <td>Room {{ $task->room?->room_number ?? 'N/A' }}</td>
                                                <td>{{ $task->task_type }}</td>
                                                <td>{{ $task->assignee }}</td>
                                                <td><span class="badge badge-pill badge-light">{{ ucfirst($task->priority) }}</span></td>
                                                <td><span class="badge badge-pill badge-info">{{ str_replace('_', ' ', ucfirst($task->status)) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">No housekeeping tasks assigned yet.</td>
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
