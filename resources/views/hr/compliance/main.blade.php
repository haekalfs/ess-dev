@extends('layouts.main')

@section('title', 'Compliance - ESS')

@section('active-page-HR')
active
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Compliance Settings</h1>
<p class="mb-4 text-danger"><i>Restricted Access</i></a></p>

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
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button"
                aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">General</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse show" id="collapseCardExample">
                <div class="card-body">
                    <div class="col-md-12">
                        <table class="table table-borderless">
                            {{-- <thead>
                                <tr>
                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole h5" colspan="2">Bank Account</th>
                                </tr>
                            </thead> --}}
                            <tbody>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Bank Name</td>
                                    <td>: </td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Bank Branch</td>
                                    <td>:</td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Bank Account Num</td>
                                    <td>: </td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 200px;">Bank Account Name</td>
                                    <td>:</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardProject" class="d-block card-header py-3" data-toggle="collapse" role="button"
                aria-expanded="true" aria-controls="collapseCardProject">
                <h6 class="m-0 font-weight-bold text-primary">Timesheet</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse show" id="collapseCardProject">
                <div class="card-body zoom90">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="text-primary" for="email">Date Prepared :</label>
                                        <?php $date_str = date('Y-m-d');
                                        $date = date('j F Y', strtotime($date_str));
                                        echo $date; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">No :</label>
                                            <input type="text" class="form-control" required name="no_doc">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">Reference Doc :</label>
                                            <input type="text" class="form-control" name="ref_doc">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Project :</label>
                                    <select class="form-control" id="update_location" name="project" required>
                                        
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Notes Assigment :</label>
                                    <textarea type="text" class="form-control" name="notes"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardProject1" class="d-block card-header py-3" data-toggle="collapse" role="button"
                aria-expanded="true" aria-controls="collapseCardProject1">
                <h6 class="m-0 font-weight-bold text-primary">Timesheet</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse show" id="collapseCardProject1">
                <div class="card-body">
                    <div class="col-md-12">
                        <table class="table table-borderless">
                            {{-- <thead>
                                <tr>
                                    <th style="padding-left: 0;" class="m-0 font-weight-bold @role('freelancer') text-success @else text-primary @endrole h5" colspan="2">Bank Account</th>
                                </tr>
                            </thead> --}}
                            <tbody>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Bank Name</td>
                                    <td>: </td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Bank Branch</td>
                                    <td>:</td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 150px;">Bank Account Num</td>
                                    <td>: </td>
                                </tr>
                                <tr class="table-sm">
                                    <td style="width: 200px;">Bank Account Name</td>
                                    <td>:</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 text-right">
        <a class="btn btn-primary shadow-sm"> Save</a>
    </div>
</div>

<style>
.action{
    width: 180px;
}
</style>
@endsection
