@extends('layouts.main')

@section('title', 'Timesheet - ESS')

@section('active-page-timesheet')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4 zoom90">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800"><i class="fas fa-calendar"></i> Timesheet</h1>
    <div>
        <select class="form-control" id="task" name="task" required onchange="redirectToPage()">
            @foreach (array_reverse($yearsBefore) as $year)
                <option value="{{ $year }}" @if ($year == $yearSelected) selected @endif>{{ $year }}</option>
            @endforeach
        </select>
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
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole" id="judul">Time Report</h6>
        <div class="text-right">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" width="100%" cellspacing="0">
                <thead class="thead-light">
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
                            <a href="{{ $entry['previewUrl'] }}" class="btn @role('freelancer') btn-success @else btn-primary @endrole btn-sm" style="margin-left: 3%;"><i class="fas fa-eye"></i> Preview</a>
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
    width: 200px;
}
</style>
<script>
    function redirectToPage() {
        var selectedOption = document.getElementById("task").value;
        var url = "{{ url('/timesheet') }}"; // Specify the base URL

        url += "/" + selectedOption;

        window.location.href = url; // Redirect to the desired page
    }
</script>
@endsection