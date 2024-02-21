@extends('layouts.main')

@section('title', 'Reimbursement - ESS')

@section('active-page-reimburse')
active
@endsection

@section('content')
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
<form method="post" action="/reimbursement/create/submit" enctype="multipart/form-data" id="myForm">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4 zoom90">
        <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-hand-holding-usd"></i> New Reimbursement Request #{{ $nextID }}</h1>
        <button class="btn btn-md btn-primary shadow-sm" id="showConfirmation" type="button">+ Submit Request</button>
    </div>

        {{ csrf_field() }}


    <div class="row zoom90">
        <!-- Area Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Reimbursement Detail</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Type of Reimbursement <span class="text-danger">*</span>:</label><br />
                                    <label class="col-md-6">
                                        <input class="form-radio-input" type="radio" name="type_reimburse" id="projectRadio" value="Project" checked="">
                                        <span class="form-radio-sign">Project</span>
                                    </label>
                                    <label class="col-md-5">
                                        <input class="form-radio-input" type="radio" name="type_reimburse" id="othersRadio" value="Others">
                                        <span class="form-radio-sign">Others</span>
                                    </label>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group" id="projectSelect">
                                        <select class="form-control" name="project" required>
                                            <option value="Others" disabled selected>Select a type</option>
                                            <optgroup label="Projects" id="project">
                                                @foreach($projects as $project)
                                                <option value="{{$project->id}}">{{ $project->project_name}}</option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Others" style="display: none;" id="reimbursementType">
                                                <option value="Travel Reimbursement">Travel Reimbursement</option>
                                                <option value="Expense Reimbursement">Expense Reimbursement</option>
                                                <option value="Mileage Reimbursement">Mileage Reimbursement</option>
                                                <option value="Cell Phone Reimbursement">Cell Phone Reimbursement</option>
                                                <option value="Sales & Marketing Reimbursement">Sales & Marketing Reimbursement</option>
                                                <option value="Others Reimbursement">Others</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="comment">Notes <span class="text-danger">*</span>:</label>
                                        <textarea class="form-control" id="comment" rows="2" name="notes" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
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
                                            {{-- <div class="form-group">
                                                <label for="email">Account Number :</label>
                                                <input type="text" class="form-control" name="account_no" id="accountNo" value="{{ Auth::user()->users_detail->usr_bank_name }} : {{ Auth::user()->users_detail->usr_bank_account }}" readonly>
                                            </div> --}}
                                        </div>
                                        {{-- <div class="col-md-2 d-flex align-items-end">
                                            <div class="form-group">
                                                <button type="button" id="editButton" class="btn btn-primary">Edit</button>
                                            </div>
                                        </div>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- <div class="form-group" style="display: none;" id="reqApproval">
                                <label for="password">Request Approval To <span class="text-danger">*</span>:</label>
                                <select class="form-control" id="approver" name="approver">
                                    <option value="" disabled selected>Select Division...</option>
                                    @foreach($approver as $app)
                                        <option value="{{$app->group_id}}">{{ $app->user->name}}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <span class="text-danger">Reimbursement Policy</span>
                </div>
                <div class="card-body" style="background-color: rgb(247, 247, 247);">
                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">General Guidelines</h6>
                    <ul>
                        <li>All reimbursement requests must comply with company policies.</li>
                        <li>Employees are responsible for accurately documenting all expenses.</li>
                        <li>Reimbursements will only be provided for approved business-related expenses.</li>
                    </ul>

                    <h6 class="h6 mb-2 font-weight-bold text-gray-800">Submission Process</h6>
                    <ol>
                        <li>Attach all necessary receipts and supporting documentation.</li>
                        <li>Submit the reimbursement request to the appropriate supervisor or manager for approval.</li>
                        <li>Hardcopy of the receipt must be given to finance within 2 weeks</li>
                        <li class="text-danger">Payment will be made 14 weeks after approval and receipt submission to the Finance Department <a href="#">Read More.</a></li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary" id="judul">Receipt & Amount</h6>
                        <div class="text-right">
                            <div class="btn-group">
                                <button class="btn btn-danger btn-sm" type="button" id="undoButton" style="display: none; margin-right: 10px;"><i class="fas fa-fw fa-trash-alt"></i></button>
                                <input class="btn btn-primary btn-sm" type="button" id="copyButton" value="Add New Item">
                                </div>
                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-5 justify-content-between flex-row">
                                    <div class="row">
                                        <div class="col-md-9">
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-right">
                                                <button class="btn btn-danger btn-sm" type="button" id="undoButton" style="display:none; margin-left: 35px;"><i class="fas fa-fw fa-trash-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row" id="targetContainer">
                                    <div class="col-md-4" id="originalForm">
                                        <div class="row">
                                            <div class="col-lg-12 mr-2">
                                                <div class="form-group">
                                                    <input type="file" accept="image/*" onchange="loadFile(event)" class="file-input" id="receipt" name="receipt[]" multiple required>
                                                    <img id="output" style="margin-top: 10px;" width="100%" height="200"/>

                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="password">Receipt Date <span class="text-danger">*</span>:</label>
                                                    <input type="date" class="form-control" name="expiration[]" id="expiration" value="" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="password">Description <span class="text-danger">*</span>:</label>
                                                    <input type="text" class="form-control" name="description[]" id="description" value="" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <div class="input-group mb-2">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">Rp.</div>
                                                        </div>
                                                        <input type="text" class="form-control" id="amount" name="amount[]" oninput="formatAmount(this)" placeholder="Total Expenses" value="" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <br>
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
<script src="{{ asset('js/reimburse.js') }}"></script>
@endsection
