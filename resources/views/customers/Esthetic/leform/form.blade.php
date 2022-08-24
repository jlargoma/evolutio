@extends('layouts.app')
@section('content')

<div>
  <div class="row titH1">
    <div class="col-md-6 text-left">LEFORM</div>
    <div class="col-md-6 text-right">GET FIT VILLAVICIOSA</div>
  </div>
  <hr>
  <h1>CUESTIONARIO CONSENTIMIENTO INFORMADO</h1>
  <h4>DIATERMIA-RADIOFRECUENCIA MONOPOLAR APLICACIONES ESTÉTICAS</h4>


  @if (isset($already))
  <div class="alert alert-success">
    Firmada
  </div>
  @else

  @if (session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
  @endif
  @if (session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
  @else

  
  <form action="/autorizacion-{{$type}}" method="post" style="margin: 1em auto;" id="formAutorizaciones">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="_code" value="<?php echo $code; ?>">
    <input type="hidden" name="_control" value="<?php echo $control; ?>">
    <div class="text-left">
      <div>
        <?php hQblock($data, 'Esthetic_leform_name', 'f1', 'Nombre Del Cliente'); ?>, con D.N.I. <?php hQblock($data, 'Esthetic_leform_dni','f2'); ?>

        <ul class="items-conc">
          <li>
            El suscrito declara y confirma que después de describir mis deseos y expectativas respecto al tratamiento de Radiofrecuencia y/o Vacuum, recibí explicaciones suficientes sobre el tratamiento y sus posibles efectos secundarios.
          </li>
          <li>
            Se me informó que la duración de la sesión dependerá de la zona corporal de tratamiento escogida.
          </li>
          <li>
            Se me informó que es posible la aparición de enrojecimiento en la zona tratada que desaparece al paso de las horas sin mayores consecuencias, pudiendo realizar cualquier actividad habitual.
          </li>
          <li>
            Se me advirtió explícitamente de que debo advertir al terapeuta mi exposición a rayos uva o solar directa en el mismo día de tratamiento o anteriores.
          </li>
          <li>
            Entiendo que el resultado y expectativas es variable debido a caracteres genéticos y dieta alimenticia. Por lo que seguiré los consejos del terapeuta, dietista o endocrino.
          </li>
          <li>
            Me comprometo a obedecer las indicaciones del profesional así en el tratamiento como en la recomendaciones posteriores entre sesiones.
          </li>
          <li>
            Comprendo que la ingesta de líquido es favorable a la eliminación del tejido graso tratado.
          </li>
        </ul>
      </div>
    </div>
    <div class="my-4">
      Fecha: <?php hQDate($data, 'Esthetic_leform_date'); ?>
    </div>
    <div class="bShadow">
      <h5>Firma:</h5>
      <input type="hidden" name="sign" id="sign" value="">
      <div class="sing-box">
        <canvas width="320" height="300" id="cSign"></canvas>
      </div>
      <button class="btn btn-danger" type="button" id="clearSign">
        <i class="fa fa-trash" aria-hidden="true"></i> Limpiar
      </button>
    </div>
    <div class="bShadow">
      <p>En caso de tratarse de un menor de edad, identificación y autorización de los padres o tutores y su aprobación a info@evolutio.fit</p>
      <div>
        Nombre: <?php hQblock($data, 'Esthetic_leform_tut_name', 'f1', 'Nombre Del Tutor'); ?>  D.N.I. <?php hQblock($data, 'Esthetic_leform_tut_dni','f2'); ?>
      </div>
      <h5>FIRMA TUTOR</h5>
      <input type="hidden" name="sign2" id="sign2" value="">
      <div class="sing-box">
        <canvas width="320" height="300" id="cSign2"></canvas>
      </div>
      <button class="btn btn-danger" type="button" id="clearSign2">
        <i class="fa fa-trash" aria-hidden="true"></i> Limpiar
      </button>
    </div>

  <button class="btn btn-success" type="button" id="sendForm">
    <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
  </button>
  </form>
</div>

@endif
@endif

@endsection
@section('scripts')

@include('customers.Esthetic.forms_styles')

@endsection