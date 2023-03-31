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

<style>
.action{
    width: 180px;
}
</style>
@endsection
