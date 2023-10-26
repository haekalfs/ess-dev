@extends('layouts.main')

@section('title', 'Requested Assignment - ESS')

@section('active-page-project')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800"><i class="fas fa-network-wired"></i> Requested Assignment #{{ $request->id }}</h1>
    <a data-toggle="modal" data-target="#addAss" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Create This Assignment</a>
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
<div class="row zoom90">
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse show" id="collapseCardExample">
                <div class="card-body">
                    <div class="col-md-12">
                        <table class="table table-borderless">
                            <tbody>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Emp. ID</td>
                                    <td>: {{ $request->user->users_detail->employee_id }}</td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Name.</td>
                                    <td>: {{ $request->user->name }}</td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Service Year</td>
                                    <td>: <?php 
                                        $hired_date = $request->user->users_detail->hired_date; // assuming $hired_date is in Y-m-d format
                                        $current_date = date('Y-m-d'); // get the current date
    
                                        // create DateTime objects from the hired_date and current_date values
                                        $hired_date_obj = new DateTime($hired_date);
                                        $current_date_obj = new DateTime($current_date);
    
                                        // calculate the difference between the hired_date and current_date
                                        $diff = $current_date_obj->diff($hired_date_obj);
    
                                        // get the total number of years from the difference object
                                        $total_years_of_service = $diff->y;
    
                                        // output the total years of service
                                        echo $total_years_of_service.' Years';
                                        ?>
                                    </td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Position</td>
                                    <td>: {{ $request->user->users_detail->position->position_name }}</td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 200px;">Emp. Status</td>
                                    <td>: {{ $request->user->users_detail->employee_status }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardProject" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="collapseCardProject">
                <h6 class="m-0 font-weight-bold text-primary">Assignment Request Information</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse show" id="collapseCardProject">
                <div class="card-body">
                    <div class="col-md-12">
                        <table class="table table-borderless">
                            <tbody>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Request Date</td>
                                    <td>: {{ $request->req_date }}</td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Requested Role</td>
                                    <td>: {{ $request->role }}</td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Responsibility</td>
                                    <td>: {{ $request->responsibility }}</td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Project</td>
                                    <td>: {{ $request->company_project->project_name }}</td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Client</td>
                                    <td>: {{ $request->company_project->client->client_name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addAss" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Convert to Assignment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="/assignment/add_entries/based_on/request/{{ $request->id }}" method="post">
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
@endsection

@section('javascript')
<script src="{{ asset('js/project.js') }}"></script>
@endsection