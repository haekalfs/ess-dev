@extends('layouts.main')

@section('title', 'UAC - ESS')

@section('active-page-system_management')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800">User Access Controller (UAC)</h1>
    <div>
        <a class="btn btn-danger btn-sm" href="/management/security_&_roles/manage/access" style="margin-right: 10px;"><i class="fas fa-users-slash fa-sm text-white-50"></i> Manage Access</a>
        <a class="btn btn-danger btn-sm" href="/management/security_&_roles/manage/roles"><i class="fas fa-users-cog fa-sm text-white-50"></i> Manage Roles</a>
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
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Users Roles</h6>
        <div class="text-right">
            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#assignRoleModal" id="manButton">+ Assign Role</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" width="100%" id="dataTableRoles" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>User ID</th>
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
                                <a class="btn btn-danger btn-sm" onclick='isconfirm();' href="/management/security_&_roles/remove/roles/{{ $user['us_Dat'] }}"><i class='fas fa-fw fa-undo-alt'></i> Reset</a>
                                {{-- <a href="/manage/roles/assign_delete/{{ $user['roles'] }}" onclick='isconfirm();'class="btn btn-danger btn-sm" ><i class='fas fa-fw fa-trash-alt'></i></a> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="assignRoleModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Assign Role to User</h5>
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
                                            <option value="{{ $rn->id }}">{{ $rn ->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Save">
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
</script>
@endsection