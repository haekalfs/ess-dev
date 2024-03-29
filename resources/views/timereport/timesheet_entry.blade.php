@extends('layouts.main')

@section('title', 'Timesheet Entry - ESS')

@section('active-page-timesheet')
active
@endsection

@section('content')
<!-- Page Heading -->

<!-- Page Heading -->
<div class="zoom90 d-sm-flex align-items-center justify-content-between">
    <div>
        <h1 class="h3 font-weight-bold zoom90 mb-2 text-gray-800"><i class="fas fa-calendar"></i> Timesheet Entry</h1>
    <p class="mb-4">Timesheet Entry for {{ date("F", mktime(0, 0, 0, $month, 1)) }} - {{$year}}</a>.</p>
    </div>
    <a type="button" data-toggle="modal" data-target="#guidelines" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-tasks"></i> Guidelines</a>
</div>
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

@if ($message = Session::get('timesheet-cutoffdate'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

<div class="alert alert-success alert-success-saving" role="alert" style="display: none;">
    Your entry has been saved successfully.
</div>

<div class="alert alert-danger alert-success-delete" role="alert" style="display: none;">
    <span class="error-message"></span>
</div>

<div class="alert alert-danger-delete" role="alert" style="display: none;">
    An error occurred while deleting your entry. Please try again.
</div>

<div class="overlay overlay-mid" style="display: none;"></div>

<div class="alert alert-danger alert-success-delete-mid" role="alert" style="display: none;">
</div>

<div class="alert alert-success alert-success-saving-mid" role="alert" style="display: none;">
    Your entry has been saved successfully.
</div>
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-12" id="activityContainer" style="display: none;">
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
                        role="button" aria-expanded="true" aria-controls="collapseCardExample">
                        <h6 class="m-0 font-weight-bold text-primary">Information Details</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseCardExample">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Mandays :</th>
                                            </tr>
                                        </thead>
                                        <tbody class="calculations zoom90">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-xl-3 col-lg-3">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>My Leave Days :</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <td>
                                            @forelse($leaveRequests as $leaves)
                                                <li>
                                                    @foreach ($leaves->dateGroups as $key => $group)
                                                        @if ($key > 0)
                                                            -
                                                        @endif
                                                        {{ implode(',', $group['dates']) }} {{ $group['monthYear'] }}
                                                    @endforeach
                                                </li>
                                            @empty
                                                <a><small><i>No Leaves</i></small></a>
                                            @endforelse
                                            </td>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-xl-5 col-lg-5">
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>My Assignments :</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="col-xl-12 col-lg-12">
                                            @forelse($assignment as $assign)
                                            <li>{{ $assign->project_name }}</li>
                                            @empty
                                                <a><small><i>No Project Assigned</i></small></a>
                                            @endforelse
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
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Calendar</h6>
                <div class="text-right">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="customSwitch1">
                        <label class="custom-control-label" for="customSwitch1">Show Details</label>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body zoom85">
                <table class="table zoom90 table-bordered calendar" id="calendarTable" style="font-size: 18px;">
                    <colgroup>
                        @foreach ($calendar[0] as $dayName)
                            <col style="width: {{ 100 / count($calendar[0]) }}%;">
                        @endforeach
                    </colgroup>
                    <thead class="bg-primary">
                        <tr class="text-white">
                            @foreach ($calendar[0] as $dayName)
                                <th>{{ $dayName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="calendarBody">
                        @foreach (array_slice($calendar, 1) as $week)
                            <tr>
                                @php
                                    $isCurrentMonth = false;
                                @endphp
                                @foreach ($week as $day)
                                    @if ($day !== '')
                                        @if (is_array($day) && isset($day['status']))
                                            @php
                                                $dayValue = $day['day'];
                                                $status = $day['status'];
                                                $summary = $day['summary'];
                                            @endphp
                                            @if ($status === "red")
                                                <td data-toggle="modal" class="clickable test text-danger" data-target="#redModal" data-date="{{ $year }}-{{ $month }}-{{ $dayValue }}" id="task_entry{{ $dayValue }}">{{ $dayValue }}. <small class="mb-4">{{ $summary }}</small> <div id="desc{{$dayValue}}"></div><br></td>
                                            @elseif ($status === 2907)
                                                <td class="clickable test text-dark" data-date="{{ $year }}-{{ $month }}-{{ $dayValue }}" id="task_entry{{ $dayValue }}"><del>{{ $dayValue }}</del>.&nbsp;
                                                    <a><i class="fas fa-plane-departure fa-sm"></i></a>
                                                </td>
                                            @elseif ($status === 100)
                                                <td class="clickable test text-danger" data-date="{{ $year }}-{{ $month }}-{{ $dayValue }}" id="task_entry{{ $dayValue }}"><del>{{ $dayValue }}</del>.&nbsp;
                                                    <div class="text-center mt-2"><br><small><i>Weekend Replacement</i></small></div>
                                                </td>
                                            @elseif ($status === 404)
                                                <td class="clickable test text-dark" data-date="{{ $year }}-{{ $month }}-{{ $dayValue }}" id="task_entry{{ $dayValue }}"><del>{{ $dayValue }}</del>.<div id="desc{{$dayValue}}"></div></td>
                                            @else
                                                <td data-toggle="modal" class="clickable test text-dark" data-target="#myModal" data-date="{{ $year }}-{{ $month }}-{{ $dayValue }}" id="task_entry{{ $dayValue }}">{{ $dayValue }}.<div id="desc{{$dayValue}}"></div></td>
                                            @endif
                                        @else
                                            @php
                                                $dayValue = $day;
                                                $status = '';
                                            @endphp
                                            @if (date('j', strtotime($year.'-'.$month.'-'.$dayValue)) == 1)
                                                @php
                                                    $isCurrentMonth = true;
                                                    $prevMonth = date('n', strtotime($year.'-'.$month.'-'.$dayValue.' -1 day'));
                                                @endphp
                                            @endif
                                            @if ($isCurrentMonth)
                                                @if (date('N', strtotime($year.'-'.$month.'-'.$dayValue)) == 6 || date('N', strtotime($year.'-'.$month.'-'.$dayValue)) == 7)
                                                    <td data-toggle="modal" class="clickable test text-danger" data-target="#myModal" data-date="{{ $year }}-{{ $month }}-{{ $dayValue }}" id="task_entry{{ $dayValue }}">{{ $dayValue }}.<div id="desc{{$dayValue}}"></div></td>
                                                @else
                                                    <td data-toggle="modal" class="clickable test text-dark" data-target="#myModal" data-date="{{ $year }}-{{ $month }}-{{ $dayValue }}" id="task_entry{{ $dayValue }}">{{ $dayValue }}.<div id="desc{{$dayValue}}"></div></td>
                                                @endif
                                            @else
                                                <td class="prev-month-day">{{ $dayValue }}</td>
                                            @endif
                                        @endif
                                    @else
                                        <td>&nbsp;</td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="card shadow mb-4 zoom90">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Detail Activity Entries</h6>
        <div class="text-right">
            @role('s-user') <a class="btn btn-primary btn-sm" type="button"  data-toggle="modal" data-target="#addModal" id="addButton" style="margin-right: 10px;">+ Bulk Entries</a> @else @endrole
            <a class="btn btn-secondary btn-sm" type="button" href="{{ $previewButton }}" id="manButton"><i class="fas fa-eye"></i> Preview</a>
        </div>
    </div>
    <!-- Card Body -->
    <div class="card-body">
        <div class="table-responsive zoom90" style="overflow: hidden;">
            <div class="alert alert-danger alert-success-delete" role="alert" style="display: none;">
                <span class="error-message"></span>
            </div>

            <div class="alert alert-danger-delete" role="alert" style="display: none;">
                An error occurred while deleting your entry. Please try again.
            </div>

            <div class="alert alert-success alert-success-saving" role="alert" style="display: none;">
                Your entry has been saved successfully.
            </div>
            <div class="row-toolbar">
                <div class="col">
                    <select style="max-width: 13%;" class="form-control" id="rowsPerPage">
                        <option value="-1">All</option>
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                    </select>
                </div>
                <div class="col-auto text-right">
                    <input class="form-control" type="text" id="searchInput" placeholder="Search...">
                </div>
            </div>
            <table class="table table-bordered" id="timesheetsTable" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 10px;">Day</th>
                        <th>Date</th>
                        <th>Task</th>
                        <th>Location</th>
                        <th>Activity</th>
                        <th>From</th>
                        <th>To</th>
                        <th style="width: 10px;">Action</th>
                </thead>
                <tbody id="activity-table">
                    <!-- Display the data fetched via AJAX here -->
                </tbody>
            </table>
        </div><br>
        <div class="text-right zoom90">
            <a class="btn btn-danger btn-sm delete-all" data-year="{{ $year }}" data-month="{{ $month }}" type="button">Reset All</a>
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
			<form method="post" id="entry-form" enctype="multipart/form-data">
                @csrf
				<div class="modal-body" style="">
                    <input type="hidden" id="clickedDate" name="clickedDate">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12" id="fileInputIfexistWfh" style="display: none;">
                                <div class="form-group d-flex align-items-center" style="margin-bottom: 8px;">
                                    <label for="email" style="margin-bottom: 0;">
                                        <span class="text-danger"><i>Surat Penugasan :</i></span>
                                    </label>
                                    <div class="ml-auto">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckWfh">
                                            <label class="form-check-label" for="flexCheckWfh">Show Recent Files</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="display: block;" id="uploadFileWfh">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="surat_penugasan_wfh" name="surat_penugasan_wfh" onchange="changeFileName('surat_penugasan_wfh', 'sp-label-wfh')">
                                        <label class="custom-file-label" id="sp-label-wfh">Choose file</label>
                                    </div>
                                    <small style="color: red;"><i>Only pdf, jpg, png, jpeg allowed! Maximum 500kb</i></small>
                                </div>
                                <div id="filesUploadedWfh" style="display: none;">
                                    @if($filesUploaded)
                                        @foreach($filesUploaded as $fu)
                                            <div class="file-container">
                                                <div class="file-infoWfh">
                                                    {{ $fu->file_name }}
                                                </div>
                                                <a data-fileid="{{ $fu->id }}" id="selectFileWfh{{$fu->id}}" class="btn btn-sm btn-primary selectFileWfh">Select</a>
                                                <div id="loadingIndicatorWfh{{$fu->id}}" style="display: none;" class="spinner-border" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <input name="selectedFileUploadedWfh" id="selectedFileUploadedWfh" type="hidden" />
                        </div>
                        <div class="row" id="fields">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Task :</label>
                                    <select class="form-control" id="task" name="task" required>
                                        <option value="HO">Head Office</option>
                                        <option value="Training">Training</option>
                                        <optgroup label="Presales">
                                        <option value="Presales">Presales</option>
                                        <option value="Trainer">Trainer</option>
                                        </optgroup>
                                        <optgroup label="Others">
                                            <option value="StandbyLK">Standby (LK)</option>
                                            <option value="StandbyLN">Standby (LN)</option>
                                            {{-- <option value="Lembur">Lembur</option> --}}
                                            <option value="Sick">Sick</option>
                                            <option value="Other">Other</option>
                                        </optgroup>
                                        <optgroup label="Projects">
                                            @if ($assignment->isEmpty())
                                                <option disabled><i>No Project Assigned</i></option>
                                            @else
                                                @foreach($assignment as $assign)
                                                    <option value="{{$assign->project_assignment_id}}">{{ $assign->project_name}}</option>
                                                @endforeach
                                            @endif
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group" id="locationContainer">
                                    <label for="location">Location :</label>
                                    <select class="form-control" id="location" name="location" required></select>
                                    {{-- <select class="form-control" id="location" name="location" required>
                                        @foreach($pLocations as $loc)
                                            <option value="{{$loc->location_code}}" {{$loc->location_code == old('location') ? 'selected' : ''}}>{{ $loc->description }}</option>
                                        @endforeach
                                        <option hidden value="N/a">N/a</option>
                                    </select> --}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">From :</label>
                                    <input type="text" class="form-control validate time-input" required autocomplete="off" placeholder="HH:mm" name="from" id="start-time" timeFormat="HH:mm">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">To :</label>
                                    <input type="text" class="form-control validate time-input" required autocomplete="off" placeholder="HH:mm" name="to" id="end-time" timeFormat="HH:mm">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Detail Activity :</label>
                                    <textarea type="text" class="form-control validate" id="activity" name="activity" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="save-entry" class="btn btn-primary" data-dismiss="modal"><i class="far fa-save"></i> Save changes</button>
                  </div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="redModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Entry <a id="selected-date-display-red"></a></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="entry-form-red" enctype="multipart/form-data">
                @csrf
				<div class="modal-body" style="">
                    <input type="hidden" id="clickedDateRed" name="clickedDateRed">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12" id="fileInputIfexist">
                                <div class="form-group d-flex align-items-center" style="margin-bottom: 8px;">
                                    <label for="email" style="margin-bottom: 0;">
                                        <span class="text-danger"><i>Surat Penugasan :</i></span>
                                    </label>
                                    <div class="ml-auto">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                            <label class="form-check-label" for="flexCheckDefault">Show Recent Files</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="display: block;" id="uploadFile">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input validate-red" id="surat_penugasan" name="surat_penugasan" onchange="changeFileName('surat_penugasan', 'sp-label')">
                                        <label class="custom-file-label" id="sp-label">Choose file</label>
                                    </div>
                                    <small style="color: red;"><i>Only pdf, jpg, png, jpeg allowed! Maximum 5000kb</i></small>
                                </div>
                                <div id="filesUploaded" style="display: none;">
                                    @if($filesUploaded)
                                        @foreach($filesUploaded as $fu)
                                            <div class="file-container">
                                                <div class="file-info">
                                                    {{ $fu->file_name }}
                                                </div>
                                                <a data-fileid="{{ $fu->id }}" id="selectFile{{$fu->id}}" class="btn btn-sm btn-primary selectFile">Select</a>
                                                <div id="loadingIndicator{{$fu->id}}" style="display: none;" class="spinner-border" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <input name="selectedFileUploaded" id="selectedFileUploaded" type="hidden" />
                        </div>
                        <div class="row" id="fields-red">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Task :</label>
                                    <select class="form-control" id="task-red" name="task" required>
                                        <option value="HO">Head Office</option>
                                        <option value="Training">Training</option>
                                        <optgroup label="Presales">
                                        <option value="Presales">Presales</option>
                                        <option value="Trainer">Trainer</option>
                                        <optgroup label="Others">
                                            <option value="StandbyLK">Standby (LK)</option>
                                            <option value="StandbyLN">Standby (LN)</option>
                                            {{-- <option value="Lembur">Lembur</option> --}}
                                            <option value="Sick">Sick</option>
                                            <option value="Other">Other</option>
                                        </optgroup>
                                        <optgroup label="Projects">
                                            @if ($assignment->isEmpty())
                                                <option disabled><i>No Project Assigned</i></option>
                                            @else
                                                @foreach($assignment as $assign)
                                                    <option value="{{$assign->project_assignment_id}}">{{ $assign->project_name}}</option>
                                                @endforeach
                                            @endif
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group" id="locationContainerRed">
                                    <label for="password">Location :</label>
                                    <select class="form-control" id="location-red" name="location" required></select>
                                    {{-- <select class="form-control" id="location-red" name="location" required>
                                        @foreach($pLocations as $loc)
                                            <option value="{{$loc->location_code}}" {{$loc->location_code == old('location') ? 'selected' : ''}}>{{ $loc->description }}</option>
                                        @endforeach
                                        <option hidden value="N/a">N/a</option>
                                    </select> --}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">From :</label>
                                    <input type="text" class="form-control validate-red time-input" required autocomplete="off" placeholder="HH:mm" name="from" id="start-time" timeFormat="HH:mm">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">To :</label>
                                    <input type="text" class="form-control validate-red time-input" required autocomplete="off" placeholder="HH:mm" name="to" id="end-time" timeFormat="HH:mm">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Detail Activity :</label>
                                    <textarea type="text" class="form-control validate-red" id="activity" name="activity" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="save-entry-red" class="btn btn-primary" data-dismiss="modal"><i class="far fa-save"></i> Save changes</button>
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
                            <div class="col-md-12" id="fileInputIfexistWfh" style="display: none;">
                                <div class="form-group">
                                    <label for="email"><span class="text-danger"><i>Surat Penugasan :</i></span></label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="surat_penugasan_wfh" name="surat_penugasan_wfh" onchange="changeFileName('surat_penugasan_wfh', 'sp-label-wfh')">
                                        <label class="custom-file-label" id="sp-label-wfh">Choose file</label>
                                    </div>
                                    <small style="color: red;"><i>Only pdf, jpg, png, jpeg allowed! Maximum 500kb</i></small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Task :</label>
                                    <select class="form-control" id="update_task" name="update_task" required>
                                        <option value="HO">Head Office</option>
                                        <option value="Training">Training</option>
                                        <optgroup label="Presales">
                                        <option value="Presales">Presales</option>
                                        <option value="Trainer">Trainer</option>
                                        <optgroup label="Others">
                                            <option value="StandbyLK">Standby (LK)</option>
                                            <option value="StandbyLN">Standby (LN)</option>
                                            {{-- <option value="Lembur">Lembur</option> --}}
                                            <option value="Sick">Sick</option>
                                            <option value="Other">Other</option>
                                        </optgroup>
                                        <optgroup label="Projects">
                                            @if ($assignment->isEmpty())
                                                <option disabled><i>No Project Assigned</i></option>
                                            @else
                                                @foreach($assignment as $assign)
                                                    <option value="{{$assign->project_assignment_id}}">{{ $assign->project_name}}</option>
                                                @endforeach
                                            @endif
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" id="updateLocationSelect">
                                    <label for="password">Location :</label>
                                    <select class="form-control" id="update_location" name="update_location" required></select>
                                    {{-- <select class="form-control" id="update_location" name="update_location" required>
                                        @foreach($pLocations as $loc)
                                            <option value="{{$loc->location_code}}">{{ $loc->description }}</option>
                                        @endforeach
                                        <option hidden value="N/a">N/a</option>
                                    </select> --}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">From :</label>
                                    <input type="text" class="form-control time-input" required autocomplete="off" name="update_from" id="update_from">
                                    <input type="hidden" class="form-control" required autocomplete="off" name="ts_type" id="ts_type">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">To :</label>
                                    <input type="text" class="form-control time-input" required autocomplete="off" name="update_to" id="update_to">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Detail Activity :</label>
                                    <textarea type="text" class="form-control" id="update_activity" name="update_activity" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    {{-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> --}}
                    <a class="btn btn-danger delete-btn-update" data-dismiss="modal" id="deleteBtn"><i class="fas fa-trash-alt"></i> Reset</a>
                    <button type="submit" id="update-entry" class="btn btn-primary" data-dismiss="modal"><i class="far fa-save"></i> Save changes</button>
                  </div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Add Entry Manually<a id="entry-date-update"></a></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="multiple-entry-form">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Date :</label>
                                    <input type="text" class="form-control validateMult" name="daterange" id="daterange"/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Task :</label>
                                    <select class="form-control" id="task" name="task" required>
                                        <option value="HO">Head Office</option>
                                        <option value="Training">Training</option>
                                        <optgroup label="Presales">
                                        <option value="Presales">Presales</option>
                                        <option value="Trainer">Trainer</option>
                                        <optgroup label="Others">
                                            <option value="StandbyLK">Standby (LK)</option>
                                            <option value="StandbyLN">Standby (LN)</option>
                                            {{-- <option value="Lembur">Lembur</option> --}}
                                            <option value="Sick">Sick</option>
                                            <option value="Other">Other</option>
                                        </optgroup>
                                        <optgroup label="Projects">
                                            @if ($assignment->isEmpty())
                                                <option disabled><i>No Project Assigned</i></option>
                                            @else
                                                @foreach($assignment as $assign)
                                                    <option value="{{$assign->project_assignment_id}}">{{ $assign->project_name}}</option>
                                                @endforeach
                                            @endif
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
                                        @foreach($pLocations as $loc)
                                            <option value="{{$loc->location_code}}">{{ $loc->description }}</option>
                                        @endforeach
                                        <option hidden value="N/a">N/a</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">From :</label>
                                    <input type="text" class="form-control validateMult time-input" placeholder="HH:mm" required autocomplete="off" name="from" id="from">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">To :</label>
                                    <input type="text" class="form-control validateMult time-input" placeholder="HH:mm" required autocomplete="off" name="to" id="to">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Detail Activity :</label>
                                    <textarea type="text" class="form-control validateMult" id="activity" name="activity" required rows="3"></textarea>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="multiple-entries" class="btn btn-primary" data-dismiss="modal">Save changes</button>
                  </div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="guidelines" tabindex="-1" role="dialog" aria-labelledby="modalguidelines" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom-1">
                <h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Timesheet Guidelines</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mr-2" style="text-align: justify;">
                <p>Here are several rules for filling out the timesheet, namely:</p>
                <ol>
                    <li>Fill in by clicking the desired date on the calendar</li>
                    <li>Filling out daily timesheet can be done repeatedly as long as it does not conflict with other tasks</li>
                    <li>For holidays, you must attach an assignment letter (can be a WA screenshot, SE/SK, etc.)</li>
                    <li>To edit a task that has been filled in, you can do this by clicking on the task on the calendar, or going down to the <i>Activities Entry</i> table and clicking on the date in question.</li>
                    <li>Submitting timesheets can only be done at certain times.</li>
                    <li>Delete tasks can be done below, in the <i>Activities Entry</i> table.</li>
                    <li>The location on the project is regulated by the Project Controller.</li>
                    <li class="text-danger font-weight-bold">If there's a faulty or an error, CLEAR YOUR CACHE [Image Cache Only, Dont Clear Your browse or cookie history].</li>
                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<style>
    /* CSS for the file container */
.file-container {
  border: 1px solid #ccc;
  padding: 8px;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%; /* Adjust width as needed */
  border-radius: 8px;
}

/* Style for the file information */
.file-info {
  flex-grow: 1;
  margin-right: 10px; /* Adjust spacing as needed */
}

/* Style for the "Select" button */
.select-button {
  background-color: #007bff; /* Change to your preferred button color */
  color: #fff;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

  </style>
{{-- this need to be corrected --}}
<input type="hidden" id="usersAllowed" name="usersAllowed" value="{{Auth::id()}}">
<script>
$(document).ready(function() {
    // Initial state
    var activityContainer = $('#activityContainer');
    var switchInput = $('#customSwitch1');

    switchInput.change(function() {
        if (switchInput.is(':checked')) {
            activityContainer.show();
        } else {
            activityContainer.hide();
        }
    });
});

function setupTimeInputs() {
    $('.time-input').datetimepicker({
      format: 'HH:mm'
    });

    const timeInputs = document.querySelectorAll('.time-input');

    timeInputs.forEach(function(timeInput) {
      timeInput.addEventListener("input", function (event) {
        const input = event.target.value;
        const regex = /^([01]?[0-9]|2[0-3]):([0-5][0-9])$/; // regex for valid time format (24-hour)

        if (regex.test(input)) { // input is valid time
          timeInput.value = input; // no need to reformat
        } else {
          let formatted = input.replace(/\D/g, ""); // remove non-digits
          if (formatted.length > 4) formatted = formatted.substring(0, 4); // limit to 4 digits

          // insert colon between hours and minutes
          const hours = formatted.substring(0, 2);
          let minutes = formatted.substring(2);

          // allow user to delete leading zero
          if (minutes === "0" && formatted.length > 2) {
            minutes = "";
          } else if (minutes.length > 0 && minutes[0] === "0" && minutes[1] !== undefined) {
            minutes = "0" + minutes[1];
          }

          // add colon between hours and minutes if necessary
          if (hours.length > 0 && minutes.length > 0) {
            timeInput.value = `${hours}:${minutes}`;
          } else {
            timeInput.value = formatted;
          }
        }
      });

      timeInput.addEventListener("keydown", function (event) {
        if (event.key === "Backspace") {
          const input = event.target.value;
          const regex = /^([01]?[0-9]|2[0-3]):([0-5][0-9])$/; // regex for valid time format (24-hour)

          if (regex.test(input)) { // input is valid time
            // remove last character (colon)
            timeInput.value = input.substring(0, input.length - 1);
          }
        }
      });
    });
  }

  // Call the setupTimeInputs function initially
  setupTimeInputs();

  // Handle form submission or page refresh event
  function handleFormSubmit() {
    // Handle form submission or page refresh logic here

    // After form submission or page refresh, call the setupTimeInputs function again
    setupTimeInputs();
  }

function changeFileName(inputId, labelId) {
  var input = document.getElementById(inputId);
  var label = document.getElementById(labelId);
  label.textContent = input.files[0].name;
}
</script>
<style>
    .row-toolbar {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px; /* Adjust the desired bottom spacing value */
    }
    td {
        cursor: pointer;
        transition: background-color 0.3s ease-in-out;
    }
    .test {
        height: 160px;
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
        height: 601px;
        perspective: 1000;
        transition: .9s;
        transform-style: preserve-3d;
        width: 100%;
    }

    .round-text-box {/* Border color similar to alert-danger */
        background-color: #f7c121; /* Background color similar to alert-danger */
        color: #7e7e7e; /* Text color similar to alert-danger */
        padding: 5px; /* Adjust padding to control the size of the box */
        border-radius: 10px; /* Border radius for rounded corners */
        transition: background-color 0.3s; /* Add a smooth transition for the background color */
    }

    .round-text-box:hover {
        background-color: #b56f00; /* Change the background color on hover */
        color: #fff; /* Change the text color on hover */
    }
    .round-text-box2 {/* Border color similar to alert-danger */
        background-color: #cdeaff; /* Background color similar to alert-danger */
        color: #7e7e7e; /* Text color similar to alert-danger */
        padding: 5px; /* Adjust padding to control the size of the box */
        border-radius: 10px; /* Border radius for rounded corners */
        transition: background-color 0.3s; /* Add a smooth transition for the background color */
    }

    .round-text-box2:hover {
        background-color: #1075bd; /* Change the background color on hover */
        color: #fff; /* Change the text color on hover */
    }
</style>
@endsection

@section('javascript')
<script src="{{ asset('js/timesheet.js') }}"></script>
@endsection
