<script src="{{ asset('admin-css/assets/js/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('admin-css/assets/js/pages/base_tables_datatables.js')}}"></script>
<script type="text/javascript">

$(document).ready(function () {
  jQuery('#tableInvoices').dataTable({
    initComplete: function () {
      $('div.loading').remove();
      $('#containerTableResult').show();
    },
    pageLength: 500,
    lengthMenu: [[500], [500]]
  });

});
</script>