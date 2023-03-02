@extends('layouts.main')

@section('active-page-users')
active
@endsection

@section('content')
<!-- Page Heading -->

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

<div class="container">
    <div class="card mt-5 d-flex p-2">
        <div class="card-header text-center">
            <h3>Data Pegawai</h3>
        </div>
        <div class="card-body">
            <a href="/users/tambah" class="btn btn-primary">Input Pegawai Baru</a>
            <br/>
            <br/>
            <table class="table table-bordered table-hover table-striped" id="dataTable">
                <thead>
                    <tr style="font-size: 13px" class="text-center">
                        <th>Emp ID</th>
                        <th>User ID</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Hired Date</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $p)
                    <tr style="font-size: 12px">
                        <td>{{$p->users_detail->employee_id}}</td>
                        <td>{{$p->user_id }}</td>
                        <td>{{$p->name}}</td>
                        <td>{{$p->users_detail->employee_status}}</td>
                        <td>{{$p->users_detail->hired_date}}</td>
                        <td>{{$p->users_detail->position}}</td>
                        <td>{{$p->users_detail->position}}</td>
                        <td class="row-cols-2 justify-content-betwen">
                            <a href="/users/edit/{{ $p->id }}" title="Edit" class="btn btn-warning btn-sm" >
                                <i class="fas fa-fw fa-edit justify-content-center"></i>
                            </a>
                            <a href="/users/hapus/{{ $p->id }}" title="Hapus" class="btn btn-danger btn-sm" ><i class="fas fa-fw fa-trash justify-content"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- <nav>
                Halaman : {{ $data->currentPage() }} <br/>
                Jumlah Data : {{ $data->total() }} <br/>
                Data Per Halaman : {{ $data->perPage() }} <br/>
            </nav>
            <nav aria-label="...">
                <ul class="pagination">
                    <li class="page-item">
                        <a class="page-link" href="{{ $data->url(1) }}" class="{{ ($data->currentPage() == 1) ? ' disabled' : '' }}">
                            First
                        </a>
                    </li>
                  <li class="page-item">
                    <a class="page-link" a href="{{ $data->previousPageUrl() }}" class="{{ ($data->currentPage() == 1) ? ' disabled' : '' }}">Previous</a>
                  </li>
                     @for ($i = 1; $i <= $data->lastPage(); $i++)
                    <a style="display: inline-block; margin: 5px; padding: 5px 10px; color:white; background-color:{{ ($data->currentPage() == $i) ? '#0e75bc' : '#ccc' }};" href="{{ $data->url($i) }}">
                        {{ $i }}
                    </a>
                    @endfor
                  <li class="page-item">
                    <a class="page-link" a href="{{ $data->nextPageUrl() }}" class="{{ ($data->currentPage() == $data->lastPage()) ? ' disabled' : '' }}">Next</a>
                  </li>
                  <li class="page-item">
                    <a class="page-link" href="{{ $data->url($data->lastPage()) }}" class="{{ ($data->currentPage() == $data->lastPage()) ? ' disabled' : '' }}">
                        Last
                    </a>
                </li>
                </ul>
              </nav> --}}
        </div>
    </div>
</div>
@endsection
