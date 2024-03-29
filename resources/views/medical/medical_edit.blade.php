@extends('layouts.main')

@section('title', 'Medical - ESS')

@section('active-page-medical')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="mb-4 d-sm-flex align-items-center justify-content-between">
    <h4 class=" mb-2 text-gray-800"><i class="fas fa-fw fa-hand-holding-medical"></i><b> Medical Request Number # MED_{{ $med->id }}</b></h4>
    <a class="btn btn-danger btn-sm" type="button" href="/medical/history" id="manButton"><i class="fas fa-fw fa-backward fa-sm text-white-50"></i> Back</a>
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
        <div class="card shadow mb-4" >
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Requested Information</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-borderless font-weight-bold">
                            <tbody>
                                <tr class="table-sm">
                                    <td>Medical Type :</td>
                                    <td class="col-8">: {{$med->medical_type->name_type}} {!! $med->medical_type->icon !!}</td>
                                </tr>
								<tr class="table-sm">
                                    <td>Request Date</td>
                                    <td>: {{$med->med_req_date}}</td>
                                </tr>
								<tr class="table-sm">
                                    <td>Payment</td>
                                    <td>: {{$med->med_payment}}</td>
                                </tr>
								<tr class="table-sm">
                                    <td>Status</td>
                                    <td>: 
                                        @switch($med->medical_approval->status)
                                            @case(29)
                                                <span>Approved By {{ $med->medical_approval->user->name }}  <i class="fa fa-check-circle" style="color: #005eff" aria-hidden="true"></i></span>
                                                @break
                                            @case(15)
                                                <span>Waiting For Approval <i class="fa fa-spinner" aria-hidden="true"></i></span>
                                                @break
                                            @case(404)
                                                <span>Rejected By {{ $med->medical_approval->user->name }}  <i class="fa fa-exclamation-circle" style="color: red" aria-hidden="true"></i></span>
                                                @break
                                            @default
                                                <span>Unknown <i class="fa fa-bug" aria-hidden="true"></i></span>
                                        @endswitch

                                        {{-- Tampilkan informasi waktu terakhir diperbarui --}}
                                        <small class="text-danger font-italic">
                                             Updated At : 
                                            @if ($timeDiff < 60)
                                                Just now
                                            @elseif ($timeDiff < 3600)
                                                {{ floor($timeDiff / 60) }} minutes ago
                                            @elseif ($timeDiff < 86400)
                                                {{ floor($timeDiff / 3600) }} hours ago
                                            @else
                                                {{ floor($timeDiff / 86400) }} days ago
                                            @endif
                                        </small>
                                    </td>
                                </tr>
                                <tr class="table-sm">
                                    <td>Notes :</td>
                                    <td class="col-8">: {{$med->notes}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4" >
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
                <div class="text-right">
                    
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="row">                    
                    <div class="col-md-12">
                        <table class="table table-borderless">
                                <tr class="table-sm">
                                    <th>Status</th>
                                    <td class="font-weight-bold">:
                                        @switch($med->medical_payment->paid_status)
                                            @case(29)
                                                <span class="text-success font-weight-bold">Paid <i class="fa fa-check-circle" aria-hidden="true"></i></span>
                                                @break
                                            @case(20)
                                                <span>Waiting For Your Payment <i class="fa fa-spinner" aria-hidden="true"></i></span>
                                                @break
                                            @case(15)
                                            <span>Waiting For Approval <i class="fa fa-spinner" aria-hidden="true"></i></span>
                                            @break
                                            @case(404)
                                                <span>Rejected<i class="fa fa-exclamation-circle" style="color: red" aria-hidden="true"></i></span>
                                                @break
                                            @default
                                                <span>Unknown <i class="fa fa-bug" aria-hidden="true"></i></span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr class="table-sm">
                                    <th>Payment Date</th>
                                    <td class="font-weight-bold">: 
                                        @if($med->medical_payment->payment_date == NULL)
                                        <span class="text-danger">Not paid yet !</span>
                                        @else
                                        {{ $med->medical_payment->payment_date }}
                                        @endif
                                    </td>
                                </tr>
                                <tr class="table-sm text-success font-weight-bold">
                                    <th>Total Funds Provided</th>
                                    <td>: {{ 'Rp. '. $med->medical_payment->total_payment }}</td>
                                </tr>
                                <tr class="table-sm text-danger font-weight-bold">
                                    <th>Medical Deducted</th>
                                    <td>: {{ 'Rp. '.$med->medical_payment->total_payment }}</td>
                                </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row zoom90">
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Medical Details</h6>
                    <a data-toggle="modal" data-target="#ModalStatus" title="Status" class="btn btn-primary btn-sm font-weight-bold" >
                        <i class="fas fa-fw fa-info-circle justify-content-center"></i> Check Status
                    </a>
                @php
                    $approved = false;
                @endphp
                @if ($med->medical_approval->status == 29)

                @elseif ($med->medical_approval->status == 404)
                   <a class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#resubmitModal"><i class="fas fa-fw fa-paper-plane fa-sm text-white-50"></i> Re-Submit</a>
                @endif
                {{-- <a class="btn btn-secondary btn-sm" type="button" href="/medical/edit/{{ $med->id }}/download" target="_blank" id="manButton"><i class="fas fa-fw fa-download fa-sm text-white-50"></i> Download</a> --}}
                
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm zoom90">
                        <thead class="thead-light">
                            <tr>
                                {{-- <th>No</th> --}}
                                <th>Attachment</th>
                                <th>Reciept Date Expired</th>
                                <th>Description</th>
                                <th>Amount Request</th>
                                <th>Estimated Funds</th>
                                <th class="text-center">Action</th>
                        </thead>
                        <tbody>
							@foreach($medDet as $md)
							<tr>
								<td class="centered-button">
                                    {{-- <img style="width: 80px; height: 80px; object-fit:fill;" class="img-fluid" src="{{ url('/storage/med_pic/'.$md->mdet_attachment)}}" alt="Attachment" data-toggle="modal" data-target="#myModal{{ $md->mdet_id}}"> --}}
									<button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#myModal{{ $md->mdet_id }}">View</button>
                                </td>
                                <td>{{ $md->mdet_date_exp }}</td>
								<td>{{ $md->mdet_desc }}</td>
								<td>
                                    Rp. <span class="amount" id="amount">{{ $md->mdet_amount }}</span>
                                </td>
								<td>
                                    @if ($med->medical_approval->status == 29)
                                        Rp. <span class="amountApproved" id="amountApproved">{{ $md->amount_approved }}</span>
                                    @else
                                        Rp. 0
                                    @endif
                                </td>
                                <td class="row-col-2 justify-content-betwen text-center">
                                    @if ($med->medical_approval->status == 29)
                                        @if($md->status == false)
                                            <span class="text-sm text-danger font-weight-bold font-italic">Reject By Approver <i class="fa fa-exclamation-circle" style="color: red" aria-hidden="true"></i></span>
                                        @else
                                            <span class="text-sm text-italic">No Action Needed</span>
                                        @endif
                                    @else
                                        @if($md->status == false)
                                            <span class="text-sm text-danger text-italic">Reject By Approver <i class="fa fa-exclamation-circle" style="color: red" aria-hidden="true"></i></span>
                                        @else
                                            <a data-toggle="modal" data-target="#ModalMedDet{{ $md->mdet_id }}" title="Edit" class="btn btn-warning btn-sm" >
                                                <i class="fas fa-fw fa-edit justify-content-center"></i> Edit
                                            </a>
                                        @endif
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
                            <tr class="p-3 mb-2 bg-secondary text-white" style="border-bottom: 1px solid #dee2e6;">
                                <td colspan="3" class="text-center">Total</td>
								<td class="text-start">
                                    <span id="totalAmountDisplay" name="totalAmountDisplay"></span>
                                </td>
								<td colspan="2" class="text-start" id="totalAmountApproved">
                                    <span id="totalAmountApprovedDisplay" name="totalAmountApprovedDisplay"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Resubmit --}}
<form action="/medical/edit/{{ $med->id }}/resubmit" enctype="multipart/form-data"  method="POST">
@csrf
@method('PUT')
<div class="modal fade" id="resubmitModal" tabindex="-1" role="dialog" aria-labelledby="resubmitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title  text-white" id="staticBackdropLabel">Alert !!</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img class="mb-2" width="96" height="96" src="https://img.icons8.com/color/96/general-warning-sign.png" alt="general-warning-sign"/>
                <h5>Are you sure you want to resubmit your medical reimbursement?</h5>
                <input type="text" id="totalAmountInput" name="totalAmountInput" value="" hidden>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn-sm btn-danger" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn-sm btn-success" id="btn-submit">Yes Im Sure</button>
                {{-- <input type="submit" class="btn btn-primary btn-sm" value="Yes Im Sure" id="btn-submit"> --}}
            </div>
        </div>
    </div>
</div>
</form>

<!-- Modal Attachment -->
@foreach($medDet as $md)

    <div class="modal fade" id="ModalStatus" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title color" id="ModalStatusLabel">Status Medical</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered zoom90" width="100%" cellspacing="0">
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
                        <iframe src="{{ url('/medical/'.$md->mdet_attachment) }}" width="100%" height="500px" alt="Attachment"></iframe>
                    @else
                        <img src="{{ url('/medical/'.$md->mdet_attachment) }}" width="100%" alt="Attachment">
                    @endif
                </div>
            </div>
        </div>
    </div>

{{-- modal Medical detail --}}
<form action="/medical/edit/{{ $med->id }}/update/{{ $md->mdet_id }}" enctype="multipart/form-data" method="POST">
@csrf
@method('PUT')
    <div class="modal fade zoom90" id="ModalMedDet{{ $md->mdet_id}}" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Medical Detail Edit #{{ $md->mdet_id }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12" >
                        <div class="form-group">
                            <label for="password">Attachment :</label>
                             <div class="custom-file">
                                <input type="file" class="custom-file-input" id="attach{{ $md->mdet_id }}" name="attach_edit" value="" style="" accept=".jpg, .jpeg, .png, .pdf" onchange="updateAttachmentLabel('{{ $md->mdet_id }}')">
                                <label class="custom-file-label" for="file" id="custom-file-label{{ $md->mdet_id }}">{{ $md->mdet_attachment ?? 'Input Attachment' }}</label>
                            </div>
                        </div>
                        <small style="color: red;"><i>if you don't want to change the attachment, you don't need to input a new one.</i></small>
                        <div class="form-group">
                            <label for="password">Reciept Date :</label>
                            <input class="form-control flex" name="input_mdet_date_exp" type="date" value="{{ $md->mdet_date_exp }}"/>
                        </div>
                        <div class="form-group mt-2">
                            <label for="password">Description :</label>
                            <textarea class="form-control flex" name="input_mdet_desc" placeholder="Description..." style="height: auto">{{ $md->mdet_desc }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="password">Amount :</label>
                            <input class="form-control flex" name="input_mdet_amount" placeholder="Amount..." value="{{ $md->mdet_amount }}" oninput="formatAmount(this)"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn-danger" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button class="btn-sm btn-success">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endforeach

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

function updateAttachmentLabel(id) {
        const input = document.getElementById('attach' + id);
        const label = document.getElementById('custom-file-label' + id);

        if (input.files.length > 0) {
            label.innerText = input.files[0].name;
        } else {
            label.innerText = 'Input Attachment';
        }
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
