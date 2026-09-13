<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Notifications</title>
    @include('admin.css')
</head>
<body>
    <div class="main-wrapper">
        @include('admin.header')
        @include('admin.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">
                @if (session('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                @endif

                <div class="page-header d-flex align-items-center justify-content-between flex-wrap">
                    <div>
                        <h4 class="page-title">Notifications</h4>
                        <p class="text-muted mb-0">Guest and operations communication</p>
                    </div>
                    <span class="badge badge-light">{{ $notifications->count() }} logged</span>
                </div>

                <div class="row">
                    <div class="col-xl-4 col-lg-5 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Send notification</h5>
                                <small class="text-muted">Queue a message for a guest or team</small>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('notifications.send') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="property_id">Property</label>
                                        <select id="property_id" name="property_id" class="form-control">
                                            <option value="">Default property</option>
                                            @foreach ($properties as $property)
                                                <option value="{{ $property->id }}">{{ $property->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="channel">Channel</label>
                                        <select id="channel" name="channel" class="form-control" required>
                                            <option value="email">Email</option>
                                            <option value="sms">SMS</option>
                                            <option value="whatsapp">WhatsApp</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="recipient">Recipient</label>
                                        <input id="recipient" type="text" name="recipient" class="form-control" placeholder="guest@example.com" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="subject">Subject</label>
                                        <input id="subject" type="text" name="subject" class="form-control" placeholder="Hotel update">
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="message">Message</label>
                                        <textarea id="message" name="message" class="form-control" rows="5" placeholder="Write your message..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">Queue notification</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8 col-lg-7 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-0">Notification log</h5>
                                    <small class="text-muted">Recent delivery activity</small>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive border-0">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th>Channel</th>
                                                <th>Recipient</th>
                                                <th>Subject</th>
                                                <th>Status</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($notifications as $notification)
                                        <tr>
                                            <td>{{ ucfirst($notification->channel) }}</td>
                                            <td>{{ $notification->recipient }}</td>
                                            <td>{{ $notification->subject ?? '-' }}</td>
                                            <td><span class="badge badge-{{ $notification->status === 'sent' ? 'success' : ($notification->status === 'not_configured' ? 'warning' : 'danger') }}">{{ ucwords(str_replace('_', ' ', $notification->status)) }}</span></td>
                                            <td>{{ $notification->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                            @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No notifications yet.</td>
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
