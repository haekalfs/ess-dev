@extends('layouts.main')

@section('title', 'Manage Leave - ESS')

@section('active-page-leave')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-2 text-gray-800">Manage Leaves <small style="color: red;"><i> &nbsp;&nbsp;Administrator</i></small></h1>
    {{-- <a class="d-none d-sm-inline-block btn btn-secondary btn-sm shadow-sm" type="button" href="/timesheet/review/fm/export"><i class="fas fa-fw fa-download fa-sm text-white-50"></i> Export All (XLS)</a> --}}
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
@yield('manage-leave-user-info')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Leaves Information</h6>
        {{-- <div class="text-right">
            <a class="d-none d-sm-inline-block btn btn-secondary btn-sm shadow-sm" type="button" href="/timesheet/review/fm/export/{{ $Month }}/{{ $Year }}"><i class="fas fa-fw fa-download fa-sm text-white-50"></i> Export All (XLS)</a>
        </div> --}}
    </div>
    <div class="card-body">
        @yield('manage-leave-table')
    </div>
</div>
<style>
.action{
    width: 190px;
}
.invisible {
    border-top: none;
    border-bottom: none;
}
</style>
@endsection
