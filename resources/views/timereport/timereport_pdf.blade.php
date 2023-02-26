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
        </thead>
        <tbody>
            @foreach($timesheet as $timesheets)
            <tr>
                <td>{{ \Carbon\Carbon::parse($timesheets->ts_date)->format('D') }}</td>
                <td>{{ \Carbon\Carbon::parse($timesheets->ts_date)->format('d-M-Y') }}</td>
                <td>{{ $timesheets->ts_task }}</td>
                <td>{{ $timesheets->ts_location }}</td>
                <td>{{ $timesheets->ts_activity }}</td>
                <td>{{ $timesheets->ts_from_time }}</td>
                <td>{{ $timesheets->ts_to_time }}</td>
            </tr>
            @endforeach
        </tbody>
	</table>
 
</body>
</html>