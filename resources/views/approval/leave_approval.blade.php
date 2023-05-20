@extends('layouts.main')

@section('title', 'Leave Approval - ESS')

@section('active-page-approval')
active
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Leave Approval</h1>
<p class="mb-4">Approval Page.</p>
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

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Approval History</h6>
        <div class="text-right">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="dataTable1" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Request Date</th>
                        <th>Qouta Used</th>
                        <th>Leave Dates</th>
                        <th>Total Days</th>
                        <th>Reason</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($approvals as $approval)
                    <tr>
                        <td>{{ $approval->leave_request->req_by }}</td>
                        <td>{{ \Carbon\Carbon::parse($approval->created_at)->format('d-M-Y') }}</td>
                        <td>{{ $approval->leave_request->leave->description }}</td>
                        <td>
                            @php
                                $dates = explode(',', $approval->leave_request->leave_dates);
                                $currentMonth = null;
                                $dateGroups = [];
                                $group = [];
                                foreach ($dates as $date) {
                                    $formattedDate = date('d', strtotime($date));
                                    $monthYear = date('F Y', strtotime($date));
                                    
                                    if ($currentMonth !== $monthYear) {
                                        if (!empty($group)) {
                                            $dateGroups[] = $group;
                                            $group = [];
                                        }
                                        $group['monthYear'] = $monthYear;
                                        $group['dates'] = [$formattedDate];
                                        $currentMonth = $monthYear;
                                    } else {
                                        $group['dates'][] = $formattedDate;
                                    }
                                }
                                if (!empty($group)) {
                                    $dateGroups[] = $group;
                                }
                            @endphp
                        
                            @foreach ($dateGroups as $key => $group)
                                @if ($key > 0)
                                    -
                                @endif
                                {{ implode(',', $group['dates']) }} {{ $group['monthYear'] }}
                            @endforeach
                        </td>
                        <td>{{ $approval->leave_request->total_days }}</td>
                        <td><span class="long-text">{{ $approval->leave_request->reason }}</span></td>
                        <td class="action text-center">
                            <div class="btn-group">
                                <button class="btn btn-primary btn-sm dropdown-toggle" style="margin-right: 5px;" type="button" id="approveDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-fw fa-check fa-sm text-white-50"></i> Approve
                                </button>
                                <div class="dropdown-menu" aria-labelledby="approveDropdown">
                                    <form action="/approval/leave/approve/{{ $approval->id }}" method="post">
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
                                    <form action="/approval/leave/reject/{{ $approval->id }}" method="post">
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
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
.action{
    width: 300px;
}
</style>
@endsection
