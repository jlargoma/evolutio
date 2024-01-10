<?php

use \Carbon\Carbon; ?>
<?php setlocale(LC_TIME, "ES"); ?>
<?php setlocale(LC_TIME, "es_ES"); ?>
@extends('layouts.admin-master')

@section('title') INFORME DE COBROS - Evolutio HTS @endsection

@section('externalScripts')
<style>
  .t-bg {
    color: #fff !important;
    background-color: #5c90d2 !important;
    border-bottom-color: #5c90d2 !important;
    font-weight: 800;
    text-align: center;
    vertical-align: middle !important;
  }
  .b-r1 {
   border-right: 3px solid black;
  }
  .btn-months{
    text-align: center;
  }
 
  d {
    display: block;
    font-size: 12px;
  }
</style>
@endsection
@section('content')
<div class="content content-boxed bg-gray-lighter">
  <div class="row ">
    <div class="col-xs-12 push-20">
      <div class="row">
        <div class="col-md-12 col-xs-12 push-20">
          <h2 class="text-center">INFORME DE INGRESOS POR EMPLEADO</h2>
        </div>
        <div class="col-xs-12 btn-months mx-1em">
           <a href="/admin/informes/cobros-mes/0" class=" btn btn-success <?php echo ($month == 0) ? 'active' : '' ?>">Anual</a>
          @foreach($months as $k=>$v)
          <a href="/admin/informes/cobros-mes/{{$k}}" class=" btn btn-success <?php echo ($month == $k) ? 'active' : '' ?>">
            {{$v.' '.$year}}
          </a>
          @endforeach
        </div>

        <div class="col-xs-12 mx-1em">
          <div class="table-responsive">
            <table class="table table-striped table-header-bg dataTable-mobile">
              <thead>
                <tr>
                  <th class="text-left t-bg  b-r1">Coach</th>
                  
                  <th class="t-bg jsRt td1 b-r1">Beneficio</th>
                  <th class="t-bg jsRt td2 b-r1">Salario</th>
                  <th class="t-bg jsRt td3 b-r1">Ingreso</th>
                  <th class="t-bg jsRt td4">Nutri</th>
                  <th class="t-bg jsRt td5">Fisio</th>
                  <th class="t-bg jsRt td5">Fisio GETAFE</th>
                  <th class="t-bg jsRt td6">Suscrip</th>
                  <th class="t-bg jsRt td7">Bonos</th>
                  <th class="t-bg jsRt td8">Total</th>
                  <th class="t-bg jsRt td9">Todos los servicios</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  $total = ['nutri' => 0, 'fisio' => 0, 'fisioG' => 0, 'suscrip' => 0, 'bonos' => 0]; 
                ?>
                @foreach($aCoachs as $cID=>$cname)
                <?php
                $data = isset($countCoachs[$cID]) ? $countCoachs[$cID] : ['nutri'=>0, 'fisio' => 0,'fisioG' => 0, 'suscrip' => 0, 'bonos' => 0];
                $total['nutri'] += $data['nutri'];
                $total['fisio'] += $data['fisio'];
                $total['fisioG'] += $data['fisioG'];
                $total['suscrip'] += $data['suscrip'];
                $total['bonos'] += $data['bonos'];
                $amount = isset($tCoachs[$cID]) ? $tCoachs[$cID] : '0';
                $liquid = isset($cLiq[$cID]) ? $cLiq[$cID] : '0';
                
                ?>
                <tr>
                  <td class="text-left showCoach b-r1" data-k="{{$cID}}">{{$cname}}</td>
                  <td class="text-center b-r1" data-order="{{$amount}}">{{moneda($amount-$liquid,false)}}</td>
                  <td class="text-center b-r1" data-order="{{$liquid}}">{{moneda($liquid,false)}}</td>
                  <td class="text-center b-r1" data-order="{{$amount}}">{{moneda($amount,false)}}</td>
                  <td class="text-center">{{$data['nutri']}}</td>
                  <td class="text-center">{{$data['fisio']}}</td>
                  <td class="text-center">{{$data['fisioG']}}</td>
                  <td class="text-center">{{$data['suscrip']}}</td>
                  <td class="text-center">{{$data['bonos']}}</td>
                  <td class="text-center">{{array_sum($data)}}</td>
                  <td class="text-center"><?php echo isset($countByCoach[$cID]) ? $countByCoach[$cID] : 0 ?></td>
                </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <th class="text-left t-bg font-w800  b-r1">Total</th>
                  <th class="t-bg jsRt td1 b-r1">{{moneda(array_sum($tCoachs)-array_sum($cLiq))}}</th>
                  <th class="t-bg jsRt td2 b-r1">{{moneda(array_sum($cLiq))}}</th>
                  <th class="t-bg jsRt td3 b-r1">{{moneda(array_sum($tCoachs))}}</th>
                  <th class="t-bg jsRt td4">{{$total['nutri']}}</th>
                  <th class="t-bg jsRt td5">{{$total['fisio']}}</th>
                  <th class="t-bg jsRt td5">{{$total['fisioG']}}</th>
                  <th class="t-bg jsRt td6">{{$total['suscrip']}}</th>
                  <th class="t-bg jsRt td7">{{$total['bonos']}}</th>
                  <th class="t-bg jsRt td8">{{array_sum($total)}}</th>
                  <th class="t-bg jsRt td9">{{array_sum($countByCoach)}}</th>
                </tr>
              </tfoot>
            </table>

          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalCobros" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
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
          <div class="row block-content">
            <div class="row">
              <h2 class="col-md-8">
              @foreach($aCoachs as $coachID=>$name)
              <span class="titCoach coach{{$coachID}}">{{$name}}</span>
              @endforeach
            </h2>
            <div class="col-md-4">
              <select id="f_servic" class="form-control">
                <option value="">Todos los servicios</option>
                <option value="bono">Compra de Bonos</option>
                @foreach($aRType as $k=>$v)
                <option value="{{$k}}">{{$v}}</option>
                @endforeach
              </select>
            </div>
</div>
            <div class="table-responsive">
              <table class="table table-striped table-header-bg">
                <thead>
                  <tr>
                    <th class="text-left t-bg font-w800">Cliente</th>
                    <th class="t-bg">Servicio</th>
                    <th class="t-bg">Monto €</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $total = 0; ?>
                  @foreach($rByCoach as $coachID=>$lst)
                  @foreach($lst as $item)
                  <?php
                  $cID = $item[0];
                  $cname = isset($aCust[$item[0]]) ? $aCust[$item[0]] : '-';
                  $import = $item[3];
                  $service = '';
                  $rType = $item[5];
                  if ($rType == 'bono') $service = $item[1];
                  else{
                    if (isset($aRType[$rType])) $service = $aRType[$rType];
                  }
                  
                  
                  
                  ?>
                  <tr class="itemByCoach" data-c="{{$coachID}}" data-v="{{$import}}" data-s="{{$rType}}">
                    <td class="text-left">{{$cname}}</td>
                    <td class="text-center">{{$service}} @if($item[6] != '') ({{$item[6]}}) @endif</td>
                    <td class="text-center">
                      <?php
                      if ($item[2] == 'bono') {
                        echo 'BONO';
                      } else {
                        echo moneda($import);
                      }
                      ?>
                    </td>
                  </tr>
                  @endforeach
                  @endforeach
                </tbody>
                <tfoot>
                  <tr>
                    <th class="text-left t-bg font-w800">Total</th>
                    <th class="text-left t-bg"></th>
                    <th class="t-bg trTotal"></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endsection
  @section('scripts')

  <script type="text/javascript">
    var dataTableMobile = 1
  </script>

  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>
  <script src="{{ asset('admin-css/assets/js/pages/base_tables_datatables.js')}}"></script>

  <script type="text/javascript">
    $(document).ready(function () {
        $('#date, #month, #day').change(function (event) {

            var year = $('#date').val();
            var month = $('#month').val();
            var day = $('#day').val();
            window.location = '/admin/informes/cuotas-mes/' + month;
        });

        $('#searchInform').keydown(function (evt) {
            setTimeout(function () {
                var search = $('#searchInform').val();
                var token = $('#_token').val();
                var month = $('#month').val();
                $.post('/admin/informes/search/' + month, {search: search, _token: token}).done(function
                  (data) {
                    $('#content-table-inform').empty().append(data);
                });
            }, 50);
        });

        var f_coach = null;
        var f_servic = '';
        function filterCobros(){
            $('.itemByCoach').hide();
             var trTotal = 0;
            $('.itemByCoach').each(
              function (index) {
                  
                  var cID = $(this).data('c');
                  var sID = $(this).data('s');
                  var show = true;
                  if(f_coach != cID) show = false;
                  if(f_servic != '' && f_servic != sID) show = false;
                  if (show){
                    $(this).show();
                    var amount = $(this).data('v');
                    if (!isNaN(amount)) {
                        trTotal += parseInt($(this).data('v'));
                    }
                  }
              });
            $('.trTotal').text(window.formatterEuro.format(trTotal));
            
        }
        
        $('#f_servic').on('change', function () {
          f_servic = $(this).val();
          filterCobros();
        });
        
        $('.showCoach').on('click', function () {
            $('.titCoach').hide();
            f_servic = '';
            $('#f_servic').val('');
            f_coach = $(this).data('k');
            $('.coach' + f_coach).show()
            filterCobros();
            $('#modalCobros').modal();
        });
        if (screen.width>480){
          $('tfoot .jsRt').each(function(){
            var class_css = $(this).attr('class');
            var aClass_css = class_css.split(' ');
            class_css = aClass_css.join('.');
            console.log($('thead .'+class_css).length,class_css);
            if ($('thead .'+class_css).length > 0){
              $('thead .'+class_css).append('<d>'+$(this).html()+'</d>');
            }
          });
        }
                

    });

  </script>
  <style>
    .table-striped tr:hover td{
      background-color: #dedede;
    }
    .showCoach{
      cursor: pointer;
      font-weight: bold;
          color: #328350;
    }
  </style>
  @endsection