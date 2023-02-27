@extends('layouts.main')

@section('active-page-timesheet')
active
@endsection

@section('content')
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
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Time Report</h6>
        <div class="text-right">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="dataTable1" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Last Updated At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                    <tr>
                        <td>{{ $entry['month'] }}</td>
                        <td>{{ $entry['lastUpdatedAt'] }}</td>
                        <td>{{ $entry['status'] }}</td>
                        <td class="action text-center">
                            @if (!$entry['isSubmitted'])
                                <a href="{{ $entry['editUrl'] }}" class="btn btn-danger btn-sm">
                                    <i class="fas fa-fw fa-edit fa-sm text-white-50"></i> Edit
                                </a>
                            @endif
                            <a href="{{ $entry['previewUrl'] }}" class="btn btn-primary btn-sm" style="margin-left: 3%;">Preview</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
.action{
    width: 180px;
}
</style>
@endsection