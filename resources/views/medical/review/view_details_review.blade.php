@extends('layouts.main')

@section('title', 'Review Medical - ESS')

@section('active-page-medical')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class=" mb-2 text-gray-800"><i class="fas fa-fw fa-hand-holding-medical"></i><b> Medical Request Number # MED_{{ $med->id }}</b></h4>
    <div>
        <a class="btn btn-danger btn-sm" type="button" href="/medical/review" id="manButton"><i class="fas fa-fw fa-backward fa-sm text-white-50"></i> Back</a>
    </div>
</div>
<h5 class="m-0 font-weight-bold text-primary"></h5>

{{-- <form method="POST" action="/medical/edit/{{  $med->mdet_id }}" enctype="multipart/form-data"> --}}
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
    <!-- Pie Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Request Information</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                    <tr>
                        <tr>
                            <th>Requestor Name</th>
                            <td style="text-align: start; font-weight:500">: {{$med->user->name}}</td>
                        </tr>
                        <tr>
                            <th>Request Date</th>
                            <td style="text-align: start; font-weight:500">: {{$med->med_req_date}}</td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td style="text-align: start; font-weight:500">: {{$med->med_payment}}</td>
                        </tr>
                        <tr>
                            <th>No Account Bank</th>
                            <td style="text-align: start; font-weight:500">: {{ $med->user->users_detail->usr_bank_account }}  An. {{ $med->user->users_detail->usr_bank_account_name }} </td>
                        </tr>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <!-- Area Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Employee Balance Information</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                    <tr>
                        <th>Medical Balance</th>
                        <td style="text-align: start; font-weight:500">: {{ $medBalance->medical_balance }}</td>
                    </tr>
                    <tr>
                        <th>Medical Remaining</th>
                        <td style="text-align: start; font-weight:500">: {{ $medBalance->medical_remaining }}</td>
                    </tr>
                    <tr>
                        <th>Medical Deducted</th>
                        <td style="text-align: start; font-weight:500">: {{ $medBalance->medical_deducted }}</td>
                    </tr>
                    <tr>
                        <th>Remaining Active Periode</th>
                        <td style="text-align: start; font-weight:500">: 
                            @php
                                // Mendapatkan tanggal aktif dan tanggal sekarang
                                $activePeriode = \Carbon\Carbon::parse($medBalance->expiration);
                                $now = \Carbon\Carbon::now();
                                
                                // Menghitung selisih bulan
                                $diffInMonths = $activePeriode->diffInMonths($now);
                            @endphp
                            <span class="text-success font-weight-bold">{{ $diffInMonths }} Months Left </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>

<div class="row zoom80">
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Medical Details</h6>
                <button class="btn btn-success btn-sm" type="button" data-toggle="modal" data-target="#viewModal{{ $med->id }}"><i class="fa fa-check" aria-hidden="true"></i> Mark As PAid</button>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm zoom90">
                        <thead class="thead-light">
                            <tr>
                                {{-- <th>No</th> --}}
                                <th>Attachment</th>
                                <th>Description</th>
                                <th class="text-danger font-weight-bold">Amount Request</th>
                                <th class="text-success font-weight-bold">Estimated Funds</th>
                                <th>Action</th>
                        </thead>
                        <tbody>
							@foreach($medDet as $md)
							<tr>
								<td class="text-center">
                                    {{-- <img style="width: 80px; height: 80px; object-fit:fill;" class="img-fluid" src="{{ url('/storage/med_pic/'.$md->mdet_attachment)}}" alt="Attachment" data-toggle="modal" data-target="#myModal{{ $md->mdet_id}}"> --}}
									<button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#myModal{{ $md->mdet_id }}">Preview</button>
                                </td>
								<td>{{ $md->mdet_desc }}</td>
								<td class="text-danger font-weight-bold" >
                                    Rp. <span class="amount" id="amount">{{ $md->mdet_amount }}</span>
                                </td>
								<td class="text-success font-weight-bold">
                                    Rp. <span class="amountApproved" id="amountApproved">{{ $med->medical_approval->total_amount_approved }}</span>
                                </td>
                                <td class="row-col-2 justify-content-betwen text-center">
                                    @if ($med->medical_approval->status == 29)
                                        <a data-toggle="modal" data-target="#ModalStatus{{ $md->mdet_id }}" title="Status" class="btn btn-secondary btn-sm" >
                                            <i class="fas fa-fw fa-info-circle justify-content-center"></i> Status
                                        </a> 
                                    @else
                                        <a data-toggle="modal" data-target="#ModalMedDet{{ $md->mdet_id }}" title="Edit" class="btn btn-warning btn-sm" >
                                            <i class="fas fa-fw fa-edit justify-content-center"></i> Edit
                                        </a>
                                        <a data-toggle="modal" data-target="#ModalStatus{{ $md->mdet_id }}" title="Status" class="btn btn-secondary btn-sm" >
                                            <i class="fas fa-fw fa-info-circle justify-content-center"></i> Status
                                        </a>
                                    @endif
                                    {{-- @if(empty($medButton))
                                        <a data-toggle="modal" data-target="#ModalMedDet{{ $md->mdet_id }}" title="Edit" class="btn btn-warning btn-sm" >
                                            <i class="fas fa-fw fa-edit justify-content-center"></i>
                                        </a>
                                    @else
                                    @endif --}}
                                    {{-- <a href="/medical/edit/{{ $med->id }}/delete/{{ $md->mdet_id }}" title="Delete" class="btn btn-danger btn-sm" ><i class="fas fa-fw fa-trash justify-content"></i></a> --}}
                                </td>
							</tr>
							@endforeach
                        </tbody>
                        {{-- <tfoot class="tfoot-light-dark">
                            <tr>
                                <td colspan="2" class="text-center font-weight-bold">Total</td>
								<td class="text-start text-danger font-weight-bold">
                                    <span id="totalAmountDisplay" name="totalAmountDisplay"></span>
                                </td>
								<td colspan="2" class="text-start text-success font-weight-bold" id="totalAmountApproved">
                                    <span id="totalAmountApprovedDisplay" name="totalAmountApprovedDisplay"></span>
                                </td>
                            </tr>
                        </tfoot> --}}
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Attachment -->
@foreach($medDet as $md)

    <div class="modal fade" id="ModalStatus{{ $md->mdet_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title color" id="ModalStatusLabel">Status Medical</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered zoom80" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>Approver</th>
                                <th>Status</th>
                                <th>Granted Funds</th>
                                <th>Date Approved</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{$med->medical_approval->user->name}}</td>
                                <td>
                                    @switch($med->medical_approval->status)
                                            @case(29)
                                                <span>Approved <i class="fas fa-check-circle" style="color: #005eff;"></i></i></span>
                                                @break
                                            @case(15)
                                                <span>Waiting For Approval <i class="fa fa-spinner" aria-hidden="true"></i></span>
                                                @break
                                            @case(404)
                                                <span>Rejected <i class="fa fa-exclamation" style="color: red;" aria-hidden="true"></i></span>
                                                @break
                                            @default
                                                <span>Unknown <i class="fa fa-bug" style="color:yellow ;" aria-hidden="true"></i></span>
                                        @endswitch
                                </td>
                                <td>
                                    @if ($med->medical_approval->status == 29)
                                        Rp. <span class="amountApprovedStatus" id="amountApprovedStatus">{{ $med->medical_approval->total_amount_approved }}</span>
                                    @else
                                        Rp. 0
                                    @endif
                                </td>
                                <td>{{$med->medical_approval->approval_date}}</td>
                                <td>{{$med->medical_approval->approval_notes}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal{{ $md->mdet_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Attachment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if(pathinfo($md->mdet_attachment, PATHINFO_EXTENSION) == 'pdf')
                        <iframe src="{{ url('/storage/med_pic/'.$md->mdet_attachment) }}" width="100%" height="500px" alt="Attachment"></iframe>
                    @else
                        <img src="{{ url('/storage/med_pic/'.$md->mdet_attachment) }}" width="100%" alt="Attachment">
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach


{{-- Modal Paid --}}
<form method="POST" action="/medical/review/{{ $med->id }}" enctype="multipart/form-data" >
@method('PUT')
@csrf
    <div class="modal fade" id="viewModal{{ $med->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="balanceEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg zoom90" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title  text-white" id="staticBackdropLabel">Medical Request MED_{{ $med->id }}</h5>
                    <button type="button" class="close" style="color: white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-start d-flex justify-content-center">
                    <div class="row">
                        <div class="col-8 col-sm-6">
                            <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                                <thead>
                                    <tr colspan="2" class="text-center font-weight-bold">Total Request Amount</tr>
                                </thead>
                                <tr>
                                    {{-- <td colspan="2" class="text-center font-weight-bold">Total</td> --}}
                                    <td class="text-start text-danger font-weight-bold">
                                        <span id="totalAmountDisplay" name="totalAmountDisplay"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4 col-sm-6">
                            <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                                <thead>
                                    <tr colspan="2" class="text-center font-weight-bold">Total Amount Approved</tr>
                                </thead>
                                <tr>
                                    <td colspan="2" class="text-start text-success font-weight-bold" id="totalAmountApproved">
                                    <span id="totalAmountApprovedDisplay" name="totalAmountApprovedDisplay"></span>
                                </td>
                                </tr>
                            </table>
                        </div>
                        <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                            <tr>
                                <td style="text-align: center;">
                                    <div class="form-group justify-content-center" style="display: flex; align-items: center;">
                                        <label  for="password" style="height: 15px; text-align: center; margin-right: 10px;">Total Payable (Rp.) :</label>
                                        <input class="form-control flex" name="input_total_paid" id="total_paid" value="{{ $med->medical_approval->total_amount_approved }}" style="width: 200px; height: 25px;" oninput="formatCurrency(this)"/>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sm btn-success">Paid</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- JavaScript untuk mengaktifkan modal Bootstrap -->
<script>
$(document).ready(function() {
    $('.modal').on('shown.bs.modal', function () {
        $(this).find('.modal-title').focus();
    });
});

function formatAmount(input) {
  // Mengambil nilai input
  let amount = input.value;

  // Menghapus karakter selain angka
  amount = amount.replace(/\D/g, '');

  // Menambahkan pemisah ribuan setiap 3 angka
  amount = amount.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

  // Memperbarui nilai input dengan format terbaru
  input.value = amount;
}

//Amount
    // Mengubah titik menjadi angka biasa dan menghitung total amount
    var amountElements = document.getElementsByClassName("amount");
    var totalAmount = 0;

    for (var i = 0; i < amountElements.length; i++) {
    var amountText = amountElements[i].textContent;
    var amountNumber = parseFloat(amountText.replace(/\./g, "").replace(",", "."));
    totalAmount += amountNumber;
    }

    // Menampilkan total amount
    var totalAmountDisplay = document.getElementById("totalAmountDisplay");
    totalAmountDisplay.textContent = "Rp. " + totalAmount.toLocaleString('id-ID');
     // Update total amount approved in input field
    var totalAmountInput = document.getElementById("totalAmountInput");
    if (totalAmountInput) {
        totalAmountInput.value = totalAmount.toLocaleString('id-ID');
    }



// Amount Approved
    var amountElements = document.getElementsByClassName("amountApproved");
var totalAmountApproved = 0;

for (var i = 0; i < amountElements.length; i++) {
    var amountText = amountElements[i].textContent;
    var amountApprovedNumber = parseFloat(amountText.replace(/\./g, "").replace(",", "."));
    totalAmountApproved += amountApprovedNumber;
}

// Menampilkan total amount
var totalAmountApprovedDisplay = document.getElementById("totalAmountApproved");
totalAmountApprovedDisplay.textContent = "Rp. " + totalAmountApproved.toLocaleString('id-ID');


</script>
@endsection