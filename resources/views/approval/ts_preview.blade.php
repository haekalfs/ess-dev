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
                                    <td>Nama</td>
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
                    <form action="/approval/timesheet/approve/{{ $id }}/{{ $year }}/{{ $month }}" method="post">
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
                    <form action="/approval/timesheet/reject/{{ $id }}/{{ $year }}/{{ $month }}" method="post">
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
            <a class="btn btn-secondary btn-sm" type="button" href="/timesheet/approval/preview/print/{{$year}}/{{$month}}/{{$id}}" id="manButton">Download</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive zoom90 table-sm">
            <table class="table table-bordered" id="tsPreview" width="100%" cellspacing="0">
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
                    @foreach($timesheet as $timesheets)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($timesheets->ts_date)->format('D') }}</td>
                            <td>{{ date('d-M-Y', strtotime($timesheets->ts_date)) }}</td>
                            <td>{{ $timesheets->ts_task }}</td>
                            <td>{{ $timesheets->ts_location }}</td>
                            <td>{{ $timesheets->ts_activity }}</td>
                            <td>{{ $timesheets->ts_from_time }}</td>
                            <td>{{ $timesheets->ts_to_time }}</td>
                            <td>
                            <?php 
                            $start_time = strtotime($timesheets->ts_from_time);
                            $end_time = strtotime($timesheets->ts_to_time);
                            $time_diff_seconds = $end_time - $start_time;
                            $time_diff_hours = gmdate('H', $time_diff_seconds);
                            $time_diff_minutes = substr(gmdate('i', $time_diff_seconds), 0, 2);
                            $total_work_hours += ($time_diff_hours + ($time_diff_minutes / 60)); echo $time_diff_hours.':'.$time_diff_minutes;
                            ?>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div><br>
        <div class="table-responsive zoom90 table-sm">
            <table class="table table-borderless">
                <tbody>
                    <tr class="table-sm">
                        <td class="m-0 font-weight-bold text-danger" width="1000px"></td>
                        <td class="text-center font-weight-bold"><i>Total Workhours : <?php echo intval($total_work_hours); ?> Hours</i></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-8">
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
                                <td style="border-bottom: none; border-top: none;">{{ $wf->activity }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->RequestTo }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->note }}</td>
                                @else
                                <td style="border-bottom: none; border-top: none;">{{ $wf->user_id }}</td>
                                <td style="border-bottom: none; border-top: none;"><span class="shorter-text">{{ $wf->ts_task }}</span></td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->activity }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->RequestTo }}</td>
                                <td style="border-bottom: none; border-top: none;">{{ $wf->note }}</td>
                                @endif
                            </tr>
                            @endforeach
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td colspan="6" class="text-center">Copyright @ Author of ESS Perdana Consulting</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-4">
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
</div>
<input type="hidden" id="yearSel" value="{{ $year }}">
<input type="hidden" id="monthSel" value="{{ $month }}">
@endsection
