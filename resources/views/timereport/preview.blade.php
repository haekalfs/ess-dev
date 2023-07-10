@extends('layouts.main')

@section('title', 'Timesheet - ESS')

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
<style>
.img-profile.rounded-circle.no-image {
    margin-top: 15px;
    position: relative;
    width: 100px;
    height: 100px;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    display: inline-block;
    align-items: center;
    justify-content: center;
    height: 20vh;
    width: 20vh;
}

.no-image-text {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 20vh;
}

</style>
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
                        <div class="col-md text-center">
                            @if($user_info->users_detail->profile_pic)
                                <img class="img-profile rounded-circle" height="150px"width="150px" style="object-fit:fill;" src="{{ url('/storage/profile_pic/'.$user_info->users_detail->profile_pic) }}" data-toggle="modal" data-target="#profileModal">
                            @else
                                <div class="img-profile rounded-circle no-image"><i class="no-image-text">No Image Available</i></div>
                            @endif
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
            @if($removeBtnSubmit == 0)
            <a class="btn btn-secondary btn-sm" type="button" href="/timesheet/entry/preview/print/{{$year}}/{{$month}}" id="manButton" style="margin-right: 10px;">Download</a>
            <a class="btn btn-primary btn-sm" type="button" href="/timesheet/entry/submit/{{$year}}/{{$month}}" id="copyButton">Submit</a>
            {{-- <a class="btn btn-warning btn-sm" type="button" href="/timesheet/entry/cancel_submit/{{$year}}/{{$month}}" id="copyButton">Cancel Submit</a> --}}
            @elseif($removeBtnSubmit == 29)
            <a class="btn btn-secondary btn-sm" type="button" href="/timesheet/entry/preview/print/{{ $year}}/{{$month}}" id="manButton">Download</a>
            @else
            <a class="btn btn-secondary btn-sm" type="button" href="/timesheet/entry/preview/print/{{$year}}/{{$month}}" id="manButton" style="margin-right: 10px;">Download</a>
            <a class="btn btn-warning btn-sm" type="button" href="/timesheet/entry/cancel_submit/{{$year}}/{{$month}}" id="copyButton">Cancel Submit</a>
            @endif
        </div>
    </div>
    <!-- Card Body -->
    <div class="card-body">
        <div class="table-responsive table-sm">
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
                        <tr>
                            <td>
                                @if ($date->dayOfWeek === 0 || $date->dayOfWeek === 6)
                                    <span class="text-danger">{{ $date->format('D') }}</span>
                                @else
                                    {{ $date->format('D') }}
                                @endif
                            </td>
                            <td>
                                @if ($date->dayOfWeek === 0 || $date->dayOfWeek === 6)
                                    @if (in_array($date->format('Y-m-d'), $srtDate))
                                        <a href="/timesheet/entry/preview/surat_penugasan/download/<?php echo $date->format('Ymd'); ?>"><span class="text-danger">{{ $date->format('d-M-Y') }}</span>&nbsp;&nbsp;&nbsp;<i class="fas fa-fw fa-download fa-sm text-danger"></i></a>
                                    @else
                                        <span class="text-danger">{{ $date->format('d-M-Y') }}</span>
                                    @endif
                                @else
                                    @if (in_array($date->format('Y-m-d'), $srtDate))
                                        <a href="/timesheet/entry/preview/surat_penugasan/download/<?php echo $date->format('Ymd'); ?>"><span>{{ $date->format('d-M-Y') }}</span>&nbsp;&nbsp;&nbsp;<i class="fas fa-fw fa-download fa-sm text-primary"></i></a>
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
                                        $time_diff_hours = gmdate('H', $time_diff_seconds);
                                        $time_diff_minutes = substr(gmdate('i', $time_diff_seconds), 0, 2);
                                        $work_hours = $time_diff_hours + ($time_diff_minutes / 60);
                                    }

                                    $total_work_hours += $work_hours;
                                    if ($work_hours > 0) {
                                        echo intval($work_hours - 1)." Hours";
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
                        <td class="m-0 font-weight-bold text-danger" width="1000px"></td>
                        @if($total_work_hours < $totalHours)
                        <td class="text-center text-danger font-weight-bold" title="Should be above {{ $totalHours }} Hours"><i>Total Workhours : <?php echo intval($total_work_hours) - $getTotalDays; ?> Hours <?php $percentage = ((intval($total_work_hours) - $getTotalDays) / $totalHours) * 100; echo "(".$percentage."%)";?></i></td>
                        @else
                        <td class="text-center text-success font-weight-bold"><i>Total Workhours : <?php echo intval($total_work_hours) - $getTotalDays; ?> Hours <?php $percentage = ((intval($total_work_hours) - $getTotalDays) / $totalHours) * 100; echo "(".$percentage."%)";?></i></td>
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
                                <td style="border-bottom: none; border-top: none;">{{ $wf->activity }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->requestTo->name }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->note }}</td>
                                @else
                                <td style="border-bottom: none; border-top: none;">{{ strtok($wf->user->name, " ") }}</td>
                                <td style="border-bottom: none; border-top: none;"><span class="shorter-text">{{ $wf->ts_task }}</span></td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->ts_location }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->ts_mandays }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->activity }}</td>
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
<!-- Modal Profile -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="text-right" width="100px">
    </div>
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="close-icon">
                    <img width="35" height="35" src="https://img.icons8.com/ios-glyphs/60/macos-close.png" alt="macos-close" data-dismiss="modal">
                </div>
                <img src="{{ url('/storage/profile_pic/'.$user_info->users_detail->profile_pic) }}" class="img-fluid" alt="Profile Picture">
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