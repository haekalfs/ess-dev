@extends('layouts.main')

@section('title', 'Compliance - ESS')

@section('active-page-HR')
active
@endsection

@section('content')
<form method="POST" action="/hr/compliance/update/regulations">
    <!-- Page Heading -->
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3 mb-2 text-gray-800">Compliance Settings</h1>
            <p class="mb-4 text-danger"><i>Restricted Access</i></p>
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

    @if ($message = Session::get('warning'))
    <div class="alert alert-warning alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
    @endif
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole">Employee Information</h6>
                    <div class="text-right">
                        <a class="btn btn-secondary btn-sm" type="button" href="" id="manButton">Save</a>
                    </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Employee ID :</label>
                                        <input class="form-control"  name="employee_id" placeholder="Employee ID..." value=""readonly/>
                                        @if($errors->has('employee_id'))
                                            <div class="text-danger">
                                                {{ $errors->first('employee_id')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">User ID :</label>
                                    <input class="form-control flex" id="usr_id" name="usr_id" placeholder="User ID..."/>
                                    <h6 style="color:red; font-size: 13px; font-style: italic" id="user-id-error"></h6>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="password">Email :</label>
                                        <input class="form-control" name="email" placeholder="Email..." />
                                        @if($errors->has('email'))
                                            <div class="text-danger">
                                                {{ $errors->first('email')}}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="password">Password :</label>
                                        <input class="form-control" name="password" value="" placeholder="****"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<style>
.action{
    width: 180px;
}
</style>
@endsection
