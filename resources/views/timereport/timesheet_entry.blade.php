@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Timesheet Entry</h1>
<p class="mb-4">Timesheet Entry for {{ date("F", mktime(0, 0, 0, $entry, 1)) }}</a>.</p>
<form method="POST" action="{{ route('save_activities') }}">
    @csrf
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary" id="judul">Timesheet Entry</h6>
            <div class="text-right">
                <button type="submit" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-save fa-sm text-white-50"></i> Save Activities</button>
            </div>
        </div>
        <div class="card-body">
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
                                <td>{{ $date->format('d-m-Y') }}</td>
                                <td><input class="form-control" type="time" name="activities[{{ $date->format('Y-m-d') }}][from]" value="{{ $savedActivities->firstWhere('ts_date', $date->format('Y-m-d'))->ts_from_time ?? '' }}" required></td>
                                <td><input class="form-control" type="time" name="activities[{{ $date->format('Y-m-d') }}][to]" value="{{ $savedActivities->firstWhere('ts_date', $date->format('Y-m-d'))->ts_to_time ?? '' }}" required></td>
                                <td><textarea class="form-control" rows="2" name="activities[{{ $date->format('Y-m-d') }}][activity]" rows="1" required>{{ $savedActivities->firstWhere('ts_date', $date->format('Y-m-d'))->ts_activity ?? '' }}</textarea></td>
                                <!-- Add more activity columns as needed -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
@endsection