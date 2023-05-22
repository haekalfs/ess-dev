@extends('layouts.main')

@section('title', 'Leave History - ESS')

@section('active-page-leave')
active
@endsection

@section('css-js-if-exist')
<script src="http://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet"/>
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800">Leave History</h1>
    <div>
        <select class="form-control" id="yearSelected" name="yearSelected" required onchange="redirectToPageAssignment()">
            @foreach (array_reverse($yearsBefore) as $year)
                <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
        </select>
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
                        <td style="text-align: start; font-weight:500">: {{Auth::user()->users_detail->hired_date}}</td>
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
                          <td style="text-align: start; font-weight:500">: {{ $empLeaveQuotaAnnual }}</td>
                      </tr>
                      <tr>
                          <th>5 Year Term</th>
                          <td style="text-align: start; font-weight:500">: {{ $empLeaveQuotaFiveYearTerm }}</td>
                      </tr>
                      <tr>
                          <th>Weekend Replacement</th>
                          <td style="text-align: start; font-weight:500">: N/A</td>
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
            <a class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#leaveRequest" id="leaveRequestBtn" style="margin-right: 5px;">Create Request</a>
            <button class="btn @role('freelancer') btn-success @else btn-secondary @endrole btn-sm" type="button" data-toggle="modal" data-target=".leaveQuota" id="manButton">Leave Quota</button>
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
                            <td>{{ $lr->req_date }}</td>
                            <td>{{ $lr->leave->description }}</td>
                            <td>
                                @foreach ($lr->dateGroups as $key => $group)
                                    @if ($key > 0)
                                        -
                                    @endif
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
                                    <a href="/leave/history/cancel/{{ $lr->id }}" class="btn btn-danger btn-sm">
                                        <i class="fas fa-fw fa-ban fa-sm text-white-50"></i> Cancel Request
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
                            {{-- <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Date :</label>
                                    <input type="text" class="form-control" name="daterangeLeave" id="daterangeLeave"/>
                                </div>
                            </div> --}}
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
<div class="modal fade leaveQuota" tabindex="-1" role="dialog" aria-labelledby="leaveQuota" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Leave Quota Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
            <div class="modal-body" style="height: 500px; overflow-y: auto;">
                <div class="col-md-12 zoom90">
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="h5 mb-2 text-gray-800">Leave Detail Information <small style="color: red;"> : &nbsp;&nbsp; {{ Auth::user()->name }}</i></small></h6><br>
                            <div class="table-responsive">
                                <table class="table table-bordered zoom90" id="dataTableProject" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Leave ID</th>
                                            <th>Active Periode</th>
                                            <th>Expired On</th>
                                            <th>Quota</th>
                                            <th>Quota Used</th>
                                            <th>Quota Left</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leaveRequests as $lr)
                                            <tr>
                                                <td>{{ $lr->leave->description }}</td>
                                                <td>{{ $lr->leave->description }}</td>
                                                <td>
                                                    @foreach ($lr->dateGroups as $key => $group)
                                                        @if ($key > 0)
                                                            -
                                                        @endif
                                                        {{ implode(',', $group['dates']) }} {{ $group['monthYear'] }}
                                                    @endforeach
                                                </td>
                                                <td>{{ $lr->total_days }}</td>
                                                <td>{{ $lr->reason }}</td>
                                                <td>{!! $lr->approvalStatus !!}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div><br>
                        </div>
                        <div class="col-md-12">
                            <h6 class="h6 text-danger mb-2"><i>Weekend Replacement</i></h6>
                            <div class="table-responsive">
                                <table class="table table-bordered zoom90" id="listAssignments" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Leave ID</th>
                                            <th>Active Periode</th>
                                            <th>Expired On</th>
                                            <th>Quota</th>
                                            <th>Quota Used</th>
                                            <th>Quota Left</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leaveRequests as $lr)
                                            <tr>
                                                <td>{{ $lr->leave->description }}</td>
                                                <td>{{ $lr->leave->description }}</td>
                                                <td>
                                                    @foreach ($lr->dateGroups as $key => $group)
                                                        @if ($key > 0)
                                                            -
                                                        @endif
                                                        {{ implode(',', $group['dates']) }} {{ $group['monthYear'] }}
                                                    @endforeach
                                                </td>
                                                <td>{{ $lr->total_days }}</td>
                                                <td>{{ $lr->reason }}</td>
                                                <td>{!! $lr->approvalStatus !!}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-bottom-1">
          <h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Leave Request Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
            <div class="modal-body" style="height: 270px; overflow-y: auto;">
                <div class="col-md-12 zoom90">
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="h5 mb-2 text-gray-800">Waiting For Approval : <span class="text-danger" id="approver"></span></i></h6>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
                            </div><br>
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
                                            <th>Request To</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody id="leaveRequestDetails">
                                        <!-- Ajax Data -->
                                    </tbody>
                                </table>
                            </div><br>
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
<script>
    $('.date').datepicker({
    multidate: true
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
    width: 195px;
}
</style>
@endsection
