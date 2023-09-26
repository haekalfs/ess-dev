@extends('layouts.main')

@section('title', 'Approval - ESS')

@section('active-page-approval')
active
@endsection

@section('content')
{{-- <h1 class="h3 mb-2 text-center text-gray-800">Approval Page</h1><br> --}}
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
    <div class="col-xl-3 col-md-6 mb-4 timesheet">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Timesheet Approval</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tsCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4 medical">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Medical Approval</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $medCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hand-holding-medical fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4 reimburse">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Reimburse Approval</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4 leave">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Leave Approval</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $leaveCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-plane-departure fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4 p_assignment">
        <div class="card border-left-project_a shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-cyan text-uppercase mb-1">
                            Project Assignment</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-network-wired fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4 p_assign_mem">
        <div class="card border-left-project_b shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-pink text-uppercase mb-1">
                            Project Member</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const cardTimesheet = document.querySelector('.timesheet');
    const cardLeave = document.querySelector('.leave');
    const cardMed = document.querySelector('.medical');
    const cardReimburse = document.querySelector('.reimburse');
    const cardP_assignment = document.querySelector('.p_assignment');
    const cardP_assign_mem = document.querySelector('.p_assign_mem');

    cardTimesheet.addEventListener('click', function() {
    window.location.href = '/approval/timesheet/p';
    });

    cardLeave.addEventListener('click', function() {
    window.location.href = '/approval/leave/';
    });
    
    cardMed.addEventListener('click', function() {
    window.location.href = '/approval/medical/';
    });

    cardReimburse.addEventListener('click', function() {
    window.location.href = '/approval/reimburse/';
    });

    cardP_assign_mem.addEventListener('click', function() {
    window.location.href = '/approval/reimburse/';
    });

    cardP_assignment.addEventListener('click', function() {
    window.location.href = '/approval/project/assignment/';
    });

    cardTimesheet.addEventListener('mouseover', function() {
    cardTimesheet.style.cursor = 'pointer';
    });
    cardTimesheet.addEventListener('mouseout', function() {
    cardTimesheet.style.cursor = 'default';
    });
    cardLeave.addEventListener('mouseover', function() {
    cardLeave.style.cursor = 'pointer';
    });
    cardLeave.addEventListener('mouseout', function() {
    cardLeave.style.cursor = 'default';
    });
    cardMed.addEventListener('mouseover', function() {
    cardMed.style.cursor = 'pointer';
    });
    cardMed.addEventListener('mouseout', function() {
    cardMed.style.cursor = 'default';
    });
    cardReimburse.addEventListener('mouseover', function() {
    cardReimburse.style.cursor = 'pointer';
    });
    cardReimburse.addEventListener('mouseout', function() {
    cardReimburse.style.cursor = 'default';
    });

    cardP_assign_mem.addEventListener('mouseover', function() {
    cardP_assign_mem.style.cursor = 'pointer';
    });
    cardP_assign_mem.addEventListener('mouseout', function() {
    cardP_assign_mem.style.cursor = 'default';
    });

    cardP_assignment.addEventListener('mouseover', function() {
    cardP_assignment.style.cursor = 'pointer';
    });
    cardP_assignment.addEventListener('mouseout', function() {
    cardP_assignment.style.cursor = 'default';
    });
</script>
<style>
.action{
    width: 190px;
}
</style>
@endsection
