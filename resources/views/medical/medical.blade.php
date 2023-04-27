@extends('layouts.main')

@section('active-page-medicals')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800">Medical Reimburse</h1>
    {{-- <a data-toggle="modal" data-target="#addModal" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> New Assignment</a> --}}
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
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Medical History</h6>
        <div class="text-right">
            <a href="/medical/entry" class="btn btn-primary btn-sm">New Request</a>
            <a href="" class="btn btn-primary btn-sm">View Report</a>
        </div>
    </div>
    <!-- Card Body -->
    <div class="card-body zoom90">
        <table class="table table-bordered table-hover " id="dataTable">
                <thead>
                    <tr style="font-size: 13px" class="text-center">
                        <th>Request Number</th>
                        <th>Request Date</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th width="120px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($med as $q)
                    <tr style="font-size: 12px">
                        <td>{{$q->med_number}}</td>
                        <td>{{$q->med_date}}</td>
                        <td>{{$q->med_payment}}</td>
                        <td>{{$q->med_status}}</td>
                        <td class="row-cols-2 justify-content-betwen text-center">
                            <a href="#" title="Edit" class="btn btn-warning btn-sm" >
                                <i class="fas fa-fw fa-edit justify-content-center"></i>
                            </a>
                            <a href="#" title="Hapus" class="btn btn-danger btn-sm" ><i class="fas fa-fw fa-bars justify-content"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
        </table>
    </div>
</div>

@endsection