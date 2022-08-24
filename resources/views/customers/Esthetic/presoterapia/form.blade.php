@extends('layouts.app')
@section('content')

<div>
  <div class="row titH1">
    <div class="col-md-6 text-left">PRESOTERAPIA</div>
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
<p>La presoterapia Consiste en el inflado y desinflado secuencial de las diferentes prendas segmentadas que lo componen.</p>
<p>El llenado y vaciado aplica una presión de manera gradual desde la planta del pie hacia los ganglios linfáticos. Favoreciendo así la activación del sistema circulatorio y la eliminación de líquido y toxinas El número de sesiones de sesiones depende completamente de cada paciente y del problema que se desee tratar. </p>
<p>Contraindicaciones: No se permite el uso a quienes sufran de trombosis, insuficiencia cardíaca, marcapasos, cáncer, hipo o hipertensión e infecciones cutáneas. Se permite el uso a embarazadas en piernas, si el tratamiento está aprobado por su médico. </p>
<p>Efectos secundarios :Rojez en la zona tratada, picor y necesidad de orinar inmediatamente después. Son efectos totalmente esperados y no dañinos para la salud.</p>
      </div>
   
    <div class="my-4 text-center">
    He sido informado y acepto la realización del tratamiento.
    </div>
    <div class="my-4">
      Nombre: <?php hQblock($data, 'Esthetic_presoterapia_name', 'f1', 'Nombre Del Cliente'); ?><br/>
      D.N.I: <?php hQblock($data, 'Esthetic_presoterapia_dni','f2'); ?><br/>
    </div>
    <div class="my-4">
      Fecha: <?php hQDate($data, 'Esthetic_presoterapia_date'); ?><br/>
    </div>
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