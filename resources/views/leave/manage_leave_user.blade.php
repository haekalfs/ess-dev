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
                        <td style="text-align: start; font-weight:500">: {{ $user_info->id}}</td>
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
@endsection

@section('manage-leave-table')
<table class="table table-bordered zoom80" id="dataTableProject" width="100%" cellspacing="0">
    <thead class="thead-light">
        <tr>
            <th>No</th>
            <th>Leave ID</th>
            <th>Quota</th>
            <th>Active Periode</th>
            <th>Quota Left</th>
            <th>Quota Used</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($empLeaves as $empLeave)
        <tr>
            <td>{{ $empLeave->id }}</td>
            <td>{{ $empLeave->leave->description }}</td>
            <td><span class="long-text">{{ $empLeave->leave->description}}</span></td>
            <td>{{ $empLeave->leave->description}}</td>
            <td>{{ $empLeave->leave->description}}</td>
            <td>{{ $empLeave->leave->description}}</td>
            <td class="text-center"><a class="btn btn-primary btn-sm" href="/project_list/view/details/{{$empLeave->id}}"><i class='fas fa-fw fa-eye'></i> View</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection