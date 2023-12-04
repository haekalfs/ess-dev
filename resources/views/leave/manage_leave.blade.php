@extends('leave.manage_leave_layout')

@section('manage-leave-table')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Leaves Information</h6>
        <div class="text-right">
            <a class="d-none d-sm-inline-block btn btn-primary btn-sm shadow-sm" type="button" data-toggle="modal" data-target="#addLeaveModal" ><i class="fas fa-fw fa-plus fa-sm text-white-50"></i> Add Leave</a>
        </div>
    </div>
    <div class="card-body">
    <form method="GET" action="/leave/manage/all">
        @csrf
        <div class="col-md-12 zoom90">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="email">Show :</label>
                        <select class="form-control" name="showOpt" required>
                            <option value="1">All</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}" @if ($emp->id == $showName) selected @endif>{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="password">Year :</label>
                        <select class="form-control" name="yearOpt" required>
                            @foreach ($yearsBefore as $year)
                                <option value="{{ $year }}" @if ($year == $Year) selected @endif>{{ $year }}</option>
                            @endforeach
                        </select>                            
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="password">Limit :</label>
                        <select class="form-control" name="limitOpt" required>
                            @foreach ($limitShown as $value)
                                @if($value == "All")
                                <option value="1000" @if ($value == $limit) selected @endif>{{ $value }}</option>
                                @else
                                <option value="{{ $value }}" @if ($value == $limit) selected @endif>{{ $value }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-1 d-flex justify-content-center align-items-end">
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Display">
                    </div>
                </div>
                <div class="col-md-12"><br>
                    <div class="table-responsive">
                        <table class="table table-bordered zoom90" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Emp ID</th>
                                    <th>Name</th>
                                    <th>Leave ID</th>
                                    <th>Quota Left</th>
                                    <th>Active Periode</th>
                                    <th>Expiration</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emp_leave_quota as $index => $leave_quota)
                                <tr>
                                    @if ($index > 0 && $leave_quota->user->name === $emp_leave_quota[$index-1]->user->name)
                                    <td style="border-bottom: none; border-top: none;"></td>
                                    <td style="border-bottom: none; border-top: none;"></td>
                                    <td style="border-bottom: none; border-top: none;">{{ $leave_quota->leave->description }}</td>
                                    <td style="border-bottom: none; border-top: none;">{{ $leave_quota->quota_left }}</td>
                                    <td style="border-bottom: none; border-top: none;">{{ $leave_quota->active_periode }}</td>
                                    <td style="border-bottom: none; border-top: none;">{{ $leave_quota->expiration }}</td>
                                    <td style="border-bottom: none; border-top: none;"></td>
                                    @else
                                    <td style="border-bottom: none; border-top: none;">{{ $leave_quota->user->users_detail->employee_id }}</td>
                                    <td style="border-bottom: none; border-top: none;">{{ $leave_quota->user->name }}</td>
                                    <td style="border-bottom: none; border-top: none;">{{ $leave_quota->leave->description }}</td>
                                    <td style="border-bottom: none; border-top: none;">{{ $leave_quota->quota_left }}</td>
                                    <td style="border-bottom: none; border-top: none;">{{ $leave_quota->active_periode }}</td>
                                    <td style="border-bottom: none; border-top: none;">{{ $leave_quota->expiration }}</td>
                                    <td style="border-bottom: none; border-top: none;" class="action text-center">
                                        <a href="/leave/manage/{{ $leave_quota->user_id }}" class="btn btn-primary btn-sm"><i class="fas fa-fw fa-mouse-pointer fa-sm text-white-50"></i> Select</a>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td colspan="7" class="text-center">Copyright @ Author of ESS Perdana Consulting</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>

{{-- Modal Add Leave --}}
<form action="/medical/edit/" enctype="multipart/form-data" method="POST">
@csrf
@method('PUT')
    <div class="modal fade" id="addLeaveModal" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Add Leave To Employee</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12" >
                        <div class="form-group">
                            <label for="email">Employee Name :</label>
                            <select class="form-control" name="input_emp_id" required>
                                <option selected disabled>Choose</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" @if ($emp->id == $showName) selected @endif>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="email">Leave ID :</label>
                            <select class="form-control" name="input_leave_id" required>
                                <option selected disabled>Choose</option>
                                <option value="10">Annual Leave</option>
                                <option value="20">5 Year Term</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Quota Used :</label>
                                    <input class="form-control flex" type="number" name="input_quota_used" placeholder="Quota Used..." value="0"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Quota Left :</label>
                                    <input class="form-control flex" type="number" name="input_quota_left" placeholder="Quota Left..." value="0"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Active Periode :</label>
                                    <input class="form-control" type="date"  name="input_active_periode" id="active_periode" value="" required/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Expiration :</label>
                                    <input class="form-control" type="date"  name="input_expiration" id="expiration" value="" required/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm btn-danger" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button class="btn-sm btn-success">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Mendapatkan elemen input tanggal
var inputExpiration = document.getElementById('expiration');

// Mendapatkan tanggal saat ini
var currentDate = new Date();

// Menentukan tanggal 1 bulan April tahun selanjutnya
var nextYear = currentDate.getFullYear() + 1;
var nextApril = new Date(nextYear, 3, 1); // April memiliki index bulan 3 (dimulai dari 0)

// Mengatur nilai input tanggal menjadi tanggal 1 bulan April tahun berikutnya
var formattedDate = nextApril.getFullYear() + '-' + ('0' + (nextApril.getMonth() + 1)).slice(-2) + '-' + ('0' + nextApril.getDate()).slice(-2);
inputExpiration.value = formattedDate;

</script>
@endsection