@extends('layouts.main')

@section('active-page-approval')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-2 text-gray-800">Approval</h1>
        <p class="mb-4">Approval Page.</p>
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
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">PO/PR in Queue</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>Requestor</th>
                        <th>Purpose of Purchase</th>
                        <th>Status</th>
                        <th>Created at</th>
                        <th>Action</th>
                </thead>
                <tfoot>
                    <tr>
                        <th>PO Number</th>
                        <th>Requestor</th>
                        <th>Purpose of Purchase</th>
                        <th>Status</th>
                        <th>Created at</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    <tr>
                        
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
