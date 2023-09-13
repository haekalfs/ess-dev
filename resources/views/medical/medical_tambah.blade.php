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
@csrf
<!-- Page Heading -->
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5 class="m-0 font-weight-bold text-primary">Medical Form Request Number # MED_0000{{ $nextId }}</h5>
                <div class="text-right">
                    <input class="btn btn-primary btn-sm" type="button" id="copyButton" value="Add Entry">
                    <input type="submit" class="btn btn-success btn-sm" value="Submit" id="btn-submit">
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7">
                            <small style="color: red;"><u><i>This Version Only Support 6 Items! You Can Edit It Later.</i></u></small>
                        </div>
                        <br>
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
                    <div class="row" style="zoom:96%; padding-left:20px">
                        <h6>Payment Method  :</h6>
                        <label class="col-md-2">
                            <input class="form-radio-input" type="radio" name="payment_method" id="payment_method" value="Transfer" checked="">
                            <span class="form-radio-sign">Transfer</span>
                        </label>
                        <label class="col-md-4">
                            <input class="form-radio-input" type="radio" name="payment_method" id="payment_method" value="Cash"  checked="">
                            <span class="form-radio-sign">Cash</span>
                        </label>
                    </div>
                    <br>
                    <div >
                        <div >
                            <div class="row">
                                <div id="originalForm" class="col-md-4">
                                    <div class="card"   style="border: 1px solid #ccc; padding: 10px; margin: 10px; width: 400px; display: inline-block;">
                                        <input type="text" class="form-control" name="no_item[]" id="no_item" hidden value="1" required>
                                        <div class="card-header py-3 d-flex flex-row align-items-start justify-content-between">
                                            <h5 class="m-0 font-weight-bold text-primary" id="items_label">ENTRY #</h5>
                                            <button type="button" id="closeButton" class="close" style="display:none;">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="col-md-12" >
                                                {{-- <div class="row"> --}}
                                                    <div class="form-group">
                                                        <input type="file" class="custom-file-input" id="attach" name="attach[]" value="" required style="">
                                                        <label class="custom-file-label" for="file" id="custom-file-label">Input Image</label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="amount">Amount (Rp) :</label>
                                                    <input type="text" class="form-control" name="amount[]" id="amount" value="" required oninput="formatAmount(this)" >
                                                </div>
                                                <div class="form-group">
                                                    <label for="description">Description :</label>
                                                    <textarea class="form-control" name="desc[]" rows="3" id="desc" required></textarea>
                                                {{-- </div> --}}
                                            </div>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                            <div >
                                <div >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <hr>
                <div class="col-md-12">
                    <small style="color: grey;"><i>*NOTE : Besides uploading files, the original receipt must be given to finance for verification</i></small>
                </div>
                <div class="text-right">
                    <input hidden id="totalAmountInput" name="totalAmountInput" value="">
                    <a>Total Amount :   Rp. </a><a class="text-danger" id="totalAmount" name="totalAmount"></a>
                </div>
            </div>
        </div>
    </div>
</form>  
<script>
const originalForm = document.querySelector("#originalForm");
const copyButton = document.querySelector("#copyButton");
const undoButton = document.querySelector("#undoButton");

let clonedForms = [];
let copyCounter = 0;

function formatInput(a, b){
    var fileInput = document.getElementById(a);
    var label = document.getElementById(b);
    fileInput.onchange = function() {
      var fileName = fileInput.value.split("\\").pop();      
      label.innerHTML = fileName;
    };
}

// Call formatInput function for the original form
formatInput("attach", "custom-file-label");

// Event listener for input amount changes
document.addEventListener("input", function (event) {
    if (event.target && event.target.id.startsWith("amount")) {
        calculateTotalAmount();
    }
});
copyButton.addEventListener("click", function (event) {
    // Prevent the copy button from submitting the form
    event.preventDefault();

    
        // Clone the original form
        const clonedForm = originalForm.cloneNode(true);

        // Set the input values to empty for the cloned form
        clonedForm.querySelector("#attach").value = "";
        clonedForm.querySelector("#amount").value = "";
        clonedForm.querySelector("#desc").value = "";

        const itemInput = clonedForm.querySelector("#attach");
        itemInput.name = "attach[]";

        const unitInput = clonedForm.querySelector("#amount");
        unitInput.name = "amount[]";

        const amountInput = clonedForm.querySelector("#desc");
        amountInput.name = "desc[]";

        clonedForm.querySelector("#no_item").value = `${copyCounter + 2}`;

        clonedForm.querySelector("#items_label").id = `items_label${copyCounter}`;
        clonedForm.querySelector("#attach").id = `attach${copyCounter}`;
        clonedForm.querySelector("#custom-file-label").id = `custom-file-label${copyCounter}`;
        clonedForm.querySelector("#amount").id = `amount${copyCounter}`;
        clonedForm.querySelector("#desc").id = `desc${copyCounter}`;

        const lastClonedForm = document.querySelector(`#originalForm + [id^="originalForm"]`);
        if (lastClonedForm) {
            lastClonedForm.after(clonedForm);
        } else {
            originalForm.after(clonedForm);
        }

        // Add the cloned form to the array
        clonedForms.push(clonedForm);

        // Reset the label for the cloned input
        const labelId = `custom-file-label${copyCounter}`;
        const clonedLabel = clonedForm.querySelector(`#${labelId}`);
        clonedLabel.textContent = "Input Image";

        // Call formatInput function for the cloned form
        formatInput(`attach${copyCounter}`, labelId);
        
        // Add a button to delete the cloned form
        const closeButton = clonedForm.querySelector("#closeButton");
        closeButton.style.display = "block"; // Show the close button

        closeButton.addEventListener("click", function () {
            // Remove the cloned form
            calculateTotalAmount(); // Recalculate the total amount
            clonedForm.remove();
        // ... Other cleanup or adjustments you need to do ...
        // Update the copyCounter and hide the undoButton if needed
        // copyCounter--;
        // if (copyCounter < 1) {
        //     undoButton.style.display = "none";
        // }
        });
        

        // Set the label for the newly added cloned form
        // clonedForm.querySelector(`#items_label${copyCounter}`).innerHTML = `ENTRY #${copyCounter + 2}`;

        // Increase the copy counter
        copyCounter++;

        // Show the undo button
        undoButton.style.display = "block";
    


    // Submit the form data to the Laravel controller
    const medForm = document.querySelector("#btn-submit");
        medForm.addEventListener("submit", function(event) {
        event.preventDefault();

        
        const formData = new FormData(medForm);

        const totalAmount = document.getElementById('totalAmount').value;
        formData.append('totalAmount', totalAmount);

        fetch("/medical/entry/store", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
        })
        .catch(error => {
            console.error(error);
        });
    });

});


// Event listener for input amount changes
document.addEventListener("input", function (event) {
    if (event.target && event.target.id.startsWith("amount")) {
        calculateTotalAmount();
    }
});


// Function to calculate total amount
function calculateTotalAmount() {
    let totalAmount = 0;

    // Get the amount from the original form
    const originalAmountInput = document.querySelector("#amount");
    const amountORValue = parseFloat(originalAmountInput.value.replace(/\./g, "").replace(",", ".")) || 0;

    // Add the amount from the original form to the total
    totalAmount += amountORValue;

    // Loop through cloned forms to add their amounts to the total
    for (let i = 0; i < clonedForms.length; i++) {
        const clonedForm = clonedForms[i];
        const amountInput = clonedForm.querySelector(`#amount${i}`);
        if (amountInput) {
            const amountValue = parseFloat(amountInput.value.replace(/\./g, "").replace(",", ".")) || 0;
            totalAmount += amountValue;
        }
    }

    // Update the total amount display
    const totalAmountDisplay = document.getElementById("totalAmount");
    totalAmountDisplay.textContent = totalAmount.toLocaleString().replace(/,/g, '.');  // Adjust as needed

   const totalAmountInput = document.getElementById("totalAmountInput");
    totalAmountInput.value = totalAmount;
}


undoButton.addEventListener("click", function (event) {
    event.preventDefault();

    if (clonedForms.length > 0) {
        const lastClonedForm = clonedForms.pop();
        lastClonedForm.remove();

        // Decrement the copy counter
        copyCounter--;

        // Hide the undo button if no more forms to undo
        if (clonedForms.length === 0) {
            undoButton.style.display = "none";
        }
    }
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

</script>
@endsection