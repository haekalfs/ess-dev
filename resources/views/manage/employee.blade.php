@extends('layouts.main')

@section('title', 'List Employee - ESS')

@section('active-page-employee')
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
<div class="card shadow mb-4">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h4 class="m-0 font-weight-bold text-primary">List Employee</h4>
    </div>
    <!-- Card Body -->
    <div class="card-body">
        <table class="table table-bordered table-hover text-center" id="dataTableUser">
            <thead>
                <tr style="font-size: 13px">
                    <th>Emp ID</th>
                    <th>User ID</th>
                    <th>Nama</th>
                    <th>Status Active</th>
                    <th>Employee Status</th>
                    <th>Position</th>
                    <th>Department</th>
                    {{-- <th>Option</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach($employee as $p)
                    @if($p->users_detail->position_id !== '4' && $p->users_detail->position_id !== '18' && $p->users_detail->position_id !=='19' && $p->users_detail->position_id !== NULL)
                        <tr>
                            <td>{{$p->users_detail->employee_id}}</td>
                            <td>{{$p->id }}</td>
                            <td>{{$p->name}}</td>
                            <td>{{$p->users_detail->status_active}}</td>
                            <td>{{$p->users_detail->employee_status}}</td>
                            <td>@if($p->users_detail->position_id){{ $p->users_detail->position->position_name }}@endif</td>
                            <td>@if($p->users_detail->department_id){{ $p->users_detail->department->department_name }}@endif</td>
                            {{-- <td class="row-cols-2 justify-content-betwen text-center">
                                <a href="/users/edit/{{ $p->id }}" title="Edit" class="btn btn-primary btn-sm" >
                                    <i class="fas fa-fw fa-edit justify-content-center"></i>
                                </a>
                                <a href="/users/hapus/{{ $p->id }}" title="Hapus" class="btn btn-danger btn-sm" ><i class="fas fa-fw fa-trash justify-content"></i></a>
                            </td> --}}
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
