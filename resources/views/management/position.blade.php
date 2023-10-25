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
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-file-signature"></i> Department & Position</h1>
        <p class="mb-4">Regulate Department & Position</a>.</p>
    </div>
    <a class="btn mb-0 btn-primary btn-sm" href="/manage/users"> Go Back</a>
</div>
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
            <a class="nav-link active" id="page1-tab" data-toggle="tab" href="#page1" role="tab" aria-controls="page1" aria-selected="true"><i class="fas fa-file-invoice"></i> Department</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="page2-tab" data-toggle="tab" href="#page2" role="tab" aria-controls="page2" aria-selected="false"><i class="fas fa-file-invoice-dollar"></i> Position</a>
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
                                <th width="50px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($department_List as $d_List)
                                <tr>
                                    <td>{{ $d_List['id'] }}</td>
                                    <td>{{ $d_List['department_name'] }}</td>
                                    <td>
                                       <a href="/manage/delete_department/{{ $d_List->id }}" onclick='isconfirm();'class="btn btn-danger btn-sm" ><i class='fas fa-fw fa-trash-alt'></i></a>                                
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($position_List as $p_List)
                                <tr>
                                    <td>{{ $p_List['id'] }}</td>
                                    <td>{{ $p_List['position_name'] }}</td>
                                    <td width="50px">
                                       <a href="/manage/delete_position/{{ $p_List->id }}" onclick='isconfirm();'class="btn btn-danger btn-sm" ><i class='fas fa-fw fa-trash-alt'></i></a>                                
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
	<div class="modal-dialog modal-dialog-centered" role="document">
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


</script>
@endsection