<?php setlocale(LC_TIME, "ES"); ?>
<?php setlocale(LC_TIME, "es_ES"); ?>
<?php

use \Carbon\Carbon; ?>
@extends('layouts.admin-master')

@section('externalScripts')
<script type="text/javascript" src="{{ asset('/admin-css/assets/js/chart.js')}}"></script>
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/datatables/jquery.dataTables.min.css') }}">
@endsection

@section('title')

@endsection

@section('content')
<style type="text/css">
    .header-navbar-fixed #main-container {
        padding-top: 0px;
    }
</style>
<div class="content content-boxed bg-gray-lighter">
    <div class="bg-white">
        <section class="content content-full">
            <div class="row">
                <div class="col-md-12 col-xs-12 push-20">
                    <h2 class="text-center">INFORME DE CIERRES DIARIOS</h2>
                </div>
                <div class="col-md-12 col-xs-12 push-20">
                    <div class="col-md-4 col-xs-1 text-right">
                        <a href="/admin/caja-diaria/{{$yesterday}}"><i class="fa fa-arrow-left"></i></a>
                    </div>
                    <div class="col-md-4 col-xs-10">
                        <div class="col-md-4 col-xs-12">
                            <label>Año</label>
                            <input value="{{$year}}" disabled="" class="form-control">
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <label>Mes</label>
                            <select id="month" class="form-control">
                                <?php
                                foreach ($months as $k => $v) :
                                    $s = ($k == $month) ? 'selected' : '';
                                    echo '<option value="' . $k . '" ' . $s . '>' . $v . '</option>';
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <label>Dia</label>
                            <select id="day" class="form-control">
                                <option value="all">Todos</option>
                                <?php
                                for ($i = 1; $i <= $lastDay; $i++) :
                                    $s = ($i == $day) ? 'selected' : '';
                                    echo '<option value="' . $i . '" ' . $s . '>' . $i . '</option>';
                                endfor;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-1">
                        <a href="/admin/caja-diaria/{{$tomorrow}}"><i class="fa fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-11">
                        <h2 class="text-center font-w300">
                            Movimientos de Caja <b>(<span id="total-caja">{{ moneda($tCashBox) }}</span>)</b>
                        </h2>
                    </div>
                </div>
                <br>
                <div class="row">
                    <table class="table table-bordered table-header-bg no-footer">
                        <thead>
                            <tr>
                                @if($is_admin)<th class="text-center">ID</th>@endif
                                <th>Concepto</th>
                                <th class="text-center">Tipo de movimiento</th>
                                <th class="text-center">Importe</th>
                                <th class="text-center">Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lstItems as $item) : ?>
                                <tr >
                                    @if($is_admin)<th class="text-center">{{ $item['id'] }}</th>@endif
                                    <td>{{ $item['concept'] }}</td>
                                    <td class="backgr {{$item['css']}}" >{{ $item['type'] }}</td>
                                    <td class="text-center"><b>{{ moneda($item['import']) }}</b></td>
                                    <td class="text-center">{{ $item['user'] }}</td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
        </section>
    </div>
<div class="text-center mt-2">
    <button type="button" class="btn btn-success" id="addNew_ingr" type="button" data-toggle="modal" data-target="#modalAddNew"><i class="fa fa-plus-circle"></i> Añadir Gastos</button>
</div>

</div>

<div class="modal fade" id="modalAddNew" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <strong class="modal-title" id="modalChangeBookTit" style="font-size: 1.4em;">Añadir Gasto</strong>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">@include('admin.contabilidad.expenses._form')</div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script src="{{ asset('admin-css/assets/js/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('admin-css/assets/js/pages/base_tables_datatables.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#month').change(function() {
            var month = $('#month').val();
            window.location.replace("/admin/caja-diaria/" + month + '/01');
        });
        $('#day').change(function() {
            var month = $('#month').val();
            var day = $('#day').val();
            window.location.replace("/admin/caja-diaria/" + month + '/' + day);
        });

        $('#modalAddNew').on('submit', '#formNewExpense', function (e) {
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
                    alert('Gasto Agregado');
                } else
                    alert(response);
                }
            });
            });
            
            $('#modalAddNew').on('click', '#reload', function (e) {
            location.reload();
            });

    });
</script>
@endsection