@extends('layouts.main')

@section('active-page-project')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800">Project Organization</h1>
    <a data-toggle="modal" data-target="#addModal" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> New Project</a>
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
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Projects Organization</h6>
        <div class="text-right">
            <button class="btn btn-primary btn-sm" type="button" id="manButton" style="margin-right: 10px;"><i class="fas fa-users-cog fa-sm text-white-50"></i> Project Roles</button>
            <button class="btn btn-primary btn-sm" type="button" id="manButton" style="margin-right: 10px;"><i class="fas fa-map-marker-alt fa-sm text-white-50"></i> Locations</button>
            <a class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target=".bd-example-modal-lg" id="new"><i class="fas fa-building fa-sm text-white-50"></i> Clients</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="dataTableProject" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Project Code</th>
                        <th>Project Name</th>
                        <th>Client Name</th>
                        <th>Periode Start</th>
                        <th>Periode End</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                    <tr>
                        <td>{{ $project->id }}</td>
                        <td>{{ $project->project_code}}</td>
                        <td>{{ $project->project_name}}</td>
                        <td>{{ $project->client->client_name}}</td>
                        <td>{{ $project->periode_start}}</td>
                        <td>{{ $project->periode_end}}</td>
                        <td><a class="btn btn-primary btn-sm"><i class='fas fa-fw fa-eye'></i> View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Create New Project</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="/project_list/new" method="post">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Project Code :</label>
                                            <input type="text" class="form-control" name="p_code">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Project Name :</label>
                                            <input type="text" class="form-control" name="p_name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Client :</label>
                                    <select class="form-control" id="update_location" name="p_client" required>
                                        @foreach($clients as $client)
                                        <option value="{{$client->client_id}}">{{ $client->client_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Location :</label>
                                    <select class="form-control" id="update_location" name="p_location" required>
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
                                    <label for="password">Address :</label>
                                    <textarea type="text" class="form-control" name="address" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">From :</label>
                                            <input type="date" class="form-control" name="from">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">To :</label>
                                            <input type="date" class="form-control" name="to">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                  </div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Add New Client</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="new-client-form" method="post">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="email">Project Name :</label>
                                    <input type="text" class="form-control" name="client_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Address :</label>
                                    <input type="text" class="form-control" name="address">
                                </div>
                            </div>
                            <div class="col-md-1 d-flex justify-content-center align-items-end">
                                <div class="form-group">
                                    <button type="button" id="save-client-entry" class="btn btn-primary">Insert</button>
                                </div>
                            </div>
                            <div class="col-md-12"><br>
                                <div class="alert alert-success alert-success-saving" role="alert" style="display: none;">
                                    Your entry has been saved successfully.
                                </div>
                                <div class="alert alert-danger" role="alert" style="display: none;">
                                    An error occurred while saving your entry. Please try again.
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered zoom90" width="100%"
                                        cellspacing="0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Client Name</th>
                                                <th>Address</th>
                                                <th width="150px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="Clients">
                                            <!-- Ajax Data -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
<script>
fetchClients();
</script>
@endsection
