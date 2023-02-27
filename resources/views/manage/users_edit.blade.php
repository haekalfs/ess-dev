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
            <h3>Edit Data Pegawai</h3>
            </div>
                <div class="card-body">
                    <a href="/manage/users" class="btn btn-primary">Kembali</a>
                    <br/>
                    <br/>
                    <form method="post" action="/users/update/{{ $data->id }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Employee Id</label>
                        <input type="text" name="employee_id" class="form-control" placeholder="Employee ID..." value=" {{ $data->users_detail->employee_id}}">

                        @if($errors->has('employee_id'))
                            <div class="text-danger">
                                {{ $errors->first('employee_id')}}
                            </div>
                        @endif

                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Pegawai .." value=" {{ $data->name }}">

                        @if($errors->has('name'))
                            <div class="text-danger">
                                {{ $errors->first('nama')}}
                            </div>
                        @endif

                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Email Pegawai .." value="{{ $data->email }}">
                        @if($errors->has('email'))
                            <div class="text-danger">
                                {{ $errors->first('email')}}
                            </div>
                        @endif

                    </div>
                    <div class="form-group">
                        <label>Posisi</label>
                        <input type="text" name="posisi" class="form-control" placeholder="Posisi pegawai .." value=" {{$data->users_detail->position}}">
                        @if($errors->has('posisi'))
                            <div class="text-danger">
                                {{ $errors->first('posisi')}}
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Employee Status</label>
                        <input type="text" name="employee_status" class="form-control" placeholder="Posisi pegawai .." value=" {{$data->users_detail->employee_status}}">
                        @if($errors->has('employee_status'))
                            <div class="text-danger">
                                {{ $errors->first('posisi')}}
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Hired Date</label>
                        <input type="text" name="hired_date" class="form-control" placeholder="Posisi pegawai .." value=" {{$data->users_detail->hired_date}}">
                        @if($errors->has('hired_date'))
                            <div class="text-danger">
                                {{ $errors->first('hired_date')}}
                            </div>
                        @endif
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-success" value="Simpan">
                    </div>
                    </form>
                </div>
            </div> 
        </div>   
    </div>        
</div>

@endsection
