@extends('layouts.main')

@section('title', 'Company Project (View Only) - ESS')

@section('active-page-project')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800">Project Organization #{{ $project_id }}</h1>
    <a href="#" onclick="deleteProject(event, {{$project_id}})" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm">
        <i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete Project
    </a>
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
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Project Information</h6>
            </div>
                <!-- Card Body -->
            <div class="card-body">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Project Name</td>
                                        <td>: {{ $project->project_name }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Client</td>
                                        <td>: {{ $project->client->client_name }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Address</td>
                                        <td>: {{ $project->address }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr class="table-sm">
                                        <td style="width: 200px;">Project Code</td>
                                        <td>: {{ $project->project_code }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Periode Start</td>
                                        <td>: {{ $project->periode_start }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Periode End</td>
                                        <td>: {{ $project->periode_end }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">All Project Member</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="col-md-12">
                    <table class="table table-bordered zoom90" width="100%" id="dataTable" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Responsibility</th>
                                <th>Periode Start</th>
                                <th>Periode End</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $row_number = 1;
                            @endphp
                            @foreach ($project_member as $usr)
                                <tr>
                                    <td>{{ $row_number++ }}</td>
                                    <td>{{ $usr->user_id }}</td>
                                    <td>{{ $usr->role }}</td>
                                    <td>{{ $usr->responsibility }}</td>
                                    <td>{{ $usr->periode_start }}</td>
                                    <td>{{ $usr->periode_end }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.action{
    width: 180px;
}
</style>
@endsection