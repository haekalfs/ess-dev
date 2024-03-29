@extends('layouts.main')

@section('title', 'Company Project (View Only) - ESS')

@section('active-page-project')
active
@endsection

@section('content')

<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800"><i class="fas fa-network-wired"></i> Project Organization #{{ $project_id }}</h1>
    <div>
        <a href="#" onclick="deleteProject(event, {{$project_id}})" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm">
            <i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete Project
        </a>
    </div>
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
<div class="row zoom90">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Project Information</h6>
                <div class="text-right">
                    <a data-toggle="modal" data-target="#editModal" data-project-id="{{ $project_id }}" class="btn btn-primary btn-sm btn-edit"><i class="fas fa-fw fa-edit"></i> Edit</a>
                </div>
            </div>
                <!-- Card Body -->
            <div class="card-body">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Project Name</td>
                                        <td>: {{ $project->project_name }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Client</td>
                                        <td>: {{ $project->client->client_name }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Locations</td>
                                        <td>: {{ $project->alias }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr class="table-sm">
                                        <td style="width: 200px;">Project Code</td>
                                        <td>: {{ $project->project_code }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Periode Start</td>
                                        <td>: {{ $project->periode_start }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Periode End</td>
                                        <td>: {{ $project->periode_end }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">All Project Member</h6>
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $row_number = 1; 
                            @endphp
                            @foreach ($project_member as $usr)
                                <tr>
                                    <td>{{ $row_number++ }}</td>
                                    <td>{{ $usr->user->name }}</td>
                                    <td>{{ $usr->project_role->role_name }}</td>
                                    <td>{{ $usr->responsibility }}</td>
                                    <td>{{ $usr->periode_start }}</td>
                                    <td>{{ $usr->periode_end }}</td>
                                    <td class="text-center">
                                        <a data-toggle="modal" data-target="#editPeriodModal" data-usr-id="{{ $usr->id }}" class="btn btn-primary btn-sm btn-usr-edit"><i class="fas fa-fw fa-edit"></i> Edit</a>
                                        <a href="/project_list/delete/assignment/member/{{ $usr->id }}/{{ $usr->project_assignment_id }}" onclick='isconfirm();'class="btn btn-danger btn-sm" ><i class='fas fa-fw fa-trash-alt'></i> Remove</a>
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
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Edit Project Information</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="editForm">
                @csrf
                <input type="hidden" name="project_id" id="project_id" value="">
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="p_code">Project Code :</label>
                                            <input type="text" class="form-control" name="p_code" id="p_code" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="p_name">Project Name :</label>
                                            <input type="text" class="form-control" name="p_name" id="p_name" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="p_client">Client :</label>
                                    <select class="form-control" id="p_client" name="p_client" required>
                                        <option disabled selected>Select to update...</option>
                                        @foreach($clients as $key => $client)
                                            <option value="{{$client->id}}">{{ $client->client_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Location :</label>
                                    <select class="form-control js-example-basic-multiple" style="width: 100%;" name="p_location[]" multiple="multiple">
                                        @foreach($locations as $location)
                                            <option value="{{$location->id}}">{{ $location->description}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Address :</label>
                                    <textarea type="text" class="form-control" name="address" id="address" required autocomplete="off"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="from">From :</label>
                                            <input type="date" class="form-control" name="from" id="from" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="to">To :</label>
                                            <input type="date" class="form-control" name="to" id="to" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitEdit">Submit Request</button>
                  </div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="editPeriodModal" tabindex="-1" role="dialog" aria-labelledby="modalPeriod" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="editPeriodModalLabel">Edit User Periode</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="editUserPeriodeForm">
                @csrf
                <input type="hidden" name="usr_id" id="usr_id" value="">
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="from">From :</label>
                                            <input type="date" class="form-control" name="fromPeriode" id="fromPeriode" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="to">To :</label>
                                            <input type="date" class="form-control" name="toPeriode" id="toPeriode" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="editUserPeriodeSubmit">Submit Request</button>
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

@section('javascript')
<script>
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2({
            width: 'resolve',
            dropdownAutoWidth: 'false' // need to override the changed default
        });
    });
</script>
<script src="{{ asset('js/project.js') }}"></script>
@endsection