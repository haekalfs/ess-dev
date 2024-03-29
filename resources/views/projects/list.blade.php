@extends('layouts.main')

@section('title', 'Project Organization - ESS')

@section('active-page-project')
active
@endsection

@section('content')
{{-- <div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800"><i class="fas fa-network-wired"></i> Project Organization</h1>
    <div>
        <a class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target=".listProjectRoles" id="pRoles" style="margin-right: 10px;"><i class="fas fa-users-cog fa-sm text-white-50"></i> Project Roles</a>
            <a class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target=".listLocations" id="listLoc" style="margin-right: 10px;"><i class="fas fa-map-marker-alt fa-sm text-white-50"></i> Locations</a>
            <a class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target=".bd-example-modal-lg" id="new"><i class="fas fa-building fa-sm text-white-50"></i> Clients</a>
    </div>
</div> --}}
<!-- Page Heading -->
<h1 class="h3 mb-2 zoom90 text-gray-800 font-weight-bold"><i class="fas fa-network-wired"></i> Project Organization</h1>
<p class="mb-3">This section displays the projects from various clients.</p>


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
<div class="card shadow mb-4 zoom90">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Projects Organization</h6>
        <div class="text-right">
            <a data-toggle="modal" data-target="#addModal" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> New Project</a>
        </div>
    </div>
    <ul class="nav nav-tabs" id="pageTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="page1-tab" data-toggle="tab" href="#page1" role="tab" aria-controls="page1" aria-selected="true"><i class="fas fa-list"></i> Project List</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="page2-tab" data-toggle="tab" href="#page2" role="tab" aria-controls="page2" aria-selected="false"><i class="fas fa-user-tie"></i> Clients</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="page3-tab" data-toggle="tab" href="#page3" role="tab" aria-controls="page3" aria-selected="false"><i class="fas fa-map-marker-alt"></i> Locations</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="page4-tab" data-toggle="tab" href="#page4" role="tab" aria-controls="page3" aria-selected="false"><i class="fas fa-user-tag"></i> Project Roles</a>
        </li>
    </ul>
    <div class="card-body">
        <div class="tab-content" id="pageTabContent">
            <div class="tab-pane fade show active" id="page1" role="tabpanel" aria-labelledby="page1-tab">
                <div class="table-responsive">
                    <table class="table table-bordered zoom90" id="dataTableProject" width="100%" cellspacing="0">
                        <thead class="thead-light">
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
                                <td><span class="long-text">{{ $project->project_name}}</span></td>
                                <td>{{ $project->client->client_name}}</td>
                                <td>{{ $project->periode_start}}</td>
                                <td>{{ $project->periode_end}}</td>
                                <td class="text-center"><a class="btn btn-primary btn-sm" href="/project_list/view/details/{{$project->id}}"><i class='fas fa-fw fa-eye'></i> View</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="page2" role="tabpanel" aria-labelledby="page2-tab">
                <form id="new-client-form" method="post">
                    @csrf
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="email">Client Name :</label>
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
                                <div class="alert alert-danger alert-success-delete" role="alert" style="display: none;">
                                    Client has been deleted successfully.
                                </div>
                                <div class="alert alert-danger alert-danger-delete" role="alert" style="display: none;">
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
                </form>
            </div>
            <div class="tab-pane fade" id="page3" role="tabpanel" aria-labelledby="page3-tab">
                <form id="new-location-form" method="post">
                    @csrf
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="email">Location Code :</label>
                                    <input type="text" class="form-control" name="loc_code">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="password">Description :</label>
                                    <input type="text" class="form-control" name="loc_desc">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="password">Fare :</label>
                                    <input type="text" class="form-control" name="loc_fare">
                                </div>
                            </div>
                            <div class="col-md-1 d-flex justify-content-center align-items-end">
                                <div class="form-group">
                                    <button type="button" id="save-location-entry" class="btn btn-primary">Insert</button>
                                </div>
                            </div>
                            <div class="col-md-12"><br>
                                <div class="alert alert-success alert-success-saving" role="alert" style="display: none;">
                                    Your entry has been saved successfully.
                                </div>
                                <div class="alert alert-danger" role="alert" style="display: none;">
                                    An error occurred while saving your entry. Please try again.
                                </div>
                                <div class="alert alert-danger alert-success-delete" role="alert" style="display: none;">
                                    Location has been deleted successfully.
                                </div>
                                <div class="alert alert-danger alert-danger-delete" role="alert" style="display: none;">
                                    An error occurred while fetching your entries. Please try again.
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered zoom90" width="100%"
                                        cellspacing="0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Locations Code</th>
                                                <th>Description</th>
                                                <th>Fare</th>
                                                <th width="150px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="Locations">
                                            <!-- Ajax Data -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="page4" role="tabpanel" aria-labelledby="page4-tab">
                <form id="new-project-roles-form" method="post">
                    @csrf
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="email">Role Code :</label>
                                            <input type="text" class="form-control" name="role_code">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="password">Description :</label>
                                            <input type="text" class="form-control" name="role_desc">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="password">Fare :</label>
                                            <input type="text" class="form-control" name="role_fare">
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex justify-content-center align-items-end">
                                        <div class="form-group">
                                            <button type="button" id="save-project-roles-entry" class="btn btn-primary">Insert</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12"><br>
                                <div class="alert alert-success alert-success-saving" role="alert" style="display: none;">
                                    Your entry has been saved successfully.
                                </div>
                                <div class="alert alert-danger" role="alert" style="display: none;">
                                    An error occurred while saving your entry. Please try again.
                                </div>
                                <div class="alert alert-danger alert-success-delete" role="alert" style="display: none;">
                                    Your entry has been delete successfully.
                                </div>
                                <div class="alert alert-danger alert-danger-delete" role="alert" style="display: none;">
                                    An error occurred while saving your entry. Please try again.
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%"
                                        cellspacing="0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Role Code</th>
                                                <th>Description</th>
                                                <th>Fare</th>
                                                <th width="150px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="projectRoles">
                                            <!-- Ajax Data -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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
                                        <option value="{{$client->id}}">{{ $client->client_name}}</option>
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

<style>
.action{
    width: 200px;
}
</style>

<script>
$(document).ready(function() {
    $('.js-example-basic-multiple').select2({
        width: 'resolve',
        dropdownAutoWidth: 'false' // need to override the changed default
    });
fetchClients();
fetchLocations();
fetchProjectRoles();
});
</script>
@endsection

@section('javascript')
<script src="{{ asset('js/project.js') }}"></script>
@endsection
