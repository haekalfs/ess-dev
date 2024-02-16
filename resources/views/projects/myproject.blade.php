@extends('layouts.main')

@section('title', 'MyProjects - ESS')

@section('active-page-project')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-network-wired"></i> MyProjects</h1>
    <a data-toggle="modal" data-target="#addMem" class="d-none d-sm-inline-block btn btn-sm @role('freelancer') btn-success @else btn-primary @endrole shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Request Assignment</a>
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
<div class="card shadow mb-4 zoom90">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">MyProjects List</h6>
        {{-- <div class="text-right">
            <button class="btn @role('freelancer') btn-success @else btn-primary @endrole btn-sm" type="button" id="manButton" style="margin-right: 10px;">+ Request Assignment</button>
        </div> --}}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="myProjects" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Assignment No.</th>
                        <th>Project Name</th>
                        <th>Project Start</th>
                        <th>Project End</th>
                        <th>Status</th>
                        <th width='120px'>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ $record->assignment_no }}</td>
                        <td>{{ $record->project_name }}
                        @if (now()->diffInDays(Carbon\Carbon::parse($record->created_at)) < 3)
                            <span class="text-danger"><small><i> &#x2728;</i></small></span>
                        @endif
                        </td>
                        <td>{{ $record->periode_start }}</td>
                        <td>{{ $record->periode_end }}</td>
                        <td class="text-center">
                            @if($record->periode_end <= date('Y-m-d'))
                            <i class="fas fa-times-circle" style="color: #ff0000;"></i>
                            @else
                            <i class="fas fa-check-circle" style="color: #0050db;"></i>
                            @endif
                        </td>
                        <td class="text-center"><a class="btn @role('freelancer') btn-success @else btn-primary @endrole btn-sm" href="/assignment/view/details/{{$record->project_assignment_id}}"><i class='fas fa-fw fa-eye'></i> View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow mb-4 zoom90">
    <!-- Card Header - Accordion -->
    <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
        role="button" aria-expanded="false" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Requested Assignment</h6>
    </a>
    <!-- Card Content - Collapse -->
    <div class="collapse" id="collapseCardExample">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered zoom90" id="dataTableRoles" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Request Date</th>
                            <th>Project</th>
                            <th>Periode Start</th>
                            <th>Periode End</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($myRequest as $record)
                        <tr>
                            <td>{{ $record->req_date }}</td>
                            <td>{{ $record->company_project->project_name }}</td>
                            <td>{{ $record->periode_start }}</td>
                            <td>{{ $record->periode_end }}</td>
                            <td>
                                @if($record->status == 0)
                                <a style='font-size: small;'><i class='fas fa-fw fa-spinner'></i></a>
                                @else
                                <a style='font-size: small;'><i class='fas fa-fw fa-check'></i></a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addMem" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Request Assignment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="/assignment/request" method="post">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="email">Employee Name :</label>
                                            <input class="form-control" name="emp_name" readonly hidden value="{{ Auth::user()->id }}">
                                            <input class="form-control" name="emp_name_placeholder" readonly required value="{{ Auth::user()->name }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="password">Project :</label>
                                            <select class="form-control" name="project" required>
                                                    @foreach($project as $company_project)
                                                    <option value="{{$company_project->id}}">{{ $company_project->project_name}}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Role :</label>
                                            <select class="form-control" name="emp_role" required>
                                                @foreach($usr_roles as $roles)
                                                <option value="{{$roles->role_code}}">{{ $roles->role_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Responsibility :</label>
                                            <input type="text" class="form-control" name="emp_resp">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">From :</label>
                                    <input type="date" class="form-control" name="fromTime">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">To :</label>
                                    <input type="date" class="form-control" name="toTime">
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
