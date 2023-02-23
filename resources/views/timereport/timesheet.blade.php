@extends('layouts.main')

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
                </thead>
                <tbody>
                    @php
                    $currentMonth = date('m');
                    @endphp
                    @foreach (range(1, $currentMonth) as $entry)
                    <tr>
                        <td>{{ date("F", mktime(0, 0, 0, $entry, 1)) }}</td>
                        <td></td>
                        <td></td>
                        <td class="action"><a href="/timesheet/entry/{{ $entry }}" class="btn btn-primary btn-sm">View Details</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
.action{
    width: 150px;
}
</style>
@endsection