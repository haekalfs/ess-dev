@extends('layouts.main')

@section('active-page-users')
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

<div class="container">
    <div class="card mt-5">
        <div class="card-header text-center">
            <h3>Data Pegawai</h3>
        </div>
        <div class="card-body">
            <a href="/users/tambah" class="btn btn-primary">Input Pegawai Baru</a>
            <br/>
            <br/>
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Employee ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Posisi</th>
                        <th>Employee Status</th>
                        <th>Hired Date</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $p)
                    <tr>
                        <td>{{ $p->user_id }}</td>
                        <td>{{$p->users_detail->employee_id}}</td>
                        <td>{{$p->name}}</td>
                        <td>{{$p->email}}</td>
                        <td>{{$p->users_detail->position}}</td>
                        <td>{{$p->users_detail->employee_status}}</td>
                        <td>{{$p->users_detail->hired_date}}</td>
                        <td>
                            <a href="/users/edit/{{ $p->id }}" class="btn btn-warning">Edit</a>
                            <a href="/users/hapus/{{ $p->id }}" class="btn btn-danger">Hapus</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
