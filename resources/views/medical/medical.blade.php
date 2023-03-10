@extends('layouts.main')

@section('active-page-myprofile')
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
        <div class="card-header text-center">
            <h3>Medical History</h3>
        </div>
        <div class="card-body">
            <a href="/medical/entry" class="btn btn-primary">New Request</a>
            <a href="" class="btn btn-primary">View Report</a>
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
                        <td>{{$q->req_number}}</td>
                        <td>{{$q->req_date}}</td>
                        <td>{{$q->payment}}</td>
                        <td>{{$q->status}}</td>
                        <td class="row-cols-2 justify-content-betwen">
                            <a href="/users/edit/" title="Edit" class="btn btn-warning btn-sm" >
                                <i class="fas fa-fw fa-edit justify-content-center"></i>
                            </a>
                            <a href="/users/hapus/" title="Hapus" class="btn btn-danger btn-sm" ><i class="fas fa-fw fa-bars justify-content"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection