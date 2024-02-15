@extends('layouts.main')

@section('title', 'Manage Medical - ESS')

@section('active-page-system_management')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h2 class=" mb-2 text-gray-800"><i class="fas fa-fw fa-hand-holding-medical"></i><b>Review Medical Reimburse</b></h2>
    {{-- <small style="color:red"><i>Finance Manager</i></small> --}}
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
                                            Unpaid <i class="fa fa-times-circle" aria-hidden="true"></i>
                                            @break
                                        @case(29)
                                            Paid <i class="fa fa-check-circle" aria-hidden="true"></i>
                                            @break
                                        @default
                                            Status Tidak Dikenal</span>
                                    @endswitch
                                    </td>
                                    <td>{{ $m->med_payment }}</td>
                                    <td>Rp. {{ $m->medical_approval->total_amount_approved }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#viewModal{{ $m->id }}"><i class="fa fa-eye" aria-hidden="true"></i> View</button>
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

{{-- Modal Add Balance --}}
@foreach ($med as $m)
<form method="POST" action="/medical/review/{{ $m->id }}" enctype="multipart/form-data" >
@method('PUT')
@csrf
    <div class="modal fade" id="viewModal{{ $m->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="balanceEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg zoom90" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title  text-white" id="staticBackdropLabel">Medical Request MED_{{ $m->id }}</h5>
                    <button type="button" class="close" style="color: white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-start d-flex justify-content-center">
                    <div class="row">
                        <div class="col-8 col-sm-6">
                            {{-- @foreach($emp_medical_balance as $medBalance) --}}
                            <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                                <tr>
                                    <th>Medical Balance</th>
                                    <td style="text-align: start; font-weight:500">: {{ $emp_medical_balance->medical_balance }}</td>
                                </tr>
                                <tr>
                                    <th>Medical Remaining</th>
                                    <td style="text-align: start; font-weight:500">: {{ $emp_medical_balance->medical_remaining }}</td>
                                </tr>
                                <tr>
                                    <th>Medical Deducted</th>
                                    <td style="text-align: start; font-weight:500">: {{ $emp_medical_balance->medical_deducted }}</td>
                                </tr>
                                <tr>
                                    <th>Active Periode</th>
                                    <td style="text-align: start; font-weight:500">: {{ $emp_medical_balance->active_periode }}</td>
                                </tr>
                            </table>
                            {{-- @endforeach --}}
                        </div>
                        <div class="col-4 col-sm-6">
                            <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                                <tr>
                                    <th>Name</th>
                                    <td style="text-align: start; font-weight:500">: {{ $m->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Request Date</th>
                                    <td style="text-align: start; font-weight:500">: {{ $m->med_req_date }}</td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td style="text-align: start; font-weight:500">: Rp. {{ $m->medical_approval->total_amount_approved }}</td>
                                </tr>
                                <tr>
                                    <th>No Account Bank</th>
                                    <td style="text-align: start; font-weight:500">: {{ $m->user->users_detail->usr_bank_account }}  An. {{ $m->user->users_detail->usr_bank_account_name }} </td>
                                </tr>
                            </table>
                        </div>
                        <table class="table table-borderless table-sm" width="100%" cellspacing="0">
                            <tr>
                                <td style="text-align: center;">
                                    <div class="form-group justify-content-center" style="display: flex; align-items: center;">
                                        <label  for="password" style="height: 15px; text-align: center; margin-right: 10px;">Total Payable (Rp.) :</label>
                                        <input class="form-control flex" name="input_total_paid" id="total_paid" value="{{ $m->medical_approval->total_amount_approved }}" style="width: 200px; height: 25px;" oninput="formatCurrency(this)"/>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sm btn-success">Paid</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endforeach
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
