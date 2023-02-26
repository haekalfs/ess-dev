@extends('layouts.main')

@section('active-page-timesheet')
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
                    <a class="btn btn-danger btn-sm" type="button" href="{{ url()->previous() }}" id="manButton">Back</a>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                
            </div>
        </div>
    </div>
</div>
<div class="card shadow mb-4">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Timesheet Preview</h6>
        <div class="text-right">
            <a class="btn btn-secondary btn-sm" type="button" href="/timesheet/entry/preview/print/{{$year}}/{{$month}}" target="_blank" id="manButton" style="margin-right: 10px;">Download</a><input class="btn btn-primary btn-sm" type="button" id="copyButton" value="Submit">
        </div>
    </div>
    <!-- Card Body -->
    <div class="card-body">
        <div class="table-responsive zoom80">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Task</th>
                        <th>Location</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Activity</th>
                </thead>
                <tbody>
                    @foreach($timesheet as $timesheets)
                    <tr>
                        <td>{{ $timesheets->ts_date }}</td>
                        <td>{{ $timesheets->ts_task }}</td>
                        <td>{{ $timesheets->ts_location }}</td>
                        <td>{{ $timesheets->ts_from_time }}</td>
                        <td>{{ $timesheets->ts_to_time }}</td>
                        <td>{{ $timesheets->ts_activity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Approval History</h6>
                {{-- <div class="text-right">
                    <input class="btn btn-primary btn-sm" type="button" id="copyButton" value="Reset">
                </div> --}}
            </div>
            <!-- Card Body -->
            <div class="card-body">
                
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Mandays</h6>
                {{-- <div class="text-right">
                    <input class="btn btn-primary btn-sm" type="button" id="copyButton" value="Reset">
                </div> --}}
            </div>
            <!-- Card Body -->
            <div class="card-body">
                
            </div>
        </div>
    </div>
</div>
@endsection
