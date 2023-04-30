
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
                <div class="card-body">
                    <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <table class="table table-borderless align-items-center text-center">
                                    <tbody>
                                        <tr>
                                            <td><img src="{{ asset('img/profile.jpg') }}" style="height: 100px; width: 100px;" /></td>
                                        </tr>
                                        <tr>
                                            <td><a class="btn @role('freelancer') btn-success @else btn-primary @endrole btn-sm" type="button" href="#" id="manButton">Upload Foto</a></td>
                                        </tr>
                                        <tr>
                                            <td><img src="{{ asset('img/PC-01.png') }}" style="height: 92px; width: 225px;" /></td>
                                        </tr>
                                        <tr>
                                            <td><a class="btn @role('freelancer') btn-success @else btn-primary @endrole btn-sm" type="button" href="#" id="manButton">Upload CV</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-borderless ">
                                    {{-- Informasi Pribadi --}}
                                    <thead>
                                        <tr>
                                            <th class="m-0 font-weight-bold text-primary" colspan="2">Profile Information</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-sm">
                                            <td>Name</td>
                                            <td><input class="form-control" type="text" name="name" placeholder="Name...">
                                                @if($errors->has('name'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('name')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Birth Date </td>
                                            <td>
                                                <input class="form-control" type="date" name="usr_dob" id="usr_dob" value="" />
                                                @if($errors->has('usr_dob'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_dob')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Birth Place</td>
                                            <td><input class="form-control" type="text"   name="usr_birth_place" placeholder="Birth Place...">
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
                                                <select class="form-control " name="usr_gender">
                                                    <option selected disabled>Choose...</option>
                                                    <option value="M">Male</option>
                                                    <option value="F">Female</option>
                                                </select>
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
                                                <select class="form-control " name="usr_religion">
                                                    <option selected disabled>Choose...</option>
                                                    <option value="Islam">Islam</option>
                                                    <option value="Kristen">Kristen Protestan</option>
                                                    <option value="Katholik">Kristen Katholik</option>
                                                    <option value="Konghucu">Konghucu</option>
                                                    <option value="Hindu">Hindu</option>
                                                    <option value="Buddha">Buddha</option>
                                                    <option value="O">Others</option>
                                                </select>
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
                                                <select class="form-control " name="usr_merital_status">
                                                    <option selected disabled>Choose...</option>
                                                    <option value="S">Single</option>
                                                    <option value="M">Married</option>
                                                    <option value="Widow">Widow (Janda)</option>
                                                    <option value="Widower">Widower (Duda)</option>
                                                    <option value="Divorced">Divorced (Cerai)</option>
                                                </select>
                                                @if($errors->has('usr_merital_status'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_merital_status')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Num of Children</td>
                                            <td><input class="form-control" type="text"   name="usr_children" placeholder="Number of Childern...">
                                                @if($errors->has('usr_children'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_children')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                    {{-- Kontak Informasi --}}
                                    <thead>
                                        <tr>
                                            <th class="m-0 font-weight-bold text-primary" colspan="2">Contact Information</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-sm">
                                            <td>Address</td>
                                            <td><textarea class="form-control"   name="usr_address" placeholder="User Address..."></textarea>
                                                @if($errors->has('usr_address'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_address')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Current Address</td>
                                            <td><textarea class="form-control"   name="current_address" placeholder="Current Address..."></textarea>
                                                @if($errors->has('current_address'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('current_address')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>City</td>
                                            <td><input class="form-control"   name="usr_address_city" placeholder="Addres City...">
                                                @if($errors->has('usr_address_city'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_address_city')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Postal Code</td>
                                            <td><input class="form-control"   name="usr_address_postal" placeholder="Address Postal...">
                                                @if($errors->has('usr_address_postal'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_address_postal')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Home Phone</td>
                                            <td><input class="form-control"   name="usr_phone_home" placeholder="Home Phone Number...">
                                                @if($errors->has('usr_phone_home'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_phone_home')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Mobile Phone</td>
                                            <td><input class="form-control" type="text"   name="usr_phone_mobile" placeholder="Mobile Phone Number...">
                                                @if($errors->has('usr_phone_mobile'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_phone_mobile')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                    {{-- Informasi bank --}}
                                    <thead>
                                        <tr>
                                            <th class="m-0 font-weight-bold text-primary" colspan="2">Bank Account</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-sm">
                                            <td>Bank Name</td>
                                            <td><input class="form-control" type="text"   name="usr_bank_name" placeholder="Bank Name...">
                                                @if($errors->has('usr_bank_name'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_bank_name')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Bank Branch</td>
                                            <td><input class="form-control" type="text"   name="usr_bank_branch" placeholder="Bank Branch ...">
                                                @if($errors->has('usr_bank_branch'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_bank_branch')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Bank Account Number</td>
                                            <td><input class="form-control" type="text"   name="usr_bank_account" placeholder="Bank Account Number...">
                                                @if($errors->has('usr_bank_account'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_bank_account')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-borderless ">
                                <thead>
                                    <tr>
                                        <th class="m-0 font-weight-bold text-primary" colspan="2">Employee Information</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-sm">
                                        <td>Department</td>
                                        <td>
                                            <select class="form-control " id="department" name="department"  style="width: 95%">
                                                <option selected disabled>Choose...</option>
                                                @foreach($dep_data as $depart)
                                                <option value="{{ $depart ->id }}">{{ $depart ->department_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td>Position</td>
                                        <td>
                                            <select class="form-control " id="position" name="position"  style="width: 95%">
                                                <option selected disabled>Choose...</option>
                                                @foreach($pos_data as $pos)
                                                <option value="{{ $pos ->id }}">{{ $pos ->position_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td>Status Active</td>
                                            <td><select class="form-control " name="status"  style="width: 95%">
                                                <option selected disabled>Choose...</option>
                                                <option value="Active">Active</option>
                                                <option value="nonActive">Non Active</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td>Employee Status</td>
                                        <td><select class="form-control " name="employee_status" style="width: 95%">
                                                <option selected disabled>Choose...</option> 
                                                <option value="Probation">Probation</option>
                                                <option value="Contract">Contract</option>
                                                <option value="Permanent">Permanent</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td>Hired Date</td>
                                        <td>
                                            <input class="form-control" type="date" style="width: 95%" name="hired_date" id="hired_date" value="" />
                                            @if($errors->has('hired_date'))
                                                <div class="text-danger">
                                                    {{ $errors->first('hired_date')}}
                                                </div>
                                            @endif
                                            </td>
                                    </tr>
                                    <tr class="table-sm">
                                    <td>Resign Date</td>
                                        <td>
                                            <input class="form-control" type="date" style="width: 95%" name="resignation_date" id="resignation_date" value="" />
                                            @if($errors->has('resignation_date'))
                                                <div class="text-danger">
                                                    {{ $errors->first('resignation_date')}}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                                    {{-- Account Information --}}
                                    <thead>
                                        <tr>
                                            <th class="m-0 font-weight-bold text-primary" colspan="2">Account Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-sm">
                                            <td>Employee ID</td>
                                            <td><input class="form-control"  name="employee_id" placeholder="Employee ID..." style="width: 95%">
                                            @if($errors->has('employee_id'))
                                                <div class="text-danger">
                                                    {{ $errors->first('employee_id')}}
                                                </div>
                                            @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>User ID</td>
                                            <td><input class="form-control flex" name="usr_id" placeholder="User ID..." style="width: 95%">
                                                @if($errors->has('usr_id'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_id')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Email</td>
                                            <td><input class="form-control" name="email" placeholder="Email..." style="width: 95%">
                                                @if($errors->has('email'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('email')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Password</td>
                                            <td><input class="form-control" name="password" value="" placeholder="****" style="width: 95%">
                                            </td>
                                        </tr>
                                    </tbody>
                                {{-- Dokumen Informasi --}}
                                    <thead>
                                        <tr>
                                            <th class="m-0 font-weight-bold text-primary" colspan="2">Document Information</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-sm">
                                            <td>NPWP</td>
                                            <td><input class="form-control" type="text" name="usr_npwp" placeholder="NPWP Number..." style="width: 95%">
                                                @if($errors->has('usr_npwp'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_npwp')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Identity Type</td>
                                            <td>
                                                <select class="form-control " name="usr_id_type" style="width: 95%">
                                                    <option selected disabled>Choose...</option>
                                                    <option value="KTP">KTP</option>
                                                    <option value="SIM">SIM</option>
                                                    <option value="Passport">Passport</option>
                                                </select>
                                                @if($errors->has('usr_id_type'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_id_type')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Identity No</td>
                                            <td><input class="form-control" type="text"   name="usr_id_no" placeholder="Number Identity..." style="width: 95%">
                                                @if($errors->has('usr_id_no'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_id_no')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Identity Expiration</td>
                                            <td><input class="form-control" type="text"   name="usr_id_expiration" placeholder="User Identity Expiration..." style="width: 95%">
                                                @if($errors->has('usr_id_expiration'))
                                                    <div class="text-danger">
                                                        {{ $errors->first('usr_id_expiration')}}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
