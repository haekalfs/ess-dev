@extends('layouts.main')

@section('active-page-approval')
active
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Approval Timesheet</h1>
<p class="mb-4">Approval Page.</p>
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
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Time Report Employees</h6>
        <div class="text-right">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Employee ID</th>
                        <th>Request Date</th>
                        <th>Timesheet Periode</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approval as $approvals)
                    <tr>
                        <td>{{ $approvals->user_timesheet }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="action">
                            <a href="" class="btn btn-primary btn-sm">
                                <i class="fas fa-fw fa-edit fa-sm text-white-50"></i> Edit
                            </a>
                            <a href="" class="btn btn-primary btn-sm" style="margin-left: 3%;">Preview</a>
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
    width: 140px;
}
</style>
@endsection
