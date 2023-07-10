@extends('layouts.main')

@section('title', 'Compliance - ESS')

@section('active-page-HR')
active
@endsection

@section('content')
<form method="POST" action="/hr/compliance/update/regulations">
    <!-- Page Heading -->
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-2 text-gray-800">Compliance Settings</h1>
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
    <div class="row">
        <!-- Area Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Cutoff Date</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Timesheet Submission :</label>
                                    <input type="number" class="form-control" name="date_prepared" value="{{ $cutoffDate->closed_date }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Timesheet Approval :</label>
                                    <input type="text" class="form-control" name="date_prepared" value="{{ $tsCutoffdate->closed_date }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Leave Approval :</label>
                                    <input type="number" class="form-control" name="date_prepared" value="{{ $leaveCutoffdate->closed_date }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Reimburse & Med Approval :</label>
                                    <input type="number" class="form-control" name="date_prepared" value="{{ $reimburseCutoffdate->closed_date }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Cutoff Date</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="comment">Financial Password :</label>
                                    <input type="password" class="form-control" name="date_prepared" value="{{ $financialPass }}">
                                </div>
                                <div class="form-group">
                                    <label for="comment">Reconfirm Password :</label>
                                    <input type="password" class="form-control" name="date_prepared" value="{{ $financialPass }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Approvers</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
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
                                                            <input type="number" class="form-control" name="date_prepared">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="password">Prior Approver :</label>
                                                            <input type="number" class="form-control" name="date_prepared">
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
                                                            <input type="number" class="form-control" name="date_prepared">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="password">Prior Approver :</label>
                                                            <input type="number" class="form-control" name="date_prepared">
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
                                                            <input type="number" class="form-control" name="date_prepared">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="password">Prior Approver :</label>
                                                            <input type="number" class="form-control" name="date_prepared">
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
                                                            <input type="number" class="form-control" name="date_prepared">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="password">Prior Approver :</label>
                                                            <input type="number" class="form-control" name="date_prepared">
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
                                                            <input type="number" class="form-control" name="date_prepared">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="password">Prior Approver :</label>
                                                            <input type="number" class="form-control" name="date_prepared">
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
                </div>
            </div>
        </div>
    </div>
    <button class="br-icon"></button>
</form>
<style type="text/css">
    body
    {
        background-color: #d4d2d2;
    }
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
@endsection
