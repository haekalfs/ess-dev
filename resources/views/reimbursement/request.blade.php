@extends('layouts.main')

@section('title', 'Reimbursement - ESS')

@section('active-page-reimburse')
active
@endsection

@section('content')
<form method="post" action="/reimbursement/create/submit" id="myForm">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-2 text-gray-800">New Reimbursement Request #1</h1>
        <input class="btn btn-md btn-primary shadow-sm" id="submit-request" type="submit" name="submit-request" value="+ Submit Request">
    </div>
    
        {{ csrf_field() }}
    
    
        <div class="row">
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
                                    <input class="form-radio-input" type="radio" name="type_reimburse" id="projectRadio" value="1" checked="">
                                    <span class="form-radio-sign">Project</span>
                                </label>
                                <label class="col-md-5">
                                    <input class="form-radio-input" type="radio" name="type_reimburse" id="othersRadio" value="0">
                                    <span class="form-radio-sign">Others</span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Payment Method :</label><br />
                                <label class="col-md-6">
                                    <input class="form-radio-input" type="radio" name="payment_method" value="1" checked="">
                                    <span class="form-radio-sign">Transfer Bank</span>
                                </label>
                                <label class="col-md-5">
                                    <input class="form-radio-input" type="radio" name="payment_method" value="2">
                                    <span class="form-radio-sign">Cash</span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Project :</label>
                                    <select class="form-control" id="project" name="project" required>
                                        @foreach($projects as $project)
                                        <option value="{{$project->id}}">{{ $project->project_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
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
                        <div class="form-group">
                            <label for="comment">Purpose of Reimbursement :</label>
                            <textarea class="form-control" id="comment" rows="3" name="purposes" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="comment">Notes :</label>
                            <textarea class="form-control" id="comment" rows="4" name="notes" required></textarea>
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
                            <input class="btn btn-primary btn-sm" type="button" id="copyButton" value="Add New Item">
                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        
                        <div class="col-md-12">
                            <div class="row">
                                {{-- <small style="color: red;"><u><i>This Version Only Support 1 Item!</i></u></small> --}}
                                <div class="col-md-7">
                                    <small style="color: red;"><u><i>*NOTE : Besides uploading files, the original receipt must be given to finance for verification.</i></u></small>
                                </div>
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
                            <hr>
                            <div class="col-lg-12">
                                <div class="row" id="targetContainer">
                                    <div class="col-md-4" id="originalForm">
                                        
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <input type="file" class="file-input" id="receipt" name="receipt[]">
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
                                                            <input type="text" class="form-control" id="amount" name="amount[]" oninput="formatAmount(this)" placeholder="Amount" value="">
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
        </div><br>
    </form>
@endsection

@section('javascript')
<script src="{{ asset('js/reimburse.js') }}"></script>
@endsection