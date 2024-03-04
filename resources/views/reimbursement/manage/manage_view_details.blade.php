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
        <a href="/reimbursement/manage" class="btn btn-primary btn-sm">
            <i class="fas fa-backward"></i>&nbsp; Go Back
        </a>
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
                                        <td>: {{ $reimbursement->created_at->format('d-M-Y') }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 180px;">Last Updated</td>
                                        <td>: {{ $reimbursement->updated_at->format('d-M-Y') }}</td>
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
                <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Reimbursement Items</h6>
                <div class="text-right">
                    <div id="proccessContainer" style="display: none;">
                        <a href="/reimbursement/create_order_letter/{{ $reimbursement->id }}" class="btn btn-danger btn-sm mr-2">
                            <i class="fas fa-paper-plane"></i> Send Disbursement Order Letter
                        </a>
                        <a href="/reimbursement/manage/disbursed-item/{{ $reimbursement->id }}" class="btn btn-success btn-sm mr-2">
                            <i class="fas fa-check"></i> Mark as Paid
                        </a>
                        <a href="/reimbursement/export/request/{{ $reimbursement->id }}" class="btn btn-secondary btn-sm"><i class="far fa-file-excel"></i> Export as Excel</a>
                    </div>
                    @if($isReceived == TRUE)
                        <div>
                            <a href="/reimbursement/create_order_letter/{{ $reimbursement->id }}" class="btn btn-danger btn-sm mr-2">
                                <i class="fas fa-paper-plane"></i> Send Disbursement Order Letter
                            </a>
                            <a href="/reimbursement/manage/disbursed-item/{{ $reimbursement->id }}" class="btn btn-success btn-sm mr-2">
                                <i class="fas fa-check"></i> Mark as Paid
                            </a>
                            <a href="/reimbursement/export/request/{{ $reimbursement->id }}" class="btn btn-secondary btn-sm"><i class="far fa-file-excel"></i> Export as Excel</a>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="col-md-12">
                    <table class="table table-bordered zoom90" width="100%" id="dataTable" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>Receipt</th>
                                <th>Description</th>
                                <th class="text-danger font-weight-bold">Emp. Request</th>
                                <th class="text-success font-weight-bold">Granted Funds</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $row_number = 1;
                            @endphp
                            @foreach ($reimbursement_items as $usr)
                                <tr>
                                    <td class="text-center"><a href="#" class="btn btn-outline-secondary btn-sm btn-sm preview-pdf" data-id="{{ $usr->id }}" style="margin-right: 3%;">Preview</a></td>
                                    <td>{{ $usr->description }}</td>
                                    <td class="text-danger font-weight-bold">Rp. {{ $usr->amount }}</td>
                                    <td class="text-success font-weight-bold">Rp. {{ $usr->approved_amount ?? '—' }}</td>
                                    <td class="text-center" style="width: 20%;">
                                        <div id="btnContainer{{ $usr->id }}" style="display: none;">
                                            <a data-toggle="modal" data-target="#editAmountModal" data-item-id="{{ $usr->id }}" class="btn btn-primary btn-sm mr-2 btn-edit"><i class="fas fa-fw fa-edit"></i> Update</a>
                                            <a data-toggle="modal" data-target="#detailsModal" data-item-id="{{ $usr->id }}" class="btn btn-secondary btn-sm btn-details"><i class="fas fa-info-circle"></i> Status</a>
                                        </div>
                                        @if($usr->receivable_receipt == false)
                                            <div id="errorMsgView{{ $usr->id }}" class="alert alert-danger" style="display: none;" role="alert">
                                                <small>An Error Occured! Refresh the Page!</small>
                                            </div>
                                            <div id="loadingIndicatorView{{ $usr->id }}" style="display: none;" class="spinner-border" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <button id="confirmBtn{{ $usr->id }}" data-item-id="{{ $usr->id }}" class="btn btn-secondary btn-sm mr-2 confirm-button"><i class="fas fa-fw fa-check"></i> Confirm Receipt</button>
                                        @else
                                            @if($usr->edited_by_finance == false && $usr->receivable_receipt == true)
                                            <a data-toggle="modal" data-target="#editAmountModal" data-item-id="{{ $usr->id }}" class="btn btn-primary btn-sm mr-2 btn-edit"><i class="fas fa-fw fa-edit"></i> Update</a>
                                            @endif
                                            <a data-toggle="modal" data-target="#detailsModal" data-item-id="{{ $usr->id }}" class="btn btn-secondary btn-sm btn-details"><i class="fas fa-info-circle"></i> Status</a>
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
<!-- Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">Receipt Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-right">
                    <a id="downloadBtn" class="btn btn-sm btn-primary"><i class="fas fa-download"></i> Download</a>
                </div>
                <!-- iframe to display the PDF -->
                <iframe id="pdfIframe" src="" style="width: 100%; height: 400px;"></iframe>
                <img id="imageframe" style="display: none;" src="" width="100%" height="400px"/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade zoom90" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Approval Flow</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered zoom90" width="100%" id="dataTable" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Request To</th>
                            <th>Granted Funds</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody id="ApproverList">
                        <!-- Ajax Data -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
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

<div class="modal fade" id="editAmountModal" tabindex="-1" role="dialog" aria-labelledby="modalPeriod" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="editAmountModalLabel">Edit Item</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="editItemForm" action="">
                @csrf
                <input type="hidden" name="item_id" id="item_id" value="">
                <div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="form-group">
                            <label for="amount">Modify Granted Amount <span class="text-danger">*</span>:</label>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Rp.</div>
                                </div>
                                <input type="text" class="form-control" name="amount" oninput="formatAmount(this)" id="amount" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment">Notes <span class="text-danger">*</span>:</label>
                            <textarea class="form-control" id="comment" rows="2" name="notes" required></textarea>
                        </div>
                        <small style="color: red;"><i>Changes will send notification to request owner.</i></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" id="rejectBtn" onclick="setFormAction('/reimbursement/finance/reject/')"><i class="fas fa-times"></i> Reject</button>
                    <button type="button" class="btn btn-primary btn-sm" id="approveBtn" onclick="setFormAction('/reimbursement/finance/approve/')"><i class="fas fa-save"></i> Approve</button>
                </div>
            </form>
		</div>
	</div>
</div>
<style>
.action{
    width: 180px;
}
</style>
<script>

$(document).ready(function() {
    $('.confirm-button').click(function(e) {
        e.preventDefault();
        var id = $(this).data('item-id');
        $('#loadingIndicatorView'+ id).show();
        $('#confirmBtn'+ id).hide();
        $('#errorMsgView'+ id).hide();

        $.ajax({
            url: '/reimbursement/finance/confirm-receivable/' + id,
            type: 'GET',
            timeout: 5000,
            success: function(response) {
                $('#loadingIndicatorView'+ id).hide();
                $('#confirmBtn'+ id).hide();
                $('#btnContainer'+ id).show();
                $('#proccessContainer').show();
            },
            error: function() {
                $('#loadingIndicatorView'+ id).hide();
                $('#confirmBtn'+ id).hide();
                $('#errorMsgView'+ id).show();
            }
        });
    });
});

$(document).ready(function () {
    // When a button with class 'preview-pdf' is clicked
    $('.preview-pdf').click(function () {
        // Get the user's ID from the data-id attribute
        var userId = $(this).data('id');

        // Set the URL for the PDF or image preview based on the user's ID
        var fileUrl = '{{ route("pdf.preview", ":id") }}';
        fileUrl = fileUrl.replace(':id', userId);

        // Open the modal
        $('#pdfModal').modal('show');


        // Function to check if the file extension represents an image
        function isImage(filename) {
            var extension = filename.split('.').pop().toLowerCase();
            return ['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(extension);
        }

        $.ajax({
            url: '/retrieveReimburseData/' + userId,
            method: 'GET',
            success: function(response) {
                var downloadUrl = "/download-receipt/reimbursement/" + response.id;
                if (isImage(response.receipt_file)) {
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

        // Prevent the default link behavior
        return false;
    });

    // When the modal is hidden, clear the iframe src
    $('#pdfModal').on('hidden.bs.modal', function () {
        $('#pdfIframe').attr('src', '');
    });
});


const label = document.getElementById("receipt-label");

$(document).on('click', '.btn-edit', function() {
    var itemId = $(this).data('item-id');
    $('#item_id').val(itemId);

    $.ajax({
        url: '/retrieveReimburseData/' + itemId,
        method: 'GET',
        success: function(response) {
            // Populate the form fields with the received data
            $('#amount').val(response.approved_amount);
        },
        error: function(xhr) {
            // Handle error
            console.log(xhr.responseText);
        }
    });
});

$(document).on('click', '.btn-details', function() {
    var itemId = $(this).data('item-id');
    $('#item_id').val(itemId);

    fetchApproverDetails(itemId);
});

function setFormAction(url) {
    var itemId = $('#item_id').val();
    document.getElementById('editItemForm').action = url + itemId;
    document.getElementById('editItemForm').submit();
}

function fetchApproverDetails(id) {
    $.ajax({
        url: '/retrieveApproverList/' + id,
        type: 'GET',
        success: function(response) {
            // Clear the table body
            $('#ApproverList').empty();
            // Check if the response is empty or null
            if (response.length === 0) {
                // Display a message to the user
                $('#ApproverList').append($('<tr><td class="text-center" colspan="3">No data available in table.</td></tr>'));
            } else {
                // Loop through the activities and append each row to the table
                $.each(response, function(index, activity) {
                    var row = $('<tr></tr>').attr('data-id', activity.id);
                    row.append($('<td></td>').text(activity.RequestTo));
                    row.append($('<td></td>').html('IDR '+activity.approved_amount));
                    row.append($('<td></td>').html(activity.status));
                    row.append($('<td></td>').text(activity.notes));
                    row.append($('<td></td>').text(activity.updated_at));
                    $('#ApproverList').append(row);
                    $('#approval_notes').val(activity.notes);
                });
            }
        },
        error: function(response) {
            console.log(response);
        }
    });
}
</script>
@endsection

@section('javascript')
<!-- Add PDF.js from a CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.7.570/pdf.min.js"></script>
<script src="{{ asset('js/reimburse.js') }}"></script>
@endsection
