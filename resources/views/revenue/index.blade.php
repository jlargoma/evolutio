@extends('layouts.admin-master')
@section('title') Revenue - EVOLUTIO @endsection

<?php
function sumMonthValue($m)
{
    $t = 0;
    foreach ($m as $k => $v) {
        if (is_numeric($k)) {
            $t += $v;
        }
    }
    return moneda($t);
} 

?>
@section('content')
@include('admin.contabilidad._button-contabiliad')


<div class="content">
    <h2>Revenue</h2>

    <div class="row">
        <div class="col-md-5">
            <canvas id="chartTotalByYear" style="width: 100%; height: 250px;"></canvas>
            <div class="row">
                @foreach($byTemp as $y=>$d)
                <div class="col-md-4 m3">
                    <div class="resultTemp">
                    <h3>Temp {{$y}}</h3><br>
                    Ingresos: <b style="float: right;">{{moneda($d['ing'])}}</b><br>
                    Gastos: <b style="float: right;">{{moneda($d['exp'])}}</b><br>
                    <hr>

                    <h3>Resultado: <b>{{moneda($d['result'])}}</b></h3>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-5">
            
            <table class="table">
                <tr><th colspan="2">Resultado</th></tr>
                <tr><td>Ventas Temporada</td><td>{{moneda($byTemp[$year]['ing'])}}</td></tr>
                <tr><td>Pagado</td><td>{{$ratesCharges['pay']}}</td></tr>
                <tr><td>A Pagado</td><td>{{$ratesCharges['no_pay']}}</td></tr>
            </table>
            <canvas id="chartPayNoPay" style="width: 100%; height: 250px;"></canvas>
        </div>


        ratesCharges










    </div>




















    <div class="row">
        <div class="col-sm-6">
           
        </div>
        <div class="col-sm-6">
            <canvas id="chartTotalByMonth" style="width: 100%; height: 250px;"></canvas>
        </div>
        <div class="col-xs-12">
        </div>
    </div>
</div>






















@endsection

@section('scripts')
<link rel="stylesheet" href="{{ assetV('css/contabilidad.css') }}">
<script type="text/javascript" src="/admin-css/assets/js/plugins/chartJs/Chart.min.js"></script>
<script type="text/javascript" src="/admin-css/assets/js/charts.js"></script>
<script type="text/javascript">
    $(document).ready(function() {


        var myBarChart = new Chart('chartTotalByYear', {
        type: 'line',
        data: {
          labels: [
              <?php foreach ($byTemp as $k=>$v){ echo "'" . $k. "'," ;} ?>
          ],
          datasets: [
            {
              label: "Ingresos por Temp",
              backgroundColor: 'rgba(54, 162, 235, 0.2)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1,
              data: [
                  <?php foreach ($byTemp as $k=>$v){ echo "'" . round($v['result']). "'," ;} ?>
              ],
            }
          ]
          }
      });


    });
</script>
<style>
.resultTemp {
    background-color: #5c90d2;
    color: #fff;
    padding: 11px;
}
.resultTemp h3 {
    margin-bottom: 7px;
}
</style>
@endsection