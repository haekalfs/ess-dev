@extends('layouts.main')

@section('title', 'Manage Medical - ESS')

@section('active-page-system_management')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4 zoom90">
    <h2 class=" mb-2 text-gray-800"><i class="fas fa-notes-medical"></i><b> Review Medical Reimburse</b></h2>
    {{-- <small style="color:red"><i>Finance Manager</i></small> --}}
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('failed'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{!! $message !!}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

<form method="GET" action="/medical/review">
<div class="card shadow mb-4 zoom90">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Employee Medical Review</h6>
        {{-- <div class="text-right">
            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#balanceModal"><i class="fa fa-plus" aria-hidden="true"></i> Add Balance</button>
        </div> --}}
    </div>
    <div class="card-body">
        <div class="col-md-12 zoom90">
            <div class="row d-flex justify-content-start">
                <div class="col-md-12">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="position_id">Filter by Name :</label>
                                <select name="user_id" class="form-control" id="user_id">
                                    <option value="1">All User</option>
                                    @foreach($user as $us)
                                    <option value="{{ $us->id }}" {{ request('user_id') == $us->id ? 'selected' : '' }}>
                                        {{ $us->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="password">Month :</label>
                                <select class="form-control" name="month">
                                    @foreach (range(1, 12) as $month)
                                        <option value="{{ $month }}" @if ($month == $currentMonth) selected @endif {{ request('month') == $month ? 'selected' : '' }} >
                                            {{ date("F", mktime(0, 0, 0, $month, 1)) }} 
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">  
                            <div class="form-group">
                                <label for="status">Year :</label>
                                <select name="year" class="form-control" id="year">
                                    {{-- <option value="">All Years</option> --}}
                                    @foreach ($years as $y)
                                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">  
                            <div class="form-group">
                                <label for="status">Status Payment :</label>
                                <select name="status_pay" class="form-control">
                                    {{-- <option value="">All Years</option> --}}
                                    <option value="20" {{  request('status_pay') == '20' ? 'selected' : ''  }}>Unpaid</option>
                                    <option value="29" {{  request('status_pay') == '29' ? 'selected' : ''  }}>Paid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-self-end justify-content-start">
                            <div class="form-group">
                                <div class="align-self-center">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="col-md-12"><br>
                    <div class="table-responsive">
                        <table class="table table-bordered zoom90 table-hover text-center" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Medical ID</th>
                                    <th>Name</th>
                                    <th>Request Date</th>
                                    <th>Status Payment</th>
                                    <th>Payment Method</th>
                                    <th>Total Amount Approved</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($med as $m)
                                <tr>
                                    <td>MED_{{ $m->id }}</td>
                                    <td>{{ $m->user->name }}</td>
                                    <td>{{ $m->med_req_date }}</td>
                                    <td>
                                    @switch($m->medical_payment->paid_status)
                                        @case(20)
                                            <span class="text-danger font-weight-bold">Unpaid <i class="fa fa-times-circle" aria-hidden="true"></i></span>
                                            @break
                                        @case(29)
                                            <span class="text-success font-weight-bold">Paid  <i class="fa fa-check-circle" aria-hidden="true"></i></span>
                                            @break
                                        @default
                                            Status Tidak Dikenal</span>
                                    @endswitch
                                    </td>
                                    <td>{{ $m->med_payment }}</td>
                                    <td>Rp. {{ $m->medical_approval->total_amount_approved }}</td>
                                    <td class="text-center">
                                        <a title="View" class="btn btn-primary btn-sm" type="button" href="/medical/review/view/{{ $m->id }}"><i class="fa fa-eye" aria-hidden="true"></i> View</a>
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
</div>
</form>


<script>
    function formatCurrency(input) {
    // Mengambil nilai input
    let amount = input.value;

    // Menghapus karakter selain angka
    amount = amount.replace(/\D/g, '');

    // Menambahkan pemisah ribuan setiap 3 angka
    amount = amount.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Memperbarui nilai input dengan format terbaru
    input.value = amount;
    }
</script>
@endsection
