@extends('layouts.main')

@section ('content')
<body>
    <div class="container">
        <div class="card mt-5">
            <div class="card-header text-center">
                <h3>Tambah Data Pegawai</h3>
            </div>
            <div class="card-body">
                <a href="/manage/users" class="btn btn-primary">Kembali</a>
                <a href="#" class="btn btn-success">Import From Excel</a>
                <br/>
                <br/>
                
                <form method="post" action="/users/store">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>User ID</label>
                        <input type="text" name="user_id" class="form-control" placeholder="User ID ..">

                        @if($errors->has('user_id'))
                            <div class="text-danger">
                                {{ $errors->first('user_id')}}
                            </div>
                        @endif

                    </div>
                    <div class="form-group">
                        <label>Employee ID</label>
                        <input type="text" name="employee_id" class="form-control" placeholder="Employee ID ..">

                        @if($errors->has('employee_id'))
                            <div class="text-danger">
                                {{ $errors->first('employee_id')}}
                            </div>
                        @endif

                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama pegawai ..">

                        @if($errors->has('name'))
                            <div class="text-danger">
                                {{ $errors->first('name')}}
                            </div>
                        @endif

                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Email pegawai ..">

                        @if($errors->has('email'))
                            <div class="text-danger">
                                {{ $errors->first('email')}}
                            </div>
                        @endif

                    </div>
                    <div class="form-group">
                        <label>Posisi</label>
                        <input type="text" name="nama" class="form-control" placeholder="Posisi pegawai ..">

                        @if($errors->has('position'))
                            <div class="text-danger">
                                {{ $errors->first('position')}}
                            </div>
                        @endif

                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" name="role" class="form-control" placeholder="Role pegawai ..">

                        @if($errors->has('role'))
                            <div class="text-danger">
                                {{ $errors->first('role')}}
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
</body>
@endsection
