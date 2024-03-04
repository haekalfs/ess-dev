@extends('layouts.main')

@section('title', 'Manage Medical - ESS')

@section('active-page-system_management')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4 zoom90">
    <h2 class=" mb-2 text-gray-800"><i class="fas fa-book-medical"></i><b> Manage Medical Balance</b></h2>
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

<form method="GET" action="/medical/manage">
<div class="card shadow mb-4 zoom90">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold" id="judul">Employee Medical Balance</h6>
        <div class="text-right">
            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#balanceModal"><i class="fa fa-plus" aria-hidden="true"></i> Add Balance</button>
        </div>
    </div>
    <div class="card-body">
        <div class="col-md-12 zoom90">
            <div class="row d-flex justify-content-start">
                <div class="col-md-12">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="position_id">Filter by Name:</label>
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
                        <div class="col-md-2">  
                            <div class="form-group">
                                <label for="status">Year:</label>
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
                        <div class="col-md-4 d-flex align-self-end justify-content-start">
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
                        <table class="table table-bordered zoom90 table-hover " id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    {{-- <th class="text-center">
                                        <div class="form-check form-check-inline larger-checkbox">
                                            <input class="form-check-input" type="checkbox" id="checkAll" onclick="toggleCheckboxes()">
                                        </div>
                                    </th> --}}
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Balance</th>
                                    <th>Remaining</th>
                                    <th>Deducted</th>
                                    <th>Active Periode</th>
                                    <th>Expiration</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($medBalance as $mB)
                                <tr>
                                    <td>{{ $mB->user->users_detail->employee_id }}</td>
                                    <td>{{ $mB->user->name }}</td>
                                    <td>{{ $mB->medical_balance }}</td>
                                    <td>{{ $mB->medical_remaining }}</td>
                                    <td>{{ $mB->medical_deducted }}</td>
                                    <td>{{ $mB->active_periode }}</td>
                                    <td>{{ $mB->expiration }}</td>
                                    <td class="text-center"><button class="btn btn-warning btn-sm" type="button" data-toggle="modal" data-target="#balanceEditModal{{ $mB->id }}"><i class="fa fa-edit" aria-hidden="true"></i> Edit</button></td>
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
{{-- <div class="card shadow mb-4">
    
    <div class="card-body">
    <!-- Form filter -->
    <form action="/manage/list/employees/" method="GET">
    @csrf
   <div class="col-md-12 zoom90">
        <div class="row">
            <div class="form-group">
                <label for="position_id">Filter by Position:</label>
                <select name="position_id" class="form-control" id="position_id">
                    <option value="">All Positions</option>
                    <option value="4,18,19" {{ request('position_id') == '4,18,19' ? 'selected' : '' }}>Consultant</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-download fa-sm text-white-50"></i> Filter</button>
        </div>
    </div>
    </form>
    </div>
</div> --}}

{{-- Modal Add Balance --}}
<form method="POST" action="/medical/manage/add_balance" enctype="multipart/form-data" >
@csrf
    <div class="modal fade" id="balanceModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="balanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title  text-white" id="staticBackdropLabel">Add Balance For Employee</h5>
                    <button type="button" class="close" style="color: white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-start">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="position_id">Name Employee:</label>
                            <select name="input_user_id" class="form-control" id="user_id" required>
                                <option value="">Choose..</option>
                                @foreach($user as $us)
                                <option value="{{ $us->id }}">{{ $us->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Input Balance :</label>
                            <input class="form-control flex" name="input_balance" id="input_balance" placeholder="Balance..." oninput="formatAmount(this)" required/>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Active Periode :</label>
                                    <input class="form-control" type="date"  name="input_active_periode" id="active_periode" value="" required/>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Expiration :</label>
                                    <input class="form-control" type="date"  name="input_expiration" id="expiration" value="" required/>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sm btn-success">Save Changes</button>
                    {{-- <input type="submit" class="btn btn-primary btn-sm" value="Yes Im Sure" id="btn-submit"> --}}
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Modal Add Balance --}}
@foreach ($medBalance as $mB)
<form method="POST" action="/medical/manage/edit_balance/{{ $mB->id }}" enctype="multipart/form-data" >
@method('PUT')
@csrf
    <div class="modal fade" id="balanceEditModal{{ $mB->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="balanceEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title  text-white" id="staticBackdropLabel">Edit {{ $mB->user->name }} Balance</h5>
                    <button type="button" class="close" style="color: white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-start">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="password">Edit Balance (Rp.)</label>
                            <input class="form-control flex" name="input_edit_balance" id="input_balance" placeholder="Balance..." value="{{ $mB->medical_balance }}"  oninput="formatAmount(this)" required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sm btn-success">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endforeach

<script>
function toggleCheckboxes() {
    var checkboxes = document.getElementsByClassName('data-checkbox');
    var checkAllCheckbox = document.getElementById('checkAll');

    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = checkAllCheckbox.checked;
    }
}

function formatAmount(input) {
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
