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
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Projects Organization</h6>
        <div class="text-right">
            <a class="btn btn-primary btn-sm" type="button" href="" id="copyButton">+ New Project</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Project Code</th>
                        <th>Project Name</th>
                        <th>Client Name</th>
                        <th>Periode Start</th>
                        <th>Periode End</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                    <tr>
                        <td>{{ $project->project_id }}</td>
                        <td>{{ $project->project_code}}</td>
                        <td>{{ $project->project_name}}</td>
                        <td>{{ $project->client_id}}</td>
                        <td>{{ $project->periode_start}}</td>
                        <td>{{ $project->periode_end}}</td>
                        <td><a class="btn btn-primary btn-sm" href="#">Action</a> </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
.action{
    width: 180px;
}
</style>
@endsection
