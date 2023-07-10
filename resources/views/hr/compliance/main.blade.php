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
        <button class="btn btn-primary" value="Save">Save</button>
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
        <!-- Area Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Cutoff Date</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Timesheet Submission :</label>
                                    <input type="number" class="form-control" name="date_prepared" value="{{ $cutoffDate->date }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Timesheet Approval :</label>
                                    <input type="text" class="form-control" name="date_prepared" value="" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Leave Approval :</label>
                                    <input type="number" class="form-control" name="date_prepared" placeholder="1-28/29/30/31....">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Reimburse & Med Approval :</label>
                                    <input type="number" class="form-control" name="date_prepared" placeholder="1-28/29/30/31....">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="comment">Financial Password :</label>
                                    <input type="password" class="form-control" name="date_prepared" value="{{ $financialPass }}">
                                </div>
                                <div class="form-group">
                                    <label for="comment">Reconfirm Password :</label>
                                    <input type="password" class="form-control" name="date_prepared" value="{{ $financialPass }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Approvers</h6>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Approver 1 :</label>
                                    <input type="number" class="form-control" name="date_prepared" placeholder="1-28/29/30/31....">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Approver 1 :</label>
                                    <input type="number" class="form-control" name="date_prepared" placeholder="1-28/29/30/31....">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Approver 1 :</label>
                                    <input type="number" class="form-control" name="date_prepared" placeholder="1-28/29/30/31....">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Approver 1 :</label>
                                    <input type="number" class="form-control" name="date_prepared" placeholder="1-28/29/30/31....">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Prior Approval :</label>
                                    <input type="number" class="form-control" name="date_prepared" placeholder="1-28/29/30/31....">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Prior Approval :</label>
                                    <input type="number" class="form-control" name="date_prepared" placeholder="1-28/29/30/31....">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Prior Approval :</label>
                                    <input type="number" class="form-control" name="date_prepared" placeholder="1-28/29/30/31....">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Prior Approval :</label>
                                    <input type="number" class="form-control" name="date_prepared" placeholder="1-28/29/30/31....">
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
