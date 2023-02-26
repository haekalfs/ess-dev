@extends('layouts.main')

@section('active-page-timesheet')
active
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Time Report</h6>
        <div class="text-right">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
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
                        <td></td>
                        <td class="action">
                            <a href="{{ $entry['editUrl'] }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-fw fa-edit fa-sm text-white-50"></i> Edit
                            </a>
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
    width: 140px;
}
</style>
@endsection