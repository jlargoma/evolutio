@extends('layouts.pdf')

@section('title') Contratos @endsection
<?php
function Qblock($data, $qID)
{
  if (isset($data[$qID])) echo $data[$qID];
}

?>
@section('styles')
@include('customers.Esthetic.prints_styles')
@endsection
@section('content')

<div class="contratoBox">
  <img src="https://desarrollo.evolutio.fit/img/logocontrato.png" class="img-logo">
  <div class="body">

    <div>
      <div class="row titH1">
        <div class="col-left">PRESOTERAPIA</div>
        <div class="col-rigth ">GET FIT VILLAVICIOSA</div>
      </div>
      <hr>
      <div class="text-left">
        <div>
          <p>La presoterapia Consiste en el inflado y desinflado secuencial de las diferentes prendas segmentadas que lo componen.</p>
          <p>El llenado y vaciado aplica una presión de manera gradual desde la planta del pie hacia los ganglios linfáticos. Favoreciendo así la activación del sistema circulatorio y la eliminación de líquido y toxinas El número de sesiones de sesiones depende completamente de cada paciente y del problema que se desee tratar. </p>
          <p>Contraindicaciones: No se permite el uso a quienes sufran de trombosis, insuficiencia cardíaca, marcapasos, cáncer, hipo o hipertensión e infecciones cutáneas. Se permite el uso a embarazadas en piernas, si el tratamiento está aprobado por su médico. </p>
          <p>Efectos secundarios :Rojez en la zona tratada, picor y necesidad de orinar inmediatamente después. Son efectos totalmente esperados y no dañinos para la salud.</p>
        </div>
      </div>
      <div class="my-4 text-center">
        He sido informado y acepto la realización del tratamiento.
      </div>
      <div class="bShadow">
          <img src="data:image/png;base64,<?= $signMain ?>" class="sing">
        </div>
      <div class="my-4">
        Nombre: <?php Qblock($data, 'Esthetic_presoterapia_name'); ?><br />
        D.N.I: <?php Qblock($data, 'Esthetic_presoterapia_dni'); ?><br />
        <div class="my-4">
          Fecha: <?php Qblock($data, 'Esthetic_presoterapia_date'); ?>
          <br /><br /><br />
        </div>
        
      </div>
    </div>
  </div>
  @endsection