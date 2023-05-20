@extends('layouts.main')

@section('title', 'Review by Finance Manager - ESS')

@section('active-page-timesheet')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-2 text-gray-800">Timesheet Review <small style="color: red;"><i> &nbsp;&nbsp;Finance Manager</i></small></h1>
    {{-- <a class="d-none d-sm-inline-block btn btn-secondary btn-sm shadow-sm" type="button" href="/timesheet/review/fm/export"><i class="fas fa-fw fa-download fa-sm text-white-50"></i> Export All (XLS)</a> --}}
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
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Filter Timesheets</h6>
        <div class="text-right">
            <a class="d-none d-sm-inline-block btn btn-secondary btn-sm shadow-sm" type="button" href="/timesheet/review/fm/export/{{ $Month }}/{{ $Year }}"><i class="fas fa-fw fa-download fa-sm text-white-50"></i> Export All (XLS)</a>
        </div>
    </div>
    <form method="GET" action="/timesheet/review/fm">
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
                                        <th>Project</th>
                                        <th>Location</th>
                                        <th>Role</th>
                                        <th>Mandays</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvals as $index => $approval)
                                    <tr>
                                        @if ($index > 0 && $approval->user->name === $approvals[$index-1]->user->name)
                                        <td style="border-bottom: none; border-top: none;"></td>
                                        <td style="border-bottom: none; border-top: none;"></td>
                                        <td style="border-bottom: none; border-top: none;">{{ $approval->ts_task }}</td>
                                        <td style="border-bottom: none; border-top: none;">{{ $approval->ts_location }}</td>
                                        <td style="border-bottom: none; border-top: none;">{{ $approval->roleAs }}</td>
                                        <td style="border-bottom: none; border-top: none;">{{ $approval->ts_mandays }}</td>
                                        <td style="border-bottom: none; border-top: none;"></td>
                                        @else
                                        <td style="border-bottom: none; border-top: none;">{{ $approval->user->users_detail->employee_id }}</td>
                                        <td style="border-bottom: none; border-top: none;">{{ $approval->user->name }}</td>
                                        <td style="border-bottom: none; border-top: none;">{{ $approval->ts_task }}</td>
                                        <td style="border-bottom: none; border-top: none;">{{ $approval->ts_location }}</td>
                                        <td style="border-bottom: none; border-top: none;">{{ $approval->roleAs }}</td>
                                        <td style="border-bottom: none; border-top: none;">{{ $approval->ts_mandays }}</td>
                                        <td style="border-bottom: none; border-top: none;" class="action text-center">
                                            <a href="/timesheet/review/fm/review/{{ $approval->user_timesheet }}/{{ $Year }}/{{ $Month }}" class="btn btn-primary btn-sm"><i class="fas fa-fw fa-eye fa-sm text-white-50"></i> View</a>
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
</style>
@endsection
