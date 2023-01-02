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
        <div class="col-left">LEFORM</div>
        <div class="col-rigth ">GET FIT VILLAVICIOSA</div>
      </div>
      <hr>
      <h1>CUESTIONARIO CONSENTIMIENTO INFORMADO</h1>
      <h2>DIATERMIA-RADIOFRECUENCIA MONOPOLAR APLICACIONES ESTÉTICAS</h2>

      <div class="text-left">
        <div>
          <p>
          <?php Qblock($data, 'Esthetic_leform_name'); ?>, con D.N.I. <?php Qblock($data, 'Esthetic_leform_dni'); ?>
          </p>
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
        Fecha: <?php Qblock($data, 'Esthetic_leform_date'); ?>
        <br/><br/><br/>
      </div>
      <div class="bShadow">
        <h5>FIRMA</h5>
        <img src="data:image/png;base64,<?= $signMain ?>" class="sing">
      </div>
      @if (isset($data['Esthetic_leform_tut_name']) && !empty($data['Esthetic_leform_tut_name']))
      <div class="bShadow">
        <p>En caso de tratarse de un menor de edad, identificación y autorización de los padres o tutores y su aprobación a info@evolutio.fit</p>
        <div>
          Nombre: <?php Qblock($data, 'Esthetic_leform_tut_name'); ?> D.N.I. <?php Qblock($data, 'Esthetic_leform_tut_dni'); ?>
        </div>
        <h5>PADRE/MADRE o TUTOR</h5>
        <img src="data:image/png;base64,<?= $signTutor ?>" class="sing">
      </div>
      @endif
    </div>
  </div>
  @endsection