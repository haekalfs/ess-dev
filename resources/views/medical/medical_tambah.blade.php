@extends('layouts.main')
@section('content')
<form method="post" action="/medical/entry/store" id="medForm">
@csrf
<!-- Page Heading -->
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5 class="m-0 font-weight-bold text-primary">Medical Form Request Number #{{ $nextMedNumber }}</h5>
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
                            <small style="color: red;"><u><i>This Version Only Support 3 Items! You Can Edit It Later.</i></u></small>
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
                            <input class="form-radio-input" type="radio" name="payment_method" id="payment_method" value="Cash" checked="">
                            <span class="form-radio-sign">Transfer</span>
                        </label>
                        <label class="col-md-4">
                            <input class="form-radio-input" type="radio" name="payment_method" id="payment_method" value="Transfer" checked="">
                            <span class="form-radio-sign">Cash</span>
                        </label>
                    </div>
                    <br>
                    <div class="col-md-12">
                        <div class="row" style="padding-left:40px">
                            <div class="col-md-4">
                                <div class="col" style="zoom: 80%;">
                                    <div class="card" id="originalForm"  >
                                        <input type="text" class="form-control" name="no_item[]" id="no_item" hidden value="1" required>
                                        <div class="card-header py-3 d-flex flex-row align-items-start justify-content-between">
                                            <h5 class="m-0 font-weight-bold text-primary" id="items_label">ENTRY #</h5>
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
                                                    <input type="text" class="form-control" name="amount[]" id="amount" value="" required>
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
                            <div class="col">
                                <div class="row" style="zoom: 80%;">
                                    <div class="card-columns" id="targetContainer" style="display:flex; ">

                                    </div>
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
                    <a>Total Amount :</a><a class="text-danger" id="totalAmount" name="totalAmount"></a>
                </div>
            </div>
        </div>
    </div>
</form>  
<script>
const originalForm = document.querySelector("#originalForm");
const copyButton = document.querySelector("#copyButton");
const undoButton = document.querySelector("#undoButton");
const targetContainer = document.querySelector("#targetContainer");

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

    
copyButton.addEventListener("click", function (event) {
    // Prevent the copy button from submitting the form
    event.preventDefault();

    if (copyCounter < 2) {
        // Clone the original form
        const clonedForm = originalForm.cloneNode(true);

        const itemInput = clonedForm.querySelector("#attach");
        itemInput.name = "attach[]";

        const unitInput = clonedForm.querySelector("#amount");
        unitInput.name = "amount[]";

        const amountInput = clonedForm.querySelector("#desc");
        amountInput.name = "desc[]";
        
        const totalAmount = document.getElementById('#totalAmount');

        clonedForm.querySelector("#no_item").value = `${copyCounter + 2}`;
        clonedForm.querySelector("#attach").value = ``;
        clonedForm.querySelector("#amount").value = ``;
        clonedForm.querySelector("#desc").value = ``;
        

        clonedForm.querySelector("#items_label").id = `items_label${copyCounter}`;
        clonedForm.querySelector("#attach").id = `attach${copyCounter}`;
        clonedForm.querySelector("#custom-file-label").id = `custom-file-label${copyCounter}`;
        clonedForm.querySelector("#amount").id = `amount${copyCounter}`;
        clonedForm.querySelector("#desc").id = `desc${copyCounter}`;
        

        targetContainer.id = `targetContainer` + targetContainer.childElementCount;
        // Append the cloned form to the target container
        targetContainer.appendChild(clonedForm);

        // Add the cloned form to the array
        clonedForms.push(clonedForm);

        document.querySelector(`#items_label0`).innerHTML = `ENTRY #2 `;
        // Increase the copy counter
        copyCounter++;
        
        // Show the undo button
        undoButton.style.display = "block";
        
        clonedForm.querySelector(`#items_label1`).innerHTML = `ENTRY #3 `;
    }
   
    document.querySelector(`#items_label0`).innerHTML = `ENTRY #2 `;
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

// Function to undo the creation of the copied form
function undoCreation() {
    if (clonedForms.length > 0) {
        const lastClonedForm = clonedForms.pop();
        targetContainer.removeChild(lastClonedForm);
        copyCounter--;
        
        if (clonedForms.length === 0) {
            undoButton.style.display = "none";
        }
    }

}

undoButton.addEventListener("click", function (event) {
    // Prevent the undo button from submitting the form
    event.preventDefault();
    undoCreation();
    document.getElementById("totalAmount").innerHTML= document.getElementById("amount").value.replace(formatRupiah);
});

function formatRupiah(angka, prefix) {
    var number_string = angka.toString().replace(/[^,\d]/g, ''),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    if (rupiah.length > 9) {
        var billions = rupiah.substring(0, rupiah.length - 9);
        var millions = rupiah.substring(rupiah.length - 9);
        rupiah = billions + "" + millions + "";
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}


// function calculate(firstNumberId, secondNumberId, thirdNumberId, totalAmount) {
//     var amount = document.getElementById("amount").value.replace(/[^0-9]/g, '');
//     var amount = document.getElementById("amount0").value;
//     var amount = document.getElementById("amount1").value;

//     var total = amount + amount0 + amount1;
    
//     document.getElementById(totalAmount).value = formatRupiah(totalAmount);
// }

document.getElementById("originalForm").addEventListener("input", function() {
    let attachment = formatInput("attach","custom-file-label");
    let amount = document.getElementById("amount").value.replace(/[^0-9]/g, '');
    let total = +amount
    let formattedAmount = formatRupiah(total, amount);
    document.getElementById("totalAmount").innerHTML = formattedAmount;
});

document.getElementById("targetContainer").addEventListener("input", function() {
    let attachment = formatInput("attach0","custom-file-label0");
    let amount = document.getElementById("amount").value.replace(/[^0-9]/g, '');
    let amount1 = document.getElementById("amount0").value.replace(/[^0-9]/g, '');
    let total = +amount + +amount1;
    let formattedAmount = formatRupiah(total, amount1);
    document.getElementById("totalAmount").innerHTML = formattedAmount;
});

document.getElementById("targetContainer").addEventListener("input", function() {
    let attachment = formatInput("attach1","custom-file-label1");
    let amount = document.getElementById("amount").value.replace(/[^0-9]/g, '');
    let amount1 = document.getElementById("amount0").value.replace(/[^0-9]/g, '');
    let amount2 = document.getElementById("amount1").value.replace(/[^0-9]/g, '');
    let total = +amount + +amount1 + +amount2;
    let formattedAmount = formatRupiah(total, amount2);
    document.getElementById("totalAmount").innerHTML= formattedAmount;
});



</script>
@endsection
