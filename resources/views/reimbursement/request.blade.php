@extends('layouts.main')

@section('title', 'Reimbursement - ESS')

@section('active-page-reimburse')
active
@endsection

@section('content')
<form method="post" action="/po/store" id="myForm">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-2 text-gray-800">New Reimbursement Request #1</h1>
        <input class="btn btn-md btn-primary shadow-sm" id="btn-submit" type="submit" name="submit_task" value="+ Submit Request">
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
                                    <input class="form-radio-input" type="radio" name="type_reimburse" value="1" checked="">
                                    <span class="form-radio-sign">Project</span>
                                </label>
                                <label class="col-md-5">
                                    <input class="form-radio-input" type="radio" name="type_reimburse" value="0">
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
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="formContainer" class="row">
                                        <div class="col-md-4" id="originalForm">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <input type="file" class="custom-file-input" id="receipt" name="receipt[]">
                                                            <label class="custom-file-label" for="receipt" id="receipt-label">Choose file</label> 
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="password">Description :</label>
                                                            <input type="text" class="form-control" name="unit[]" id="unit" value="" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <div class="input-group mb-2">
                                                                <div class="input-group-prepend">
                                                                    <div class="input-group-text">Rp.</div>
                                                                </div>
                                                                <input type="text" class="form-control" id="result" name="result[]" placeholder="amount" value="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div id="targetContainer">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="m-0 font-weight-bold text-right" style="color: red;"><span id="ppn-label">: Total</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><br>
    </form>
<style>

</style>
<script>
    const originalForm = document.querySelector("#originalForm");
    const copyButton = document.querySelector("#copyButton");
    const undoButton = document.querySelector("#undoButton");
    const targetContainer = document.querySelector("#targetContainer");
    const form = document.querySelector("#btn-submit");

    let copyCounter = 0;

    copyButton.addEventListener("click", function (event) {
        event.preventDefault();

        if (copyCounter < 2) {
            const clonedForm = originalForm.cloneNode(true);

            // Modify the input's name attributes
            const itemInput = clonedForm.querySelector("#receipt");
            itemInput.name = "receipt[]";

            const unitInput = clonedForm.querySelector("#unit");
            unitInput.name = "unit[]";

            const priceInput = clonedForm.querySelector("#result");
            priceInput.name = "result[]";

            // Clear input values
            itemInput.value = "";
            unitInput.value = "";
            priceInput.value = "";

            // Generate unique IDs for cloned elements
            itemInput.id = `receipt${copyCounter}`;
            unitInput.id = `unit${copyCounter}`;
            priceInput.id = `result${copyCounter}`;

            // Append the cloned form to the target container
            targetContainer.appendChild(clonedForm);

            // Increase the copy counter
            copyCounter++;

            // Show the undo button
            undoButton.style.display = "block";
        }
    });

    undoButton.addEventListener("click", function (event) {
        event.preventDefault();

        if (copyCounter > 0) {
            // Remove the last cloned form
            targetContainer.removeChild(targetContainer.lastChild);

            // Decrease the copy counter
            copyCounter--;

            // Hide the undo button if necessary
            if (copyCounter === 0) {
                undoButton.style.display = "none";
            }
        }
    });

    // Submit the form data to the Laravel controller
    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(form);

        fetch("/reimbursement/create/submit", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                console.log(data);
            })
            .catch((error) => {
                console.error(error);
            });
    });
</script>

@endsection

@section('javascript')
<script src="{{ asset('js/reimburse.js') }}"></script>
@endsection