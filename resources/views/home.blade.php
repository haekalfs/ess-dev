@extends('layouts.main')

@section('content')
    <div class="row" style="margin: 20px; padding:10px">
        <div class="col-sm-6 mb-3 mb-sm-0">
            <div class="card">
                <h5 class="card-header" style="color:#0e75bc; font-weight:800">Employee Information</h5>
                <div class="table-responsive">
                  <table class="table table-bordered" width="100%" cellspacing="0">
                        <tr>
                            <th>ID</th>
                            <td style="text-align: start; font-weight:650">01</td>
                        </tr>
                        <tr>
                            <th>User ID</th>
                            <td style="text-align: start; font-weight:650">12345</td>
                        </tr>
                        <tr>
                            <th>Employee ID</th>
                            <td style="text-align: start; font-weight:650">1720090</td>
                        </tr>
                        <tr>
                            <th>Full Name</th>
                            <td style="text-align: start; font-weight:650">Dio febrian</td>
                        </tr>
                        <tr>
                            <th>Position</th>
                            <td style="text-align: start; font-weight:650">Frontend Web</td>
                        </tr>
                      </tr>
                  </table>
                </div>
            </div>  
        </div>
        <div class="col-sm-6">
            <div class="card">
                <h5 class="card-header" style="color:#0e75bc; font-weight:800">Leave Balance</h5>
                <div class="table-responsive">
                  <table class="table table-bordered" width="100%" cellspacing="0">
                      <tr>
                          <tr><th>Leaves Balance</th></tr>
                          <tr><th>5 Year Term</th></tr>
                          <tr><th>Weekend Replacement</th></tr>
                          <tr><th>Total Leave Available</th></tr>
                          <tr><th>Sick</th></tr>
                      </tr>
                  </table>
                </div>
            </div>
      </div>
      <div class="col-sm-12" style="margin-top:20px;" >
        <div class="card max-width mb-3">
            <h5 class="card-header" style="color:#0e75bc; font-weight:800;" >NEWS INFORMATION</h5>
            <div style="height:300px; overflow:auto">
                <div class="card-body">
                  <h5 class="card-title">Judul</h5>
                  <p class="card-text">Slugnyaaa.....</p>
                  <a href="#" class="card-link">Read more</a>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Special title treatment</h5>
                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    <a href="#" class="card-link">Read more</a>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Special title treatment</h5>
                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    <a href="#" class="card-link">Read more</a>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Special title treatment</h5>
                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    <a href="#" class="card-link">Read more</a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
