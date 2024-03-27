@extends('layouts.main')

@section('title', 'Performance Metrics - ESS')

@section('active-page-perform.metrics')
active
@endsection

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 zoom90 text-gray-800 font-weight-bold"><i class="fas fa-chart-line"></i> Employees Performances Metrics</h1>
        <p class="mb-3">Manage Users Account.</p>
    </div>
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
        <h6 class="m-0 font-weight-bold text-primary" id="judul">Filter Employees</h6>
        {{-- <div class="text-right">
            <a class="d-none d-sm-inline-block btn btn-secondary btn-sm shadow-sm" type="button" href="/timesheet/review/fm/export/{{ $Month }}/{{ $Year }}"><i class="fas fa-fw fa-download fa-sm text-white-50"></i> Export All (XLS)</a>
        </div> --}}
    </div>
    <form method="GET" action="/management/emp-key-performance-indicator">
        @csrf
    <div class="card-body">
        <div class="col-md-12 zoom90">
            <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="email">Employee :</label>
                            <select class="form-control" name="showOpt" required>
                                <option selected disabled>Select Employee...</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" @if ($emp->id == ($userSelected->id ?? 20)) selected @endif>{{ $emp->name }}</option>
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
                </form>
                <div class="col-md-12"><br>
                    @if($userSelected)
                    <div class="row">
                        <div class="col-md-3 align-items-center text-center">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr class="table-sm text-center">
                                        <td style="d-flex align-items-center text-center">
                                            @if($userSelected->users_detail->profile_pic)
                                                <img class="img-profile rounded-circle" height="150px"width="140px" style="object-fit:fill;" src="{{ url('/images_storage/'.$userSelected->users_detail->profile_pic) }}" data-toggle="modal" data-target="#profileModal">
                                            @else
                                                <div class="img-profile rounded-circle no-image"><i class="no-image-text">No Image Available</i></div>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-12">
                                    <h1 class="m-0 font-weight-bold text-dark">[{{$userSelected->users_detail->employee_id}}] {{$userSelected->name}}</h1>
                                    <hr class="sidebar-divider">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr class="table-sm">
                                                <td style="width: 150px;">Department</td>
                                                <td style="width: 300px;">: @if($userSelected->users_detail->department_id)
                                                        {{ $userSelected->users_detail->department->department_name }}
                                                    @endif
                                                </td>
                                                <td style="width: 200px;">Employment Status</td>
                                                <td>: {{$userSelected->users_detail->employee_status}}</td>
                                            </tr>
                                            <tr class="table-sm">
                                                <td style="width: 150px;">Position</td>
                                                <td style="width: 300px;">: @if($userSelected->users_detail->position_id)
                                                        {{ $userSelected->users_detail->position->position_name }}
                                                    @endif
                                                </td>
                                                <td style="width: 150px;">Status Active</td>
                                                <td style="width: 200px;">: {{$userSelected->users_detail->status_active}}</td>
                                            </tr>
                                            <tr class="table-sm">
                                                <td style="width: 200px;">Hired Date</td>
                                                <td>: {{ \Carbon\Carbon::createFromFormat('Y-m-d', $userSelected->users_detail->hired_date)->format('d-M-Y') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if($savedEvaluation)
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="h5 m-0 font-weight-bold text-primary mt-4 mb-4"><i class="fas fa-user"></i> Employee's Evaluation Form</h6>
                                        <hr class="sidebar-divider mb-4">
                                        <div class="row">
                                            <div class="col-md-12">
                                                @php $no = 1; @endphp
                                                @foreach($savedEvaluation as $savedquest)
                                                    <label>{{ $no++ . '. ' . $savedquest->question->question }} :</label><br>
                                                    <div class="mb-3 mt-2">
                                                        <label class="col-md-3">
                                                            <input class="form-radio-input" type="radio" name="question_mark[{{ $savedquest->question_id }}]" value="25" {{ $savedquest->q_value == 25 ? 'checked' : 'disabled' }}>
                                                            <span class="form-radio-sign">Sangat Baik</span>
                                                        </label>
                                                        <label class="col-md-2">
                                                            <input class="form-radio-input" type="radio" name="question_mark[{{ $savedquest->question_id }}]" value="12" {{ $savedquest->q_value == 12 ? 'checked' : 'disabled' }}>
                                                            <span class="form-radio-sign">Baik</span>
                                                        </label>
                                                        <label class="col-md-2">
                                                            <input class="form-radio-input" type="radio" name="question_mark[{{ $savedquest->question_id }}]" value="7" {{ $savedquest->q_value == 7 ? 'checked' : 'disabled' }}>
                                                            <span class="form-radio-sign">Cukup</span>
                                                        </label>
                                                        <label class="col-md-4">
                                                            <input class="form-radio-input" type="radio" name="question_mark[{{ $savedquest->question_id }}]" value="1" {{ $savedquest->q_value == 1 ? 'checked' : 'disabled' }}>
                                                            <span class="form-radio-sign">Sangat Kurang</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <form method="POST" action="{{ route('save.evaluation', ['id' => $userSelected->id, 'month' => $Month, 'year' => $Year]) }}">
                                @csrf
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="h5 m-0 font-weight-bold text-primary mt-2"><i class="fas fa-user"></i> Employee's Evaluation Form</h6>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                                <div class="col-md-12">
                                                    <hr class="sidebar-divider mb-4">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    @php $no = 1; @endphp
                                                    @foreach($question as $quest)
                                                        <label>{{ $no++ . '. ' . $quest->question }} :</label><br>
                                                        <div class="mb-3 mt-2">
                                                            <label class="col-md-3">
                                                                <input class="form-radio-input" type="radio" name="question_mark[{{ $quest->id }}]" value="25">
                                                                <span class="form-radio-sign">Sangat Baik</span>
                                                            </label>
                                                            <label class="col-md-2">
                                                                <input class="form-radio-input" type="radio" name="question_mark[{{ $quest->id }}]" value="12">
                                                                <span class="form-radio-sign">Baik</span>
                                                            </label>
                                                            <label class="col-md-2">
                                                                <input class="form-radio-input" type="radio" name="question_mark[{{ $quest->id }}]" value="7">
                                                                <span class="form-radio-sign">Cukup</span>
                                                            </label>
                                                            <label class="col-md-4">
                                                                <input class="form-radio-input" type="radio" name="question_mark[{{ $quest->id }}]" value="1">
                                                                <span class="form-radio-sign">Sangat Kurang</span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($userSelected)
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary" id="judul">Evaluation Chart</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <canvas id="chBar" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endif
<style>
.action{
    width: 190px;
}
.invisible {
    border-top: none;
    border-bottom: none;
}
</style>
<script>// chart colors
    var colors = ['#007bff','#28a745','#444444','#c3e6cb','#dc3545','#6c757d'];

    var savedEvaluation = @json($allEvaluation);
    var labels = [];
    var datasetsValue = [];
    savedEvaluation.forEach(function(item) {
        labels.push(item.month);
        datasetsValue.push({
            data: [item.q_value], // Assuming q_value is a single value, adjust as needed
            backgroundColor: colors[0] // You can define this function to generate random colors
        });
    });

    var chBar = document.getElementById("chBar");
    var chartData = {
        labels: labels,
        datasets: datasetsValue
    };

    if (chBar) {
        new Chart(chBar, {
            type: 'bar',
            data: chartData,
            options: {
                scales: {
                    xAxes: [{
                        barPercentage: 0.4,
                        categoryPercentage: 0.5
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                },
                legend: {
                    display: false
                }
            }
        });
    }
</script>
@endsection
