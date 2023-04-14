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
                <h4 class="m-0 font-weight-bold text-primary">Edit Employee Information</h4>
                <form method="post" action="/users/update/{{ $user->id }}">
                @csrf
                @method('PUT')
                <div class="text-right">
                    <a href="/manage/users" class="btn btn-primary" id="manButton">Kembali</a>
                    <input type="submit" class="btn btn-success" value="Simpan">
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body zoom90">
                <div class="row ">
                    <div class="col-md-3 ">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td><img src="{{ asset('img/PC-01.png') }}" style="height: 92px; width: 225px;" /></td>
                                </tr>
                                <tr>
                                    <td><a class="btn btn-primary btn-sm" type="button" href="{{ url()->previous() }}" id="manButton">Upload CV</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="row input-group input-group-sm ">
{{-- Employee Informasi --}}
                    <table class="table table-borderless ">
                        <thead>
                            <tr>
                                <th class="m-0 font-weight-bold text-primary" colspan="2">Employee Information</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-sm">
                                <td>Department</td>
                                <td>Finances And General Affair</td>
                            </tr>
                            <tr class="table-sm">
                                <td>Position</td>
                                <td><input class="input-group-text flex"  style="font-size: 11px"  name="position" placeholder="Position..." value=" {{ $user->users_detail->position}}">
                                    @if($errors->has('position'))
                                        <div class="text-danger">
                                            {{ $errors->first('position')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Status</td>
                                <td>
                                    <select class="form-control form-control-sm" name="status" style="width: 48%">
                                        <option value="active" {{ old('status', $user->users_detail->status) == "active" ? 'selected' : '' }}>Active</option>
                                        <option value="nonActive" {{ old('status', $user->users_detail->status) == "nonActive" ? 'selected' : '' }}>Non Active</option>
                                    </select>
                                    {{-- <input class="input-group-text flex"  style="font-size: 11px"  name="status" placeholder="Status..." value=" {{ $user->users_detail->status}}"> --}}
                                    @if($errors->has('status'))
                                        <div class="text-danger">
                                            {{ $errors->first('status')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Employee Status</td>
                                <td><input class="input-group-text" style="font-size: 11px"  name="employee_status" placeholder="Status Employee..." value=" {{ $user->users_detail->employee_status}}">
                                    @if($errors->has('employee_status'))
                                        <div class="text-danger">
                                            {{ $errors->first('employee_status')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Hired Date</td>
                                <td><input class="input-group-text" style="font-size: 11px" name="hired_date" placeholder="Hired Date..." value=" {{ $user->users_detail->hired_date}}">
                                    @if($errors->has('hired_date'))
                                        <div class="text-danger">
                                            {{ $errors->first('hired_date')}}
                                        </div>
                                    @endif
                                    </td>
                            </tr>
                            <tr class="table-sm">
                            <td>Resign Date</td>
                                <td><input class="input-group-text" style="font-size: 11px" name="resignation_date" placeholder="Resign Date..." value=" {{ $user->users_detail->resignation_date}}">
                                    @if($errors->has('resignation_date'))
                                        <div class="text-danger">
                                            {{ $errors->first('resignation_date')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Employee ID</td>
                                <td><input class="input-group-text" style="font-size: 11px"  name="employee_id" placeholder="Employee ID..." value=" {{ $user->users_detail->employee_id}}">
                                @if($errors->has('employee_id'))
                                    <div class="text-danger">
                                        {{ $errors->first('employee_id')}}
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
                                <td><input class="input-group-text" style="font-size: 11px"  name="usr_address" placeholder="User Address..." value=" {{ $user->users_detail->usr_address}}">
                                    @if($errors->has('usr_address'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_address')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Current Address</td>
                                <td><input class="input-group-text" style="font-size: 11px"  name="current_address" placeholder="Current Address..." value=" {{ $user->users_detail->current_address}}">
                                    @if($errors->has('current_address'))
                                        <div class="text-danger">
                                            {{ $errors->first('current_address')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>City</td>
                                <td><input class="input-group-text" style="font-size: 11px"  name="usr_address_city" placeholder="Addres City..." value=" {{ $user->users_detail->usr_address_city}}">
                                    @if($errors->has('usr_address_city'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_address_city')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Postal Code</td>
                                <td><input class="input-group-text" style="font-size: 11px"  name="usr_address_postal" placeholder="Address Postal..." value=" {{ $user->users_detail->usr_address_postal}}">
                                    @if($errors->has('usr_address_postal'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_address_postal')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Email</td>
                                <td><input class="input-group-text" style="font-size: 11px"  name="email" placeholder="Email..." value=" {{$user->email}}">
                                    @if($errors->has('email'))
                                        <div class="text-danger">
                                            {{ $errors->first('email')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Home Phone</td>
                                <td><input class="input-group-text" style="font-size: 11px"  name="usr_phone_home" placeholder="Home Phone Number..." value=" {{ $user->users_detail->usr_phone_home}}">
                                    @if($errors->has('usr_phone_home'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_phone_home')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Mobile Phone</td>
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_phone_mobile" placeholder="Mobile Phone Number..." value=" {{ $user->users_detail->usr_phone_mobile}}">
                                    @if($errors->has('usr_phone_mobile'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_phone_mobile')}}
                                        </div>
                                    @endif
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
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_npwp" placeholder="NPWP Number..." value=" {{ $user->users_detail->usr_npwp}}">
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
                                    <input class="input-group-text" type="text" style="font-size: 11px"  name="usr_id_type" placeholder="ID Type..." value=" {{ $user->users_detail->usr_id_type}}">
                                    @if($errors->has('usr_id_type'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_id_type')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Identity No</td>
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_id_no" placeholder="Number Identity..." value=" {{ $user->users_detail->usr_id_no}}">
                                    @if($errors->has('usr_id_no'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_id_no')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Identity Expiration</td>
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_id_expiration" placeholder="User ID Card Expiration..." value=" {{ $user->users_detail->usr_id_expiration}}">
                                    @if($errors->has('usr_id_expiration'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_id_expiration')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                        <thead>
                            <tr>
                                <th class="m-0 font-weight-bold text-primary" colspan="2">Profile Information</th>
                            </tr>
                        </thead>
                        {{-- Informasi Pribadi --}}
                        <tbody>
                            <tr class="table-sm">
                                <td>Name</td>
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="name" placeholder="Name..." value=" {{ $user->name}}">
                                    @if($errors->has('name'))
                                        <div class="text-danger">
                                            {{ $errors->first('name')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Birth Date :</td>
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_dob" placeholder="Date Of Birth..." value=" {{ $user->users_detail->usr_dob}}">
                                    @if($errors->has('usr_dob'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_dob')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Birth Place</td>
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_birth_place" placeholder="Birth Place..." value=" {{ $user->users_detail->usr_birth_place}}">
                                    @if($errors->has('usr_birth_place'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_birth_place')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Gender</td>
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_gender" placeholder="Gender..." value=" {{ $user->users_detail->usr_gender}}">
                                    @if($errors->has('usr_gender'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_gender')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Religion</td>
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_religion" placeholder="Religion..." value=" {{ $user->users_detail->usr_religion}}">
                                    @if($errors->has('usr_religion'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_religion')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Marital Status</td>
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_merital_status" placeholder="Merital Status..." value=" {{ $user->users_detail->usr_merital_status}}">
                                    @if($errors->has('usr_merital_status'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_merital_status')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Num of Children</td>
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_children" placeholder="Number of Childern..." value=" {{ $user->users_detail->usr_children}}">
                                    @if($errors->has('usr_children'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_children')}}
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
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_bank_name" placeholder="Bank Name..." value=" {{ $user->users_detail->usr_bank_name}}">
                                    @if($errors->has('usr_bank_name'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_bank_name')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Bank Branch</td>
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_bank_branch" placeholder="Bank Branch ..." value=" {{ $user->users_detail->usr_bank_branch}}">
                                    @if($errors->has('usr_bank_branch'))
                                        <div class="text-danger">
                                            {{ $errors->first('usr_bank_branch')}}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="table-sm">
                                <td>Bank Account Number</td>
                                <td><input class="input-group-text" type="text" style="font-size: 11px"  name="usr_bank_account" placeholder="Number of Childern..." value=" {{ $user->users_detail->usr_bank_account}}">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
