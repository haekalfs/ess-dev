
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
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between ">
                <h4 class="m-0 font-weight-bold text-primary">Add Employee</h4>
                <form method="POST" action="/users/store">
                @csrf
                <div class="text-right">
                    <a href="/manage/users" class="btn btn-primary btn-sm" id="manButton">Kembali</a>
                    <input type="submit" class="btn btn-success btn-sm" value="Simpan">
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body zoom90">
                <div class="row">
                    <div class="col-md-3 align-items-center text-center">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td><img src="{{ asset('img/PC-01.png') }}" style="height: 92px; width: 225px;" /></td>
                                </tr>
                                <tr>
                                    <td><a class="btn btn-primary btn-sm" type="button" href="#" id="manButton">Upload CV</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th class="m-0 font-weight-bold text-primary" colspan="2">Profile Information</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-sm">
                                            <td>Name</td>
                                            <td><input class="input-group-text" type="text" name="name" placeholder="Name...">
                                                @if($errors->has('name'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('name')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Birth Date :</td>
                                            <td>
                                                <input class="input-group-text" type="date" name="usr_dob" id="usr_dob" value="" />
                                                {{-- <input class="input-group-text" type="text" style="font-size: 11px"  name="usr_dob" placeholder="Date Of Birth..."> --}}
                                                @if($errors->has('usr_dob'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_dob')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Birth Place</td>
                                            <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_birth_place" placeholder="Birth Place...">
                                                @if($errors->has('usr_birth_place'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_birth_place')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Gender</td>
                                            <td>
                                                <select class="form-control form-control-sm" name="usr_gender" style="width: 48%">
                                                    <option selected disabled>Choose...</option>
                                                    <option value="M">Male</option>
                                                    <option value="F">Female</option>
                                                </select>
                                                {{-- <input class="input-group-text" type="text" style="font-size: 11px"  name="usr_gender" placeholder="Gender..."> --}}
                                                @if($errors->has('usr_gender'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_gender')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Religion</td>
                                            <td>
                                                <select class="form-control form-control-sm" name="usr_religion" style="width: 48%">
                                                    <option selected disabled>Choose...</option>
                                                    <option value="Islam">Islam</option>
                                                    <option value="Kristen">Kristen</option>
                                                    <option value="Katholik">Katholik</option>
                                                    <option value="Konghucu">Konghucu</option>
                                                    <option value="Hindu">Hindu</option>
                                                    <option value="Buddha">Buddha</option>
                                                </select>
                                                {{-- <input class="input-group-text" type="text" style="font-size: 11px"  name="usr_religion" placeholder="Religion..."> --}}
                                                @if($errors->has('usr_religion'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_religion')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Marital Status</td>
                                            <td>
                                                <select class="form-control form-control-sm" name="usr_merital_status" style="width: 48%">
                                                    <option selected disabled>Choose...</option>
                                                    <option value="S">Single</option>
                                                    <option value="M">Married</option>
                                                    <option value="Widow">Widow</option>
                                                    <option value="Widower">Widower</option>
                                                    <option value="Divorced">Divorced</option>
                                                </select>
                                                {{-- <input class="input-group-text" type="text" style="font-size: 11px"  name="usr_merital_status" placeholder="Merital Status..."> --}}
                                                @if($errors->has('usr_merital_status'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_merital_status')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Num of Children</td>
                                            <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_children" placeholder="Number of Childern...">
                                                @if($errors->has('usr_children'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_children')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="col-md-7">
                                    <table class="table table-borderless">
                                        <thead>
                                    <tr>
                                        <th class="m-0 font-weight-bold text-primary" colspan="2">Profile Information</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-sm">
                                        <td>Name</td>
                                        <td><input class="input-group-text" type="text" name="name" placeholder="Name...">
                                            @if($errors->has('name'))
                                                <div class="text-danger">
                                                    {{ $errors->first('name')}}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td>Birth Date :</td>
                                        <td>
                                            <input class="input-group-text" type="date" name="usr_dob" id="usr_dob" value="" />
                                            {{-- <input class="input-group-text" type="text" style="font-size: 11px"  name="usr_dob" placeholder="Date Of Birth..."> --}}
                                            @if($errors->has('usr_dob'))
                                                <div class="text-danger">
                                                    {{ $errors->first('usr_dob')}}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td>Birth Place</td>
                                        <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_birth_place" placeholder="Birth Place...">
                                            @if($errors->has('usr_birth_place'))
                                                <div class="text-danger">
                                                    {{ $errors->first('usr_birth_place')}}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td>Gender</td>
                                        <td>
                                            <select class="form-control form-control-sm" name="usr_gender" style="width: 48%">
                                                <option selected disabled>Choose...</option>
                                                <option value="M">Male</option>
                                                <option value="F">Female</option>
                                            </select>
                                            {{-- <input class="input-group-text" type="text" style="font-size: 11px"  name="usr_gender" placeholder="Gender..."> --}}
                                            @if($errors->has('usr_gender'))
                                                <div class="text-danger">
                                                    {{ $errors->first('usr_gender')}}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td>Religion</td>
                                        <td>
                                            <select class="form-control form-control-sm" name="usr_religion" style="width: 48%">
                                                <option selected disabled>Choose...</option>
                                                <option value="Islam">Islam</option>
                                                <option value="Kristen">Kristen</option>
                                                <option value="Katholik">Katholik</option>
                                                <option value="Konghucu">Konghucu</option>
                                                <option value="Hindu">Hindu</option>
                                                <option value="Buddha">Buddha</option>
                                            </select>
                                            {{-- <input class="input-group-text" type="text" style="font-size: 11px"  name="usr_religion" placeholder="Religion..."> --}}
                                            @if($errors->has('usr_religion'))
                                                <div class="text-danger">
                                                    {{ $errors->first('usr_religion')}}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td>Marital Status</td>
                                        <td>
                                            <select class="form-control form-control-sm" name="usr_merital_status" style="width: 48%">
                                                <option selected disabled>Choose...</option>
                                                <option value="S">Single</option>
                                                <option value="M">Married</option>
                                                <option value="Widow">Widow</option>
                                                <option value="Widower">Widower</option>
                                                <option value="Divorced">Divorced</option>
                                            </select>
                                            {{-- <input class="input-group-text" type="text" style="font-size: 11px"  name="usr_merital_status" placeholder="Merital Status..."> --}}
                                            @if($errors->has('usr_merital_status'))
                                                <div class="text-danger">
                                                    {{ $errors->first('usr_merital_status')}}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td>Num of Children</td>
                                        <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_children" placeholder="Number of Childern...">
                                            @if($errors->has('usr_children'))
                                                <div class="text-danger">
                                                    {{ $errors->first('usr_children')}}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                                    </table>
                                </div>
                                <div class="col-md-5">
                                    <table class="table table-borderless">
                                        
                                    </table>
                                </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-7">
                                        <table class="table table-borderless">
                                            
                                        </table>
                                    </div>
                                    <div class="col-md-5">
                                        <table class="table table-borderless">
                                           
                                        </table>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div><br>
    </div>
</div>
@endsection
