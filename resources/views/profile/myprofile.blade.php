@extends('layouts.main')

@section('active-page-myprofile')
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
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Employee Information</h6>
                <div class="text-right">
                    <a class="btn btn-danger btn-sm" type="button" href="{{ url()->previous() }}" id="manButton">Change Password</a>
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
                                    <td><a class="btn btn-primary btn-sm" type="button" href="{{ url()->previous() }}" id="manButton">Upload CV</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-12">
                                <h1 class="m-0 font-weight-bold text-dark">[{{$user_info->users_detail->employee_id}}] {{Auth::user()->name}}</h1><br>
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr class="table-sm">
                                            <td>Department</td>
                                            <td style="width: 400px;">: Finances And General Affair</td>
                                            <td style="width: 200px;">Employment Status</td>
                                            <td>: {{$user_info->users_detail->status}}</td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td>Status</td>
                                            <td style="width: 200px;">: {{$user_info->users_detail->hired_date}}</td>
                                            <td style="width: 200px;">Hired Date</td>
                                            <td>: {{$user_info->users_detail->hired_date}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-7">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th class="m-0 font-weight-bold text-primary" colspan="2">Contact Information</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>Address</td>
                                                    <td>: {{$user_info->users_detail->usr_address}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Current Address</td>
                                                    <td>: {{$user_info->users_detail->current_address}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>City</td>
                                                    <td>: {{$user_info->users_detail->usr_address_city}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Postal Code</td>
                                                    <td>: {{$user_info->users_detail->usr_address_postal}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Province</td>
                                                    <td>: {{$user_info->users_detail->employee_id}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Email</td>
                                                    <td>: {{$user_info->email}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Home Phone</td>
                                                    <td>: {{$user_info->users_detail->usr_phone_home}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Mobile Phone</td>
                                                    <td>: {{$user_info->users_detail->usr_phone_mobile}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-5">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th class="m-0 font-weight-bold text-primary" colspan="2">Profile Information</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>Birth Date :</td>
                                                    <td>: {{$user_info->users_detail->usr_dob}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Birth Place</td>
                                                    <td>: {{$user_info->users_detail->usr_birth_place}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Gender</td>
                                                    <td>: {{$user_info->users_detail->usr_gender}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Religion</td>
                                                    <td>: {{$user_info->users_detail->usr_religion}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Merital Status</td>
                                                    <td>: {{$user_info->users_detail->usr_merital_status}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Num of Children</td>
                                                    <td>: {{$user_info->users_detail->usr_children}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-7">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th class="m-0 font-weight-bold text-primary" colspan="2">Document Information</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>NPWP</td>
                                                    <td>: {{$user_info->users_detail->usr_npwp}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Identity Type</td>
                                                    <td>: {{$user_info->users_detail->usr_id_type}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Identity No</td>
                                                    <td>: {{$user_info->users_detail->usr_id_no}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Identity Expiration</td>
                                                    <td>: {{$user_info->users_detail->usr_id_expiration}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-5">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th class="m-0 font-weight-bold text-primary" colspan="2">Bank Account</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>Bank Name</td>
                                                    <td>: {{$user_info->users_detail->usr_bank_name}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Bank Branch</td>
                                                    <td>: {{$user_info->users_detail->usr_bank_branch}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Bank Account Num</td>
                                                    <td>: {{$user_info->users_detail->usr_bank_account}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Bank Account Name</td>
                                                    <td>: {{$user_info->name}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><br>
    </div>
</div>
@endsection
