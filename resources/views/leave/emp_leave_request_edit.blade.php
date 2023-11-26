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
<form method="post" action="/leave/request/manage/id/{{$id}}/update" id="myForm">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
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
                        <th>Approver</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody><?php $no = 1; ?>
                    @foreach($leaveRequests->leave_request_approval as $lra)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>
                                <div class="form-group">
                                    <select class="form-control transparent-input" name="items[{{ $lra->id }}][approver]" >
                                        <option selected disabled>Choose...</option>
                                        @foreach($usersList as $us)
                                            <option value="{{ $us->id }}" @if( $us->id == $lra->RequestTo ) selected @endif >{{ $us->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <select class="form-control transparent-input" name="items[{{ $lra->id }}][status]" >
                                        <option selected disabled>Choose...</option>
                                        @foreach($statusDesc as $stat)
                                            <option value="{{ $stat->approval_status_id }}" @if( $stat->approval_status_id == $lra->status ) selected @endif >{{ $stat->status_desc }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="/leave/request/manage/id/{{$lra->id}}/delete" class="btn btn-danger btn-sm mr-2"><i class="fas fa-fw fa-trash-alt fa-sm text-white-50"></i> Delete</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button class="br-icon"></button>
    </div>
</form>

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

<style type="text/css">
    body
    {
        background-color: #d4d2d2;
    }
    .br-icon
    {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 100;
        height: 75px;
        width: 75px;
        border-radius: 100%;
        background-color: #1e43e9;
        box-shadow: 2px 2px 10px 1px rgba(0,0,0,0.58);
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-transform: scale(0.92);
        transform: scale(0.92);
    }
    .br-icon::before {
        content: "\f0c7";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        font-size: 28px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #fff;
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


