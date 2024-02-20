@extends('layouts.main')

@section('title', 'Home Page - ESS')

@section('active-page-db')
active
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800"><i class="far fa-smile-beam"></i> Welcome onboard, {{ Auth::user()->name }}!</h1>
    {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-smile-beam fa-sm text-white-50"></i> Show Details</a> --}}
</div>
@if ($message = Session::get('quotes'))
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
<?php
$hired_date = Auth::user()->users_detail->hired_date; // assuming $hired_date is in Y-m-d format
$current_date = date('Y-m-d'); // get the current date

// create DateTime objects from the hired_date and current_date values
$hired_date_obj = new DateTime($hired_date);
$current_date_obj = new DateTime($current_date);

// calculate the difference between the hired_date and current_date
$diff = $current_date_obj->diff($hired_date_obj);

// get the total number of years from the difference object
$total_years_of_service = $diff->y;
?>
<div class="row zoom90">
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4 queue">
        <div class="card border-bottom-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Services Year</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_years_of_service.' Years'?></div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalQuota }}</div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reimbursementCount }}</div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $countAssignments }}</div>
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
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Leaderboard</h6>
                <div class="text-right">
                    <select class="form-control" id="type" name="type" required onchange="redirectToPage()">
                        <option value="1" @if (1 == $typeSelected) selected @endif>Head Office</option>
                        <option value="2" @if (2 == $typeSelected) selected @endif>Non-Head Office</option>
                    </select>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="row zoom80">
                    <div class="col-md-12">
                        <div id="header" style="padding-top:0%;">
                            <h1 class="h4 mb-0 font-weight-bold text-gray-800">Employee of The Month 🎉</h1>
                            <button class="share">
                                <i class="fas fa-share fa-2x"></i>
                            </button>
                        </div>
                        <div id="leaderboard">
                            <div class="ribbon"></div>
                            <table>
                                @foreach($activitiesArray as $key => $activity)
                                    <tr>
                                        <td class="number">{{ $key + 1 }}</td>
                                        <td class="name">
                                            @if($key === 0)
                                                {{ $activity['ts_user_id'] }}
                                            @else
                                                {{ $activity['ts_user_id'] }}
                                            @endif
                                        </td>
                                        <td class="points">
                                            @if($key === 0)
                                            <span class="text-danger"><small><i>{{ $activity['attendance_days_count'] }} days of office attendance < {{ $activity['earliest_come_time'] }}</i></small></span> <img class="gold-medal" src="https://github.com/malunaridev/Challenges-iCodeThis/blob/master/4-leaderboard/assets/gold-medal.png?raw=true" alt="gold medal"/>
                                            @else
                                                <span class="text-primary"><small><i>{{ $activity['attendance_days_count'] }} days of office attendance around {{ $activity['earliest_come_time'] }} - 08:00 AM</i></small></span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div><br>
                        <span class="text-danger mt-2"><small>Congratulations! Your reward awaits you at the closest canteen. Bon appétit!</small></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>

    #header {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 2rem;
    }

    .share {
      width: 4.5rem;
      height: 3rem;
      background-color: #f55e77;
      border: 0;
      border-bottom: 0.2rem solid #c0506a;
      border-radius: 2rem;
      cursor: pointer;
    }

    .share:active {
      border-bottom: 0;
    }

    .share i {
      color: #fff;
      font-size: 2rem;
    }

    h1 {
      font-size: 1.7rem;
      color: #141a39;
      text-transform: uppercase;
      cursor: default;
    }

    #leaderboard {
      width: 100%;
      position: relative;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
      color: #141a39;
      cursor: default;
    }

    tr {
      transition: all 0.2s ease-in-out;
      border-radius: 0.2rem;
    }

    tr:not(:first-child):hover {
      background-color: #fff;
      transform: scale(1.1);
      -webkit-box-shadow: 0px 5px 15px 8px #e4e7fb;
      box-shadow: 0px 5px 15px 8px #e4e7fb;
    }

    tr:nth-child(odd) {
      background-color: #f9f9f9;
    }

    tr:nth-child(1) {
      color: #fff;
    }

    td {
      height: 5rem;
      font-family: "Rubik", sans-serif;
      font-size: 1.4rem;
      padding: 1rem 2rem;
      position: relative;
    }

    .number {
      width: 1rem;
      font-size: 2.2rem;
      font-weight: bold;
      text-align: left;
    }

    .name {
      text-align: left;
      font-size: 1.2rem;
    }

    .points {
      font-weight: bold;
      font-size: 1.3rem;
      display: flex;
      justify-content: flex-end;
      align-items: center;
    }

    .points:first-child {
      width: 10rem;
    }

    .gold-medal {
      height: 3rem;
      margin-left: 1.5rem;
    }

    .ribbon {
      width: 42rem;
      height: 5.5rem;
      top: -0.5rem;
      background-color: #5c5be5;
      position: absolute;
      left: -1rem;
      -webkit-box-shadow: 0px 15px 11px -6px #7a7a7d;
      box-shadow: 0px 15px 11px -6px #7a7a7d;
    }

    .ribbon::before {
      content: "";
      height: 1.5rem;
      width: 1.5rem;
      bottom: -0.8rem;
      left: 0.35rem;
      transform: rotate(45deg);
      background-color: #5c5be5;
      position: absolute;
      z-index: -1;
    }

    .ribbon::after {
      content: "";
      height: 1.5rem;
      width: 1.5rem;
      bottom: -0.8rem;
      right: 0.35rem;
      transform: rotate(45deg);
      background-color: #5c5be5;
      position: absolute;
      z-index: -1;
    }

    #buttons {
      width: 100%;
      margin-top: 3rem;
      display: flex;
      justify-content: center;
      gap: 2rem;
    }

    .exit {
      width: 11rem;
      height: 3rem;
      font-family: "Rubik", sans-serif;
      font-size: 1.3rem;
      text-transform: uppercase;
      color: #7e7f86;
      border: 0;
      background-color: #fff;
      border-radius: 2rem;
      cursor: pointer;
    }

    .exit:hover {
      border: 0.1rem solid #5c5be5;
    }

    .continue {
      width: 11rem;
      height: 3rem;
      font-family: "Rubik", sans-serif;
      font-size: 1.3rem;
      color: #fff;
      text-transform: uppercase;
      background-color: #5c5be5;
      border: 0;
      border-bottom: 0.2rem solid #3838b8;
      border-radius: 2rem;
      cursor: pointer;
    }

    .continue:active {
      border-bottom: 0;
    }

    @media (max-width: 740px) {
        * {
          font-size: 70%;
        }
    }

    @media (max-width: 500px) {
        * {
          font-size: 55%;
        }
    }

    @media (max-width: 390px) {
        * {
          font-size: 45%;
        }
    }
        </style>
        <script>
            function redirectToPage() {
                var selectedOption = document.getElementById("type").value;
                var url = "{{ url('/home') }}"; // Specify the base URL

                url += "/" + selectedOption;

                window.location.href = url; // Redirect to the desired page
            }
        </script>
@endsection
