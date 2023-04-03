@extends('layouts.main')

@section('active-page-medicals')
active
@endsection

@section('content')
<!-- Page Heading -->

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

<div class="container">
    <div class="card mt-5 d-flex p-2">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h5 class="m-0 font-weight-bold text-primary">Medical History</h5>
        </div>
        <div class="card-body">
            <a href="/medical/entry" class="btn btn-primary btn-sm">New Request</a>
            <a href="" class="btn btn-primary btn-sm">View Report</a>
            <br/>
            <br/>
            <table class="table table-bordered table-hover table-striped" id="dataTable">
                <thead>
                    <tr style="font-size: 13px" class="text-center">
                        <th>Request Number</th>
                        <th>Request Date</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($med as $q)
                    <tr style="font-size: 12px">
                        <td>{{$q->med_number}}</td>
                        <td>{{$q->med_date}}</td>
                        <td>{{$q->med_payment}}</td>
                        <td>{{$q->med_status}}</td>
                        <td class="row-cols-2 justify-content-betwen" style="align-items: center">
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
</div>
@endsection