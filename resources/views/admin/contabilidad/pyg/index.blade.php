@extends('layouts.admin-master')
@section('title') P&G- EVOLUTIO  @endsection
<?php 
function sumMonthValue($m){
  $t=0;
  foreach ($m as $k=>$v){
    if (is_numeric($k)){
      $t += $v;
    }
  }
  return ($t);
}?>
@section('content')
@include('admin.contabilidad._button-contabiliad')
<div class="content">
  <h2>Perdidas & Ganancias</h2>

  <div class="row">

    <div class="col-md-4 col-xs-12">
      <canvas id="chart_incomesYear" style="width: 100%; height: 250px;"></canvas>
    </div>
    <div class="col-md-4 col-xs-12">
      <canvas id="chart_expensesYear" style="width: 100%; height: 250px;"></canvas>
    </div>
    <div class="col-md-4 col-xs-12">
      <canvas id="chartTotalByMonth" style="width: 100%; height: 250px;"></canvas>
    </div>
    <div class="col-md-4 col-xs-6">
      <div class="box"><h2>Ingresos {{$year}}</h2><span>{{moneda($incomesYear[$year])}}</span></div>
    </div>
    <div class="col-md-4 col-xs-6">
      <div class="box"><h2>Gastos {{$year}}</h2><span>{{moneda($expensesYear[$year])}}</span></div>
    </div>
    <div class="col-md-4 col-xs-12">
      <?php $result = $incomesYear[$year]-$expensesYear[$year]; ?>
      <div class="box">
        <h2>Resultado {{$year}}</h2>
        <span>{{moneda($result)}}
          @if($result<0 )
          <i class="fa fa-arrow-down text-danger"></i>
          @else
          <i class="fa fa-arrow-up text-success"></i>
          @endif
        </span></div>
    </div>
    <div class="col-lg-3 col-md-6 col-xs-6 col-smal">
      <div class="box"><h2>Suscripciones activas</h2><span>{{$subscs}}</span></div>
      <div class="box"><h2>Clientes activos</h2><span>{{$uActivs}}</span></div>
    </div>
    <div class="col-lg-3 col-md-6 col-xs-6 chart_1">
      <canvas id="chart_1" width="50" height="50"></canvas>
    </div>
    <div class="col-xs-12">
@include('admin.contabilidad.pyg.table')
    </div>
  </div>
</div>

<div class="modal fade" id="modalInfo" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="block block-themed block-transparent remove-margin-b">
        <div class="block-header bg-primary-dark">
          <ul class="block-options">
            <li>
              <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
            </li>
          </ul>
        </div>
        <div class="row block-content" id="contentModalInfo">

        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
  @include('admin.contabilidad.pyg.scripts')

<style>
  .box {
    border: 2px solid #5c90d2;
    margin: 1em;
    padding: 1em;
    text-align: center;
    border-radius: 29px;
}
.box span {
    font-size: 31px;
}
tr.subtotal td {
    background-color: #d6d6d6;
    font-weight: 700;
}
tfoot tr td {
    background-color: #5c90d2;
    font-weight: bold;
    color: #FFF;
    font-size: 1.3em;
}
@media(min-width:430px) {
  .chart_1{
    margin-top: -55px;
  }
}
@media(max-width:480px) {
  .chart_1{width: 100%;}
  .col-smal{width: 100%;}
  .col-smal .box{width: 45%; float: left;margin: 2%;}
  .box {
    padding: 1em 0px;
    margin: 1em auto;
    min-height: 9em;
  }
  .box h2{
    font-size: 14px;
  }
}
</style>
@endsection
