@extends('layouts.main')

@section('active-page-timesheet')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-2 text-gray-800">Timesheet Review <small style="color: red;"><i> &nbsp;&nbsp;Finance Manager</i></small></h1>
    <a class="d-none d-sm-inline-block btn btn-secondary btn-sm shadow-sm" type="button" href="/timesheet/review/fm/export"><i class="fas fa-fw fa-download fa-sm text-white-50"></i> Export All (XLS)</a>
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
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Approved Timesheets</h6>
        <div class="text-right">
            <a class="btn btn-secondary btn-sm" type="button" href="#" id="manButton"><i class="fas fa-fw fa-download fa-sm text-white-50"></i> Export Selected</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="dataTable1" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date Requested</th>
                        <th>Timesheet Periode</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($approvals as $approval)
                    <tr>
                        @if ($approval)
                        <td>{{ $approval->user_timesheet }}</td>
                        <td>{{ $approval->date_submitted }}</td>
                        <td>{{ date("F", mktime(0, 0, 0, substr($approval->month_periode, 4, 2), 1)) }} - {{ substr($approval->month_periode, 0, 4) }}</td>
                        <td class="action text-center">
                            {{-- <a href="/timesheet/review/fm/export" class="btn btn-primary btn-sm"><i class="fas fa-fw fa-download fa-sm text-white-50"></i> Download</a> --}}
                            <div class="form-check form-check-inline larger-checkbox">
                                <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                            </div>
                        </td>
                        @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="action">
                            <a href="#" class="btn btn-primary btn-sm">
                                <i class="fas fa-fw fa-edit fa-sm text-white-50"></i> Edit
                            </a>
                            <a href="#" class="btn btn-danger btn-sm" style="margin-left: 3%;">Preview</a>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
.action{
    width: 190px;
}
</style>
@endsection
