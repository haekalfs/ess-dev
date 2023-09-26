@extends('layouts.main')

@section('title', 'Compliance - ESS')

@section('active-page-HR')
active
@endsection

@section('content')
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

<!-- Page Heading -->
<div class="row align-items-center">
    <div class="col">
        <h1 class="h3 mb-2 text-gray-800">API KEY Setting</h1>
        <h5 class="mb-4 text-danger"><i>Restricted Access</i><small style="color: red;"><i> &nbsp;&nbsp;Administrator</i></small></h5>
    </div>
</div>
<div class="card shadow mb-4">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">API List</h6>
        <div class="text-right">
            <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#newApiModal" ><i class="fas fa-plus fa-sm text-white-50"></i> Add API</a>
        </div>
    </div>
    <!-- Card Body -->
    <div class="card-body">
        <table class="table table-bordered zoom90 table-hover" id="dataTableUser" >
            <thead>
                <tr style="font-size: 13px">
                    <th width="20px">No</th>
                    <th width="200px">Name API</th>
                    <th>Public Key</th>
                    <th>Secret Key</th>
                    <th width="100px">Option</th>
                </tr>
            </thead>
            <tbody>
                @foreach($api_all as $api)
                <tr>
                    <td>{{$api->id}}</td>
                    <td>{{$api->name}}</td>
                    <td>{{$api->public_key}}</td>
                    <td>{{$api->secret_key}}</td>
                    <td class="d-flex justify-content-center ">
                        <a data-toggle="modal" data-target="#apiModal_{{ $api->id }}" title="Edit" class="btn btn-primary btn-sm" >
                            <i class="fas fa-fw fa-edit justify-content-center"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- Modal API NEW -->
<form action="/manage/api_key/add_api" method="POST">
@csrf
<div class="modal fade" id="newApiModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div  class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add New API</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
       <div class="modal-body ml-2 mr-2">
                <div>
                    <label for="password">Name API :</label>
                    <input class="form-control flex" name="add_name_api" placeholder="API Name..." value=""/>
                </div>
                <div class="mt-2">
                    <label for="password">Public Key :</label>
                    <input class="form-control flex" name="add_public_key" placeholder="Public Key..." value=""/>
                </div>
                <div class="mt-2">
                    <label for="password">Secret Key :</label>
                    <input class="form-control flex" name="add_secret_key" placeholder="Secret Key.." value=""/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary btn-sm" value="Save" id="btn-submit">
            </div>
        </div>
    </div>
</div>
</form>

<!-- Modal API EDIT -->
@foreach($api_all as $api)
<form action="/manage/api_key/update/{{ $api->id }}" method="POST">
@csrf
@method('PUT')
<div class="modal fade" id="apiModal_{{ $api->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div  class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit API {{ $api->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <div class="modal-body ml-2 mr-2">
                <div>
                    <label for="password">Public Key :</label>
                    <input class="form-control flex" name="input_public_key" placeholder="Public Key..." value="{{ $api->public_key }}"/>
                </div>
                <div class="mt-2">
                    <label for="password">Secret Key :</label>
                    <input class="form-control flex" name="input_secret_key" placeholder="Secret Key.." value="{{ $api->secret_key }}"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary btn-sm" value="Save" id="btn-submit">
            </div>
        </div>
    </div>
</div>
</form>
@endforeach
@endsection
