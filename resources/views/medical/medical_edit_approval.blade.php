@extends('layouts.main')

@section('title', 'Medical - ESS')

@section('active-page-medical')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h4 class=" mb-2 text-gray-800"><i class="fas fa-fw fa-hand-holding-medical"></i><b> Medical Request Number # MED_{{ $med->id }}</b></h4>
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
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Request Information</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th>Employee ID</th>
                        <td style="text-align: start; font-weight:500">: {{$med->user->users_detail->employee_id}}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td style="text-align: start; font-weight:500">: {{$med->user->name}}</td>
                    </tr>
                    <tr>
                        <th>Service Year</th>
                        <td style="text-align: start; font-weight:500" >: {{ $total_years_of_service }} Years</td>
                    </tr>
                    <tr>
                        <th>Position</th>
                        <td style="text-align: start; font-weight:500">: {{ $med->user->users_detail->position->position_name }}</td>
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
                <h6 class="m-0 font-weight-bold text-primary">Detail Information</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                    <tr>
                        <tr>
                            <th>Request Date</th>
                            <td style="text-align: start; font-weight:500">: {{$med->med_req_date}}</td>
                        </tr>
                        <tr>
                            <th>Medical Type</th>
                            <td style="text-align: start; font-weight:500">: {{ $med->medical_type->name_type }} {!! $med->medical_type->icon !!}</td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td style="text-align: start; font-weight:500">: {{$med->med_payment}}</td>
                        </tr>
                        <tr>
                            <th>Notes</th>
                            <td style="text-align: start; font-weight:500">: {{ $med->notes }}</td>
                        </tr>
                    </tr>
                </table>
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
                <div class="text-right">
                    <a class="btn btn-success btn-sm" type="button" id="addButton" onclick="validateAndOpenModal()">
                        <i class="fas fa-check"></i> Approve
                    </a>
                    <a class="btn btn-danger btn-sm" type="button"  data-toggle="modal" data-target="#rejectModal" id="addButton"><i class="fas fa-times"></i> Reject All</a>
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
                                <th>Reciept Date</th>
                                <th>Description</th>
                                <th>Amount Request</th>
                                <th>Estimate Approved</th>
                                <th class="text-center">Action</th>
                        </thead>
                        <tbody>
							@foreach($medDet as $md)
							<tr>
								<td class="centered-button">
                                    {{-- <img style="width: 80px; height: 80px; object-fit:fill;" class="img-fluid" src="{{ url('/storage/med_pic/'.$md->mdet_attachment)}}" alt="Attachment" data-toggle="modal" data-target="#myModal{{ $md->mdet_id}}"> --}}
									<button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#myModal{{ $md->mdet_id }}">View</button>
                                </td>
                                <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $md->mdet_date_exp }}
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
                                <td class="col-2 justify-content-betwen text-center">
                                    @if($md->status == true)
                                        @if($md->receipt_real == false)
                                        <a id="confirmReceiptBtn{{$md->mdet_id}}" title="Confirm" class="btn btn-secondary btn-sm confirm-receipt-btn">
                                            <i class="fas fa-fw fa-notes-medical justify-content-center"></i><span class="text-sm"> Confirm Receipt</span>
                                        </a>
                                        @else
                                        <a data-toggle="modal" data-target="#ModalMedDet{{ $md->mdet_id }}" title="Update" class="btn btn-primary btn-sm" >
                                            <i class="fas fa-fw fa-edit justify-content-center"></i> Update
                                        </a>
                                        @endif
                                        <!-- Periksa jika hanya ada satu objek $medDet -->
                                        @if(count($medDet) > 1)
                                        {{-- <a  href="/medical/edit/{{ $med->id }}/delete/{{ $md->mdet_id }}" title="Delete" class="btn btn-danger btn-sm" ><i class="fas fa-ban"></i> Reject</a> --}}
                                        <a  onclick="confirmDelete('{{ $med->id }}', '{{ $md->mdet_id }}')" title="Delete" class="btn btn-danger btn-sm">
                                            <i class="fas fa-ban"></i> Reject
                                        </a>
                                        @endif
                                    @else
                                        <span class="text-sm font-italic font-weight-bold">You've Rejected this !!</span>
                                    @endif
                                </td>
							</tr>
							@endforeach
                            <tr class="p-3 mb-2 bg-secondary text-white" style="border-bottom: 1px solid #dee2e6;">
                                <td colspan="3" class="text-center">Total</td>
								<td class="text-start">
                                    <span id="totalAmount" name="totalAmount"></span>
                                    <input id="totalAmount" name="totalAmount" value="" hidden>
                                </td>
								<td colspan="3" class="text-start" >
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
<form action="/medical/approval/{{ $med->id }}/update/{{ $md->mdet_id }}" method="POST">
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
    <div class="modal fade" id="ModalMedDet{{ $md->mdet_id}}" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="myModalLabel" style="color: white">Medical Detail Edit #{{ $md->mdet_id }} </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="zoom90 m-2">
                        <div>
                            <label for="password">Amount of Employee's Requested :</label>
                            <div class="input-group flex-nowrap">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="addon-wrapping">Rp. </span>
                                </div>
                                <input class="form-control flex"  value="{{ $md->mdet_amount }}" oninput="formatAmount(this)" disabled/>
                            </div>
                        </div>
                        <div class="mt-2">
                            <label for="password">Amount Approved :</label>
                            <div class="input-group flex-nowrap">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="addon-wrapping">Rp. </span>
                                </div>
                                <input class="form-control flex" name="input_mdet_amount_approved" placeholder="Amount Approved..." value="{{ $md->amount_approved }}" oninput="formatAmount(this)"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button class="btn btn-sm btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endforeach

{{-- Modal Approve --}}
<form action="/medical/approval/{{ $med->id }}/approve" method="POST" enctype="multipart/form-data" id="approve">
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
                                <input type="text" id="totalAmountApprovedInput" name="totalAmountApprovedInput" value="" hidden required>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <label for="password">Notes :<small class="text-danger">*</small></label>
                                <textarea class="form-control flex" name="input_approve_note" placeholder="Notes..." required ></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                <input type="button" class=" btn btn-success btn-sm" value="Submit" id="btn-submit" onclick="validateForm()">
            </div>
        </div>
    </div>
</div>
</form>
{{-- Modal Reject --}}
<form action="/medical/approval/{{ $med->id }}/reject" method="POST" enctype="multipart/form-data" id="reject">
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
                                <label for="password">Notes :<small class="text-danger">*</small></label>
                                <textarea class="form-control flex" name="input_reject_note" placeholder="Notes..." ></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer ">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                <button class="btn btn-sm btn-primary">Reject</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Loop melalui setiap tombol dan menambahkan event listener untuk setiap klik
    var buttons = document.querySelectorAll('.confirm-receipt-btn');
    buttons.forEach(function(button) {
        button.addEventListener('click', function() {
            var mdet_id = button.getAttribute('id').replace('confirmReceiptBtn', '');

            // Mengirimkan permintaan Ajax untuk memanggil fungsi receipt di MedicalController
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/medical/approval/receipt/' + mdet_id, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Berhasil, reload halaman
                    location.reload();
                } else {
                    // Gagal, tampilkan pesan kesalahan jika perlu
                    alert('Failed to confirm receipt');
                }
            };
            xhr.send();
        });
    });
});
    function validateAndOpenModal() {
    var receiptRealArray = <?php echo json_encode($md->receipt_real); ?>;
    var statusArray = <?php echo json_encode($md->status); ?>;

    // Mengecek apakah semua receipt adalah true jika statusnya true
    var allStatusAreTrue = true;
    if (Array.isArray(statusArray)) {
        for (var i = 0; i < statusArray.length; i++) {
            if (!statusArray[i]) {
                allStatusAreTrue = false;
                break;
            }
        }
    } else {
        allStatusAreTrue = statusArray;
    }

    // Mengecek apakah semua receipt adalah true jika statusnya true
    var allReceiptsAreTrue = true;
    if (Array.isArray(receiptRealArray)) {
        for (var i = 0; i < receiptRealArray.length; i++) {
            if (!receiptRealArray[i]) {
                allReceiptsAreTrue = false;
                break;
            }
        }
    } else {
        allReceiptsAreTrue = receiptRealArray;
    }

    // Menampilkan modal jika semua receipt adalah true
    if (allStatusAreTrue) {
        if (allReceiptsAreTrue) {
            $('#approveModal').modal('show');
        } else {
            alert('All receipts must be confirmed first for approval');
        }
    } else {
        alert('You already reject all receipts');
    }
}

</script>
@section('javascript')
<script src="{{ asset('js/medical_approval.js') }}"></script>
@endsection
@endsection
