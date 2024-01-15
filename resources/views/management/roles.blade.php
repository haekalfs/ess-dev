@extends('layouts.main')

@section('title', 'UAC - ESS')

@section('active-page-system_management')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800"><i class="fas fa-user-tag"></i> User Access Controller (UAC)</h1>
    {{-- <div>
        <a class="btn btn-danger btn-sm" href="/management/security_&_roles/manage/access" style="margin-right: 10px;"><i class="fas fa-users-slash fa-sm text-white-50"></i> Manage Access</a>
        <a class="btn btn-danger btn-sm" href="/management/security_&_roles/manage/roles"><i class="fas fa-users-cog fa-sm text-white-50"></i> Manage Roles</a>
    </div> --}}
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
<div class="card shadow mb-4 zoom90">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Roles & Access Configurations</h6>
        {{-- <div class="text-right">
            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#assignRoleModal" id="manButton">+ Assign Role</button>
        </div> --}}
    </div>
    <ul class="nav nav-tabs" id="pageTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active text-primary" id="page1-tab" data-toggle="tab" href="#page1" role="tab" aria-controls="page1" aria-selected="true"><i class="fas fa-file-invoice"></i> User's Roles</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-success" id="page2-tab" data-toggle="tab" href="#page2" role="tab" aria-controls="page2" aria-selected="false"><i class="fas fa-file-invoice-dollar"></i> Manage Roles</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" id="page3-tab" data-toggle="tab" href="#page3" role="tab" aria-controls="page3" aria-selected="false"><i class="fas fa-file-invoice-dollar"></i> Manage Access</a>
        </li>
    </ul>
    <div class="card-body">
        <div class="tab-content" id="pageTabContent">
            <div class="tab-pane fade show active" id="page1" role="tabpanel" aria-labelledby="page1-tab">
                <form method="post" action="/manage/roles/assign_roles">
                    @csrf
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="email">User Name :</label>
                                    <select class="custom-select" id="inputUser" name="inputUser">
                                        <option selected disabled>Choose...</option>
                                        @foreach ($us_List as $userlist)
                                            <option value="{{ $userlist->id}}">{{ $userlist->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Roles :</label>
                                    <select class="custom-select" id="inputRole" name="inputRole">
                                        <option selected disabled>Choose...</option>
                                        @foreach($r_name as $rn)
                                            <option value="{{ $rn->id }}">{{ $rn ->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1 d-flex justify-content-center align-items-end">
                                <div class="form-group">
                                    <button type="submit" id="insert-data-fingerprint" class="btn btn-primary">Insert</button>
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
                                    <table class="table table-bordered zoom90" width="100%" id="dataTableRoles" cellspacing="0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No.</th>
                                                <th>User ID</th>
                                                <th>Role Name</th>
                                                <th width="80px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = 1; @endphp
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $user['name'] }}</td>
                                                    <td>{{ $user['roles'] }}</td>
                                                    <td>
                                                        <a class="btn btn-danger btn-sm" onclick='isconfirm();' href="/management/security_&_roles/remove/roles/{{ $user['us_Dat'] }}"><i class='fas fa-fw fa-undo-alt'></i> Reset</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="page2" role="tabpanel" aria-labelledby="page2-tab">
                <form method="post" action="/manage/roles/add_roles">
                    @csrf
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="password">Role Name :</label>
                                    <input type="text" class="form-control" id="input-new_role" name="new_role">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Role Code :</label>
                                    <input type="text" class="form-control" id="input-new_role" name="new_role_code">
                                </div>
                            </div>
                            <div class="col-md-1 d-flex justify-content-center align-items-end">
                                <div class="form-group">
                                    <button type="submit" id="insert-data-fingerprint" class="btn btn-primary">Insert</button>
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
                                    <table class="table table-bordered zoom90" width="100%" id="dataTable" cellspacing="0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Role Code</th>
                                                <th>Description</th>
                                                <th width="80px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $no = 1; @endphp
                                            @foreach ($r_name as $rn)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $rn['role'] }}</td>
                                                    <td>{{ $rn['description'] }}</td>
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
                </form>
            </div>
            <div class="tab-pane fade" id="page3" role="tabpanel" aria-labelledby="page3-tab">
                <form method="post" action="/management/security_&_roles/add/access">
                    @csrf
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="email">Page :</label>
                                    <select class="custom-select" id="inputPage" name="inputPage">
                                        <option selected disabled>Choose...</option>
                                        @foreach ($pages as $page)
                                            <option value="{{ $page->id }}">{{ $page->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Roles :</label>
                                    <select class="custom-select" id="inputRole" name="inputRole">
                                        <option selected disabled>Choose...</option>
                                        @foreach($r_name as $rn)
                                            <option value="{{ $rn->id }}">{{ $rn ->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1 d-flex justify-content-center align-items-end">
                                <div class="form-group">
                                    <button type="submit" id="insert-data-fingerprint" class="btn btn-primary">Insert</button>
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
                                    <table class="table table-bordered zoom90" width="100%" id="dataTableUsersAcc" cellspacing="0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Page</th>
                                                <th>Grant Access</th>
                                                <th width="80px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($access as $userAc)
                                                <tr>
                                                    <td>{{ $userAc['id'] }}</td>
                                                    <td>{{ $userAc['page'] }}</td>
                                                    <td>{{ $userAc['grantTo'] }}</td>
                                                    <td>
                                                        <a class="btn btn-danger btn-sm" onclick='isconfirm();' href="/management/security_&_roles/remove/access/{{ $userAc['page_id'] }}"><i class='fas fa-fw fa-undo-alt'></i> Reset</a>
                                                    </td>
                                                </tr>
                                            @endforeach
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
