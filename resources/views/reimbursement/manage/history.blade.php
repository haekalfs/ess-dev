@extends('layouts.main')

@section('title', 'Reimbursement Review - ESS')

@section('active-page-reimburse')
active
@endsection

@section('content')
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-hand-holding-usd"></i> Manage Reimbursement <small style="color: red;"><i> &nbsp;&nbsp;Finance Department</i></small></h1>
    <div>
        <div class="d-sm-flex justify-content-end" id="bulkPaid">
        <form method="POST" action="/reimbursement/manage/disbursed/all">
            @csrf
            <button type="submit" class="btn btn-success btn-sm btn-edit mr-3 shadow-sm"><i class="fas fa-check"></i> Mark as Paid</button>
            <input type="hidden" name="usersName" id="usersName" value="" />
            <input type="hidden" name="formId" id="formId" value="" />
        </form>
        <form method="POST" action="/reimbursement/export/selected-items">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm btn-edit shadow-sm"><i class="far fa-file-excel"></i> Export Selected</button>
            <input type="hidden" name="usersName2" id="usersName2" value="" />
            <input type="hidden" name="formId2" id="formId2" value="" />
        </form>
        </div>
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
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Filter Reimbursement</h6>
        <div class="text-right">
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
                            <table class="table table-bordered zoom90" id="listAssignments" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center">
                                            <div class="form-check form-check-inline larger-checkbox">
                                                <input class="form-check-input" type="checkbox" id="checkAll" onclick="toggleCheckboxes()">
                                            </div>
                                        </th>
                                        <th>Form ID</th>
                                        <th>Name</th>
                                        <th>Reimbursement Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>@php $no = 1; @endphp
                                    @foreach($approvals as $index => $approval)
                                        <tr>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline larger-checkbox">
                                                    <input class="form-check-input data-checkbox" type="checkbox" value="option1" onclick="toggleCheckboxes2()" data-username="{{ $approval->f_req_by }}" data-form-id="{{ $approval->id }}">
                                                </div>
                                            </td>
                                            <td>{{ $approval->f_id }}</td>
                                            <td id="{{ $no++ }}">
                                                {{ $approval->user->name }}
                                            </td>
                                            <td>{{ $approval->f_type }}</td>
                                            <td>
                                                @if($approval->status_id == 29)
                                                <span class="m-0 font-weight-bold text-primary"><i class="fas fa-check-circle" style="color: #005eff;"></i> Approved</span>
                                                @elseif($approval->status_id == 2002)
                                                <span class="m-0 font-weight-bold text-success"><i class="fas fa-check-circle" style="color: #01e476;"></i> Paid</span>
                                                @else
                                                <span class="m-0 font-weight-bold text-danger"><i class="fas fa-times-circle" style="color: #ff0000;"></i> Unknown</span>
                                                @endif
                                            </td>
                                            <td class="action text-center">
                                                <a href="/reimbursement/manage/view/{{ $approval->id }}" class="mr-2 btn btn-primary btn-sm btn-edit"><i class="fas fa-eye"></i> View</a>
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
    </form>
</div>
<script>
function toggleCheckboxes() {
    var checkboxes = document.getElementsByClassName('data-checkbox');
    var checkAllCheckbox = document.getElementById('checkAll');
    var usersNameInput = document.getElementById('usersName');
    var formIdInput = document.getElementById('formId');
    var usersNameInput2 = document.getElementById('usersName2');
    var formIdInput2 = document.getElementById('formId2');

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
    usersNameInput2.value = checkedUserNames.join(', ');
    formIdInput2.value = checkedFormId.join(', ');
}

function toggleCheckboxes2() {
    var checkboxes = document.getElementsByClassName('data-checkbox');
    var bulkPaidButton = document.getElementById('bulkPaid');
    var usersNameInput = document.getElementById('usersName');
    var formIdInput = document.getElementById('formId');
    var usersNameInput2 = document.getElementById('usersName2');
    var formIdInput2 = document.getElementById('formId2');

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
    usersNameInput2.value = checkedUserNames.join(', ');
    formIdInput2.value = checkedFormId.join(', ');

}

</script>
<style>
.action{
    width: 190px;
}
</style>
@endsection
