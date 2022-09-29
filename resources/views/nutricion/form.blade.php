@extends('layouts.popup')

@section('content')
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ assetV('css/custom.css') }}">
<?php 
$date_type_u = "Nutricionista";
$date_type = 'nutri'
?>
<a class="back" href="<?php echo $urlBack; ?>">X</a>
@if($blocked)<!-- es un bloqueo -->
  @if($isGroup)
    @include('calendars.editGroup')
  @else
    @include('calendars.editBlock')
  @endif
@else
  @include('calendars.editDate')
@endif

@if($id<1) 
  @include('calendars.blockDate')
@endif
<div class="modal fade in" id="modalCliente" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="block block-themed block-transparent remove-margin-b">
        <div class="block-header bg-primary-dark">
          <ul class="block-options">s
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

@endsection

@section('scripts')
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datetimepicker/moment.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/select2/select2.full.min.js')}}"></script>
<script type="text/javascript">
jQuery(function () {
    App.initHelpers(['datepicker', 'select2']);

        $("#id_user").change(function() {
            var id = $(this).val();
            $.get('/admin/get-mail/' + id, function(data) {
                $('#NC_email').val(data[0]);
                $('#NC_phone').val(data[1]);
            });
        });
        
        $('.sendForm').on('click', function(){
          var oForm = $('#'+$(this).data('id'));
          var data = {
            id: {{$id}},
            date: oForm.find('#date').val(),
            time: oForm.find('#hour').val(),
            uID: oForm.find('#id_user').val(),
            cID: oForm.find('#id_coach').val(),
            type: 'nutri',
            _token: '{{csrf_token()}}',
          };
          $.post('/admin/citas/checkDisp',data, function(resp) {
                        
            if (resp == 'bloqueo'){
              window.show_notif('error', 'Horario bloqueado para el Nutricionista');
              return;
            }
            
            if (resp>1){
              window.show_notif('error', 'Horario no disponible');
            } else {
              if (resp == 1){
                if (confirm('Ya hay una cita para ese momento, continuar de todas maneras?')){
                  oForm.submit();
                }
              } else {
                oForm.submit();
              }
            }
          });
        });
        $("#id_rate").change(function () {
            var price = $(this).find(':selected').data('price');
            $('#importeFinal').val(price);
        });
        $("#hour").change(function () {
            var val = $(this).find(':selected').val();
            $('#customTime').val(val+':00');
        });
        @if ($id > 0)
            $(".btnDeleteCita").click(function () {
                if (confirm('Eliminar la Cita?'))
                    location.assign("/admin/citas/delete/{{$id}}");
            });
        @else
            $('#newUser').click(function (e) {
                e.preventDefault();
                $('#u_name').show();
                $('#div_user').hide();
                $('#id_user').val('0');
                $('#tit_user').text('Nuevo Cliente');
            });
          $('#is_group').click(function (e) {
            if($(this).is(':checked')){
              $('#id_user').val('0').attr('disabled',true);
              $('#NC_email').val('').attr('disabled',true);
              $('#NC_phone').val('').attr('disabled',true);
              $('#u_name').val('').attr('disabled',true);
            } else {
              $('#id_user').val('0').attr('disabled',false);
              $('#NC_email').val('').attr('disabled',false);
              $('#NC_phone').val('').attr('disabled',false);
              $('#u_name').val('').attr('disabled',false);
            }
          });
        @endif

        $('.btn-user').click(function (e) {
          e.preventDefault();
          var id = $(this).attr('data-idUser');
          $('#ifrCliente').attr('src','/admin/usuarios/informe/' + id);
          $('#modalCliente').modal('show');
        });
        
        $('.clearEncuesta').click(function (e) {
          e.preventDefault();
          var id = $(this).data('id');
          if (confirm('Limpiar los datos actuales de la encuesta?')){
            var data = {
              uID: id,
              _token: '{{csrf_token()}}'
            };
            var posting = $.post( '/admin/clearEncuesta', data ).done(function( data ) {
                if (data == 'OK'){
                  location.reload();
                } else {
                    alert(data);
                }
            });
          }
        });
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
                  location.reload();
                } else {
                    alert(data);
                }
            });
          }
        });
        
        $('#modal_newUser').on('submit','#form-new',function(event){
            event.preventDefault();
           // Get some values from elements on the page:
            var $form = $( this );
            var url       = $form.attr( "action" );
            // Send the data using post
            var posting = $.post( url, $form.serialize() ).done(function( data ) {
                if (data == 'OK'){
                  location.reload();
                } else {
                    alert(data);
                }
            });
        //    
        });

        function formatCoach (coach) {
          // console.log(coach);
          if (!coach.id) {
            return coach.text;
          }
          var class_css = 's_avail_' + coach.id + ' ';
          if (typeof window.availCoach != 'undefined' && typeof window.availCoach[coach.id] != 'undefined') 
            class_css += window.availCoach[coach.id];
            
          var $coach = $(
            '<span class="' + class_css  + '"><b class="cColors coach_' + coach.id + '" /> ' + coach.text + '</span>'
          );
          return $coach;
        };
        
        $(".js-select2-coach").select2({
          templateResult: formatCoach
        });


    });

</script>
 @if ($id > 0)
@include('admin.blocks.cardScripts')
 @endif
 <style>
   .tpayData{
     max-width: 320px;
     margin: 1em auto;
   }
   .tpayData table.table {
     border: 1px solid;
   }
   .tpayData table.table .success{
      border: 1px solid;
      text-align: center;
      background-color: #e0f5e9;
      font-size: 1.7em;
      font-weight: bold;
   }
   input#customTime {padding: 0px 4px;}
   #tit_user{
      width: 100% !important;
    }
    #tit_user span{
      float: right;
      padding-right: 11px;
      margin-top: -3px;
    }
    #tit_user span input{
      margin-right: 6px;
      height: 13px;
    }
    
    a.back{
      display: none;
    }

    .cColors{
      width: 10px;
      height: 10px;
      background-color: red;
      display: inline-block;
      border-radius: 50%;
    }
    span.select2-selection.select2-selection--single {
      padding: 7px;
    }
    span.s_disable {
    text-decoration: line-through;
}
    @foreach($tColors as $k=>$v)
     .coach_{{$k}} {background-color: {{$v}};}
    @endforeach

    @media (max-width: 780px) {  
        a.back {
        display: block;
        font-weight: bold;
        float: right;
        width: 31px;
        background-color: #6e6e6e;
        color: #FFF;
        text-align: center;
        font-size: 22px;
        border-radius: 6px;
      }
    }
 </style>
@endsection