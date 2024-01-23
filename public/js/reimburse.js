function checkFormValidity() {
    // Check if the form is valid
    if (document.getElementById('myForm').checkValidity()) {
        // If the form is valid, show the confirmation modal
        $('#confirmationModal').modal('show');
    } else {
        // If the form is not valid, trigger Bootstrap's form validation
        document.getElementById('myForm').reportValidity();
    }
}


// Add an event listener to the "Submit Request" button
document.getElementById('showConfirmation').addEventListener('click', checkFormValidity);

// Add an event listener to the "Submit Request" button
document.getElementById('confirmSubmit').addEventListener('click', function () {
    // Close the modal
    $('#confirmationModal').modal('hide');

    // Check date expiration
    var expirationDate = $('input[name^="expiration"]').val();
    var currentDate = new Date();
    var sevenDaysBeforeNow = new Date(currentDate);
    sevenDaysBeforeNow.setDate(currentDate.getDate() - 7);

    if (new Date(expirationDate) < sevenDaysBeforeNow) {
        alert('Receipt date is expired & cannot be reimbursed!.');
    } else {
        // Submit the form if it's valid
        if (document.getElementById('myForm').checkValidity()) {
            document.getElementById('myForm').submit();
        }
    }
});

// Get references to the radio buttons and the "project" select element
const projectSelect = document.getElementById('project');
const projectRadio = document.getElementById('projectRadio');
const othersRadio = document.getElementById('othersRadio');
var reimbursementTypeSelect = document.getElementById('reimbursementType');

// Get references to the radio buttons and the account number input field
const transferBankRadio = document.querySelector('input[value="Transfer Bank"]');
const cashRadio = document.querySelector('input[value="Cash"]');
const accountNumberField = document.getElementById('accountNumberField');

// Get references to the input field and the "Edit" button
const accountNoInput = document.getElementById('accountNo');
const editButton = document.getElementById('editButton');

// // Add an event listener to the "Edit" button
// editButton.addEventListener('click', function () {
//     // Toggle the readonly attribute of the input field
//     accountNoInput.readOnly = !accountNoInput.readOnly;

//     // Change the button text based on the input field's readonly state
//     if (accountNoInput.readOnly) {
//         editButton.textContent = 'Edit';
//     } else {
//         editButton.textContent = 'Save'; // You can change this to 'Save' or any desired text
//     }
// });

// Add event listeners to the radio buttons
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

$(document).ready(function() {
    var reqApprovalContainer = $('#reqApproval');
    projectRadio.addEventListener('change', function () {
        if (this.checked) {
            // Enable the "project" select element and set it as required
            reqApprovalContainer.hide();
            projectSelect.style.display = 'block';
            projectSelect.required = true;
            reimbursementTypeSelect.required = false;
            reimbursementTypeSelect.style.display = 'none';
        }
    });

    othersRadio.addEventListener('change', function () {
        if (this.checked) {
            // Disable the "project" select element and set reimbursementType as required
            reqApprovalContainer.show();
            projectSelect.style.display = 'none';
            projectSelect.required = false;
            reimbursementTypeSelect.style.display = 'block';
            reimbursementTypeSelect.required = true;
        }
    });
});

    const originalForm = document.querySelector("#originalForm");
    const copyButton = document.querySelector("#copyButton");
    const undoButton = document.querySelector("#undoButton");
    const targetContainer = document.querySelector("#targetContainer");
    const form = document.querySelector("#btn-submit");

    let copyCounter = 0;

    copyButton.addEventListener('click', function(event) {
        event.preventDefault();

        if (copyCounter < 6) {
            const clonedForm = originalForm.cloneNode(true);

            // Modify the IDs and names of elements inside the cloned form
            const itemInput = clonedForm.querySelector("#receipt");
            const descriptionInput = clonedForm.querySelector("#description");
            const expirationInput = clonedForm.querySelector("#expiration");
            const priceInput = clonedForm.querySelector("#amount");

            itemInput.name = "receipt[]";
            descriptionInput.name = "description[]";
            priceInput.name = "amount[]";

            // Clear input values
            itemInput.value = "";
            descriptionInput.value = "";
            expirationInput.value = "";
            priceInput.value = "";

            // Generate unique IDs for cloned elements
            itemInput.id = `receipt${copyCounter}`;
            descriptionInput.id = `description${copyCounter}`;
            priceInput.id = `amount${copyCounter}`;
            // Append the cloned form to the target container
            targetContainer.appendChild(clonedForm);

            // Increase the copy counter
            copyCounter++;

            // Show the undo button
            undoButton.style.display = "block";
        }
    });

    // Update the loadFile function to handle multiple previews
    const loadFile = function(event) {
        const output = event.target.nextElementSibling; // Get the next element (image preview) after the input
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src); // free memory
        };
    };

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

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData();

        // Append each file input separately
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach((fileInput, index) => {
            // Check if the file input has files selected
            if (fileInput.files.length > 0) {
                // Append the file(s) with the same name attribute
                formData.append('receipt[]', fileInput.files[0]); // Assuming you want to handle only the first file
            }
        });

        // Append other form fields as needed
        formData.append('description', document.getElementById('description').value);
        formData.append('expiration', document.getElementById('expiration').value);
        formData.append('amount', document.getElementById('amount').value);

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


    function formatAmount(input) {
        // Remove non-numeric characters
        let amount = input.value.replace(/[^0-9]/g, '');

        // Add thousands separator (dots) using a regular expression
        amount = amount.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        // Set the formatted value back to the input
        input.value = amount;
    }

    function formatAmountPrefix(input) {
        // Remove non-numeric characters
        let amount = input.value.replace(/[^0-9]/g, '');

        // Add thousands separator (dots) using a regular expression
        amount = amount.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        // Format the numeric value with "Rp." prefix
        const formattedValue = `Rp. ${amount}`;

        // Set the formatted value back to the input
        input.value = formattedValue;
    }

    function displayFileName() {
        const fileInput = document.getElementById("receiptInput");
        const fileName = fileInput.files[0].name;
        const label = document.getElementById("receipt-label");
        label.innerText = fileName;
    }

