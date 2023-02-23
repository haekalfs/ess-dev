// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable({
    "order": [[ 0, "asc" ]],
    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
    "pageLength": 7
  } );
} );

// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable1').DataTable({
    "order": [[ 0, "desc" ]],
    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
    "pageLength": 5
  } );
} );
