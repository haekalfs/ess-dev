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
<div class="row zoom90">
    <!-- Area Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Employee Information</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th>User ID</th>
                        <td style="text-align: start; font-weight:500">: {{Auth::user()->id}}</td>
                    </tr>
                    <tr>
                        <th>Employee ID</th>
                        <td style="text-align: start; font-weight:500">: {{Auth::user()->users_detail->employee_id}}</td>
                    </tr>
                    <tr>
                        <th>Full Name</th>
                        <td style="text-align: start; font-weight:500">: {{Auth::user()->name}}</td>
                    </tr>
                    <tr>
                        <th>Hired Date</th>
                        <td style="text-align: start; font-weight:500">: {{Auth::user()->users_detail->hired_date}}</td>
                    </tr>
                  </tr>
              </table>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Medical Balance</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                    <tr>
                      <tr>
                          <th width="300px">Medical Balance</th>
                          <td style="text-align: start; font-weight:500">: </td>
                      </tr>
                      <tr>
                          <th>5 Year Term</th>
                          <td style="text-align: start; font-weight:500">: </td>
                      </tr>
                      <tr>
                          <th>Weekend Replacement</th>
                          <td style="text-align: start; font-weight:500">: </td>
                      </tr>
                      <tr>
                          <th>Total Leave Available</th>
                          <td style="text-align: start; font-weight:500">: </td>
                      </tr>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

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
                    <tr class="text-center">
                        <th>Request Number</th>
                        <th>Request Date</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th width="120px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($med as $q)
                    <tr >
                        <td>MED_0000{{$q->id}}</td>
                        <td>{{$q->med_req_date}}</td>
                        <td>{{$q->med_payment}}</td>
                        <td>{{$q->med_status}}</td>
                        <td class="row-cols-2 justify-content-betwen text-center">
                            <a href="/medical/edit/{{ $q->id }}" title="Edit" class="btn btn-warning btn-sm" >
                                <i class="fas fa-fw fa-edit justify-content-center"></i>
                            </a>
                            <a href="/medical/delete/{{ $q->id }}" title="Hapus" class="btn btn-danger btn-sm" ><i class="fas fa-fw fa-trash justify-content"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
        </table>
    </div>
</div>

@endsection