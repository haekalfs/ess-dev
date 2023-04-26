@extends('layouts.main')

@section('active-page-system_management')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800">Module Security</h1>
    <div>
        {{-- <a class="btn btn-primary btn-sm" href="/management/security_&_roles/manage/access" style="margin-right: 10px;">Manage Access</a> --}}
        <a class="btn btn-danger btn-sm" href="{{ url()->previous() }}"><i class="fas fa-redo-alt fa-sm text-white-50"></i> Back</a>
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
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Users Access</h6>
        <div class="text-right">
            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#grantAccessModal" id="manButton">+ Grant Access To</button>
        </div>
    </div>
    <div class="card-body">
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
                                {{-- <a href="/manage/roles/assign_delete/{{ $user['roles'] }}" onclick='isconfirm();'class="btn btn-danger btn-sm" ><i class='fas fa-fw fa-trash-alt'></i></a> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="grantAccessModal" tabindex="-1" role="dialog" aria-labelledby="grantAccessModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="grantAccessModal">Grant Access to Roles</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="/management/security_roles/add/access/" method="post">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Select Users :</label>
                                    <select class="custom-select" id="inputPage" name="inputPage">
                                        <option selected disabled>Choose...</option>
                                        @foreach ($pages as $page)
                                            <option value="{{ $page->id }}">{{ $page->description }}</option>
                                        @endforeach
                                    </select>
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