@extends('layouts.main')

@section('active-page-medicals')
active
@endsection
@section('content')
<!-- Page Heading -->

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
<form method="POST" action="/medical/entry/store" enctype="multipart/form-data" id="medForm">
    <div class="d-sm-flex align-items-center justify-content-between mb-4 zoom90">
        <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-hand-holding-medical"></i> New Medical Request # MED_{{ $nextId }}</h1>
        <button class="mr-2 btn btn-md btn-primary shadow-sm" id="showConfirmation" type="button"><i class="fa fa-paper-plane" aria-hidden="true"></i> Submit Request</button>
    </div>
@csrf
<!-- Page Heading -->
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-12 zoom80">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5 class="m-0 font-weight-bold text-primary">Medical Details</h5>
            </div>
            <!-- Card Body -->
            <div class="card-body row">
                <div class="col-md-3">
                    <label>Payment Method <span class="text-danger">*</span>:</label><br />
                    <label class="col-md-5">
                        <input class="form-radio-input" type="radio" name="payment_method" value="Cash" checked="">
                        <span class="form-radio-sign">Cash</span>
                    </label>
                    <label class="col-md-6">
                        <input class="form-radio-input" type="radio" name="payment_method" value="Transfer Bank">
                        <span class="form-radio-sign">Transfer Bank</span>
                    </label>
                </div>
                <div class="col-md-12" id="accountNumberField" style="display: none;">
                    <div class="row">
                        <div class="col-md-12"><br>
                            <h6 class="m-0 font-weight-bold text-primary" id="judul">Your Account Number : {{ Auth::user()->users_detail->usr_bank_account }} ({{ Auth::user()->users_detail->usr_bank_name }})</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <!-- Card Header -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5 class="m-0 font-weight-bold text-primary">Receipt & Amount</h5>
                <div class="row justify-content-between text-right">
                    <button class="btn btn-danger btn-sm" type="button" id="undoButton" style="display:none; margin-left: 35px;"><i class="fas fa-fw fa-undo"></i> Undo</button>
                    <input class="ml-2 mr-2 btn btn-primary btn-sm" type="button" id="copyButton" value="+ Add Entry">
                    {{-- <input type="submit" class="btn btn-success btn-sm" value="Submit" id="btn-submit"> --}}
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7">
                            <small style="color: red;"><u><i>This Version Only Support 6 Items! You Can Edit It Later.</i></u></small>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div id="originalForm" class="col-md-4">
                            <div class="card mt-4">
                                <input type="text" class="form-control" name="no_item[]" id="no_item" hidden value="1" required>
                                <div class="card-header py-3 d-flex flex-row align-items-start justify-content-between">
                                    <h5 class="m-0 font-weight-bold text-primary" id="items_label">ENTRY #</h5>
                                    <button type="button" id="closeButton" class="close" style="display:none;">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="col-md-12" >
                                        <div class="form-group"  style="position: relative;">
                                            <input type="file" onchange="loadFile(event)" class="custom-file-input" id="attach" name="attach[]" value="" multiple required accept=".jpg, .png, .jpeg, .pdf">
                                            <label class="custom-file-label" for="file" id="custom-file-label">Input File</label>
                                            <iframe id="output" style="margin-top: 10px;" width="100%" height="250"></iframe>
                                            <small style="color: red;"><i>* Max File 2 MB</i></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Receipt Expiration <span class="text-danger">*</span> :</label>
                                            <input class="form-control" type="date" name="date_exp[]" id="date_exp" required/>
                                        </div>
                                        <label for="amount">Amount <span class="text-danger">*</span> :</label>
                                        <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">Rp.</div>
                                            </div>
                                            <input type="text" class="form-control" id="amount" name="amount[]" oninput="formatAmount(this)" value="" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description <span class="text-danger">*</span> :</label>
                                            <input class="form-control" name="desc[]" id="desc" required/>
                                        </div>
                                    </div>
                                </div>    
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="col-md-12">
                    <small style="color: black;"><i>*NOTE : <b>Besides uploading files, the original receipt must be given to finance within 7 days</b></i></small>
                </div>
                <div class="text-right">
                    <input hidden id="totalAmountInput" name="totalAmountInput" value="">
                    <a>Total Amount :   Rp. </a><a class="text-danger" id="totalAmount" name="totalAmount"></a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Submission</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to submit this request?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSubmit">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</form>  
@endsection

@section('javascript')
<script src="{{ asset('js/medical.js') }}"></script>
@endsection

