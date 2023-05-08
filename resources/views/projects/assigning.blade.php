@extends('layouts.main')

@section('title', 'Project Assignment - ESS')

@section('active-page-project')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800">Project Assignment</h1>
    <div>
        <select class="form-control" id="yearSelected" name="yearSelected" required onchange="redirectToPageAssignment()">
            @foreach (array_reverse($yearsBefore) as $year)
                <option value="{{ $year }}" @if ($year == $yearSelected) selected @endif>{{ $year }}</option>
            @endforeach
        </select>
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
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Project Assignment</h6>
        <div class="text-right">
            <a data-toggle="modal" data-target="#addModal" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> New Assignment</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="listAssignments" width="100%" cellspacing="0">
                <thead class="thead-light">
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
                        <td><span class="long-text">{{ $assign->project_name }}</span></td>
                        <td>@if($assign->approval_status == 40)
                            <span class="m-0 font-weight-bold text-danger">Waiting for Approval Service Diretor</span>
                            @elseif($assign->approval_status == 29) 
                            <span class="m-0 font-weight-bold text-primary">Approved by Service Director</span>
                            @else 
                            <span class="text-danger">Rejected</span>
                            @endif
                        </td>
                        <td class="text-center"><a class="btn btn-primary btn-sm" href="/assignment/member/{{ $assign->id }}"><i class='fas fa-fw fa-eye'></i> View</a></td>
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
			<form action="/assignment/add_entries" method="post">
                @csrf
				<div class="modal-body" style="">
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
                                            <input type="text" class="form-control" required name="no_doc">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Reference Doc :</label>
                                            <input type="text" class="form-control" name="ref_doc">
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
                                            <option value="{{$company_project->id}}">{{ $company_project->project_name}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Notes Assigment :</label>
                                    <textarea type="text" class="form-control" name="notes"></textarea>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
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
    function redirectToPageAssignment() {
        var selectedOption = document.getElementById("yearSelected").value;
        var url = "{{ url('/assignment') }}"; // Specify the base URL

        url += "/" + selectedOption;

        window.location.href = url; // Redirect to the desired page
    }
</script>
@endsection
