// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable({
    "order": [[ 0, "asc" ]],
    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
    "pageLength": 5
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

// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTableMonth').DataTable({
    "order": [[ 0, "desc" ]],
    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
    "pageLength": 5
  } );
} );

// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTableRoles').DataTable({
    "order": [[ 0, "asc" ]],
    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
    "pageLength": 50
  } );
} );

// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTableUser').DataTable({
    "order": [[ 0, "asc" ]],
    "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
    "pageLength": 10
  } );
} );