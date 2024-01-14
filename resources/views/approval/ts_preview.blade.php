@extends('layouts.main')

@section('title', 'Timesheet Approval - ESS')

@section('active-page-approval')
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
                    <a class="btn btn-danger btn-sm" type="button" href="{{ url()->previous() }}" id="manButton"><i class="fas fa-fw fa-backward fa-sm text-white-50"></i> Back</a>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body zoom90">
                <div class="row">
                    <div class="col-md-3 align-items-center text-center">
                        <div class="col-md-3 text-center">
                            <img src="{{ asset('img/PC-01.png') }}" style="height: 92px; width: 220px;" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th style="padding-left: 0;" class="m-0 font-weight-bold text-primary" colspan="2">Employee Information</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-sm">
                                    <td>Name</td>
                                    <td>: {{$user_info->name}}</td>
                                </tr>
                                <tr class="table-sm">
                                    <td>Service year</td>
                                    <td>: <?php
                                    $hired_date = $user_info->users_detail->hired_date; // assuming $hired_date is in Y-m-d format
                                    $current_date = date('Y-m-d'); // get the current date

                                    // create DateTime objects from the hired_date and current_date values
                                    $hired_date_obj = new DateTime($hired_date);
                                    $current_date_obj = new DateTime($current_date);

                                    // calculate the difference between the hired_date and current_date
                                    $diff = $current_date_obj->diff($hired_date_obj);

                                    // get the total number of years from the difference object
                                    $total_years_of_service = $diff->y;

                                    // output the total years of service
                                    echo $total_years_of_service.' Years';
                                    ?></td>
                                </tr>
                                <tr class="table-sm">
                                    <td>Assignment</td>
                                    <td class="clickable"><span class="long-text" title="{{ $assignmentNames }}">: {{ $assignmentNames }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-5">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th style="padding-left: 0;" class="m-0 font-weight-bold text-primary" colspan="2">Timesheet Information</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($info as $info)
                                <tr class="table-sm">
                                    <td>Periode :</td>
                                    <td>: {{ date("F", mktime(0, 0, 0, $month, 1)); }} {{ $year }}</td>
                                </tr>
                                <tr class="table-sm">
                                    <td>Status</td>
                                    <td>: {{ $info['status'] }}</td>
                                </tr>
                                <tr class="table-sm">
                                    <td>Updated At</td>
                                    <td>: {{ $info['lastUpdatedAt'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow mb-4">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Timesheet Preview</h6>
        <div class="text-right">
            <div class="btn-group">
                <button class="btn btn-primary btn-sm dropdown-toggle" style="margin-right: 5px;" type="button" id="approveDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-fw fa-check fa-sm text-white-50"></i> Approve
                </button>
                <div class="dropdown-menu" aria-labelledby="approveDropdown">
                    <form action="/approval/timesheet/approve/{{ $user_id }}/{{ $year }}/{{ $month }}" method="post">
                        @csrf
                        <div class="col-md-12 zoom90">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="approval_notes">Notes :</label>
                                                <textarea type="text" class="form-control" style="width: 300px;" name="approval_notes"></textarea>
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" id="approve" class="btn btn-sm btn-primary">Send & Approve</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="btn-group">
                <button class="btn btn-danger btn-sm dropdown-toggle" style="margin-right: 5px;" type="button" id="rejectDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-fw fa-ban fa-sm text-white-50"></i> Reject
                </button>
                <div class="dropdown-menu" aria-labelledby="rejectDropdown">
                    <form action="/approval/timesheet/reject/{{ $user_id }}/{{ $year }}/{{ $month }}" method="post">
                        @csrf
                        <div class="col-md-12 zoom90">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="reject_notes">Notes :</label>
                                                <textarea type="text" class="form-control" style="width: 300px;" name="reject_notes"></textarea>
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" id="reject" class="btn btn-sm btn-danger">Send & Reject</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <a class="btn btn-secondary btn-sm" type="button" href="/approval/timesheet/preview/print/{{$year}}/{{$month}}/{{$user_id}}" id="manButton">Download</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive zoom90 table-sm">
            <table class="table table-bordered zoom80" id="tsPreview" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Day</th>
                        <th>Date</th>
                        <th>Task</th>
                        <th>Location</th>
                        <th>Activity</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Hours</th>
                </thead>
                <tbody>
                    <?php $total_work_hours = 0; ?>
                    @for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay())
                    @php
                        $currentDate = $date->format('Y-m-d');
                        $isHoliday = false;
                        $holidayDescription = '';

                        // Check if the current date is in the array of formatted holiday dates
                        foreach ($formattedDatesHoliday as $holiday) {
                            if ($holiday['date'] === $currentDate) {
                                $isHoliday = true;
                                $holidayDescription = $holiday['summary'];
                                break; // Break the loop once found
                            }
                        }
                    @endphp
                        <tr>
                            <td>
                                @if ($date->dayOfWeek === 0 || $date->dayOfWeek === 6)
                                    <span class="text-danger">{{ $date->format('D') }}</span>
                                @elseif ($isHoliday)
                                    <span class="text-danger">{{ $date->format('D') }}</span>
                                @else
                                    {{ $date->format('D') }}
                                @endif
                            </td>
                            <td>
                                @if ($date->dayOfWeek === 0 || $date->dayOfWeek === 6)
                                    @if (in_array($date->format('Y-m-d'), $srtDate))
                                        <a href="/timesheet/user/preview/surat_penugasan/download/<?php echo $date->format('Ymd'); ?>/{{Auth::id()}}"><span class="text-danger">{{ $date->format('d-M-Y') }}</span>&nbsp;&nbsp;&nbsp;<i class="fas fa-fw fa-download fa-sm text-danger"></i></a>
                                    @elseif ($isHoliday)
                                        <span class="text-danger">{{ $date->format('d-M-Y') }}</span>
                                    @else
                                        <span class="text-danger">{{ $date->format('d-M-Y') }}</span>
                                    @endif
                                @else
                                    @if (in_array($date->format('Y-m-d'), $srtDate))
                                        <a href="/timesheet/entry/preview/surat_penugasan/download/<?php echo $date->format('Ymd'); ?>/{{Auth::id()}}"><span>{{ $date->format('d-M-Y') }}</span>&nbsp;&nbsp;&nbsp;<i class="fas fa-fw fa-download fa-sm text-primary"></i></a>
                                    @elseif ($isHoliday)
                                        <span class="text-danger">{{ $date->format('d-M-Y') }}</span>
                                    @else
                                        {{ $date->format('d-M-Y') }}
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if (in_array($date->format('Y-m-d'), $formattedDates))
                                    <span><i>Leave Day</i></span>
                                @else
                                    @foreach ($activities as $timesheet)
                                        @if ($timesheet->ts_date == $date->format('Y-m-d'))
                                            {{ $timesheet->ts_task }}<br>
                                        @else
                                            @if ($isHoliday)
                                                <span class="text-danger">{{ $holidayDescription }}</span>
                                                <?php break; ?>
                                            @endif
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if (in_array($date->format('Y-m-d'), $formattedDates))
                                    -
                                @else
                                    @foreach ($activities as $timesheet)
                                        @if ($timesheet->ts_date == $date->format('Y-m-d'))
                                            {{ $timesheet->ts_location }}<br>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if (in_array($date->format('Y-m-d'), $formattedDates))
                                    -
                                @else
                                    @foreach ($activities as $timesheet)
                                        @if ($timesheet->ts_date == $date->format('Y-m-d'))
                                            {{ $timesheet->ts_activity }}<br>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if (in_array($date->format('Y-m-d'), $formattedDates))
                                    -
                                @else
                                    @foreach ($activities as $timesheet)
                                        @if ($timesheet->ts_date == $date->format('Y-m-d'))
                                            {{ $timesheet->ts_from_time }}<br>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if (in_array($date->format('Y-m-d'), $formattedDates))
                                    -
                                @else
                                    @foreach ($activities as $timesheet)
                                        @if ($timesheet->ts_date == $date->format('Y-m-d'))
                                            {{ $timesheet->ts_to_time }}<br>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td class="text-center">
                                @if (in_array($date->format('Y-m-d'), $formattedDates))
                                    -
                                @else
                                <?php
                                        $work_hours = 0;
                                        $start_time = PHP_INT_MAX;
                                        $end_time = 0;

                                        foreach ($activities as $timesheet) {
                                            if ($timesheet->ts_date == $date->format('Y-m-d')) {
                                                $current_start_time = strtotime($timesheet->ts_from_time);
                                                $current_end_time = strtotime($timesheet->ts_to_time);

                                                if ($current_start_time < $start_time) {
                                                    $start_time = $current_start_time;
                                                }

                                                if ($current_end_time > $end_time) {
                                                    $end_time = $current_end_time;
                                                }
                                            }
                                        }

                                        if ($end_time > $start_time) {

                                            $time_diff_seconds = $end_time - $start_time;
                                            if($time_diff_seconds > 3600){
                                                $time_diff_seconds -= 3600;
                                            } else {
                                                $time_diff_seconds -= $time_diff_seconds;
                                            }
                                            $time_diff_hours = gmdate('H', $time_diff_seconds);
                                            $time_diff_minutes = substr(gmdate('i', $time_diff_seconds), 0, 2);
                                            $total_work_hours += ($time_diff_hours + ($time_diff_minutes / 60));
                                            echo $time_diff_hours.':'.$time_diff_minutes." Hours";
                                        }
                                    ?>
                                @endif
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div><br>
        <div class="table-responsive zoom90 table-sm">
            <table class="table table-borderless">
                <tbody>
                    <tr class="table-sm">
                        <td class="m-0 font-weight-bold text-danger" width="900px"></td>
                        @if($total_work_hours < $totalHours)
                            <td class="text-center text-danger font-weight-bold" title="Should be above {{ $totalHours }} Hours">
                                <i>
                                    Total :
                                    <?php
                                        $totalHoursWithoutDays = intval($total_work_hours);
                                        $totalMinutes = ($total_work_hours - intval($total_work_hours)) * 60; // Extract minutes
                                        echo $totalHoursWithoutDays." Hours ".intval($totalMinutes)." Minutes";
                                        $percentage = (($totalHoursWithoutDays + $totalMinutes / 60) / $totalHours) * 100;
                                        // echo "(".intval($percentage)."%)";
                                    ?>
                                    <br><small>NB : Each Day is Deducted (1 Hour) for Break Time</small>
                                </i>
                            </td>
                        @else
                            <td class="text-center text-success font-weight-bold">
                                <i>
                                    Total :
                                    <?php
                                        $totalHoursWithoutDays = intval($total_work_hours);
                                        $totalMinutes = ($total_work_hours - intval($total_work_hours)) * 60; // Extract minutes
                                        echo $totalHoursWithoutDays." Hours ".intval($totalMinutes)." Minutes";
                                        $percentage = (($totalHoursWithoutDays + $totalMinutes / 60) / $totalHours) * 100;
                                        // echo "(".intval($percentage)."%)";
                                    ?>
                                    <br><small>NB : Each Day is Deducted (1 Hour) for Break Time</small>
                                </i>
                            </td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Timesheet Workflow</h6>
                <div class="text-right">
                    {{-- <a class="btn btn-primary btn-sm" type="button"  data-toggle="modal" data-target="#addModal" id="addButton">View Details</a> --}}
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm zoom90">
                        <thead class="thead-light">
                            <tr>
                                <th>Username</th>
                                <th>Project</th>
                                <th>Location</th>
                                <th>Mandays</th>
                                <th>Status</th>
                                <th>Approver</th>
                                <th>Notes</th>
                        </thead>
                        <tbody>
                            @foreach($workflow as $index => $wf)
                            <tr>
                                @if ($index > 0 && $wf->user->name === $workflow[$index-1]->user->name)
                                <td style="border-bottom: none; border-top: none;"></td>
                                <td style="border-bottom: none; border-top: none;"><span class="shorter-text">{{ $wf->ts_task }}</span></td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->ts_location }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->ts_mandays }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->approval_status->status_desc }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->requestTo->name }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->note }}</td>
                                @else
                                <td style="border-bottom: none; border-top: none;">{{ strtok($wf->user->name, " ") }}</td>
                                <td style="border-bottom: none; border-top: none;"><span class="shorter-text">{{ $wf->ts_task }}</span></td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->ts_location }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->ts_mandays }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->approval_status->status_desc }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->requestTo->name }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->note }}</td>
                                @endif
                            </tr>
                            @endforeach
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td colspan="7" class="text-center">Copyright @ Author of ESS Perdana Consulting</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="yearSel" value="{{ $year }}">
<input type="hidden" id="monthSel" value="{{ $month }}">
@endsection

@section('javascript')
<script src="{{ asset('js/timesheet.js') }}"></script>
@endsection

