<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Add Guest</title>
    @include('admin.css')
</head>
<body>
<div class="main-wrapper">
    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header mb-4">
                <h3 class="page-title mb-1">Add guest</h3>
                <p class="text-muted mb-0">Create a new guest profile with identification and contact details.</p>
            </div>

            <div class="card shadow-sm border-0 rounded-20">
                <div class="card-body">
                    <form action="{{ route('guests.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last name</label>
                                <input type="text" name="last_name" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ID / Passport number</label>
                                <input type="text" name="id_number" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" class="form-control">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" rows="4" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">Save guest</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.script')
</body>
</html>
