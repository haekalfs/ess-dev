@extends('leave.manage_leave_layout')

@section('manage-leave-user-info')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Filter Requests</h6>
        {{-- <div class="text-right">
            <a class="d-none d-sm-inline-block btn btn-secondary btn-sm shadow-sm" type="button" href="/timesheet/review/fm/export/{{ $Month }}/{{ $Year }}"><i class="fas fa-fw fa-download fa-sm text-white-50"></i> Export All (XLS)</a>
        </div> --}}
    </div>
    <form method="GET" action="/leave/request/manage/all">
        @csrf
        <div class="card-body">
            <div class="col-md-12 zoom90">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="email">Employee :</label>
                            <select class="form-control" name="showOpt" required>
                                <option value="1">All</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="password">Year :</label>
                            <select class="form-control" name="yearOpt" required>
                                @foreach (array_reverse($yearsBefore) as $year)
                                    <option value="{{ $year }}" @if ($year == $Year) selected @endif>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="password">Month :</label>
                            <select class="form-control" name="monthOpt" required>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}" @if ($month == $Month) selected @endif>{{ date("F", mktime(0, 0, 0, $month, 1)) }}</option>
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
                            <table class="table table-bordered zoom90" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Emp ID</th>
                                        <th>Name</th>
                                        <th>Leave Type</th>
                                        <th>Days on Leave</th>
                                        <th>Date Created</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($approvals->isEmpty())
                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                            <td colspan="6" class="text-center"><a><i>No Data Available</i></a></td>
                                        </tr>
                                    @else
                                        @foreach($approvals as $index => $approval)
                                        <tr>
                                            @if ($index > 0 && $approval->user->name === $approvals[$index-1]->user->name)
                                            <td style="border-bottom: none; border-top: none;"></td>
                                            <td style="border-bottom: none; border-top: none;"></td>
                                            <td style="border-bottom: none; border-top: none;">{{ $approval->leave->description }}</td>
                                            <td style="border-bottom: none; border-top: none;">{{ $approval->total_days }} Days</td>
                                            <td style="border-bottom: none; border-top: none;">{{ $approval->created_at->format('d-M-Y H:m') }}</td>
                                            <td style="border-bottom: none; border-top: none;"></td>
                                            @else
                                            <td style="border-bottom: none; border-top: none;">{{ $approval->user->users_detail->employee_id }}</td>
                                            <td style="border-bottom: none; border-top: none;">{{ $approval->user->name }}</td>
                                            <td style="border-bottom: none; border-top: none;">{{ $approval->leave->description }}</td>
                                            <td style="border-bottom: none; border-top: none;">{{ $approval->total_days }} Days</td>
                                            <td style="border-bottom: none; border-top: none;">{{ $approval->created_at->format('d-M-Y H:m') }}</td>
                                            <td style="border-bottom: none; border-top: none;" class="action text-center">
                                                <a href="/leave/request/manage/id/{{ $approval->req_by }}/{{ $Month }}/{{ $Year }}" class="btn btn-primary btn-sm"><i class="fas fa-fw fa-eye fa-sm text-white-50"></i> Manage</a>
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    @endif
                                    <tr style="border-bottom: 1px solid #dee2e6;">
                                        <td colspan="6" class="text-center">Copyright @ Author of ESS Perdana Consulting</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
{{-- <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Approved Timesheets</h6>
        <div class="text-right">
            <a class="btn btn-secondary btn-sm" type="button" href="#" id="manButton"><i class="fas fa-fw fa-download fa-sm text-white-50"></i> Export Selected</a>
        </div>
    </div>
    <div class="card-body">
        
    </div>
</div> --}}
<style>
.action{
    width: 190px;
}
.invisible {
    border-top: none;
    border-bottom: none;
}
</style>
@endsection
