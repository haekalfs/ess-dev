@extends('layouts.main')

@section('title', 'Approval - ESS')

@section('active-page-approval')
active
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 zoom90 text-gray-800 font-weight-bold"><i class="fas fa-calendar"></i> Approvals & Submission</h1>
<p class="mb-4">This section displays the approvals and submissions for various requests.</p>

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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reimbCount }}</div>
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

    <div class="col-xl-3 col-md-6 mb-4 approval_po">
        <div class="card border-left-po shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-green-tea text-uppercase mb-1">
                            Purchase Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4 approval_pr">
        <div class="card border-left-pr shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-green-donker text-uppercase mb-1">
                            Purchase Requsitions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4 approval_rn">
        <div class="card border-left-rn shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-orange text-uppercase mb-1">
                            Receivable Notes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
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
    const approval_po = document.querySelector('.approval_po');
    const approval_pr = document.querySelector('.approval_pr');
    const approval_rn = document.querySelector('.approval_rn');

    approval_po.addEventListener('click', function() {
    window.location.href = 'https://e-form.perdana.co.id/';
    });
    approval_pr.addEventListener('click', function() {
    window.location.href = 'https://e-form.perdana.co.id/';
    });
    approval_rn.addEventListener('click', function() {
    window.location.href = 'https://e-form.perdana.co.id/';
    });

    cardP_assignment.addEventListener('click', function() {
    window.location.href = '/approval/project/assignment/';
    });

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


    approval_po.addEventListener('mouseover', function() {
    approval_po.style.cursor = 'pointer';
    });
    approval_po.addEventListener('mouseout', function() {
    approval_po.style.cursor = 'default';
    });
    approval_pr.addEventListener('mouseover', function() {
    approval_pr.style.cursor = 'pointer';
    });
    approval_pr.addEventListener('mouseout', function() {
    approval_pr.style.cursor = 'default';
    });
    approval_rn.addEventListener('mouseover', function() {
    approval_rn.style.cursor = 'pointer';
    });
    approval_rn.addEventListener('mouseout', function() {
        approval_rn.style.cursor = 'default';
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
