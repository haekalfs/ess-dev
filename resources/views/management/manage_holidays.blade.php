@extends('layouts.main')

@section('title', 'Holidays in KIP - ESS')

@section('active-page-system_management')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-snowman"></i> Management Holidays</h1>
    <a data-toggle="modal" data-target="#addMem" class="d-none d-sm-inline-block btn btn-sm @role('freelancer') btn-success @else btn-primary @endrole shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Add New Holiday</a>
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
<div class="card shadow mb-4 zoom90">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Holiday List</h6>
        {{-- <div class="text-right">
            <button class="btn @role('freelancer') btn-success @else btn-primary @endrole btn-sm" type="button" id="manButton" style="margin-right: 10px;">+ Request Assignment</button>
        </div> --}}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="holidaysTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Document</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Intended For</th>
                        <th>isHoliday</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th width='120px'>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($holidaysList as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ $record->document->doc_letter_code }}</td>
                        <td>{{ $record->ts_date }}</td>
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
                        @if($record->status == 1)
                            <td>Approved</td>
                        @elseif($record->status == 404)
                            <td>Rejected</td>
                        @else
                            <td>NO</td>
                        @endif
                        <td>{{ $record->user_id }}</td>
                        <td class="text-center">
                            <a class="btn btn-danger btn-sm" onclick='isconfirm();' href="{{ route('holiday.delete', ['id' => $record->id]) }}"><i class='fas fa-fw fa-undo-alt'></i> Reset</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addMem" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Add New Holiday</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="{{ route('holiday.save') }}" method="post">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Date :</label>
                                    <input type="text" class="form-control" name="daterange" id="daterange"/>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="password">Surat Edar :</label>
                                            <select class="form-control" name="surat_edar">
                                                @foreach($documentLetter as $doc)
                                                    <option value="{{$doc->id}}">{{ $doc->doc_letter_code}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label for="selectOption">is Holiday :</label>
                                                <select class="form-control" name="isHoliday">
                                                    <option value="0">Not Holiday</option>
                                                    <option value="1" selected>Holiday</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <label>Intended For <span class="text-danger">*</span>:</label><br />
                                <label class="col-md-6">
                                    <input class="form-radio-input" type="radio" name="type_reimburse" id="projectRadio" value="1" checked>
                                    <span class="form-radio-sign">Project</span>
                                </label>
                                <label class="col-md-5">
                                    <input class="form-radio-input" type="radio" name="type_reimburse" id="othersRadio">
                                    <span class="form-radio-sign">Non-Project</span>
                                </label>
                                <input name="type" type="hidden" value="1" />
                                <div class="form-group">
                                    <select class="form-control" name="roles" required>
                                        <option value="Others" disabled selected>Select a type</option>
                                        <optgroup label="Member of Project :" id="project">
                                            @if ($getProject->isEmpty())
                                                <option disabled><i>No Project Assigned</i></option>
                                            @else
                                                @foreach($getProject as $assignment)
                                                    <option value="{{$assignment->id}}">{{ $assignment->project_name}}</option>
                                                @endforeach
                                            @endif
                                        </optgroup>
                                        <optgroup label="Member of Role :" style="display: none;" id="roleList">
                                            @foreach($roles as $role)
                                                <option value="{{$role->id}}">{{ $role->description}}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="password">Description :</label>
                                    <textarea name="description" type="text" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <span class="text-danger">Holiday Policy</span>
                                    </div>
                                    <div class="card-body rules" style="background-color: rgb(247, 247, 247);">
                                        <h6 class="h6 mb-2 font-weight-bold text-gray-800">General Guidelines for Adding Holiday Dates</h6>
                                        <ul>
                                            <li><strong>Accuracy:</strong> All holiday dates must be accurately documented and entered into the system.</li>
                                            <li><strong>Approval:</strong> Holiday dates should be approved by the relevant authority within the company.</li>
                                            <li><strong>Company Policies:</strong> Ensure that all added holiday dates comply with company policies and regulations.</li>
                                            <li><strong>Communication:</strong> Notify employees about the added holiday dates and any related changes to the calendar.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                  </div>
			</form>
		</div>
	</div>
</div>
@endsection

@section('javascript')
<script>
function initializeDateRangePicker() {
    var currentDate = moment(); // Get the current date
    var startDate = currentDate.clone().startOf('month');
    var endDate = currentDate.clone().endOf('month');

    $('input[name="daterange"]').daterangepicker({
        "startDate": startDate,
        "endDate": endDate,
        "opens": "right",
        "isInvalidDate": function(date) {
            // Disable Saturdays and Sundays
            return (date.day() === 0 || date.day() === 6);
        }
    }, function(start, end, label) {
        console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });
}

$(function() {
    initializeDateRangePicker();
});

// Get references to the radio buttons and the "project" select element
const projectSelect = document.getElementById('project');
const projectRadio = document.getElementById('projectRadio');
const othersRadio = document.getElementById('othersRadio');
var roleListSelect = document.getElementById('roleList');

$(document).ready(function() {
    projectRadio.addEventListener('change', function () {
        if (this.checked) {
            // Enable the "project" select element and set it as required
            projectSelect.style.display = 'block';
            projectSelect.required = true;
            roleListSelect.required = false;
            roleListSelect.style.display = 'none';
        }
    });

    othersRadio.addEventListener('change', function () {
        if (this.checked) {
            // Disable the "project" select element and set roleList as required
            projectSelect.style.display = 'none';
            projectSelect.required = false;
            roleListSelect.style.display = 'block';
            roleListSelect.required = true;
        }
    });
});
// Add event listener for project radio button
document.getElementById("projectRadio").addEventListener("click", function() {
    // Set the value of the hidden input field to 1
    document.querySelector("input[name='type']").value = "1";
});

// Add event listener for non-project radio button
document.getElementById("othersRadio").addEventListener("click", function() {
    // Set the value of the hidden input field to empty
    document.querySelector("input[name='type']").value = "0";
});
</script>
@endsection
