@extends('layouts.main')

@section('active-page-approval')
active
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Timesheet Approval <small style="color: red;"><i> &nbsp;&nbsp;Director</i></small></h1>
<p class="mb-4">Approval Page.</p>
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
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Approval History</h6>
        <div class="text-right">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="dataTable1" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Request Date</th>
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
                            <a href="/approval/director/{{$approval->user_timesheet}}/{{ substr($approval->month_periode, 0, 4) }}/{{ substr($approval->month_periode, 4, 2) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-fw fa-edit fa-sm text-white-50"></i> Approve
                            </a>
                            <a href="/reject/director/{{$approval->user_timesheet}}/{{ substr($approval->month_periode, 0, 4) }}/{{ substr($approval->month_periode, 4, 2) }}" class="btn btn-danger btn-sm" style="margin-left: 3%;">Reject</a>
                            <a href="/approval/director/preview/{{$approval->user_timesheet}}/{{ substr($approval->month_periode, 0, 4) }}/{{ substr($approval->month_periode, 4, 2) }}" class="btn btn-secondary btn-sm" style="margin-left: 3%;">Preview</a>
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
    width: 250px;
}
</style>
@endsection
