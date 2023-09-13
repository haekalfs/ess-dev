@extends('layouts.main')

@section('title', 'Medical - ESS')

@section('active-page-medical')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800" style="font-weight:bold">Medical Request Number # MED_0000{{ $med->med_number }}</h1>
    <div>
        <a class="btn btn-danger btn-sm" type="button" href="/approval/medical" id="manButton"><i class="fas fa-fw fa-backward fa-sm text-white-50"></i> Back</a>
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
<style>
.img-profile.rounded-circle.no-image {
    margin-top: 15px;
    position: relative;
    width: 100px;
    height: 100px;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    display: inline-block;
    align-items: center;
    justify-content: center;
    height: 20vh;
    width: 20vh;
}

.no-image-text {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 20vh;
}

.centered-button {
	display: flex;
	justify-content: center;
	align-items: center;
}
</style>
<div class="row zoom90">
    <!-- Area Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4" style="width: 750px; height: 245px;">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Request Information</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th>Name</th>
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
                    {{-- <tr>
                        <th>Status</th>
                        <td style="text-align: start; font-weight:500">: {{$med->med_status}}</td>
                    </tr> --}}
                  </tr>
              </table>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4" style="width: 750px; height: 245px;">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Approval Information</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                    <tr>
                      <tr>
                          <th>Approval By</th>
                          <td style="text-align: start; font-weight:500">: @if ($medAppUpdate){{ $medAppUpdate->user->name }}@else @endif</td>
                      </tr>
                      <tr>
                          <th>Approval Date</th>
                          <td style="text-align: start; font-weight:500">: @if ($medAppUpdate){{$medAppUpdate->approval_date}}@else @endif</td>
                      </tr>
                      <tr>
                          <th>Approval Notes</th>
                          <td style="text-align: start; font-weight:500;"> : @if ($medAppUpdate){{$medAppUpdate->approval_notes}}@else @endif</td>
                      </tr>
                      {{-- <tr>
                          <th>Total Leave Available</th>
                          <td style="text-align: start; font-weight:500">: </td>
                      </tr> --}}
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Medical Details</h6>
                <div class="text-right">
                    <a class="btn btn-success btn-sm" type="button"  data-toggle="modal" data-target="#approveModal" id="addButton">Approve</a>
                    <a class="btn btn-danger btn-sm" type="button"  data-toggle="modal" data-target="#rejectModal" id="addButton">Reject</a>
                </div>
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
                                <th>Amount Request</th>
                                <th>Amount Approved</th>
                                <th>Action</th>
                        </thead>
                        <tbody>
							@foreach($medDet as $md)
							<tr>
								<td class="centered-button">
                                    {{-- <img style="width: 80px; height: 80px; object-fit:fill;" class="img-fluid" src="{{ url('/storage/med_pic/'.$md->mdet_attachment)}}" alt="Attachment" data-toggle="modal" data-target="#myModal{{ $md->mdet_id}}"> --}}
									<button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#myModal{{ $md->mdet_id }}">View</button>
                                </td>
								<td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $md->mdet_desc }}
                                </td>
								<td>
                                    Rp. <span class="amount" id="amount">{{ $md->mdet_amount }}</span>
                                </td>
								<td>
                                    <input type="number" id="amountApprovedInput" value="{{ $md->amount_approved }}" hidden>
                                    Rp. <span class="amountApproved" id="amountApproved">{{ $md->amount_approved }}</span>
                                </td>
                                <td class="row-cols-2 justify-content-betwen text-center">
                                   @if($medButton->isNotEmpty())
                                    <a data-toggle="modal" data-target="#ModalMedDet{{ $md->mdet_id }}" title="Edit" class="btn btn-warning btn-sm" >
                                        <i class="fas fa-fw fa-edit justify-content-center"></i>Edit
                                    </a>
                                    @endif
                                    {{-- <a href="/medical/edit/{{ $med->id }}/delete/{{ $md->mdet_id }}" title="Delete" class="btn btn-danger btn-sm" ><i class="fas fa-fw fa-trash justify-content"></i></a> --}}
                                </td>
							</tr>
							@endforeach
                            <tr class="p-3 mb-2 bg-secondary text-white" style="border-bottom: 1px solid #dee2e6;">
                                <td colspan="2" class="text-center">Total</td>
								<td class="text-start">
                                    <span id="totalAmount" name="totalAmount"></span>
                                    <input id="totalAmount" name="totalAmount" value="" hidden>
                                </td>
								<td colspan="2" class="text-start" >
                                    <span id="totalAmountApproved" name="totalAmountApproved"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
            </div>
        </div>
    </div>
</div>
<span id="span1"></span>
<span id="span2"></span>

<!-- Modal -->
@foreach($medDet as $md)
<form action="/medical/approval/{{ $med->id }}M/update/{{ $md->mdet_id }}" method="POST">
    @csrf
    @method('PUT')

<!-- Modal Attachment -->
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
                    <img src="{{ url('/storage/med_pic/'.$md->mdet_attachment)}}" class="img-fluid" alt="Attachment">
                </div>
            </div>
        </div>
    </div>

{{-- modal Medical detail --}}
    <div class="modal fade" id="ModalMedDet{{ $md->mdet_id}}" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Medical Detail Edit #{{ $md->mdet_id }} </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-sm zoom90">
                        <tbody>
							<tr>
								<td>
                                    <label for="password">Description :</label>
                                    <textarea class="form-control flex" name="input_mdet_desc" placeholder="Description..." style="height: auto">{{ $md->mdet_desc }}</textarea>
                                </td>
							</tr>
                            <tr>
                                <td><label for="password">Amount Approved :</label>
                                    <input class="form-control flex" name="input_mdet_amount_approved" placeholder="Amount Approved..." value="{{ $md->amount_approved }}" oninput="formatAmount(this)"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn-danger" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button class="btn-sm btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endforeach

{{-- Modal Approve --}}
<form action="/medical/approval/{{ $med->id }}M/approve" method="POST" enctype="multipart/form-data" id="approve">
@csrf
@method('PUT')
<div class="modal fade" id="approveModal" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="myModalLabel" style="color: white">Approve Medical Reimburse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless table-sm">
                    <tbody>
                        <tr>
                            <th>Approved By :</th>
                            <th>Approved Date :</th>
                            <th>Total Approved :</th>
                        </tr>
                        <tr>
                            <td>
                                <span name='approved_name'> {{ Auth::user()->name }}</span>
                                <input name='approved_name' value="{{ Auth::user()->name }}" readonly hidden>
                            </td>
                            <td>
                                <span name='date_approved'>{{ now()->format('Y-m-d') }}</span>
                                <input name='date_approved' value="{{ now()->format('Y-m-d') }}" readonly hidden>                                
                            </td>
                            <td colspan="2" class="text-start" >
                                <span id="totalAmountApproved2" name="totalAmountApproved"></span>
                                <input type="text" id="totalAmountApprovedInput" name="totalAmountApprovedInput" value="" hidden>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <label for="password">Notes :</label>
                                <textarea class="form-control flex" name="input_approve_note" placeholder="Notes..." ></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sm btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                <input type="submit" class="btn btn-success btn-sm" value="Submit" id="btn-submit">
            </div>
        </div>
    </div>
</div>
</form>
{{-- Modal Reject --}}
<form action="/medical/approval/{{ $med->id }}M/reject" method="POST" enctype="multipart/form-data" id="reject">
@csrf
@method('PUT')
<div class="modal fade" id="rejectModal" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="width: 350px">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="myModalLabel">Reject Medical Reimburse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless table-sm">
                    <tbody>
                        <tr>
                            <th>Rejected By :</th>
                            <th>Rejected Date :</th>
                        </tr>
                        <tr>
                           <td>
                                <span name='rejected_name'> {{ Auth::user()->name }}</span>
                                <input name='rejected_name' value="{{ Auth::user()->name }}" readonly hidden>
                            </td>
                            <td>
                                <span name='date_rejected'>{{ now()->format('Y-m-d') }}</span>
                                <input name='date_rejected' value="{{ now()->format('Y-m-d') }}" readonly hidden>                                
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label for="password">Notes :</label>
                                <textarea class="form-control flex" name="input_reject_note" placeholder="Notes..." ></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer ">
                <button type="button" class="btn-sm btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                <button class="btn-sm btn-primary">Reject</button>
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

function changeFileName(inputId, labelId) {
  var input = document.getElementById(inputId);
  var label = document.getElementById(labelId);
  label.textContent = input.files[0].name;
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
var totalAmountDisplay = document.getElementById("totalAmount");
totalAmountDisplay.textContent = "Rp. " + totalAmount.toLocaleString().replace(/,/g, '.');



// Amount Approved
// Mengubah titik menjadi angka biasa dan menghitung total amount amountApproved
document.addEventListener("DOMContentLoaded", function() {
    var amountApprovedElements = document.getElementsByClassName("amountApproved");
    var totalAmountApproved = 0;

    for (var i = 0; i < amountApprovedElements.length; i++) {
        var amountText = amountApprovedElements[i].textContent;
        var amountApprovedNumber = parseFloat(amountText.replace(/\./g, "").replace(",", "."));
        totalAmountApproved += amountApprovedNumber;
    }

    // Display total amount approved in different locations
    var totalAmountApprovedDisplay1 = document.getElementById("totalAmountApproved");
    totalAmountApprovedDisplay1.textContent = "Rp. " + totalAmountApproved.toLocaleString().replace(/,/g, '.');

    var totalAmountApprovedDisplay2 = document.getElementById("totalAmountApproved2");
    totalAmountApprovedDisplay2.textContent = "Rp. " + totalAmountApproved.toLocaleString().replace(/,/g, '.');

    // Update total amount approved in input field
    var totalAmountApprovedInput = document.getElementById("totalAmountApprovedInput");
    if (totalAmountApprovedInput) {
        totalAmountApprovedInput.value = totalAmountApproved.toLocaleString().replace(/\./g, "").replace(",", ".");
    }
});
// Simpan nilai totalApprovedAmount dalam input tersembunyi saat menghitungnya
document.getElementById('totalAmountApprovedInput').value = totalApprovedAmountInput;


</script>
@endsection
