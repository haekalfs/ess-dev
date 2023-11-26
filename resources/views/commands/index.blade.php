@extends('layouts.main')

@section('title', 'Commands Execution - ESS')

@section('active-page-HR')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-list"></i> Commands</h1>
    <a data-toggle="modal" data-target="#addMem" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm"><i class="fas fa-list"></i> Execute</a>
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
    <div class="card-header py-3 d-flex flex-row align-items-center bg-primary justify-content-between">
        <h6 class="m-0 font-weight-bold text-light" id="judul">Commands List</h6>
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
                        <th>Commands Name</th>
                        <th>Commands Description</th>
                        <th>Url</th>
                        <th width='120px'>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $d)
                    <tr>
                        <td class="text-center" style="border-bottom: none; border-top: none;">
                            <div class="form-check form-check-inline larger-checkbox">
                                <input class="form-check-input data-checkbox" type="checkbox" value="option1">
                            </div>
                        </td>
                        <td>{{ $d->commands_name }}</td>
                        <td>{{ $d->command_desc }}</td>
                        <td>{{ $d->url }}</td>
                        <td class="text-center"><a class="btn btn-sm btn-danger">View Details</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('javascript')
@endsection
