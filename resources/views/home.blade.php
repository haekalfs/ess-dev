@extends('layouts.main')

@section('title', 'Home Page - ESS')

@section('active-page-db')
active
@endsection

@section('content')
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
                    <tr>
                        <th>Position</th>
                        <td style="text-align: start; font-weight:500">: 
                        @if(Auth::user()->users_detail->position)
                            {{ Auth::user()->users_detail->position->position_name }}
                        @endif
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
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Leave Balance</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                    <tr>
                      <tr>
                          <th>Leaves Balance</th>
                          <td style="text-align: start; font-weight:500">: N/A</td>
                      </tr>
                      <tr>
                          <th>5 Year Term</th>
                          <td style="text-align: start; font-weight:500">: N/A</td>
                      </tr>
                      <tr>
                          <th>Weekend Replacement</th>
                          <td style="text-align: start; font-weight:500">: N/A</td>
                      </tr>
                      <tr>
                          <th>Total Leave Available</th>
                          <td style="text-align: start; font-weight:500">: N/A</td>
                      </tr>
                      <tr>
                          <th>Sick</th>
                          <td style="text-align: start; font-weight:500">: N/A</td>
                      </tr>
                    </tr>
                </table>
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
                    <div class="col-md-7">
                        <div class="lc-block position-relative">
                            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img class="d-block w-100 img-fluid rounded shadow" src="{{ asset('img/news2023.jpg') }}"
                                            alt="First slide">
                                    </div>
                                    <div class="carousel-item">
                                        <img class="d-block w-100 img-fluid rounded shadow" src="{{ asset('img/ESS.png') }}"
                                            alt="Second slide">
                                    </div>
                                    <div class="carousel-item">
                                        <img class="d-block w-100 img-fluid rounded shadow" src="{{ asset('img/news2023.jpg') }}"
                                            alt="Third slide">
                                    </div>
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
                    <div class="col-md-5">
                        <div style="height:400px; overflow:auto">
                            <div class="card-body">
                              <h5 class="card-title">Peraturan Perusahaan 2023 - PT Konsulindo Informatika Perdana</h5>
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
                                <a class="btn btn-sm @role('freelancer') btn-success @else btn-primary @endrole" href="https://perdana365-my.sharepoint.com/:b:/g/personal/admin_perdana365_onmicrosoft_com/ET8qcmGYOk9LtyebhqZmJroBM1FHsHF3OZ3sg_s3lxvTVg" class="card-link">Read more</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="homeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" >
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title m-0 font-weight-bold text-danger" id="exampleModalLabel">User Guide</h5>
          </button>
        </div>
        <div class="modal-body">
            <p>[<a class="text-warning"> Warning </a>] ESS is in the development stage. Some page is still not accessible & you may find faults on input.</p>
            <p>Sincerely,</p>
            <p>IT Administrator</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">I Understand</button>
        </div>
      </div>
    </div>
</div>
@endsection
