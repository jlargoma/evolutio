<?php

use \Carbon\Carbon; 
if (!$filt_rate && $filt_month>0)
$filt_month = intVal($filt_month);

?>
<?php setlocale(LC_TIME, "ES"); ?>
<?php setlocale(LC_TIME, "es_ES"); ?>
@extends('layouts.admin-master')

@section('title') INFORME DE CUOTAS PAGADAS - Evolutio HTS @endsection

@section('externalScripts')
<style>
  .bg-complete {
    color: #fff !important;
    background-color: #5c90d2 !important;
    border-bottom-color: #5c90d2 !important;
    font-weight: 800;
    vertical-align: middle !important;
  }
  option.b {
    font-weight: bold;
  }
  .table-responsive.form-rigth {
    margin: 15px;
  }
  .table-responsive.form-rigth .field-rigth {
    max-width: 170px;
    float: right;
  }
  .table-responsive.form-rigth label {
    float: right;
    margin-top: 6px;
    margin-right: 1em;
}
tr.payType_PENDIENTE td.price,
tr.payType_PENDIENTE td.typePay {
    color: red;
}
</style>
@endsection
@section('content')
<div class="content content-boxed bg-gray-lighter">
  <h2 class="text-center">INFORME DE SERVICIOS CLIENTES</h2>
  <div class="text-center">Listado de SERVICIOS realizados por fecha</div>
  
  <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
  
  
  <div class="col-xs-12 btn-months mx-1em">
          @foreach($months as $k=>$v)
          <a href="/admin/informes/servicios-mes/{{$k}}" class=" btn btn-success <?php echo ($filt_month == $k) ? 'active' : '' ?>">
            {{$v.' '.$year}}
          </a>
          @endforeach
        </div>
  <input type="text" id="searchInform" class="form-control" placeholder="Buscar"/>
  <div class="row mt-1">
    <div class="col-md-2 col-xs-3">
      <label>Mes</label>
      <select id="f_month" class="form-control">
        <option> -- </option>
        <?php
        foreach ($months as $k => $v):
          $s = ($k == $filt_month) ? 'selected' : '';
          echo '<option value="' . $k . '" ' . $s . '>' . $v . '</option>';
        endforeach;
        ?>
      </select>
    </div>
    <div class="col-md-3 col-xs-6">
      <label>Servicio</label>
      <select id="f_rate" class="form-control">
        <option value="all">Todos</option>
        <?php
        foreach ($rateFilter as $k => $v):
          $s = ($k == $filt_rate) ? 'selected' : '';
          echo '<option value="' . $k . '" ' . $s . ' class="b">' . $v['n'] . '</option>';
          foreach ($v['l'] as $k2 => $v2):
            $aux = "$k-$k2";
            $s = ($aux == $filt_rate) ? 'selected' : '';
            echo '<option value="' . $aux . '" ' . $s . '>&nbsp; - ' . $v2 . '</option>';
          endforeach;
        endforeach;
        ?>
      </select>
    </div>
    
    <div class="col-md-3 col-xs-6">
      <label>ENTRENADOR/FISIO</label>
      <select id="f_coach" class="form-control">
        <option></option>
        <?php
        foreach ($aCoachs as $id => $name):
          $sel = ($f_coach == $id) ? 'selected' : '';
          ?>
          <option value="{{$id}}" <?php echo $sel; ?>>{{$name}}</option>
          <?php
        endforeach;
        ?>
      </select>
    </div>
    <div class="col-md-2 col-xs-6" style="margin-top: 1.7em;">
      <button type="button" id="filter_cliMonths" class="btn btn-success">Filtrar</button>
      <a href="/admin/informes/servicios-mes-all">Descarga Anual</a>
    </div>
    
  </div>

 

  <div class="row" id="content-table-inform">
    <div class="table-responsive">
    @include('admin.informes._table_sumary_global')
    </div>

    <div class="table-responsive form-rigth">
      <div class="field-rigth">
        <select id="f_method" class="form-control">
          <option value="all">Todos</option>
          <option value="BANCO" >BANCO</option>
          <option value="METALICO" >METALICO</option>
          <option value="TARJETA" >TARJETA</option>
          <option value="invita" >Invit. Evolutio</option>
          <option value="BONO" >BONO</option>
          <option value="PENDIENTE" >PENDIENTE</option>
        </select>
      </div>
      <label class="">Tipo de Pago</label>
    </div>
    <div class="table-responsive">
    @include('admin.informes.servicios._table_sumary')
    </div>
    <div class="table-responsive">
    @include('admin.informes.servicios._table_informes')
      </div>

      <h2>Bonos Vendidos</h2>
    <div class="table-responsive">
    @include('admin.informes._table_sumary_bonos')
    </div>
    <div class="table-responsive">
      @include('admin.informes._table_informes_bonos')
      </div>

    </div>
  </div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
  $('#f_method').on('change',function (event) {
    var f_method = $(this).val();
    console.log(f_method);
    if (f_method == 'all'){
      $('#tableServices > tr').each(function(index, tr) { 
        $(this).show();
      });
      $('#tableBonos > tr').each(function(index, tr) { 
        $(this).show();
      });
    } else {
      f_method = 'payType_'+f_method;
      
      $('#tableServices > tr').each(function(index, tr) { 
        console.log(f_method,$(this).hasClass(f_method));
        if ($(this).hasClass(f_method)){
          $(this).show();
        } else {
          $(this).hide();
        }
      });
      $('#tableBonos > tr').each(function(index, tr) { 
        console.log(f_method,$(this).hasClass(f_method));
        if ($(this).hasClass(f_method)){
          $(this).show();
        } else {
          $(this).hide();
        }
      });
      
    }
  });
  $('#filter_cliMonths').on('click',function (event) {

      var year = $('#date').val();
      var f_month = $('#f_month').val();
      var f_rate = $('#f_rate').val();
      var f_method = $('#f_method').val();
      var f_coach = $('#f_coach').val();
      window.location.href = '/admin/informes/servicios-mes/' + f_month + '/' + f_rate + '/' + f_method + '/' + f_coach;
  });

  $('#searchInform').keydown(function (evt) {
      setTimeout(function () {
          var search = $('#searchInform').val();
          var token = $('#_token').val();
          
          $('#f_month').val('');
          $('#f_rate').val('');
          $('#f_method').val('');
          $('#f_coach').val('');
      
          $.post('/admin/informes/search/{{$filt_month}}' , {search: search, _token: token}).done(function
            (data) {
              $('#content-table-inform').empty().append(data);
          });
      }, 50);
  });

</script>
@endsection