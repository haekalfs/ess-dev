@extends('layouts.main')

@section('title', 'Reimbursement Approval - ESS')

@section('active-page-approval')
active
@endsection

@section('content')
<div class="zoom90 d-sm-flex align-items-center justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-hand-holding-usd"></i> Reimbursement Approval</h1>
        <p class="mb-4">Approval Page.</p>
    </div>
    <div>
        <select class="form-control" id="year" name="year" required onchange="redirectToPage()">
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
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Approval History</h6>
        <div class="text-right">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="dataTable1" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Form ID</th>
                        <th>Request Date</th>
                        <th>Purpose of Reimbursement</th>
                        <th>Requested By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($approvals as $approval)
                    <tr>
                        <td>{{ $approval->request->f_id }}</td>
                        <td>{{ $approval->request->created_at }}</td>
                        <td><span class="long-text">{{ $approval->request->f_type }}</span></td>
                        <td>{{ $approval->request->user->name }}</td>
                        <td class="text-center">
                            <a class="btn btn-primary btn-sm mr-2" href="/approval/reimburse/view/{{ $approval->request->id }}"><i class='fas fa-fw fa-eye'></i> View</a>
                            {{-- <a class="btn btn-secondary btn-sm" href="/approval/reimburse/view/{{ $approval->request->id }}"><i class="fas fa-paper-plane"></i> Handover</a> --}}
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
<script>
    function redirectToPage() {
        var selectedOption = document.getElementById("year").value;
        var url = "{{ url('/approval/reimburse') }}"; // Specify the base URL

        url += "/" + selectedOption;

        window.location.href = url; // Redirect to the desired page
    }
</script>
@endsection
