$(document).ready(function(){
  var table = $('#user-group-datatable');
  var handler = table.data('handler');
  var columns = table.data('table-columns') ? table.data('table-columns').split(',') : [];

  table.dataTable( {
    processing: true,
    serverSide: true,
    deferRender: true,
    ajax: {
      url: handler,
      data: function(d) {
        d.tableColumns = columns;
      }
    },
    columns: [
      { "data": "user_group_id" },
      { "data": "name" }
    ]
  });
});