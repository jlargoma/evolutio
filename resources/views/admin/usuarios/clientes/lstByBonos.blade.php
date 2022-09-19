@extends('layouts.admin-master')

@section('title') Clientes - Evolutio HTS @endsection

@section('externalScripts')
<!--<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/datatables/jquery.dataTables.min.css') }}">-->
<style type="text/css">
  #DataTables_Table_0_wrapper .row>.col-sm-6:first-child {
    display: none;
  }

  #DataTables_Table_0_wrapper .row>.col-sm-6 #DataTables_Table_0_filter {
    text-align: left !important;
  }

  input[type="search"],
  ::-webkit-input-placeholder,
  :-moz-placeholder,
  :-ms-input-placeholder {
    color: black;
  }

  .header-navbar-fixed #main-container {
    padding-top: 0;
  }

  .btn-user {
    cursor: pointer
  }

  .js-dataTable-full-clients .label {
    padding: 6px;
    display: inline-block;
    cursor: pointer;
  }

  .openUser {
    cursor: pointer
  }

  .no-pay {
    color: #c54b4b;
    font-weight: bold;
  }

  .openEditCobro,
  .open-cobro {
    cursor: pointer;
  }

  a.inline {
    display: inline-block;
    margin-right: 2px;
  }

  .text-center.tc1 {
    min-width: 120px;
  }

  th label.text-danger {
    display: block
  }

  .boxAddServBono {
    position: absolute;
    background-color: #9a9a9a;
    padding: 5px;
  }
</style>
@endsection

@section('headerButtoms')
<li class="text-center">
  <button id="newUser" class="btn btn-success btn-home">
    <i class="fa fa-plus"></i> Cliente
  </button>
</li>
@endsection

<?php
$b_aux = ['btn-primary', 'btn-primary', 'btn-primary', 'all' => 'btn-primary'];
if (isset($b_aux[$status])) $b_aux[$status] = 'btn-success';
?>
@section('content')
<div class="content content-full bg-gray-lighter">
  <div class="row ">
    <div class="col-md-8 col-xs-12 mb-1em">
      <a href="{{url('/admin/clientes/lstByBonos')}}?status=all" class="inline">
        <button class="btn btn-md {{$b_aux['all']}}">
          Todos
        </button>
      </a>
      <a href="{{url('/admin/clientes/lstByBonos')}}?status=1" class="inline">
        <button class="btn btn-md {{$b_aux[1]}}">
          Activos
        </button>
      </a>
      <a href="{{url('/admin/clientes/lstByBonos')}}?status=0" class="inline">
        <button class="btn btn-md {{$b_aux[0]}}">
          Inactivos
        </button>
      </a>
    </div>

    <div class="col-xs-8 col-md-3 pull-right">
      <select id="filterByRate" class="form-control">
        <option value="">Filtrar Por Servicio</option>
        <?php
        foreach ($oTRates as $rID => $rate) :
          $cant = isset($cantByRate[$rID]) ? $cantByRate[$rID] : 0;
        ?>
          <option value="<?= $rID ?>"><?php echo $rate['n'] . " ($cant)"; ?></option>
          <?php
          if ($rate['l']) :
            foreach ($rate['l'] as $k2 => $v2) :
              $cant = isset($cantByRate[$k2]) ? $cantByRate[$k2] : 0;
          ?>
              <option value="<?= $k2 ?>"><?php echo $rate['n'] . ' - ' . $v2 . " ($cant)"; ?></option>
        <?php
            endforeach;
          endif;
        endforeach; ?>
      </select>

    </div>
    <div class="col-xs-4 col-md-1 pull-right">
      <select id="filterBytime" class="form-control">
        <option value="">últ compr</option>
        <option value="1">3 meses</option>
        <option value="2">6 meses</option>
      </select>
    </div>
  </div>

  <div class="row mt-1">
    <div class="col-md-12">


      <table class="table table-striped js-dataTable-full-clients table-header-bg">
        <thead>
          <tr>
            <th class="text-center tc1">Nombre Cliente<br></th>
            <th class="text-center tc3">Tel<span class="hidden-xs hidden-sm">éfono</span><br></th>
            <th class="text-center">Bonos</th>
          </tr>
        </thead>
        <tbody id="lstTd">
          <?php foreach ($users as $key => $user) :
            $dataKEy = (isset($uBonoRate[$user->id]) ? implode('|', $uBonoRate[$user->id]) : '');
            $aDataTime = '';
            if (isset($uBonos[$user->id])) {
              $lst = $uBonos[$user->id];
              foreach ($lst as $b) {
                if (is_array($b['last'])) dd($b['last']);
                $aDataTime .= $b['last'] . '|';
              }
            }

          ?>
            <tr data-k="{{$dataKEy}}" data-time="{{$aDataTime}}">
              <td class="text-left tc1">
                <a class="openUser" data-id="<?php echo $user->id; ?>" data-type="user" data-original-title="Editar user"><b><?php echo $user->name; ?></b></a>
                <?php echo in_array($user->id, $uPlan) ? '<i class="fa fa-heart text-success fidelity"></i>' : '' ?>
                <?php echo in_array($user->id, $uPlanPenal) ? '<i class="fa fa-heart text-danger fidelity" title="CLEINTE CON PENALIZACION DE 60€ POR BAJA ANTICIPADA"></i>' : '' ?>
              </td>
              <td class="text-center tc3">
                <span class="hidden-xs hidden-sm"><?php echo $user->telefono; ?></span>
                <span class="hidden-lg hidden-md">
                  <a href="tel:<?php echo $user->telefono; ?>">
                    <i class="fa fa-phone"></i>
                  </a>
                </span>
              </td>
              <td class="text-center tc3 last">
                <?php
                if (isset($uBonos[$user->id])) {
                  $lst = $uBonos[$user->id];
                  foreach ($lst as $b) {
                    $name = '';
                    if (isset($typeRates[$b['rtype']])) $name .= $typeRates[$b['rtype']] . ' ';
                    if (isset($oFamily[$b['rsubf']])) $name .= $oFamily[$b['rsubf']] . ' ';
                    $name .= $b['q'] . ' ';
                    if ($b['last'] > 180) {
                      echo '<span class="red lstBonoDetail" data-id="'.$b['bu_id'].'">' . $name . '</span> ';
                    } else {
                      if ($b['last'] > 89) {
                        echo '<span class="yelow lstBonoDetail" data-id="'.$b['bu_id'].'">' . $name . '</span> ';
                      } else {
                        echo '<span class="lstBonoDetail" data-id="'.$b['bu_id'].'">' . $name . '</span>';
                      }
                    }
                  }
                }
                ?>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>
@include('/admin/usuarios/clientes/modals')
@endsection


@section('scripts')

<style>
  .last span {
    border: 1px solid #9a9a9a;
    border-radius: 6px;
    padding: 3px 9px;
    font-weight: 500;
    margin-right: 4px;
  }

  .last span.red {
    background-color: #f7afaf;
    border-color: red;
  }

  .last span.yelow {
    background-color: #ffc931;
    border-color: #c79300;
  }
  .lstBonoDetail{
    cursor: pointer;
  }
</style>
<script type="text/javascript">
  var dataTableClient = 1
  $(document).ready(function() {

    var filterTable = function() {
      val = $('#filterByRate').val();
      time = $('#filterBytime').val();
      if (val != '' || time != '') {
        $('#lstTd').find('tr').each(function() {
          let show = false;

          if (val != '') {
            let dataKey = $(this).data('k') + '';
            dataKey = dataKey.split('|');

            if (dataKey.includes(val)) show = true;
          }

          if (time != '') {
            let dataTime = $(this).data('time') + '';
            dataTime = dataTime.split('|');
            for (aux in dataTime) {
              let last = dataTime[aux];
              if (last > 60 && last < 180 && time == 1) show = true;
              if (last > 180 && time == 2) show = true;
            }
          }

          if (show) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      } else {
        $('#lstTd').find('tr').show();
      }
    }
    $('#filterBytime,#filterByRate').on('change', function() {
      filterTable();
    });

    $('#lstTd').on('click','.openUser',function (e) {
      e.preventDefault();
      var id = $(this).data('id');
      $('#ifrCliente').attr('src','/admin/usuarios/informe/' + id);
      $('#modalCliente').modal('show');
    });


    $('.lstBonoDetail').on('click',function(){
        // $('.lstBonoDetail').removeClass('selected');
        // $(this).addClass('selected');

        $('#ifrCliente').attr('src','/admin/bonologs/' + $(this).data('id') + '?iframe=1');
        $('#modalCliente').modal('show');

      })

  });
</script>



@endsection