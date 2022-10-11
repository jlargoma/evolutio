@extends('layouts.admin-master')
@section('title') Distribucion de Beneficios - EVOLUTIO @endsection

@section('content')
<div class="content">
  @include('admin.contabilidad._button-contabiliad')
  <h2>Distribucion de Beneficios</h2>
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
@endsection

@section('scripts')
<link rel="stylesheet" href="{{ assetV('css/contabilidad.css') }}">

<style>

</style>

@endsection