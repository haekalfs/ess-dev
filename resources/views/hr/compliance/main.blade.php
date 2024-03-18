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
            <div class="card-header d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary mt-2 mb-2">System Configuration</h6>
            </div>
            <ul class="nav nav-tabs" id="pageTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="page1-tab" data-toggle="tab" href="#page1" role="tab" aria-controls="page1" aria-selected="true"><i class="fas fa-calendar-day"></i> Approval & Restriction</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="page2-tab" data-toggle="tab" href="#page2" role="tab" aria-controls="page2" aria-selected="false"><i class="fas fa-plane-departure"></i> Submission</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="page3-tab" data-toggle="tab" href="#page3" role="tab" aria-controls="page3" aria-selected="false"><i class="fas fa-fingerprint" style="color: #ff0000;"></i> Integration</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="page4-tab" data-toggle="tab" href="#page4" role="tab" aria-controls="page4" aria-selected="false"><i class="fas fa-fingerprint" style="color: #ffff06;"></i> Integration Manual</a>
                </li>
            </ul>
            <div class="card-body">
                <div class="tab-content" id="pageTabContent">
                    <div class="tab-pane fade show active" id="page1" role="tabpanel" aria-labelledby="page1-tab">
                        <div class="col-md-12">
                            <h6 class="h5 m-0 font-weight-bold text-secondary mt-2 mb-4"><i class="fas fa-users-cog"></i> Manage Approvers</h6><hr class="sidebar-divider">
                            <form action="/system-management/add-new-approver" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="email">User Name :</label>
                                            <select class="form-control" name="user_name" required>
                                                <option selected disabled>Select...</option>
                                                @foreach($users as $employees)
                                                <option value="{{$employees->id}}">{{ $employees->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="password">Department :</label>
                                            <select class="form-control" name="department" required>
                                                <option selected disabled>Select...</option>
                                                @foreach($department as $dept)
                                                <option value="{{$dept->id}}">{{ $dept->department_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="password">Set As :</label>
                                            <select class="form-control" name="setAs" required>
                                                <option selected disabled>Select...</option>
                                                <option value="2">Reviewer</option>
                                                <option value="1">Director Authorization</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex justify-content-center align-items-end">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Insert</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <form action="/hr/compliance/update/regulations" method="POST">
                            @method('PUT')
                            @csrf
                            <div class="col-md-12">
                                <div class="row">
                                    @php $no = 1 @endphp
                                    @foreach($department as $dp)
                                    <div class="col-md-6 mt-4">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-secondary @endrole h6" colspan="2">{{ $no++ }}. {{ $dp->department_name }} Dept.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>
                                                        <div class="row">
                                                            @foreach($dp->approvers as $dpa)
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    @if($dpa->group_id == 1)
                                                                    <label for="email">Director Authorization :</label>
                                                                    @else
                                                                    <label for="email">Initial Reviewer :</label>
                                                                    @endif
                                                                    <div class="input-group"> <!-- Wrap select and button in an input-group -->
                                                                        <select class="form-control" name="approvers{{$dpa->id}}">
                                                                            <option selected disabled>Choose...</option>
                                                                            @foreach($user as $us)
                                                                            <option value="{{ $us->id }}" @if( $us->id == $dpa->approver ) selected @endif>{{ $us->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <div class="input-group-append">
                                                                            <a class="btn btn-danger" href="{{ route('remove.approver', ['id' => $dpa->id]) }}" onclick='isconfirm();'><i class="fas fa-trash-alt"></i></a> <!-- Add the button here -->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @endforeach
                                    <div class="col-md-5">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-secondary @endrole h6" colspan="2">Default Approvers<hr class="sidebar-divider"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="email">Initial Reviewer :</label>
                                                                    <div class="input-group"> <!-- Wrap select and button in an input-group -->
                                                                        <select class="form-control" name="Default_FA" >
                                                                            <option selected disabled>Choose...</option>
                                                                            @foreach($user as $us)
                                                                            <option value="{{ $us->id }}" @if( $us->id == $Default1->approver ) selected @endif >{{ $us->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="email">Director Authorization :</label>
                                                                    <div class="input-group"> <!-- Wrap select and button in an input-group -->
                                                                        <select class="form-control" name="Default_PA" >
                                                                            <option selected disabled>Choose...</option>
                                                                            @foreach($user as $us)
                                                                            <option value="{{ $us->id }}" @if( $us->id == $Default2->approver ) selected @endif >{{ $us->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-7 mt-4">
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <span class="text-danger">Revision Authorization Protocol</span>
                                            </div>
                                            <div class="card-body" style="background-color: rgb(247, 247, 247);">
                                                <h6 class="h6 mb-2 font-weight-bold text-gray-800">General Guidelines</h6>
                                                <ul>
                                                    <li><strong>Department Approval Responsibility:</strong> Each department is responsible for reviewing and approving any edits made within their respective areas.</li>
                                                    <li><strong>Adherence to Policies:</strong> All employees must adhere to the company's editing approval policies and procedures as outlined by their department.</li>
                                                    <li><strong>Accuracy and Accountability:</strong> Employees are required to ensure that any edits made are accurate and comply with departmental standards. Unauthorized adjustments to records are strictly prohibited.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-4 mt-4">
                                        <span class="m-0 font-weight-bold text-danger h6" colspan="2">Additional Configuration & Restriction</span>
                                        <hr class="sidebar-divider">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Export Timesheet :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="form-control" name="export_ts" >
                                                    <option selected disabled>Choose...</option>
                                                    @foreach($position as $pos)
                                                        <option value="{{ $pos->id }}" @if( $pos->id == $export_ts->position_id ) selected @endif >{{ $pos->position_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Export Reimbursement :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="form-control" name="export_reimburse" >
                                                    <option selected disabled>Choose...</option>
                                                    @foreach($position as $pos)
                                                    <option value="{{ $pos->id }}" @if( $pos->id == $export_reimburse->position_id ) selected @endif >{{ $pos->position_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;">
                                                <p style="margin: 0;">Always CC To :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="form-control" name="email_cc" >
                                                    <option selected disabled>Choose...</option>
                                                    @foreach($user as $ccTo)
                                                    <option value="{{ $ccTo->id }}" @if( $ccTo->id == $cc_email->user_id ) selected @endif >{{ $ccTo->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Medical Admin :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="form-control" name="medical_admin" >
                                                    <option selected disabled>Choose...</option>
                                                    @foreach($user as $us)
                                                    <option value="{{ $us->id }}" @if( $us->id == $medical_admin->approver ) selected @endif >{{ $us->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-4">
                                            <div style="width: 140px;" class="mr-2">
                                                <p style="margin: 0;">Reimbursement Admin :</p>
                                            </div>
                                            <div class="flex-grow-1">
                                                <select class="form-control" name="reimburse_admin" >
                                                    <option selected disabled>Choose...</option>
                                                    @foreach($user as $us)
                                                    <option value="{{ $us->id }}" @if( $us->id == $reimburse_admin->approver ) selected @endif >{{ $us->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <span class="text-danger">Role-Based Access Control Administration</span>
                                            </div>
                                            <div class="card-body" style="background-color: rgb(247, 247, 247);">
                                                <h6 class="h6 mb-2 font-weight-bold text-gray-800">Guidelines for Managing Roles and Permissions</h6>
                                                <p>Our Role-Based Access Control (RBAC) system enables administrators to effectively manage user roles and permissions within our system. Below are the key guidelines for administering roles and permissions:</p>
                                                <ul>
                                                    <li><strong>Role Assignment:</strong> Assign appropriate roles to users based on their responsibilities and access needs.</li>
                                                    <li><strong>Permission Configuration:</strong> Configure permissions for each role to restrict or grant access to specific features or data.</li>
                                                    <li><strong>Access Review:</strong> Regularly review and update role assignments and permissions to ensure compliance with organizational policies.</li>
                                                    <li><strong>Audit Trails:</strong> Maintain audit trails to track changes to role assignments and permissions for accountability and security purposes.</li>
                                                    <li><strong>Training and Documentation:</strong> Provide training and documentation to administrators and users on role management best practices and system usage.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="br-icon"></button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="page2" role="tabpanel" aria-labelledby="page2-tab">
                        <form action="/hr/compliance/update/cutoff-date" method="POST">
                            @method('PUT')
                            @csrf
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            @php
                                                $classes = ['text-danger', 'text-primary', 'text-secondary', 'text-success'];
                                            @endphp

                                            @foreach($cutoffDate as $key => $cd)
                                                @php
                                                    // Get the class for this iteration
                                                    $class = $classes[$key % count($classes)];
                                                @endphp

                                                <div class="col-md-6">
                                                    <div class="mb-4 mt-2">
                                                        <span class="font-weight-bold {{ $class }} h6" colspan="2">{{ $cd->name }}</span>
                                                        <hr class="sidebar-divider mb-4">
                                                    </div>
                                                    <div class="d-flex align-items-center mb-4">
                                                        <div style="width: 140px;" class="mr-2">
                                                            <p style="margin: 0;">Start Date :</p>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <input type="number" class="form-control" name="cutoff_dates[{{ $cd->id }}][start_date]" value="{{ $cd->start_date }}">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-4">
                                                        <div style="width: 140px;" class="mr-2">
                                                            <p style="margin: 0;">End Date :</p>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <input type="number" class="form-control" name="cutoff_dates[{{ $cd->id }}][closed_date]" value="{{ $cd->closed_date }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <span class="text-danger">Submission Deadline Management</span>
                                            </div>
                                            <div class="card-body" style="background-color: rgb(247, 247, 247);">
                                                <h6 class="h6 mb-2 font-weight-bold text-gray-800">Guidelines for Managing Submission Deadlines</h6>
                                                <p>Our submission deadline management system ensures strict adherence to cutoff dates and imposes necessary restrictions. Below are the guidelines for effectively managing submission deadlines:</p>
                                                <ul>
                                                    <li><strong>Submission Policies:</strong> Establish clear policies regarding submission deadlines, including cutoff dates and time restrictions.</li>
                                                    <li><strong>Authorization Levels:</strong> Assign authorization levels to users to restrict or grant access to submission functions based on their roles and responsibilities.</li>
                                                    <li><strong>Automated Alerts:</strong> Implement automated alerts and notifications to remind users of approaching submission deadlines and cutoff times.</li>
                                                    <li><strong>Enforcement Measures:</strong> Enforce strict adherence to submission deadlines through system-imposed restrictions and penalties for late submissions.</li>
                                                    <li><strong>Review and Monitoring:</strong> Regularly review and monitor submission activities to ensure compliance with established deadlines and identify any potential issues.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="br-icon"></button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="page3" role="tabpanel" aria-labelledby="page3-tab">
                        <form id="f_form" method="post">
                            @csrf
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="email">User Name :</label>
                                                    <select class="form-control" id="user_name" name="user_name" required>
                                                        <option selected disabled>Select...</option>
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
                                                <div class="alert alert-danger alert-danger-delete" role="alert" style="display: none;">
                                                    An error occurred while saving your entry. Please try again.
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered zoom90" width="100%" id="dataTableAttendance" cellspacing="0">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Name</th>
                                                                <th>Fingerprint ID</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>@php $no = 1 @endphp
                                                            @foreach ($usersFingerprint as $uf)
                                                                <tr>
                                                                    <td style="width: 10%;">{{ $no++ }}</td>
                                                                    <td>{{ $uf->user->name }}</td>
                                                                    <td style="width: 20%;">{{ $uf->fingerprint_id }}</td>
                                                                    <td class="text-center" style="width: 20%;">
                                                                    <a href="/hr/compliance/integration/delete/{{$uf->id}}" onclick='isconfirm();' class="btn btn-danger btn-sm" ><i class='fas fa-fw fa-trash-alt'></i> Remove</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <span class="text-danger">Attendance Machine Policy</span>
                                            </div>
                                            <div class="card-body" style="background-color: rgb(247, 247, 247);">
                                                <h6 class="h6 mb-2 font-weight-bold text-gray-800">General Guidelines</h6>
                                                <ul>
                                                    <li>All employees must adhere to the company's attendance machine policy.</li>
                                                    <li>Employees are required to use the attendance machine to record their daily working hours accurately.</li>
                                                    <li>Unauthorized adjustments to attendance records are strictly prohibited.</li>
                                                </ul>

                                                <h6 class="h6 mb-2 font-weight-bold text-gray-800">Submission Process</h6>
                                                <ol>
                                                    <li>Ensure all attendance records are correctly captured by the attendance machine.</li>
                                                    <li>Report any discrepancies or issues with the attendance machine to the HR department immediately.</li>
                                                    <li>Attendance records must be submitted to the HR department for verification and approval.</li>
                                                    <li class="text-danger">Any discrepancies found in attendance records may result in disciplinary actions.</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="page4" role="tabpanel" aria-labelledby="page4-tab">
                        <form method="post" action="{{ route('import.checkinout') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-11">
                                                <div class="form-group">
                                                    <label for="receipt">CSV File :</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="file" name="file" aria-describedby="inputreceipt" onchange="displayFileName()">
                                                        <label class="custom-file-label" for="file" id="file-label">Choose file</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 d-flex justify-content-center align-items-end">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary">Insert</button>
                                                </div>
                                            </div>
                                            <div class="col-md-12"><br>
                                                <div class="alert alert-success alert-success-saving" role="alert" style="display: none;">
                                                    Your entry has been saved successfully.
                                                </div>
                                                <div class="alert alert-danger alert-danger-delete" role="alert" style="display: none;">
                                                    An error occurred while saving your entry. Please try again.
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered zoom90" width="100%" id="dataTable" cellspacing="0">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Name</th>
                                                                <th>Fingerprint ID</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>@php $no = 1 @endphp
                                                            @foreach ($usersFingerprint as $uf)
                                                                <tr>
                                                                    <td style="width: 10%;">{{ $no++ }}</td>
                                                                    <td>{{ $uf->user->name }}</td>
                                                                    <td style="width: 20%;">{{ $uf->fingerprint_id }}</td>
                                                                    <td class="text-center" style="width: 20%;">
                                                                    <a href="/hr/compliance/integration/delete/{{$uf->id}}" onclick='isconfirm();' class="btn btn-danger btn-sm" ><i class='fas fa-fw fa-trash-alt'></i> Remove</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <span class="text-danger">Attendance Machine Policy</span>
                                            </div>
                                            <div class="card-body" style="background-color: rgb(247, 247, 247);">
                                                <h6 class="h6 mb-2 font-weight-bold text-gray-800">General Guidelines</h6>
                                                <ul>
                                                    <li>All employees must adhere to the company's attendance machine policy.</li>
                                                    <li>Employees are required to use the attendance machine to record their daily working hours accurately.</li>
                                                    <li>Unauthorized adjustments to attendance records are strictly prohibited.</li>
                                                </ul>

                                                <h6 class="h6 mb-2 font-weight-bold text-gray-800">Submission Process</h6>
                                                <ol>
                                                    <li>Ensure all attendance records are correctly captured by the attendance machine.</li>
                                                    <li>Report any discrepancies or issues with the attendance machine to the HR department immediately.</li>
                                                    <li>Attendance records must be submitted to the HR department for verification and approval.</li>
                                                    <li class="text-danger">Any discrepancies found in attendance records may result in disciplinary actions.</li>
                                                </ol>
                                            </div>
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

    function displayFileName() {
        const fileInput = document.getElementById("file");
        const fileName = fileInput.files[0].name;
        const label = document.getElementById("file-label");
        label.innerText = fileName;
    }
</script>
@endsection
