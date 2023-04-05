@extends('layouts.main')

@section('active-page-project')
active
@endsection

@section('content')
<!-- Page Heading -->

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
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Project Assignment</h6>
        <div class="text-right">
            <a class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#addModal" id="addButton">+ New Assignment</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Request Date</th>
                        <th>Assignment No.</th>
                        <th>Project Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignment as $assign)
                    <tr>
                        <td>{{ $assign->req_date }}</td>
                        <td>{{ $assign->assignment_no }}</td>
                        <td>{{ $assign->project_name }}</td>
                        <td>New Request</td>
                        <td>
                            <div class=''>
                                <a class='btn btn-danger btn-sm' type='button' id='dropdownMenu' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                    Action
                                </a>
                                <div class='dropdown-menu' aria-labelledby='dropdownMenu'>
                                    <a class='dropdown-item' href='' onclick='isconfirm();'><i class='fas fa-fw fa-edit'></i> Edit</a>
                                    <a class='dropdown-item' href='' onclick='isconfirm();'><i class='fas fa-fw fa-trash-alt'></i> Remove</a>
                                </div>
                            </div>
                        </td>
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
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Add New Assignment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="/assignment/add-entries" method="post">
                @csrf
				<div class="modal-body" style="">
                    <input type="hidden" id="update_clickedDate" name="update_clickedDate">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="text-primary" for="email">Date Prepared :</label>
                                        <?php $date_str = date('Y-m-d');
                                        $date = date('j F Y', strtotime($date_str));
                                        echo $date; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">No :</label>
                                            <input type="text" class="form-control" name="no_doc" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Reference Doc :</label>
                                            <input type="text" class="form-control" name="ref_doc" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Project :</label>
                                    <select class="form-control" id="update_location" name="project" required>
                                            @foreach($project as $company_project)
                                            <option value="{{$company_project->project_name}}">{{ $company_project->project_name}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Notes Assigment :</label>
                                    <textarea type="text" class="form-control" id="update_activity" name="notes" required></textarea>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" data-dismiss="modal">Save changes</button>
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
