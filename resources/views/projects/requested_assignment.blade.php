@extends('layouts.main')

@section('title', 'Requested Assignment - ESS')

@section('active-page-project')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 font-weight-bold text-gray-800"><i class="fas fa-network-wired"></i> Requested Assignment by User</h1>
    {{-- <a data-toggle="modal" data-target="#addMem" class="d-none d-sm-inline-block btn btn-sm @role('freelancer') btn-success @else btn-primary @endrole shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Request Assignment</a> --}}
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
        <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole" id="judul">Pending Request</h6>
        {{-- <div class="text-right">
            <button class="btn @role('freelancer') btn-success @else btn-primary @endrole btn-sm" type="button" id="manButton" style="margin-right: 10px;">+ Request Assignment</button>
        </div> --}}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="requestedAssignment" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Request Date</th>
                        <th>Requestor</th>
                        <th>For Project</th>
                        <th>Requested Periode</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($request as $record)
                    <tr>
                        <td>{{ $record->req_date }}</td>
                        <td>{{ $record->user->name }}</td>
                        <td><span class="long-text">{{ $record->company_project->project_name }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($record->periode_start)->format('d-M-Y') }} &nbsp; to &nbsp; {{ \Carbon\Carbon::parse($record->periode_end)->format('d-M-Y') }}</td>
                        <td class="text-center">
                            @if($record->status == 0)
                            <i class="fas fa-spinner fa-spin" style="color: #808080;"></i>
                            @elseif ($record->status == 404)
                            <i class="fas fa-times-circle" style="color: #ff0000;"></i>
                            @else
                            <i class="fas fa-check-circle" style="color: #0050db;"></i>
                            @endif
                        </td>
                        <td class="action text-center">
                            @if($record->status == 1)
                            <a class="btn btn-primary btn-sm" href="/assignment/requested/by/user/view/{{$record->id}}"><i class='fas fa-fw fa-eye'></i> View</a>
                            @elseif ($record->status == 404)
                            <a class="btn btn-primary btn-sm" href="/assignment/requested/by/user/view/{{$record->id}}"><i class='fas fa-fw fa-eye'></i> View</a>
                            @else
                            <a class="btn btn-primary btn-sm" href="/assignment/requested/by/user/approve/{{$record->id}}"><i class='fas fa-fw fa-check'></i> Process</a>
                            <a class="btn btn-danger btn-sm" href="/assignment/requested/by/user/reject/{{$record->id}}"><i class='fas fa-fw fa-ban'></i> Reject</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script src="{{ asset('js/project.js') }}"></script>
@endsection