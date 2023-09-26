@extends('layouts.main')

@section('title', 'Reimbursement - ESS')

@section('active-page-reimburse')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800">Reimbursement History</h1>
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
<div class="card shadow mb-4">
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
                        <th>Purpose of Reimbursement</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reimbursement as $reimb)
                    <tr>
                        <td>{{ $reimb->f_id }}</td>
                        <td>{{ $reimb->created_at }}</td>
                        <td><span class="long-text">{{ $assign->f_purpose_of_purchase }}</span></td>
                        <td>@if($reimb->status_id == 40)
                            <span class="m-0 font-weight-bold text-danger">Waiting for Approval Service Diretor</span>
                            @elseif($reimb->status_id == 29) 
                            <span class="m-0 font-weight-bold text-primary">Approved by Service Director</span>
                            @else 
                            <span class="m-0 font-weight-bold text-danger">Rejected</span>
                            @endif
                        </td>
                        <td class="text-center"><a class="btn btn-primary btn-sm" href="/assignment/member/{{ $reimb->id }}"><i class='fas fa-fw fa-eye'></i> View</a></td>
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
<script src="{{ asset('js/project.js') }}"></script>
@endsection