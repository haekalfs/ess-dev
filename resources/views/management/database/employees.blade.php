@extends('layouts.main')

@section('title', 'Database Employees - ESS')

@section('active-page-system_management')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800">Employees Database</h1>
    <a data-toggle="modal" data-target="#addModal" class="d-none d-sm-inline-block btn btn-sm @role('freelancer') btn-success @else btn-primary @endrole shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Download Master Data</a>
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
        <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole" id="judul">Database</h6>
        <div class="text-right">
            <button class="btn @role('freelancer') btn-primary @else btn-success @endrole btn-sm" type="button" id="manButton"><i class="fas fa-download fa-sm text-white-50"></i> Export Selected</button>
        </div>
    </div>
    <form action="/manage/list/employees/" method="GET">
        @csrf
        <div class="card-body">
            <div class="col-md-12 zoom90">
                <div class="row d-flex justify-content-start">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="position_id">Filter by Position:</label>
                            <select name="position_id" class="form-control" id="position_id">
                                <option value="">All Positions</option>
                                <option value="4,18,19" {{ request('position_id') == '4,18,19' ? 'selected' : '' }}>Consultant</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex justify-content-center align-items-end">
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Filter"/>
                        </div>
                    </div>
                    <div class="col-md-12"><br>
                        <div class="table-responsive">
                            <table class="table table-bordered zoom90" id="myProjects" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center">
                                            <div class="form-check form-check-inline larger-checkbox">
                                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                                            </div>
                                        </th>
                                        <th>User ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                    <tr>
                                        <td class="text-center">
                                            <div class="form-check form-check-inline larger-checkbox">
                                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                                            </div>
                                        </td>
                                        <td>{{ $user->user_id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    @endforeach
                                 </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
{{-- <div class="card shadow mb-4">
    
    <div class="card-body">
    <!-- Form filter -->
    <form action="/manage/list/employees/" method="GET">
    @csrf
   <div class="col-md-12 zoom90">
        <div class="row">
            <div class="form-group">
                <label for="position_id">Filter by Position:</label>
                <select name="position_id" class="form-control" id="position_id">
                    <option value="">All Positions</option>
                    <option value="4,18,19" {{ request('position_id') == '4,18,19' ? 'selected' : '' }}>Consultant</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-download fa-sm text-white-50"></i> Filter</button>
        </div>
    </div>
    </form>
    </div>
</div> --}}
@endsection
