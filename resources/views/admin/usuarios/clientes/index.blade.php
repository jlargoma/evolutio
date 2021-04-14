@extends('layouts.admin-master')

@section('title') Clientes - Evolutio HTS @endsection

@section('externalScripts')
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/datatables/jquery.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css') }}">
<style type="text/css">
    #DataTables_Table_0_wrapper .row > .col-sm-6:first-child{
        display: none;
    }
    #DataTables_Table_0_wrapper .row > .col-sm-6 #DataTables_Table_0_filter{
        text-align: left!important;
    }
    input[type="search"], ::-webkit-input-placeholder, :-moz-placeholder, :-ms-input-placeholder{
        color: black;
    }
    .header-navbar-fixed #main-container{
        padding-top: 0; 
    }
    .btn-user{cursor: pointer}
    .js-dataTable-full-clients .label{
        padding: 6px;
        display: inline-block;
        cursor: pointer;
    }

    .no-pay{
        color: #c54b4b;
        font-weight: bold;
    }
    .openEditCobro,
    .open-cobro{ cursor: pointer;}
</style>
@endsection

@section('headerButtoms')
<li class="text-center">
    <button id="newUser" class="btn btn-sm btn-success font-s16 font-w300" data-toggle="modal" data-target="#modal-newUser" style="padding: 10px 15px;">
        <i class="fa fa-plus"></i> Cliente
    </button>
</li>
@endsection


@section('content')
<div class="content content-full bg-gray-lighter">
    <div class="row ">
        <div class="col-md-5 col-xs-12 push-20">
            <div class="col-md-2 col-xs-4">
                <a href="{{url('/admin/clientes')}}?status=all">
                    <button class="btn btn-md 
                            @if($status == 'all') btn-success @else btn-primary @endif
                            " style="width: 100%;">
                        Todos
                    </button>
                </a>
            </div>
            <div class="col-md-2 col-xs-4">
                <a href="{{url('/admin/clientes')}}?status=1">
                    <button class="btn btn-md @if($status == 1) btn-success @else btn-primary @endif" style="width: 100%;">
                        Activos
                    </button>
                </a>
            </div>
            <div class="col-md-2 col-xs-4">
                <a href="{{url('/admin/clientes')}}?status=0">
                    <button class="btn btn-md @if($status != 1) btn-success @else btn-primary @endif" style="width: 100%;">
                        Inactivos
                    </button>
                </a>
            </div>
            <div class="col-md-3 col-xs-4">
                <a href="{{url('/admin/clientes-export')}}">
                    <button class="btn btn-md" style="width: 100%;">
                        EXPORT EXCEL
                    </button>
                </a>
            </div>
        </div> 
        <div class="col-xs-12 col-md-2 text-center center">
<!--            <button id="addIngreso" class="btn btn-success btn-mobile-lg" data-toggle="modal" data-target="#modal-ingreso"
                    style="padding: 7px 20px;">
                TPV
            </button>-->
        </div>
        <div class="col-md-4 col-xs-12 push-20">
            <div class="col-xs-12 col-md-6 pull-right">
                <select id="date" class="form-control">
                    <?php
                    foreach ($months as $k => $v):
                        $selected = ($k == $month) ? "selected" : "";
                        ?>
                        <option value="<?php echo $k; ?>" <?php echo $selected ?>>
                            <?php echo $v . ' ' . $year; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-xs-12">
            <h2 class="text-center font-s36 font-w300">Listado de Clientes</h2>
        </div>
        <div class="col-xs-12">
            <div class="col-md-2 col-xs-12">
                @if ($total_pending > 0)
                <a href="#" style="float: right; margin: 5px 10px 5px 0; ">
                    <button id="cuotas-pendientes" class="btn btn-danger right">
                        Cuotas Pendientes {{ $total_pending }} €
                    </button>
                </a>
                @endif
            </div>

        </div>

        <div class="col-xs-12 push-20">
            <div class="row">
                <div class="loading text-center" style="padding: 150px 0;">
                    <i class="fa fa-5x fa-circle-o-notch fa-spin"></i><br><span class="font-s36">CARGANDO</span>
                </div>
                <div class="col-md-12" id="containerTableResult" style="display: none;">
                   @include('/admin/usuarios/clientes/table')
                </div>
            </div> 
        </div>
    </div>
</div>
@include('/admin/usuarios/clientes/modals')
@endsection


@section('scripts')
<script src="{{ asset('admin-css/assets/js/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('admin-css/assets/js/pages/base_tables_datatables.js')}}"></script>
@include('/admin/usuarios/clientes/scripts')
@endsection