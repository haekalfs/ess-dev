@extends('layouts.main')

@section('title', 'Leave History - ESS')

@section('active-page-leave')
active
@endsection

@section('css-js-if-exist')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet"/>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800"><i class="fas fa-plane-departure"></i> Leave History</h1>
    <div>
        {{-- <select class="form-control" id="yearSelected" name="yearSelected" required onchange="redirectToPageAssignment()">
            @foreach (array_reverse($yearsBefore) as $year)
                <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
        </select> --}}
        <button class="btn @role('freelancer') btn-success @else btn-primary @endrole btn-sm" type="button" data-toggle="modal" data-target=".leaveUsage" id="manButton" style="margin-right: 5px;"><i class="fas fa-eye"></i> Leave Information</button>
    </div>
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

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
<div class="row zoom90">
    <!-- Area Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Employee Information</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th>User ID</th>
                        <td style="text-align: start; font-weight:500">: {{Auth::user()->id}}</td>
                    </tr>
                    <tr>
                        <th>Employee ID</th>
                        <td style="text-align: start; font-weight:500">: {{Auth::user()->users_detail->employee_id}}</td>
                    </tr>
                    <tr>
                        <th>Full Name</th>
                        <td style="text-align: start; font-weight:500">: {{Auth::user()->name}}</td>
                    </tr>
                    <tr>
                        <th>Hired Date</th>
                        <td style="text-align: start; font-weight:500">: {{ \Carbon\Carbon::createFromFormat('Y-m-d', Auth::user()->users_detail->hired_date)->format('d-M-Y') }}</td>
                    </tr>
                  </tr>
              </table>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Leave Balance</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                    <tr>
                      <tr>
                          <th width="300px">Leaves Balance</th>
                          <td style="text-align: start; font-weight:500">: {{ $empLeaveQuotaAnnualSum }}</td>
                      </tr>
                      <tr>
                          <th>Leave Quota Used</th>
                          <td style="text-align: start; font-weight:500">: {{ $empLeaveQuotaFiveYearTerm }}</td>
                      </tr>
                      <tr>
                          <th>Weekend Replacement</th>
                          <td style="text-align: start; font-weight:500">: {{ $empLeaveQuotaWeekendReplacement }}</td>
                      </tr>
                      <tr>
                          <th>Total Leave Available</th>
                          <td style="text-align: start; font-weight:500">: {{ $totalQuota }}</td>
                      </tr>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole" id="judul">Leave History</h6>
        <div class="text-right">
            <a class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#leaveRequest" id="leaveRequestBtn">Create Request</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="myLeave" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Request Date</th>
                        <th>Qouta Used</th>
                        <th>Leave Dates</th>
                        <th>Total Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th width='120px'>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leaveRequests as $lr)
                        <tr>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $lr->req_date)->format('d-M-Y') }}</td>
                            <td>{{ $lr->leave->description }}</td>
                            <td>
                                @foreach ($lr->dateGroups as $key => $group)
                                    @if ($key > 0)
                                        -
                                    @endif
                                    @php
                                    asort($group['dates']);
                                    @endphp
                                    {{ implode(',', $group['dates']) }} {{ $group['monthYear'] }}
                                @endforeach
                            </td>
                            <td>{{ $lr->total_days }}</td>
                            <td>{{ $lr->reason }}</td>
                            <td>{!! $lr->approvalStatus !!}</td>
                            <td class="action text-center">
                                @php
                                    $approved = false;
                                @endphp

                                @foreach ($lr->leave_request_approval as $stat)
                                    @if ($stat->status == 29 || $stat->status == 20 || $stat->status == 404)
                                        <a class="btn btn-secondary btn-sm" data-toggle="modal" and data-target="#leaveRequestDetailModal" data-id="{{ $lr->id }}">
                                            <i class="fas fa-fw fa-eye fa-sm text-white-50"></i> View Details
                                        </a>
                                        @php
                                            $approved = true;
                                            break;
                                        @endphp
                                    @endif
                                @endforeach

                                @unless ($approved)
                                    <a class="btn btn-secondary btn-sm" data-toggle="modal" and data-target="#leaveRequestDetailModal" data-id="{{ $lr->id }}">
                                        <i class="fas fa-fw fa-eye fa-sm text-white-50"></i> Details
                                    </a>
                                    <a href="/leave/history/cancel/{{ $lr->id }}" class="btn btn-danger btn-sm">
                                        <i class="fas fa-fw fa-ban fa-sm text-white-50"></i> Cancel
                                    </a>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="leaveRequest" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">New Leave Request<a id="entry-date-update"></a></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="leave-request" action="{{ route('leave.entry') }}">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Date :</label>
                                    <input type="text" class="form-control date" name="datepickLeave" id="datepickLeave" autocomplete="off" placeholder="mm/dd/YYYY" onblur="calculateTotalDays()"/>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Quota Used :</label>
                                    <select class="form-control" name="quota_used" required>
                                        @foreach($leaveType as $l)
                                        <option value="{{$l->id}}">{{ $l->description}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">CP During Leave :</label>
                                    <input type="text" class="form-control" required autocomplete="off" name="cp_number" id="cp_number" placeholder="083818XXXX">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Total Leave Days :</label>
                                    <input type="number" class="form-control" required autocomplete="off" name="total_days" id="total_days" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Reason :</label>
                                    <textarea type="text" class="form-control" name="reason" required></textarea>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                  </div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade leaveUsage" tabindex="-1" role="dialog" aria-labelledby="leaveQuota" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Leave Quota Information</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
            <ul class="nav nav-tabs" id="pageTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="page1-tab" data-toggle="tab" href="#page1" role="tab" aria-controls="page1" aria-selected="true"><i class="fas fa-calendar-day"></i> Quota</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="page2-tab" data-toggle="tab" href="#page2" role="tab" aria-controls="page2" aria-selected="false"><i class="fas fa-plane-departure"></i> Usage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="page3-tab" data-toggle="tab" href="#page3" role="tab" aria-controls="page3" aria-selected="false"><i class="fas fa-calendar-week" style="color: #ff0000;"></i> Replacement</a>
                </li>
            </ul>
            <div class="modal-body" style="height: 400px; overflow-y: auto;">
                <div class="tab-content" id="pageTabContent">
                    <div class="tab-pane fade show active" id="page1" role="tabpanel" aria-labelledby="page1-tab">
                        <div class="col-md-12 zoom90">
                            <div class="row">
                                <div class="col-md-12">
                                    {{-- <h6 class="h6 text-danger mb-2"><i>Leave Quota Information</i></h6> --}}
                                    <div class="table-responsive">
                                        <table class="table table-bordered zoom90" id="dataTableProject" width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Leave ID</th>
                                                    <th>Active Periode</th>
                                                    <th>Expired On</th>
                                                    <th>Status</th>
                                                    <th>Initial Quota</th>
                                                    <th>Quota Used</th>
                                                    <th>Quota Left</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($leaveQuotaAnnual as $lqa)
                                                    <tr>
                                                        <td>{{ $lqa->leave->description }}</td>
                                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $lqa->active_periode)->format('d-M-Y') }}</td>
                                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $lqa->expiration)->format('d-M-Y') }}</td>
                                                        <td><?php
                                                        if($lqa->expiration < date('Y-m-d')){
                                                            echo '<h6 class="h6 text-danger mb-2"><i>Expired</i></h6>';
                                                        } else {
                                                            echo '<h6 class="h6 text-primary mb-2"><i>Active</i></h6>';
                                                        }
                                                        ?></td>
                                                        <td>{{ $lqa->leave->leave_quota }}</td>
                                                        <td>{{ $lqa->quota_used }}</td>
                                                        <td>{{ $lqa->quota_left }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div><br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="page2" role="tabpanel" aria-labelledby="page2-tab">
                        <div class="col-md-12 zoom90">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered zoom90" id="listAssignments" width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Date Created</th>
                                                    <th>Quota Used</th>
                                                    <th>Total Days</th>
                                                    <th>Deducted Quota</th>
                                                    <th>Quota Left</th>
                                                    <th>Description</th>
                                                    {{-- <th>Active Periode</th>
                                                    <th>Expiration</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($leaveQuotaUsage as $lqu)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $lqu->req_date)->format('d-M-Y') }}</td>
                                                        <td>{{ $lqu->leave->description }}</td>
                                                        <td>{{ $lqu->requested_days }}</td>
                                                        <td>- {{ $lqu->quota_used }}</td>
                                                        <td>{{ $lqu->quota_left }}</td>
                                                        <td>{{ $lqu->description }}</td>
                                                        {{-- <td>{{ $lqu->emp_leave_quota->active_periode }}</td>
                                                        <td>{{ $lqu->emp_leave_quota->expiration }}</td> --}}
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div><br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="page3" role="tabpanel" aria-labelledby="page3-tab">
                        <div class="col-md-12 zoom90">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered zoom90" id="weekendReplacement" width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Leave ID</th>
                                                    <th>Active Periode</th>
                                                    <th>Expired On</th>
                                                    <th>Status</th>
                                                    <th>Quota</th>
                                                    <th>Quota Used</th>
                                                    <th>Quota Left</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($weekendReplacementQuota as $wrq)
                                                    <tr>
                                                        <td>{{ $wrq->leave->description }}</td>
                                                        <td>{{ $wrq->active_periode }}</td>
                                                        <td>{{ $wrq->expiration }}</td>
                                                        <td><?php
                                                            if($wrq->expiration < date('Y-m-d')){
                                                                echo '<h6 class="h6 text-danger mb-2"><i>Expired</i></h6>';
                                                            } else {
                                                                echo '<h6 class="h6 text-primary mb-2"><i>Active</i></h6>';
                                                            }
                                                        ?></td>
                                                        <td>{{ $wrq->quota_left }}</td>
                                                        <td>{{ $wrq->quota_used }}</td>
                                                        <td>{{ $wrq->quota_left }}</td>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="leaveRequestDetailModal" tabindex="-1" role="dialog" aria-labelledby="leaveRequestDetailModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-bottom-1">
          <h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Leave Request Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
            <div class="modal-body zoom90" style="height: 400px; overflow-y: auto;">
                <h6 class="h5 mb-2 text-gray-800">Approval Status : <span class="text-primary" id="approver"></span></i></h6>
                <div class="table-responsive">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <table style="border: none;">
                                    <tbody>
                                        <tr>
                                            <td class="bold">Request By</td>
                                            <td width="10px">:</td>
                                            <td><input class="form-control transparent-input" type="text" id="request_by_detail" value="" disabled></td>
                                        </tr>
                                        <tr>
                                            <td class="bold">Leave Quota Used</td>
                                            <td width="10px">:</td>
                                            <td><input class="form-control transparent-input" type="text" id="quota_used_detail" value="" disabled></td>
                                        </tr>
                                        <tr>
                                            <td class="bold">Leave Dates</td>
                                            <td width="10px">:</td>
                                            <td><input class="form-control transparent-input" type="text" id="leave_dates_detail" value="" disabled></td>
                                        </tr>
                                        <tr>
                                            <td class="bold">Reason</td>
                                            <td width="10px">:</td>
                                            <td><input class="form-control transparent-input" type="text" id="reason_detail" value="" disabled></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table style="border: none;">
                                    <tbody>
                                        <tr>
                                            <td class="bold">Deducted Quota</td>
                                            <td width="10px">:</td>
                                            <td><input class="form-control transparent-input" type="text" id="total_days_detail" value="" disabled></td>
                                        </tr>
                                        <tr>
                                            <td class="bold">Lead Approver</td>
                                            <td width="10px">:</td>
                                            <td><input class="form-control transparent-input" type="text" id="approver_detail" value="" disabled></td>
                                        </tr>
                                        <tr>
                                            <td class="bold">Request Date</td>
                                            <td width="10px">:</td>
                                            <td><input class="form-control transparent-input" type="text" id="request_date_detail" value="" disabled></td>
                                        </tr>
                                        <tr>
                                            <td class="bold">Last Updated</td>
                                            <td width="10px">:</td>
                                            <td><input class="form-control transparent-input" type="text" id="last_updated_detail" value="" disabled></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div><br>
                    </div>
                    <table class="table table-bordered zoom90" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Request To</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody id="leaveRequestDetails">
                            <!-- Ajax Data -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
var now = new Date();
var startDate = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 7);

$('.date').datepicker({
    multidate: true,
    startDate: startDate,
    daysOfWeekDisabled: "0,6"
});
function calculateTotalDays() {
        var dateInput = document.getElementById('datepickLeave');
        var selectedDates = dateInput.value.split(',');

        var totalDays = 0;
        for (var i = 0; i < selectedDates.length; i++) {
            var currentDate = new Date(selectedDates[i]);
            totalDays++;
        }

        document.getElementById('total_days').value = totalDays;
    }
</script>
<style>
.action{
    width: 205px;
}
.transparent-input {
	background-color:transparent !important;
	border:none !important;
}
</style>
@endsection

@section('javascript')
<script src="{{ asset('js/leave.js') }}"></script>
@endsection
