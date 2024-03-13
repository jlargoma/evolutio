<?php setlocale(LC_TIME, "ES"); ?>
<?php setlocale(LC_TIME, "es_ES"); ?>
@extends('layouts.app')

@section('title') INFORME DE CONVENIOS - Evolutio HTS @endsection

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
</style>
@endsection
@section('content')
<div class="content content-boxed bg-gray-lighter">
  <h2 class="text-center">INFORME DE CONVENIO <b>{{$oConvenio->name}}</b></h2>
  <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">
  <div class="col-xs-12 btn-months mx-1em">
    @foreach($lstMonths as $k=>$v)
    <a href="<?= '/informes-convenio/' .$year.'/' . $oConvenio->token . '/' . $k  ?>" class=" btn btn-success <?php echo ($month == $k) ? 'active' : '' ?>">
      {{$v.' '.$year}}
    </a>
    @endforeach
  </div>
  <div class="row mx-1em">
    <div class="col-md-6 col-xs-12">
      <label>Servicio</label>
      <select id="rate" class="form-control">
        <option value=""> -- </option>
        <?php
        foreach ($lstRateTypes as $k => $v) :
          $s = ($k == $rateTypeID) ? 'selected' : '';
          echo '<option value="' . $k . '" ' . $s . '>' . $v . '</option>';
        endforeach;
        ?>
      </select>
    </div>
    <div class="col-md-2 col-xs-6" style="margin-top: 1.7em;">
      <button type="button" id="filter_form" class="btn btn-success">Filtrar</button>
    </div>
  </div>
  <div class="row" id="content-table-inform">
    <div class="table-responsive">
      <div class="col-md-12 col-xs-12 push-20">
        <table class="table table-striped table-header-bg">
          <tbody>
            <tr>
              <td class="text-center bg-complete font-w800" rowspan="2">RESUMEN</td>
              <td class="text-center bg-complete font-w800">Nº Clientes</td>
              <td class="text-center bg-complete font-w800">TOTAL</td>
              <td class="text-center bg-complete font-w800">TOTAL COMISIONES</td>
            </tr>
            <tr>
              <td class="text-center bg-complete"><?php echo count($convLstUsers); ?></td>
              <td class="text-center bg-complete"><?php echo moneda($totals, false, 1)  ?> </td>
              <td class="text-center bg-complete"><?php echo moneda($totalsComision, false, 1)  ?> </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="table-responsive">
      <div class="col-md-12 col-xs-12">
        <table class="table table-striped table-header-bg">
          <thead>
            <tr>
              <th class="text-center">Fecha</th>
              <th class="text-center">Nombre cliente</th>
              <th class="text-center">Familia</th>
              <th class="text-center">Importe</th>
              <th class="text-center">Comisión</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($convLstUsers as $data) : ?>
              <tr>
                <td class="text-center">
                  @if($data->date)
                  <b>{{dateMin($data->date)}}</b>
                  @else
                  <b>--</b>
                  @endif
                </td>
                <td class="text-center">{{$data->name}}</td>
                @if(isset($data->type_rate) && isset($lstRateTypes[$data->type_rate]))
                  <td class="text-center">{{$lstRateTypes[$data->type_rate]}}</td>
                @else
                  <td class="text-center">Otros</td>
                @endif
                <td class="text-center">
                  @if($data->charged_method == 'bono')
                  BONO
                  @else
                  {{moneda($data->price, false, 1)}}
                  @endif
                </td>
                <td class="text-center">{{moneda($data->comision,false,1)}}</td>
              </tr>
              <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection
@section('scripts')
<style>
  .mx-1em {
    margin-top: 1em;
    margin-bottom: 1em;
  }

  a.btn.btn-success {
    margin: 7px 2px;
  }
</style>
<script type="text/javascript">
  $(document).ready(function() {
    $('#filter_form').on('click', function(event) {
      var rate = $('#rate').val();
      window.location.href = '/informes-convenio/{{$year}}/{{$oConvenio->token}}/{{$month}}/' + rate;
    });
  });
</script>
@endsection