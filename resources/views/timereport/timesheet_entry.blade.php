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
<div class="alert bg-success text-white shadow alert-success-delete" role="alert" style="display: none;">
    Your entry has been deleted.
</div>

<div class="alert bg-danger text-white shadow alert-danger-delete" role="alert" style="display: none;">
    An error occurred while deleting your entry. Please try again.
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
                                    @if ($day !== '' && date('n', strtotime($year.'-'.$month.'-'.$day)) == $month)
                                        @if (date('j', strtotime($year.'-'.$month.'-'.$day)) == 1)
                                            @php
                                                $prevMonth = date('n', strtotime($year.'-'.$month.'-'.$day.' -1 day'));
                                            @endphp
                                        @endif
                                        @if (date('N', strtotime($year.'-'.$month.'-'.$day)) == 6 || date('N', strtotime($year.'-'.$month.'-'.$day)) == 7)
                                            <td data-toggle="modal" class="clickable text-danger" data-target="#myModal" data-date="{{ $year }}-{{ $month }}-{{ $day }}" id="task_entry{{ $day }}">{{ $day }}</td>
                                        @else
                                            <td data-toggle="modal" class="clickable text-dark" data-target="#myModal" data-date="{{ $year }}-{{ $month }}-{{ $day }}" id="task_entry{{ $day }}">{{ $day }}</td>
                                        @endif
                                    @else
                                        <td class="prev-month-day">&nbsp;</td>
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
                        @foreach($assignment as $assign)
                        <li class="zoom90" style="font-size: 12px;">{{ $assign->project_name}}</li>
                        @endforeach
                        {{-- <small class="text-danger zoom80"><u><i>If there's any misassignment, please report to Project Admin.</i></u></small> --}}
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
            <a class="btn btn-primary btn-sm" type="button"  data-toggle="modal" data-target="#addModal" id="addButton">+ Add Entries</a> <a class="btn btn-secondary btn-sm" type="button" href="{{ $previewButton }}" id="manButton">Preview</a>
        </div>
    </div>
    <!-- Card Body -->
    <div class="card-body">
        <div class="table-responsive zoom80">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead class="thead-light">
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
                <tbody id="activity-table">
                    <!-- Display the data fetched via AJAX here -->
                </tbody>
            </table>
        </div>
        <div class="text-right zoom90">
            <a class="btn btn-danger btn-sm" type="button" href="" id="">Reset All</a>
        </div>
    </div>
</div>
<input type="hidden" id="yearSel" value="{{ $year }}">
<input type="hidden" id="monthSel" value="{{ $month }}">

<script></script>
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
                                    <select class="form-control" id="task" name="task" required>
                                        <option value="HO">HO</option>
                                        <option value="Sick">Sick</option>
                                        <option value="Other">Other</option>
                                        <option>Standby</option>
                                        <optgroup label="Projects">
                                            @foreach($assignment as $assign)
                                            <option value="{{$assign->project_name}}">{{ $assign->project_name}}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Location :</label>
                                    <select class="form-control" id="location" name="location" required>
                                        <option value="DK">Dalam Kota</option>
                                        <option value="LK">Luar Kota</option>
                                        <option value="HO">Head Office</option>
                                        <option value="Outer Ring">Outer Ring (Bogor, Depok, Tangerang, Bekasi)</option>
                                        <option value="WFH">WFH/WFA (Work From Home/Anywhere)</option>
                                        <option hidden value="N/a">N/a</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">From :</label>
                                    <input type="time" class="form-control" required autocomplete="off" name="from" id="start-time">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">To :</label>
                                    <input type="time" class="form-control" required autocomplete="off" name="to" id="end-time">
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">From :</label>
                                    <input type="time" class="form-control" required step="60" min="00:00" max="23:59" required pattern="[0-9]{2}:[0-9]{2}" placeholder="HH:mm" autocomplete="off" name="from" id="start-time" timeFormat="HH:mm">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">To :</label>
                                    <input type="time" class="form-control" required step="60" min="00:00" max="23:59" required pattern="[0-9]{2}:[0-9]{2}" placeholder="HH:mm" autocomplete="off" name="to" id="end-time">
                                </div>
                            </div> --}}
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Activity :</label>
                                    <textarea type="text" class="form-control" id="activity" name="activity" required></textarea>
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

<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Edit Entry <a id="entry-date-update"></a></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="update-form">
                @csrf
				<div class="modal-body" style="">
                    <input type="hidden" id="update_clickedDate" name="update_clickedDate">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Task :</label>
                                    <select class="form-control" id="update_task" name="update_task" required>
                                        <option value="HO">HO</option>
                                        <option value="Sick">Sick</option>
                                        <option value="Other">Other</option>
                                        <option>Standby</option>
                                        <optgroup label="Projects">
                                            @foreach($assignment as $assign)
                                            <option value="{{$assign->project_name}}">{{ $assign->project_name}}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Location :</label>
                                    <select class="form-control" id="update_location" name="update_location" required>
                                        <option value="DK">Dalam Kota</option>
                                        <option value="LK">Luar Kota</option>
                                        <option value="HO">Head Office</option>
                                        <option value="Outer Ring">Outer Ring (Bogor, Depok, Tangerang, Bekasi)</option>
                                        <option value="WFH">WFH/WFA (Work From Home/Anywhere)</option>
                                        <option hidden value="N/a">N/a</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">From :</label>
                                    <input type="time" class="form-control" required autocomplete="off" name="update_from" id="update_from">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">To :</label>
                                    <input type="time" class="form-control" required autocomplete="off" name="update_to" id="update_to">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Activity :</label>
                                    <textarea type="text" class="form-control" id="update_activity" name="update_activity" required></textarea>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="update-entry" class="btn btn-primary" data-dismiss="modal">Save changes</button>
                  </div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Add Manually Entry<a id="entry-date-update"></a></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="update-form">
                @csrf
				<div class="modal-body" style="">
                    <input type="hidden" id="update_clickedDate" name="update_clickedDate">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Date :</label>
                                    <input type="date" class="form-control" required autocomplete="off" name="update_from" value="<?php echo date('Ymd'); ?>?">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Task :</label>
                                    <select class="form-control" id="update_task" name="update_task" required>
                                        <option value="HO">HO</option>
                                        <option value="Sick">Sick</option>
                                        <option value="Other">Other</option>
                                        <option>Standby</option>
                                        <optgroup label="Projects">
                                            @foreach($assignment as $assign)
                                            <option value="{{$assign->project_name}}">{{ $assign->project_name}}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Location :</label>
                                    <select class="form-control" id="update_location" name="update_location" required>
                                        <option value="DK">Dalam Kota</option>
                                        <option value="LK">Luar Kota</option>
                                        <option value="HO">Head Office</option>
                                        <option value="Outer Ring">Outer Ring (Bogor, Depok, Tangerang, Bekasi)</option>
                                        <option value="WFH">WFH/WFA (Work From Home/Anywhere)</option>
                                        <option hidden value="N/a">N/a</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">From :</label>
                                    <input type="time" class="form-control" required autocomplete="off" name="update_from" id="update_from">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">To :</label>
                                    <input type="time" class="form-control" required autocomplete="off" name="update_to" id="update_to">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Activity :</label>
                                    <textarea type="text" class="form-control" id="update_activity" name="update_activity" required></textarea>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="update-entry" class="btn btn-primary" data-dismiss="modal">Save changes</button>
                  </div>
			</form>
		</div>
	</div>
</div>
<script>
    document.getElementById("task").addEventListener("change", function() {
    if (this.value === "Sick") {
        document.getElementById("activity").value = "-";
        document.getElementById("location").value = "N/a";
        document.getElementById("start-time").value = "00:00";
        document.getElementById("end-time").value = "00:00";
        document.getElementById("activity").setAttribute("readonly", true);
        document.getElementById("location").setAttribute("readonly", true);
        document.getElementById("start-time").setAttribute("readonly", true);
        document.getElementById("end-time").setAttribute("readonly", true);
    } else if(this.value === "Other") {
        document.getElementById("activity").value = "-";
        document.getElementById("location").value = "N/a";
        document.getElementById("start-time").value = "00:00";
        document.getElementById("end-time").value = "00:00";
        document.getElementById("activity").setAttribute("readonly", true);
        document.getElementById("location").setAttribute("readonly", true);
        document.getElementById("start-time").setAttribute("readonly", true);
        document.getElementById("end-time").setAttribute("readonly", true);  
    } else {
        document.getElementById("activity").removeAttribute("readonly");
        document.getElementById("location").removeAttribute("readonly");
        document.getElementById("start-time").removeAttribute("readonly");
        document.getElementById("end-time").removeAttribute("readonly");
    }
});
</script>
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