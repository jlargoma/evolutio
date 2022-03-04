@extends('layouts.app')
<?php
global $responses;
$responses = $resp;

function Qblock($data, $qID) {
  global $responses;
  echo "<label>$data[$qID]:</label>";
  if (isset($responses[$qID]))
    echo $responses[$qID];
  else
    echo '<b>-</b>';
}

function QDate($data, $qID) {
  return Qblock($data, $qID);
}

function QTextarea($data, $qID) {
  global $responses;
  echo "<label>$data[$qID]:</label>";
  echo '<p class="text">' . (isset($responses[$qID]) ? nl2br($responses[$qID]) : '-') . '</p>';
}

function QblockOpt($data, $qID, $qIDsub) {
  global $responses;
  $resp = isset($responses[$qID]) ? $responses[$qID] : '';
  ?>
  <h4>{{$data[$qID]}}</h4>
  <div class="field f4_1">
    <div class="radios">
      <span style="margin-right: 12px;" <?php if ($resp == 'SI') echo 'class="selected"' ?>>SI</span>
      <span <?php if ($resp == 'NO') echo 'class="selected"' ?>>NO</span>
    </div>
  </div>
  <div class="field f4_2">
    <label>{{$data[$qIDsub]}}</label>
    <?= isset($responses[$qIDsub]) ? $responses[$qIDsub] : '-'; ?>
  </div>
  <?php
}

function printPainImg() {
  global $responses;
  ?>
  <h4>Colorea todas las zonas en las que Usted siente dolor</h4>
  <div class="canvasBox">
    <div class="pain_img-box">
      <img src="/img/hClinic.jpg" class="backgroundImg">
      <div class="canvasImg">
          @if(isset($responses) && $responses['hclinic_PainImg'])
          <img src="/seeImg{{$responses['hclinic_PainImg']}}">
          @endif
      </div>
    </div>
  </div>
  <?php
}

function QblockOption($data, $qID, $opcion) {
  global $responses;
  $resp = isset($responses[$qID]) ? $responses[$qID] : '';
  ?>
  <label>{{$data[$qID]}}</label>
  <?php
  foreach ($opcion as $q):
    ?>
    <div class="radios">
      <span <?php if ($resp == $q) echo 'class="selected"' ?>>{{$q}}</span>
    </div>
    <?php
  endforeach;
}

function qLstOptions($data, $qID, $qID2,$opcion) {
  global $responses;
  $resp = null;
  if (isset($responses[$qID]))
    $resp = json_decode($responses[$qID], false);
  if (!$resp) $resp = [];
  if (!is_array($resp)) $resp = [$resp];
  ?>
  <label>{{$data[$qID]}}</label>
  <?php
  foreach ($opcion as $k=>$q):
    ?>
    <div class="radios">
      <span <?php if (in_array($k, $resp)) echo 'class="selected"' ?>>{{$q}}</span>
    </div>
  <?php endforeach; ?>
  <div class="otros">
    <?php echo Qblock($data, $qID2); ?>
  </div>
  <?php
}
?>
@section('content')
<h1>Hª CLINICA</h1>
<div class="text-left">
  <div class="fromEncNutri">
    @include('customers.ClinicalHistory.fields')
    <div class="text-center">
      <button type="button" onclick="window.print();" class="btn btn-success">Imprimir</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')

<style type="text/css">

  .fromEncNutri label{
    font-weight: bold !important;
  }
  .canvasBox{
    width: 350px;
    height: auto;
    max-width: 100%;
    margin: 1em auto 2em;
    height: 220px;
  }
  
  
  .pain_img-box{
    position:relative;
  }
.backgroundImg {
    position: absolute;
    left: 0;
    top: 0;
    z-index: 1;
}

  .pain_img-box {
    width: 320px;
  }
  .canvasText{
    width: 220px;
  }
  span.selected {
    border: 2px solid #00ad0e;
    padding: 4px 8px;
    border-radius: 50%;
  }
  .otros {
    font-weight: 400 !important;
    font-size: 12px;
    margin-top: 4px;
  }
  p.text {
    font-size: 12px;
  }
  @if(isset($resp) && $resp['hclinic_PainImg'])
  .canvasImg{
    width: 320px;
    background-size: contain;
    height: 197px;
    background-repeat: no-repeat;
    position: absolute;
    z-index: 2;
    top: 0;
    left: 0;
  }
  @endif


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