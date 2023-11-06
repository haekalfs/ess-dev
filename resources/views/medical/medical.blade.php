@extends('layouts.main')

@section('active-page-medicals')
active
@endsection

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h2 class=" mb-2 text-gray-800"><i class="fas fa-fw fa-hand-holding-medical"></i><b> Medical Reimburse</b></h2>
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
                            <td style="text-align: start;"> :
                                <span id="medicalBalance" style="font-weight:700; ">**********</span>
                                <a href="#" onclick="togglePasswordVisibility()">
                                    <i id="eyeIcon" class="fa fa-eye"></i>
                                </a>
                            </td>
                      </tr>
                      <tr>
                          <th>5 Year Term</th>
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
            {{-- <a href="" class="btn btn-primary btn-sm">View Report</a> --}}
        </div>
    </div>
    <!-- Card Body -->
    <div class="card-body zoom90">
        <table class="table table-bordered table-hover " id="dataTable">
                <thead>
                    <tr class="text-center">
                        <th>Request Number</th>
                        <th>Request Date</th>
                        <th>Payment Method</th>
                        <th>Approval Status</th>
                        <th>Payment Status</th>
                        <th width="120px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($med as $q)
                    <tr>
                        <td>MED_0000{{$q->med_number}}</td>
                        <td>{{$q->med_req_date}}</td>
                        <td>{{$q->med_payment}}</td>
                        <td class="text-center">
                            @foreach ($q->medical_approval as $md)
                                @switch($md->status)
                                    @case(29)
                                        <span class="badge  badge-success" style="font-size: 14px">Approved By {{ $md->user->name }}  <i class="fa fa-check" aria-hidden="true"></i></span>
                                        @break
                                    @case(15)
                                        <span class="badge badge-secondary" style="font-size: 14px">Waiting For Approval <i class="fa fa-spinner" aria-hidden="true"></i></span>
                                        @break
                                    @case(404)
                                        <span class="badge badge-danger" style="font-size: 14px">Rejected By {{ $md->user->name }}  <i class="fa fa-exclamation" aria-hidden="true"></i></span>
                                        @break
                                    @default
                                        <span class="badge badge-info" style="font-size: 14px">Status Tidak Dikenal</span>
                                @endswitch
                            @endforeach
                        </td>
                        <td class="text-center">
                            @switch($q->paid_status)
                                @case(20)
                                    <span class="badge badge-pill badge-primary" style="font-size: 14px">Waiting For Payment <i class="fa fa-check" aria-hidden="true"></i></span>
                                    @break
                                @case(15)
                                    <span class="badge badge-pill badge-secondary" style="font-size: 14px">Waiting For Approval <i class="fa fa-spinner" aria-hidden="true"></i></span>
                                    @break
                                @case(29)
                                    <span class="badge badge-pill badge-success" style="font-size: 16px">Paid <i class="fa fa-check-circle" aria-hidden="true"></i></span>
                                    @break
                                @case(404)
                                    <span class="badge badge-pill badge-danger" style="font-size: 16px">Rejected <i class="fa fa-exclamation" aria-hidden="true"></i></span>
                                    @break
                                @default
                                    <span class="badge badge-pill badge-info" style="font-size: 16px">Status Tidak Dikenal</span>
                            @endswitch
                        </td>
                        <td class="row-cols-2 justify-content-betwen text-center">
                            @foreach ($q->medical_approval as $md)
                                @if ($md->status == 29 || $md->status == 404)
                                    <a class="btn btn-primary btn-sm" type="button" href="/medical/edit/{{ $q->id }}" id="manButton">View<i class="fas fa-fw fa-eye fa-sm"></i></a>
                                @else
                                    <a href="/medical/edit/{{ $q->id }}" title="Edit" class="btn btn-warning btn-sm" >
                                    <i class="fas fa-fw fa-edit justify-content-center"></i>
                                </a>
                                    <a title="Hapus" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#staticBackdrop" ><i class="fas fa-fw fa-trash justify-content"></i></a>
                                @endif
                            @endforeach
                            
                        </td>
                    </tr>
                    @endforeach
                </tbody>
        </table>
    </div>
</div>

@foreach($med as $q)
<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title  text-white" id="staticBackdropLabel">Alert !!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img class="mb-2" width="96" height="96" src="https://img.icons8.com/color/96/general-warning-sign.png" alt="general-warning-sign"/>
        <h6>Are You Sure Want Delete This Record !!!</h6>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn-sm btn-primary" data-dismiss="modal">Cancel</button>
        <a href="/medical/delete/{{ $q->id }}" title="Hapus" class="btn btn-danger btn-sm" >Yes Im Sure</a>
      </div>
    </div>
  </div>
</div>
@endforeach


<script>
    function togglePasswordVisibility() {
    var medicalBalance = document.getElementById('medicalBalance');
    var eyeIcon = document.getElementById('eyeIcon');

    if (medicalBalance.innerText === '**********') {
        medicalBalance.innerText = '{{ $emp_medical_balance->medical_balance }}';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        medicalBalance.innerText = '**********';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}

</script>
@endsection