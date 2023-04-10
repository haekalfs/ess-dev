@extends('layouts.main')

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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
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
{{-- 
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Recently Activity</h6>
        <div class="text-right">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" width="100%" cellspacing="0">
                <thead class="thead-dark">
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
                        <td>{{ $workflow->activity }}</td>
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
</div>
<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
        role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Approval History</h6>
    </a>
    <!-- Card Content - Collapse -->
    <div class="collapse" id="collapseCardExample">
        <div class="card-body">
            <div class="row">

                <!-- Earnings (Monthly) Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-dark shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2"><a href="/approval/director">
                                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                        Timesheet History</div>
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
                    <div class="card border-left-dark shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2"><a href="#">
                                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                        Medical History</div>
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
                    <div class="card border-left-dark shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2"><a href="#">
                                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                        Reimburse History</div>
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
                <div class="col-xl-3 col-md-6 mb-4 testtt">
                    <div class="card border-left-dark shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                        Leave History</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-plane-departure fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}
<script>
    const card = document.querySelector('.timesheet');

    card.addEventListener('click', function() {
    window.location.href = '/approval/timesheet/p';
    });
    card.addEventListener('mouseover', function() {
    card.style.cursor = 'pointer';
    });

    card.addEventListener('mouseout', function() {
    card.style.cursor = 'default';
    });
</script>
<style>
.action{
    width: 190px;
}
</style>
@endsection
