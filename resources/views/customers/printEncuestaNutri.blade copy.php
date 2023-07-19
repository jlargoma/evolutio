@extends('layouts.app')
<?php
$opts = [];
function printQuestions($quest,$data){
  $nro = 1;
  foreach($quest as $k=>$v){
    echo '<div class=""><label>'.$nro.'. '. $v.'</label>';
    echo (isset($data[$k]))? $data[$k] : ' --- ';
    echo '</div>';
     $nro++;
  }


}
?>
@section('content')
<h1>ENCUESTA NUTRICIÓN</h1>
<div class="text-left">
<div class="fromEncNutri">
  <h2>Datos personales</h2>
  <?php printQuestions($data['qstion1'],$data); ?>
  <h2>Datos laborales</h2>
  <?php printQuestions($data['qstion2'],$data); ?>
  <h2>Motivo de la consulta </h2>
  <?php printQuestions($data['qstion3'],$data); ?>
  <h2>Historia Ponderal </h2>
  <?php printQuestions($data['qstion4'],$data); ?>
  <h2>Datos clínicos </h2>
  <?php printQuestions($data['qstion5'],$data); ?>
  <h2>Historial Dietético </h2>
  <?php printQuestions($data['qstion6'],$data); ?>
  <h2>Temas digestivos</h2>
  <?php printQuestions($data['qstion7'],$data); ?>
  <h2>Si es mujer</h2>
  <?php printQuestions($data['qstion8'],$data); ?>
  <h2>Describir un día estándar en su semana y un día estándar en el fin de semana (Recuerdo 24h)</h2>
  <div class="">
     <?php  echo (isset($data["nutri2_q9_1"]))? $data["nutri2_q9_1"] : ' --- '; ?>
  </div>
  <h2>Antropometria</h2>
  <?php printQuestions($data['qstion10'],$data); ?>











  <?php $nro = 1; ?>
  @foreach($data['qstion1'] as $i=>$q)
  <div class="field">
    <label>{{$nro.'. '.$q}}</label>
    <?php
    if($i == 'nutri_q22') {
        ?>
        <table class="table">
          <tr>
            <td></td>
            <th class="text-center">Entre semana</th>
            <th class="text-center">Fines de semana</th>
          </tr>
          <tr>
            <th>Desayuno</th>
            <td><?php echo (isset($data['nutri_q22_1_1']))? $data['nutri_q22_1_1'] : ''; ?></td>
            <td><?php echo (isset($data['nutri_q22_2_1']))? $data['nutri_q22_2_1'] : ''; ?></td>
          </tr>
          <tr>
            <th>Comida</th>
            <td><?php echo (isset($data['nutri_q22_1_2']))? $data['nutri_q22_1_2'] : ''; ?></td>
            <td><?php echo (isset($data['nutri_q22_2_2']))? $data['nutri_q22_2_2'] : ''; ?></td>
          </tr>
          <tr>
            <th>Cena</th>
            <td><?php echo (isset($data['nutri_q22_1_3']))? $data['nutri_q22_1_3'] : ''; ?></td>
            <td><?php echo (isset($data['nutri_q22_2_3']))? $data['nutri_q22_2_3'] : ''; ?></td>
          </tr>
          <tr>
            <th>Snacks / Entrehoras</th>
            <td><?php echo (isset($data['nutri_q22_1_4']))? $data['nutri_q22_1_4'] : ''; ?></td>
            <td><?php echo (isset($data['nutri_q22_2_4']))? $data['nutri_q22_2_4'] : ''; ?></td>
          </tr>
        </table>
        <?php
    } else {
      echo (isset($data[$i]))? $data[$i] : '';
    }
    $nro++;
    ?>

  </div>
  @endforeach


</div>
  <div class="text-center">
  <button type="button" onclick="window.print();" class="btn btn-success">Imprimir</button>
  </div>
</div>
@endsection
@section('scripts')

<style type="text/css">

  .img-logo{
    width: 220px !important;
  }
    h1 {
    font-size: 24px;
    background-color: #f7f7f7;
    padding: 15px 0;
  }
  .fromEncNutri img{
    max-width: 100%;
  }

  .fromEncNutri .radio input[type="radio"] {
    margin-right: 10px;
  }
  .fromEncNutri .radio span {
    margin-top: 0;
  }

  .fromEncNutri .bold{
    text-align: center;
    font-weight: bold;
    color: #000;
    margin-bottom: 2em;
  }

  .fromEncNutri .field {
    margin: 1px 0px 10px !important;
    padding: 3px 8px;
    font-size: 13px;
    min-height: 2.1em;
  }
  .fromEncNutri label {
    display: block;
    font-size: 15px;
    font-weight: 600;
    margin: 14px 2px 4px;
  }
  .fromEncNutri .tqstion2 th{
    width: 25%;
    padding: 18px 5px !important;
    font-size: 13px !important;
  }
  .table td{
    text-align: center;
  }

  .fromEncNutri h2{
    color: #787878;
    font-size: 16px;
    font-weight: 600;
    border-bottom: 1px solid #787878;
    padding-bottom: 5px;
  }
   @media print {
    .btn-success,
    .fondo{
      display: none;
    }
    .content-box {
      max-width: 90%;
      box-shadow: none;
    }
    body{
      background-color: transparent;
    }
  }
</style>
@endsection