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
    #sendClose {
        border: 1px solid #c3c3c3;
        padding: 15px;
    }
    span.balanceDay {
        font-size: 1.4em;
    }
    @media (min-width: 740px){
        .btn-monthly{
            float: right;
            margin-top: -48px;
        }
    }
</style>
<div class="content content-boxed bg-gray-lighter">
    <div class="bg-white">
        <section class="content content-full">
            <div class="row">
            @if($is_admin)
                <div class="col-md-12 col-xs-12 push-20 text-center">
                    <h2 class="text-center">INFORME DE CIERRES DIARIOS</h2>
                    
                    <button type="button" class="btn btn-success btn-monthly" type="button" data-toggle="modal" data-target="#modalAllMonthCashBox">CAJA MENSUAL</button>
                   
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
                @else
                    <div class="col-md-12 col-xs-12 push-20 text-center">
                        <h2 class="text-center">INFORME DE CIERRES DIARIOS <b>{{$day}} {{$months[intval($month)]}} {{$year}}</b></h2>
                    </div>
                @endif
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
                                <tr>
                                    @if($is_admin)<th class="text-center">{{ $item['id'] }}</th>@endif
                                    <td>{{ $item['concept'] }}</td>
                                    <td class="backgr {{$item['css']}}">{{ $item['type'] }}</td>
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
    @if($closedBy && $oCashbox)
        <p class="alert alert-info">Caja cerrada por <b>{{$closedBy}}</b></p>
    @else
        <button type="button" class="btn btn-success" id="addNew_ingr" type="button" data-toggle="modal" data-target="#modalAddNew"><i class="fa fa-plus-circle"></i> Añadir Gastos</button>
        <button type="button" class="btn btn-warnnig" type="button" data-toggle="modal" data-target="#modalCloseCashBox">CERRAR CAJA</button>
    @endif
    </div>

</div>

<div class="modal fade" id="modalAddNew" tabindex="-1" role="dialog" aria-hidden="true">
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

@if($is_admin)
<div class="modal fade" id="modalAllMonthCashBox" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <strong class="modal-title" id="modalChangeBookTit" style="font-size: 1.4em;">CAJA MENSUAL</strong>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo $tableMonthly; ?>
            </div>
        </div>
    </div>
</div>
@endif


<div class="modal fade" id="modalCloseCashBox" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <div class="col-md-3">
                    <table class="table" style="background-color: #c5c5c5;">
                        <tr>
                            <th>Ingresos del día</th>
                            <td with="50px">{{moneda($tIngr)}}</td>
                        </tr>
                        <tr>
                            <th>Salidas del día</th>
                            <td with="50px">{{moneda($tExpen)}}</td>
                        </tr>
                        <tr>
                            <th>Balance</th>
                            <td with="50px"><span class="balanceDay">{{$tSaldo+$tIngr-$tExpen}}</span>€</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-9">
                <form action="{{ url('/admin/caja-diaria-cierre') }}" method="post" id="sendClose">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="date" value="{{$dateQry}}">
                    <input type="hidden" name="tCashBox" value="{{$tCashBox}}">
                    <input type="hidden" name="tSaldo" value="{{$tSaldo}}">
                    <input type="hidden" name="tIngr" value="{{$tIngr}}">
                    <input type="hidden" name="tExpen" value="{{$tExpen}}">
                    <div class="row">
                        <div class="col-lg-2 col-md-3 col-xs-12 mb-1em">
                            <label for="import">Arqueo</label>
                            <input type="number" step="1" name="import" id="importClose" class="form-control" required />
                        </div>
                        <div class="col-lg-6 col-md-6 col-xs-12 mb-1em">
                            <label for="concept">Concepto</label>
                            <input type="text" class="form-control" name="concept" id="concept" />
                        </div>
                        <div class="col-lg-4 col-md-3 col-xs-12 mb-1em">
                            <label for="user">Cierre por:</label>
                            <select class="form-control" name="to_user" style="width: 100%;">
                                <option value="-1">--</option>
                                @foreach($oCoachs as $u)
                                <option value="{{$u->id}}">{{$u->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-center col-xs-12 mb-1em">
                            <div class="mb-1em"><b>Balance del día: <span class="balanceDay">{{$tSaldo+$tIngr-$tExpen}}</span>€</b></div>
                            <button class="btn btn-success" type="submit">Cerrar CAJA</button>
                        </div>
                        
                    </div>
                </form>
                </div>
            </div>
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
        $('#importClose').change(function() {
            var value = $(this).val();
            var oldValue = parseInt({{ceil($tSaldo+$tIngr-$tExpen)}});
            $('.balanceDay').text(oldValue+parseInt(value));
        });
        $('#day').change(function() {
            var month = $('#month').val();
            var day = $('#day').val();
            window.location.replace("/admin/caja-diaria/" + month + '/' + day);
        });

        $('#modalAddNew').on('submit', '#formNewExpense', function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serializeArray(),
                success: function(response) {
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

        $('#modalAddNew').on('click', '#reload', function(e) {
            location.reload();
        });

    });
</script>
@endsection