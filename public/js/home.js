$(document).on('click', '.notification-item', function(e) {
    e.stopPropagation(); // Prevent the dropdown from closing

    var notificationId = $(this).data('notification-id');

    var notificationItem = $(this);
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: 'POST',
        url: '/notification/read/true/' + notificationId,
        headers: {
            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
        },
        success: function(response) {
            notificationItem.find('.font-weight-bold').removeClass('font-weight-bold');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Handle the error response here
            console.log(jqXHR.responseText);
        }
    });
});

function isconfirm(){
	if(!confirm('Are you sure want to do this ?')){
	    event.preventDefault();
	    return;
	}
    return true;
}

$(document).ready(function () {
    // Check for the stored tab ID in local storage
    let selectedTab = localStorage.getItem('selectedTab');
    if (selectedTab) {
        $('.nav-tabs a[href="#' + selectedTab + '"]').tab('show');
    }

    // Store the selected tab ID in local storage when a tab is clicked
    $('.nav-tabs a').on('click', function () {
        let selectedTab = $(this).attr('href').substring(1); // Get the ID of the selected tab
        localStorage.setItem('selectedTab', selectedTab); // Store the selected tab ID in local storage

        // Set a timeout to remove the stored tab data after 5 minutes
        setTimeout(function () {
            localStorage.removeItem('selectedTab');
        }, 5 * 60 * 1000); // 5 minutes in milliseconds
    });

    // Remove the stored tab data after 5 minutes if it exists on page load
    let storedTab = localStorage.getItem('selectedTab');
    if (storedTab) {
        setTimeout(function () {
            localStorage.removeItem('selectedTab');
        }, 5 * 60 * 1000); // 5 minutes in milliseconds
    }
});
