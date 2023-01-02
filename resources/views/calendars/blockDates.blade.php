@extends('layouts.popup')
@section('content')
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ assetV('css/custom.css') }}">
<div class="col-xs-12">
  <h2 class="text-center">Bloqueo / Desbloqueo de Fechas</h2>
  <div class="row">
    <form action="{{ url('/admin/citas/bloqueo-horarios') }}" method="post" id="formEdit">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="date_type" value="{{$type}}">
      <input type="hidden" name="blocked" value="1">
      <div class="row">
        <div class="col-xs-12 likeOption">
          <?php $old = old('type_action', 'block'); ?>
          <input type="hidden" name="type_action" id="type_action" value="<?php echo $old; ?>">
          <button  data-v="block"  type="button" <?php if ($old == 'block') echo 'class="active"'; ?>>Bloquear</button>
          <button  data-v="unblock"  type="button" <?php if ($old == 'unblock') echo 'class="active"'; ?>>Desbloquear</button>

        </div>
        <div class="col-xs-12 col-md-12 push-20">
          <label for="id_coach">Usuarios</label>
          <div class="ck-box ">
            <?php foreach ($coachs as $key => $coach): ?>
              <div class="ck-button type2">
                <label>
                  <input type="checkbox" name="user_ids[]" value="<?php echo $key ?>"><span><?php echo $coach ?></span>
                </label>
              </div>
            <?php endforeach ?>
          </div>
        </div>
        <div class="col-xs-3 col-md-2  push-20">
          <label for="date">Fecha de Inicio</label>
          <input class="js-datepicker form-control" type="text" id="start" name="start" placeholder="Fecha y hora..." style="cursor: pointer;" data-date-format="dd-mm-yyyy"/>
        </div>
        <div class="col-xs-3 col-md-2  push-20">
          <label for="date">Fecha de Finalización</label>
          <input class="js-datepicker form-control" type="text" id="end" name="end" placeholder="Fecha y hora..." style="cursor: pointer;" data-date-format="dd-mm-yyyy"/>
        </div>
        <div class="col-xs-3 col-md-2  push-20">
          <label for="date">Motivo</label>
          <input class="form-control" type="text" id="motive" name="motive" placeholder="Motivo..."/>
        </div>

        <div class="col-xs-12 ck-box push-20">

          <div class="ck-box ">
            <?php foreach ($aWeeks as $k => $v): ?>
              <div class="ck-button type2">
                <label>
                  <input type="checkbox" name="wDay[]" value="<?php echo $k ?>"><span><?php echo $v ?></span>
                </label>
              </div>
            <?php endforeach ?>
          </div>
        </div>
        <div class="col-xs-12 ck-box push-20">
          <h3>Hora</h3>
          <?php for ($i = 8; $i <= 22; $i++) : ?>
            <?php
            if ($i < 10) {
              $hour = "0" . $i;
            } else {
              $hour = $i;
            }
            ?>
            <div class="ck-button">
              <label>
                <input type="checkbox" name="hours[]" value="<?php echo $hour ?>"><span><?php echo $hour ?>: 00</span>
              </label>
            </div>

          <?php endfor; ?>
        </div>
        <div class="col-xs-12 text-center">
          <button class="btn btn-lg btn-success sendForm">
            Bloquear
          </button>
        </div>
      </div>
      <p class="text-center">
        <small>Si no selecciona <b>Fecha de Finalización</b>, se tomará el día del campo <b>Fecha de Inicio</b><br/>
        Si no selecciona ningún <b>día de la semana</b>, se tomarán todos los días del rango seleccionado<br/>
        Si no selecciona ninguna <b>hora del día</b>, se tomará el día completo</small>
      </p>
    </form>
  </div>
</div>
@endsection
@section('scripts')
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datetimepicker/moment.min.js')}}"></script>
<script type="text/javascript">
jQuery(function () {
    App.initHelpers(['datepicker']);
});

$(document).ready(function () {

    $('.likeOption').on('click', 'button', function (e) {
        $('.likeOption').find('button').removeClass('active');
        var value = $(this).data('v');
        $(this).addClass('active');
        $('#type_action').val(value);
        $('.sendForm').text($(this).text());

    });
});
</script>
<style>

  .ck-box {
    position: relative;
    clear: both;
    overflow: auto;
  }
  .ck-button {
    margin:4px;
    background-color:#EFEFEF;
    border-radius:4px;
    border:1px solid #D0D0D0;
    overflow:auto;
    float:left;
  }

  .ck-button label {
    float:left;
    width:4.0em;
    margin: 1px;
  }
  .ck-button.type2 label {
    width:auto;
    margin: 1px;
  }

  .ck-button label span {
    text-align:center;
    padding:3px 0px;
    display:block;
    border-radius:4px;
  }
  .ck-button.type2 label span{
    padding:2px 8px;
  }

  .ck-button label input {
    position:absolute;
    top:-20px;
  }

  .ck-button input:hover + span {
    background-color:#efE0E0;
  }

  .ck-button input:checked + span {
    background-color:#911;
    color:#fff;
  }

  .ck-button input:checked:hover + span {
    background-color:#c11;
    color:#fff;
  }
</style>
@endsection