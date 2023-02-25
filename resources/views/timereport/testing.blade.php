@extends('layouts.main')

@section('content')
<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css' rel='stylesheet' />
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js'></script>

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Timesheet Entry</h1>

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
<br>
<table class="table calendar">
    <thead>
      <tr>
        <th scope="col">Sun</th>
        <th scope="col">Mon</th>
        <th scope="col">Tue</th>
        <th scope="col">Wed</th>
        <th scope="col">Thu</th>
        <th scope="col">Fri</th>
        <th scope="col">Sat</th>
      </tr>
    </thead>
    <tbody>
        <tr>
            @foreach($calendar as $day)
                @if($loop->iteration % 7 == 1)
                    </tr><tr>
                @endif
                <td data-toggle="modal" data-target="#myModal">
                    @if($day['holiday'] != "bukan tanggal merah")
                    <div><a style="color: red;">{{ $day['date'] }}</a></div>
                    @else
                    <div>{{ $day['date'] }}</div>
                    @endif
                </td>
            @endforeach
        </tr>
    </tbody>
  </table>
  <!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">Modal title</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          Modal content...
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>
  <style>
    .calendar {
  background: #ffffff;
  border-radius: 4px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, .3);
  height: 501px;
  perspective: 1000;
  transition: .9s;
  transform-style: preserve-3d;
  width: 100%;
}
</style>
{{-- <form method="POST" action="{{ route('save_activities') }}">
    @csrf
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary" id="judul">Timesheet Entry</h6>
            <div class="text-right">
                <button type="submit" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-save fa-sm text-white-50"></i> Save Activities</button>
            </div>
        </div>
        <div class="card-body zoom80">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Activity</th>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Date</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Activity</th>
                        </tr>
                    </tfoot>
                    <tbody>                 
                        @foreach ($dates as $date)
                            <tr>
                                <td>{{ $date->format('Y-m-d') }}</td>
                                <td><input class="form-control" type="time" name="activities[{{ $date->format('Y-m-d') }}][from]" value="{{ $savedActivities->firstWhere('ts_date', $date->format('Y-m-d'))->ts_from_time ?? '' }}" ></td>
                                <td><input class="form-control" type="time" name="activities[{{ $date->format('Y-m-d') }}][to]" value="{{ $savedActivities->firstWhere('ts_date', $date->format('Y-m-d'))->ts_to_time ?? '' }}" ></td>
                                <td><textarea class="form-control" rows="2" name="activities[{{ $date->format('Y-m-d') }}][activity]" rows="1" >{{ $savedActivities->firstWhere('ts_date', $date->format('Y-m-d'))->ts_activity ?? '' }}</textarea></td>
                                <!-- Add more activity columns as needed -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form> --}}
@endsection