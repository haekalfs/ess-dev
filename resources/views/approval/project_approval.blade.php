@extends('layouts.main')

@section('title', 'Project Approval - ESS')

@section('active-page-approval')
active
@endsection

@section('content')
<h1 class="h3 mb-2 zoom90 font-weight-bold text-gray-800"><i class="fas fa-network-wired"></i> Project Assignments Approval</h1>
<p class="zoom90 mb-4">Approval Page.</p>
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
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Approval History</h6>
        <div class="text-right">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="dataTable1" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>No. Assignment</th>
                        <th>Request Date</th>
                        <th>Requested By</th>
                        <th>Project</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($queue as $approval)
                    <tr>
                        <td>{{ $approval->assignment_no }}</td>
                        <td>{{ $approval->req_date }}</td>
                        <td>{{ $approval->req_by }}</td>
                        <td>{{ $approval->company_project->project_name }}</td>
                        <td class="action text-center">
                            <a href="/approval/project/assignment/preview/{{ $approval->id }}" class="btn btn-secondary btn-sm" style="margin-right: 3%;">Preview</a>
                            <a href="/approval/project/assignment/reject/{{ $approval->id }}" class="btn btn-danger btn-sm">Reject</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
.action{
    width: 250px;
}
</style>
@endsection
