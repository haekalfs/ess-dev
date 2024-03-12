@extends('layouts.main')

@section('title', 'Department & Position - ESS')

@section('active-page-system_management')
active
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-file-signature"></i> Department & Position</h1>
        <p class="mb-4">Regulate Department & Position</a>.</p>
    </div>
    <a class="btn mb-0 btn-primary btn-sm" href="/manage/users"><i class="fas fa-backward"></i>&nbsp; Go Back</a>
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

@if ($message = Session::get('notify'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{!! $message !!}</strong>
</div>
@endif
<div class="card shadow mb-4 zoom90">
    <div class="card-header d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Manage Department</h6>
        <div class="text-right">
            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#addDepartmentModal" id="manButton" style="margin-right: 10px;">+ Add Department</button>
            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#addPositionModal" id="manButton">+ Add Position</button>
        </div>
    </div>
    <ul class="nav nav-tabs" id="pageTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="page1-tab" data-toggle="tab" href="#page1" role="tab" aria-controls="page1" aria-selected="true"><i class="fas fa-file-signature"></i> Department</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="page2-tab" data-toggle="tab" href="#page2" role="tab" aria-controls="page2" aria-selected="false"><i class="fas fa-user-tie"></i> Position</a>
        </li>
    </ul>
    <div class="card-body">
        <div class="tab-content" id="pageTabContent">
            <div class="tab-pane fade show active" id="page1" role="tabpanel" aria-labelledby="page1-tab">
                <div class="table-responsive">
                    <table class="table table-bordered zoom90" width="100%" id="dataTable" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Department Name</th>
                                <th>PIC for Approvals</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($department_List as $d_List)
                                <tr>
                                    <td style="width: 5%;">{{ $d_List->id }}</td>
                                    <td class="text-primary font-weight-bold">{{ $d_List->department_name }}</td>
                                    <td>
                                        @foreach($d_List->approvers as $ap)
                                            <ul>
                                                <li>{{ $ap->user->name }}</li>
                                            </ul>
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                       <a href="/manage/delete_department/{{ $d_List->id }}" onclick='isconfirm();'class="btn btn-danger" ><i class='fas fa-fw fa-trash-alt'></i> Delete</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="page2" role="tabpanel" aria-labelledby="page2-tab">
                <div class="table-responsive">
                    <table class="table table-bordered zoom90" width="100%" id="dataTableProject" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Position Name</th>
                                <th>Position Priority</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($position_List as $p_List)
                                <tr>
                                    <td style="width: 5%;">{{ $p_List->id }}</td>
                                    <td class="font-weight-bold text-secondary">{{ $p_List->position_name }}</td>
                                    <td>
                                        @if($p_List->position_level == 1) <span class="font-weight-bold text-danger">High!</span> @else <span class="font-weight-bold text-primary">Standard</span> @endif
                                    </td>
                                    <td class="text-center" style="width: 15%;">
                                       <a href="/manage/delete_position/{{ $p_List->id }}" onclick='isconfirm();'class="btn btn-danger btn-sm" ><i class='fas fa-fw fa-trash-alt'></i> Delete</a>
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
<div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Create New Department</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="/manage/add_department" method="POST">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Department Name :</label>
                                    <input type="text" class="form-control" id="input-new_department" name="new_department">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Approvers :</label>
                                    <select class="form-control js-example-basic-multiple" style="width: 100%;" name="array_users[]" multiple="multiple">
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}">{{ $user->name}}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger"><i>Person in Charge for Approvals in this Department.</i></small>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary" value="Submit">
                  </div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="addPositionModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Create New Position</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="/manage/add_position" method="POST">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Position Name :</label>
                                    <input type="text" class="form-control" id="input-new_Position" name="new_Position">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Position Priority :</label>
                                    <select class="form-control" name="priority" required>
                                        <option value="1">High&#x2757;</option>
                                        <option value="2">Standard</option>
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

$(document).ready(function() {
    $('.js-example-basic-multiple').select2({
        width: 'resolve',
        dropdownAutoWidth: 'false' // need to override the changed default
    });
});
</script>

</script>
@endsection
