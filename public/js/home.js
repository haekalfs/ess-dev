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