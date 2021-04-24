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

<style>
  table tr th,
  table tr td{
    text-align: center;
  }
  table tr th{
    background-color: #5c90d2;
    color: #FFF;
  }
  table tr td{
    background-color: #FFF;
  }
  tr.d1 td {
    background-color: #a0a0a0;
    color: #FFF;
}
tr.d2,
tr.d3{
  display: none;
}
  tr.d2 td {
    background-color: #dedede;
}
table tr th.static{ text-align: left;}
table tr td.static{ text-align: left; cursor: pointer;}
table tr.d3 .static{ cursor: initial;}
  @media(max-width:991px) {
  table tr .static{
    width: 130px;
    overflow-x: scroll;
    margin-top: 1px;
    position: absolute;
    border-right: 1px solid #efefef;
    z-index: 9;
  }
  table tr .first-col {
    padding-right: 13px !important;
    padding-left: 135px!important;
}
  }
</style>
<div class="content">
<div class="row">
  <div class="col-xs-12">
  @include('admin.contabilidad.incomes.table')
  </div>
</div>
  </div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
      $('.d1').on('click',function(){
        var k = $(this).data('k');
        $('.d1_'+k).toggle();
      });
      $('.d2').on('click',function(){
        var k = $(this).data('k');
        console.log('.d1_'+k);
        $('.d2_'+k).toggle();
      });
    });
</script>
@endsection
