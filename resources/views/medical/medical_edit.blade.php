@extends('layouts.main')

@section('title', 'Medical - ESS')

@section('active-page-medical')
active
@endsection

@section('content')
<!-- Page Heading -->
<form method="POST" action="/medical/edit/{{  $md->mdet_id }}" enctype="multipart/form-data">
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
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Employee Information</h6>
                <div class="text-right">
                    <a class="btn btn-danger btn-sm" type="button" href="{{ url()->previous() }}" id="manButton"><i class="fas fa-fw fa-backward fa-sm text-white-50"></i> Back</a>
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
								<tr class="table-sm">
                                    <td>Status</td>
                                    <td>: {{$med->med_status}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table class="table table-borderless">
							<thead>
                                <tr>
                                    <th style="padding-left: 0;" class="m-0 font-weight-bold text-primary" colspan="2">Approval Information</th>
                                </tr>
                            </thead>
                           <tr class="table-sm">
								<td>Approval By</td>
								<td>: Ronnyy</td>
							</tr>
							<tr class="table-sm">
								<td>Approval Date</td>
								<td>: 09-06-2023</td>
							</tr>
							<tr class="table-sm">
								<td>Approval Notes</td>
								<td>: Good</td>
							</tr>
                        </table>
                    </div>
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
                <h6 class="m-0 font-weight-bold text-primary">Medical Details Number #MED_0000{{ $med->id }}</h6>
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
									<button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#myModal{{ $md->mdet_id }}">View</button></td>
								<td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $md->mdet_desc }}
                                </td>
								<td id="amount">Rp. {{ $md->mdet_amount }}</td>
								<td>Rp. 50.000</td>
                                <td class="row-cols-2 justify-content-betwen text-center">
                                    <a data-toggle="modal" data-target="#ModalMedDet{{ $md->mdet_id }}" title="Edit" class="btn btn-warning btn-sm" >
                                        <i class="fas fa-fw fa-edit justify-content-center"></i>
                                    </a>
                                    <a href="#" title="Hapus" class="btn btn-danger btn-sm" ><i class="fas fa-fw fa-trash justify-content"></i></a>
                                </td>
							</tr>
							@endforeach
                            <tr class="p-3 mb-2 bg-secondary text-white" style="border-bottom: 1px solid #dee2e6;">
                                <td colspan="2" class="text-center">Total </td>
								<td class="text-start" id="totalAmount">
                                    <input hidden id="totalAmountInput" name="totalAmountInput" value="">
                                    <a>Total Amount :</a><a class="text-danger" id="totalAmount" name="totalAmount"></a>
                                </td>
								<td colspan="2" 
                                class="text-start">{{ $med->med_total_amount }}</td>
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
                                <th>Username</th>
                                <th>Project</th>
                                <th>Status</th>
                                <th>Approver</th>
                                <th>Notes</th>
                        </thead>
                        <tbody>
                            
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td colspan="6" class="text-center">Copyright @ Author of ESS Perdana Consulting</td>
                            </tr>
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

<!-- Modal -->
@foreach($medDet as $md)
    <div class="modal fade" id="myModal{{ $md->mdet_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
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
</form>
{{-- modal Medical detail --}}
<form action="/medical/update/medical_details/{{ $md->mdet_id }}" enctype="multipart/form-data">
@csrf
@method('PUT')
    <div class="modal fade" id="ModalMedDet{{ $md->mdet_id}}" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Medical Detail Edit </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-sm zoom90">
                        <tbody>
							<tr>
								<td rowspan="2" class="align-items-center text-center">
                                    <img class="img-fluid mt-4 mb-4" style="width: 350px; height: auto;" src="{{ url('/storage/med_pic/'.$md->mdet_attachment)}}"  alt="Attachment">
                                    <div class="custom-file mb-4">
                                        <input type="file" class="custom-file-input" id="inputAttach" name="inputAttach" aria-describedby="inputGroupFileAddon01" onchange="changeFileName('inputAttach', 'inputAttach-label')">
                                        <label class="custom-file-label" for="inputAttach-label" id="inputAttach-label">Choose file</label>
                                    </div>
                                </td>
								<td>
                                    <label for="password">Description :</label>
                                    <textarea class="form-control flex" name="input_mdet_desc" placeholder="Description..." style="height: auto">{{ $md->mdet_desc }}</textarea>
                                </td>
							</tr>
                            <tr>
                                <td><label for="password">Amount :</label>
                                    <input class="form-control flex" name="input_mdet_amount" placeholder="Amount..." value="Rp. {{ $md->mdet_amount }}"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
</script>
<script>
    
function changeFileName(inputId, labelId) {
  var input = document.getElementById(inputId);
  var label = document.getElementById(labelId);
  label.textContent = input.files[0].name;
}
</script>
@endsection
