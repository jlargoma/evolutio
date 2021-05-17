@extends('layouts.popup')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

<h1 class="text-center"><?php echo $user->name; ?></h1>
<ul class="nav nav-tabs">
  <li <?php if ($tab == 'datos') echo 'class="active"'; ?>><a data-toggle="tab" href="#datos">Datos</a></li>
  <li <?php if ($tab == 'servic') echo 'class="active"'; ?>><a data-toggle="tab" href="#servic">Suscripciones</a></li>
  <li <?php if ($tab == 'history') echo 'class="active"'; ?>><a data-toggle="tab" href="#history">Historial</a></li>
  <li <?php if ($tab == 'notes') echo 'class="active"'; ?>><a data-toggle="tab" href="#notes">Anotaciones</a></li>
  <li <?php if ($tab == 'consent') echo 'class="active"'; ?>><a data-toggle="tab" href="#consent">Consentimiento</a></li>
</ul>

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
  <div class="modal-dialog modal-md">
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
        $('.editNote').on('click',function (e) {
            e.preventDefault();
            $('#noteID').val($(this).data('id'));
            $('#note').val($(this).data('note'));
            $('#delNote').show();
            $('#newNote').show();
        });
        $('#newNote').on('click',function (e) {
            e.preventDefault();
            $('#noteID').val('');
            $('#note').val('');
            $('#delNote').hide();
            $('#newNote').hide();
        });
        $('#delNote').on('click',function (e) {
           if (confirm('Eliminar la nota?'))
            $(this).closest('form').attr('action','/admin/usuarios/del-note').submit();
        });
        
        $('#id_rateSubscr').on('change',function (e) {
          var obj  = $(this).find(':selected');
          var data = obj.data('t');
          $('#r_price').val(obj.data('p'));
          if (data == 'pt'){
            $('#rateCoach').removeClass('disabled');
            $('#id_rateCoach').attr('disabled',false);
          }
          else {
            $('#rateCoach').addClass('disabled');
            $('#id_rateCoach').val('').attr('disabled',true);
          }
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
        var canvas = document.querySelector("canvas");
        var signaturePad = new SignaturePad(canvas);
        $('#newSign').on('click',function (e) {
        $('#iSign').hide();
        $('#cSign').show();
        signaturePad.clear();
        $('#saveSign').show();
        });
        $('#saveSign').on('click',function (e) {
            e.preventDefault();
            $('#sign').val(signaturePad.toDataURL()); // save image as PNG
            $(this).closest('form').submit();
        });
        /**************************************************/
    });

  @if($detail)
    var details = {!!$detail!!};
  @endif
</script>
<script src="{{asset('/admin-css/assets/js/toltip.js')}}"></script>
<style>
    .openEditCobro,
    .openCobro{ cursor: pointer;}
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
</style>
@endsection