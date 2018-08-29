

// Call the dataTables jQuery plugin
$(document).ready(function() {

	// Bootstrap datepicker
	$('.input-daterange input').each(function() {
	  $(this).datepicker('clearDates');
  });
  
  var supplier = $("#supName").val();

  var table = $('#dataTable').DataTable({
    "processing": true,
    "language": {
      "processing": " <img style='width:50%' src='../img/giphy.gif' /><br>Requesting data from Texttalk Please wait..." //add a loading image,simply putting <img src="loader.gif" /> tag.
    },
    "ajax": "/test5/product-list.php?name="+supplier,
    "columns": [
        { "data": "quantity" },
        { "data": "productName" },
        { "data": "articleNumber" },
        { "data": "ordered" }
    ]

  });



// Extend dataTables search
$.fn.dataTable.ext.search.push(
  function(settings, data, dataIndex) {
    var min = $('#min-date').val();
    var max = $('#max-date').val();
    var createdAt = data[3] || 0; // Our date column in the table

    if (
      (min == "" || max == "") ||
      (moment(createdAt).isSameOrAfter(min) && moment(createdAt).isSameOrBefore(max))
    ) {
      return true;
    }
    return false;
  }
);

// Re-draw the table when the a date range filter changes
$('.date-range-filter').change(function() {
  table.draw();
});

$('#my-table_filter').hide();


});
