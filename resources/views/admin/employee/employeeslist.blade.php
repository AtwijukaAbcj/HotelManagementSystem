<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Employees</title>
    @include('admin.css')

</head>

<body>

    <div class="main-wrapper">

        @include('admin.header')
        @include('admin.sidebar')


        <div class="page-wrapper">
            <div class="content container-fluid">

                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="d-flex align-items-center justify-content-between flex-wrap mt-3">
                                
                            @if(session()->has('message'))
                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                        {{session()->get('message')}}
                                    </div>
                                    @endif

                                <div>
                                    <h4 class="card-title mb-1">Employees</h4>
                                    <p class="text-muted mb-0">Manage staff, roles, and access permissions.</p>
                                </div>
                                <a href="{{url('form/addemployee')}}" class="btn btn-primary veiwbutton"><i class="fas fa-user-plus mr-1"></i> Add employee</a>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="row">
                    <div class="col-lg-12">


                    <form action="" class="employee-filter employee-toolbar">
                            <div class="row formtype align-items-end">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="sr-only" for="employee-search">Search employees</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
                                            <input id="employee-search" type="search" name="search" placeholder="Search name or email" class="form-control" value="{{$search}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="sr-only" for="employee-role">Role</label>
                                        <select id="employee-role" name="role" class="form-control">
                                            <option value="">All roles</option>
                                            @foreach(['Manager', 'Receptionist', 'Staff', 'Accountant', 'Room Maintainer'] as $employeeRole)
                                                <option value="{{ $employeeRole }}" @selected($role === $employeeRole)>{{ $employeeRole }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group"><button class="btn btn-primary btn-block mt-0 search_button"><i class="fas fa-filter mr-1"></i> Filter</button></div>
                                </div>
                            </div>
                        </form>

                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="datatable table table-hover align-middle employee-table">
                                        <thead>
                                            <tr>
                                                <th data-orderable="false" class="select-column"><input type="checkbox" id="employee-select-all" aria-label="Select all employees"></th>
                                                <th>Employee ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Address</th>
                                                <th>Join Date</th>
                                                <th>Role</th>
                                                <th data-orderable="false" class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($employees as $employee)
                                            <tr>
                                                <td class="select-column"><input type="checkbox" class="employee-select" aria-label="Select {{ $employee->name }}"></td>
                                                <td>EMP-{{ sprintf('%03d', $employee->id) }}</td>
                                                <td><div class="employee-identity"><span class="employee-avatar">{{ strtoupper(substr($employee->name, 0, 1)) }}</span><span>{{ $employee->name }}</span></div></td>
                                                <td>{{$employee->email}}</td>
                                                <td>{{$employee->phone}}</td>
                                                <td>{{$employee->address}}</td>
                                                <td>{{$employee->join_date}}</td>
                                                <td><span class="employee-role-pill">{{$employee->role}}</span></td>
                                                <td class="text-right">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false" aria-label="Actions for {{ $employee->name }}"><i class="fas fa-ellipsis-h"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right"> <a class="dropdown-item" href="{{url ('update_emp',$employee->id)}}"><i class="fas fa-pencil-alt m-r-5"></i> Edit</a> <a class="dropdown-item" href="{{url('delete_emp',$employee->id)}}" data-toggle="modal" data-target="#delete_asset_{{ $employee->id }}"><i class="fas fa-trash-alt m-r-5"></i> Delete</a> </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="text-center">No employees found.</td>
                                            </tr>
                                            @endforelse

                                        </tbody>
                                    </table>
                                    @foreach($employees as $employee)
                                    <div id="delete_asset_{{ $employee->id }}" class="modal fade delete-modal" role="dialog">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-body text-center">
                                                    <h3 class="delete_class">Are you sure you want to delete this employee?</h3>
                                                    <div class="m-t-20">
                                                        <a href="#" class="btn btn-white" data-dismiss="modal">Close</a>
                                                        <a class="btn btn-danger" href="{{ url('delete_emp', $employee->id) }}">Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>


    @include('admin.script')
    <script>
        $('#employee-select-all').on('change', function() {
            $('.employee-select').prop('checked', this.checked);
        });

        $(function() {
            $('#datetimepicker3').datetimepicker({
                format: 'LT'

            });
        });
    </script>
</body>

</html>