function isconfirm(){
	if(!confirm('Are you sure want to do this ?')){
	    e.preventDefault();
	    return;
	}
    return true;
}
function deleteProject(event, id) {
    event.preventDefault();
    swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this project!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                // perform the actual delete request
                axios.delete('/project_list/delete/' + id)
                    .then(response => {
                        // show success message using SweetAlert
                        swal("Poof! The project has been deleted!", {
                            icon: "success",
                        });

                        // remove the assignment from the page
                        window.location.href = '/project_list';
                    })
                    .catch(error => {
                        // show error message using SweetAlert
                        swal("Oops!", "Something went wrong while deleting the project!", "error");
                    });
            }
        });
}


function deleteAssignment(event, id) {
    event.preventDefault();
    swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this assignment!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                // perform the actual delete request
                axios.delete('/assignment/delete/' + id)
                    .then(response => {
                        // show success message using SweetAlert
                        swal("Poof! The assignment has been deleted!", {
                            icon: "success",
                        });

                        // remove the assignment from the page
                        window.location.href = '/assignment';
                    })
                    .catch(error => {
                        // show error message using SweetAlert
                        swal("Oops!", "Something went wrong while deleting the assignment!", "error");
                    });
            }
        });
}

function fetchProjectRoles() {
    $.ajax({
        url: '/retrieveProjectRoles',
        type: 'GET',
        success: function(response) {
            // Clear the table body
            $('#projectRoles').empty();
            // Check if the response is empty or null
            if (response.length === 0) {
                // Display a message to the user
                $('#projectRoles').append($('<tr><td class="text-center" colspan="4">No data available in table.</td></tr>'));
            } else {
                // Loop through the activities and append each row to the table
                $.each(response, function(index, activity) {
                    var row = $('<tr></tr>').attr('data-id', activity.id);
                    row.append($('<td></td>').text(activity.id));
                    row.append($('<td></td>').text(activity.role_code));
                    row.append($('<td></td>').text(activity.role_name));
                    row.append($('<td></td>').text(activity.fare));
                    var actions = $('<td class="text-center"></td>');
                    actions.append($('<a></a>').addClass('btn-sm btn btn-danger deleteRole').text('Delete').attr('data-id', activity.id)).attr('onclick', 'return isconfirm()');;
                    row.append(actions);
                    $('#projectRoles').append(row);
                });
                // Add click handlers for the edit and delete buttons
                $('.deleteRole').click(deleteRole);
            }
        },
        error: function(response) {
            console.log(response);
        }
    });
}
$(document).ready(function () {
    $('#save-project-roles-entry').click(function(e) {
        e.preventDefault();
        // Serialize the form data
        var formData = $('#new-project-roles-form').serialize();
        // Send an AJAX request to the entries.store route
        $.ajax({
        type: 'POST',
        url: '/projectRole/create',
        data: formData,
        success: function(response) {
            $('.alert-success-saving').show();
            $('#new-project-roles-form')[0].reset();
            setTimeout(function() {
                $('.alert-success-saving').fadeOut('slow');
            }, 3000);
            fetchProjectRoles();
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


$(document).on('click', '.deleteRole', function(event) {
    var roleId = $(event.target).data('id');
    deleteRole(roleId);
});

function deleteRole(roleId) {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: '/project_list/delete/project_role/' + roleId,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
        },
        success: function(response) {
            $('.alert-success-delete').show();
            setTimeout(function() {
                $('.alert-success-delete').fadeOut('slow');
            }, 3000);
            fetchProjectRoles();
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

function fetchLocations() {
    $.ajax({
        url: '/retrieveLocations',
        type: 'GET',
        success: function(response) {
            // Clear the table body
            $('#Locations').empty();
            // Check if the response is empty or null
            if (response.length === 0) {
                // Display a message to the user
                $('#Locations').append($('<tr><td class="text-center" colspan="4">No data available in table.</td></tr>'));
            } else {
                // Loop through the activities and append each row to the table
                $.each(response, function(index, activity) {
                    var row = $('<tr></tr>').attr('data-id', activity.id);
                    row.append($('<td></td>').text(activity.id));
                    row.append($('<td></td>').text(activity.location_code));
                    row.append($('<td></td>').text(activity.description));
                    row.append($('<td></td>').text(activity.fare));
                    var actions = $('<td class="text-center"></td>');
                    actions.append($('<a></a>').addClass('btn-sm btn btn-danger delete-btn-location').text('Delete').attr('data-id', activity.id)).attr('onclick', 'return isconfirm()');;
                    row.append(actions);
                    $('#Locations').append(row);
                });
                // // Add click handlers for the edit and delete buttons
                $('.delete-btn-location').click(deleteLocation);
            }
        },
        error: function(response) {
            console.log(response);
        }
    });
}

$(document).ready(function () {
    $('#save-location-entry').click(function(e) {
        e.preventDefault();
        // Serialize the form data
        var formData = $('#new-location-form').serialize();
        // Send an AJAX request to the entries.store route
        $.ajax({
        type: 'POST',
        url: '/location/create',
        data: formData,
        success: function(response) {
            var cardBody = $('.Locations');
            $('.alert-success-saving').show();
            $('#new-location-form')[0].reset();
            setTimeout(function() {
                $('.alert-success-saving').fadeOut('slow');
            }, 3000);
            fetchLocations();
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

$(document).ready(function () {
    $('#save-client-entry').click(function(e) {
        e.preventDefault();
        // Serialize the form data
        var formData = $('#new-client-form').serialize();
        // Send an AJAX request to the entries.store route
        $.ajax({
        type: 'POST',
        url: '/client/create',
        data: formData,
        success: function(response) {
            var cardBody = $('.Clients');
            $('.alert-success-saving').show();
            $('#new-client-form')[0].reset();
            setTimeout(function() {
                $('.alert-success-saving').fadeOut('slow');
            }, 3000);
            fetchClients();
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


$(document).on('click', '.delete-btn-client', function(event) {
    var clientId = $(event.target).data('id');
    deleteClient(clientId);
});

function deleteClient(clientId) {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: '/project_list/delete/client/' + clientId,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
        },
        success: function(response) {
            $('.alert-success-delete').show();
            setTimeout(function() {
                $('.alert-success-delete').fadeOut('slow');
            }, 3000);
            fetchClients();
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
function fetchClients() {
    $.ajax({
        url: '/retrieveClients',
        type: 'GET',
        success: function(response) {
            // Clear the table body
            $('#Clients').empty();
            // Check if the response is empty or null
            if (response.length === 0) {
                // Display a message to the user
                $('#Clients').append($('<tr><td class="text-center" colspan="4">No data available in table.</td></tr>'));
            } else {
                // Loop through the activities and append each row to the table
                $.each(response, function(index, activity) {
                    var row = $('<tr></tr>').attr('data-id', activity.id);
                    row.append($('<td></td>').text(activity.id));
                    row.append($('<td></td>').text(activity.client_name));
                    row.append($('<td></td>').text(activity.address));
                    var actions = $('<td class="text-center"></td>');
                    actions.append($('<a></a>').addClass('btn-sm btn btn-danger delete-btn-client').text('Delete').attr('data-id', activity.id)).attr('onclick', 'return isconfirm()');;
                    row.append(actions);
                    $('#Clients').append(row);
                });
                // Add click handlers for the edit and delete buttons
                $('.delete-btn-client').click(deleteClient);
            }
        },
        error: function(response) {
            console.log(response);
        }
    });
}


$(document).on('click', '.delete-btn-location', function(event) {
    var locationId = $(event.target).data('id');
    deleteLocation(locationId);
});

function deleteLocation(locationId) {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: '/project_list/delete/location/' + locationId,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
        },
        success: function(response) {
            $('.alert-success-delete').show();
            setTimeout(function() {
                $('.alert-success-delete').fadeOut('slow');
            }, 3000);
            fetchLocations();
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


$(document).on('click', '.btn-edit', function() {
    var projectId = $(this).data('project-id');
    $('#project_id').val(projectId);
    
    // Make an AJAX request to fetch the project data and populate the form fields
    $.ajax({
        url: '/retrieveProjectData/' + projectId,
        method: 'GET',
        success: function(response) {
            // Populate the form fields with the received data
            $('#p_code').val(response.project_code);
            $('#p_name').val(response.project_name);
            $('#address').val(response.address);
        },
        error: function(xhr) {
            // Handle error
            console.log(xhr.responseText);
        }
    });
});

$(document).on('click', '#submitEdit', function() {
    var formData = $('#editForm').serialize();
    var projectId = $('#project_id').val();
    
    // Make an AJAX request to update the project data
    $.ajax({
        url: '/project_list/edit/save/' + projectId,
        method: 'PUT',
        data: formData,
        success: function(response) {
            // Handle success
            console.log(response);
            
            // Close the modal
            $('#editModal').modal('hide');
            window.location.href = '/project_list/view/details/' + projectId;
            
            // Reload or update the project list on the page
            // Implement the appropriate logic based on your requirements
        },
        error: function(xhr) {
            // Handle error
            console.log(xhr.responseText);
        }
    });
});

$(document).on('click', '.btn-usr-edit', function() {
    var usrId = $(this).data('usr-id');
    $('#usr_id').val(usrId);

    $.ajax({
        url: '/retrieveUsrPeriodData/' + usrId,
        method: 'GET',
        success: function(response) {
            // Populate the form fields with the received data
            $('#fromPeriode').val(response.periode_start);
            $('#toPeriode').val(response.periode_end);
        },
        error: function(xhr) {
            // Handle error
            console.log(xhr.responseText);
        }
    });
});
$(document).on('click', '#editUserPeriodeSubmit', function() {
    var formData = $('#editUserPeriodeForm').serialize();
    var usrId = $('#usr_id').val();
    
    // Make an AJAX request to update the project data
    $.ajax({
        url: '/project_list/edit/usr/save/' + usrId,
        method: 'PUT',
        data: formData,
        success: function(response) {
            // Handle success
            console.log(response);
            
            // Close the modal
            $('#editPeriodModal').modal('hide');
            window.location.reload();

        },
        error: function(xhr) {
            // Handle error
            console.log(xhr.responseText);
        }
    });
});