@extends('layouts.main')

@section('title', 'Home Page - ESS')

@section('active-page-db')
active
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800"><i class="far fa-smile-beam"></i> Welcome aboard, {{ Auth::user()->name }}!</h1>
    {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-smile-beam fa-sm text-white-50"></i> Show Details</a> --}}
</div>
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
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4 queue">
        <div class="card border-bottom-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Services Year</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">2</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-people-carry fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-bottom-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Leave Balance</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-plane-departure fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-bottom-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Reimbursements</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">9</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests Card Example -->
    <div class="col-xl-3 col-md-6 mb-4 formHistory">
        <div class="card border-bottom-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Project Assigned</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-network-wired fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Area Chart -->
    {{-- <div class="col-xl-6 col-lg-6">
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
                    <tr>
                        <th>Position</th>
                        <td style="text-align: start; font-weight:500">:
                        @if(Auth::user()->users_detail->position)
                            {{ Auth::user()->users_detail->position->position_name }}</td>
                        @endif
                    </tr>
                  </tr>
              </table>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Leave Balance</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                    <tr>
                      <tr>
                          <th width="300px">Leaves Balance</th>
                          <td style="text-align: start; font-weight:500">: {{ $empLeaveQuotaAnnual }}</td>
                      </tr>
                      <tr>
                          <th>Leave Quota Used</th>
                          <td style="text-align: start; font-weight:500">: {{ $empLeaveQuotaFiveYearTerm }}</td>
                      </tr>
                      <tr>
                          <th>Weekend Replacement</th>
                          <td style="text-align: start; font-weight:500">: {{ $empLeaveQuotaWeekendReplacement }}</td>
                      </tr>
                      <tr>
                          <th>Total Leave Available</th>
                          <td style="text-align: start; font-weight:500">: {{ $totalQuota }}</td>
                      </tr>
                      <tr>
                        <th>Sick</th>
                        <td style="text-align: start; font-weight:500">: N/A</td>
                    </tr>
                    </tr>
                </table>
            </div>
        </div>
    </div> --}}
    <div class="col-xl-12 col-md-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">News Information</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="lc-block position-relative">
                            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach($headline as $index => $hl)
                                        <div class="carousel-item{{ $index === 0 ? ' active' : '' }}">
                                            <img class="d-block w-100 img-fluid rounded shadow" src="{{ asset($hl->filepath) }}" alt="Slide {{ $index + 1 }}">
                                        </div>
                                    @endforeach
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div style="height:350px;" class="transparent-scroll2">
                            <div class="card-body transparent-scroll">
                                @foreach($newsFeed as $feed)
                                    <h5 class="card-title font-weight-bold">{{ $feed->title }}</h5>
                                    {!! $feed->content !!}
                                    <div class="mb-4">
                                        <a class="btn btn-sm btn-primary mt-0" data-id="{{ $feed->id }}" class="card-link">Read more</a>
                                    </div>
                                @endforeach
                                 {{-- <h5 class="card-title">Peraturan Perusahaan 2023 - PT Konsulindo Informatika Perdana</h5>
                              <p class="card-text">Dear All,<br>
                                The following is a link regarding the company regulation in Perdana Consulting
                                Thank you for your attention.
                                <br>Sincerely,<br><br>
                                Perdana Consulting</p>
                              <a class="btn btn-sm @role('freelancer') btn-success @else btn-primary @endrole" href="https://perdana365-my.sharepoint.com/personal/admin_office365_perdana_co_id/_layouts/15/onedrive.aspx?id=%2Fpersonal%2Fadmin%5Foffice365%5Fperdana%5Fco%5Fid%2FDocuments%2FPeraturan%20Perusahaan%20%2D%20Konsulindo%20Informatika%20Perdana%202023%2Epdf&parent=%2Fpersonal%2Fadmin%5Foffice365%5Fperdana%5Fco%5Fid%2FDocuments&ga=1" class="card-link">Read more</a>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Peraturan Perusahaan 2021 - PT Konsulindo Informatika Perdana</h5>
                                <p class="card-text">Dear All,<br>
                                  The following is a link regarding the company regulation in Perdana Consulting
                                  Thank you for your attention.
                                  <br>Sincerely,<br><br>
                                  Perdana Consulting</p>
                                <a class="btn btn-sm @role('freelancer') btn-success @else btn-primary @endrole" href="https://perdana365-my.sharepoint.com/:b:/g/personal/admin_office365_perdana_co_id/ET8qcmGYOk9LtyebhqZmJroB2_6CQ1EGsEpGH0b5tmtZYQ?e=5ddh4u" class="card-link">Read more</a> --}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
