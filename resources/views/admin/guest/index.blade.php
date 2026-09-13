<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Guest Profiles</title>
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
                    <h3 class="page-title mb-1">Guest profiles</h3>
                    <p class="text-muted mb-0">Manage guest records and stay history.</p>
                </div>
                <a href="{{ route('guests.create') }}" class="btn btn-primary">Add guest</a>
            </div>

            <div class="card shadow-sm border-0 rounded-20">
                <div class="card-body">
                    <form method="GET" class="row align-items-end mb-3"><div class="col-md-4"><label class="form-label">Property</label><select name="property_id" class="form-control"><option value="">All properties</option>@foreach($properties as $property)<option value="{{ $property->id }}" @selected(request('property_id') == $property->id)>{{ $property->name }}</option>@endforeach</select></div><div class="col-md-2"><button class="btn btn-outline-primary">Filter</button></div></form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>ID</th>
                                    <th>Country</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($guests as $guest)
                                    <tr>
                                        <td>{{ $guest->first_name }} {{ $guest->last_name }}</td>
                                        <td>{{ $guest->email ?? '-' }}</td>
                                        <td>{{ $guest->phone ?? '-' }}</td>
                                        <td>{{ $guest->id_number ?? '-' }}</td>
                                        <td>{{ $guest->country ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No guest records found.</td>
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
