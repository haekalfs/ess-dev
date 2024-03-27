@extends('layouts.main')

@section('title', 'Manage Users - ESS')

@section('active-page-system_management')
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
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-gray-800"><i class="fas fa-users"></i> Manage Users</h1>
        <p class="mb-3">Manage Users Account.</p>
    </div>
    <a class="btn btn-secondary btn-sm shadow-sm" href="/hrtools/manage/position"><i class="fas fa-solid fa-user-plus fa-sm text-white-50"></i> Position & Department</a>
</div>
<div class="card shadow mb-4 zoom90">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Users Accounts</h6>
        <div class="text-right">
            <a class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" href="/manage/users/non-internal/new-user-registration" ><i class="fas fa-plus fa-sm text-white-50"></i> Add User (Non-Internal)</a>
            <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm ml-2" href="/manage/users/new-user-registration" ><i class="fas fa-plus fa-sm text-white-50"></i> Add New User</a>
        </div>
    </div>
    <ul class="nav nav-tabs" id="pageTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="page1-tab" data-toggle="tab" href="#page1" role="tab" aria-controls="page1" aria-selected="true"><i class="fas fa-calendar-day"></i> Internal</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="page2-tab" data-toggle="tab" href="#page2" role="tab" aria-controls="page2" aria-selected="false"><i class="fas fa-plane-departure"></i> Non-Internal</a>
        </li>
    </ul>
    <div class="card-body">
        <div class="tab-content" id="pageTabContent">
            <div class="tab-pane fade show active" id="page1" role="tabpanel" aria-labelledby="page1-tab">
                <div class="table-responsive">
                    <table class="table table-bordered zoom90 table-hover" width="100%" id="dataTableUser" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>Emp ID</th>
                                <th>User ID</th>
                                <th>Nama</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Employement Status</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $p)
                            <tr>
                                <td class="text-primary font-weight-bold">{{$p->users_detail->employee_id}}</td>
                                <td class="text-secondary font-weight-bold">{{$p->id }}</td>
                                <td>{{$p->name}}</td>
                                <td>@if($p->users_detail->position_id){{ $p->users_detail->position->position_name }}@endif</td>
                                <td>@if($p->users_detail->department_id){{ $p->users_detail->department->department_name }}@endif</td>
                                <td>{{$p->users_detail->employee_status}}</td>
                                <td class="text-center">
                                    @if ($p->users_detail->status_active == "Active")
                                        <i class="fas fa-user-check" style="color: #0053fa;"></i>
                                    @else
                                        <i class="fas fa-user-times" style="color: #ff0000;"></i>
                                    @endif
                                </td>
                                <td class="text-center" style="width: 15%;">
                                    <a href="/users/edit/{{ Crypt::encrypt($p->id) }}" title="Edit" class="btn btn-primary btn-sm mr-2" >
                                        <i class="fas fa-fw fa-edit justify-content-center"></i> Edit
                                    </a>
                                    <a title="delete" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal{{ $p->id }}"><i class="fas fa-fw fa-trash justify-content"></i> Delete</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="page2" role="tabpanel" aria-labelledby="page2-tab">
                <div class="table-responsive">
                    <table class="table table-bordered zoom90 table-hover" width="100%" id="dataTable" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>Emp ID</th>
                                <th>User ID</th>
                                <th>Nama</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Employement Status</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataFreelancer as $fl)
                            <tr>
                                <td class="text-primary font-weight-bold">{{$fl->users_detail->employee_id}}</td>
                                <td class="text-secondary font-weight-bold">{{$fl->id }}</td>
                                <td>{{$fl->name}}</td>
                                <td>@if($fl->users_detail->position_id){{ $fl->users_detail->position->position_name }}@endif</td>
                                <td>@if($fl->users_detail->department_id){{ $fl->users_detail->department->department_name }}@endif</td>
                                <td>{{$fl->users_detail->employee_status}}</td>
                                <td class="text-center">
                                    @if ($fl->users_detail->status_active == "Active")
                                        <i class="fas fa-user-check" style="color: #0053fa;"></i>
                                    @else
                                        <i class="fas fa-user-times" style="color: #ff0000;"></i>
                                    @endif
                                </td>
                                <td class="text-center" style="width: 15%;">
                                    <a href="/users/edit/{{ Crypt::encrypt($fl->id) }}" title="Edit" class="btn btn-primary btn-sm mr-2" >
                                        <i class="fas fa-fw fa-edit justify-content-center"></i> Edit
                                    </a>
                                    <a title="delete" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal{{ $p->id }}"><i class="fas fa-fw fa-trash justify-content"></i> Delete</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
@foreach($data as $p)
<!-- Modal Deelete -->
<div class="modal fade" id="deleteModal{{ $p->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title  text-white" id="staticBackdropLabel">Alert !!</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img class="mb-2" width="96" height="96" src="https://img.icons8.com/color/96/general-warning-sign.png" alt="general-warning-sign"/>
        <h6>Are You Sure Want Delete This Record !!!</h6>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn-sm btn-primary" data-dismiss="modal">Cancel</button>
        <a href="/users/delete/{{ Crypt::encrypt($p->id) }}" title="Delete" class="btn btn-danger btn-sm" >Yes Im Sure</a>
      </div>
    </div>
  </div>
</div>
@endforeach
@endsection
