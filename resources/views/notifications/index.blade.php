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
<div class="row zoom90">
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-12">
        <div class="table-responsive">
            <table class="table table-hover" id="notificationsCenter">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Notification</th>
                        <th scope="col">Category</th>
                        <th scope="col">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach ($notifications as $notification)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>
                            <a class="dropdown-item notification-item d-flex align-items-center"
                                data-notification-id="{{ $notification->id }}">
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
                                    @if($notification->read_stat == 1)
                                    <span>{{ $notification->message}}</span>
                                    @else
                                    <span class="font-weight-bold">{{ $notification->message}}</span>
                                    @endif
                                </div>
                            </a>
                        </td>
                        <td>Test</td>
                        <td>{{ $notification->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
