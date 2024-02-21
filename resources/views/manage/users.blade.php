@extends('layouts.main')

@section('title', 'Manage Users - ESS')

@section('active-page-system_management')
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
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-users"></i> Manage Users</h1>
        <p class="mb-3">Manage Users Account.</p>
    </div>
    <a class="btn btn-success btn-sm shadow-sm" href="/hrtools/manage/position"><i class="fas fa-solid fa-user-plus fa-sm text-white-50"></i> Position & Department</a>
</div>
<div class="card shadow mb-4 zoom90">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Manage Department</h6>
        <div class="text-right">
            <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="/manage/users/new-user-registration" ><i class="fas fa-plus fa-sm text-white-50"></i> Add User</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90 table-hover" width="100%" id="dataTableUser" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Emp ID</th>
                        <th>User ID</th>
                        <th>Nama</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Employement Status</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $p)
                    <tr>
                        <td>{{$p->users_detail->employee_id}}</td>
                        <td>{{$p->id }}</td>
                        <td>{{$p->name}}</td>
                        <td>@if($p->users_detail->position_id){{ $p->users_detail->position->position_name }}@endif</td>
                        <td>@if($p->users_detail->department_id){{ $p->users_detail->department->department_name }}@endif</td>
                        <td>{{$p->users_detail->employee_status}}</td>
                        <td class="text-center">
                            @if ($p->users_detail->status_active == "Active")
                                <i class="fas fa-user-check" style="color: #0053fa;"></i>
                            @else
                                <i class="fas fa-user-times" style="color: #ff0000;"></i>
                            @endif
                        </td>
                        <td class="row-cols-2 justify-content-between">
                            <a href="/users/edit/{{ Crypt::encrypt($p->id) }}" title="Edit" class="btn btn-primary btn-sm" >
                                <i class="fas fa-fw fa-edit justify-content-center"></i>
                            </a>
                            <a href="/users/hapus/{{ Crypt::encrypt($p->id) }}" title="Hapus" class="btn btn-danger btn-sm" ><i class="fas fa-fw fa-trash justify-content"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
