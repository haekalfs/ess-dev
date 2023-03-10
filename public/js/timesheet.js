
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

$(function() {
    $('#updateModal').on('show.bs.modal', function (event) {
        var activityId = $(event.relatedTarget).data('id');
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
        $.ajax({
            url: '/get-data/' + year + '/' + month + '/' + activityId,
            type: 'GET',
            success: function(response) {
                // Set the values of the form fields with the data received from the server
                $('#updateModal').find('#update_task').val(response.ts_task);
                $('#updateModal').find('#update_location').val(response.ts_location);
                $('#updateModal').find('#update_activity').val(response.ts_activity);
                $('#updateModal').find('#update_from-time').val(response.ts_from_time);
                $('#updateModal').find('#update_to-time').val(response.ts_to_time);
    
            },
            error: function(response, jqXHR, textStatus, errorThrown) {
                console.log(response);
            }
        });
        $('#entry-date-update').text(formattedDateStr);

        $('#update-entry').click(function(e) {
            e.preventDefault();
            var yearput = $('#yearSel').val();
            var monthput = $('#monthSel').val();
            // Serialize the form data
            var updateData = $('#update-form').serialize();
            // Send an AJAX request to the entries.store route
            $.ajax({
            type: 'POST',
            url: '/update-entries/' + activityId,
            data: updateData,
            success: function(response) {
                $('.alert-success').show();
                $('#update-form')[0].reset();
                    setTimeout(function() {
                        $('.alert-success').fadeOut('slow');
                    }, 3000);
                // Fetch the updated list of activities
                window.location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                    $('.alert-danger').show();
                    setTimeout(function() {
                        $('.alert-danger').fadeOut('slow');
                    }, 3000);
                }
            });
        });
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
            $('.alert-success-delete').show();
            setTimeout(function() {
                $('.alert-success-delete').fadeOut('slow');
            }, 3000);
            fetchActivities(yearput, monthput);
        },
        error: function(response,jqXHR, textStatus, errorThrown) {
            $('.alert-danger-delete').show();
            setTimeout(function() {
                $('.alert-danger-delete').fadeOut('slow');
            }, 3000);
            console.log(response);
        }
        });
    }
    var yearput = $('#yearSel').val();
    var monthput = $('#monthSel').val();
    // Fetch the activities when the page loads
    fetchActivities(yearput, monthput);
    
    // Function to fetch the activities via AJAX
function fetchActivities(yearput, monthput) {
    $.ajax({
        url: '/get-activities/' + yearput + '/' + monthput,
        type: 'GET',
        success: function(response) {
            // Clear the table body
            $('#activity-table').empty();
            // Check if the response is empty or null
            if (response.length === 0) {
                // Display a message to the user
                $('#activity-table').append($('<tr><td class="text-center" colspan="8">No data available in table.</td></tr>'));
                $('.calculations').text('No data available.');
            } else {
                // Create an object to store the counts for each location
                var counts = {
                    'HO': 0,
                    'LK': 0,
                    'DK': 0,
                    'WFH': 0,
                    'Outer Ring' : 0
                };
                // Loop through the activities and append each row to the table
                $.each(response, function(index, activity) {
                    var row = $('<tr></tr>').attr('data-id', activity.ts_id);
                    var date = new Date(activity.ts_date);
                    var options = { weekday: 'short' };
                    row.append($('<td></td>').text(date.toLocaleDateString('en-US', options)));
                    row.append($('<td data-toggle="modal" class="clickable" data-target="#updateModal"></td>').attr('data-date', activity.ts_date).attr('data-id', activity.ts_id).text(activity.ts_date));
                    row.append($('<td data-toggle="modal" class="clickable" data-target="#updateModal"></td>').attr('data-date', activity.ts_date).attr('data-id', activity.ts_id).text(activity.ts_task));
                    row.append($('<td data-toggle="modal" class="clickable" data-target="#updateModal"></td>').attr('data-date', activity.ts_date).attr('data-id', activity.ts_id).text(activity.ts_location));
                    row.append($('<td data-toggle="modal" class="clickable" data-target="#updateModal "></td>').attr('data-date', activity.ts_date).attr('data-id', activity.ts_id).text(activity.ts_activity));
                    row.append($('<td data-toggle="modal" class="clickable" data-target="#updateModal"></td>').attr('data-date', activity.ts_date).attr('data-id', activity.ts_id).text(activity.ts_from_time));
                    row.append($('<td data-toggle="modal" class="clickable" data-target="#updateModal"></td>').attr('data-date', activity.ts_date).attr('data-id', activity.ts_id).text(activity.ts_to_time));
                    var actions = $('<td></td>');
                    actions.append($('<a></a>').addClass('btn-sm btn btn-danger delete-btn').text('Reset').attr('data-id', activity.ts_id));
                    row.append(actions);
                    $('#activity-table').append(row);
                    
                    // Increment the count for the location of this activity
                    counts[activity.ts_location] += 1;

                    var day = date.getDate();
                    var taskEntry = $('#task_entry' + day);
                    taskEntry.addClass('border-bottom-primary');
                });
                // Add click handlers for the edit and delete buttons
                $('.delete-btn').click(deleteActivity);
                
                // Create a lookup table of rates for each location
                var rates = {
                    'HO': 70000,
                    'LK': 200000,
                    'DK': 115000,
                    'WFH': 45000,
                    'Outer Ring' : 140000
                };

                // Create an object to store the total for each location
                var totals = {
                    'HO': 0,
                    'LK': 0,
                    'DK': 0,
                    'WFH': 0,
                    'Outer Ring' : 0
                };
                // Update the card body with the counts for each location
                var cardBody = $('.calculations');
                cardBody.empty(); // Clear the card body
                var clickable = $('.clickable2');
                clickable.empty(); // Clear the card body
                // cardBody.append($('<td><h6 class="m-0 font-weight-bold text-primary">Leaves Calculation</h6></td>'));
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
                    cardBody.append($('<td></td>').text(location));
                    cardBody.append($('<td width="30px" class="text-center"></td>').text(':'));
                    cardBody.append($('<td></td>').text(count + ' Days'));
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
                    cardBody.append($('<td></td>').text('Estimated Total'));
                    cardBody.append($('<td width="30px" class="text-center"></td>'));
                    cardBody.append($('<td></td>'));
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
            document.getElementById("activity").removeAttribute("readonly");
            document.getElementById("location").removeAttribute("readonly");
            document.getElementById("start-time").removeAttribute("readonly");
            document.getElementById("end-time").removeAttribute("readonly");
            $('#entry-form')[0].reset();
                setTimeout(function() {
                    $('.alert-success').fadeOut('slow');
                }, 3000);
            // Fetch the updated list of activities
            fetchActivities(yearput, monthput);
        },
        error: function(jqXHR, textStatus, errorThrown) {
                $('.alert-danger').show();
                setTimeout(function() {
                    $('.alert-danger').fadeOut('slow');
                }, 3000);
            }
        });
    });
});



