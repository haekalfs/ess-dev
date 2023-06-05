
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
    $('#redModal').on('show.bs.modal', function (event) {
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
        $('#selected-date-display-red').text(formattedDateStr);
    });
});


$(document).ready(function () {
    $('.clickable').click(function () {
        var clickedDate = $(this).data('date');
        var dateObj = new Date(clickedDate);
        var formattedDate = dateObj.getFullYear() + '-' + (dateObj.getMonth() + 1).toString().padStart(2, '0') + '-' + dateObj.getDate().toString().padStart(2, '0');
        $('#clickedDate').val(formattedDate);
        $('#clickedDateRed').val(formattedDate);
    });
});

//this is my save function 
$(document).ready(function() {
    $(function() {
        $(function() {
            $('#updateModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var activityId = button.data('id');
                var date = button.data('date');
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
                        $('#updateModal').find('#update_task').val(response.ts_task_id);
                        $('#updateModal').find('#update_location').val(response.ts_location);
                        $('#updateModal').find('#update_activity').val(response.ts_activity);
                        $('#updateModal').find('#update_from').val(response.ts_from_time);
                        $('#updateModal').find('#update_to').val(response.ts_to_time);
            
                    },
                    error: function(response, jqXHR, textStatus, errorThrown) {
                        console.log(response);
                    }
                });
                
                $('#update-entry').data('id', activityId); // Store the activityId as data attribute
                $('#entry-date-update').text(formattedDateStr); 
            });
            
            $('#update-entry').click(function(e) {
                e.preventDefault();
                var activityId = $(this).data('id'); // Retrieve the activityId from the data attribute
                
                // Serialize the form data
                var updateData = $('#update-form').serialize();
                
                $.ajax({
                    type: 'POST',
                    url: '/update-entries/' + activityId,
                    data: updateData,
                    success: function(response) {
                        $('.alert-success-saving').show();
                        $('#update-form')[0].reset();
                            setTimeout(function() {
                                $('.alert-success-saving').fadeOut('slow');
                            }, 3000);
                        // Fetch the updated list of activities
                        fetchActivities(yearput, monthput);
                    },
                    error: function(response, jqXHR, textStatus, errorThrown) {
                        console.log(response);
                        $('.alert-danger').show();
                        setTimeout(function() {
                            $('.alert-danger').fadeOut('slow');
                        }, 3000);
                    }
                });
            });
        });
    });
    
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
                for (var i = 1; i <= 31; i++) {
                    var taskEntry = $('#task_entry' + i);
                    taskEntry.removeClass('border-bottom-primary');
                  }
                $('#sp-label').text('Choose File');
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
    $(document).on('click', '.delete-all', function(event) {
        var activityYear = $(event.target).data('year');
        var activityMonth = $(event.target).data('month');
        deleteAllActivity(activityYear, activityMonth);
    });
    function deleteAllActivity(activityYear, activityMonth) {
        $.ajax({
            url: '/activities/all/' + activityYear + '/' + activityMonth,
            type: 'DELETE',
            success: function(response) {
                $('.alert-success-delete').show();
                setTimeout(function() {
                    $('.alert-success-delete').fadeOut('slow');
                }, 3000);
                for (var i = 1; i <= 31; i++) {
                    var taskEntry = $('#task_entry' + i);
                    taskEntry.removeClass('border-bottom-primary');
                  }
                $('#sp-label').text('Choose File');
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
                    $('.calculations').html('<i>No data available.</i>');
                } else {
                    // Create an object to store the counts for each location
                    var counts = {
                        'HO': 0,
                        'LK': 0,
                        'LN': 0,
                        'DK': 0,
                        'WFH': 0,
                        'OR' : 0
                    };
                    // Loop through the activities and append each row to the table
                    $.each(response, function(index, activity) {
                        var row = $('<tr></tr>').attr('data-id', activity.ts_id);
                        var date = new Date(activity.ts_date);
                        var options = { weekday: 'short' };
                        row.append($('<td></td>').text(date.toLocaleDateString('en-US', options)));
                        var formattedDate = moment(activity.ts_date).format('D-MMM-YYYY');
                        row.append($('<td width="150px" data-toggle="modal" data-target="#updateModal" class="clickable" ></td>').attr('data-date', activity.ts_date).attr('data-id', activity.ts_id).text(formattedDate));
                        row.append($('<td data-toggle="modal" data-target="#updateModal" class="clickable" ></td>').attr('data-date', activity.ts_date).attr('data-id', activity.ts_id).text(activity.ts_task));
                        row.append($('<td data-toggle="modal" data-target="#updateModal" class="clickable" ></td>').attr('data-date', activity.ts_date).attr('data-id', activity.ts_id).text(activity.ts_location));
                        row.append($('<td data-toggle="modal" data-target="#updateModal" class="clickable" ></td>').attr('data-date', activity.ts_date).attr('data-id', activity.ts_id).text(activity.ts_activity));
                        row.append($('<td data-toggle="modal" data-target="#updateModal" class="clickable" ></td>').attr('data-date', activity.ts_date).attr('data-id', activity.ts_id).text(activity.ts_from_time));
                        row.append($('<td data-toggle="modal" data-target="#updateModal" class="clickable" ></td>').attr('data-date', activity.ts_date).attr('data-id', activity.ts_id).text(activity.ts_to_time));
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

                    var rowsPerPageSelect = $('#rowsPerPage');
                    var activityTable = $('#activity-table');
                    var rows = activityTable.find('tr');

                    // Event listener for select change
                    rowsPerPageSelect.on('change', function() {
                    var rowsPerPage = parseInt($(this).val());

                    // Show or hide rows based on the selected number of rows per page
                    if (rowsPerPage === -1) {
                        rows.show();
                    } else {
                        rows.hide();
                        rows.slice(0, rowsPerPage).show();
                    }
                    });
                     // Perform search when the search input value changes
                    $('#searchInput').on('keyup', function() {
                        var searchText = $(this).val().toLowerCase();
                        $('#activity-table tr').filter(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                        });
                    });
                    // Add click handlers for the edit and delete buttons
                    $('.delete-btn').click(deleteActivity);
                    
                    // Create a lookup table of rates for each location
                    var rates = {
                        'HO': 70000,
                        'LK': 200000,
                        'LN': 400000,
                        'DK': 115000,
                        'WFH': 45000,
                        'OR' : 140000
                    };

                    // Create an object to store the total for each location
                    var totals = {
                        'HO': 0,
                        'LK': 0,
                        'LN': 0,
                        'DK': 0,
                        'WFH': 0,
                        'OR' : 0
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
        
        var isValid = true;
    
        // Check if any field with the class "validate" is empty
        $('.validate').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('is-invalid'); // Add "is-invalid" class to highlight the field
            } else {
                $(this).removeClass('is-invalid'); // Remove "is-invalid" class if the field is filled
            }
        });
    
        if (isValid) {
            // Create a FormData object to send the form data including the file
            var formData = new FormData($('#entry-form')[0]);
            var fileInput = $('#surat_penugasan_wfh');
            // Send an AJAX request to the entries.store route
            $.ajax({
                type: 'POST',
                url: '/entries',
                data: formData,
                dataType: 'json',
                contentType: false, // Set contentType to false, as we are sending FormData
                processData: false, // Set processData to false, as we are sending FormData
                success: function(response) {
                    $('.alert-success-saving').show();
                    document.getElementById("activity").removeAttribute("readonly");
                    document.getElementById("location").removeAttribute("readonly");
                    document.getElementById("start-time").removeAttribute("readonly");
                    document.getElementById("end-time").removeAttribute("readonly");
                    fileInput.removeClass('validate');
                    $('#fileInputIfexistWfh').hide();
                    
                    $('#entry-form')[0].reset();
                    $('#sp-label').text('Choose File');
                    setTimeout(function() {
                        $('.alert-success-saving').fadeOut('slow');
                    }, 3000);
                    // Fetch the updated list of activities
                    fetchActivities(yearput, monthput);
                },
                error: function(response,jqXHR, textStatus, errorThrown) {
                    console.log(response);
                    $('#entry-form')[0].reset();
                    $('.alert-danger').show();
                    setTimeout(function() {
                        $('.alert-danger').fadeOut('slow');
                    }, 3000);
                }
            });
        } else {
            // Show a popup or perform any other action to indicate that there are empty fields
            alert('Please fill in all the required fields.');
        }
    });

    $('#save-entry-red').click(function(e) {
        e.preventDefault();
        
        var isValid = true;
    
        // Check if any field with the class "validate" is empty
        $('.validate-red').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('is-invalid'); // Add "is-invalid" class to highlight the field
            } else {
                $(this).removeClass('is-invalid'); // Remove "is-invalid" class if the field is filled
            }
        });
    
        if (isValid) {
            // Create a FormData object to send the form data including the file
            var formData = new FormData($('#entry-form-red')[0]);
    
            // Send an AJAX request to the entries.store route
            $.ajax({
                type: 'POST',
                url: '/save_entries/holiday',
                data: formData,
                dataType: 'json',
                contentType: false, // Set contentType to false, as we are sending FormData
                processData: false, // Set processData to false, as we are sending FormData
                success: function(response) {
                    console.log(response);
                    $('.alert-success-saving').show();
                    document.getElementById("activity").removeAttribute("readonly");
                    document.getElementById("location").removeAttribute("readonly");
                    document.getElementById("start-time").removeAttribute("readonly");
                    document.getElementById("end-time").removeAttribute("readonly");
                    $('#entry-form-red')[0].reset();
                    $('#sp-label').text('Choose File');
                    setTimeout(function() {
                        $('.alert-success-saving').fadeOut('slow');
                    }, 3000);
                    // Fetch the updated list of activities
                    fetchActivities(yearput, monthput);
                },
                error: function(response,jqXHR, textStatus, errorThrown) {
                    console.log(response);
                    $('#entry-form-red')[0].reset();
                    $('.alert-danger').show();
                    setTimeout(function() {
                        $('.alert-danger').fadeOut('slow');
                    }, 3000);
                }
            });
        } else {
            // Show a popup or perform any other action to indicate that there are empty fields
            alert('Please fill in all the required fields.');
        }
    });
  

    $('#multiple-entries').click(function(e) {
        e.preventDefault();

        var isValidMult = true;

        // Check if any field with the class "validate" is empty
        $('.validateMult').each(function() {
            if ($(this).val() === '') {
            isValidMult = false;
            $(this).addClass('is-invalid'); // Add "is-invalid" class to highlight the field
            } else {
            $(this).removeClass('is-invalid'); // Remove "is-invalid" class if the field is filled
            }
        });

        if (isValidMult) {
            // Serialize the form data
            var formData = $('#multiple-entry-form').serialize();
            // Send an AJAX request to the entries.store route
            $.ajax({
            type: 'POST',
            url: '/multiple_entries',
            data: formData,
            success: function(response) {
                $('.alert-success-saving').show();
                document.getElementById("activity").removeAttribute("readonly");
                document.getElementById("location").removeAttribute("readonly");
                document.getElementById("start-time").removeAttribute("readonly");
                document.getElementById("end-time").removeAttribute("readonly");
                $('#multiple-entry-form')[0].reset();
                $(function() {
                    initializeDateRangePicker();
                  });
                setTimeout(function() {
                $('.alert-success-saving').fadeOut('slow');
                }, 3000);
                // Fetch the updated list of activities
                fetchActivities(yearput, monthput);
            },
            error: function(response,jqXHR, textStatus, errorThrown) {
                console.log(response);
                $('.alert-danger').show();
                setTimeout(function() {
                $('.alert-danger').fadeOut('slow');
                }, 3000);
            }
            });
        } else {
            // Show a popup or perform any other action to indicate that there are empty fields
            alert('Please fill in all the required fields.');
        }
    });
});

function initializeDateRangePicker() {
    var year = $('#yearSel').val();
    var month = $('#monthSel').val();
    var startDate = moment().year(year).month(month - 1).startOf('month');
    var endDate = moment().year(year).month(month - 1).endOf('month');
  
    $('input[name="daterange"]').daterangepicker({
      "startDate": startDate,
      "endDate": endDate,
      "opens": "right",
      "isInvalidDate": function(date) {
        // Disable Saturdays and Sundays
        return (date.day() === 0 || date.day() === 6);
      },
      "minDate": startDate,
      "maxDate": endDate
    }, function(start, end, label) {
      console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });
}
  
$(function() {
initializeDateRangePicker();
});

$(document).ready(function() {
    $('#location').change(function() {
        var selectedLocation = $(this).val();
        var fileInput = $('#surat_penugasan_wfh');
        var checkbox = $('#flexCheckWfh');
        if (selectedLocation === "WFH") {
            fileInput.addClass('validate');
            $('#fileInputIfexistWfh').show();
        } else {
            fileInput.removeClass('validate');
            $('#fileInputIfexistWfh').hide();
        }
        if (checkbox.is(':checked')) {
            $('#surat_penugasan_wfh').removeClass('validate');
        }
    });

    $('#flexCheckWfh').change(function() {
        if ($(this).is(':checked')) {
            $('#surat_penugasan_wfh').removeClass('validate');
        }
    });
});

$(function() {
  $('#flexCheckDefault').change(function() {
    if ($(this).is(':checked')) {
      $('#surat_penugasan').removeClass('validate-red');
    } else {
      $('#surat_penugasan').addClass('validate-red');
    }
  });
});