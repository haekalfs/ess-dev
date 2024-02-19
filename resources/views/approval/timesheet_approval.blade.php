@extends('layouts.main')

@section('title', 'Timesheet Approval - ESS')

@section('active-page-approval')
active
@endsection

@section('content')
<h1 class="h3 mb-2 zoom90 font-weight-bold text-gray-800"><i class="fas fa-calendar"></i> Timesheet Approval</h1>
<p class="zoom90 mb-4">Approval Page.</p>
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
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Filter Timesheets Approvals</h6>
        {{-- <div class="text-right">
            <a class="d-none d-sm-inline-block btn btn-secondary btn-sm shadow-sm" type="button" href="/timesheet/review/fm/export/{{ $Month }}/{{ $Year }}"><i class="fas fa-fw fa-download fa-sm text-white-50"></i> Export All (XLS)</a>
        </div> --}}
    </div>
    <form method="GET" action="/approval/timesheet/p">
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
                            @if ($notify)
                                <label for="password">Year<span class="position-absolute top-0 start-100 translate-middle badge bg-danger text-white">!</span> :</label>
                            @else
                                <label for="password">Year :</label>
                            @endif
                            <select class="form-control" name="yearOpt" required>
                                @foreach (array_reverse($yearsBefore) as $year)
                                    <option value="{{ $year }}" @if ($year == $Year) selected @endif>{{ $year }}
                                        @if(in_array($year, $notifyYear))
                                            &#x2757; <!-- Display the warning icon if $month is in $notifyMonth -->
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            @if ($notify)
                                <label for="password">Month<span class="position-absolute top-0 start-100 translate-middle badge bg-danger text-white">!</span> :</label>
                            @else
                                <label for="password">Month :</label>
                            @endif
                            <select class="form-control" name="monthOpt" required>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ $month }}" @if ($month == $Month) selected @endif>
                                        {{ date("F", mktime(0, 0, 0, $month, 1)) }}
                                        @if(in_array($month, $notifyMonth))
                                            &#x2757; <!-- Display the warning icon if $month is in $notifyMonth -->
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex justify-content-center align-items-end">
                        <div class="form-group">
                            @if ($notify)
                                <button type="submit" class="btn btn-primary position-relative">
                                    Display
                                    <span class="position-absolute top-0 start-100 translate-middle badge bg-danger">!</span>
                                </button>
                            @else
                                <input type="submit" class="btn btn-primary" value="Display">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12"><br>
                        <div class="table-responsive">
                            <table class="table table-bordered zoom90" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Request Date</th>
                                        <th>Timesheet Periode</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($approvals->isEmpty())
                                        <tr style="border-bottom: 1px solid #dee2e6;">
                                            <td colspan="4" class="text-center"><a><i>No Data Available</i></a></td>
                                        </tr>
                                    @else
                                        @foreach($approvals as $index => $approval)
                                            <tr>
                                                @if ($index > 0 && $approval->user->name === $approvals[$index-1]->user->name)
                                                <td style="border-bottom: none; border-top: none;"></td>
                                                <td style="border-bottom: none; border-top: none;">{{ $approval->date_submitted }}</td>
                                                <td style="border-bottom: none; border-top: none;">{{ date("F", mktime(0, 0, 0, substr($approval->month_periode, 4, 2), 1)) }} - {{ substr($approval->month_periode, 0, 4) }}</td>
                                                <td style="border-bottom: none; border-top: none;"></td>
                                                @else
                                                <td style="border-bottom: none; border-top: none;">{{ $approval->user_timesheet }}</td>
                                                <td style="border-bottom: none; border-top: none;">{{ $approval->date_submitted }}</td>
                                                <td style="border-bottom: none; border-top: none;">{{ date("F", mktime(0, 0, 0, substr($approval->month_periode, 4, 2), 1)) }} - {{ substr($approval->month_periode, 0, 4) }}</td>
                                                <td  style="border-bottom: none; border-top: none;" class="action text-center">
                                                    <a href="/approval/timesheet/preview/{{ Crypt::encrypt($approval->user_timesheet) }}/{{ Crypt::encrypt(substr($approval->month_periode, 0, 4)) }}/{{ Crypt::encrypt(substr($approval->month_periode, 4, 2)) }}" class="btn btn-secondary btn-sm" style="margin-left: 3%;"><i class="fas fa-fw fa-eye fa-sm text-white-50"></i> Preview</a>
                                                </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr style="border-bottom: 1px solid #dee2e6;">
                                        <td colspan="4" class="text-center">Copyright @ Author of ESS Perdana Consulting</td>
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
<style>
.action{
    width: 300px;
}
.invisible {
    border-top: none;
    border-bottom: none;
}
</style>
@endsection
