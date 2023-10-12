@extends('layouts.main')

@section('title', 'Manage Users - ESS')

@section('active-page-users')
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
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800"><i class="fas fa-user"></i> Manage Users</h1>
    <div>
        <a class="btn btn-success btn-sm shadow-sm" href="/hrtools/manage/position"><i class="fas fa-solid fa-user-plus fa-sm text-white-50"></i> Position & Department</a>
    </div>
</div>
<div class="card shadow mb-4">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Users List</h6>
        <div class="text-right">
            <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="/users/tambah" ><i class="fas fa-plus fa-sm text-white-50"></i> Add User</a>
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
                    <th>Employee Status</th>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Option</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $p)
                <tr>
                    <td>{{$p->users_detail->employee_id}}</td>
                    <td>{{$p->id }}</td>
                    <td>{{$p->name}}</td>
                    <td class="text-center">
                        @if ($p->users_detail->status_active == "Active")
                            <i class="fas fa-user-check" style="color: #0053fa;"></i>
                        @else
                            <i class="fas fa-user-times" style="color: #ff0000;"></i>
                        @endif
                    </td>
                    <td>{{$p->users_detail->employee_status}}</td>
                    <td>@if($p->users_detail->position_id){{ $p->users_detail->position->position_name }}@endif</td>
                    <td>@if($p->users_detail->department_id){{ $p->users_detail->department->department_name }}@endif</td>
                    <td class="row-cols-2 justify-content-between">
                        <a href="/users/edit/{{ $p->id }}" title="Edit" class="btn btn-primary btn-sm" >
                            <i class="fas fa-fw fa-edit justify-content-center"></i>
                        </a>
                        <a href="/users/hapus/{{ $p->id }}" title="Hapus" class="btn btn-danger btn-sm" ><i class="fas fa-fw fa-trash justify-content"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
