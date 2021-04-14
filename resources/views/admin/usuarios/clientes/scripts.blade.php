<script type="text/javascript">
$(document).ready(function () {
  $('#cuotas-pendientes').click(function () {
    $('#estado-payment').click();
  })

  $('#addIngreso').click(function () {
    $.get('/admin/nuevo/ingreso', function (data) {
      $('#contentListIngresos').empty().append(data);
    });
  });

  $('.btn-cobro').click(function (e) {
    e.preventDefault();

    var dateCobro = $(this).attr('data-dateCobro');
    var id_user = $(this).attr('data-idUser');
    var importe = $(this).attr('data-import');
    var rate = $(this).attr('data-rate');
    $('#ifrCliente').attr('src','/admin/clientes/generar-cobro/' + dateCobro + '/' + id_user + '/' + importe + '/' + rate);
  });


   


  $('.btn-edit-cobro').click(function (e) {
    e.preventDefault();
    var charge_id = $(this).data('charge');
    var rate_id = $(this).data('rate');
    console.log(charge_id,rate_id);
    $('#ifrCliente').attr('src','/admin/update/cobro/' + charge_id);
  });

  $('#newUser').click(function (e) {
    e.preventDefault();
    $('#content-new-user').empty().load('/admin/usuarios/new');
  });

  $('.btn-user').click(function (e) {
    e.preventDefault();
    var id = $(this).attr('data-idUser');
    $('#ifrCliente').attr('src','/admin/usuarios/informe/' + id);

  });

  $('.btn-rate-charge').click(function (e) {
    e.preventDefault();
    var id_user = $(this).attr('data-idUser');
    $('#ifrCliente').attr('src','/admin/usuarios/cobrar/tarifa?id_user=' + id_user);
  });



  $('#date').change(function (event) {

    var month = $(this).val();
    window.location = '/admin/clientes/' + month;
  });

  $('.switchStatus').change(function (event) {
    var id = $(this).attr('data-id');

    if ($(this).is(':checked')) {
      $.get('/admin/usuarios/activate/' + id, function (data) {
      });
    } else {
      $.get('/admin/usuarios/disable/' + id, function (data) {
      });
    }
  });

  $('#date-nutri, #date-fisio').click(function (event) {
    event.preventDefault();
    var id_user = $(this).attr('data-idUser');
    var consulta = $(this).attr('data-title');
    // $.get(', {id_user: id_user}, function(data) {
    $('#content-date').empty().load('/admin/citas/form/inform/create/' + id_user + '/' + consulta);
    // });
  });



});
</script>