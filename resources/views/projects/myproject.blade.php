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
        <h6 class="m-0 font-weight-bold text-primary" id="judul">My Projects</h6>
        <div class="text-right">
            <button class="btn btn-primary btn-sm" type="button" id="manButton" style="margin-right: 10px;">+ Request Assignment</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Project Code</th>
                        <th>Project Name</th>
                        <th>Project Start</th>
                        <th>Project End</th>
                        <th width='80px'>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ $record->project_code }}</td>
                        <td>{{ $record->project_name }}</td>
                        <td>{{ $record->periode_start }}</td>
                        <td>{{ $record->periode_end }}</td>
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
@endsection
