@extends('layouts.main')

@section('active-page-medicals')
active
@endsection

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class=" mb-2 text-gray-800"><i class="fas fa-fw fa-hand-holding-medical"></i><b> Medical Reimburse</b></h4>
    <div>
        <select class="form-control" id="yearSelected" name="yearSelected" required onchange="redirectToPageAssignment()">
            @foreach (array_reverse($yearsBefore) as $year)
                <option value="{{ $year }}" @if ($year == $yearSelected) selected @endif>{{ $year }}</option>
            @endforeach
        </select>
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
                        <th>Service Years</th>
                        <td style="text-align: start; font-weight:500">: {{ $total_years_of_service }} Years</td>
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
                          <th width="300px">Balance Limit</th>
                            <td style="text-align: start;"> :
                                <span id="medicalBalance" style="font-weight:700; ">**********</span>
                                <a href="#" onclick="togglePasswordVisibility()">
                                    <i id="eyeIcon" class="fa fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                          <th>Balance Remaining</th>
                          <td style="text-align: start; font-weight:500">: {{ $emp_medical_balance->medical_remaining }}</td>
                        </tr>
                        <tr>
                            <th>Balance Deducted</th>
                            <td style="text-align: start; font-weight:500">: {{ $emp_medical_balance->medical_deducted }}</td>
                        </tr>
                        <tr>
                          <th>Remaining Active Period</th>
                           <td style="text-align: start; font-weight:500">: 
                                <span class="text-success font-weight-bold">{{ $monthPeriode }} Months Left </span>
                            </td>
                        </tr>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4 zoom90">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Medical History</h6>
        <div class="text-right">
            <a href="/medical/entry" id="newRequestBtn" class="btn btn-primary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> New Request</a>
        </div>
    </div>
    <!-- Card Body -->
    <div class="card-body">
        <table class="table table-bordered table-hover " id="dataTable">
                <thead class="thead-light font-weight-bold">
                    <tr class="text-center">
                        <th>Request Number</th>
                        <th>Request Date</th>
                        <th>Medical Type</th>
                        {{-- <th>Payment Method</th> --}}
                        <th>Approval Status</th>
                        <th>Payment Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($med as $q)
                    <tr>
                        <td class="font-weight-bold">MED_{{$q->id}}</td>
                        <td>{{$q->med_req_date}}</td>
                        <td>
                            @if($q->type_id){{ $q->medical_type->name_type }} {!! $q->medical_type->icon !!}@endif
                        </td>
                        <td class="text-center">
                            @switch($q->medical_approval->status)
                                @case(29)
                                    Approved By {{ $q->medical_approval->user->name }}  <i class="fa fa-check-circle text-success" aria-hidden="true"></i>
                                    @break
                                @case(15)
                                    Waiting For Approval <i class="fa fa-spinner" aria-hidden="true"></i>
                                    @break
                                @case(404)
                                    Rejected By {{ $q->medical_approval->user->name }}  <i class="fa fa-exclamation-circle text-danger" aria-hidden="true"></i>
                                    @break
                                @default
                                    Status Tidak Dikenal
                            @endswitch
                        </td>
                        <td class="text-center">
                            @switch($q->medical_payment->paid_status)
                                @case(20)
                                    Waiting For Payment <i class="fa fa-spinner" aria-hidden="true"></i>
                                    @break
                                @case(15)
                                    Waiting For Approval <i class="fa fa-spinner" aria-hidden="true"></i>
                                    @break
                                @case(29)
                                    <span class="text-success font-weight-bold">Paid <i class="fa fa-check-circle" aria-hidden="true"></i></span>
                                    @break
                                @case(404)
                                    <span class="text-success font-weight-bold">Rejected <i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                                    @break
                                @default
                                    Unknown</span>
                            @endswitch
                        </td>
                        <td class="col-2 justify-content-betwen text-center">
                            @if ($q->medical_approval->status == 29 || $q->medical_approval->status == 404)
                                <a class="btn btn-primary btn-sm" type="button" href="/medical/edit/{{ $q->id }}" id="manButton"><i class="fa fa-eye" aria-hidden="true"></i> View</i></a>
                            @else
                                <a href="/medical/edit/{{ $q->id }}" title="Edit" class="btn btn-warning btn-sm" >
                                    <i class="fas fa-fw fa-edit justify-content-center"></i> Edit
                                </a>
                                <a title="Delete" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete{{ $q->id }}" >
                                    <i class="fas fa-fw fa-trash justify-content"></i> Cancel
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
        </table>
    </div>
</div>

@foreach($med as $q)
<!-- Modal Deelete -->
<div class="modal fade" id="delete{{ $q->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary">
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
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
        <a href="/medical/delete/{{ $q->id }}" title="Delete" class="btn btn-danger btn-sm" >Yes Im Sure</a>
      </div>
    </div>
  </div>
</div>
@endforeach

<script>
    function redirectToPageAssignment() {
        var selectedOption = document.getElementById("yearSelected").value;
        var url = "{{ url('/medical/history') }}"; // Specify the base URL

        url += "/" + selectedOption;

        window.location.href = url; // Redirect to the desired page
    }
    document.getElementById('newRequestBtn').addEventListener('click', function(event) {
       
        var totalYearsOfService = {{ $total_years_of_service }};
        // var medicalBalance = {{ $emp_medical_balance->medical_balance  }};

        if (totalYearsOfService >= 1 ) {
            window.location.href = event.target.href;
        } else {
            alert('Unfortunately your service year is still below one year !!!');
            event.preventDefault();
        }
    });

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