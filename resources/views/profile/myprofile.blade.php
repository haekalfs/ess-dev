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
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th class="m-0 font-weight-bold text-primary" colspan="2">Contact Information</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>Nama</td>
                                                    <td>: {{$user_info->name}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Hired Date</td>
                                                    <td>: {{$user_info->users_detail->hired_date}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Assignment</td>
                                                    <td>: 2 Years</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th class="m-0 font-weight-bold text-primary" colspan="2">Document Information</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>Nama</td>
                                                    <td>: {{$user_info->name}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Hired Date</td>
                                                    <td>: {{$user_info->users_detail->hired_date}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Assignment</td>
                                                    <td>: 2 Years</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th class="m-0 font-weight-bold text-primary" colspan="2">Profile Information</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>Periode :</td>
                                                    {{-- <td>: {{ date("F", mktime(0, 0, 0, $month, 1)); }} {{ $year }}</td> --}}
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Status</td>
                                                    <td>: 
                                                        {{-- @if ($lastUpdate->ts_status_id == '10')
                                                        Saved
                                                        @elseif($lastUpdate->ts_status_id == '20')
                                                        Submitted
                                                        @elseif($lastUpdate->ts_status_id == '29')
                                                        Approved
                                                        @else
                                                        Waiting for Approval
                                                        @endif --}}
                                                    </td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Updated At</td>
                                                    {{-- <td>: {{ $lastUpdate->updated_at }}</td> --}}
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th class="m-0 font-weight-bold text-primary" colspan="2">Bank Account</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="table-sm">
                                                    <td>Nama</td>
                                                    <td>: {{$user_info->name}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Hired Date</td>
                                                    <td>: {{$user_info->users_detail->hired_date}}</td>
                                                </tr>
                                                <tr class="table-sm">
                                                    <td>Assignment</td>
                                                    <td>: 2 Years</td>
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
    </div>
</div>
@endsection
