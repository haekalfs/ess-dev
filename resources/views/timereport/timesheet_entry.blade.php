@extends('layouts.main')

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Timesheet Entry</h1>
<p class="mb-4">Timesheet Entry for {{ date("F", mktime(0, 0, 0, $month, 1)) }} - {{$year}}</a>. <small style="color: red;"><u><i>This app is still under development. You may find fault on inputs</i></u></small></p>
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
{{-- <form method="POST" action="{{ route('save_activities') }}">
    @csrf
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary" id="judul">Timesheet Entry</h6>
            <div class="text-right">
                <button type="submit" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-save fa-sm text-white-50"></i> Save Activities</button>
            </div>
        </div>
        <div class="card-body zoom80">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Activity</th>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Date</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Activity</th>
                        </tr>
                    </tfoot>
                    <tbody>                 
                        @foreach ($dates as $date)
                            <tr>
                                <td>{{ $date->format('Y-m-d') }}</td>
                                <td><input class="form-control" type="time" name="activities[{{ $date->format('Y-m-d') }}][from]" value="{{ $savedActivities->firstWhere('ts_date', $date->format('Y-m-d'))->ts_from_time ?? '' }}" ></td>
                                <td><input class="form-control" type="time" name="activities[{{ $date->format('Y-m-d') }}][to]" value="{{ $savedActivities->firstWhere('ts_date', $date->format('Y-m-d'))->ts_to_time ?? '' }}" ></td>
                                <td><textarea class="form-control" rows="2" name="activities[{{ $date->format('Y-m-d') }}][activity]" rows="1" >{{ $savedActivities->firstWhere('ts_date', $date->format('Y-m-d'))->ts_activity ?? '' }}</textarea></td>
                                <!-- Add more activity columns as needed -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form> --}}
<div class="alert alert-success" role="alert" style="display: none;">
    Your entry has been saved successfully.
  </div>
  
  <div class="alert alert-danger" role="alert" style="display: none;">
    An error occurred while saving your entry. Please try again.
  </div>
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Calendar</h6>
                {{-- <div class="text-right">
                    <input class="btn btn-primary btn-sm" type="button" id="copyButton" value="Reset">
                </div> --}}
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <table class="table zoom80 table-bordered calendar">
                    <colgroup>
                        @foreach ($calendar[0] as $dayName)
                          <col style="width: {{ 100 / count($calendar[0]) }}%;">
                        @endforeach
                      </colgroup>
                    <thead class="thead-dark">
                        @foreach ($calendar[0] as $dayName)
                            <th>{{ $dayName }}</th>
                        @endforeach
                    </thead>
                    <tbody>
                        @foreach (array_slice($calendar, 1) as $week)
                            <tr>
                                @foreach ($week as $day)
                                    @if ($day !== '' && date('N', strtotime($year.'-'.$month.'-'.$day)) == 7)
                                        <td style="color: red">{{ $day }}</td>
                                    @else
                                    <td data-toggle="modal" class="clickable" data-target="#myModal" data-date="{{ $year }}-{{ $month }}-{{ $day }}">{{ $day }}</td>

                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-6">
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Allowances Calculation</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <table class="zoom80">
                            {{-- <thead>
                                <tr class="calculations">
                                </tr>
                            </thead> --}}
                            <tbody class="calculations">
                            </tbody>
                        </table><small class="text-danger zoom80"><u><i>For exact calculations, request payslip from Finances Department.</i></u></small>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Leaves Calculation</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <table class="zoom80">
                            {{-- <thead>
                                <tr class="calculations">
                                </tr>
                            </thead> --}}
                            <tbody class="calculations">
                            </tbody>
                        </table><small class="text-danger zoom80"><u><i>For exact calculations, request payslip from Finances Department.</i></u></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow mb-4">
    <!-- Card Header - Dropdown -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Activity Entries</h6>
        <div class="text-right">
            <button class="btn btn-secondary btn-sm" type="button" id="manButton" style="margin-right: 10px;">Preview</button><input class="btn btn-primary btn-sm" type="button" id="copyButton" value="Submit">
        </div>
    </div>
    <!-- Card Body -->
    <div class="card-body">
        <div class="table-responsive zoom80">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Task</th>
                        <th>Location</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Activity</th>
                        <th style="width: 10px;">Action</th>
                </thead>
                <tfoot>
                    <tr>
                        <th>Date</th>
                        <th>Task</th>
                        <th>Location</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Activity</th>
                        <th style="width: 10px;">Action</th>
                    </tr>
                </tfoot>
                <tbody id="activity-table">
                    <!-- Display the data fetched via AJAX here -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<input type="hidden" id="yearSel" value="{{ $year }}">
<input type="hidden" id="monthSel" value="{{ $month }}">
<script>
const year = {{ $year }};
const month = {{ $month }};
$(function() {
    $('#myModal').on('show.bs.modal', function (event) {
        var date = $(event.relatedTarget).data('date');
        var formattedDate = new Date(date).toLocaleDateString('en-US', { 
            day: 'numeric', 
            month: 'short', 
            year: 'numeric' 
        }).replace(',', '').split(' ');
        var month = formattedDate[0];
        var day = formattedDate[1];
        var year = formattedDate[2];
        var formattedDateStr = day + '-' + month + '-' + year;
        $('#selected-date-display').text(formattedDateStr);
    });
});
$(document).ready(function () {
    $('.clickable').click(function () {
        var clickedDate = $(this).data('date');
        var dateObj = new Date(clickedDate);
        var formattedDate = dateObj.getFullYear() + '-' + (dateObj.getMonth() + 1).toString().padStart(2, '0') + '-' + dateObj.getDate().toString().padStart(2, '0');
        $('#clickedDate').val(formattedDate);
    });
});



//this is my save function 
$(document).ready(function() {

$(document).on('click', '.delete-btn', function(event) {
    var activityId = $(event.target).data('id');
    deleteActivity(activityId);
});
function deleteActivity(activityId) {
    $.ajax({
        url: '/activities/' + activityId,
        type: 'DELETE',
        success: function(response) {
            fetchActivities(year, month);
        },
        error: function(response) {
            console.log(response);
        }
        });
    }
    var yearput = $('#yearSel').val();
    var monthput = $('#monthSel').val();
    // Fetch the activities when the page loads
    fetchActivities(yearput, monthput);
    
    // Function to fetch the activities via AJAX
function fetchActivities(year, month) {
    $.ajax({
        url: '/get-activities/' + year + '/' + month,
        type: 'GET',
        success: function(response) {
            // Clear the table body
            $('#activity-table').empty();
            // Check if the response is empty or null
            if (response.length === 0) {
                // Display a message to the user
                $('#activity-table').append($('<tr><td class="text-center" colspan="7">No data available in table.</td></tr>'));
                $('.calculations').text('No data available.');
            } else {
                // Create an object to store the counts for each location
                var counts = {
                    'HO': 0,
                    'Luar Kota': 0,
                    'Dalam Kota': 0,
                    'WFH': 0,
                    'Outer Ring' : 0
                };
                // Loop through the activities and append each row to the table
                $.each(response, function(index, activity) {
                    var row = $('<tr></tr>').attr('data-id', activity.ts_id);
                    row.append($('<td data-toggle="modal" class="clickable" data-target="#myModal"></td>').text(activity.ts_date));
                    row.append($('<td></td>').text(activity.ts_task));
                    row.append($('<td></td>').text(activity.ts_location));
                    row.append($('<td></td>').text(activity.ts_from_time));
                    row.append($('<td></td>').text(activity.ts_to_time));
                    row.append($('<td></td>').text(activity.ts_activity));
                    var actions = $('<td></td>');
                    actions.append($('<a></a>').addClass('btn-sm btn btn-danger delete-btn').text('Reset').attr('data-id', activity.ts_id));
                    row.append(actions);
                    $('#activity-table').append(row);
                    
                    // Increment the count for the location of this activity
                    counts[activity.ts_location] += 1;
                });
                // Add click handlers for the edit and delete buttons
                $('.delete-btn').click(deleteActivity);
                
                // Create a lookup table of rates for each location
                var rates = {
                    'HO': 70000,
                    'Luar Kota': 80000,
                    'Dalam Kota': 90000,
                    'WFH': 60000,
                    'Outer Ring' : 70000
                };

                // Create an object to store the total for each location
                var totals = {
                    'HO': 0,
                    'Luar Kota': 0,
                    'Dalam Kota': 0,
                    'WFH': 0,
                    'Outer Ring' : 0
                };
                // Update the card body with the counts for each location
                var cardBody = $('.calculations');
                cardBody.empty(); // Clear the card body
                var cardBodyTotals = $('.calculationTotals');
                cardBodyTotals.empty(); // Clear the card body
                $.each(counts, function(location, count) {
                // Calculate the result for the current location
                var result = counts[location] * rates[location];
                totals[location] = result;
                // Format the result as Indonesian Rupiah currency
                var formattedResult = result.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                // Create the count text with the formatted result
                var countText = location + ' : ' + count;
                    cardBody.append($('<tr>'));
                    cardBody.append($('<td></td>').text(countText));
                    cardBody.append($('<td width="30px" class="text-center"></td>').text(':'));
                    cardBody.append($('<td></td>').text(formattedResult));
                    cardBody.append($('</tr>'));
                    // cardBodyRates.append($('<td></td>').text(formattedResult));
                });
                // Calculate the overall total
                var overallTotal = 0;
                $.each(totals, function(location, total) {
                    overallTotal += total;
                });

                // Format the overall total as Indonesian Rupiah currency
                var formattedTotal = overallTotal.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                // Add the overall total to the card body
                var totalText = 'Overall total: ' + formattedTotal;
                cardBody.append($('<tr>'));
                    cardBody.append($('<td></td>').text('Overall total'));
                    cardBody.append($('<td width="30px" class="text-center"></td>').text(':'));
                    cardBody.append($('<td></td>').text(formattedTotal));
                cardBody.append($('</tr>'));
            }
        },
        error: function(response) {
            console.log(response);
        }
    });
}


  $('#save-entry').click(function(e) {
    e.preventDefault();
    // Serialize the form data
    var formData = $('#entry-form').serialize();
    // Send an AJAX request to the entries.store route
    $.ajax({
      type: 'POST',
      url: '/entries',
      data: formData,
      success: function(response) {
        $('.alert-success').show();
        $('#entry-form')[0].reset();
            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, 5000);
        // Fetch the updated list of activities
        fetchActivities(year, month);
      },
      error: function(jqXHR, textStatus, errorThrown) {
            $('.alert-danger').show();
            setTimeout(function() {
                $('.alert-danger').fadeOut('slow');
            }, 5000);
        }
    });
  });
});
// function deleteActivity() {
//     var activityId = $(this).data('id');
//     if (confirm("Are you sure you want to delete this activity?")) {
//         $.ajax({
//             type: 'DELETE',
//             url: '/activities/' + activityId,
//             success: function(response) {
//                 fetchActivities();
//             },
//             error: function(jqXHR, textStatus, errorThrown) {
//                 console.log(textStatus, errorThrown);
//             }
//         });
//     }
// }


</script>
<style>
    td {
        cursor: pointer;
        transition: background-color 0.3s ease-in-out;
    }
    td:hover {
        background-color: #f5f5f5;
    }
    td:active {
        background-color: #e6e6e6;
    }
</style>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="modalSign" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header border-bottom-1">
				<h5 class="modal-title m-0 font-weight-bold text-secondary" id="exampleModalLabel">Entry <a id="selected-date-display"></a></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" id="entry-form">
                @csrf
				<div class="modal-body" style="">
                    <input type="hidden" id="clickedDate" name="clickedDate">
                    <div class="col-md-12 zoom90">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Task :</label>
                                    <select class="form-control" id="task" name="task">
                                        <option>HO</option>
                                        <option>Project (JOB Tomori, PLN, LPS, Vale, etc...)</option>
                                        <option>Sick</option>
                                        <option>Other</option>
                                        <option>Standby</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Location :</label>
                                    <select class="form-control" id="location" name="location">
                                        <option value="Dalam Kota">Dalam Kota</option>
                                        <option value="Luar Kota">Luar Kota</option>
                                        <option value="HO">Head Office</option>
                                        <option value="Outer Ring">Outer Ring (Bogor, Depok, Tangerang, Bekasi)</option>
                                        <option value="WFH">WFH/WFA (Work From Home/Anywhere)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">From :</label>
                                    <input type="time" class="form-control" name="from" id="start-time">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">To :</label>
                                    <input type="time" class="form-control" name="to" id="end-time">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password">Activity :</label>
                                    <textarea type="text" class="form-control" id="activity" name="activity"></textarea>
                                </div>
                            </div>
                        </div>
				    </div>
                </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" id="save-entry" class="btn btn-primary" data-dismiss="modal">Save changes</button>
                  </div>
			</form>
		</div>
	</div>
</div>

  <style>
    .calendar {
  background: #ffffff;
  border-radius: 4px;
  height: 501px;
  perspective: 1000;
  transition: .9s;
  transform-style: preserve-3d;
  width: 100%;
}
</style>
@endsection