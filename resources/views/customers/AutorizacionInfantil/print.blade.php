@extends('layouts.pdf')

@section('title') Contratos @endsection
<?php
function Qblock($data, $qID)
{
  if (isset($data[$qID])) echo $data[$qID];
}

?>
@section('styles')
<style type="text/css">
  .img-logo {
    width: 290px;
    margin-bottom: 50px;
    margin-top: -50px;
  }
  h5 {
    margin: 0;
  }
  .contratoBox {
    max-width: 860px;
    margin: 50px auto;
    font-size: 12px;
    padding: 5px 10px 15px 40px;
    background-color: #FFF;
    text-align: center;
  }

  .rateCalendar .item {
    width: 200px;
  }

  .sing-box {
    width: 80px;
    padding: 5px;
    margin: 0px auto;
  }

  .sing-box p {
    width: 100%;
    margin: 0;
    text-align: center;
  }

  .sing-box img {
    max-width: 100%;
  }

  ul {
    padding-left: 15px;
  }

  li {
    padding-bottom: 10px;
  }

  .contratoBox h1 {
    font-size: 24px;
    padding: 25px 0 0;
    margin: 0;
    font-weight: bold;
    text-decoration: underline;
    text-align: center;
  }

  .contratoBox h2 {
    font-size: 16px;
    padding: 15px 0 0;
    margin: 0;
    font-weight: bold;
    text-align: center;
  }

  .contratoBox h3 {
    margin-bottom: 14px;
    font-size: 14px;
    text-align: center;
    font-weight: bold;
  }

  .contratoBox .body,
  .contratoBox .body p {
    font-size: 11px;
    /*text-indent: 5px;*/
    text-align: justify;
  }

  table.table-sing {
    width: 100%;
    padding: 1em;
    margin: 2em 0 3em;
    text-align: center;
  }

  .table-sing td {
    width: 49%;
  }

  .table-sing .sing-box {
    border: 1px solid #c3c3c3;
    padding: 7px;
    width: 85%;
    margin: 1em auto;
  }
</style>
@endsection
@section('content')

<div class="contratoBox">
  <img src="https://desarrollo.evolutio.fit/img/logocontrato.png" class="img-logo">
  <div class="body">


    <div class="fromEncNutri">
      <p>En <?php Qblock($data, 'autoriz_lugar'); ?>, a <?php Qblock($data, 'autoriz_dias_dia', 'f2'); ?> de <?php Qblock($data, 'autoriz_dias_mes', 'f2'); ?> de <?php Qblock($data, 'autoriz_dias_anio', 'f2'); ?></p>
      <br /><br />
      <p>D/Dª <?php Qblock($data, 'autoriz_tutor'); ?> con NIF nº <?php Qblock($data, 'autoriz_tutorNIF', 'f2'); ?>, como PADRE/MADRE o TUTOR, autoriza a asistir al centro deportivo
        EVOLUTIO.FIT y hacer uso de sus instalaciones y actividades a su hijo/a <?php Qblock($data, 'autoriz_menor'); ?></p>
      <p>Menor de edad y con NIF nº <?php Qblock($data, 'autoriz_menorNIF', 'f2'); ?> y asume las obligaciones contractuales
        pactadas en el contrato que también se firma el mismo día de fecha <?php Qblock($data, 'autoriz_ini_dia', 'f2'); ?> de <?php Qblock($data, 'autoriz_ini_mes', 'f2'); ?> de <?php Qblock($data, 'autoriz_ini_anio', 'f2'); ?>.</p>
    </div>
    <br /><br /><br />
      <table class="table-sing">
        <tr>
          <td>
            <h5>PADRE/MADRE o TUTOR</h5>
            <img src="data:image/png;base64,<?= $signTutor ?>">
          </td>
          <td>
            <h5>FIRMA GERENTE</h5>
            <img src="data:image/png;base64,<?= $signGerente ?>">
          </td>
        </tr>
        <tr>
          <td>Fdo. <?php Qblock($data, 'autoriz_tutorFdo', 'f2'); ?></td>
          <td>Fdo. <?php Qblock($data, 'autoriz_gerenteFdo', 'f2'); ?></td>
        </tr>
      </table>

  </div>
  @endsection