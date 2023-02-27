<!DOCTYPE html>
<html>
<head>
	<title>Timesheet</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style>
<img src="{{ public_path('img/PC-01.png') }}" style="height: 42px; width: 120px;" />
<div><br></div>
<table class='table table-bordered table-sm'>
    <thead>
        <tr>
            <th>Period</th>
            <th>Year</th>
            <th>Name</th>
            <th>NIK</th>
    </thead>
    <tbody>
        <tr>
            <td style="width: 50px;">{{date("F", mktime(0, 0, 0, $month, 1))}}</td>
            <td style="width: 50px;">{{$year}}</td>
            <td style="width: 300px;">{{$user_info->name}}</td>
            <td style="width: 50px;">{{$user_info_emp_id}}</td>
        </tr>
    </tbody>
</table>
	<table class='table table-bordered table-sm'>
		<thead>
            <tr>
                <th style="width: 10px;">Day</th>
                <th>Date</th>
                <th>Task</th>
                <th>Location</th>
                <th style="width: 300px;">Activity</th>
                <th>From</th>
                <th>To</th>
                <th>Hours</th>
        </thead>
        <tbody>
            <?php $total_work_hours = 0; ?>
            @foreach($timesheet as $timesheets)
            <tr>
                <td>{{ \Carbon\Carbon::parse($timesheets->ts_date)->format('D') }}</td>
                <td>{{ \Carbon\Carbon::parse($timesheets->ts_date)->format('d-M-Y') }}</td>
                <td>{{ $timesheets->ts_task }}</td>
                <td>{{ $timesheets->ts_location }}</td>
                <td>{{ $timesheets->ts_activity }}</td>
                <td>{{ $timesheets->ts_from_time }}</td>
                <td>{{ $timesheets->ts_to_time }}</td>
                <td>
                    <?php 
                    $start_time = strtotime($timesheets->ts_from_time);
                    $end_time = strtotime($timesheets->ts_to_time);
                    $time_diff_seconds = $end_time - $start_time;
                    $time_diff_hours = gmdate('H', $time_diff_seconds);
                    $time_diff_minutes = substr(gmdate('i', $time_diff_seconds), 0, 2);
                    $total_work_hours += ($time_diff_hours + ($time_diff_minutes / 60)); echo $time_diff_hours.':'.$time_diff_minutes;
                    ?>
                </td>
            </tr>
            @endforeach
        </tbody>
	</table>
    <table class="table table-bordered">
        <tbody>
            <tr class="table-sm">
                <td class="m-0 font-weight-bold text-danger" width="600px">Total Workhours</td>
                <td class="text-center"><?php echo intval($total_work_hours); ?> Hours</td>
            </tr>
        </tbody>
    </table>
 
</body>
</html>