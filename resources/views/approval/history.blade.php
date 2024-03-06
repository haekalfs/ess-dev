@extends('layouts.main')

@section('title', 'Approval - ESS')

@section('active-page-approval')
active
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 zoom90 text-gray-800 font-weight-bold"><i class="fas fa-calendar"></i> Approvals History</h1>
<p class="mb-4">This section displays the approvals and submissions history.</p>

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

@if ($message = Session::get('marquee'))
<div class="alert alert-danger alert-block text-center">
    <button type="button" class="close" data-dismiss="alert" style="opacity: 0.1;">×</button>
    <strong>{!! $message !!}</strong>
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
                        <th>No.</th>
                        <th>Message</th>
                        <th>Intended for</th>
                        <th>At</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach ($history as $approval)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $approval->message }}</td>
                        <td>{{ $approval->intended_for }}</td>
                        <td>{{ $approval->created_at->format('d-M-Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
.action{
    width: 190px;
}
</style>
@endsection
