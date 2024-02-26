@extends('layouts.main')

@section('title', 'Reimbursement Details - ESS')

@section('active-page-reimburse')
active
@endsection

@section('content')

<div class="row align-items-center zoom90">
    <div class="col">
        <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-hand-holding-usd"></i> Reimbursement #{{ $f_id }}</h1>
        @if($reimbursement->status_id == 20)
        <p class="mb-4 text-primary"><i>Waiting for Approval</i></p>
        @elseif($reimbursement->status_id == 404)
        <p class="mb-4 text-danger"><i>Rejected</i></p>
        @else
        <p class="mb-4 text-primary"><i>Approved</i></p>
        @endif
    </div>
    <div class="col-auto">
        @if($reimbursement_items_count > 0)
            <a href="#" class="btn btn-primary btn-sm" onclick="showPreventModal();">
                <i class="fas fa-backward"></i>&nbsp; Go Back
            </a>
        @else
            <a href="/approval/reimburse/" class="btn btn-primary btn-sm">
                <i class="fas fa-forward"></i>&nbsp; Go Back
            </a>
        @endif
    </div>
</div>
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
    <div class="row zoom90">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
                    role="button" aria-expanded="true" aria-controls="collapseCardExample">
                    <h6 class="m-0 font-weight-bold text-primary">Reimbursement Information</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseCardExample">
                    <div class="card-body">
                        <div class="col-md-12">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Form ID.</td>
                                        <td>: {{ $reimbursement->id }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 180px;">Requested By</td>
                                        <td>: {{ $reimbursement->user->name }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 180px;">Reimbursement Type</td>
                                        <td class="font-weight-bold text-primary">: {{ $reimbursement->f_type }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Payment Method</td>
                                        <td>: {{ $reimbursement->f_payment_method }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;" class="text-success font-weight-bold">Total Granted Funds :</td>
                                        <td class="text-success font-weight-bold">: IDR {{ number_format($reimbursement->f_granted_funds, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseCardProject" class="d-block card-header py-3" data-toggle="collapse"
                    role="button" aria-expanded="true" aria-controls="collapseCardProject">
                    <h6 class="m-0 font-weight-bold text-primary">Additional Information</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseCardProject">
                    <div class="card-body">
                        <div class="col-md-12">
                            <table class="table table-borderless">
                                <tbody>
                                    <?php
                                        $commaDelimitedApprovers = implode(', ', $approversArrayName);
                                    ?>
                                    <tr class="table-sm">
                                        <td style="width: 180px;">Requesting Approval to</td>
                                        <td class="long-text" title="{{ $commaDelimitedApprovers }}">:
                                            <?php
                                            echo $commaDelimitedApprovers;
                                            ?>
                                        </td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Notes</td>
                                        <td>: {{ $reimbursement->notes }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Date Requested</td>
                                        <td>: {{ $reimbursement->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 180px;">Last Updated</td>
                                        <td>: {{ $reimbursement->updated_at->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;" class="text-success font-weight-bold">Paid On</td>
                                        <td class="text-success font-weight-bold">: {{ $reimbursement->f_paid_on }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Items</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="col-md-12">
                    <table class="table table-bordered zoom90" width="100%" id="dataTable" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Item Description</th>
                                <th class="text-danger font-weight-bold">Emp. Request</th>
                                <th class="text-success font-weight-bold">Granted Funds <small class="text-secondary">(by you)</small></th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $row_number = 1;
                            @endphp
                            @foreach ($reimbursement_items as $usr)
                                <tr>
                                    <td>{{ $row_number++ }}</td>
                                    <td>{{ $usr->item->description }}</td>
                                    <td class="text-danger font-weight-bold">Rp. {{ $usr->item->amount }}</td>
                                    <td class="text-success font-weight-bold">Rp. {{ $usr->approved_amount ?? '—' }}</td>
                                    <td>
                                        @if($usr->status == 20)
                                            <a><i class="fas fa-spinner fa-spin"></i></a>
                                        @elseif ($usr->status == 404)
                                            <i class="far fa-times-circle"></i>
                                        @else
                                            <a><i class="far fa-check-circle"></i></a>
                                        @endif
                                    </td>
                                    <td class="action text-center">
                                        @if($usr->status == 20)
                                        <a data-toggle="modal" data-target="#editAmountModal" data-item-id="{{ $usr->item->id }}" class="btn btn-primary btn-sm btn-edit"><i class="fas fa-fw fa-edit"></i> Action</a>
                                        @else
                                            <small style="color: grey;"><i>Action already applied.</i></small>
                                            {{-- <a data-toggle="modal" data-target="#editAmountModal" data-item-id="{{ $usr->item->id }}" class="btn btn-primary btn-sm btn-edit"><i class="fas fa-fw fa-edit"></i> Action</a> --}}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editAmountModal" tabindex="-1" role="dialog" aria-labelledby="modalPeriod" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document" style="max-width: 850px;">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="editAmountModalLabel">Preview Item</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="approvalItemForm">
                @csrf
                <input type="hidden" name="item_id" id="item_id" value="">
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12 zoom90">
                                <div class="zoom90 d-sm-flex align-items-center justify-content-between mb-4">
                                    <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-money-bill-wave"></i> Settlement for Disbursement</h1>
                                    <div>
                                        <a id="downloadBtn" class="btn btn-primary mr-2"><i class="fas fa-download"></i> Download</a>
                                        <button type="button" id="viewReceipt" class="btn btn-secondary" onclick="toggleReceipt()"><i class="fas fa-eye"></i> View Receipt</button>
                                        <button type="button" id="closeReceipt" style="display: none;" class="btn btn-secondary2" onclick="toggleReceipt()">Close</button>
                                    </div>
                                </div>
                                <div class="col-md-12" style="display: none;" id="receiptContainer">
                                    <iframe id="pdfIframe" src="" style="width: 100%; height: 500px;"></iframe>
                                    <img id="imageframe" style="display: none;" src="" width="100%" height="500px"/>
                                </div>
                                <div class="row" id="detailContainer">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="to">Employee's Request :</label>
                                                    <input type="text" class="form-control" name="current_amount" readonly oninput="formatAmount(this)" id="current_amount">
                                                    <small style="color: red;"><i>Amount of Employee's Request.</i></small>
                                                </div>
                                            </div>
                                            <div class="col-md-2 d-flex justify-content-center align-items-center">
                                                <span class="h2"><i class="fas fa-random"></i></span>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="to">Amount to be Processed :</label>
                                                    <input type="text" class="form-control" name="approved_amount" title="Leave it blank/null if you won't edit the value!" oninput="formatAmountPrefix(this)" id="approved_amount">
                                                    <small style="color: red;"><i>To maintain amount with the previous approver's, leave this blank.</i></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="approval_notes">Notes to Employee :</label>
                                            <textarea type="text" class="form-control" rows="5" name="approval_notes" id="approval_notes"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <h6 class="h6 mb-2 font-weight-bold text-gray-800">Approvals Process</h6>
                                        <ul id="ApproverList">
                                            {{-- ajax --}}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <a href="#" class="btn btn-sm btn-danger mr-2" id="rejectButton" onclick='askConfirm();'>
                        <i class="fas fa-fw fa-ban fa-sm text-white-50"></i> Reject
                    </a>
                    <div id="loadingIndicatorView" style="display: none;" class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <a href="#" class="btn btn-sm btn-primary" id="approveButton" onclick='askConfirm();'>
                        <i class="fas fa-fw fa-check fa-sm text-white-50"></i> Approve
                    </a>
                </div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <span class="m-0 font-weight-bold text-success">Action executed successfully.</span>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="preventModal" tabindex="-1" aria-labelledby="preventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <span class="m-0 font-weight-bold text-danger">You need to review the request first! before leave the page. Take action for all items (Approve/Reject) please...</span>
            </div>
        </div>
    </div>
</div>
<script>
    function showPreventModal() {
        $('#preventModal').modal('show');
    }
</script>

<style>
.action{
    width: 180px;
}
</style>
<script>
    $(document).on('click', '.btn-edit', function() {
        var itemId = $(this).data('item-id');
        $('#item_id').val(itemId);

        fetchApproverList(itemId);
        // Set the URL for the PDF or image preview based on the user's ID
        var fileUrl = '{{ route("pdf.preview", ":id") }}';
        fileUrl = fileUrl.replace(':id', itemId);

        // Function to check if the file extension represents an image
        function isImage(filename) {
            var extension = filename.split('.').pop().toLowerCase();
            return ['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(extension);
        }

        $.ajax({
            url: '/retrieveReimburseDataApproval/' + itemId,
            method: 'GET',
            success: function(response) {
                $('#current_amount').val("Rp. " + response.itemData.amount);
                $('#approved_amount').attr('placeholder', "Rp. " + response.grantedFunds);
                // Assuming response.url contains the desired URL
                var downloadUrl = "/download-receipt/reimbursement/" + response.itemData.id;
                if (isImage(response.itemData.receipt_file)) {
                    // If it's an image, show image frame and hide PDF frame
                    $('#imageframe').attr('src', fileUrl);
                    $('#imageframe').show();
                    $('#pdfIframe').hide();
                } else {
                    // If it's not an image (assume it's a PDF), show PDF frame and hide image frame
                    $('#pdfIframe').attr('src', fileUrl);
                    $('#pdfIframe').show();
                    $('#imageframe').hide();
                }
                // Set the href attribute of the download button
                $('#downloadBtn').attr('href', downloadUrl);
            },
            error: function(xhr) {
                // Handle error
                console.log(xhr.responseText);
            }
        });

        // When the modal is hidden, clear the iframe src
        $('#pdfModal').on('hidden.bs.modal', function () {
            $('#pdfIframe').attr('src', '');
            $('#imageframe').attr('src', '');
        });

        function askConfirm(action) {
            var confirmation = confirm("Are you sure you want to " + action + "?");

            if (confirmation) {
                // User clicked OK, proceed with form submission
                submitForm(action);
            } else {
                // User clicked Cancel, do nothing or handle as needed
            }
        }

        function submitForm(action) {
            var test = document.getElementById('item_id').value;

            // Assuming you want to submit the form with ID "approvalItemForm"
            var formData = $('#approvalItemForm').serialize(); // Serialize the form data

            var url;

            if (action === 1) {
                url = '/approval/reimburse/view/approve/' + test;
            } else if (action === 2) {
                url = '/approval/reimburse/view/reject/' + test;
            }

            // Perform an AJAX POST request to the controller
            $.ajax({
                url: url, // Replace with your controller URL
                method: 'POST', // Change to POST if necessary
                data: formData,
                success: function(response) {
                    $('#successModal').modal('show');
                    window.location.reload();
                },
                error: function(error) {
                    // Handle error response as needed
                    console.error(error);
                }
            });
        }

        // Event handler for the Approve button
        $('#approveButton').on('click', function(e) {
            e.preventDefault(); // Prevent the default navigation behavior
            $('#approveButton').hide();
            $('#loadingIndicatorView').show();
            submitForm(1);
        });

        // Event handler for the Reject button
        $('#rejectButton').on('click', function(e) {
            e.preventDefault(); // Prevent the default navigation behavior
            $('#rejectButton').hide();
            $('#loadingIndicatorView').show();
            submitForm(2);
        });
    });

    function fetchApproverList(id) {
        $.ajax({
            url: '/retrieveApproverHistory/' + id,
            type: 'GET',
            success: function(response) {
                // Clear the table body
                $('#ApproverList').empty();
                // Check if the response is empty or null
                if (response.length === 0) {
                    // Display a message to the user
                    $('#ApproverList').append($('<li>No approvals have been processed yet.</li>'));
                } else {
                    // Loop through the activities and append each row to the table
                    $.each(response, function(index, activity) {
                        var listItem = $('<li></li>').attr('data-id', activity.id);
                        listItem.append($('<span></span>').html('<span class="text-primary font-weight-bold">' + activity.RequestTo + '</span> has approved this item for IDR ' + activity.approved_amount + '<br> Notes : ' + activity.notes));
                        $('#ApproverList').append(listItem);
                    });
                }
            },
            error: function(response) {
                console.log(response);
            }
        });
    }
</script>

<script>
let click = 0;

function toggleReceipt() {
    var viewBtn = document.getElementById("viewReceipt");
    var receiptContainer = document.getElementById("receiptContainer");
    var detailContainer = document.getElementById("detailContainer");

    click++;

    if (click % 2 === 1) {
        // Showing receipt and close button
        viewBtn.textContent = "Close Receipt";
        receiptContainer.style.display = "block";
        detailContainer.style.display = "none";
    } else {
        viewBtn.textContent = "View Receipt";
        receiptContainer.style.display = "none";
        detailContainer.style.display = "block";
    }
}
</script>
@endsection

@section('javascript')
<!-- Add PDF.js from a CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.7.570/pdf.min.js"></script>
<script src="{{ asset('js/reimburse.js') }}"></script>
@endsection
