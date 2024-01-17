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
        <a href="/approval/reimburse/" class="btn btn-primary btn-sm">
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
                                    <td>Rp. {{ $usr->item->amount }}</td>
                                    <td>Rp. {{ $usr->item->approved_amount ?? '—' }}</td>
                                    <td>
                                        @if($usr->status == 20)
                                            <a><i class="fas fa-spinner fa-spin"></i></a>
                                        @elseif ($usr->status == 404)
                                            <i class="far fa-times-circle"></i>
                                        @else
                                            <a><i class="far fa-check-circle fa-spin"></i></a>
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
	<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
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
                            <div class="col-md-6">
                                <iframe id="pdfIframe" src="" style="width: 100%; height: 400px;"></iframe>
                            </div>
                            <div class="col-md-6 zoom90">
                                <div class="d-sm-flex align-items-center justify-content-between mb-4 zoom90">
                                    <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-money-bill-wave"></i> Settlement for Disbursement</h1>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="to">Employee's Expenses :</label>
                                            <input type="text" class="form-control" name="current_amount" readonly oninput="formatAmount(this)" id="current_amount">
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex justify-content-center align-items-center">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="to">Payout :</label>
                                            <input type="text" class="form-control" name="approved_amount" oninput="formatAmountPrefix(this)" id="approved_amount">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="approval_notes">Notes :</label>
                                            <textarea type="text" class="form-control" name="approval_notes" id="approval_notes"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <small style="color: red;"><i>NOTE : Only financial managers or those at the same level are allowed to define the payout amount.</i></small>
                                    </div>
                                    <div class="col-md-12 mt-4">
                                        <table class="table table-bordered zoom90" width="100%" id="dataTable" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Request To</th>
                                                    <th>Status</th>
                                                    <th>Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ApproverList">
                                                <!-- Ajax Data -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <a href="#" class="btn btn-primary btn-sm" id="approveButton" style="margin-right: 5px;">
                        <i class="fas fa-fw fa-check fa-sm text-white-50"></i> Approve
                    </a>
                    <a href="#" class="btn btn-danger btn-sm" id="rejectButton" style="margin-right: 5px;">
                        <i class="fas fa-fw fa-ban fa-sm text-white-50"></i> Reject
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

        // Load the content in the modal iframe
        $('#pdfIframe').attr('src', fileUrl);

        $.ajax({
            url: '/retrieveReimburseData/' + itemId,
            method: 'GET',
            success: function(response) {
                $('#approved_amount').val(response.amount);
                $('#current_amount').val("Rp. " + response.amount);
            },
            error: function(xhr) {
                // Handle error
                console.log(xhr.responseText);
            }
        });

        // When the modal is hidden, clear the iframe src
        $('#pdfModal').on('hidden.bs.modal', function () {
            $('#pdfIframe').attr('src', '');
        });

        // Click event handler for the Approve button
        $('#approveButton').on('click', function(e) {
            e.preventDefault(); // Prevent the default navigation behavior

            var test = document.getElementById('item_id').value;

            // Assuming you want to submit the form with ID "editItemForm"
            var formData = $('#approvalItemForm').serialize(); // Serialize the form data

            // Perform an AJAX POST request to the controller
            $.ajax({
                url: '/approval/reimburse/view/approve/' + test, // Replace with your controller URL
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
        });

        // Click event handler for the Reject button
        $('#rejectButton').on('click', function(e) {
            e.preventDefault(); // Prevent the default navigation behavior

            var test = document.getElementById('item_id').value;

            // Assuming you want to submit the form with ID "editItemForm"
            var formData = $('#approvalItemForm').serialize(); // Serialize the form data

            // Perform an AJAX POST request to the controller
            $.ajax({
                url: '/approval/reimburse/view/reject/' + test, // Replace with your controller URL
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
        });
    });

    function fetchApproverList(id) {
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
