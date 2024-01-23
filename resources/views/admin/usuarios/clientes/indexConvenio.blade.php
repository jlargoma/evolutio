@extends('layouts.admin-master')

@section('title') Clientes - Evolutio HTS @endsection

@section('externalScripts')
<!--<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/datatables/jquery.dataTables.min.css') }}">-->
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

  .openUser{cursor: pointer}
  .no-pay{
    color: #c54b4b;
    font-weight: bold;
  }
  .openEditCobro,
  .open-cobro{ cursor: pointer;}
  a.inline {
    display: inline-block;
    margin-right: 2px;
  }
  .text-center.tc1 {
    min-width: 120px;
}
th label.text-danger{display: block}
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
$b_aux = ['btn-primary','btn-primary','btn-primary','all'=>'btn-primary','new'=>'btn-primary','unsubscribeds'=>'btn-primary'];
if (isset($b_aux[$status])) $b_aux[$status] = 'btn-success';
?>
@section('content')
<div class="content content-full bg-gray-lighter">
  
  
<h1 class="tit-primary">{{$tit}}</h1>
  <div class="row mt-1">
    <div class="loading text-center" style="padding: 150px 0;">
      <i class="fa fa-5x fa-circle-o-notch fa-spin"></i><br><span class="font-s36">CARGANDO</span>
    </div>
    <div class="col-md-12" id="containerTableResult" style="display: none;">
      @include('/admin/usuarios/clientes/tableConvenio')
    </div>
  </div>
</div>
@include('/admin/usuarios/clientes/modals')
@endsection

@section('scripts')
  <script type="text/javascript">
    var dataTableClient = 1
    $(document).ready(function () {

      $('.convenio-select').change(function(){
        let usuario = $(this).data('user');
        let convenio = $(this).val();

        $.ajax({
          url: '/admin/convenios/actualizar-usuario',
          type: 'POST',
          data: {
            usuario: usuario,
            convenio: convenio,
            _token: "{{csrf_token()}}"
          }
        })
        .done(function (resp) {
          if (resp == 'OK')
            window.show_notif('success', 'Convenio actualizado');
          else
            window.show_notif('error', resp);
        })
      })

    });
    
  </script>
    
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/datatables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>
<script src="{{ asset('admin-css/assets/js/pages/base_tables_datatables.js')}}"></script>

@include('/admin/usuarios/clientes/scripts')
@endsection