<?php setlocale(LC_TIME, "ES"); ?>
<?php setlocale(LC_TIME, "es_ES"); ?>
@extends('layouts.admin-master')

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
  <h2 class="text-center">INFORME DE CONVENIOS CLIENTES</h2>
  <div class="text-center">Listado de CONVENIOS por fecha</div>

  <input type="hidden" id="_token" name="_token" value="<?php echo csrf_token(); ?>">


  <div class="col-xs-12 btn-months mx-1em">
    @foreach($lstMonths as $k=>$v)
    <?php
    $url = '/admin/convenios/informes/' . $k .'/'.(($convenio) ? $convenio : 'all').(($rateTypeID) ? '/' . $rateTypeID : '');
    ?>
    <a href="{{$url}}" class=" btn btn-success <?php echo ($month == $k) ? 'active' : '' ?>">
      {{$v.' '.$year}}
    </a>
    @endforeach
  </div>
  <input type="text" id="searchInform" class="form-control" placeholder="Buscar" />
  <div class="row mt-1">
    <div class="col-md-2 col-xs-3">
      <label>Mes</label>
      <select id="month" class="form-control">
        <option> -- </option>
        <?php
        foreach ($lstMonths as $k => $v) :
          $s = ($k == $month) ? 'selected' : '';
          echo '<option value="' . $k . '" ' . $s . '>' . $v . '</option>';
        endforeach;
        ?>
      </select>
    </div>
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
    <div class="col-md-3 col-xs-6">
      <label>Convenio</label>
      <select id="convenio" class="form-control">
        <option value="all">Todos</option>
        <?php
        foreach ($lstObjs as $k => $oConve) :
          if($oConve->id == $convenio){
            $s ='selected';
          } else {
            $s = '';
          }
          
          echo '<option value="' . $oConve->id . '" ' . $s . ' class="b">' . $oConve->name . '</option>';
        endforeach;
        ?>
      </select>
    </div>
    <div class="col-md-2 col-xs-6" style="margin-top: 1.7em;">
      <button type="button" id="filter_form" class="btn btn-success">Filtrar</button>
      @if($urlPubl) <a href="{{$urlPubl}}" target="_blank" class="btn btn-success">Url Pública</a> @endif
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
              <td class="text-center bg-complete"><?php echo moneda($totalsComision, false, 1)   ?> </td>
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
              <th class="text-center">Convenio</th>
              <th class="text-center">Familia</th>
              <th class="text-center">Importe</th>
              <th class="text-center">Comisión</th>
              <th class="text-center"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($convLstUsers as $data) : ?>
              <tr class="informeConvenioRate">
                <td class="text-center">
                  @if($data->date)
                  <b>{{dateMin($data->date)}}</b>
                  @else
                  <b>--</b>
                  @endif
                </td>
                <td class="text-center">{{$data->name}}</td>
                <td class="text-center">{{$oConveniosId[$data->convenio]->name}}</td>
                @if(isset($data->type_rate) && isset($lstRateTypes[$data->type_rate]))
                  <td class="text-center">{{$lstRateTypes[$data->type_rate]}}</td>
                @else
                  <td class="text-center">Otros</td>
                @endif
                <td class="text-center">
                  @if($data->charged_method == 'bono')
                  BONO
                  @else
                    <input 
                      style="width: 80px;"
                      type="number" 
                      value="{{$data->price}}"
                      id="priceConvenioRate{{$data->id}}" 
                      step="0.01"
                      pattern="\d+(\.\d{1,2})?"
                    />
                  @endif
                </td>
                
                <td class="text-center">
                  <input 
                    style="width: 80px;"
                    type="number" 
                    value="{{$data->comision}}"
                    id="comisionConvenioRate{{$data->id}}" 
                    step="0.01"
                    pattern="\d+(\.\d{1,2})?"
                  />
                </td>
                <td><button data-id="{{$data->id}}" class="btn btn-primary btn-actualizar">Actualizar</button></td>
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
<script type="text/javascript">
  $('#filter_form').on('click', function(event) {
    var year = $('#date').val();
    var month = $('#month').val();
    var convenio = $('#convenio').val();
    var rate = $('#rate').val();
    window.location.href = '/admin/convenios/informes/' + month + '/' + convenio+ '/' + rate;
  });

  $('#searchInform').keydown(function(evt) {
    setTimeout(function() {
      var search = $('#searchInform').val();
      var token = $('#_token').val();
      var month = $('#month').val();
      var convenio = $('#convenio').val();
      var rate = $('#rate').val();
      $.post('/admin/convenios/informes/' + month + '/' + convenio+ '/' + rate, {
        search: search,
        _token: token
      }).done(function(data) {
        $('#content-table-inform').empty().append(data);
      });
    }, 50);
  });

  $('.btn-actualizar').on('click', (e) => {
      let $this = $(e.target);
      let id = $this.data('id');

      let price = $('#priceConvenioRate'+id).val();
      let comision = $('#comisionConvenioRate'+id).val();

      if(!price){
        price = 0;
      }

      if(!comision) {
        comision = 0;
      }

      $.post( '/admin/update/cobro/comision', { 
        _token: '{{csrf_token()}}',
        price: price,
        comision: comision,
        id: id,
      }).done(function (data) {
        if (data.status == 'OK') {
          window.show_notif('success', data.details);
          location.reload();
        } else {
          window.show_notif('error', data.details);
        }
      });
    });
</script>
@endsection