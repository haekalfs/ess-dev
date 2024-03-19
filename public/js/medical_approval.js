      $(document).ready(function() {
        $('.modal').on('shown.bs.modal', function () {
            $(this).find('.modal-title').focus();
        });
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

function changeFileName(inputId, labelId) {
  var input = document.getElementById(inputId);
  var label = document.getElementById(labelId);
  label.textContent = input.files[0].name;
}


//Amount
// Mengubah titik menjadi angka biasa dan menghitung total amount
var amountElements = document.getElementsByClassName("amount");
var totalAmount = 0;

for (var i = 0; i < amountElements.length; i++) {
  var amountText = amountElements[i].textContent;
  var amountNumber = parseFloat(amountText.replace(/\./g, "").replace(",", "."));
  totalAmount += amountNumber;
}

// Menampilkan total amount
var totalAmountDisplay = document.getElementById("totalAmount");
totalAmountDisplay.textContent = "Rp. " + totalAmount.toLocaleString('id-ID');

// Amount Approved
// Mengubah titik menjadi angka biasa dan menghitung total amount amountApproved
document.addEventListener("DOMContentLoaded", function() {
    var amountApprovedElements = document.getElementsByClassName("amountApproved");
    var totalAmountApproved = 0;

    for (var i = 0; i < amountApprovedElements.length; i++) {
        var amountText = amountApprovedElements[i].textContent;
        var amountApprovedNumber = parseFloat(amountText.replace(/\./g, "").replace(",", "."));
        totalAmountApproved += amountApprovedNumber;
    }

    // Display total amount approved in different locations
    var totalAmountApprovedDisplay1 = document.getElementById("totalAmountApproved");
    totalAmountApprovedDisplay1.textContent = "Rp. " + totalAmountApproved.toLocaleString('id-ID');

    var totalAmountApprovedDisplay2 = document.getElementById("totalAmountApproved2");
    totalAmountApprovedDisplay2.textContent = "Rp. " + totalAmountApproved.toLocaleString('id-ID');

    // Update total amount approved in input field
    var totalAmountApprovedInput = document.getElementById("totalAmountApprovedInput");
    if (totalAmountApprovedInput) {
        totalAmountApprovedInput.value = totalAmountApproved.toLocaleString().replace(/,/g, ".");
    }
});
// Simpan nilai totalApprovedAmount dalam input tersembunyi saat menghitungnya
document.getElementById('totalAmountApprovedInput').value = totalApprovedAmountInput;

function validateForm() {
    // Mendapatkan nilai input
    var totalAmountApproved = document.getElementById("totalAmountApprovedInput").value;
    
    // Memeriksa apakah nilai input adalah 0
    if (parseInt(totalAmountApproved) === 0) {
        // Jika nilai input adalah 0, tampilkan pesan kesalahan
        alert("Please fill the approved amount first");
    } else {
        // Jika nilai input bukan 0, submit formulir
        document.getElementById("approve").submit();
    }
}



$(document).ready(function() {
    // Menambahkan event listener untuk menangani penutupan modal
    $('#approveModal').on('hidden.bs.modal', function (e) {
        // Membersihkan modal saat ditutup
        $(this).find('form')[0].reset();
        // Menghapus backdrop secara manual
        $('.modal-backdrop').remove();
    });
});
 


function confirmDelete(medId, mdetId) {
        // Tampilkan konfirmasi
        if (confirm("Are You Sure Want To Reject This ?")) {
            // Redirect ke URL Laravel
            window.location.href = "/medical/approval/" + medId + "/reject/" + mdetId;
        }
    }