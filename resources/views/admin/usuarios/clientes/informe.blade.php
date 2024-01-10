@extends('layouts.popup')
@section('content')
<h1 class="text-center"><?php echo $user->name; ?></h1>
<div class="nav-box">
<ul class="nav nav-tabs">
  <li <?php if ($tab == 'datos') echo 'class="active"'; ?>><a data-toggle="tab" href="#datos">Datos</a></li>
  <li <?php if ($tab == 'servic') echo 'class="active"'; ?>><a data-toggle="tab" href="#servic">Suscripciones</a></li>
  <li <?php if ($tab == 'history') echo 'class="active"'; ?>><a data-toggle="tab" href="#history">Historial</a></li>
  <li <?php if ($tab == 'notes') echo 'class="active"'; ?>><a data-toggle="tab" href="#notes">ENTR. PERSONAL</a></li>
  <li <?php if ($tab == 'consent') echo 'class="active"'; ?>><a data-toggle="tab" href="#consent">Consentimiento</a></li>
  <li <?php if ($tab == 'invoice') echo 'class="active"'; ?>><a data-toggle="tab" href="#invoice">Factura</a></li>
  <li <?php if ($tab == 'bono') echo 'class="active"'; ?>><a data-toggle="tab" href="#bono">Bonos</a></li>
  <li <?php if ($tab == 'valoracion') echo 'class="active"'; ?>><a data-toggle="tab" href="#valoracion">Valoración</a></li>
  <li <?php if ($tab == 'nutricion') echo 'class="active"'; ?>><a data-toggle="tab" href="#nutricion">Nutrición</a></li>
  <li <?php if ($tab == 'fisio') echo 'class="active"'; ?>><a data-toggle="tab" href="#fisio">Fisio</a></li>
  <li <?php if ($tab == 'fisioG') echo 'class="active"'; ?>><a data-toggle="tab" href="#fisioG">Fisio GETAFE</a></li>
  <li <?php if ($tab == 'esthetic') echo 'class="active"'; ?>><a data-toggle="tab" href="#esthetic">Estética</a></li>
</ul>
</div>
<div class="tab-content box">
  <div id="datos" class="tab-pane fade <?php if ($tab == 'datos') echo 'in active'; ?>">
      @include('admin.usuarios.clientes.forms.data')
  </div>
  <div id="servic" class="tab-pane fade <?php if ($tab == 'servic') echo 'in active'; ?>">
        @include('admin.usuarios.clientes.forms.servic')
  </div>
  <div id="history" class="tab-pane fade <?php if ($tab == 'history') echo 'in active'; ?>">
        @include('admin.usuarios.clientes.forms.history')
  </div>
  <div id="notes" class="tab-pane fade <?php if ($tab == 'notes') echo 'in active'; ?>">
        @include('admin.usuarios.clientes.forms.notes')
  </div>
  <div id="consent" class="tab-pane fade <?php if ($tab == 'consent') echo 'in active'; ?>">
        @include('admin.usuarios.clientes.forms.consent')
  </div>
  <div id="invoice" class="tab-pane fade <?php if ($tab == 'invoice') echo 'in active'; ?>">
        @include('admin.usuarios.clientes.forms.invoice')
  </div>
  <div id="bono" class="tab-pane fade <?php if ($tab == 'bono') echo 'in active'; ?>">
        @include('admin.usuarios.clientes.forms.bonos')
  </div>
  <div id="valoracion" class="tab-pane fade <?php if ($tab == 'valoracion') echo 'in active'; ?>">
        @include('admin.usuarios.clientes.forms.valoracion')
  </div>
  <div id="nutricion" class="tab-pane fade <?php if ($tab == 'nutricion') echo 'in active'; ?>">
        @include('admin.usuarios.clientes.forms.nutricion')
  </div>
  <div id="fisio" class="tab-pane fade <?php if ($tab == 'fisio') echo 'in active'; ?>">
        @include('admin.usuarios.clientes.forms.fisio')
  </div>
  <div id="esthetic" class="tab-pane fade <?php if ($tab == 'esthetic') echo 'in active'; ?>">
        @include('admin.usuarios.clientes.forms.esthetic')
  </div>
</div>
<div class="row">
    <div class="col-md-12 push-10 bg-white" >
        <div class="col-md-6" style="margin-right: 1px solid #e8e8e8;">
            <div class="col-md-12">
            </div>
        </div>
        <div class="col-md-6" style="margin-left: 1px solid #e8e8e8;">
            
        </div>

    </div>


    
</div>

<div class="modal fade in" id="modalCliente" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="block block-themed block-transparent remove-margin-b">
        <div class="block-header bg-primary-dark">
          <ul class="block-options">
            <li>
              <button data-dismiss="modal" type="button" class="reload"><i class="si si-close "> Cerrar y refrescar</i></button>
            </li>
          </ul>
        </div>
        <div><iframe id="ifrCliente"></iframe></div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade in" id="modalSendFile" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="block block-themed block-transparent remove-margin-b">
        <div class="block-header bg-primary-dark">
          <ul class="block-options">
            <li>
              <button data-dismiss="modal" type="button" ><i class="si si-close "> Cerrar</i></button>
            </li>
          </ul>
        </div>
        <div class="p-3">
          <h2>Enviar <span class="filename"></span></h2>
          <div class="p-3">
            <label>Nombre:</label>
            <input type="text" placeholder="Enviar a.." class="form-control" id="nameFileTo">
          </div>
          <div class="p-3">
            <label>Correo electrónico</label>
            <input type="email" placeholder="Enviar a.." class="form-control" id="emailFileTo">
          </div>
          <div class="p-3 text-center"><button type="button" class="sendFileTo btn btn-success"><i class="fa fa-emvolpe"></i>Enviar</button></div>
          <div class="p-3 text-center alert alert-danger" style="display:none"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('admin-css/assets/js/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('admin-css/assets/js/pages/base_tables_datatables.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $(".rates-inform").mouseenter(function () {
            var idRate = $(this).attr('data-idrate');
            var idUser = $(this).attr('data-iduser');
            $.get('/admin/desgloce/tarifa/usuario/', {idRate: idRate, idUser: idUser}).done(function (data) {
                $(".rate-" + idRate).empty();
                $(".rate-" + idRate).append(data);
                $(".rate-" + idRate).show('fast');
            });
        }).mouseleave(function () {
            var idRate = $(this).attr('data-idrate');
            var idUser = $(this).attr('data-iduser');
            $(".rate-" + idRate).empty();
            $(".rate-" + idRate).hide('fast');
        });
        $('.add_rate').click(function (e) {
          e.preventDefault();
          var id_user = $(this).attr('data-idUser');
          $('#ifrCliente').attr('src','/admin/usuarios/cobrar/tarifa?id_user=' + id_user);
          $('#modalCliente').modal('show');
        });
        $('.openEditCobro').on('click', function (e) {
            e.preventDefault();
            var cobro_id = $(this).data('cobro');
            $('#ifrCliente').attr('src','/admin/update/cobro/' + cobro_id);
            $('#modalCliente').modal('show');
        });
        $('.openCobro').on('click',function (e) {
            e.preventDefault();
            var rate = $(this).data('rate');
             var appointment = $(this).data('appointment');
            if (appointment>0){
              $('#ifrCliente').attr('src','/admin/clientes/cobro-cita/' + appointment);
//              alert('Las citas se deben abonar en el calendario'); return;
            } else {
              $('#ifrCliente').attr('src','/admin/clientes/generar-cobro/' + rate);
            }
            $('#modalCliente').modal('show');
        });
        $('.add_bono').click(function (e) {
          e.preventDefault();
          var id_user = $(this).attr('data-idUser');
          var back = '/ficha/'+id_user
          $('#ifrCliente').attr('src','/admin/bonos/comprar/' + id_user + back );
          $('#modalCliente').modal('show');
        });
        
        $('.editNote').on('click',function (e) {
            e.preventDefault();
            var parent = $(this).closest('.blockNote');
            parent.find('[name="id"]').val($(this).data('id'));
            parent.find('[name="note"]').val($(this).data('note'));
            parent.find('[name="coach"]').val($(this).data('coach'));
            parent.find('.delNote').show();
            parent.find('.newNote').show();
        });
        $('.newNote').on('click',function (e) {
            e.preventDefault();
            var parent = $(this).closest('.blockNote');
             parent.find('[name="id"]').val('');
            parent.find('[name="note"]').val('');
            parent.find('.delNote').hide();
            parent.find('.newNote').hide();
        });
        $('.delNote').on('click',function (e) {
           if (confirm('Eliminar la nota?'))
            $(this).closest('form').attr('action','/admin/usuarios/del-note').submit();
        });
        
        $('#id_rateSubscr').on('change',function (e) {
          var obj  = $(this).find(':selected');
          var data = obj.data('t');
            
          $('#r_price').val(obj.data('p'));
//          if (data == 'pt'){
//            $('#rateCoach').removeClass('disabled');
//            $('#id_rateCoach').attr('disabled',false);
//          }
//          else {
//            $('#rateCoach').addClass('disabled');
//            $('#id_rateCoach').val('').attr('disabled',true);
//          }
              
          var tarifa = obj.data('tarifa');
          $('#showTartifa').html('');
          if (tarifa == 'fidelity') $('#showTartifa').html('<i class="fa fa-heart text-success"></i> Plan Fidelity');
          if (tarifa == 'nofidelity') $('#showTartifa').html('<i class="fa fa-heart text-danger"></i> Plan Básico');
        });
        
        /**************************************************/        
        $('.subscr_price').on('change',function (e) {
          var posting = $.post( '/admin/change-subscr-price', { 
                            _token: '{{csrf_token()}}',
                            subscr_id: $(this).data('r'),
                            price: $(this).val(),
                        });
          posting.done(function (data) {
              if (data[0] == 'OK') {
                window.show_notif('success', data[1]);
              } else {
                window.show_notif('error', data[1]);
              }

          });
        });
        
        
        /**************************************************/
                
        $('.sendEncuesta').click(function (e) {
          e.preventDefault();
          var id = $(this).data('id');
          if (confirm('Reenviar el mail para completar la encuesta?')){
            var data = {
              uID: id,
              _token: '{{csrf_token()}}'
            };
            var posting = $.post( '/admin/sendEncuesta', data ).done(function( data ) {
                if (data == 'OK'){
                  window.show_notif('success', 'Encuesta Reenviada');
                } else {
                   window.show_notif('error',data);
                }
            });
          }
        });
        
        /**************************************************/
        $('.sendConsent').on('click', function () {
          var type = $(this).closest('tr').data('id');
          var posting = $.post('/admin/usuarios/send-consent', {
            _token: '{{csrf_token()}}',
            id_user: {{$user->id}},
            type: type
          });
          posting.done(function (data) {
            if (data[0] == 'OK') {
              window.show_notif('success', data[1]);
            } else {
              window.show_notif('error', data[1]);
            }
          });
          posting.fail(function (data) {
            window.show_notif('error', 'UPs, algo salió mal');
          });
          
          
        });
        $('.goContracts').on('click', function () {
          var type = $(this).closest('tr').data('id');
          var posting = $.post('/admin/usuarios/linkContratos', {
            _token: '{{csrf_token()}}',
            id_user: {{$user->id}},
            type: type
          });
          posting.done(function (data) {
            if (data[0] == 'OK') {
              window.open(data[1], '_blank').focus();
            } else {
              window.show_notif('error', data[1]);
            }
          });
          posting.fail(function (data) {
            window.show_notif('error', 'UPs, algo salió mal');
          });
          
          
        });
        $('.rmContrato').on('click', function () {
          if (confirm('Cancelar y volver a solicitar el contrato?')){
            var posting = $.post('/admin/usuarios/remove-contrato', {
              _token: '{{csrf_token()}}',
              id_user: {{$user->id}},
            });
            posting.done(function (data) {
              if (data[0] == 'OK') {
                window.show_notif('success', data[1]);
              } else {
                window.show_notif('error', data[1]);
              }
            });
            posting.fail(function (data) {
              window.show_notif('error', 'UPs, algo salió mal');
            });
          }
          
        });
        /**************************************************/
        $('.sendValora').on('click', function () {
          var posting = $.post('/admin/usuarios/send-valoracion', {
            _token: '{{csrf_token()}}',
            id_user: {{$user->id}},
          });
          posting.done(function (data) {
            if (data[0] == 'OK') {
              window.show_notif('success', data[1]);
            } else {
              window.show_notif('error', data[1]);
            }
          });
          posting.fail(function (data) {
            window.show_notif('error', 'UPs, algo salió mal');
          });
          
          
        });
        $('.autosaveValora').on('change', function () {
          var posting = $.post('/admin/clientes/autosaveValora', {
            id: {{$user->id}},
            field: $(this).attr('name'),
            val: $(this).val(),
          }).done(function (data) {
            console.log(data);
          });
        });
         /**************************************************/
         $('.showImg').on('click',function(){
          $(this).attr('src', '/admin/photo/{{$user->id}}');
         });
       
        /**************************************************/
       $('.nav-tabs').on('click','a',function(){
         var newURL = '/admin/usuarios/informe/{{$user->id}}/';
         var href =$(this).attr('href');
         window.history.pushState("", "", newURL+href.slice(1));
       });
        /**************************************************/
        var fileSendID = null;
        $('.sendMail').on('click',function(){
          fileSendID = $(this).data('k');
          $('#modalSendFile .filename').text($(this).data('n'));
          $('#modalSendFile #emailFileTo').val('');
          $('#modalSendFile #nameFileTo').val('');
          $('#modalSendFile').modal();
        });
        $('#modalSendFile').on('click','.sendFileTo',function(){
          var posting = $.post('/admin/clientes/sendFileTo', {
            uid: {{$user->id}},
            fid: fileSendID,
            mail: $('#emailFileTo').val(),
            nameTo: $('#nameFileTo').val()
          }).done(function (data) {
            if (data == 'OK'){
              $('#modalSendFile #emailFileTo').val('');
              $('#modalSendFile #nameFileTo').val('');
              $('#modalSendFile .alert').removeClass('alert-danger').addClass('alert-success').text('Archivo enviado');
            } else {
              $('#modalSendFile .alert').removeClass('alert-success').addClass('alert-danger').text(data);
            }
            $('#modalSendFile .alert').show();
          });
        });
        /**************************************************/

    });

  @if($detail)
    var details = {!!$detail!!};
  @endif
</script>
@include('invoices.script')
@include('invoices.script_mail')
<script src="{{assetv('/admin-css/assets/js/toltip.js')}}"></script>
<style>
  .openEditCobro,
  .openCobro{
    cursor: pointer;
  }
  .tab-pane{
    padding: 24px 16px;
    background-color: white;
  }
  .sing-box {
    border: 1px solid;
    width: 325px;
    padding: 5px;
    margin: 1em auto;
  }
  #id_rateCoach:disabled{
    background-color: #d0d0d0;
  }
  .subscr_price {
    background-color: #f7f7f7;
    border: none;
    text-align: right;
    width: 81px;
    padding: 3px 0px;
    cursor: pointer;
  }
  td.btnCel {
    width: 50px;
  }
  div#tableInvoices_filter {
    float: right;
  }
  .lstBono{
    padding: 10px 0 10px 12px;
    margin: 11px 1%;
    width: 95%;
    box-shadow: 1px 1px 4px 2px #a5a5a5;
    cursor: pointer;
  }
  .lstBono:hover,
  .lstBono.selected{
    background-color: #d1eadc;
  }
  .lstBono label{
    font-size: 17px;
  }
  .lstBono span {
    float: right;
    font-size: 34px;
    margin-top: -8px;
    margin-right: 7px;
  }
  #bonoLog{
    max-width: 780px;
    margin: 1em auto;
  }
  #bonoLog thead th{
    background-color: #d1eadc;
  }

  .fFIDELITY .form-control{
    width: calc( 100% - 40px);
    float: left;
    padding: 2px;
  }
  .fFIDELITY i{
    padding: 6px 5px;
    float: left;
    font-size: 21px;
    margin-left: 7px;
  }
  .box-photo{
    min-height: 12em;
    text-align: center;
  }
  img.photo {
    max-width: 100%;
    max-height: 8em;
    margin: 0 auto 8px;
}
</style>
@endsection