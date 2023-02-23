@extends('layouts.login')

@section ('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800">Dashboard</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
</div>
@endsection
@section('content-sidebar')

@endsection

@section('sidebar-info-ssl')
<hr class="sidebar-divider my-0">
<div class="text-center text"><br>
    <p><strong><a>Supported Browser</a></strong><br>
        <a>Chrome, Firefox, IE9, Opera, Safari, dan mobile browser terkini
            <br>Min : 1024 x 768</a>
    </p>
</div>
<div class="text-center">
    <img class="text-center" width="150px" height="100px" src="{{ asset('img/ssl.png') }}" />
</div>
@endsection
