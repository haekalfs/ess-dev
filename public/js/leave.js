$(document).ready(function() {
    $(function() {
        $('#leaveRequestDetailModal').on('show.bs.modal', function (event) {
            var Id = $(event.relatedTarget).data('id');
            var tableBody = $('#leaveRequestDetails');
            tableBody.empty(); // Clear the table body

            $.ajax({
                url: '/leave/request/details/' + Id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (Array.isArray(response)) {
                        for (var i = 0; i < response.length; i++) {
                            var row = response[i];
                            var html = '<tr>' +
                                '<td>' + row.RequestTo + '</td>' +
                                '<td>' + row.status + '</td>' +
                                '<td>' + row.notes + '</td>' +
                                '<td>' + row.last_updated + '</td>' +
                                '</tr>';
                            tableBody.append(html);
                        }

                        $('#request_by_detail').val(row.requestBy);
                        $('#quota_used_detail').val(row.quotaUsed);
                        $('#leave_dates_detail').val(row.leaveDates);
                        $('#reason_detail').val(row.reason);
                        $('#last_updated_detail').val(row.last_updated);
                        $('#request_date_detail').val(row.requestDate);
                        $('#total_days_detail').val(row.totalDays + " Day(s)");
                        $('#approver_detail').val(row.RequestTo);

                        // Update the progress bar
                        var approvalPercentage = response[0].approvalPercentage;
                        var progressBar = $('#leaveRequestDetailModal').find('.progress-bar');
                        progressBar.css('width', approvalPercentage + '%');
                        progressBar.text(approvalPercentage + '%');

                        var leaveRunningApprover = response[0].leaveRunningApprover;
                        $('#approver').text(leaveRunningApprover);
                    } else {
                        console.log('Invalid response format');
                    }
                },
                error: function(response, jqXHR, textStatus, errorThrown) {
                    console.log(response);
                }
            });
        });
    });
});

$(document).ready(function() {
    $(function() {
        $(function() {
            $('#updateLeave').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var leaveId = button.data('id');
                $.ajax({
                    url: '/leave/manage/edit/' + leaveId,
                    type: 'GET',
                    success: function(response) {
                        $('#updateLeave').find('#active_periode').val(response.active_periode);
                        $('#updateLeave').find('#expiration').val(response.expiration);
                        $('#updateLeave').find('#quota_left').val(response.quota_left);
                    },
                    error: function(response, jqXHR, textStatus, errorThrown) {
                        console.log(response);
                    }
                });
                
                $('#update-leave-btn').data('id', leaveId); // Store the activityId as data attribute
            });
            
            $('#update-leave-btn').click(function(e) {
                e.preventDefault();
                var leaveId = $(this).data('id'); // Retrieve the activityId from the data attribute
                // Serialize the form data
                var updateData = $('#update-leave').serialize();
                
                $.ajax({
                    type: 'POST',
                    url: '/leave/manage/update/' + leaveId,
                    data: updateData,
                    success: function(response) {
                        $('#successModal').modal('show');
                        $('#update-leave')[0].reset();
                        window.location.reload();
                    },
                    error: function(response, jqXHR, textStatus, errorThrown) {
                        console.log(response);
                    }
                });
            });
        });
    });
});