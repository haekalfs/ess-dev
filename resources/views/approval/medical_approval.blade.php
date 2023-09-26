@extends('layouts.main')

@section('title', 'Medical Approval - ESS')

@section('active-page-approval')
active
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Medical Approval</h1>
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
                        <th>Request Number</th>
                        <th>Request Date</th>
                        <th>Request By</th>
                        <th>Payment</th>
                        {{-- <th>Status</th>  --}}
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($medical as $med)
                    <tr>
                        <td>MED_0000{{ $med->medical->med_number }}</td>
                        <td>{{ $med->medical->med_req_date }}</td>
                        <td>{{ $med->medical->user->name }}</td>
                        <td>{{ $med->medical->med_payment }}</td>
                        {{-- <td>{{ $med->med_status }}</td> --}}
                        <td class="row-cols-2 justify-content-betwen text-center">
                            <a href="/medical/approval/{{ $med->medical_id }}" title="Edit" class="btn btn-primary btn-sm" >
                                <i class="fas fa-fw fa-eye justify-content-center"></i> View
                            </a>
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
