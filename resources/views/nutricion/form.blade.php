@extends('layouts.popup')
@section('content')
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@if($blocked)<!-- es un bloqueo -->
<div class="col-xs-12">
  <h2 class="text-center">Bloqueo de Fecha</h2>
  <div class="row">
    <form action="{{ url('/admin/citas/create') }}" method="post" id="formEdit">
      <input type="hidden" name="idDate" value="{{$id}}">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="date_type" value="nutri">
      <input type="hidden" name="blocked" value="1">
      <div class="row">
        <div class="col-xs-1 col-md-3  push-20"></div>
        <div class="col-xs-3 col-md-2  push-20">
          <label for="date">Fecha</label>
          <input class="js-datepicker form-control" value="{{$date}}" type="text" id="date" name="date" placeholder="Fecha y hora..." style="cursor: pointer;" data-date-format="dd-mm-yyyy"/>
        </div>
        <div class="col-xs-3 col-md-2 not-padding  push-20">
          <label for="id_user">hora</label>
          <select class="form-control" id="hour" name="hour" style="width: 100%;" data-placeholder="hora" required >
            <?php for ($i = 8; $i <= 22; $i++) : ?>
              <?php
              if ($i < 10) {
                $hour = "0" . $i;
              } else {
                $hour = $i;
              }
              ?>
              <option value="<?php echo $hour ?>" <?php if ($time == $i) echo 'selected'; ?>>
                <?php echo $hour ?>: 00
              </option>
            <?php endfor; ?>

          </select>
        </div>
        <div class="col-xs-3 col-md-2 push-20">
          <label for="id_coach">Nutricionista</label>
          <select class="js-select2 form-control" id="id_coach" name="id_coach" style="width: 100%; cursor: pointer" data-placeholder="Seleccione coach.." >
            <option></option>
            <?php foreach ($coachs as $key => $coach): ?>
              <option value="<?php echo $coach->id; ?>" <?php if (isset($id_coach) && $id_coach == $coach->id) echo 'selected' ?>>
                <?php echo $coach->name; ?>
              </option>
            <?php endforeach ?>
          </select>

        </div>
        <div class="col-xs-2 col-md-3  push-20"></div>
      </div>
    </form>
    <div class=" col-xs-12 form-group push-20">
      <div class="col-xs-12 text-center">
        <button class="btn btn-lg btn-success sendForm" data-id="formEdit"  type="button" >
          Guardar
        </button>
        @if($id>0)   
        <button class="btn btn-lg btn-danger btnDeleteCita" type="button">
          Eliminar
        </button>
        @endif
      </div>
    </div>
    <hr/>
  </div>
</div>
@else
  @include('nutricion.editDate')
@endif
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
           location.href = '/admin/usuarios/informe/' + id;

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