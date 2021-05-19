<script src="{{ asset('admin-css/assets/js/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('admin-css/assets/js/pages/base_tables_datatables.js')}}"></script>
<script type="text/javascript">
  
$(document).ready(function () {
 jQuery('#tableInvoices').dataTable({
            initComplete: function() {
                            $('div.loading').remove();
                            $('#containerTableResult').show();
                        },
            pageLength: 500,
            lengthMenu: [[500], [500]]
        });
        
   $('.sendInvoiceEmail').on('click',function (e){
    e.preventDefault();
    e.stopPropagation();
    if(confirm('Enviar factura a '+ $('#email').val() +'?')){
      $('#loadigPage').show('slow');
       $.ajax({
        url: '/admin/facturas/enviar',
          type: 'POST',
          data: {
            id: $(this).data('id'),
            _token: "{{csrf_token()}}"
          }
        })
        .done(function (resp) {
          if (resp == 'OK')  window.show_notif('success', 'Factura enviada');
          else   window.show_notif('error', resp);
        })
        .fail(function () {
          window.show_notif('error', 'Factura no enviada');
        })
        .always(function () {
          $('#loadigPage').hide('slow');
        });
      }
    });    
});
</script>