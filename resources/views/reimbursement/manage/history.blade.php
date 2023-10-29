@extends('layouts.main')

@section('title', 'Reimbursement Review - ESS')

@section('active-page-reimburse')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4 zoom90">
    <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-hand-holding-usd"></i> Manage Reimbursement <small style="color: red;"><i> &nbsp;&nbsp;Finance Department</i></small></h1>
    <a class="d-none d-sm-inline-block btn btn-secondary btn-sm shadow-sm" type="button" onclick="confirmPassword()"><i class="fas fa-fw fa-file-export fa-sm text-white-50"></i> Export All (XLS)</a>
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
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Filter Timesheets</h6>
        <div class="text-right">
            <form method="POST" action="/reimbursement/manage/disbursed/all">
                @csrf
                <button type="submit" id="bulkPaid" style="display: none;" class="btn btn-success btn-sm btn-edit"><i class="fas fa-check"></i> Disbursed</button>
                <input type="hidden" name="usersName" id="usersName" value="" />
                <input type="hidden" name="formId" id="formId" value="" />
            </form>
        </div>
    </div>
    <form method="GET" action="/reimbursement/manage">
        @csrf
        <div class="card-body">
            <div class="col-md-12 zoom90">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="email">Employee :</label>
                            <select class="form-control" name="showOpt" required>
                                <option value="1">All</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="password">Year :</label>
                            <select class="form-control" name="yearOpt" required>
                                @foreach (array_reverse($yearsBefore) as $year)
                                    <option value="{{ $year }}" @if ($year == $Year) selected @endif>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="password">Month :</label>
                            <select class="form-control" name="monthOpt" required>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}" @if ($month == $Month) selected @endif>
                                        {{ date("F", mktime(0, 0, 0, $month, 1)) }} @if ($notifyMonth == $month) &#x2757; @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex justify-content-center align-items-end">
                        <div class="form-group">
                            {{-- <input type="submit" class="btn btn-primary" value="Display"> --}}
                            {{-- <button type="submit" class="btn btn-primary notification-item position-relative">
                                 Display
                                <span class="position-absolute top-0 start-100 translate-middle badge bg-danger">!</span>
                            </button> --}}
                            @if ($notify)
                                <button type="submit" class="btn btn-primary position-relative">
                                    Display
                                    <span class="position-absolute top-0 start-100 translate-middle badge bg-danger">!</span>
                                </button>
                            @else
                                <input type="submit" class="btn btn-primary" value="Display">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12"><br>
                        <div class="table-responsive">
                            <table class="table table-bordered zoom90" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center">
                                            <div class="form-check form-check-inline larger-checkbox">
                                                <input class="form-check-input" type="checkbox" id="checkAll" onclick="toggleCheckboxes()">
                                            </div>
                                        </th>
                                        <th>Emp ID</th>
                                        <th>Name</th>
                                        <th>Reimbursement Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($approvals->isEmpty())
                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                            <td colspan="6" class="text-center"><a><i>No Data Available</i></a></td>
                                        </tr>
                                    @else
                                    @php $no = 1; @endphp
                                        @foreach($approvals as $index => $approval)
                                        <tr>
                                            @if ($index > 0 && $approval->user->name === $approvals[$index-1]->user->name)
                                            <td style="border-bottom: none; border-top: none;"></td>
                                            <td style="border-bottom: none; border-top: none;"></td>
                                            <td style="border-bottom: none; border-top: none;"></td>
                                            <td style="border-bottom: none; border-top: none;">{{ $approval->f_type }}</td>
                                            <td style="border-bottom: none; border-top: none;"></td>
                                            <td style="border-bottom: none; border-top: none;"></td>
                                            @else
                                            <td class="text-center" style="border-bottom: none; border-top: none;">
                                                <div class="form-check form-check-inline larger-checkbox">
                                                    <input class="form-check-input data-checkbox" type="checkbox" value="option1" onclick="toggleCheckboxes2()" data-username="{{ $approval->f_req_by }}" data-form-id="{{ $approval->id }}">
                                                </div>
                                            </td>
                                            <td style="border-bottom: none; border-top: none;">{{ $approval->user->users_detail->employee_id }}</td>
                                            <td style="border-bottom: none; border-top: none;" id="{{ $no++ }}">
                                                {{ $approval->user->name }}
                                            </td>
                                            <td style="border-bottom: none; border-top: none;">{{ $approval->f_type }}</td>
                                            <td style="border-bottom: none; border-top: none;">
                                                @if($approval->status_id == 29) 
                                                <span class="m-0 font-weight-bold text-primary"><i class="fas fa-check-circle" style="color: #005eff;"></i> Approved</span>
                                                @elseif($approval->status_id == 2002) 
                                                <span class="m-0 font-weight-bold text-success"><i class="fas fa-check-circle" style="color: #01e476;"></i> Paid</span>
                                                @else 
                                                <span class="m-0 font-weight-bold text-danger"><i class="fas fa-times-circle" style="color: #ff0000;"></i> Unknown</span>
                                                @endif
                                            </td>
                                            <td style="border-bottom: none; border-top: none;" class="action text-center">
                                                <a href="/reimbursement/manage/view/{{ $approval->id }}" class="mr-2 btn btn-primary btn-sm btn-edit"><i class="fas fa-hand-pointer"></i> View</a>
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    @endif
                                    <tr style="border-bottom: 1px solid #dee2e6;">
                                        <td colspan="6" class="text-center">Copyright @ Author of ESS Perdana Consulting</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
function confirmPassword() {
    var password = prompt('Please enter your password:');
    if (password !== null) {
        // Send the plain password to the server
        var url = "/reimbursement/export/all/{{ $Month }}/{{ $Year }}?password=" + encodeURIComponent(password);
        window.location.href = url;
    }
}

function toggleCheckboxes() {
    var checkboxes = document.getElementsByClassName('data-checkbox');
    var checkAllCheckbox = document.getElementById('checkAll');
    var bulkPaidButton = document.getElementById('bulkPaid');
    var usersNameInput = document.getElementById('usersName');
    var formIdInput = document.getElementById('formId');

    var checkedUserNames = [];
    var checkedFormId = [];

    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = checkAllCheckbox.checked;
        if (checkboxes[i].checked) {
            var username = checkboxes[i].getAttribute('data-username');
            checkedUserNames.push(username);
            var id = checkboxes[i].getAttribute('data-form-id');
            checkedFormId.push(id);
        }
    }

    // Update the hidden input field with comma-separated user names
    usersNameInput.value = checkedUserNames.join(', ');
    formIdInput.value = checkedFormId.join(', ');

    // Check the state of the checkAll checkbox and show/hide the "Paid" button accordingly
    if (checkAllCheckbox.checked) {
        bulkPaidButton.style.display = 'block'; // Show the "Paid" button
    } else {
        bulkPaidButton.style.display = 'none'; // Hide the "Paid" button
    }
}

function toggleCheckboxes2() {
    var checkboxes = document.getElementsByClassName('data-checkbox');
    var bulkPaidButton = document.getElementById('bulkPaid');
    var usersNameInput = document.getElementById('usersName');
    var formIdInput = document.getElementById('formId');

    var checkedUserNames = [];
    var checkedFormId = [];

    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            var username = checkboxes[i].getAttribute('data-username');
            checkedUserNames.push(username);
            var id = checkboxes[i].getAttribute('data-form-id');
            checkedFormId.push(id);
        }
    }

    // Update the hidden input field with comma-separated user names
    usersNameInput.value = checkedUserNames.join(', ');
    formIdInput.value = checkedFormId.join(', ');

    // Show/hide the "Paid" button based on checked users
    if (checkedUserNames.length > 0) {
        bulkPaidButton.style.display = 'block'; // Show the "Paid" button
    } else {
        bulkPaidButton.style.display = 'none'; // Hide the "Paid" button
    }
}

</script>
<style>
.action{
    width: 190px;
}
</style>
@endsection
