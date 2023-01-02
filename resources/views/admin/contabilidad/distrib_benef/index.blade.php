@extends('layouts.admin-master')
@section('title') Distribucion de Beneficios - EVOLUTIO @endsection

@section('content')
<div class="content">
  @include('admin.contabilidad._button-contabiliad')
  <h2>Distribucion de Beneficios
  <button type="button" class="btn btn-success"  type="button" data-toggle="modal" data-target="#modalAddNew"><i class="fa fa-plus-circle"></i> Añadir</button>

  </h2>
  <div class="row">
    <div class="col-lg-8 col-sm-6 col-xs-12">

      <div class="table-responsive">
        <table class="table table-resumen">
          <thead>
            <tr class="resume-head">
              <th>Fecha</th>
              <th>Concepto</th>
              <th>Importe</th>
              <th>Imputado a</th>
              <th>Observaciones</th>
              <th>Borrar</th>
            </tr>
          </thead>
          <tbody>
            @foreach($lst as $item)
            <tr>
              <td>{{convertDateToShow_text($item->date)}}</td>
              <td>{{$item->concept}}</td>
              <td>{{moneda($item->import)}}</td>
              <td><?= (isset($concepts[$item->to_concept])) ? $concepts[$item->to_concept] : '-' ?></td>
              <td style="max-width: 150px;overflow: auto;">{{$item->comment}}</td>
              <td><i class="fa fa-trash delItem" data-id="{{$item->id}}"></i></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

    </div>
    <div class="col-lg-4 col-sm-6 col-xs-12">
      <div class="table-responsive">
        <table class="table table-resumen">
          <thead>
            <tr>
              <th colspan="2">RESUMEN DEL REPARTO DE BENEFICIOS</th>
            </tr>
          </thead>
          <tbody>
            @foreach($listResume as $k=>$v)
            <tr>
              <td><?= (isset($concepts[$k])) ? $concepts[$k] : '-' ?></td>
              <td>{{moneda($v)}}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th>TOTAL</th>
              <th>{{moneda(array_sum($listResume))}}</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
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
        <div class="modal-body">@include('admin.contabilidad.distrib_benef._form')</div>
      </div>
    </div>
  </div>


  <form action="{{ url('/admin/distr-beneficios/delete') }}" method="post"  id="deteleItem" data-ajax="1">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <input type="hidden" name="idToDelete" id="idToDelete" value="">
  </form>
@endsection

@section('scripts')
<link rel="stylesheet" href="{{ assetV('css/contabilidad.css') }}">

<style>
i.fa.fa-trash.delItem {
    color: red;
    cursor: pointer;
}
</style>

<script>
    $(document).ready(function () {
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
            alert('Distribucion de Beneficio Agregada');
          } else
            alert(response);
        }
      });
    });
      
    $('#modalAddNew').on('click', '#reload', function (e) {
      location.reload();
    });

    $('.delItem').on('click', function (e) {
      if (confirm('Borrar registro de manera permanente? (no se podrá recuperar)')){
        $('#idToDelete').val($(this).data('id'));
        $('#deteleItem').submit();
      }
    });
  });
</script>
@endsection