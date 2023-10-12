@extends('layouts.main')

@section('title', 'Reimbursement - ESS')

@section('active-page-reimburse')
active
@endsection

@section('content')
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
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Type of Reimbursement :</label><br />
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
                                    <select class="form-control" id="project" name="project">
                                        <option value="0" disabled selected>Select a project</option>
                                        @foreach($projects as $project)
                                        <option value="{{$project->id}}">{{ $project->project_name}}</option>
                                        @endforeach
                                    </select>
                                    <select class="form-control" id="reimbursementType" name="reimbursementType" style="display: none;">
                                        <option value="0" disabled selected>Select a type</option>
                                        <option value="Travel Reimbursement">Travel Reimbursement</option>
                                        <option value="Expense Reimbursement">Expense Reimbursement</option>
                                        <option value="Healthcare Reimbursement">Healthcare Reimbursement</option>
                                        <option value="Tuition Reimbursement">Tuition Reimbursement</option>
                                        <option value="Mileage Reimbursement">Mileage Reimbursement</option>
                                        <option value="Cell Phone Reimbursement">Cell Phone Reimbursement</option>
                                        <option value="Business Meal Reimbursement">Business Meal Reimbursement</option>
                                        <option value="Relocation Reimbursement">Relocation Reimbursement</option>
                                        <option value="Vendor or Supplier Reimbursement">Vendor or Supplier Reimbursement</option>
                                        <option value="Petty Cash Reimbursement">Petty Cash Reimbursement</option>
                                        <option value="Others Reimbursement">Others</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Payment Method :</label><br />
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
                </div>
            </div>
        </div>
    
        <!-- Area Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Additional Information</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Request Approval To :</label>
                                    <select class="form-control" id="approver" name="approver" required>
                                        @foreach($approver as $app)
                                        <option value="{{$app->id}}">{{ $app->department_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment">Notes :</label>
                            <textarea class="form-control" id="comment" rows="1" name="notes" required></textarea>
                        </div>
                    </div>
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
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <input type="file" class="file-input" id="receipt" name="receipt[]" multiple required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label for="password">Description :</label>
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