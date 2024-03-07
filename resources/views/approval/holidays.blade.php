@extends('layouts.main')

@section('title', 'Holidays Approval - ESS')

@section('active-page-approval')
active
@endsection

@section('content')
<h1 class="h4 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-snowman"></i> Holidays Approval</h1>
<p class="zoom90 mb-4">Approval Page.</p>
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

<div class="card shadow mb-4 zoom90">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Holidays in Queue</h6>
        <div class="text-right">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="holidaysTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Document</th>
                        <th>Description</th>
                        <th>Intended For</th>
                        <th>isHoliday</th>
                        <th>Created By</th>
                        <th width='120px'>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($holidaysList as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ $record->document->doc_letter_code }}</td>
                        <td>{{ $record->description }}</td>
                        @if($record->isProject == TRUE)
                            <td>{{ $record->company_project->project_name }}</td>
                        @else
                            <td>{{ $record->role->description }}</td>
                        @endif
                        @if($record->isHoliday == TRUE)
                            <td>YES</td>
                        @else
                            <td>NO</td>
                        @endif
                        <td>{{ $record->user_id }}</td>
                        <td class="action text-center">
                            <a data-toggle="modal" data-target="#editAmountModal" data-item-id="{{ $record->surat_edar }}" class="btn btn-primary btn-sm btn-edit"><i class="fas fa-eye"></i> Preview</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="editAmountModal" tabindex="-1" role="dialog" aria-labelledby="modalPeriod" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document" style="max-width: 1250px;">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="editAmountModalLabel">Holidays Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="approvalItemForm">
                @csrf
                <input type="hidden" name="item_id" id="item_id" value="">
				<div class="modal-body">
                    <table class="table table-bordered zoom90" width="100%" id="dataTable" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Document</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Intended For</th>
                                <th>isHoliday</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody id="ApproverList">
                            <!-- Ajax Data -->
                        </tbody>
                    </table>
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
<style>
.action{
    width: 250px;
}
</style>
<script>
$(document).on('click', '.btn-edit', function() {
    var itemId = $(this).data('item-id');
    $('#item_id').val(itemId);

    $.ajax({
        url: '/retrieveHolidaysDetail/' + itemId,
        method: 'GET',
        success: function(response) {
            // Clear the table body
            $('#ApproverList').empty();

            // Check if the response is empty or null
            if (response.length === 0) {
                // Display a message to the user
                $('#ApproverList').append('<tr><td colspan="7">No approvals have been processed yet.</td></tr>');
            } else {
                // Loop through the activities and append each row to the table
                $.each(response, function(index, activity) {
                    var row = $('<tr></tr>');
                    row.append($('<td></td>').text(activity.id));
                    row.append($('<td></td>').text(activity.surat_edar));
                    row.append($('<td></td>').text(activity.ts_date));
                    row.append($('<td></td>').text(activity.description));
                    row.append($('<td></td>').text(activity.intended_for));
                    row.append($('<td></td>').text(activity.isHoliday));
                    row.append($('<td></td>').text(activity.user_id));
                    $('#ApproverList').append(row);
                });
            }
        },
        error: function(xhr) {
            // Handle error
            console.log(xhr.responseText);
        }
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
            url = '/approval/holidays/approve/' + test;
        } else if (action === 2) {
            url = '/approval/holidays/reject/' + test;
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
</script>
@endsection
