@extends('layouts.main')

@section('content')
@if (session('status'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ session('status') }}</strong>
</div>
@endif
@error('email')
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@enderror
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
<div class="zoom90 d-sm-flex align-items-center justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-bell fa-fw"></i> Notification Center</h1>
        <p class="mb-4">Stay Informed: Your Latest Notifications Await</a>.</p>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Notifications</h6>
        <div class="text-right">
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
            @foreach ($notifications as $notification)
                <a class="dropdown-item notification-item d-flex align-items-center" data-notification-id="{{ $notification->id }}">
                    @if($notification->importance == 1)
                    <div class="mr-3">
                        <div class="icon-circle bg-success">
                            <i class="fas fa-check-square text-white"></i>
                        </div>
                    </div>
                    @else
                    <div class="mr-3">
                        <div class="icon-circle bg-warning">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                    </div>
                    @endif
                    <div>
                        <div class="small text-gray-500">{{ $notification->created_at }}</div>
                        @if($notification->read_stat == 1)
                        <span>{{ $notification->message}}</span>
                        @else
                        <span class="font-weight-bold">{{ $notification->message}}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
