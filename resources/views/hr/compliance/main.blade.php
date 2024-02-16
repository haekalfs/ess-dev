@extends('layouts.main')

@section('title', 'Compliance - ESS')

@section('active-page-HR')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="row align-items-center zoom90">
    <div class="col">
        <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-tasks"></i> Compliance Settings</h1>
        <p class="mb-4 text-danger"><i>Restricted Access</i></p>
    </div>
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
<div class="row zoom90">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header d-flex flex-row align-items-center justify-content-between bg-primary">
                <h6 class="m-0 font-weight-bold text-white mt-2 mb-2">System Configuration</h6>
            </div>
            <ul class="nav nav-tabs" id="pageTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="page1-tab" data-toggle="tab" href="#page1" role="tab" aria-controls="page1" aria-selected="true"><i class="fas fa-calendar-day"></i> Submission & Approval</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" id="page2-tab" data-toggle="tab" href="#page2" role="tab" aria-controls="page2" aria-selected="false"><i class="fas fa-plane-departure"></i> Submission</a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link" id="page3-tab" data-toggle="tab" href="#page3" role="tab" aria-controls="page3" aria-selected="false"><i class="fas fa-calendar-week" style="color: #ff0000;"></i> Integration</a>
                </li>
            </ul>
            <div class="card-body">
                <div class="tab-content" id="pageTabContent">
                    <div class="tab-pane fade show active" id="page1" role="tabpanel" aria-labelledby="page1-tab">
                        <form action="/hr/compliance/update/regulations" method="POST" onsubmit="return validatePassword();">
                            @method('PUT')
                            @csrf
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole h6" colspan="2">Finance & General Affair</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="email">First Approver :</label>
                                                                    <select class="form-control" name="FGA_FA" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($user as $us)
                                                                        <option value="{{ $us->id }}" @if( $us->id == $FGA1->approver ) selected @endif >{{ $us->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="password">Prior Approver :</label>
                                                                    <select class="form-control" name="FGA_PA" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($user as $us)
                                                                        <option value="{{ $us->id }}" @if( $us->id == $FGA2->approver ) selected @endif >{{ $us->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="email">Staff Finance Approver :</label>
                                                                <select class="form-control" name="Finance_approver" >
                                                                    <option selected disabled>Choose...</option>
                                                                    @foreach($user as $us)
                                                                    <option value="{{ $us->id }}" @if( $us->id == $Finance->approver ) selected @endif >{{ $us->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole h6" colspan="2">Technology & Human Capital</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="email">First Approver :</label>
                                                                    <select class="form-control" name="THM_FA" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($user as $us)
                                                                        <option value="{{ $us->id }}" @if( $us->id == $THC1->approver ) selected @endif >{{ $us->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="password">Prior Approver :</label>
                                                                    <select class="form-control" name="THM_PA" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($user as $us)
                                                                        <option value="{{ $us->id }}" @if( $us->id == $THC2->approver ) selected @endif >{{ $us->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole h6" colspan="2">Sales & Marketing</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="email">First Approver :</label>
                                                                    <select class="form-control" name="SM_FA" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($user as $us)
                                                                        <option value="{{ $us->id }}" @if( $us->id == $SM1->approver ) selected @endif >{{ $us->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="password">Prior Approver :</label>
                                                                    <select class="form-control" name="SM_PA" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($user as $us)
                                                                        <option value="{{ $us->id }}" @if( $us->id == $SM2->approver ) selected @endif >{{ $us->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole h6" colspan="2">Services</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="email">First Approver :</label>
                                                                    <select class="form-control" name="Service_FA" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($user as $us)
                                                                        <option value="{{ $us->id }}" @if( $us->id == $Service1->approver ) selected @endif >{{ $us->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="password">Prior Approver :</label>
                                                                    <select class="form-control" name="Service_PA" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($user as $us)
                                                                        <option value="{{ $us->id }}" @if( $us->id == $Service2->approver ) selected @endif >{{ $us->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-12">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-secondary @endrole h6" colspan="2">Default Approvers</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="email">First Approver :</label>
                                                                    <select class="form-control" name="Default_FA" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($user as $us)
                                                                        <option value="{{ $us->id }}" @if( $us->id == $Default1->approver ) selected @endif >{{ $us->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="password">Prior Approver :</label>
                                                                    <select class="form-control" name="Default_PA" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($user as $us)
                                                                        <option value="{{ $us->id }}" @if( $us->id == $Default2->approver ) selected @endif >{{ $us->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-12">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole h6" colspan="2">Export Role</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="email">Timesheet Export :</label>
                                                                    <select class="form-control" name="export_ts" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($position as $pos)
                                                                        <option value="{{ $pos->id }}" @if( $pos->id == $export_ts->position_id ) selected @endif >{{ $pos->position_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="password">Reimburse Export :</label>
                                                                    <select class="form-control" name="export_reimburse" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($position as $pos)
                                                                        <option value="{{ $pos->id }}" @if( $pos->id == $export_reimburse->position_id ) selected @endif >{{ $pos->position_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-12">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole h6" colspan="2">Reimburse and Medical Admin</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="email">Reimburse Admin :</label>
                                                                    <select class="form-control" name="reimburse_admin" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($user as $us)
                                                                        <option value="{{ $us->id }}" @if( $us->id == $reimburse_admin->approver ) selected @endif >{{ $us->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="password">Medical Admin :</label>
                                                                    <select class="form-control" name="medical_admin" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($user as $us)
                                                                        <option value="{{ $us->id }}" @if( $us->id == $medical_admin->approver ) selected @endif >{{ $us->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-12">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole h6" colspan="2">Emails</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="email">CC Emails :</label>
                                                                    <select class="form-control" name="email_cc" >
                                                                        <option selected disabled>Choose...</option>
                                                                        @foreach($user as $user)
                                                                        <option value="{{ $user->id }}" @if( $user->id == $cc_email->user_id ) selected @endif >{{ $user->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-danger @endrole h6" colspan="2">Submission Cutt Off Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-sm">
                                            <td>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="email">Timesheet Submission Start Date :</label>
                                                            <input type="number" class="form-control" name="ts_submit_start_date" value="{{ $cutoffDate->start_date }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="email">Timesheet Submission Closed Date :</label>
                                                            <input type="number" class="form-control" name="ts_submit_closed_date" value="{{ $cutoffDate->closed_date }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="password">Timesheet Approval Start Date :</label>
                                                            <input type="text" class="form-control" name="ts_approve_start_date" value="{{ $tsCutoffdate->start_date }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="password">Timesheet Approval Closed Date :</label>
                                                            <input type="text" class="form-control" name="ts_approve_closed_date" value="{{ $tsCutoffdate->closed_date }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="password">Leave Approval Start Date :</label>
                                                            <input type="number" class="form-control" name="leave_approve_start_date" value="{{ $leaveCutoffdate->start_date }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="password">Leave Approval Closed Date :</label>
                                                            <input type="number" class="form-control" name="leave_approve_closed_date" value="{{ $leaveCutoffdate->closed_date }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="password">Reimburse & Med Approval Start Date :</label>
                                                            <input type="number" class="form-control" name="reimburse_approve_start_date" value="{{ $reimburseCutoffdate->start_date }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="password">Reimburse & Med Approval Closed date :</label>
                                                            <input type="number" class="form-control" name="reimburse_approve_closed_date" value="{{ $reimburseCutoffdate->start_date }}">
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="comment">New Financial Password :</label>
                                                            <input type="password" class="form-control" id="password" name="password" value="">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="comment">Reconfirm Password :</label>
                                                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" value="">
                                                        </div>
                                                        <span id="passwordError" style="color: red;"></span>
                                                    </div> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button class="br-icon"></button>
                        </form>
                    </div>
                    {{-- <div class="tab-pane fade" id="page2" role="tabpanel" aria-labelledby="page2-tab">

                    </div> --}}
                    <div class="tab-pane fade" id="page3" role="tabpanel" aria-labelledby="page3-tab">
                        <form id="f_form" method="post">
                            @csrf
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="email">User Name :</label>
                                            <select class="form-control" id="user_name" name="user_name" required>
                                                @foreach($users as $employees)
                                                <option value="{{$employees->id}}">{{ $employees->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Fingerprint ID :</label>
                                            <input type="number" class="form-control" name="f_id">
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex justify-content-center align-items-end">
                                        <div class="form-group">
                                            <button type="submit" id="insert-data-fingerprint" class="btn btn-primary">Insert</button>
                                        </div>
                                    </div>
                                    <div class="col-md-12"><br>
                                        <div class="alert alert-success alert-success-saving" role="alert" style="display: none;">
                                            Your entry has been saved successfully.
                                        </div>
                                        <div class="alert alert-danger" role="alert" style="display: none;">
                                            An error occurred while saving your entry. Please try again.
                                        </div>
                                        <div class="alert alert-danger alert-success-delete" role="alert" style="display: none;">
                                            Client has been deleted successfully.
                                        </div>
                                        <div class="alert alert-danger alert-danger-delete" role="alert" style="display: none;">
                                            An error occurred while saving your entry. Please try again.
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered zoom90" width="100%" id="dataTableRoles" cellspacing="0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Fingerprint ID</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($usersFingerprint as $uf)
                                                        <tr>
                                                            <td>{{ $uf->user->name }}</td>
                                                            <td>{{ $uf->fingerprint_id }}</td>
                                                            <td class="text-center" width="50px">
                                                            <a href="/hr/compliance/integration/delete/{{$uf->id}}" onclick='isconfirm();'class="btn btn-danger btn-sm" ><i class='fas fa-fw fa-trash-alt'></i> Remove</a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
    .br-icon
    {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 100;
        height: 75px;
        width: 75px;
        border-radius: 100%;
        background-color: #1e43e9;
        box-shadow: 2px 2px 10px 1px rgba(0,0,0,0.58);
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-transform: scale(0.92);
        transform: scale(0.92);
    }
    .br-icon::before {
        content: "\f0c7";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        font-size: 28px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #fff;
    }
</style>
<script>
    function validatePassword() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirmPassword").value;
        var errorElement = document.getElementById("passwordError");

        if (password !== confirmPassword) {
            errorElement.innerHTML = "Password tidak cocok";
            return false;
        } else {
            errorElement.innerHTML = "";
            return true;
        }
    }

    $(document).ready(function () {
        $('#insert-data-fingerprint').click(function(e) {
            e.preventDefault();
            // Serialize the form data
            var formData = $('#f_form').serialize();
            // Send an AJAX request to the entries.store route
            $.ajax({
            type: 'POST',
            url: '/post-data/to/fingerprint/machine',
            data: formData,
            success: function(response) {
                $('.alert-success-saving').show();
                $('#f_form')[0].reset();
                setTimeout(function() {
                    $('.alert-success-saving').fadeOut('slow');
                    window.location.reload();
                }, 3000);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                    $('.alert-danger').show();
                    setTimeout(function() {
                        $('.alert-danger').fadeOut('slow');
                    }, 3000);
                }
            });
        });
    });
</script>
@endsection
