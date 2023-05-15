@extends('layouts.main')

@section('title', 'My Profile - ESS')

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
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Employee Information</h6>
                <div class="text-right">
                    <a class="btn btn-danger btn-sm" type="button" data-toggle="modal" data-target="#changePass" id="manButton">Change Password</a>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body zoom90">
                <div class="row">
                    <div class="col-md-3 align-items-center text-center">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td><img src="{{ asset('img/profile.jpg') }}" style="height: 100px; width: 100px;" /></td>
                                </tr>
                                <tr>
                                    <td><a class="btn @role('freelancer') btn-success @else btn-primary @endrole btn-sm" type="button" href="#" id="manButton">Upload CV</a></td>
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
                                            <td style="width: 150px;">Department</td>
                                            <td style="width: 300px;">: @if($user_info->users_detail->department_id)
                                                    {{ $user_info->users_detail->department->department_name }}
                                                @endif
                                            </td>
                                            <td style="width: 200px;">Employment Status</td>
                                            <td>: {{$user_info->users_detail->employee_status}}</td>
                                        </tr>
                                        <tr class="table-sm">
                                            <td style="width: 150px;">Position</td>
                                            <td style="width: 300px;">: @if($user_info->users_detail->position_id)
                                                    {{ $user_info->users_detail->position->position_name }}
                                                @endif
                                            </td>
                                            <td style="width: 150px;">Status Active</td>
                                            <td style="width: 200px;">: {{$user_info->users_detail->status_active}}</td>
                                        </tr>
                                        <tr class="table-sm">
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
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole h5" colspan="2">Contact Information</th>
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
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole h5" colspan="2">Profile Information</th>
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
                                                    <td>: @switch($user_info->users_detail->usr_gender)@case('M') Male @break @case('F') Female @break @default Unknown Gender @endswitch</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Religion</td>
                                                    <td>: @switch($user_info->users_detail->usr_religion)
                                                        @case('Islam') Islam @break 
                                                        @case('Kristen') Kristen Protestan @break 
                                                        @case('Katholik') Kristen Katholik @break 
                                                        @case('Hindu') Hindu @break 
                                                        @case('Buddha') Buddha @break 
                                                        @case('Konghucu') Konghucu @break 
                                                        @case('O') Other @break 
                                                        @default Unknown Religion @endswitch
                                                    </td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Marital Status</td>
                                                    <td>: @switch($user_info->users_detail->usr_merital_status)
                                                        @case('S') Single @break 
                                                        @case('M') Married @break 
                                                        @case('Widow') Widow / Janda @break 
                                                        @case('Widower') Widower / Duda @break 
                                                        @case('Divorced') Divorced @break
                                                        @default Unknown Merital Status @endswitch
                                                    </td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Number of Children</td>
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
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole h5" colspan="2">Document Information</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td style="width: 150px;">NPWP</td>
                                                    <td>: {{$user_info->users_detail->usr_npwp}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td style="width: 150px;">Identity Type</td>
                                                    <td>: @switch($user_info->users_detail->usr_id_type)
                                                        @case('KTP') KTP @break 
                                                        @case('SIM') SIM @break
                                                        @case('Passport') Passport @break
                                                        @default Unknown Identity Type @endswitch
                                                    </td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td style="width: 150px;">Identity No</td>
                                                    <td>: {{$user_info->users_detail->usr_id_no}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td style="width: 200px;">Identity Expiration</td>
                                                    <td>: {{$user_info->users_detail->usr_id_expiration}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-5">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole h5" colspan="2">Bank Account</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td style="width: 150px;">Bank Name</td>
                                                    <td>: {{$user_info->users_detail->usr_bank_name}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td style="width: 150px;">Bank Branch</td>
                                                    <td>: {{$user_info->users_detail->usr_bank_branch}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td style="width: 150px;">Bank Account Number</td>
                                                    <td>: {{$user_info->users_detail->usr_bank_account}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td style="width: 200px;">Bank Account Name</td>
                                                    <td>: {{$user_info->name}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><br>
    </div>
</div>
<div class="modal fade" id="changePass" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Change Password</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
            @if (session('status'))
			<div class="alert alert-success" role="alert">
			    {{ session('status') }}
			</div>
			@endif
			<form method="POST" action="{{ route('password.email') }}">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
        
                            <div class="col-md-8">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>
        
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
				    </div>
                </div>
			    <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        <button type="submit" class="btn btn-primary">
                        {{ __('Send Password Reset Link') }}
                    </button>
			    </div>
			</form>
		</div>
	</div>
</div>
@endsection
