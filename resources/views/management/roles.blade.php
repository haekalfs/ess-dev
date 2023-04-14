@extends('layouts.main')

@section('active-page-system_management')
active
@endsection

@section('content')
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
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Manage Roles</h6>
        <div class="text-right">
            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#assignRoleModal" id="manButton" style="margin-right: 10px;">+ Assign Role</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" width="100%" id="dataTableRoles" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Role Name</th>
                        <th width="80px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user['name'] }}</td>
                            <td>{{ $user['roles'] }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm" type="button" onclick="fetchData({{ $user['mmk'] }})" id="manButton" style="margin-right: 10px;"><i class='fas fa-fw fa-trash-alt'></i></button>
                                {{-- <a href="/manage/roles/assign_delete/{{ $user['roles'] }}" onclick='isconfirm();'class="btn btn-danger btn-sm" ><i class='fas fa-fw fa-trash-alt'></i></a> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
        role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Roles</h6>
    </a>
    <!-- Card Content - Collapse -->
    <div class="collapse" id="collapseCardExample">
        <div class="card-body">
            <div class="text-right">
                <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#addRoleModal" id="addButton" style="margin-right: 10px;">+ New Role</button> 
            </div><br>
            <div class="table-responsive">
                <table class="table table-bordered zoom90" width="100%" id="dataTable" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Role Code</th>
                            <th>Role Name</th>
                            <th width="80px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($r_name as $rn)
                            <tr>
                                <td>{{ $rn['id'] }}</td>
                                <td>{{ $rn['role'] }}</td>
                                <td>{{ $rn['role_name'] }}</td>
                                <td>
                                  <a href="/manage/roles/delete/{{ $rn->id }}" onclick='isconfirm();'class="btn btn-danger btn-sm" ><i class='fas fa-fw fa-trash-alt'></i> Remove</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="assignRoleModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Assign Role</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="/manage/roles/assign_roles" method="post">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Select Users :</label>
                                    <select class="custom-select" id="inputUser" name="inputUser">
                                        <option selected disabled>Choose...</option>
                                        @foreach ($us_List as $userlist)
                                            <option value="{{ $userlist->id}}">{{ $userlist->name }}</option>
                                        @endforeach
                                    </select>
                                    {{-- <input list="encodings" value="" class="col-sm-12 custom-select custom-select-sm">
                                    <datalist id="encodings">
                                        @foreach ($usersList as $userlist)
                                            <option value="{{ $userlist->name }}">{{ $userlist->name }}</option>
                                        @endforeach
                                    </datalist> --}}
                                </div>
                            </div>
                        </div>
				    </div>
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Select Roles :</label>
                                    <select class="custom-select" id="inputRole" name="inputRole">
                                        <option selected disabled>Choose...</option>
                                        @foreach($r_name as $rn)
                                            <option value="{{ $rn ->role }}">{{ $rn ->role_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary btn-sm" value="Save">
                  </div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="DeleteAssignRoleModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Delete Assign Role</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="/manage/roles/assign_delete" method="post">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Name :</label>
                                    <input type="text" id="test1" name="name_user">
                                    {{-- <select class="custom-select" id="inputUser" name="inputUser">
                                        <option selected disabled>Choose...</option>
                                        @foreach ($us_List as $userlist)
                                            <option value="{{ $userlist->id}}">{{ $userlist->name }}</option>
                                        @endforeach
                                    </select> --}}
                                    {{-- <input list="encodings" value="" class="col-sm-12 custom-select custom-select-sm">
                                    <datalist id="encodings">
                                        @foreach ($usersList as $userlist)
                                            <option value="{{ $userlist->name }}">{{ $userlist->name }}</option>
                                        @endforeach
                                    </datalist> --}}
                                </div>
                            </div>
                        </div>
				    </div>
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Select Roles :</label>
                                    <select class="custom-select" id="inputRole" name="inputRole">
                                        <option selected disabled>Choose...</option>
                                        @foreach($r_name as $rn)
                                            <option value="{{ $rn ->role }}">{{ $rn ->role_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary btn-sm" value="Save">
                  </div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Create New Role</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="/manage/roles/add_roles" method="post">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Role Name :</label>
                                    <input type="text" class="form-control" id="input-new_role" name="new_role">
                                </div>
                                <div class="form-group">
                                    <label for="password">Role Code :</label>
                                    <input type="text" class="form-control" id="input-new_role" name="new_role_code">
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary btn-sm" value="Save">
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
    function isconfirm() {
        var conf = confirm("Are you sure you want to delete this ?");
        if (conf) {
            return true;
        } else {
            return false;
        }
    }

function fetchData(id) {
    $.ajax({
        url: "/test/" + id,
        method: "GET",
        success: function(data) {
            data = JSON.parse(data);
            $('#test1').val(data.name);
            $('#DeleteAssignRoleModal').modal('show');
        }
    });
}
</script>
@endsection