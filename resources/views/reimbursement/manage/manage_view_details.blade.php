@extends('layouts.main')

@section('title', 'Reimbursement Details - ESS')

@section('active-page-reimburse')
active
@endsection

@section('content')
<div class="row align-items-center zoom90">
    <div class="col">
        <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-hand-holding-usd"></i> Reimbursement #{{ $f_id }}</h1>
        <p class="mb-4 text-danger"><i>{{ $stat }}</i></p>
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
                                    @foreach($reimbursement as $row)
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Form ID.</td>
                                        <td>: {{ $row->id }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 180px;">Requested By</td>
                                        <td>: {{ $row->user->name }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 180px;">Reimbursement Type</td>
                                        <td>: {{ $row->f_type }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Payment Method</td>
                                        <td>: {{ $row->f_payment_method }}</td>
                                    </tr>
                                    @endforeach
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
                                    @foreach($reimbursement as $row)
                                    <tr class="table-sm">
                                        <td style="width: 180px;">Requesting Approval to</td>
                                        <td>:
                                            <?php
                                                $uniqueApprovers = array_unique($row->approval->pluck('RequestTo')->toArray());
                                                $commaDelimitedApprovers = implode(', ', $uniqueApprovers);
                                                $commaDelimitedApprovers = ucwords($commaDelimitedApprovers);
                                                echo $commaDelimitedApprovers;
                                            ?>
                                        </td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Notes</td>
                                        <td>: {{ $row->notes }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 150px;">Date Requested</td>
                                        <td>: {{ $row->created_at }}</td>
                                    </tr>
                                    <tr class="table-sm">
                                        <td style="width: 180px;">Last Updated</td>
                                        <td>: {{ $row->updated_at }}</td>
                                    </tr>
                                    @endforeach
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
                                <th>Description</th>
                                <th>Expense</th>
                                <th>Payout</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $row_number = 1;
                            @endphp
                            @foreach ($reimbursement_items as $usr)
                                <tr>
                                    <td class="text-center" style="width: 20%;"><a href="#" class="btn btn-outline-secondary btn-sm btn-sm preview-pdf" data-id="{{ $usr->id }}" style="margin-right: 3%;">Preview</a></td>
                                    <td>{{ $usr->description }}</td>
                                    <td>Rp. {{ $usr->amount }}</td>
                                    <td>Rp. {{ $usr->approved_amount ?? '—' }}</td>
                                    <td>
                                        @php
                                            $approved = false;
                                        @endphp

                                        @foreach ($usr->approval as $status)
                                            @if ($status->status == 29)
                                                <a><i class="fas fa-check-circle" style="color: #005eff;"></i> <small>Approved</small></a>
                                                @php
                                                    $approved = true;
                                                    break;
                                                @endphp
                                            @elseif ($status->status == 404)
                                                <a><i class="fas fa-times-circle" style="color: #ff0000;"></i> <small>Rejected</small></a>
                                                @php
                                                    $approved = true;
                                                    break;
                                                @endphp
                                            @elseif ($status->status == 30)
                                                <a><i class="fas fa-check-circle" style="color: #005eff;"></i> <small>Approved</small></a>
                                                @php
                                                    $approved = true;
                                                    break;
                                                @endphp
                                            @endif
                                        @endforeach

                                        @unless ($approved)
                                            <a><i class="fas fa-spinner fa-spin"></i> <small>Waiting for Approval</small></a>
                                        @endunless
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
                <!-- iframe to display the PDF -->
                <iframe id="pdfIframe" src="" style="width: 100%; height: 400px;"></iframe>
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
<style>
.action{
    width: 180px;
}
</style>
<script>
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

            // Load the content in the modal iframe
            $('#pdfIframe').attr('src', fileUrl);

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
                $('#description').val(response.description);
                $('#amount').val(response.amount);
                label.innerText = response.receipt_file;
            },
            error: function(xhr) {
                // Handle error
                console.log(xhr.responseText);
            }
        });
    });

    $(document).on('click', '#updateReimburseDataSubmit', function() {
        var formData = new FormData($('#editItemForm')[0]); // Use FormData to include the file
        var itemId = $('#item_id').val();

        // Make an AJAX request to update the project data
        $.ajax({
            url: '/reimbursement/edit/save/' + itemId,
            method: 'POST', // Use POST instead of PUT
            data: formData,
            contentType: false, // Important for handling file uploads
            processData: false, // Important for handling file uploads
            success: function(response) {
                // Handle success
                console.log(response);

                // Close the modal
                $('#editAmountModal').modal('hide');
                $('#editItemForm')[0].reset();
                window.location.reload();
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
                        row.append($('<td></td>').html(activity.status));
                        row.append($('<td></td>').text(activity.notes));
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
