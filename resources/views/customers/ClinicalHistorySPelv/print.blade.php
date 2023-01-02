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


?>
@section('content')
<h1>Hª CLÍNICA SUELO PÉLVICO</h1>
<div class="text-left">
  <div class="fromEncNutri">
    @include('customers.ClinicalHistorySPelv.fields')
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