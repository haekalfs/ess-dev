@extends('leave.manage_leave_layout')

@section('manage-leave-user-info')
<div class="row zoom90">
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Employee Information</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse show" id="collapseCardExample">
                <div class="card-body">
                    <div class="col-md-12">
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
        </div>
    </div>

    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardProject" class="d-block card-header py-3" data-toggle="collapse"
                role="button" aria-expanded="true" aria-controls="collapseCardProject">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Leave Balance</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse show" id="collapseCardProject">
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
</div>
@endsection

@section('manage-leave-table')
<div class="card shadow mb-4 zoom90">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Employee's Leaves Requests</h6>
        {{-- <div class="text-right">
            <a data-toggle="modal" data-target="#addLeave" class="btn btn-primary btn-sm btn-edit"><i class="fas fa-fw fa-plus"></i> Add New</a>
        </div> --}}
    </div>
    <div class="card-body">
        <table class="table table-bordered zoom90" id="dataTableProject" width="100%" cellspacing="0">
            <thead class="thead-light">
                <tr>
                    <th>No</th>
                    <th>Request Date</th>
                    <th>Quota Used</th>
                    <th>Leave Days</th>
                    <th>Total</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody><?php $no = 1; ?>
                @foreach ($leaveRequests as $lr)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $lr->req_date }}</td>
                        <td><span>{{ $lr->leave->description }}</span></td>
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
                        <td>{{ $lr->total_days }} Days</td>
                        <td>{{ $lr->reason }}</td>
                        <td class="text-center" width="380px">
                            <a href="/leave/request/manage/id/{{$lr->req_by}}/{{ $month }}/{{$year}}/{{$lr->id}}/approve" class="btn btn-primary btn-sm mr-2"><i class="fas fa-fw fa-edit fa-sm text-white-50"></i> Approve</a>
                            <a href="/leave/request/manage/id/{{$lr->req_by}}/{{ $month }}/{{$year}}/{{$lr->id}}/reject" class="btn btn-danger btn-sm mr-2"><i class="fas fa-fw fa-edit fa-sm text-white-50"></i> Reject</a>
                            <a href="/leave/request/manage/id/{{$lr->req_by}}/{{ $month }}/{{$year}}/{{$lr->id}}" class="btn btn-success btn-sm"><i class="fas fa-fw fa-edit fa-sm text-white-50"></i> Manage Approval</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
