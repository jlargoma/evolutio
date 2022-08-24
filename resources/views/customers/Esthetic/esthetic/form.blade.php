@extends('layouts.app')
@section('content')

<div>
  <div class="row titH1">
    <div class="col-md-6 text-left">SHR</div>
    <div class="col-md-6 text-right">GET FIT VILLAVICIOSA</div>
  </div>
  <hr>
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
        <?php hQblock($data, 'Esthetic_esthetic_name', 'f1', 'Nombre Del Cliente'); ?>, con D.N.I. <?php hQblock($data, 'Esthetic_esthetic_dni','f2'); ?>

        <ul class="items-conc">
          <li>
          El suscrito declara y confirma que después de describir mis deseos y expectativas respecto a la depilación o foto rejuvenecimiento con SHR, recibí explicaciones suficientes sobre el tratamiento y sus posibles aspectos secundarios.
          </li>
          <li>
          GET FIT S.L me informó que la duración del tratamiento depende del tamaño la ubicación del área a tratar y que el periodo entre una sesión y otra variará dependiendo del área tratada y como resultado de las diferentes fases de crecimiento del pelo en las diferentes partes del cuerpo, así como la posible sensibilidad a variaciones hormonales.
          </li>
          <li>
          Se me explicó que es posible que sienta ardor en la zona tratada después del tratamiento y en algunos casos pueden producirse enrojecimientos o falta de pigmentación, que desaparecerá en breve periodo de tiempo.
          </li>
          <li>
          Así mismo se me explicó que debido a que el equipo utilizado abarca una zona determinada, puede suceder que un tratamiento una parte de los pelos no reciba todo el tratamiento en cuyo caso sean necesarios tratamientos posteriores.
          </li>
          <li>
          Se me advirtió explícitamente que debo llevar puestas las gafas protectoras durante todo el tratamiento y que no debo mirar directamente al cabezal aplicador.
          </li>
          <li>
          Entiendo que el resultado posible es la eliminación permanente o de muy larga duración del pelo, y que el número de sesiones dependerá de la evolución y características de mi tipo de piel y de pelo
          </li>
          <li>
          Me comprometo a obedecer las indicaciones del operador técnico así en el tratamiento como en las indicaciones posteriores entre sesiones.
          </li>
          <li>
          Entiendo que no puedo depilar la zona tratada entre sesiones, sólo rasurar o cortar con tijera.
          </li>
          <li>
          Conozco la imposibilidad de exponer mi piel directamente al sol o U.V.A 72 horas antes y después del tratamiento con SHR (obedeceré las indicaciones de la operadora). Siempre usando un protector con factor total.
          </li>

        </ul>
      </div>
    </div>
    <div class="my-4">
      Fecha: <?php hQDate($data, 'Esthetic_esthetic_date'); ?>, Hora: <?php hQblock($data, 'Esthetic_esthetic_time','f2'); ?>
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
        Nombre: <?php hQblock($data, 'Esthetic_esthetic_tut_name', 'f1', 'Nombre Del Tutor'); ?>  D.N.I. <?php hQblock($data, 'Esthetic_esthetic_tut_dni','f2'); ?>
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