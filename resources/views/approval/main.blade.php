@extends('layouts.main')

@section('active-page-approval')
active
@endsection

@section('content')
{{-- <!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Approval</h1>
<p class="mb-4">Approval Page.</p> --}}
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

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2"><a href="/approval/director">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Timesheet Approval</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div></a>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2"><a href="#">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Medical Approval</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div></a>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hand-holding-medical fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2"><a href="#">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Reimburse Approval</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div></a>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2"><a href="#">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Leave Approval</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div><a>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-plane-departure fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Approval History</h6>
        <div class="text-right">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="dataTable1" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Activity</th>
                        <th>Periode</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workflows as $workflow)
                    <tr>
                        @if ($workflow)
                        <td>{{ $workflow->user_id }}</td>
                        <td>{{ $workflow->date_submitted }}</td>
                        <td>{{ date("F", mktime(0, 0, 0, substr($workflow->month_periode, 4, 2), 1)) }} - {{ substr($workflow->month_periode, 0, 4) }}</td>
                        <td>{{ $workflow->updated_at }}</td>
                        @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="action"></td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> --}}
<style>
.action{
    width: 190px;
}
</style>
@endsection
