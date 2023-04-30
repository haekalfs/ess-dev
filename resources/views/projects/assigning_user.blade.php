@extends('layouts.main')

@section('title', 'Project Assignment - ESS')

@section('active-page-project')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800">Project Assignment #{{ $assignment_id }}</h1>
    <a href="#" onclick="deleteAssignment(event, {{$assignment_id}})" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm">
        <i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete Assignment
    </a>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('failed'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
                    role="button" aria-expanded="true" aria-controls="collapseCardExample">
                    <h6 class="m-0 font-weight-bold text-primary">Assignment Information</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseCardExample">
                    <div class="card-body">
                        <div class="col-md-12">
                            <table class="table table-borderless">
                                <tbody>
                                    @foreach($assignment as $row)
                                    <tr class="table-sm">
                                        <td style="width: 150px;">No.</td>
                                        <td>: {{ $row->assignment_no }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Date</td>
                                        <td>: {{ $row->req_date }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Ref. Document</td>
                                        <td>: {{ $row->reference_doc }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 200px;">Notes Assignment</td>
                                        <td>: {{ $row->notes }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseCardProject" class="d-block card-header py-3" data-toggle="collapse"
                    role="button" aria-expanded="true" aria-controls="collapseCardProject">
                    <h6 class="m-0 font-weight-bold text-primary">Project Information</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseCardProject">
                    <div class="card-body">
                        <div class="col-md-12">
                            <table class="table table-borderless">
                                <tbody>
                                    @foreach($assignment as $row)
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Project Code</td>
                                        <td>: {{ $row->project_code }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Project</td>
                                        <td>: {{ $row->project_name }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Location</td>
                                        <td>: {{ $row->alias }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Client</td>
                                        <td>: {{ $project->client_name }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Project Member</h6>
                <div class="text-right">
                    <a class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#addMem" id="addMemModal">+ Add Member</a>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="col-md-12">
                    <table class="table table-bordered zoom90" width="100%" id="dataTable" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Responsibility</th>
                                <th>Periode Start</th>
                                <th>Periode End</th>
                                <th width="80px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $row_number = 1;
                            @endphp
                            @foreach ($project_member as $usr)
                                <tr>
                                    <td>{{ $row_number++ }}</td>
                                    <td>{{ $usr->user_id }}</td>
                                    <td>{{ $usr->role }}</td>
                                    <td>{{ $usr->responsibility }}</td>
                                    <td>{{ $usr->periode_start }}</td>
                                    <td>{{ $usr->periode_end }}</td>
                                    <td>
                                      <a href="/assignment/member/delete/{{ $usr->id }}" onclick='isconfirm();'class="btn btn-danger btn-sm" ><i class='fas fa-fw fa-trash-alt'></i> Remove</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addMem" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Add Member</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="/assignment/add_member_to_assignment/{{ $assignment_id }}" method="post">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="email">Employee Name :</label>
                                            <select class="form-control" id="update_location" name="emp_name" required>
                                                @foreach($user as $employees)
                                                <option value="{{$employees->id}}">{{ $employees->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Role :</label>
                                            <select class="form-control" id="update_location" name="emp_role" required>
                                                @foreach($usr_roles as $roles)
                                                <option value="{{$roles->role_code}}">{{ $roles->role_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Responsibility :</label>
                                            <input type="text" class="form-control" name="emp_resp" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">From :</label>
                                    <input type="date" class="form-control" name="fromTime" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">To :</label>
                                    <input type="date" class="form-control" name="toTime" required>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                  </div>
			</form>
		</div>
	</div>
</div>
<style>
.action{
    width: 180px;
}
</style>
@endsection
