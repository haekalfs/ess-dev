function checkFormValidity() {
    // Check if the form is valid
    if (document.getElementById('medForm').checkValidity()) {
        // If the form is valid, show the confirmation modal
        $('#confirmationModal').modal('show');
    } else {
        // If the form is not valid, trigger Bootstrap's form validation
        document.getElementById('medForm').reportValidity();
    }
}


// Add an event listener to the "Submit Request" button
document.getElementById('showConfirmation').addEventListener('click', checkFormValidity);

// Add an event listener to the "Submit Request" button
document.getElementById('confirmSubmit').addEventListener('click', function () {
    // Close the modal
    $('#confirmationModal').modal('hide');

    // Check date expiration
    var expirationDate = $('input[name^="date_exp"]').val();
    var currentDate = new Date();
    var sevenDaysBeforeNow = new Date(currentDate);
    sevenDaysBeforeNow.setDate(currentDate.getDate() - 60);

    if (new Date(expirationDate) < sevenDaysBeforeNow) {
        alert('The receipt date has expired and cannot be reimbursed for medical expenses.');
    } else {
        // Submit the form if it's valid
        if (document.getElementById('medForm').checkValidity()) {
            document.getElementById('medForm').submit();
        }
    }
});


// Get references to the radio buttons and the account number input field
const transferBankRadio = document.querySelector('input[value="Transfer Bank"]');
const cashRadio = document.querySelector('input[value="Cash"]');
const accountNumberField = document.getElementById('accountNumberField');

transferBankRadio.addEventListener('change', function () {
    if (this.checked) {
        // Show the account number input field
        accountNumberField.style.display = 'block';
    }
});

cashRadio.addEventListener('change', function () {
    if (this.checked) {
        // Hide the account number input field
        accountNumberField.style.display = 'none';
    }
});


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

function initializeForm() {
    // Call formatInput function for the original form
    formatInput("attach", "custom-file-label");
    displayImagePreview("attach", "output");
}

// Call the initialization function
initializeForm();

// Event listener for input amount changes
document.addEventListener("input", function (event) {
    if (event.target && event.target.id.startsWith("amount")) {
        calculateTotalAmount();
    }
});

// Event listener for input file changes
document.addEventListener("change", function (event) {
    if (event.target && event.target.id.startsWith("attach")) {
        displayImagePreview(event.target);
    }
});

// Fungsi untuk menampilkan gambar preview
function displayImagePreview(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const objectURL = URL.createObjectURL(file);

        // Menggunakan template literal untuk membuat ID output sesuai dengan nomor urutan
        const outputId = `output${input.id.substring(6)}`;
        const output = document.getElementById(outputId);

        output.src = objectURL;

        // Revoke the object URL after use to free up memory
        URL.revokeObjectURL(objectURL);
    }
}

copyButton.addEventListener("click", function (event) {
    // Prevent the copy button from submitting the form
    event.preventDefault();

    
        // Clone the original form
        const clonedForm = originalForm.cloneNode(true);

        // Set the input values to empty for the cloned form
        clonedForm.querySelector("#attach").value = "";
        clonedForm.querySelector("#amount").value = "";
        clonedForm.querySelector("#desc").value = "";
        // clonedForm.querySelector("#output").value = "";
        clonedForm.querySelector("#date_exp").value = "";

        const itemInput = clonedForm.querySelector("#attach");
        itemInput.name = "attach[]";

        // const FileInput = clonedForm.querySelector("#output");
        // FileInput.name = "output[]";

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
        clonedForm.querySelector("#date_exp").id = `date_exp${copyCounter}`;
        clonedForm.querySelector("#output").id = `output${copyCounter}`;

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
        clonedLabel.textContent = "Input File";
 
        const FileId = `output${copyCounter}`;
        const clonedFIle = clonedForm.querySelector(`#${FileId}`);
        clonedFIle.src = "";

        // Call formatInput function for the cloned form
        formatInput(`attach${copyCounter}`, labelId);
        // displayImagePreview(clonedFIle);
                
        // Add a button to delete the cloned form
        const closeButton = clonedForm.querySelector("#closeButton");
        closeButton.style.display = "block"; // Show the close button

        closeButton.addEventListener("click", function () {
            // Remove the cloned form
            calculateTotalAmount(); // Recalculate the total amount
            clonedForm.remove();
       
        });
        

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
