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
        <div class="col-left">SHR</div>
        <div class="col-rigth ">GET FIT VILLAVICIOSA</div>
      </div>
      <hr>
      <div class="text-left">
        <div>
          <p>
          <?php Qblock($data, 'Esthetic_esthetic_name'); ?>, con D.N.I. <?php Qblock($data, 'Esthetic_esthetic_dni'); ?>
          </p>
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
        Fecha: <?php Qblock($data, 'Esthetic_esthetic_date'); ?>, Hora: <?php Qblock($data, 'Esthetic_esthetic_time'); ?>
        <br/><br/><br/>
      </div>
      <div class="bShadow">
        <h5>FIRMA</h5>
        <img src="data:image/png;base64,<?= $signMain ?>" class="sing">
      </div>
      @if (isset($data['Esthetic_esthetic_tut_name']) && !empty($data['Esthetic_esthetic_tut_name']))
      <div class="bShadow">
        <p>En caso de tratarse de un menor de edad, identificación y autorización de los padres o tutores y su aprobación a info@evolutio.fit</p>
        <div>
          Nombre: <?php Qblock($data, 'Esthetic_esthetic_tut_name'); ?> D.N.I. <?php Qblock($data, 'Esthetic_esthetic_tut_dni'); ?>
        </div>
        <h5>PADRE/MADRE o TUTOR</h5>
        <img src="data:image/png;base64,<?= $signTutor ?>" class="sing">
      </div>
      @endif
    </div>
  </div>
  @endsection