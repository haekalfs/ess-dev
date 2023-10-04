// Get references to the radio buttons and the "project" select element
const projectSelect = document.getElementById('project');
const projectRadio = document.getElementById('projectRadio');
const othersRadio = document.getElementById('othersRadio');

// Add event listeners to both radio buttons
projectRadio.addEventListener('change', function () {
    if (this.checked) {
        // Enable the "project" select element
        projectSelect.disabled = false;
    }
});

othersRadio.addEventListener('change', function () {
    if (this.checked) {
        // Disable the "project" select element
        projectSelect.disabled = true;
    }
});

    const originalForm = document.querySelector("#originalForm");
    const copyButton = document.querySelector("#copyButton");
    const undoButton = document.querySelector("#undoButton");
    const targetContainer = document.querySelector("#targetContainer");
    const form = document.querySelector("#btn-submit");

    let copyCounter = 0;

    copyButton.addEventListener("click", function (event) {
        event.preventDefault();

        if (copyCounter < 6) {
            const clonedForm = originalForm.cloneNode(true);

            // Modify the input's name attributes
            const itemInput = clonedForm.querySelector("#receipt");
            itemInput.name = "receipt[]";

            const descriptionInput = clonedForm.querySelector("#description");
            descriptionInput.name = "description[]";

            const priceInput = clonedForm.querySelector("#amount");
            priceInput.name = "amount[]";

            // Clear input values
            itemInput.value = "";
            descriptionInput.value = "";
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

    function formatAmount(input) {
        // Remove non-numeric characters
        let amount = input.value.replace(/[^0-9]/g, '');
    
        // Add thousands separator (dots) using a regular expression
        amount = amount.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    
        // Set the formatted value back to the input
        input.value = amount;
    }