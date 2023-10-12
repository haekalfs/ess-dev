@extends('layouts.main')

@section('title', 'Medical - ESS')

@section('active-page-medical')
active
@endsection

@section('content')
<!-- Page Heading -->
<form method="POST" action="/medical/edit/{{  $med->id }}/resubmit" enctype="multipart/form-data">
<div class="d-sm-flex align-items-center justify-content-end">
    <div class="mb-3 text-right ">
        <a class="btn btn-secondary btn-sm" type="button" href="/medical/edit/{{ $med->id }}/download" target="_blank" id="manButton"><i class="fas fa-fw fa-download fa-sm text-white-50"></i> Download</a>
        <a class="btn btn-primary btn-sm" type="button" href="/medical/edit/{{ $med->id }}/resubmit" id="manButton"><i class="fas fa-fw fa-paper-plane fa-sm text-white-50"></i> Re-Submit</a>
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
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4" >
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Requested Information</h6>
                <div class="text-right">
                    <a class="btn btn-danger btn-sm" type="button" href="/medical/history" id="manButton"><i class="fas fa-fw fa-backward fa-sm text-white-50"></i> Back</a>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body zoom90">
                <div class="row">
                    <div class="col-md-3 align-items-center text-center">
                        <div class="col-md text-center">
                            @if($user_info->users_detail->profile_pic)
                                <img class="img-profile rounded-circle" height="150px"width="150px" style="object-fit:fill;" src="{{ url('/storage/profile_pic/'.$user_info->users_detail->profile_pic) }}" data-toggle="modal" data-target="#profileModal">
                            @else
                                <div class="img-profile rounded-circle no-image"><i class="no-image-text">No Image Available</i></div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th style="padding-left: 0;" class="m-0 font-weight-bold text-primary" colspan="2">Employee Information</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-sm">
                                    <td>Name</td>
                                    <td>: {{$user_info->name}}</td>
                                </tr>
								<tr class="table-sm">
                                    <td>Request Date</td>
                                    <td>: {{$med->med_req_date}}</td>
                                </tr>
								<tr class="table-sm">
                                    <td>Payment</td>
                                    <td>: {{$med->med_payment}}</td>
                                </tr>
								{{-- <tr class="table-sm">
                                    <td>Status</td>
                                    <td>: {{$med->med_status}}</td>
                                </tr> --}}
                            </tbody>
                        </table>
                    </div>
                    {{-- <div class="col-md-4">
                        <table class="table table-borderless">
							<thead>
                                <tr>
                                    <th style="padding-left: 0;" class="m-0  text-primary" colspan="2">Approval Information</th>
                                </tr>
                            </thead>
                           <tr>
                                <th>Approval By</th>
                                <td>: {{$med->approved_by}}</td>
                            </tr>
                            <tr>
                                <th>Approval Date</th>
                                <td>: {{$med->approved_date}}</td>
                            </tr>
                            <tr>
                                <th>Approval Notes</th>
                                <td>: {{$med->approved_note}}</td>
                            </tr>
                        </table>
                    </div> --}}
                </div>
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
                <h6 class="m-0 font-weight-bold text-primary">Medical Details Number #MED_0000{{ $med->med_number }}</h6>
                <div class="text-right">
                    {{-- <a class="btn btn-primary btn-sm" type="button"  data-toggle="modal" data-target="#addModal" id="addButton">View Details</a> --}}
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
                                    @php
                                        $approved = false;
                                    @endphp
                                    @foreach ($med->medical_approval as $m)
                                        @if ($m->status == 29)
                                            Rp. <span class="amountApproved" id="amountApproved">{{ $md->amount_approved }}</span>
                                            @php
                                                $approved = true;
                                                break;
                                            @endphp
                                        @endif
                                    @endforeach
                                    
                                    @unless ($approved)
                                        Rp. 0
                                    @endunless
                                </td>
                                <td class="row-cols-2 justify-content-betwen text-center">
                                    @php
                                        $approved = false;
                                    @endphp
                                    @foreach ($med->medical_approval as $m)
                                        @if ($m->status == 29 ||$m->status == 20)
                                           @php
                                                $approved = true;
                                                // break;
                                            @endphp
                                        @endif
                                        @if ($m->status == 404)
                                            @php
                                                $approved = false; // Set to false if 404 is encountered
                                                break;
                                            @endphp
                                        @endif
                                        
                                    @endforeach
                                    
                                    @unless ($approved)
                                         <a data-toggle="modal" data-target="#ModalMedDet{{ $md->mdet_id }}" title="Edit" class="btn btn-warning btn-sm" >
                                            <i class="fas fa-fw fa-edit justify-content-center"></i> Edit
                                        </a>
                                    @endunless

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
                                <td colspan="2" class="text-center">Total</td>
								<td class="text-start" id="totalAmount">
                                    <span id="totalAmountDisplay" name="totalAmountDisplay"></span>
                                    <input type="text" id="totalAmountInput" name="totalAmountInput" value="" hidden>
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
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Medical Workflow</h6>
                <div class="text-right">
                    {{-- <a class="btn btn-primary btn-sm" type="button"  data-toggle="modal" data-target="#addModal" id="addButton">View Details</a> --}}
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm zoom90">
                        <thead class="thead-light">
                            <tr>
                                <th>Approver</th>
                                <th>Status</th>
                                <th>Date Approved</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medApp as $workflow)
                                <tr>
                                    <td>{{$workflow->user->name}}</td>
                                    <td>@switch($workflow->status)
                                        @case('15') Waiting For Approval @break 
                                        @case('20') Approved @break 
                                        @case('29') Approved By Finance @break
                                        @case('404') Rejected @break
                                        @default Unknown Status @endswitch
                                    </td>
                                    <td>{{$workflow->approval_date}}</td>
                                    <td>{{$workflow->approval_notes}}</td>
                                </tr>
                            @endforeach
                            {{-- <tr style="border-bottom: 1px solid #dee2e6;">
                                <td colspan="6" class="text-center">Copyright @ Author of ESS Perdana Consulting</td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Profile -->
<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="text-right" width="100px">
    </div>
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="close-icon">
                    <img width="35" height="35" src="https://img.icons8.com/ios-glyphs/60/macos-close.png" alt="macos-close" data-dismiss="modal">
                </div>
                <img src="{{ url('/storage/profile_pic/'.$user_info->users_detail->profile_pic) }}" class="img-fluid" alt="Profile Picture">
            </div>
        </div>
    </div>
</div>

<!-- Modal Attachment -->
@foreach($medDet as $md)
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

{{-- </form> --}}
{{-- modal Medical detail --}}
<form action="/medical/edit/{{ $med->id }}/update/{{ $md->mdet_id }}" enctype="multipart/form-data" method="POST">
@csrf
@method('PUT')
    <div class="modal fade" id="ModalMedDet{{ $md->mdet_id}}" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Medical Detail Edit #{{ $md->mdet_id }} </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12" >
                        <div class="form-group">
                            <label for="password">Description :</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="inputAttach" name="inputAttach" aria-describedby="inputGroupFileAddon01" onchange="changeFileName('inputAttach', 'inputAttach-label')">
                                <label class="custom-file-label" for="file" id="inputAttach-label">Choose file</label>
                            </div>
                        </div>
                        <small style="color: red;"><i>if you don't want to change the attachment, you don't need to input a new attachment</i></small>
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
    totalAmountDisplay.textContent = "Rp. " + totalAmount.toLocaleString();
     // Update total amount approved in input field
    var totalAmountApprovedInput = document.getElementById("totalAmountApprovedInput");
    if (totalAmountApprovedInput) {
        totalAmountApprovedInput.value = totalAmount.toLocaleString().replace(/\./g, "").replace(",", ".");
    }



// Amount Approved
    // Mengubah titik menjadi angka biasa dan menghitung total amount amountApproved
    var amountElements = document.getElementsByClassName("amountApproved");
    var totalAmountApproved = 0;

    for (var i = 0; i < amountElements.length; i++) {
    var amountText = amountElements[i].textContent;
    var amountApprovedNumber = parseFloat(amountText.replace(/\./g, "").replace(",", "."));
    totalAmountApproved += amountApprovedNumber;
    }

    // Menampilkan total amount
    var totalAmountApprovedDisplay = document.getElementById("totalAmountApproved");
    totalAmountApprovedDisplay.textContent = "Rp. " + totalAmountApproved.toLocaleString();

</script>
@endsection
