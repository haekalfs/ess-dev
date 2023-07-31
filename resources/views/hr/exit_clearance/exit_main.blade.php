@extends('layouts.main')

@section('title', 'Exit Clearance - ESS')

@section('active-page-hr')
active
@endsection

@section('content')

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
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800">Exit Clearance</h1>
</div>
<div class="card shadow mb-4">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Users Resign List</h6>
        <div class="text-right">
            <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#addModal"><i class="fas fa-plus fa-sm text-white-50"></i> Add Resign User</a>
        </div>
    </div>
    <!-- Card Body -->
    <div class="card-body">
        <table class="table table-bordered zoom90 table-hover" id="dataTableUser">
            <thead>
                <tr style="font-size: 13px">
                    <th>Emp ID</th>
                    <th>User ID</th>
                    <th>Nama</th>
                    <th>Status Active</th>
                    <th>Position</th>
                    <th>Hired Date</th>
                    <th>Resign Date</th>
                    <th width="80px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $p)
                <tr>
                    <td>{{$p->users_detail->employee_id}}</td>
                    <td>{{$p->id }}</td>
                    <td>{{$p->name}}</td>
                    <td>{{$p->users_detail->status_active}}</td>
                    <td>@if($p->users_detail->position_id){{ $p->users_detail->position->position_name }}@endif</td>
                    <td>{{$p->users_detail->hired_date}}</td>
                    <td>{{$p->users_detail->resignation_date}}</td>
                    <td>
                        <a href="/hr/exit_clearance/print/{{ $p->id }}" target="_blank" title="Print" class="btn btn-primary btn-sm ml-3" >
                            <i class="fas fa-fw fa-print "></i>
                            Print
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Resign User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <form action="/hr/exit_clearance/resign_emp" method="post">
            @csrf
            @method('PUT')
        <div class="modal-body" style="">
            <div class="col-md-12 zoom90">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="password">Select Users :</label>
                            <select class="custom-select" id="inputUser" name="inputUser">
                                <option selected disabled>Choose...</option>
                                @foreach ($us_List as $userlist)
                                    <option value="{{ $userlist->id}}">{{ $userlist->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 zoom90">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="password">Input Date Resign:</label>
                            <input class="form-control" type="date"  name="resign_date" id="resignation_date" value="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <input type="submit" class="btn btn-success btn-sm" value="Save Changes">
    </div>
        </form>
    </div>
  </div>
</div>
@endsection
