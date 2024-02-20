@extends('layouts.main')

@section('title', 'Reimbursement - ESS')

@section('active-page-reimburse')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4 zoom90">
    <h1 class="h4 mb-2 font-weight-bold text-gray-800"><i class="fas fa-hand-holding-usd"></i> Reimbursement History</h1>
    <div>
        <select class="form-control" id="yearSelected" name="yearSelected" required onchange="redirectToPageAssignment()">
            @foreach (array_reverse($yearsBefore) as $year)
                <option value="{{ $year }}" @if ($year == $yearSelected) selected @endif>{{ $year }}</option>
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
<div class="card shadow mb-4 zoom90">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Reimbursement Request History</h6>
        <div class="text-right">
            <a href="/reimbursement/create/request" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> New Request</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="listAssignments" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Form ID</th>
                        <th>Request Date</th>
                        <th>Type of Reimbursement</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reimbursement as $reimb)
                    <tr>
                        <td>{{ $reimb->f_id }}</td>
                        <td>{{ $reimb->created_at }}</td>
                        <td><span class="long-text">{{ $reimb->f_type }}</span></td>
                        <td>
                            @if($reimb->status_id == 20)
                            <span class="m-0 font-weight-bold text-secondary"><i class="fas fa-spinner fa-spin"></i> Waiting for Approval</span>
                            @elseif($reimb->status_id == 30)
                            <span class="m-0 font-weight-bold" style="color: #00d5ff;"><i class="fas fa-check-circle" style="color: #00d5ff;"></i> Partially Approved</span>
                            @elseif($reimb->status_id == 29)
                            <span class="m-0 font-weight-bold text-primary"><i class="fas fa-check-circle" style="color: #005eff;"></i> Completed</span>
                            @elseif($reimb->status_id == 2002)
                            <span class="m-0 font-weight-bold text-success"><i class="fas fa-check-circle" style="color: #01e476;"></i> Paid</span>
                            @else
                            <span class="m-0 font-weight-bold text-danger"><i class="fas fa-times-circle" style="color: #ff0000;"></i> Rejected</span>
                            @endif
                        </td>
                        {{-- <td class="text-center">
                            <a class="btn btn-primary btn-sm" href="/reimbursement/view/{{ $reimb->id }}"><i class='fas fa-fw fa-eye'></i> View</a>
                            <a href="/reimbursement/history/cancel/{{ $reimb->id }}" class="btn btn-danger btn-sm">
                                <i class="fas fa-fw fa-ban fa-sm text-white-50"></i> Cancel
                            </a>
                        </td> --}}
                        <td class="text-center" style="width: 290px;">
                            @php
                                $approved = false;
                            @endphp

                            @foreach ($reimb->approval as $status)
                                @if ($status->status == 29 || $status->status == 30 || $status->status == 404)
                                    <a class="btn btn-primary btn-sm" href="/reimbursement/view/{{ $reimb->id }}"><i class='fas fa-fw fa-eye'></i> View</a>
                                    @php
                                        $approved = true;
                                        break;
                                    @endphp
                                @endif
                            @endforeach

                            @unless ($approved)
                                <a class="btn btn-primary btn-sm mr-2" href="/reimbursement/view/{{ $reimb->id }}"><i class='fas fa-fw fa-eye'></i> View</a>
                                <a href="#" onclick="deleteRequest(event, {{ $reimb->id }})" class="btn btn-danger btn-sm">
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
<style>
.action{
    width: 180px;
}
</style>
<script>
    function redirectToPageAssignment() {
        var selectedOption = document.getElementById("yearSelected").value;
        var url = "{{ url('/reimbursement/history') }}"; // Specify the base URL

        url += "/" + selectedOption;

        window.location.href = url; // Redirect to the desired page
    }
</script>
@endsection

@section('javascript')
<script src="{{ asset('js/reimburse.js') }}"></script>
@endsection
