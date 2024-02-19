@extends('layouts.main')

@section('title', 'Vendor Data KIP - ESS')

@section('active-page-HR')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center zoom90 justify-content-between mb-4">
    <h1 class="h4 mb-0 text-gray-800 font-weight-bold"><i class="fas fa-network-wired"></i> Vendor List</h1>
    <a data-toggle="modal" data-target="#addMem" class="d-none d-sm-inline-block btn btn-sm @role('freelancer') btn-success @else btn-primary @endrole shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Add New Vendor</a>
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
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Vendor List</h6>
        {{-- <div class="text-right">
            <button class="btn @role('freelancer') btn-success @else btn-primary @endrole btn-sm" type="button" id="manButton" style="margin-right: 10px;">+ Request Assignment</button>
        </div> --}}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered zoom90" id="myProjects" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Company</th>
                        <th>Contact Name</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th width='120px'>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vdData as $record)
                    <tr>
                        <td>{{ $record->id }}</td>
                        <td>{{ $record->company }}</td>
                        <td>{{ $record->contact_name }}</td>
                        <td>{{ $record->email_address }}</td>
                        <td>{{ $record->phone_number }}</td>
                        <td>{{ $record->address }}</td>
                        <td class="text-center">
                            <div class=''>
                                <a class='btn btn-danger btn-sm' type='button' id='dropdownMenu2' data-toggle='dropdown'
                                    aria-haspopup='true' aria-expanded='false'>
                                    Action
                                </a>
                                <div class='dropdown-menu' style="zoom: 100%;" aria-labelledby='dropdownMenu2'>
                                    <a class='dropdown-item' target="_blank" href="{{$urlEform}}/po/create-from-vendor/{{ $record->id }}"><i class="fas fa-fw fa-print"></i> Create PO</a>
                                    <a class='dropdown-item' href="#"><i class="fas fa-fw fa-print"></i> Create PR</a>
                                    <hr>
                                    <a class='dropdown-item' href="/vendor-list/item/delete/{{ $record->id }}"><i class="fas fa-fw fa-trash-alt"></i> Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div>
    <div class="text-right">
        <a data-toggle="modal" data-target="#addModal" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-file-export"></i> Export Data</a>
    </div>
</div>

<div class="modal fade" id="addMem" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Add New Data Vendor</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="{{ route('add_vendor') }}" method="post">
                @csrf
				<div class="modal-body" style="">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="text-primary" for="email">Date Created :</label>
                                        <?php $date_str = date('Y-m-d');
                                        $date = date('j F Y', strtotime($date_str));
                                        echo $date; ?>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="email">Company :</label>
                                            <input type="text" class="form-control" required name="company">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="password">Contact Name :</label>
                                            <input type="text" class="form-control" name="contact_name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label for="selectOption">Vendor Type:</label>
                                                <select class="form-control" id="selectOption" name="vendor_type">
                                                    <option value="0">Corporate</option>
                                                    <option value="1">Individual</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address :</label>
                                    <input type="email" class="form-control" required name="email_address">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Phone Number :</label>
                                    <input type="text" class="form-control" name="phone">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Country :</label>
                                    <input type="text" class="form-control" required name="country">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Address :</label>
                                    <textarea type="text" class="form-control" name="address"></textarea>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                  </div>
			</form>
		</div>
	</div>
</div>
@endsection

@section('javascript')
@endsection
