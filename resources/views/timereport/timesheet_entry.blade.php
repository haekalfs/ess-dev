@extends('layouts.main')

@section('active-page-timesheet')
active
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Timesheet Entry</h1>
<p class="mb-4">Timesheet Entry for {{ date("F", mktime(0, 0, 0, $month, 1)) }} - {{$year}}</a>. <small style="color: red;"><u><i>This app is still under development. You may find fault on inputs</i></u></small></p>
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

<div class="alert alert-success" role="alert" style="display: none;">
    Your entry has been saved successfully.
  </div>
  
  <div class="alert alert-danger" role="alert" style="display: none;">
    An error occurred while saving your entry. Please try again.
  </div>
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Calendar</h6>
                {{-- <div class="text-right">
                    <input class="btn btn-primary btn-sm" type="button" id="copyButton" value="Reset">
                </div> --}}
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table zoom80 table-bordered calendar">
                    <colgroup>
                        @foreach ($calendar[0] as $dayName)
                          <col style="width: {{ 100 / count($calendar[0]) }}%;">
                        @endforeach
                      </colgroup>
                    <thead class="thead-dark">
                        @foreach ($calendar[0] as $dayName)
                            <th>{{ $dayName }}</th>
                        @endforeach
                    </thead>
                    <tbody>
                        @foreach (array_slice($calendar, 1) as $week)
                            <tr>
                                @foreach ($week as $day)
                                    @if ($day !== '' && date('N', strtotime($year.'-'.$month.'-'.$day)) == 7)
                                        <td style="color: red">{{ $day }}</td>
                                    @else
                                    <td data-toggle="modal" class="clickable" data-target="#myModal" data-date="{{ $year }}-{{ $month }}-{{ $day }}">{{ $day }}</td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-6">
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Allowances Calculation</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <table class="zoom80">
                            {{-- <thead>
                                <tr class="calculations">
                                </tr>
                            </thead> --}}
                            <tbody class="calculations">
                            </tbody>
                        </table><small class="text-danger zoom80"><u><i>For exact calculations, request payslip from Finances Department.</i></u></small>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Projects Assignment</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <table class="zoom80">
                            {{-- <thead>
                                <tr class="calculations">
                                </tr>
                            </thead> --}}
                            <tbody>
                            </tbody>
                        </table><small class="text-danger zoom80"><u><i>For exact calculations, request payslip from Finances Department.</i></u></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow mb-4">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Activity Entries</h6>
        <div class="text-right">
            <a class="btn btn-secondary btn-sm" type="button" href="{{ $previewButton }}" id="manButton">Preview</a>
        </div>
    </div>
    <!-- Card Body -->
    <div class="card-body">
        <div class="table-responsive zoom80">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 10px;">Day</th>
                        <th>Date</th>
                        <th>Task</th>
                        <th>Location</th>
                        <th style="width: 600px;">Activity</th>
                        <th>From</th>
                        <th>To</th>
                        <th style="width: 10px;">Action</th>
                </thead>
                <tfoot>
                    <tr>
                        <th style="width: 10px;">Day</th>
                        <th>Date</th>
                        <th>Task</th>
                        <th>Location</th>
                        <th style="width: 600px;">Activity</th>
                        <th>From</th>
                        <th>To</th>
                        <th style="width: 10px;">Action</th>
                    </tr>
                </tfoot>
                <tbody id="activity-table">
                    <!-- Display the data fetched via AJAX here -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<input type="hidden" id="yearSel" value="{{ $year }}">
<input type="hidden" id="monthSel" value="{{ $month }}">

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Entry <a id="selected-date-display"></a></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="entry-form">
                @csrf
				<div class="modal-body" style="">
                    <input type="hidden" id="clickedDate" name="clickedDate">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Task :</label>
                                    <select class="form-control" id="task" name="task">
                                        <option>HO</option>
                                        <option>Project (JOB Tomori, PLN, LPS, Vale, etc...)</option>
                                        <option>Sick</option>
                                        <option>Other</option>
                                        <option>Standby</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Location :</label>
                                    <select class="form-control" id="location" name="location">
                                        <option value="Dalam Kota">Dalam Kota</option>
                                        <option value="Luar Kota">Luar Kota</option>
                                        <option value="HO">Head Office</option>
                                        <option value="Outer Ring">Outer Ring (Bogor, Depok, Tangerang, Bekasi)</option>
                                        <option value="WFH">WFH/WFA (Work From Home/Anywhere)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">From :</label>
                                    <input type="time" class="form-control" step="60" min="00:00" max="23:59" required pattern="[0-9]{2}:[0-9]{2}" placeholder="HH:mm" autocomplete="off" name="from" id="start-time">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">To :</label>
                                    <input type="time" class="form-control" step="60" min="00:00" max="23:59" required pattern="[0-9]{2}:[0-9]{2}" placeholder="HH:mm" autocomplete="off" name="to" id="end-time">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Activity :</label>
                                    <textarea type="text" class="form-control" id="activity" name="activity"></textarea>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="save-entry" class="btn btn-primary" data-dismiss="modal">Save changes</button>
                  </div>
			</form>
		</div>
	</div>
</div>
<style>
    td {
        cursor: pointer;
        transition: background-color 0.3s ease-in-out;
    }
    td:hover {
        background-color: #f5f5f5;
    }
    td:active {
        background-color: #e6e6e6;
    }
    .calendar {
        background: #ffffff;
        border-radius: 4px;
        height: 501px;
        perspective: 1000;
        transition: .9s;
        transform-style: preserve-3d;
        width: 100%;
    }
</style>
@endsection