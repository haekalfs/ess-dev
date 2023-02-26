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
	<center>
		<h5>Timesheet</h4>
		<h6><a target="_blank" href="https://www.malasngoding.com/membuat-laporan-…n-dompdf-laravel/">www.ess.perdana.co.id</a></h5>
	</center>
 
	<table class='table table-bordered'>
		<thead>
            <tr>
                <th>Date</th>
                <th>Task</th>
                <th>Location</th>
                <th>From</th>
                <th>To</th>
                <th>Activity</th>
        </thead>
        <tbody>
            @foreach($timesheet as $timesheets)
            <tr>
                <td>{{ $timesheets->ts_date }}</td>
                <td>{{ $timesheets->ts_task }}</td>
                <td>{{ $timesheets->ts_location }}</td>
                <td>{{ $timesheets->ts_from_time }}</td>
                <td>{{ $timesheets->ts_to_time }}</td>
                <td>{{ $timesheets->ts_activity }}</td>
            </tr>
            @endforeach
        </tbody>
	</table>
 
</body>
</html>