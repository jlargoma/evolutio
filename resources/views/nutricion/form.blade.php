@extends('layouts.popup')

@section('content')
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<?php 
$date_type_u = "Nutricionista";
$date_type = 'nutri'
?>
@if($blocked)<!-- es un bloqueo -->
  @include('calendars.editBlock')
@else
  @include('calendars.editDate')
@endif

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
            $('#'+$(this).data('id')).submit();
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
        @endif

        $('.btn-user').click(function (e) {
          e.preventDefault();
          var id = $(this).attr('data-idUser');
          $('#ifrCliente').attr('src','/admin/usuarios/informe/' + id);
          $('#modalCliente').modal('show');
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
 </style>
@endsection