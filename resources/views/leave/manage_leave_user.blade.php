@extends('leave.manage_leave_layout')

@section('manage-leave-user-info')
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
                        <td style="text-align: start; font-weight:500">: {{ $user_info->id }}</td>
                    </tr>
                    <tr>
                        <th>Employee ID</th>
                        <td style="text-align: start; font-weight:500">: {{$user_info->users_detail->employee_id}}</td>
                    </tr>
                    <tr>
                        <th>Full Name</th>
                        <td style="text-align: start; font-weight:500">: {{$user_info->name}}</td>
                    </tr>
                    <tr>
                        <th>Hired Date</th>
                        <td style="text-align: start; font-weight:500">: {{$user_info->users_detail->hired_date}}</td>
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
@endsection

@section('manage-leave-table')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Leaves Information</h6>
        <div class="text-right">
            <a data-toggle="modal" data-target="#addLeave" class="btn btn-primary btn-sm btn-edit"><i class="fas fa-fw fa-plus"></i> Add New</a>
        </div>
    </div>
    <div class="card-body">
    <table class="table table-bordered zoom80" id="dataTableProject" width="100%" cellspacing="0">
        <thead class="thead-light">
            <tr>
                <th>No</th>
                <th>Leave ID</th>
                <th>Default Quota</th>
                <th>Active Periode</th>
                <th>Expiration</th>
                <th>Status</th>
                <th>Quota Left</th>
                <th>Quota Used</th>
                <th>Action</th>
            </tr>
        </thead> 
        <tbody><?php $no = 1; ?>
            @foreach($empLeaves as $empLeave)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $empLeave->leave->description }}</td>
                <td><span class="long-text">{{ $empLeave->leave->leave_quota }}</span></td>
                <td>{{ $empLeave->active_periode }}</td>
                <td>{{ $empLeave->expiration }}</td>
                <td><?php
                    if($empLeave->expiration < date('Y-m-d')){
                        echo '<h6 class="h6 text-danger mb-2"><i>Expired</i></h6>';
                    } else {
                        echo '<h6 class="h6 text-primary mb-2"><i>Active</i></h6>';
                    }
                ?></td>
                <td>{{ $empLeave->quota_left }}</td>
                <td>{{ $empLeave->quota_used }}</td>
                <td class="text-center">
                    <a data-toggle="modal" data-target="#updateLeave" data-id="{{ $empLeave->id }}" class="btn btn-primary btn-sm"><i class="fas fa-fw fa-edit fa-sm text-white-50"></i> Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

<div class="modal fade" id="updateLeave" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="updateLeaveLabel">Edit Leave Quota</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="update-leave">
                @csrf
				<div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Active Periode :</label>
                                    <input type="date" class="form-control" required autocomplete="off" name="active_periode" id="active_periode">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Expiration :</label>
                                    <input type="date" class="form-control" required autocomplete="off" name="expiration" id="expiration">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Qouta Left :</label>
                                    <input type="number" class="form-control" required autocomplete="off" name="quota_left" id="quota_left">
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="update-leave-btn" class="btn btn-primary" data-dismiss="modal">Save changes</button>
                  </div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="addLeave" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="addLeaveLabel">Insert New Quota</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="add-leave" action="/leave/manage/add_quota/{{$user_info->id}}">
                @csrf
				<div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Quota Type:</label>
                                    <select class="form-control" name="addLeaveQuotaType" required>
                                        @foreach($leaveType as $l)
                                        <option value="{{$l->id}}">{{ $l->description}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="leaveStatus" id="leaveStatus">
                                        <label class="custom-control-label" for="leaveStatus">Once in a Service Years?</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Active Periode :</label>
                                    <input type="date" class="form-control" required autocomplete="off" name="addLeaveActivePeriode" id="addLeaveActivePeriode">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Expiration :</label>
                                    <input type="date" class="form-control" required autocomplete="off" name="addLeaveExpiration" id="addLeaveExpiration">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Quota :</label>
                                    <input type="number" class="form-control" required autocomplete="off" name="addLeaveQuota" id="addLeaveQuota">
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="add-leave-btn" class="btn btn-primary">Save changes</button>
                  </div>
			</form>
		</div>
	</div>
</div>

<!-- The success popup modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <span class="m-0 font-weight-bold text-success">Employee Leave updated successfully.</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ asset('js/leave.js') }}"></script>
@endsection