<?php setlocale(LC_TIME, "ES"); ?>
<?php setlocale(LC_TIME, "es_ES"); ?>
<?php

use \Carbon\Carbon; ?>
@extends('layouts.admin-master')

@section('title') RESUMEN  INGRESOS POR  LINEA DE  NEGOCIO - EVOLUTIO  <?php echo $date->copy()->format('Y') ?> @endsection


@section('content')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
<style type="text/css">
  #main-container{
    padding-top: 10px!important
  }
  .table.table-bordered.table-striped.table-header-bg .first-line > th{
    /*padding: 5px 0 0 0 ;*/
  }

  .table.table-bordered.table-striped.table-header-bg .second-line > th{
    padding: 10px 0;
  }
  .table.table-bordered.table-striped.table-header-bg tbody tr td{
    padding: 8px 10px;
    background-color: #ffffff;
  }
  .table-responsive >.fixed-column {
    position: absolute;
    display: inline-block;
    width: auto;
    border-right: 1px solid #ddd;
  }
  @media(min-width:768px) {
    .table-responsive > .fixed-column {
      display: none;
    }
  }
</style>
<div class="col-md-12 col-xs-12 push-20">

  @include('admin.contabilidad._button-contabiliad')


</div>

<div style="clear: both;"></div>
<div class="col-xs-12 bg-white push-10">
  <section class="content content-full">
    <div class="col-md-3 col-xs-12">
      <canvas id="barChart" style="width: 100%; height: 250px;"></canvas>
    </div>
    <div class="col-md-3 col-xs-12">
      <canvas id="barChartClient" style="width: 100%; height: 250px;"></canvas>
    </div>
    <div class="col-md-5 col-xs-12">
      <div class="row">
        <div class="col-xs-12 col-md-6 not-padding">
          <div class="block block-link-hover3 text-center push-0">
            <?php $total = 0 ?>
            <?php
            for ($i = 1; $i < 12; $i++) {
              $total += 0;
            }
            ?>
            <div class="block-content block-content-full bg-primary">
              <div class="h5 text-white-op text-uppercase push-5-t">Ingresos anuales</div>
              <div class="h1 font-w700 text-white"> <?php echo number_format(abs($total), 0, ',', '.'); ?><span class="h2 text-white-op">€</span></div>

            </div>
          </div>
        </div>
        <div class="col-xs-12 col-md-6 not-padding">
          <div class="block block-link-hover3 text-center push-0">
            <div class="block-content block-content-full bg-info">
              <div class="h5 text-white-op text-uppercase push-5-t">Media ingreso/mes</div>
<?php $actualDate = Carbon::now(); ?>
<?php $diff = $actualDate->diffInMonths($actualDate->copy()->startOfYear()); ?>
              <div class="h1 font-w700 text-white"> <?php echo number_format(abs($total / ($diff + 1)), 0, ',', '.'); ?><span class="h2 text-white-op">€</span></div>

            </div>
          </div>
        </div>
        <?php $aux = $actualDate->copy()->subMonths(2) ?>
        <?php $countIngresos = 0; ?>
        <?php for ($i = 1; $i <= 3; $i++): ?>
          <?php
          $incomes = 0;

          if (abs($incomes) > $countIngresos) {
            $countIngresos = abs($incomes);
          }
          if ($i > 1) {
            if (abs($incomes) > $countIngresos) {
              $statusIncomes = 1;
            } else {
              $statusIncomes = 0;
            }
          }
          ?>
          <div class="col-xs-12 col-md-4 not-padding push-0" style="border-right: solid 1px black">
            <div class="block block-link-hover3 text-center push-0">
              <div class="block-content block-content-full  bg-gray-light" style="padding: 10px;">
                <div class="h3 font-w700 text-black">
                  <?php echo number_format(abs($incomes), 0, ',', '.'); ?><span class="h3 text-black">€</span>
                  <?php if (isset($statusIncomes) && $statusIncomes == 1): ?>
                    <i class="fa fa-arrow-down text-danger"></i>
  <?php elseif (isset($statusIncomes) && $statusIncomes == 0): ?>
                    <i class="fa fa-arrow-up text-success"></i>
  <?php endif ?>
                </div>
                <div class="text-black-op text-uppercase push-5-t">
                  <span class="font-w700 h2"><?php echo ucfirst($aux->copy()->formatlocalized('%B')) ?></span>
                </div>

              </div>
            </div>
          </div>
          <?php $aux->addMonths(1) ?>
        <?php endfor; ?>

        <?php $aux = $actualDate->copy()->subMonths(2)->startOfMonth() ?>
        <?php
        $auxTotalClientes = 0;
        $auxTotalCuotaClienteMes = 0;
        ?>
<?php for ($i = 1; $i <= 3; $i++): ?>
          <div class="col-xs-12 col-md-4 not-padding push-0" style="border-right: solid 1px black">
            <div class="block block-link-hover3 text-centerpush-0" style="margin-bottom: 0px">

              <div class="row block-content  bg-gray-light" style="padding: 10px 5px;">
                <?php
                $clientsCoutas = DB::table('charges')
                        ->select('id_user')
                        ->whereIn('type_rate', [1, 2, 3])
                        ->whereYear('date_payment', '=', $aux->copy()->format('Y'))
                        ->whereMonth('date_payment', '=', $aux->copy()->format('m'))
                        ->get();

                $clientsNotCoutas = DB::table('charges')
                        ->select('id_user')
                        ->whereIn('type_rate', [4])
                        ->whereYear('date_payment', '=', $aux->copy()->format('Y'))
                        ->whereMonth('date_payment', '=', $aux->copy()->format('m'))
                        ->get();
                ?>

                <?php $totalClientes = count($clientsCoutas) + count($clientsNotCoutas) ?>
                <?php
                if ($totalClientes > $auxTotalClientes) {
                  $auxTotalClientes = $totalClientes;
                } else {
                  $auxTotalClientes = 0;
                }

                if ($i > 1) {
                  if ($totalClientes > $auxTotalClientes) {
                    $statusTotalClientes = 1;
                  } elseif ($auxTotalClientes > $totalClientes) {
                    $statusTotalClientes = 0;
                  } else {
                    $statusTotalClientes = 0;
                  }
                }
                ?>
                <div class="col-md-6 not-padding">
                  <div class="col-md-12">
                    Clientes <br>
                    <?php
                    $clients = \App\User::getUserActiveByMonth($aux->copy()->format
                                            ('Y-m-d'), "CUOTAS MENSUALES") +
                            \App\User::getUserActiveByMonth($aux->copy()->format
                                            ('Y-m-d'), "BONOS P.T.") +
                            \App\User::getUserActiveByMonth($aux->copy()->format
                                            ('Y-m-d'), "FISIOTERAPIA") +
                            \App\User::getUserActiveByMonth($aux->copy()->format
                                            ('Y-m-d'), "NUTRICION") +
                            \App\User::getUserActiveByMonth($aux->copy()->format
                                            ('Y-m-d'), "OTROS SERVICIOS");
                    ?>
                    <span class="font-w700 font-s18"><?php echo $clients; ?></span>/<span class="font-w700 font-s18"><?php echo count($clientsNotCoutas) ?></span>
                    <?php
                    $totalCoutaCliente = \App\Charges::whereYear('date_payment', '=', $aux->copy()->format('Y'))
                            ->whereMonth('date_payment', '=', $aux->copy()->format('m'))
                            ->sum('import');
                    if ($totalCoutaCliente > $auxTotalCuotaClienteMes) {
                      $auxTotalCuotaClienteMes = $totalCoutaCliente;
                    }
                    if ($i > 1) {
                      if ($totalCoutaCliente > $auxTotalCuotaClienteMes) {
                        $statusTotalCoutaCliente = 1;
                      } else {
                        $statusTotalCoutaCliente = 0;
                      }
                    }
                    ?>
                  </div>
                </div>

                <div class="col-md-6 not-padding" >
                  <div class="col-md-12 text-center  h1 text-black-op text-uppercase not-padding">
                    <span class="font-w700"><?php echo $clients; ?></span>
                    <?php if (isset($statusTotalClientes) && $statusTotalClientes == 1): ?>
                      <i class="fa fa-arrow-down text-danger"></i>
  <?php elseif (isset($statusTotalClientes) && $statusTotalClientes == 0): ?>

                      <i class="fa fa-arrow-up text-success"></i>
  <?php endif ?>
                  </div>
                </div>

                <div class="col-md-12 not-padding">
                  <div class="col-md-5">Cuota/P.T</div>
                  <div class="col-md-7 text-center">
                    <span class="font-w700 font-s18 ">
                      <?php if ((count($clientsCoutas) + count($clientsNotCoutas) == 0)): ?>
                        <?php $counter = 1; ?>

                      <?php else: ?>
                        <?php $counter = (count($clientsCoutas) + count($clientsNotCoutas) ); ?>
                      <?php endif ?>
                      <?php $avg = $totalCoutaCliente / $counter ?>
                      <?php echo number_format(abs($avg), 2, ',', '.'); ?>€
                      <?php if (isset($statusTotalCoutaCliente) && $statusTotalCoutaCliente == 1): ?>
                        <i class="fa fa-arrow-down text-danger"></i>
  <?php elseif (isset($statusTotalCoutaCliente) && $statusTotalCoutaCliente == 0): ?>
                        <i class="fa fa-arrow-up text-success"></i>
  <?php endif ?> 
                    </span>
                  </div>
                </div>

              </div>
            </div>
          </div>
  <?php $aux->addMonths(1) ?>
<?php endfor; ?>
      </div>
  </section>
</div>
<div class="bg-white">
  <section class="content content-full">
    <div class="row">
      <div class="col-xs-9 col-md-6 push-30">
        <h2 class="font-w600">
          Listado de Ingresos de <span class="font-w600"><?php echo $date->copy()->format('Y') ?></span>
        </h2>
      </div>
      <div class="col-xs-3 col-md-3 push-30">
        <div class="col-md-4 pull-right">
          <select class="form-control" id="yearSelector">
              <?php $yearSelector = $date->copy()->subYears(1); ?>
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <?php if ($year == $yearSelector->copy()->formatlocalized('%Y')) {
                $selected = "selected";
              } else {
                $selected = "";
              } ?>
              <option value="<?php echo $yearSelector->copy()->formatlocalized('%Y') ?>" <?php echo $selected ?>>
  <?php echo $yearSelector->copy()->formatlocalized('%Y') ?>
              </option>
  <?php $yearSelector->addYears(1); ?>
<?php endfor ?>
          </select>
        </div>
      </div><br>

      <div class="col-xs-12 push-30">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-header-bg table-colums-fixed">
            <thead>
              <tr class="first-line">
                <th class="text-center fixed-td">Area de negocio</th>
                  <?php $yearAux = $date->copy()->startOfMonth()->startOfYear(); ?>
                  <?php for ($i = 1; $i <= 12; $i++): ?>
                  <th class="text-center fixed-td">
                    <?php
                    $clientes = \App\User::getUserActiveByMonth($yearAux->copy()->format
                                            ('Y-m-d'), "CUOTAS MENSUALES") +
                            \App\User::getUserActiveByMonth($yearAux->copy()->format
                                            ('Y-m-d'), "BONOS P.T.") +
                            \App\User::getUserActiveByMonth($yearAux->copy()->format
                                            ('Y-m-d'), "FISIOTERAPIA") +
                            \App\User::getUserActiveByMonth($yearAux->copy()->format
                                            ('Y-m-d'), "NUTRICION") +
                            \App\User::getUserActiveByMonth($yearAux->copy()->format
                                            ('Y-m-d'), "OTROS SERVICIOS");
                    ?>
  <?php echo $yearAux->copy()->formatlocalized('%B') ?>(<?php echo $clientes ?>)
                  </th>
                <?php $yearAux->addMonths(1); ?>
              <?php endfor; ?>
                <th class="text-center fixed-td">Total Acumuluado</th>
              </tr>
            </thead>
            <tbody>

                <?php if (isset($rates_by_area[$year])) foreach ($rates_by_area[$year] as $key => $rate): ?>
                    <?php $totalRates = 0; ?>
                  <tr>
                    <td class="text-left">
    <?php echo strtoupper($key) ?>
                    </td>
                      <?php $yearAux = $date->copy()->startOfMonth()->startOfYear(); ?>
                      <?php for ($i = 1; $i <= 12; $i++) : ?>
                        <?php
                        $clientesByRate = \App\User::getUserActiveByMonth($yearAux->copy()->format('Y-m-d'), $key)
                        ?>
                      <td class="text-center">
                        @if ($rate[$i] != 0 || (isset($clients_by_month[$key][$i]) && $clients_by_month[$key][$i] !== null && $clients_by_month[$key][$i] !== 0))

                      <?php echo "(" . $clientesByRate . ")"; ?>
                      <?php echo number_format($rate[$i], 2, ',', '.'); ?> €
                        @else
                        ----
                        @endif
                      </td>

      <?php $totalRates += $rate[$i]; ?>
      <?php $yearAux->addMonths(1); ?>
    <?php endfor; ?>
                    <td class="text-center">
                      @if ($totalRates != 0)
                      <b><?php echo number_format($totalRates, 2, ',', '.'); ?> €</b>
                      (<?php echo number_format(array_sum($rates_by_area[$year][$key]) / $total * 100, 2, ',', '.') ?> %)
                      @else
                      ----
                      @endif
                    </td>
                  </tr>
                    <?php endforeach; ?>
              <tr >
                <td class="text-center" style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2; font-size: 24px;">
                  <b>TOTALES</b>
                </td>
<?php $totalYear = 0; ?>
<?php for ($i = 1; $i <= 12; $i++): ?>
                  <td class="text-center" style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2; font-size: 20px;">
                    <?php $totalMonth = 0;
                    ?>
                    @if ($totalMonth != 0)
                    <b><?php echo number_format($totalMonth, 0, ',', '.'); ?> €</b>
                    @else
                    ----
                    @endif
  <?php $totalYear += $totalMonth; ?>
                  </td>
<?php endfor; ?>
                <td class="text-center" style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2; font-size: 20px;">
                  <b>
<?php echo number_format($totalYear, 0, ',', '.'); ?> €
                  </b>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<div class="bg-white">
  <section class="content content-full">
    <div class="row">
      <div class="col-xs-9 col-md-6 push-30">
        <h2 class="font-w600">
          Listado de Formas de Cobro de <span class="font-w600"><?php echo $date->copy()->format('Y') ?></span>
        </h2>
      </div>
      <div class="col-xs-3 col-md-3 push-30">
        <div class="col-md-4 pull-right">
          <select class="form-control" id="yearSelector">
            <?php $yearSelector = $date->copy()->subYears(1); ?>
<?php for ($i = 1; $i <= 5; $i++): ?>
  <?php if ($year == $yearSelector->copy()->formatlocalized('%Y')) {
    $selected = "selected";
  } else {
    $selected = "";
  } ?>
              <option value="<?php echo $yearSelector->copy()->formatlocalized('%Y') ?>" <?php echo $selected ?>>
  <?php echo $yearSelector->copy()->formatlocalized('%Y') ?>
              </option>
                  <?php $yearSelector->addYears(1); ?>
                <?php endfor ?>
          </select>
        </div>
      </div><br>

      <div class="col-xs-12 push-30">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-header-bg table-cobros-fixed">
            <thead>
              <tr class="first-line">
                <th class="text-center">Area de negocio</th>
<?php $yearAux = $date->copy()->startOfYear(); ?>
              <?php for ($i = 1; $i <= 12; $i++): ?>
                  <th class="text-center">
                <?php echo $yearAux->copy()->formatlocalized('%B') ?>
                  </th>
  <?php $yearAux->addMonths(1); ?>
<?php endfor; ?>
                <th class="text-center">Total Acumuluado</th>
              </tr>
            </thead>

            <tbody>

                    <?php
                    $array_resume_type_payment = [];
                    ?>
              <tr>
                <td class="text-left font-w600">
                  BANCO
                </td>
                <?php for ($i = 1; $i <= 12; $i++) : ?>
                  <td class="text-center">
                    <b>
  <?php if (isset($type_income[$i]["banco"])): ?>
                        <?php echo number_format($type_income[$i]["banco"], 2, ',', '.'); ?>€
                        <?php @$array_resume_type_payment["banco"] += $type_income[$i]["banco"]; ?>
    <?php @$array_resume_type_payment[$i] += $type_income[$i]["banco"]; ?>
  <?php else: ?>
                        0,00€
  <?php endif ?>
                    </b>
                  </td>
<?php endfor; ?>
                <td class="text-center">
                  <b>
                    @if(isset($array_resume_type_payment["banco"]))
                <?php echo number_format($array_resume_type_payment["banco"], 2, ',', '.') ?>€
                    @endif

                  </b>
                </td>
              </tr>

              <tr>
                <td class="text-left font-w600">
                  CAJA
                </td>
                <?php for ($i = 1; $i <= 12; $i++) : ?>
                  <td class="text-center">
                    <b>
  <?php if (isset($type_income[$i]["cash"])): ?>
                        <?php echo number_format($type_income[$i]["cash"], 2, ',', '.'); ?>€
                        <?php @$array_resume_type_payment["cash"] += $type_income[$i]["cash"]; ?>
    <?php @$array_resume_type_payment[$i] += $type_income[$i]["cash"]; ?>
  <?php else: ?>
                        0,00€
  <?php endif ?>
                    </b>
                  </td>
<?php endfor; ?>
                <td class="text-center">
                  <b>
                    @if(isset($array_resume_type_payment["cash"]))
<?php echo number_format($array_resume_type_payment["cash"], 2, ',', '.') ?>€
                    @endif
                  </b>
                </td>
              </tr>

              <tr >
                <td class="text-center" style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2; font-size: 24px;">
                  <b>TOTALES</b>
                </td>
<?php for ($i = 1; $i <= 12; $i++): ?>
                  <td class="text-center" style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2; font-size: 20px;">
                    <b>
  <?php echo number_format(@$array_resume_type_payment[$i], 2, ',', '.') ?>€
                    </b>
                  </td>
<?php endfor; ?>
                <td class="text-center" style="color: #fff; background-color: #5c90d2; border-bottom-color: #5c90d2; font-size: 20px;">
                  <b>
                    @if(isset($array_resume_type_payment["cash"]) && isset($array_resume_type_payment["banco"]))
<?php echo number_format($array_resume_type_payment["cash"] + $array_resume_type_payment["banco"], 2, ',', '.') ?>€
                    @endif
                  </b>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<div class="bg-white">
  <section class="content content-full">
    <div class="row">
      <div class="col-xs-9 col-md-6 push-30">
        <h2 class=" font-w600">
          Listado de <span class="font-w600">Saldo</span>
        </h2>
      </div>
      <div style="clear: both"></div>
      <div class="col-md-4">
        <table class="table table-bordered table-striped table-header-bg">
          <thead>
          <th></th>
          <th>Saldo</th>
          <th>Ultima modificación</th>
          </thead>
          <tbody>
            <tr>
              <td>Banco</td>
              <td>TODO</td>
              <td>TODO</td>
            </tr>
            <tr>
              <td>Caja</td>
              <td>{{ number_format($resume_cashbox["total_balance"],2,',','.') }} €</td>
              <td>Carbon::CreateFromFormat('Y-m-d', $resume_cashbox['last_movement_date'] )->format('d-m-Y') }}</td>
            </tr>
            <tr>
              <td class="bg-primary">TOTAL</td>
              <td>TODO</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
$(document).ready(function() {

$('#yearSelector').change(function() {
var year = $(this).val();
window.location.replace("/admin/ingresos/" + year);
});
$('#addIngreso').click(function(){
$.get('/admin/nuevo/ingreso', function(data) {
$('#contentListIngresos').empty().append(data);
});
});
var data = {
labels: [
<?php $init = $date->copy()->startOfYear(); ?>
<?php for ($i = 1; $i <= 12; $i++): ?>
  <?php if ($i == 12): ?>
    "<?php echo substr(ucfirst($init->formatlocalized('%B')), 0, 3); ?>"
  <?php else: ?>
    "<?php echo substr(ucfirst($init->formatlocalized('%B')), 0, 3); ?>",
  <?php endif; ?>
  <?php $init->addMonths(1); ?>
<?php endfor; ?>
],
        datasets: [
        {
        label: "Ingresos por mes",
                backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                ],
                borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                ],
                borderWidth: 1,
                data: [
<?php for ($i = 1; $i <= 12; $i++): ?>
  <?php
  $totalMonth = 0;
  ?>
  <?php if ($i == 12): ?>
    <?php echo $totalMonth; ?>
  <?php else: ?>
    <?php echo $totalMonth; ?>,
  <?php endif ?>
<?php endfor; ?>
                ],
        }
        ]
};
var myBarChart = new Chart('barChart', {
type: 'line',
        data: data,
});
/* La configuracion de posicion funciona con la siguiente que se escriba*/
Chart.defaults.global.legend.position = 'bottom';
Chart.defaults.global.legend.labels.usePointStyle = true;
/*Fin de configuracion de posicion*/

var dataPie = {
labels: [

],
        datasets: [
        {
        data: [


        ],
                backgroundColor: [
                        "#388E3C",
                        "#EF6C00",
                        "#01579B",
                        "#00BFA5",
                        "#FF5252",
                        "#4A148C",
                        "#00E5FF",
                        "#FFEB3B",
                        "#F57C00",
                        "#00E676",
                        "#B39DDB"
                ],
                hoverBackgroundColor: [
                        "#388E3C",
                        "#EF6C00",
                        "#01579B",
                        "#00BFA5",
                        "#FF5252",
                        "#4A148C",
                        "#00E5FF",
                        "#FFEB3B",
                        "#F57C00",
                        "#00E676",
                        "#B39DDB"
                ]
        }]
};
var options = {
// String - Template string for single tooltips
tooltipTemplate: "<%if (label){%><%=label %>: <%}%><%= value + ' %' %>",
        // String - Template string for multiple tooltips
        multiTooltipTemplate: "<%= value + ' %' %>",
};
var myPieChart = new Chart('pieChart', {
type: 'pie',
        data: dataPie,
        options: options
});
/* La configuracion de posicion funciona con la siguiente que se escriba*/
Chart.defaults.global.legend.position = 'top';
Chart.defaults.global.legend.labels.usePointStyle = true;
/*Fin de configuracion de posicion*/
var dataClient = {
labels: [
<?php $year = $date->copy()->startOfYear(); ?>
<?php for ($i = 1; $i <= 12; $i++): ?>
  <?php if ($i == 12): ?>
    "<?php echo substr(ucfirst($year->formatlocalized('%B')), 0, 3); ?>"
  <?php else: ?>
    "<?php echo substr(ucfirst($year->formatlocalized('%B')), 0, 3); ?>",
  <?php endif; ?>
  <?php $year->addMonths(1); ?>
<?php endfor; ?>
],
        datasets: [
        {
        label: "Clientes por mes",
                backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                ],
                borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(54, 162, 235, 1)',
                ],
                borderWidth: 1,
                data: [
<?php for ($i = 1; $i <= 12; $i++): ?>
    <?php echo isset($clientes[$i]) ?  $clientes[$i] : ''; ?>,
<?php endfor; ?>
                ],
        }
        ]
};
var myBarChartClient = new Chart('barChartClient', {
type: 'bar',
        data: dataClient,
});
var $table = $('.table-colums-fixed');
var $fixedColumn = $table.clone().insertBefore($table).addClass('fixed-column');
$fixedColumn.find('th:not(:first-child),td:not(:first-child)').remove();
$fixedColumn.find('tr').each(function (i, elem) {
$(this).height($table.find('tr:eq(' + i + ')').height());
});
var $tableCobros = $('.table-cobros-fixed');
var $fixedColumnCobros = $tableCobros.clone().insertBefore($tableCobros).addClass('fixed-column');
$fixedColumnCobros.find('th:not(:first-child),td:not(:first-child)').remove();
$fixedColumnCobros.find('tr').each(function (i, elem) {
$(this).height($table.find('tr:eq(' + i + ')').height());
});
});
</script>
@endsection