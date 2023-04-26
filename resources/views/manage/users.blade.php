@extends('layouts.main')

@section('active-page-users')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800">Manage Users</h1>
    <a  href="/users/tambah" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Add User</a>
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
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Users List</h6>
        {{-- <div class="text-right">
            <a class="btn btn-primary btn-sm" type="button" href="/users/tambah">+ Add User</a>
        </div> --}}
    </div>
    <!-- Card Body -->
    <div class="card-body">
        <table class="table table-bordered table-hover" id="dataTableUser">
            <thead>
                <tr style="font-size: 13px" class="text-center">
                    <th>Emp ID</th>
                    <th>User ID</th>
                    <th>Nama</th>
                    <th>Status</th>
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
                    <td>{{$p->users_detail->employee_status}}</td>
                    <td>{{$p->users_detail->position}}</td>
                    <td>{{$p->users_detail->department}}</td>
                    <td class="row-cols-2 justify-content-betwen text-center">
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
