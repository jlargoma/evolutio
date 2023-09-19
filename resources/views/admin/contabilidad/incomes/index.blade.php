@extends('layouts.admin-master')
@section('title') RESUMEN  INGRESOS POR  LINEA DE  NEGOCIO - EVOLUTIO  @endsection

<?php 
function sumMonthValue($m){
  $t=0;
  foreach ($m as $k=>$v){
    if (is_numeric($k)){
      $t += $v;
    }
  }
  return moneda($t);
}?>
@section('content')
@include('admin.contabilidad._button-contabiliad')

<div class="content">
<h2>Ingresos 
  <button type="button" class="btn btn-success" id="addNew_ingr" type="button" data-toggle="modal" data-target="#modalAddNew"><i class="fa fa-plus-circle"></i> Añadir</button>
  <button type="button" class="btn btn-info" type="button" data-toggle="modal" data-target="#modalLst"><i class="fa fa-eye"></i>  Listado</button>
</h2>

<div class="row">
  
  <div class="col-sm-6">
    <canvas id="chartTotalByYear" style="width: 100%; height: 250px;"></canvas>
  </div>
  <div class="col-sm-6">
      <canvas id="chartTotalByMonth" style="width: 100%; height: 250px;"></canvas>
  </div>
  <div class="col-xs-12">
  @include('admin.contabilidad.incomes.table')
  </div>
</div>
  </div>


  <div class="modal fade" id="modalAddNew" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <strong class="modal-title" id="modalChangeBookTit" style="font-size: 1.4em;">Añadir Ingreso</strong>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">@include('admin.contabilidad.incomes._form_new')</div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalLst" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <strong class="modal-title" id="modalChangeBookTit" style="font-size: 1.4em;"> Listado de Ingresos Manuales de <b><?php echo $year ?></b></strong>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">@include('admin.contabilidad.incomes._lst_Incomes')</div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
<link rel="stylesheet" href="{{ assetV('css/contabilidad.css') }}">
<script type="text/javascript" src="/admin-css/assets/js/plugins/chartJs/Chart.min.js"></script>
<script type="text/javascript" src="/admin-css/assets/js/charts.js"></script>
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
  <script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datetimepicker/moment.min.js')}}"></script>
<?php
  $tYearMonths = '';
    $aux = '';
    $ij = 0;
     foreach ($byYears as $Y=>$monthData){
      $aux .= '{
              data: [';
      foreach ($monthData as $k=>$v){
        if ($k>0) $aux .= "'" . round($v) . "',";
      }
      
      $aux .= '],
              label: "Año '.$Y.'",
              borderColor: "'.printColor($ij).'",
              fill: false
            },';
      $ij++;
    }
    $tYearMonths = $aux;
?>
        
<script type="text/javascript">
    $(document).ready(function () {

      App.initHelpers(['datepicker']);

      $('.d1').on('click',function(){
        var k = $(this).data('k');
        
        $('.d1_'+k).each(function(){
          if ($(this).css('display') != 'none'){
            var k = $(this).data('k');
            
            $('.d2_'+k).each(function(){
              if ($(this).css('display') != 'none'){
                var k = $(this).data('k');
                $('.d3_'+k).each(function(){
                  if ($(this).css('display') != 'none'){
                    var k = $(this).data('k');
                    $('.d4_'+k).hide();
                  }
                });
                $('.d3_'+k).hide();
              }
            });
            $('.d2_'+k).hide();
          }
        });
        
        $('.d1_'+k).toggle();
      });
      $('.d2').on('click',function(){
        var k = $(this).data('k');
        
        $('.d2_'+k).each(function(){
          if ($(this).css('display') != 'none'){
            var k = $(this).data('k');
            $('.d3_'+k).each(function(){
              if ($(this).css('display') != 'none'){
                var k = $(this).data('k');
                $('.d4_'+k).hide();
              }
            });
            $('.d3_'+k).hide();
          }
        });
        
        $('.d2_'+k).toggle();
      });
      $('.d3').on('click',function(){
        var k = $(this).data('k');
        $('.d3_'+k).toggle();
      });
      
      
      
      
       new Chart(document.getElementById("chartTotalByMonth"), {
        type: 'line',
        data: {
          labels: [
            <?php foreach ($monts as $v) echo "'" . $v . "',";?>
          ],
          datasets: [ <?php echo $tYearMonths;?>]
        },
        options: {
          title: {
            display: true,
            text: 'Total x Año'
          }
        }
      });
      
      var myBarChart = new Chart('chartTotalByYear', {
        type: 'bar',
        data: {
          labels: [
              <?php foreach ($tByYears as $k=>$v){ echo "'" . $k. "'," ;} ?>
          ],
          datasets: [
            {
              label: "Ingresos por Temp",
              backgroundColor: 'rgba(54, 162, 235, 0.2)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1,
              data: [
                  <?php foreach ($tByYears as $k=>$v){ echo "'" . round($v). "'," ;} ?>
              ],
            }
          ]
          }
      });
      
      
      
      
    $('#modalAddNew').on('submit', '#formNewIncomes', function (e) {
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serializeArray(),
        success: function (response) {
          if (response == 'ok') {
            $('#import').val('');
            $('#concept').val('');
            $('#comment').val('');
            alert('Ingreso Agregado');
          } else
            alert(response);
        }
      });
    });
      
    $('#modalAddNew').on('click', '#reload', function (e) {
      location.reload();
    });
      
    $('#modalLst').on('click', '.filtIncome', function (e) {
      var month = $(this).data('k');
      $('.filtIncome').removeClass('active');
      $(this).addClass('active');
      console.log(month);
      if (month<1){
        $('.incomesMonths').show();
      } else {
        $('.incomesMonths').hide();
        $('.im_'+month).show();
      }
    });
      
      
      
    });
</script>
<style>
  .filtIncome{
    cursor: pointer;
  }
  .filtIncome.active {
    border: 1px solid #0046a0;
    background-color: #0067ea !important;
  }
  </style>
@endsection
